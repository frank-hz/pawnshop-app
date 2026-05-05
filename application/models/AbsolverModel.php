<?php  
	class AbsolverModel extends CI_Model {

		function __construct(){
			$suc = $this->session->userdata('pres_ses_sucursal');
		}

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

		function cuotas_x_credito($id){
			$sql = "
				SELECT 
					cuo_codigo AS id, 
					cuo_credito as credito,
					cuo_vencimiento as vencimiento,
					cuo_estado as estado
				FROM cuota 
				WHERE 
					(cuo_estado = 'E' OR cuo_estado = 'V') AND 
					cuo_credito = ?
			";
			$query = $this->db->query($sql, [$id]);
			return $query->result();
		}

		function registros_x_dia($suc){
			$sql = "
				SELECT COUNT(*) as valor FROM credito 
				WHERE 
					cred_sucursal = ? AND 
					cred_creacion = ?
			";
			$query = $this->db->query($sql, [$suc, date("Y-m-d")]);
			return $query->row();
		}

		function mov_prestado_x_dia($suc){
			$sql = "
				SELECT 
					SUM(cred_monto) as valor 
				FROM credito 
				WHERE 
					cred_sucursal = ? AND 
					cred_creacion = ?
			";
			$query = $this->db->query($sql, [$suc, date("Y-m-d")]);
			return $query->row();
		}

		function mov_recuperado_x_dia($suc){
			$sql = "
				SELECT 
					SUM(pdet_monto) as valor 
				FROM pago_detalle
				INNER JOIN pago ON pag_codigo = pdet_pago 
				INNER JOIN credito ON cred_codigo = pag_credito 
				WHERE 
					pdet_tipo = 'CAP' AND
					cred_liquidacion = ? AND
					date(pag_fecha) = ? AND 
					cred_sucursal = ?
			";
			$query = $this->db->query($sql, [date("Y-m-d"), date("Y-m-d"), $suc]);
			return $query->row();
		}

		// Functions Reporte

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
