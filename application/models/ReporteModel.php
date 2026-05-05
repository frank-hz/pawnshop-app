<?php  
	
	class ReporteModel extends CI_Model
	{
		
		function creditos_activos(){
			$this->db->where('cred_estado', 'E');
			$this->db->select('cred_codigo as id');
			$this->db->from('credito');
			$res = $this->db->get();
			return $res->result();
		}

		

		// Encode 01-2023
		function general_data_creditos_activos($suc){
			$sql = "
				SELECT 
					cred_codigo AS id, 
					cred_serie AS serie,
					cred_estado AS estado,
					cred_ciclo_pago as ciclo_pago,
					concat(cli_nombres, ' ', cli_paterno, ' ', cli_materno) AS cliente
				FROM credito
				INNER JOIN cliente ON cli_codigo = cred_cliente 
				WHERE 
					(cred_estado = 'E' OR cred_estado = 'CC') AND 
					cred_sucursal = ?
			";
			$query = $this->db->query($sql, [$suc]);
			return $query->result();
		} 
		function rep_ultima_cuota($id){
			$fecha = date("Y-m-d");
			$this->db->where('cuo_credito', $id);
			$this->db->select('cred_serie as credito');
			$this->db->select('cred_codigo as credito_id');
			$this->db->select('cred_monto as prestamo');
			$this->db->select('cred_pago_semanal as interes');
			$this->db->select('cuo_estado as estado');
			$this->db->select('cuo_orden as orden');
			$this->db->select("DATE_FORMAT(cuo_vencimiento, '%d/%m/%Y') as 'vencimiento'");
			$this->db->select('cuo_penalidad as penalidad');
			$this->db->select('cuo_mora as mora');
			$this->db->select('cuo_dias_atraso as dias');
			$this->db->select("IF(cuo_vencimiento > '".$fecha."', cuo_monto, cuo_prorrateo) as 'monto'");
			$this->db->select("IF(cuo_vencimiento > '".$fecha."', cuo_prorrateo, 0) as 'prorrateo'");
			$this->db->select("CONCAT(cli_nombres,' ',cli_paterno,' ',cli_materno) as cliente");
			$this->db->from('cuota');
			$this->db->join('credito', 'cred_codigo = cuo_credito');
			$this->db->join('cliente', 'cli_codigo = cred_cliente');
			$this->db->order_by('cuo_orden', 'DESC');
			$this->db->limit(1);
			$res = $this->db->get();
			return $res->row();
		}
		function rep_cuotas($id){
			$fecha = date("Y-m-d");
			$this->db->where('cuo_credito', $id);
			$this->db->where("(cuo_estado = 'E' OR cuo_estado = 'V')");
			$this->db->select('cred_serie as credito');
			$this->db->select('cred_codigo as credito_id');
			$this->db->select('cred_monto as prestamo');
			$this->db->select('cred_pago_semanal as interes');
			$this->db->select('cuo_estado as estado');
			$this->db->select('cuo_orden as orden');
			$this->db->select("DATE_FORMAT(cuo_vencimiento, '%d/%m/%Y') as 'vencimiento'");
			$this->db->select("cuo_vencimiento as 'vence'");
			$this->db->select('cuo_penalidad as penalidad');
			$this->db->select('cuo_mora as mora');
			$this->db->select('cuo_dias_atraso as dias');
			$this->db->select("IF(cuo_vencimiento > '".$fecha."', cuo_monto, cuo_prorrateo) as 'monto'");
			$this->db->select("IF(cuo_vencimiento > '".$fecha."', cuo_prorrateo, 0) as 'prorrateo'");
			$this->db->select("CONCAT(cli_nombres,' ',cli_paterno,' ',cli_materno) as cliente");
			$this->db->from('cuota');
			$this->db->join('credito', 'cred_codigo = cuo_credito');
			$this->db->join('cliente', 'cli_codigo = cred_cliente');
			$this->db->order_by('cred_serie', 'ASC');
			$res = $this->db->get();
			return $res->result();
		}

		
		

	}
?>	