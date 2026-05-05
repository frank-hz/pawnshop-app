<?php  

	class CuotaModel extends CI_Model
	{
		
		// NEW CODE
		function Buscar_libre($data, $cond, $format = 'file', $limite = 0, $orden = 'ASC', $cleaner = 'NO'){
			$this->db->where($cond);
			$this->db->select($data);
			if ($limite > 0) $this->db->limit($limite);
			$this->db->order_by('cuo_codigo', $orden);
			if ($cleaner === 'RESET')  mysqli_next_result( $this->db->conn_id ); 
			$res = $this->db->get('cuota');
			if ($format === 'list') {
				return $res->result();
			}else{
				return $res->row();
			}
		}
		function Modificar_evaluacion_mora($credito){
			$fecha = date("Y-m-d");
			$this->db->where('cuo_credito', $credito);
			$this->db->where('cuo_vencimiento <= "'.$fecha.'"');
			$this->db->update('cuota', array('cuo_cobra_mora' => 'I'));
		}
		function Modificar($id, $data){
			$this->db->where('cuo_codigo', $id);
			$this->db->update('cuota', $data);
		}
		function Eliminar($id){
			$this->db->delete('cuota', array('cuo_codigo' => $id));
		}


		function Modificar_libre($data, $cond){
			$this->db->where($cond);
			$this->db->update('cuota', $data);
		}

		function Lista_regularizacion($id, $fecha){
			// $sql = "CALL proc_regularizacion_cuotas(?,?)";
			$sql = "
				SELECT 
					cu.cuo_codigo as 'id', 
					IF(cu.cuo_orden < 10, CONCAT('0',cu.cuo_orden), cu.cuo_orden) as 'orden', 
					DATE_FORMAT(cu.cuo_vencimiento, '%d/%m/%Y') as 'vencimiento', 
					IF(
						? < cu.cuo_vencimiento,
						cu.cuo_prorrateo,
						cu.cuo_monto
					) as 'monto',
					cu.cuo_estado as 'estado'
				FROM cuota cu 
					INNER JOIN credito c ON c.cred_codigo = cu.cuo_credito
					WHERE 
					cu.cuo_vencimiento <= ? AND 
					c.cred_codigo = ? AND 
					(cu.cuo_estado = 'V' OR cu.cuo_estado = 'E' || cu.cuo_estado = 'CC')
			";
			$ins = $this->db->query($sql, [$fecha, $fecha, $id]);
			// mysqli_next_result( $this->db->conn_id );
			return $ins->result();
		}

		function Lista_recuperacion($id, $fecha){
			// $sql = "CALL proc_recuperacion_cuota(?,?)";
			$sql = "
				SELECT 
					cu.cuo_codigo as 'id', 
					IF(cu.cuo_orden < 10, CONCAT('0',cu.cuo_orden), cu.cuo_orden) as 'orden', 
					DATE_FORMAT(cu.cuo_vencimiento, '%d/%m/%Y') as 'vencimiento', 
					IF(
						? >= cu.cuo_vencimiento OR cu.cuo_orden = 1,
						cu.cuo_monto,
						cu.cuo_prorrateo
					) as 'monto',
					IF(
						? < cu.cuo_vencimiento,
						'PROR',
						'NORM'
					) as 'tipo_cobro',
					cu.cuo_estado as 'estado'
				FROM cuota cu 
					INNER JOIN credito c ON c.cred_codigo = cu.cuo_credito
					WHERE 
						c.cred_codigo = ? AND
						(c.cred_estado = 'E' OR c.cred_estado = 'CC') AND 
						(cu.cuo_estado = 'V' OR cu.cuo_estado = 'E' OR cu.cuo_estado = 'CC')
			";
			$ins = $this->db->query($sql, array($fecha,$fecha,$id));
			// mysqli_next_result( $this->db->conn_id );
			return $ins->result();
		}

		function insertar($data){
			return $this->db->insert('cuota', $data);
		}
		
		function Buscar_prorroga_aplicada($id, $fecha){
			$sql = "SELECT pror_codigo as id FROM prorroga 
			WHERE ? >= pror_inicio AND ? < pror_termino AND pror_credito = ?";
			$res = $this->db->query($sql, array($fecha, $fecha, $id));
			return $res->result();
		}

		function ContadorAtraso($id){
			$sql = "
				SELECT 
					COUNT(*) as 'valor' 
				FROM cuota 
				WHERE 
					cuo_estado='V' AND 
					cuo_credito = ?
				";
			$res = $this->db->query($sql, array($id));
			return $res->row();
		}


		// encode: 04-2023
		function get_all_to_congelacion($id){
			$curdate = date("m-d-Y");
			// $sql = "
			// 	SELECT
			// 		cuo_codigo as id,
			// 		cuo_orden as orden,
			// 		cuo_monto as monto,
			// 		cuo_vencimiento as vencimiento,
			// 		cuo_prorrateo as prorrateo,
			// 		cuo_estado as estado,
			// 		cuo_credito as id_credito
			// 	FROM cuota c 
			// 	WHERE 
			// 		cuo_credito = ? AND 
			// 		cuo_estado = ?
			// ";
			$sql = "
				SELECT 
					cu.cuo_codigo as id, 
					cu.cuo_orden as orden, 
					cu.cuo_estado as estado,
					DATE_FORMAT(cu.cuo_vencimiento, '%d/%m/%Y') as vencimiento, 
					IF(
						? >= cu.cuo_vencimiento OR cu.cuo_orden = 1,
						cu.cuo_monto,
						cu.cuo_prorrateo
					) as monto,
					IF(
						? < cu.cuo_vencimiento,
						'PROR',
						'NORM'
					) as tipo_cobro
				FROM cuota cu 
				INNER JOIN credito c ON c.cred_codigo = cu.cuo_credito
				WHERE 
					c.cred_codigo = ? AND
					(c.cred_estado = 'E' OR c.cred_estado = 'CC') AND 
					(cu.cuo_estado = 'V' OR cu.cuo_estado = 'E' OR cu.cuo_estado = 'CC')
			";
			$res = $this->db->query($sql, [$curdate, $curdate, $id]);
			return $res->result();
		}

		function get_cantidad_atraso($id){
			$curdate = date("Y-m-d");
			$sql = "
				SELECT 
					COUNT(*) as cantidad
				FROM cuota 
				WHERE 
					(cuo_estado='E' OR cuo_estado='V' OR cuo_estado='CC') AND 
					cuo_credito = ? AND 
					cuo_vencimiento <= ?
				";
			$res = $this->db->query($sql, [$id, $curdate]);
			return $res->row();
		}



		// Rework 05-2023
		function getByCredito($idcredito, $parm = []){ 			
			$ord_target_raw = (!empty($parm['orden_target'])) ? strtolower($parm['orden_target']) : '';			
			$orden_target = (in_array($ord_target_raw,['codigo','orden','vencimiento','monto'])) ? 'cuo_'.$ord_target_raw : 'cuo_codigo';
			$orden_type = (!empty($parm['orden_type'])) ? $parm['orden_type'] : 'ASC';

			$sql = "
				SELECT 
					cuo_codigo as 'id',
					cuo_credito as 'credito_id',
					cuo_orden as 'orden',
					cuo_monto as 'monto',
					cuo_penalidad as 'penalidad',
					DATE_FORMAT(cuo_vencimiento, '%d/%m/%Y') as 'vencimiento',
					cuo_vencimiento as 'vencimiento_raw',
					DATE_FORMAT(cuo_inicio_mora, '%d/%m/%Y') as 'inicio_mora',
					cuo_inicio_mora as 'inicio_mora_raw',
					cuo_dias_atraso as 'dias_atraso',
					cuo_mora as 'mora',
					cuo_prorrateo as 'prorrateo',
					cuo_cobra_mora as 'cobra_mora',
					cuo_estado as 'estado',
					cuo_create_at as create_at,
					cuo_update_at as update_at
				FROM cuota 
				WHERE cuo_credito = ? 
				ORDER BY $orden_target $orden_type 
			";
			$res = $this->db->query($sql, [$idcredito]);
			return $res->result();
		}
		function getData($id){
			$this->db->select('cuo_dias_atraso as dias_atraso');
			$this->db->select('cuo_mora as mora');
			$this->db->select('cuo_prorrateo as prorrateo');
			$this->db->select('cuo_cobra_mora as cobra_mora');
			$this->db->select('cuo_estado as estado');
			$this->db->select('cuo_create_at as create_at');
			$this->db->select('cuo_update_at as update_at');
			$res = $this->db->get('cuotas');
			return $res->result();
		}




		// B A S I C 



		// C U S T O M 
		function UpdateCustom($data, $condition){
			$this->db->where($condition);
			return $this->db->update('cuota', $data);
		}

		function getForRegularizar($id){
			$currentDate = date("Y-m-d");
			$pars = Array($currentDate, $currentDate, $id);
			$sql = "
				SELECT 
					cu.cuo_codigo as 'id', 
					IF(cu.cuo_orden < 10, CONCAT('0',cu.cuo_orden), cu.cuo_orden) as 'orden', 
					DATE_FORMAT(cu.cuo_vencimiento, '%d/%m/%Y') as 'vencimiento', 
					IF(
						? < cu.cuo_vencimiento,
						cu.cuo_prorrateo,
						cu.cuo_monto
					) as 'monto',
					cu.cuo_estado as 'estado'
				FROM cuota cu 
				INNER JOIN credito c ON c.cred_codigo = cu.cuo_credito
				WHERE 
				cu.cuo_vencimiento <= ? AND 
				c.cred_codigo = ? AND 
				(cu.cuo_estado = 'V' OR cu.cuo_estado = 'E' || cu.cuo_estado = 'CC')
			";
			$ins = $this->db->query($sql, $pars);
			return $ins->result();
		}


	}
?>