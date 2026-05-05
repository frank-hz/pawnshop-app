<?php  

	class MoraModel extends CI_Model
	{
		
		// 
		function Buscar_libre($data, $cond){
			$this->db->where($cond);
			$this->db->select($data);
			$res = $this->db->get('mora');
			return $res->row();
		}
		// 
		function Buscar_detalle($id){
			// $sql = "CALL proc_mora_detalle(?)";
			$sql = "
				SELECT
					m.mor_codigo as 'id',
					m.mor_credito as 'credito',
					DATE_FORMAT(c.cuo_vencimiento, '%d/%m/%Y') as 'vence',
					m.mor_dias as 'dias',
					m.mor_monto as 'monto',
					m.mor_estado as 'estado',
					c.cuo_orden as 'orden',
					c.cuo_codigo as 'cuota' 
				FROM mora m
				INNER JOIN cuota c ON c.cuo_codigo = m.mor_cuota
				WHERE 
					m.mor_codigo = ?;
			";
			$ins = $this->db->query($sql, array($id));
			return $ins->row();
		}
		// 
		function Modificar($id, $data){
			$this->db->where('mor_codigo', $id);
			$this->db->update('mora', $data);
		}
		// 
		function Modificar_libre($data, $cond){
			$this->db->where($cond);
			$this->db->update('mora', $data);
		}
		// 
		function Eliminar($id){
			$this->db->delete('mora', array('mor_codigo' => $id)); 
		}
		// 
		function Insertar($data){
			return $this->db->insert('mora', $data);
		}
		// 
		function Insertar_prorroga($data){
			return $this->db->insert('prorroga', $data);
		}
		// 
		function Verifica_prorroga($id, $fecha){
			// $this->db->where('pror_credito', $id);
			// $this->db->where('(pror_inicio <= "'.$fecha.'" OR pror_termino > "'.$fecha.'")');
			// $this->db->select('pror_codigo as id');
			// $res = $this->db->get('prorroga');
			$sql = "SELECT pror_codigo as 'id' FROM prorroga WHERE pror_credito = ? AND (pror_inicio <= ? OR pror_termino > ?)";
			$res = $this->db->query($sql, array($id, $fecha, $fecha));
			return $res->result();
		}
		// 
		// function Buscar_mora_activa($id){
		// 	$this->db->where("mor_estado = 'PEN'");
		// 	$this->db->where("mor_credito", $id);
		// 	$this->db->select('mor_codigo as id');
		// 	$res = $this->db->get('mora');
		// 	return $res->result();
		// }

		function Buscar_pendiente($id){
			// $sql = "CALL proc_mora_pendiente(?)";
			$sql = "
				SELECT 
					m.mor_codigo as 'id',
					m.mor_credito as 'credito',
					m.mor_cuota as 'cuota',
					m.mor_dias as 'dias',
					c.cuo_penalidad as 'penalidad',
					ROUND(m.mor_monto,1) as 'monto',
					m.mor_estado as 'estado'
				FROM mora m 
				INNER JOIN cuota c ON c.cuo_codigo = m.mor_cuota
					WHERE m.mor_credito = ? AND m.mor_estado = 'PEN'
			";
			$ins = $this->db->query($sql, array($id));
			return $ins->row();
		}

		function Buscar_pospuesto($id){
			// $sql = "CALL proc_mora_pospuesta(?)";
			$sql = "
				SELECT 
					m.mor_codigo as 'id',
					m.mor_credito as 'credito',
					m.mor_cuota as 'cuota',
					m.mor_dias as 'dias',
					ROUND(m.mor_monto,1) as 'monto',
					c.cuo_penalidad as 'penalidad',
					m.mor_estado as 'estado'
				FROM mora m 
				INNER JOIN cuota c ON c.cuo_codigo = m.mor_cuota
					WHERE m.mor_credito = ? AND m.mor_estado = 'POS'
			";
			$ins = $this->db->query($sql, array($id));
			return $ins->result();
		}

		function Buscar_total($id){
			// $sql = "CALL proc_mora_deuda_total(?)";
			$sql = "
				SELECT 
					m.mor_codigo as 'id',
					m.mor_credito as 'credito',
					m.mor_cuota as 'cuota',
					m.mor_dias as 'dias',
					ROUND(m.mor_monto,1) as 'monto',
					IF(m.mor_estado = 'POS', 'MORA POSPUESTA', 'MORA') as 'descripcion'
				FROM mora m 
				INNER JOIN credito cd ON cd.cred_codigo = m.mor_credito
				WHERE 
					m.mor_credito = ? AND
					(cd.cred_estado = 'E' OR cd.cred_estado = 'CC') AND
					m.mor_estado != 'PAG'
			";
			$ins = $this->db->query($sql, array($id));
			return $ins->result();
		}

	}
?>