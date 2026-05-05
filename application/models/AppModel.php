<?php  
	
	class AppModel extends CI_Model
	{	
		// NEW CODE

		// Cuotas que aun no cumplen su fecha de vencimiento
		function cuotas_en_proceso($id, $limite = 0){
			$fecha = date("Y-m-d");
			$this->db->where('cuo_credito', $id);
			$this->db->where("(cuo_estado = 'E' OR cuo_estado = 'V')");
			$this->db->where("cuo_vencimiento < '".$fecha."'");
			$this->db->select('cuo_codigo as id');
			$this->db->select('cuo_credito as credito');
			$this->db->select('cuo_orden as orden');
			$this->db->select('cuo_vencimiento as vencimiento');
			$this->db->select('cuo_inicio_mora as inicio_mora');
			$this->db->select('cuo_monto as monto');
			$this->db->select('cuo_penalidad as penalidad');
			$this->db->select('cuo_estado as estado');
			$this->db->select('cuo_cobra_mora as cobra_mora');
			$this->db->select('cred_prorroga as prorroga');
			$this->db->join('credito', 'cred_codigo = cuo_credito');
			if ($limite > 0) $this->db->limit($limite);
			$this->db->from('cuota');
			$res = $this->db->get();
			return $res->result();
		}
		// Cuotas activas que aun no hay sido pagadas
		function cuotas_activas($id, $limite = 0){
			$this->db->where('cuo_credito', $id);
			$this->db->where("(cuo_estado = 'E' OR cuo_estado = 'V')");
			// $this->db->where("cuo_cobra_mora = 'A'");
			$this->db->select('cuo_codigo as id');
			$this->db->select('cuo_credito as credito');
			$this->db->select('cuo_orden as orden');
			$this->db->select('cuo_vencimiento as vencimiento');
			$this->db->select('cuo_inicio_mora as inicio_mora');
			$this->db->select('cuo_monto as monto');
			$this->db->select('cuo_penalidad as penalidad');
			$this->db->select('cuo_estado as estado');
			$this->db->select('cuo_cobra_mora as cobra_mora');
			$this->db->select('cred_prorroga as prorroga');
			$this->db->select('cred_aplica_penalidad as aplica_penalidad');
			$this->db->join('credito', 'cred_codigo = cuo_credito');
			if ($limite > 0) $this->db->limit($limite);
			$this->db->from('cuota');
			$res = $this->db->get();
			return $res->result();
		}

		// Cuotas mas recientes del credito
		function cuotas_recientes($id, $limite = 3){
			$fecha = date("Y-m-d");
			$this->db->where('cuo_credito', $id);
			// $this->db->where("(cuo_estado = 'E' OR cuo_estado = 'V')");
			$this->db->where("cuo_vencimiento < '".$fecha."'");
			$this->db->select('cuo_codigo as id');
			$this->db->select('cuo_credito as credito');
			$this->db->select('cuo_orden as orden');
			$this->db->select('cuo_vencimiento as vencimiento');
			$this->db->select('cuo_inicio_mora as inicio_mora');
			$this->db->select('cuo_monto as monto');
			$this->db->select('cred_penalidad as penalidad');
			$this->db->select('cuo_estado as estado');
			$this->db->select('cuo_cobra_mora as cobra_mora');
			$this->db->select('cred_prorroga as prorroga');
			$this->db->join('credito', 'cred_codigo = cuo_credito');
			$this->db->limit($limite);
			$this->db->order_by('cuo_codigo DESC');
			$this->db->from('cuota');
			$res = $this->db->get();
			return $res->result();
		}

		// Creditos activos del sistema
		function creditos_activos(){
			$this->db->where('cred_estado', 'E');
			$this->db->select('cred_codigo as id');
			$this->db->select('cred_aplica_penalidad as cobro_penalidad');
			$this->db->select('cred_ciclo_pago as ciclo_pago');
			$this->db->from('credito');
			$res = $this->db->get();
			return $res->result();
		}

		function getSearchCredito($id){
			$this->db->where('cred_codigo', $id);
			$this->db->select('cred_ciclo_pago as ciclo_pago');
			$this->db->from('credito');
			$res = $this->db->get();
			return $res->result();
		}

		// Datos generales segun sucursal
		function datos_generales($fecha, $sucursal){
			$sql = "CALL proc_info_general(?,?)";
			$res = $this->db->query($sql, array($fecha, $sucursal));
			return $res->row();
		}

		// Evalua la generacion de una cuota nueva
		function Evaluar_generacion_cuota(array $data){
			$sql = "CALL proc_revision_generacion_cuota(?,?,?,?,?,?,?,?,?,?)";
			$res = $this->db->query($sql, $data);
			mysqli_next_result( $this->db->conn_id );
			return $res->row();
		}
		// Procesa y calcula los nuevos valores de la cuota
		function Evaluar_calculo_cuota(array $data){
			$sql = "CALL proc_revision_cuota_calculo(?,?,?,?,?,?,?,?,?,?,?)";
			$res = $this->db->query($sql, $data);
			mysqli_next_result( $this->db->conn_id );
			return $res->row();
		}
		




		// OLD CODE


		// Lista de cuotas que han cumplido su fecha de vecimiento
		// Candidatos para evaluacion y posible generacion de una nueva cuota  
		function  Lista_cuotas_cumplidas($par){
			$sql = "CALL proc_revision_cuota_cumplida(?)";
			$res = $this->db->query($sql, array($par));
			mysqli_next_result( $this->db->conn_id );
			return $res->result();
		}

		// Lista de creditos candidatos para la revision diaria
		function Lista_candidatos_reporte($suc){
			$this->db->where('cred_sucursal', $suc);
			$this->db->where('(cred_estado = "E" OR cred_estado = "CC")');
			$this->db->select('
				cred_codigo as id, 
				cred_serie as serie');
			$this->db->from('credito');
			$this->db->order_by('cred_serie', 'ASC');
			$res = $this->db->get();
			return $res->result();
		}

		// Lista de cuotas disponibles para el reporte diario
		function Lista_reporte_diario($id){
			$sql = "CALL proc_reporte_diario(?,?)";
			$res = $this->db->query($sql, array($id, date("Y-m-d")));
			// $res->free_result();
			mysqli_next_result( $this->db->conn_id );
			return $res->result();
		}

		function cuota_codetest($id){
			$sql = "CALL proc_cuota_codetest(?)";
			$res = $this->db->query($sql, array($id));
			mysqli_next_result( $this->db->conn_id );
			return $res->result();
		}


		// Funciones para la generacion del reporte
		function reporte_creditos($suc){
			$this->db->where("(cred_estado = 'E' OR cred_estado = 'CC')");
			$this->db->where('cred_sucursal', $suc);
			$this->db->select('cred_codigo as id');
			$this->db->select('cred_estado as estado');
			$this->db->order_by('cred_codigo', 'ASC');
			$this->db->from('credito');
			$res = $this->db->get();
			return $res->result();
		}

		function reporte_ultima_cuota($id){
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

		function reporte_cuotas($id){
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
		
		function reporte_cuotas_dia($id){
			$fecha = date("Y-m-d");
			$this->db->where('cred_sucursal', $id);
			$this->db->where("cred_estado", 'E');
			$this->db->where("cuo_vencimiento", $fecha);
			// $this->db->where("(cuo_estado = 'E' OR cuo_estado = 'V')");
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
