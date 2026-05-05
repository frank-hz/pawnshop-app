<?php  
	
	class PagoModel extends CI_Model
	{
		
		// --- NEW CODE
		function Listar($data){
			$sql = "CALL proc_pago_lista(?,?,?,?)";
			$res = $this->db->query($sql, $data);
			return $res->result();
		}

		function RecodeListar($t, $val, $lim, $suc) {
			$filter_string = "";
			$pars = [];
			switch ($t) {
				case 'cliente':
					$filter_string = "
						WHERE 
							c.cred_sucursal = ? AND 
							(
								cl.cli_nombres LIKE CONCAT(UPPER(?), '%') OR 
								cl.cli_paterno LIKE CONCAT(UPPER(?), '%') OR 
								cl.cli_materno LIKE CONCAT(UPPER(?), '%')
							)
					";
					$pars = [$suc, $val, $val, $val];
					break;
				
				case 'credito':
					$filter_string = "
						WHERE 
							c.cred_sucursal = ? AND 
							c.cred_serie LIKE CONCAT(?, '%')
					";
					$pars = [$suc, $val];
					break;

				case 'forma':
					$filter_string = "
						WHERE 
							c.cred_sucursal = ? AND 
							p.pag_forma_pago = UPPER(?)
					";
					$pars = [$suc, $val];
					break;

				case 'fecha':
					$filter_string = "
						WHERE 
							c.cred_sucursal = ? AND 
							DATE(p.pag_fecha) = DATE(?)
					";
					$pars = [$suc, $val];
					break;
					
				default:
					$filter_string = "
						WHERE 
							c.cred_sucursal = ? 
					";
					$pars = [$suc];
					break;
			}

			$sql = "
				SELECT
					p.pag_codigo as 'id', 
					p.pag_serie as 'serie',
					c.cred_serie as 'credito',
					p.pag_concepto as 'concepto',
					concat(cl.cli_nombres,' ',cl.cli_paterno,' ',cl.cli_materno) as 'cliente',
					DATE_FORMAT(p.pag_fecha, '%d/%m/%Y %H:%i') as 'fecha',
					IF(p.pag_forma_pago = 'TIEN', 'TIENDA', 'DEPOSITO') as 'forma',
					p.pag_total as 'total',
					p.pag_concepto as 'concepto'
				FROM pago p 
				INNER JOIN credito c ON c.cred_codigo = p.pag_credito
				INNER JOIN cliente cl ON cl.cli_codigo = c.cred_cliente 
				$filter_string 
				ORDER BY p.pag_codigo DESC

			";
			$res = $this->db->query($sql, $pars);
			return $res->result();
		}
		
		function Insertar(array $data){
			return $this->db->insert('pago', $data);
		}
		
		function Insertar_detalle(array $data){
			return $this->db->insert('pago_detalle', $data);
		}

		function Buscar_libre($data, $cond){
			$this->db->where($cond);
			$this->db->select($data);
			$res = $this->db->get('pago');
			return $res->row();
		}

		function Buscar_detalle($id){
			$this->db->where('pdet_pago', $id);
			$this->db->select('pdet_codigo as id');
			$this->db->select('pdet_tipo as tipo');
			$this->db->select('pdet_monto as monto');
			$this->db->select('pdet_id_ref as id_ref');
			$res = $this->db->get('pago_detalle');
			return $res->result();
		}
		
		function Buscar_orden_actual($nox, $suc){
			$this->db->where(
				array(
					'pag_nomen' => $nox,
					'cred_sucursal' => $suc
				)
			);
			$this->db->select('MAX(pag_orden) as orden');
			$this->db->join('credito', 'cred_codigo = pag_credito');
			$res = $this->db->get('pago');
			return $res->row();
		}
		
		function Modificar($id, $data){
			$this->db->where('pag_codigo', $id);
			$this->db->update('pago', $data);
		}
		
		function Rango_fecha($data){
			// $sql = "CALL proc_pago_rango_fecha(?,?,?,?)";
			$sql = "
				SELECT
					p.pag_codigo as 'id', 
					p.pag_serie as 'serie',
					c.cred_serie as 'credito',
					concat(cl.cli_nombres,' ',cl.cli_paterno,' ',cl.cli_materno) as 'cliente',
					DATE_FORMAT(p.pag_fecha, '%d/%m/%Y %H:%i') as 'fecha',
					IF(p.pag_forma_pago = 'TIEN', 'TIENDA', 'DEPOSITO') as 'forma',
					p.pag_total as 'total',
					p.pag_concepto as 'concepto'
				FROM pago p 
				INNER JOIN credito c ON c.cred_codigo = p.pag_credito
				INNER JOIN cliente cl ON cl.cli_codigo = c.cred_cliente 
				WHERE 
					c.cred_sucursal = ? AND 
					(p.pag_fecha BETWEEN ? AND ?)
				ORDER BY p.pag_codigo DESC
			";
			$res = $this->db->query($sql, $data);
			return $res->result();
		}

		// -----

		function obtener_pago($id){
			$sql = "CALL proc_pago_informacion(?)";
			$res = $this->db->query($sql, array($id));
			mysqli_next_result( $this->db->conn_id );
			return $res->row();
		}
		function obtener_detalle($id){
			// $sql = "CALL proc_pago_detalle(?)";
			$sql = "
				SELECT
					pd.pdet_codigo as 'id',
					pd.pdet_tipo as 'tipo',
					IF(
						pd.pdet_tipo = 'CU', 
						(
						select concat('- Vencimiento: ',DATE_FORMAT(cuo_vencimiento, '%d/%m/%Y')) from cuota where cuo_codigo = pd.pdet_id_ref), 
						''
					) as 'subref',
					pd.pdet_referencia as 'referencia',
					pd.pdet_id_ref as 'id_referencia',
					pd.pdet_monto as 'monto'
				FROM pago_detalle pd 
				WHERE pd.pdet_pago = ?
			";
			$ins = $this->db->query($sql, array($id));
			// mysqli_next_result( $this->db->conn_id );
			return $ins->result();
		}
		function obtener_orden_guia(){
			$sql = "
				SELECT 
					gpag_orden as 'orden' 
				FROM guia_pago ORDER BY gpag_codigo DESC LIMIT 1";
			$ins = $this->db->query($sql);
			return $ins->row()->orden;
		}
		function insertar_guia(array $data){
			return $this->db->insert('guia_pago', $data);
		}
		function consulta_pago($id){
			$sql = "CALL proc_pago_informacion(?)";
			$res = $this->db->query($sql, array($id));
			mysqli_next_result( $this->db->conn_id );
			return $res->row();
		}
		function consulta_detalle($id){
			// $sql = "CALL proc_pago_detalle(?)";
			$sql = "
				SELECT
					pd.pdet_codigo as 'id',
					pd.pdet_tipo as 'tipo',
					IF(
						pd.pdet_tipo = 'CU', 
						(select concat('- Vencimiento: ',DATE_FORMAT(cuo_vencimiento, '%d/%m/%Y')) from cuota where cuo_codigo = pd.pdet_id_ref), 
						''
					) as 'subref',
					pd.pdet_referencia as 'referencia',
					pd.pdet_id_ref as 'id_referencia',
					pd.pdet_monto as 'monto'
				FROM pago_detalle pd 
				WHERE pd.pdet_pago = ?
			";
			$ins = $this->db->query($sql, array($id));
			// mysqli_next_result( $this->db->conn_id );
			return $ins->result();
		}
		function amortizacion_entradas($id){
			$this->db->where("pag_credito", $id);
			$this->db->where("pdet_tipo", "CAP");
			$this->db->select("pdet_referencia as referencia");
			$this->db->select('pdet_monto as monto');
			$this->db->select('pag_codigo as pago_id');
			$this->db->select('pag_serie as pago_serie');
			$this->db->select("DATE_FORMAT(pag_fecha, '%d/%m/%Y') as fecha");
			$this->db->from('pago_detalle');
			$this->db->join('pago', 'pag_codigo = pdet_pago');
			$this->db->order_by('pag_fecha', 'DESC');
			$res = $this->db->get();
			return $res->result();
		}


		// Eliminacion - Anulacion
		function Eliminar($id){
			$this->db->delete('pago', array('pag_codigo' => $id)); 
		}
		function Eliminar_detalle($id){
			$this->db->delete('pago_detalle', array('pdet_pago' => $id)); 
		}

		// RECODE
		function getPagoDetail($id){
			$sql = "
			SELECT
				p.pag_codigo as 'id',
				c.cred_codigo as 'credito',
				suc_nombre as suc_nombre,
				suc_direccion as suc_direccion,
				ubi_distrito as suc_distrito,
				ubi_provincia as suc_provincia,
				ubi_departamento as suc_departamento,
				c.cred_serie as 'codigo_credito',
				p.pag_serie as 'serie',
				DATE_FORMAT(p.pag_fecha, '%d/%m/%Y - %h:%i %p') as 'fecha',
				p.pag_forma_pago as 'forma',
				p.pag_comprobante as 'comprobante',
				IF(
					(p.pag_forma_pago = 'DEP' OR p.pag_forma_pago = 'TRAN') AND p.pag_banco > 0,
					(select ban_nombre from banco WHERE ban_codigo = p.pag_banco),
					''
				) as 'banco',
				p.pag_subtotal as 'subtotal',
				p.pag_descuento as 'descuento',
				p.pag_total as 'total',
				p.pag_recibido as 'recibido',
				p.pag_devuelto as 'devuelto',
				p.pag_concepto as 'concepto',
				CONCAT(e.emp_nombres,' ',e.emp_paterno,' ',e.emp_materno) as 'empleado',
				CONCAT(cl.cli_nombres,' ',cl.cli_paterno,' ',cl.cli_materno) as 'cliente',
				cl.cli_email as email,
				p.pag_adicional as adicional,
				p.pag_comentario as comentario,
				(select ban_nombre from banco where ban_codigo = p.pag_banco) as banco_nombre,
				p.pag_comentario as comentario
			FROM pago p 
			INNER JOIN credito c ON c.cred_codigo = p.pag_credito
			INNER JOIN empleado e ON e.emp_codigo = p.pag_empleado
			INNER JOIN cliente cl ON cl.cli_codigo = c.cred_cliente 
			INNER JOIN sucursal ON suc_codigo = cred_sucursal 
			INNER JOIN ubigeo ON ubi_codigo = suc_ubigeo
			WHERE 
				p.pag_codigo = ?
			";

			$res = $this->db->query($sql, [$id]);
			return $res->row();
		}

		// Update 05-06-22

		function GetAll($dp){
			$suc = $this->session->userdata('pres_ses_sucursal');
			$fecha_actual = date("Y-m-d");
			$params = [$suc];
			$sql = "
				SELECT  
					pag_codigo as pago_id,
					pag_serie as pago_serie,
					cred_codigo as credito_id,
					cred_serie as credito_serie,
					pag_concepto as concepto,
					DATE_FORMAT(pag_fecha, '%d-%m-%Y %H:%i:%s') as fecha,
					pag_forma_pago as forma_pago,
					pag_total as total,
					CONCAT(
						cli_nombres, ' ',
						cli_paterno, ' ',
						cli_materno
					) as cliente					
				FROM pago 
				INNER JOIN credito ON pag_credito = cred_codigo 
				INNER JOIN cliente ON cred_cliente = cli_codigo
				WHERE cred_sucursal = ?
			";
			switch ($dp->type) {
				case 'DIA':
					$sql .= "AND DATE(pag_fecha) = ?";
					array_push($params, [$fecha_actual]);
					break;
				case 'COD':
					$sql .= "AND pag_serie LIKE CONCAT(?, '%')";
					array_push($params, [strtoupper($dp->in_text)]);
					break;
				case 'CRED':
					$sql .= "AND cred_serie LIKE CONCAT(?, '%')";
					array_push($params, [strtoupper($dp->in_text)]);
					break;
				case 'CLI':
					$sql .= "
						AND (
							cli_nombres LIKE CONCAT(?, '%') OR 
							cli_paterno LIKE CONCAT(?, '%') OR 
							cli_materno LIKE CONCAT(?, '%') 
						)
					";
					array_push($params, [strtoupper($dp->in_text)]);
					array_push($params, [strtoupper($dp->in_text)]);
					array_push($params, [strtoupper($dp->in_text)]);
					break;
				case 'FECHA':
					$sql .= "AND DATE(pag_fecha) = ?";
					$dp->in_date = date("Y-m-d", strtotime($dp->in_date));
					array_push($params, [$dp->in_date]);
					// $sql = "rango ".$dp->in_date;
					break;
				case 'RANGO':
					$sql .= "AND DATE(pag_fecha) BETWEEN ? AND ?";
					$dp->in_range_1 = date("Y-m-d", strtotime($dp->in_range_1));
					$dp->in_range_2 = date("Y-m-d", strtotime($dp->in_range_2));
					array_push($params, [$dp->in_range_1]);
					array_push($params, [$dp->in_range_2]);
					// $sql = "rango ".$dp->in_range_1." - ".$dp->in_range_2;
					break;
			}

			$sql .= " ORDER BY pag_fecha $dp->order LIMIT $dp->limit";
			$res = $this->db->query($sql, $params);
			return $res->result();
			// return $sql;

		}

		// Update 17/07/22
		function GetSearchAll($idcredito){
			$sql = "
				SELECT  
					pag_codigo as id,
					pag_serie as serie,
					cred_codigo as credito_id,
					cred_serie as credito_serie,
					pag_concepto as concepto,
					DATE_FORMAT(pag_fecha, '%d-%m-%Y %H:%i:%s') as fecha,
					pag_forma_pago as forma_pago,
					pag_total as total,
					CONCAT(
						cli_nombres, ' ',
						cli_paterno, ' ',
						cli_materno
					) as cliente					
				FROM pago 
				INNER JOIN credito ON pag_credito = cred_codigo 
				INNER JOIN cliente ON cred_cliente = cli_codigo
				WHERE pag_credito = ?
			";
			$res = $this->db->query($sql, [$idcredito]);
			return $res->result();
		}

		function get_data($param){
			$id_suc = $this->session->userdata('pres_ses_sucursal');
			$sql = "
				SELECT  
					pag_codigo as pago_id,
					pag_serie as pago_serie,
					cred_codigo as credito_id,
					cred_serie as credito_serie,
					pag_concepto as concepto,
					DATE_FORMAT(pag_fecha, '%d/%m/%Y - %h:%i %p') as fecha,
					pag_forma_pago as forma_pago,
					ROUND(pag_total,2) as total,
					CONCAT(
						cli_nombres, ' ',
						cli_paterno, ' ',
						cli_materno
					) as cliente_nombre,
					cli_codigo as cliente_id					
				FROM pago 
				INNER JOIN credito ON pag_credito = cred_codigo 
				INNER JOIN cliente ON cred_cliente = cli_codigo
				WHERE cred_sucursal = ? 
			";
			$pars_db = [$id_suc];
			if(!empty($param->buscar)) {
				switch ($param->tipo_busqueda) {
					case 'CODIGO':
						$sql .= " AND pag_serie LIKE CONCAT('%', ?, '%')";
						array_push($pars_db, strtoupper($param->buscar));
						break;
					
					case 'CREDITO':
						$sql .= " AND cred_serie LIKE CONCAT('%', ?, '%')";
						array_push($pars_db, strtoupper($param->buscar));
						break;
					
					case 'CLIENTE':
						$sql .= " AND ( 
								cli_paterno LIKE CONCAT(?, '%') OR
								cli_materno LIKE CONCAT(?, '%') OR 
								cli_nombres LIKE CONCAT('%', ?, '%')
							)";
						array_push(
							$pars_db, 
							$param->buscar, 
							$param->buscar, 
							$param->buscar
						);
						break;
				}
			}
			if (!empty($param->concepto) && in_array($param->concepto, ['REG','REC','AMOR','ADEL','DEB_AUTO','ADEL_AUTO'])){
				$sql .= " AND pag_concepto = ?";
				array_push($pars_db, $param->concepto);
			}
			if (!empty($param->fecha->tipo) && in_array($param->fecha->tipo, ['DATE','RANGE'])) {
				if ($param->fecha->tipo === 'DATE') {
					if (!empty($param->fecha->unico)) {
						$sql .= " AND DATE(pag_fecha) = ?";
						array_push($pars_db, $param->fecha->unico);
					}
					
				}else if($param->fecha->tipo === 'RANGE') {
					if (!empty($param->fecha->valor_a) && !empty($param->fecha->valor_b)) {
						$sql .= "AND DATE(pag_fecha) BETWEEN ? AND ?";
						array_push($pars_db, $param->fecha->valor_a, $param->fecha->valor_b);
					}
				}
			}
			if (!empty($param->forma_pago)){
				$sql .= " AND (";
				$forma_raw = [];
				foreach ($param->forma_pago as $ind => $fp) {
					if (in_array($fp, ['EFEC','VIRT','DEB_AUTO'])){
						switch ($fp) {
							case 'EFEC':
								array_push($forma_raw, 'TIEN');
								break;							
							case 'VIRT':
								array_push($forma_raw, 'AGEN');
								array_push($forma_raw, 'TRANF');
								array_push($forma_raw, 'DEP');
								break;
							case 'DEB_AUTO':
								array_push($forma_raw, 'REV');
								break;
						}
					}
				}
				foreach ($forma_raw as $ind => $fp) {
					if (in_array($fp, ['TIEN','AGEN','TRANF','DEP','REV'])) {
						if ($ind === 0) {
							$sql .= "pag_forma_pago = ?";
						}else {
							$sql .= " OR pag_forma_pago = ?";
						}
						array_push($pars_db, $fp);
					}
				}
				$sql .= ")";
			}
			if (!empty($param->concepto)){
				$sql .= " AND (";
				foreach ($param->concepto as $ind => $fp) {
					if (in_array($fp, ['REG','REC','AMOR','ADEL','DEB_AUTO','ADEL_AUTO'])) {
						if ($ind === 0) {
							$sql .= "pag_concepto = ?";
						}else {
							$sql .= " OR pag_concepto = ?";
						}
						array_push($pars_db, $fp);
					}
				}
				$sql .= ")";
			}

			$order_str = "";
			if (in_array($param->orden->tipo, ['ASC', 'DESC']) && in_array($param->orden->por, ['SERIE','FECHA','TOTAL'])) {
				$por = 'pag_' . strtolower($param->orden->por);
				$tipo = $param->orden->tipo;
				$order_str = " ORDER BY $por $tipo";								
			}
			$sql .= $order_str;

			// $sql ="
			// SELECT pag_codigo as pago_id,pag_serie as pago_serie FROM `pago` inner join credito on pag_credito = cred_codigo where cred_sucursal = 2 and pag_concepto = 'REC' ORDER BY `pag_codigo` DESC
			// ";
			$res = $this->db->query($sql, $pars_db);
			return $res->result();
			// return [
			// 	'data' => $res->result(),
			// 	'string' => $sql,
			// 	'params' => $pars_db
			// ];
		}



		// 
		function getAllByCredito($idcredito){
			$sql = "
				SELECT  
					pag_codigo as 'id',
					pag_serie as 'serie',
					pag_orden as 'orden',
					pag_nomen as 'nomen',
					pag_credito as 'credito_id',
					cred_serie as credito_serie,
					DATE_FORMAT(pag_fecha, '%d/%m/%Y %H:%i:%s') as 'fecha_hora',
					DATE_FORMAT(pag_fecha, '%d/%m/%Y') as 'fecha',
					pag_forma_pago as 'forma_pago_raw',
					pag_concepto as 'concepto_raw',
					pag_banco as 'banco_id',
					pag_total as 'total',
					pag_empleado as 'empleado_id',
					CONCAT(emp_nombres,' ',emp_paterno,' ',emp_materno) as 'empleado_nombre_full'				
				FROM pago 
				INNER JOIN credito ON cred_codigo = pag_credito 
				INNER JOIN empleado ON emp_codigo = pag_empleado
				WHERE pag_credito = ? 
				ORDER BY pag_codigo DESC
			";
			$res = $this->db->query($sql, [$idcredito]);
			return $res->result();
		}
		function getById($id){
			$sql = "
				SELECT  
					pag_codigo as 'id',
					pag_serie as 'serie',
					pag_orden as 'orden',
					pag_nomen as 'nomen',
					cred_codigo as 'credito_id',
					cred_serie as credito_serie,
					pag_concepto as 'concepto_raw',
					DATE_FORMAT(pag_fecha, '%d/%m/%Y %H:%i:%s') as 'fecha_hora',
					DATE_FORMAT(pag_fecha, '%d/%m/%Y') as 'fecha',
					pag_fecha as 'fecha_hora_raw',
					pag_fecha as 'fecha_raw',
					pag_forma_pago as forma_pago_raw,
					pag_comprobante as 'comprobante',
					pag_banco as 'banco_id',
					ban_nombre as banco_nombre,
					pag_subtotal as 'subtotal',
					pag_descuento as 'descuento',
					pag_total as 'total',
					pag_recibido as 'recibido',
					pag_devuelto as 'devuelto',
					pag_concepto as 'concepto_raw',
					pag_empleado as 'empleado_id',
					CONCAT(emp_nombres,' ',emp_paterno,' ',emp_materno) as 'empleado_nombre_full',
					cred_cliente as cliente_id,
					CONCAT(cli_nombres,' ',cli_paterno,' ',cli_materno) as 'cliente_nombre_full',
					pag_adicional as 'adicional',
					pag_comentario as 'comentario'					
				FROM pago 
				INNER JOIN credito ON cred_codigo = pag_credito 
				INNER JOIN empleado ON emp_codigo = pag_empleado 
				INNER JOIN cliente ON cli_codigo = cred_cliente 
				INNER JOIN banco ON ban_codigo = pag_banco
				WHERE pag_codigo = ?
			";
			$res = $this->db->query($sql, [$id]);
			return $res->row();
		}
		
		
	}
?>	
