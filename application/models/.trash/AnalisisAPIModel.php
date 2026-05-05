<?php  
	class AnalisisAPIModel extends CI_Model 
	{
		protected $id_suc = "";

		function __construct(){
			$this->id_suc = $this->session->userdata('pres_ses_sucursal');
		}

		function get_rango_fecha_sucursal(){
			$sql = "
				SELECT 
					MIN(cred_creacion) AS inicio,
					MAX(cred_creacion) AS ultimo 
				FROM credito
				WHERE 
					cred_sucursal = ? AND  
					cred_creacion != '0000-00-00'
			";
			$res = $this->db->query($sql, [ $this->id_suc ]);
			return $res->row();
		}

		function get_credito_activo(){
			$sql = "
				SELECT COUNT(cred_monto) AS value FROM credito
				WHERE 
					cred_sucursal = ? AND
					cred_estado = 'E'
			";
			$res = $this->db->query($sql, [ $this->id_suc ]);
			return $res->row();
		}

		function get_dinero_activo(){
			$sql = "
				SELECT SUM(cred_monto) AS value FROM credito
				WHERE 
					cred_sucursal = ? AND
					cred_estado = 'E'
			";
			$res = $this->db->query($sql, [ $this->id_suc ]);
			return $res->row();
		}


		// Balance
		function get_data_utilidad_cuota($data){
			$array_attr = [];
			$sql = "
				SELECT 
					SUM(pdet_monto) AS total,
					COUNT(*) as cantidad
				FROM pago_detalle
				INNER JOIN pago ON pag_codigo = pdet_pago
				INNER JOIN credito ON cred_codigo = pag_credito
				WHERE 
					(
						cred_estado = 'E' OR 
						cred_estado = 'R'
					) 
					AND pdet_tipo = 'CU' 
					AND cred_sucursal = ? 
			";
			switch ($data->mode) {
				case 'mes':
					$sql .= "AND YEAR(pag_fecha) = ? AND MONTH(pag_fecha) = ?";
					$array_attr = [$this->id_suc, $data->anio, $data->mes];
					break;
				
				case 'anio':
					$sql .= "AND YEAR(pag_fecha) = ?";
					$array_attr = [$this->id_suc, $data->anio];
					break;

				case 'anio':
					$sql .= "AND YEAR(pag_fecha) = ?";
					$array_attr = [$this->id_suc, $data->anio];
					break;

				case 'fecha':
					$fecha = $data->fecha_anio."-".$data->fecha_mes."-".$data->fecha_dia;
					$sql .= "AND DATE(pag_fecha) = ?";
					$array_attr = [$this->id_suc, $fecha];
					break;

				case 'today':
					$sql .= "AND DATE(pag_fecha) = ?";
					$array_attr = [$this->id_suc, Date("Y-m-d")];
					break;

				case 'rango':
					$fecha_1 = $data->rango_inicio_anio."-".$data->rango_inicio_mes."-".$data->rango_inicio_dia;
					$fecha_2 = $data->rango_fin_anio."-".$data->rango_fin_mes."-".$data->rango_fin_dia;
					$sql .= "AND DATE(pag_fecha) BETWEEN ? AND ?";
					$array_attr = [$this->id_suc, $fecha_1, $fecha_2];
					break;

				case 'actual':
					$sql .= "AND YEAR(pag_fecha) = ? AND MONTH(pag_fecha) = ?";
					$array_attr = [$this->id_suc, $data->anio, $data->mes];
					break;
				case 'mes_actual':
					$mesAC = date("m");
					$anioAC = date("Y"); 
					$sql .= "AND YEAR(pag_fecha) = ? AND MONTH(pag_fecha) = ?";
					$array_attr = [$this->id_suc, $anioAC, $mesAC];
					break;
			}
			$res = $this->db->query($sql, $array_attr);
			return $res->row();
		}

		function get_data_utilidad_mora($data){
			$array_attr = [];
			$sql = "
				SELECT 
					SUM(pdet_monto) AS total,
					COUNT(*) as cantidad
				FROM pago_detalle
				INNER JOIN pago ON pag_codigo = pdet_pago
				INNER JOIN credito ON cred_codigo = pag_credito
				WHERE 
					(
						cred_estado = 'E' OR 
						cred_estado = 'R'
					) AND
					pdet_tipo = 'MO' AND
					cred_sucursal = ? 
			";
			switch ($data->mode) {
				case 'mes':
					$sql .= "AND YEAR(pag_fecha) = ? AND MONTH(pag_fecha) = ?";
					$array_attr = [$this->id_suc, $data->anio, $data->mes];
					break;
				
				case 'anio':
					$sql .= "AND YEAR(pag_fecha) = ?";
					$array_attr = [$this->id_suc, $data->anio];
					break;

				case 'anio':
					$sql .= "AND YEAR(pag_fecha) = ?";
					$array_attr = [$this->id_suc, $data->anio];
					break;

				case 'fecha':
					$fecha = $data->fecha_anio."-".$data->fecha_mes."-".$data->fecha_dia;
					$sql .= "AND DATE(pag_fecha) = ?";
					$array_attr = [$this->id_suc, $fecha];
					break;

				case 'today':
					$sql .= "AND DATE(pag_fecha) = ?";
					$array_attr = [$this->id_suc, Date("Y-m-d")];
					break;

				case 'rango':
					$fecha_1 = $data->rango_inicio_anio."-".$data->rango_inicio_mes."-".$data->rango_inicio_dia;
					$fecha_2 = $data->rango_fin_anio."-".$data->rango_fin_mes."-".$data->rango_fin_dia;
					$sql .= "AND DATE(pag_fecha) BETWEEN ? AND ?";
					$array_attr = [$this->id_suc, $fecha_1, $fecha_2];
					break;

				case 'actual':
					$sql .= "AND YEAR(pag_fecha) = ? AND MONTH(pag_fecha) = ?";
					$array_attr = [$this->id_suc, $data->anio, $data->mes];
					break;
				
				case 'mes_actual':
					$mesAC = date("m");
					$anioAC = date("Y"); 
					$sql .= "AND YEAR(pag_fecha) = ? AND MONTH(pag_fecha) = ?";
					$array_attr = [$this->id_suc, $anioAC, $mesAC];
					break;
			}
			$res = $this->db->query($sql, $array_attr);
			return $res->row();
		}

		function get_data_descuento($data){
			$array_attr = [];
			$sql = "
				SELECT 
					SUM(pag_descuento) AS total,
					COUNT(*) as cantidad
				FROM pago
				INNER JOIN credito ON cred_codigo = pag_credito
				WHERE 
					( cred_estado = 'E' OR cred_estado = 'R') AND 
					pag_descuento > 0 AND
					cred_sucursal = ?
			";

			switch ($data->mode) {
				case 'mes':
					$sql .= "AND YEAR(pag_fecha) = ? AND MONTH(pag_fecha) = ?";
					$array_attr = [$this->id_suc, $data->anio, $data->mes];
					break;
				
				case 'anio':
					$sql .= "AND YEAR(pag_fecha) = ?";
					$array_attr = [$this->id_suc, $data->anio];
					break;				

				case 'fecha':
					$fecha = $data->fecha_anio."-".$data->fecha_mes."-".$data->fecha_dia;
					$sql .= "AND DATE(pag_fecha) = ?";
					$array_attr = [$this->id_suc, $fecha];
					break;

				case 'today':
					$sql .= "AND DATE(pag_fecha) = ?";
					$array_attr = [$this->id_suc, Date("Y-m-d")];
					break;

				case 'rango':
					$fecha_1 = $data->rango_inicio_anio."-".$data->rango_inicio_mes."-".$data->rango_inicio_dia;
					$fecha_2 = $data->rango_fin_anio."-".$data->rango_fin_mes."-".$data->rango_fin_dia;
					$sql .= "AND DATE(pag_fecha) BETWEEN ? AND ?";
					$array_attr = [$this->id_suc, $fecha_1, $fecha_2];
					break;

				case 'actual':
					$sql .= "AND YEAR(pag_fecha) = ? AND MONTH(pag_fecha) = ?";
					$array_attr = [$this->id_suc, $data->anio, $data->mes];
					break;
					
				case 'mes_actual':
					$mesAC = date("m");
					$anioAC = date("Y");
					$sql .= "AND YEAR(pag_fecha) = ? AND MONTH(pag_fecha) = ?";
					$array_attr = [$this->id_suc, $anioAC, $mesAC];
					break;
			}
			$res = $this->db->query($sql, $array_attr);
			return $res->row();
		}

		// Creditos x Estado
		function get_creditos_estado($estado){
			$par_stash = [$this->id_suc];
			$sql = "
				SELECT 
					COUNT(*) as cantidad,
					SUM(cred_monto) as monto
				FROM credito 
				WHERE 
					cred_sucursal = ?
			";
			if($estado != 'TOTAL') {
				$sql .= "AND cred_estado = ?";
				array_push($par_stash, $estado);
			}
			$res = $this->db->query($sql, $par_stash);
			return $res->row();
		}

		// generacion v recuperacion
		function get_credito_generado($data){
			$array_attr = [];
			$sql = "
				SELECT 
					COUNT(*) as cantidad,
					SUM(cred_monto) as total
				FROM credito 
				WHERE 
					(cred_estado = 'E' OR cred_estado = 'R') AND
					cred_sucursal = ?
			";
			switch ($data->mode) {
				case 'mes':
					$sql .= "AND YEAR(cred_creacion) = ? AND MONTH(cred_creacion) = ?";
					$array_attr = [$this->id_suc, $data->anio, $data->mes];
					break;
				
				case 'anio':
					$sql .= "AND YEAR(cred_creacion) = ?";
					$array_attr = [$this->id_suc, $data->anio];
					break;

				case 'anio':
					$sql .= "AND YEAR(cred_creacion) = ?";
					$array_attr = [$this->id_suc, $data->anio];
					break;

				case 'fecha':
					$fecha = $data->fecha_anio."-".$data->fecha_mes."-".$data->fecha_dia;
					$sql .= "AND DATE(cred_creacion) = ?";
					$array_attr = [$this->id_suc, $fecha];
					break;

				case 'today':
					$sql .= "AND DATE(cred_creacion) = ?";
					$array_attr = [$this->id_suc, Date("Y-m-d")];
					break;

				case 'rango':
					$fecha_1 = $data->rango_inicio_anio."-".$data->rango_inicio_mes."-".$data->rango_inicio_dia;
					$fecha_2 = $data->rango_fin_anio."-".$data->rango_fin_mes."-".$data->rango_fin_dia;
					$sql .= "AND DATE(cred_creacion) BETWEEN ? AND ?";
					$array_attr = [$this->id_suc, $fecha_1, $fecha_2];
					break;

				case 'actual':
					$sql .= "AND YEAR(cred_creacion) = ? AND MONTH(cred_creacion) = ?";
					$array_attr = [$this->id_suc, $data->anio, $data->mes];
					break;
				
				case 'mes_actual':
					$mesAC = date("m");
					$anioAC = date("Y"); 
					$sql .= "AND YEAR(cred_creacion) = ? AND MONTH(cred_creacion) = ?";
					$array_attr = [$this->id_suc, $anioAC, $mesAC];
					break;
			}
			$res = $this->db->query($sql, $array_attr);
			return $res->row();
		}

		function get_credito_recuperado($data){
			$array_attr = [];
			$sql = "
				SELECT 
					COUNT(*) as cantidad,
					SUM(cred_monto) as total
				FROM credito 
				WHERE 
					(cred_estado = 'E' OR cred_estado = 'R') AND
					cred_sucursal = ?
			";
			switch ($data->mode) {
				case 'mes':
					$sql .= "AND YEAR(cred_liquidacion) = ? AND MONTH(cred_liquidacion) = ?";
					$array_attr = [$this->id_suc, $data->anio, $data->mes];
					break;
				
				case 'anio':
					$sql .= "AND YEAR(cred_liquidacion) = ?";
					$array_attr = [$this->id_suc, $data->anio];
					break;

				case 'anio':
					$sql .= "AND YEAR(cred_liquidacion) = ?";
					$array_attr = [$this->id_suc, $data->anio];
					break;

				case 'fecha':
					$fecha = $data->fecha_anio."-".$data->fecha_mes."-".$data->fecha_dia;
					$sql .= "AND DATE(cred_liquidacion) = ?";
					$array_attr = [$this->id_suc, $fecha];
					break;

				case 'today':
					$sql .= "AND DATE(cred_liquidacion) = ?";
					$array_attr = [$this->id_suc, Date("Y-m-d")];
					break;

				case 'rango':
					$fecha_1 = $data->rango_inicio_anio."-".$data->rango_inicio_mes."-".$data->rango_inicio_dia;
					$fecha_2 = $data->rango_fin_anio."-".$data->rango_fin_mes."-".$data->rango_fin_dia;
					$sql .= "AND DATE(cred_liquidacion) BETWEEN ? AND ?";
					$array_attr = [$this->id_suc, $fecha_1, $fecha_2];
					break;

				case 'actual':
					$sql .= "AND YEAR(cred_liquidacion) = ? AND MONTH(cred_liquidacion) = ?";
					$array_attr = [$this->id_suc, $data->anio, $data->mes];
					break;
				
				case 'mes_actual':
					$mesAC = date("m");
					$anioAC = date("Y"); 
					$sql .= "
						AND YEAR(cred_liquidacion) = ? 
						AND MONTH(cred_liquidacion) = ?
					";
					$array_attr = [$this->id_suc, $anioAC, $mesAC];
					break;
			}
			$res = $this->db->query($sql, $array_attr);
			return $res->row();
		}

	}
?>