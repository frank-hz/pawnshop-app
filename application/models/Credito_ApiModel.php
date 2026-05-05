<?php
	class Credito_apiModel extends CI_Model
	{

		function GetListData($idsucursal, $setting){
			$orden = !empty($setting['order']) ? $setting['order'] : '';
			$search_query_base = " WHERE (cred_sucursal = ?)";

			// concatenacion: query filtros por defecto
			$search_query_base .= !empty($setting['search_default']['query']) ? " AND  ".$setting['search_default']['query'] : '';

			// concatenacion: query filtros personalizados
			$search_query_base .= !empty($setting['search_custom']['query']) ? " AND  ".$setting['search_custom']['query'] : '';

			// filtros por defecto: asignacion de valores
			$default_parameters = (!empty($setting['search']['parameters']) ? $setting['search_default']['parameters'] : []);
				// filtros personalizados: asignacion de valores
			$custom_parameters = (!empty($setting['search_custom']['parameters']) ? $setting['search_custom']['parameters'] : []);

			$prenda_search = !empty($setting['search_prenda']) ? $setting['search_prenda'] : '';

			$pars = [$idsucursal];
			if (!empty($search_params)) {
				$pars = array_merge($pars, $search_params);
			}
			$parameters = array_merge(
				[$idsucursal],
				$default_parameters,
				$custom_parameters
			);

			$sql = "
				SELECT
					cred_codigo as 'id',
					cred_serie as 'serie',
					DATE_FORMAT(cred_creacion, '%d/%m/%Y') as 'creacion',
					DATE_FORMAT(cred_creacion, '%H:%i:%s') as 'hora',
					DATE_FORMAT(cred_liquidacion, '%d/%m/%Y') as 'liquidacion',
					cred_monto as 'monto',
					cli_codigo as cliente_id,
					CONCAT(cli_nombres,' ',cli_paterno,' ',cli_materno) as 'cliente_nombre',
					cred_estado as 'estado',
					IF(
						cred_estado='E',
						(SELECT COUNT(*) FROM cuota WHERE (cuo_estado='V' OR cuo_estado='E') AND cuo_credito=cred_codigo and cuo_vencimiento <= now()),
						''
					) as 'cuotas_vencidas',
					(
						SELECT COUNT(*) FROM cuota WHERE (cuo_estado='V' OR cuo_estado='E') AND cuo_credito=cred_codigo and cuo_vencimiento <= now()
					) as 'cred_atraso',
					cred_prenda as prenda_id,
					IF (
						cred_prenda > 0,
						(SELECT pren_descripcion FROM prenda WHERE pren_codigo = cred_prenda),
						'Sin Datos'
					) as prenda_descripcion,
					cred_sucursal as sucursal,
					(
						SELECT COUNT(*) FROM cuota WHERE cuo_vencimiento = CURDATE() and cuo_credito = cred_codigo
					) as 'cuota_cumplida',
					(
						SELECT COUNT(*) FROM notas_credito WHERE ncred_credito = cred_codigo
					) as notas_cantidad
				FROM credito
				INNER JOIN cliente ON cli_codigo = cred_cliente
				$search_query_base
				$orden
			";
			$res = $this->db->query($sql, $parameters);
			return $res->result();
			// return [
			// 	'query' => $search_query_base,
			// 	'pars' => $parameters,
			// 	'order' => $orden
			// ];
		}

		function get_data_all($pars_in){
			$sql = "
				SELECT
					cred_codigo as 'id',
					cred_serie as 'serie',
					DATE_FORMAT(cred_creacion, '%d/%m/%Y') as 'creacion',
					DATE_FORMAT(cred_creacion, '%H:%i:%s') as 'hora',
					cred_monto as 'monto',
					cli_codigo as cliente_id,
					CONCAT(cli_nombres,' ',cli_paterno,' ',cli_materno) as 'cliente_nombre',
					cred_estado as 'estado',
					IF(
						cred_estado='E',
						(SELECT COUNT(*) FROM cuota WHERE (cuo_estado='V' OR cuo_estado='E') AND cuo_credito=cred_codigo and cuo_vencimiento <= now()),
						''
					) as 'cuotas_vencidas',
					(
						SELECT COUNT(*) FROM cuota WHERE (cuo_estado='V' OR cuo_estado='E') AND cuo_credito=cred_codigo and cuo_vencimiento <= now()
					) as 'cred_atraso',
					cred_prenda as prenda_id,
					IF (
						cred_prenda > 0,
						(SELECT pren_descripcion FROM prenda WHERE pren_codigo = cred_prenda),
						'Sin Datos'
					) as prenda_descripcion,
					cred_sucursal as sucursal,
					(
						SELECT COUNT(*) FROM cuota WHERE cuo_vencimiento = CURDATE() and cuo_credito = cred_codigo
					) as 'cuota_cumplida'
				FROM credito
				INNER JOIN cliente ON cli_codigo = cred_cliente
				WHERE
					cred_sucursal = ?
			";
			$pars_db = [$this->id_suc];
			if (!empty($pars_in->buscar)) {
				switch ($pars_in->tipo_busqueda) {
					case 'CODIGO':
						$sql .= " AND cred_serie LIKE CONCAT('%', ?, '%')";
						array_push($pars_db, strtoupper($pars_in->buscar));
						break;

					case 'CLIENTE':
						$sql .= " AND (
								cli_paterno LIKE CONCAT(?, '%') OR
								cli_materno LIKE CONCAT(?, '%') OR
								cli_nombres LIKE CONCAT('%', ?, '%')
							)";
						array_push(
							$pars_db,
							$pars_in->buscar,
							$pars_in->buscar,
							$pars_in->buscar
						);
						break;
				}
			}

			if (!empty($pars_in->estado) && in_array($pars_in->estado,['E','R','CC', 'A'])) {
				$sql .= " AND cred_estado = ?";
				array_push($pars_db, $pars_in->estado);
			}

			$simbol = (in_array($pars_in->atraso->simbolo, ['>','>=','=','<=','<'])) ? $pars_in->atraso->simbolo : 'NO_DATA';
			if (!empty($pars_in->atraso->valor) && $simbol != 'NO_DATA') {
				$sql .= "
					AND
						(SELECT COUNT(*) FROM cuota WHERE cuo_estado='V' AND cuo_credito=cred_codigo) $simbol ?
				";
				array_push($pars_db, intval($pars_in->atraso->valor));
			}

			$more_html = "";
			if (!empty($pars_in->fechas->inicio) && !empty($pars_in->fechas->final)) {
				$sql .= " AND cred_creacion BETWEEN ? AND ?";
				array_push($pars_db, $pars_in->fechas->inicio);
				array_push($pars_db, $pars_in->fechas->final);
			}

			$order_str = "";
			if (in_array($pars_in->orden->tipo, ['ASC', 'DESC']) && in_array($pars_in->orden->por, ['CODIGO', 'CREACION', 'MONTO', 'ATRASO'])) {
				$por = 'cred_' . strtolower($pars_in->orden->por);
				$tipo = $pars_in->orden->tipo;
				$order_str = " ORDER BY $por $tipo";
			}
			$limite = (!empty($pars_in->limite)) ? ' limit '.$pars_in->limite : '';
			$sql .= $order_str;

			$res = $this->db->query($sql, $pars_db);
			return [
				'data' => $res->result(),
				'string' => $sql,
				'params' => $pars_db
			];
		}

		function getData($id){
			$this->db->where("c.cred_codigo", $id);
			$this->db->select("c.cred_codigo as id");
			$this->db->select("cl.cli_codigo as cliente_id");
			$this->db->select("CONCAT(
				cl.cli_nombres, ' ',
				cl.cli_paterno, ' ',
				cl.cli_materno 
			) as cliente_nombre");
			$this->db->select("cl.cli_email as cliente_email");
			$this->db->select("c.cred_serie as serie");
			$this->db->select("c.cred_estado as estado");
			$this->db->join("cliente cl", "c.cred_cliente = cl.cli_codigo");
			$res = $this->db->get('credito c');
			return $res->row();
		}



		// B A S I C 

		function Update($id, $data){
			$this->db->where('cred_codigo', $id);
			return $this->db->update('credito', $data);
		}

		// C U S T O M
		function getCustom($data, $condition){
			$this->db->where($condition);
			$this->db->select($data);
			$res = $this->db->get('credito');
			return $res->row();
		}
		
		function getAllAdvanced($idsucursal, $setting){
			$orden = !empty($setting['order']) ? $setting['order'] : '';
			$search_query_base = " WHERE (cred_sucursal = ?)";
			$global_query = " WHERE (cred_sucursal = ?)";
			$global_params = array($idsucursal);

			// filtros por defecto
			if (!empty($setting['search_default']['query'])){
				$global_query .= " AND ". $setting['search_default']['query'];
				$global_params = array_merge($global_params, $setting['search_default']['values']);
			}

			// filtros personalizados
			if (!empty($setting['search_custom']['query'])){
				$global_query .= " AND ". $setting['search_custom']['query'];
				$global_params = array_merge($global_params, $setting['search_custom']['values']);
			}

			// Busqueda por prenda
			if (!empty($setting['search_prenda'])){
				$global_query .= " AND (pren_descripcion LIKE CONCAT(?,'%') ) ";
				array_push($global_params, $setting['search_prenda']);
			}
			
			// Busqueda por categoria
			if (!empty($setting['search_categoria'])){
				$global_query .= " AND (ct.cat_codigo = ? ) ";
				array_push($global_params, $setting['search_categoria']);
			}

			$sql = "
				SELECT 
				c1.cred_codigo as id,
				c1.cred_serie as serie,
				c1.cred_monto as monto,
				c1.cred_sucursal as sucursal,
				DATE_FORMAT(c1.cred_creacion, '%d/%m/%Y') as fecha_creacion,
				DATE_FORMAT(c1.cred_creacion, '%H:%i:%s') as hora,
				DATE_FORMAT(c1.cred_liquidacion, '%d/%m/%Y') as fecha_liquidacion,
				c1.cred_estado as estado,
				cl.cli_codigo as cliente_id,
				CONCAT(cl.cli_nombres,' ',cl.cli_paterno,' ',cl.cli_materno) as 'cliente_nombre',
				p.pren_codigo as prenda_id,
				p.pren_descripcion as prenda_descripcion,
				p.pren_serial as prenda_serial,
				p.pren_marca as prenda_marca,
				p.pren_modelo as prenda_modelo,
				p.pren_peso as prenda_peso,
				p.pren_anio as prenda_anio,
				p.pren_valoracion as prenda_valoracion_estado,
				p.pren_observacion as prenda_observacion,
				p.pren_comentario as prenda_comentario,
				p.pren_estado as prenda_estado,
				CASE 
					WHEN c1.cred_estado = 'E' 
						THEN (select count(*) from cuota where (cuo_estado='V' OR cuo_estado='E') AND cuo_credito=c1.cred_codigo and cuo_vencimiento <= now()) 
					WHEN c1.cred_estado = 'A' OR c1.cred_estado = 'CC' 
						THEN (select count(*) from cuota where (cuo_estado='CC' OR cuo_estado='V') AND cuo_credito=c1.cred_codigo)
					ELSE 0 
				END as cuotas_vencidas,
				(
					SELECT COUNT(*) FROM cuota WHERE cuo_vencimiento = CURDATE() and cuo_credito = c1.cred_codigo
				) as cuotas_cumplidas,
				(
					SELECT COUNT(*) FROM cuota WHERE cuo_estado = 'P' and cuo_credito = c1.cred_codigo
				) as cuotas_pagadas,
				(
					SELECT COUNT(*) FROM notas_credito WHERE ncred_credito = c1.cred_codigo
				) as notas_cantidad
				FROM credito c1 
				INNER JOIN cliente cl ON cl.cli_codigo = c1.cred_cliente 
				LEFT JOIN prenda p ON p.pren_codigo = c1.cred_prenda 
				LEFT JOIN categoria ct ON ct.cat_codigo = p.pren_categoria 
				$global_query
				$orden
			";
			
			$res = $this->db->query($sql, $global_params);
			return $res->result();

			// return [
			// 	'query' => $global_query,
			// 	'pars' => $global_params,
			// 	'order' => $orden,
			// 	'sql' => $sql 
			// ];
		}
		
		function getAllByCliente($id){
			$sql = "
				SELECT 
					c.cred_codigo as id,
					c.cred_serie as serie,
					c.cred_monto as monto,
					DATE_FORMAT(c.cred_creacion, '%d/%m/%Y') as fecha_creacion,
					DATE_FORMAT(c.cred_creacion, '%H:%i:%s') as hora,
					DATE_FORMAT(c.cred_liquidacion, '%d/%m/%Y') as fecha_liquidacion,
					c.cred_estado as estado,
					e.emp_codigo as empleado_id,
					CONCAT(
						e.emp_nombres,' ',
						e.emp_paterno,' ',
						e.emp_materno
					) as empleado_nombre_full,
					cl.cli_codigo as cliente_id,
					CONCAT(
						cl.cli_nombres,' ',
						cl.cli_paterno,' ',
						cl.cli_materno
					) as cliente_nombre_full
				FROM credito c 
				INNER JOIN cliente cl ON cl.cli_codigo = c.cred_cliente
				INNER JOIN empleado e ON e.emp_codigo = c.cred_empleado
				WHERE c.cred_cliente = ? 
				ORDER BY c.cred_creacion DESC
			";
			$res = $this->db->query($sql, array($id));
			return $res->result();
		}
	}
?>
