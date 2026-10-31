<?php 
    class AnalisisRestModel extends CI_Model{   
        
        public $idsuc;
        function __construct(){
            $this->idsuc = $this->session->userdata('pres_ses_sucursal');
        }
        function get_line_time(){
			$sql = "
				SELECT 
					DISTINCT YEAR(pag_fecha) as value  
				FROM pago  
				INNER JOIN credito ON cred_codigo = pag_credito 
				WHERE 
					cred_sucursal = ?
			";
			$res = $this->db->query($sql, [$this->idsuc]);
			return $res->result();
		}
        function get_rango_fecha_sucursal(){
			$sql = "
				SELECT 
					MIN(DATE(cred_create_at)) AS inicio,
					MAX(DATE(cred_create_at)) AS final 
				FROM credito
				WHERE 
					cred_sucursal = ? AND  
					DATE(cred_create_at) != 0000-00-00
			";
			$res = $this->db->query($sql, [ $this->idsuc ]);
			return $res->row();
		}
        function get_credito_activo(){
			$sql = "
				SELECT COUNT(cred_monto) AS value FROM credito
				WHERE 
					cred_sucursal = ? AND
					cred_estado = 'E'
			";
			$res = $this->db->query($sql, [ $this->idsuc ]);
			return $res->row();
		}
        function get_dinero_activo(){
			$sql = "
				SELECT SUM(cred_monto) AS value FROM credito
				WHERE 
					cred_sucursal = ? AND
					cred_estado = 'E'
			";
			$res = $this->db->query($sql, [ $this->idsuc ]);
			return $res->row();
		}
        function get_all_credito_generado($data){
			$array_attr = [];
			$sql = "
				SELECT 
					c.cred_codigo as id,
					c.cred_serie as serie,
					ROUND(c.cred_monto, 1) as monto,
					ROUND(c.cred_monto, 1) as monto_prestado,
					DATE_FORMAT(c.cred_create_at, '%d/%m/%Y - %h:%i %p') as fecha_creacion,
					DATE_FORMAT(c.cred_liquidacion, '%d/%m/%Y - %h:%i %p') as fecha_liquidacion,
					c.cred_estado as estado,
					'GEN' as tipo,
					CONCAT(
						cl.cli_nombres,' ',
						cl.cli_paterno,' ',
						cl.cli_materno
					) as cliente_nombre,
					IF (
						c.cred_prenda > 0,
						(SELECT pren_descripcion FROM prenda WHERE pren_codigo = c.cred_prenda),
						'Sin Datos'
					) as prenda_descripcion
				FROM credito c
				JOIN cliente cl ON cl.cli_codigo = c.cred_cliente 
				WHERE 
					(c.cred_estado = 'E' OR c.cred_estado = 'R') AND
					c.cred_sucursal = ?
			";
			switch ($data->mode) {
				case 'MES':
					$sql .= "AND YEAR(c.cred_creacion) = ? AND MONTH(c.cred_creacion) = ?";
					$array_attr = [$this->idsuc, $data->in_anio, $data->in_mes];
					break;
				
				case 'ANIO':
					$sql .= "AND YEAR(c.cred_creacion) = ?";
					$array_attr = [$this->idsuc, $data->in_anio];
					break;

				case 'FECHA':
					$sql .= "AND DATE(c.cred_creacion) = ?";
					$array_attr = [$this->idsuc, $data->in_fecha];
					break;

				case 'FECHA_ACTUAL':
					$sql .= "AND DATE(c.cred_creacion) = ?";
					$array_attr = [$this->idsuc, date("Y-m-d")];
					break;

				case 'RANGO':
					$sql .= "AND DATE(c.cred_creacion) BETWEEN ? AND ?";
					$array_attr = [$this->idsuc, $data->in_rango_inicial, $data->in_rango_final];
					break;
				
				case 'MES_ACTUAL':
					$mesAC = date("m");
					$anioAC = date("Y"); 
					$sql .= "AND YEAR(c.cred_creacion) = ? AND MONTH(c.cred_creacion) = ?";
					$array_attr = [$this->idsuc, $anioAC, $mesAC];
					break;
			}
			$res = $this->db->query($sql, $array_attr);
			return $res->result();
		}
		function get_all_credito_generado_v2($data){
			$array_attr = [];
			$sql = "
				SELECT 
					c.cred_codigo as credito_id,
					c.cred_serie as credito_serie,
					DATE_FORMAT(c.cred_create_at, '%d/%m/%Y - %h:%i %p') as fecha_creacion,
					CONCAT(
						cl.cli_nombres,' ',
						cl.cli_paterno,' ',
						cl.cli_materno
					) as cliente_nombre,
					IF (
						c.cred_prenda > 0,
						(SELECT pren_descripcion FROM prenda WHERE pren_codigo = c.cred_prenda),
						'Sin Datos'
					) as prenda_descripcion,
					ROUND(c.cred_monto, 1) as monto_prestado,
					DATE_FORMAT(c.cred_liquidacion, '%d/%m/%Y - %h:%i %p') as fecha_liquidacion,
					c.cred_estado as estado,
					'GENERACION' as tipo
				FROM credito c
				JOIN cliente cl ON cl.cli_codigo = c.cred_cliente 
				WHERE 
					(c.cred_estado = 'E' OR c.cred_estado = 'R') AND
					c.cred_sucursal = ?
			";
			switch ($data->mode) {
				case 'MES':
					$sql .= "AND YEAR(c.cred_creacion) = ? AND MONTH(c.cred_creacion) = ?";
					$array_attr = [$this->idsuc, $data->in_anio, $data->in_mes];
					break;
				
				case 'ANIO':
					$sql .= "AND YEAR(c.cred_creacion) = ?";
					$array_attr = [$this->idsuc, $data->in_anio];
					break;

				case 'FECHA':
					$sql .= "AND DATE(c.cred_creacion) = ?";
					$array_attr = [$this->idsuc, $data->in_fecha];
					break;

				case 'FECHA_ACTUAL':
					$sql .= "AND DATE(c.cred_creacion) = ?";
					$array_attr = [$this->idsuc, date("Y-m-d")];
					break;

				case 'RANGO':
					$sql .= "AND DATE(c.cred_creacion) BETWEEN ? AND ?";
					$array_attr = [$this->idsuc, $data->in_rango_inicial, $data->in_rango_final];
					break;
				
				case 'MES_ACTUAL':
					$mesAC = date("m");
					$anioAC = date("Y"); 
					$sql .= "AND YEAR(c.cred_creacion) = ? AND MONTH(c.cred_creacion) = ?";
					$array_attr = [$this->idsuc, $anioAC, $mesAC];
					break;
			}
			$res = $this->db->query($sql, $array_attr);
			return $res->result();
		}
		function get_all_credito_recuperado($data){			
			$sql = "
				SELECT 
					c.cred_codigo as id,
					c.cred_serie as serie,
					c.cred_monto as monto,
					c.cred_monto as monto_capital,
					'RECU' as tipo,
					CONCAT(
						cl.cli_nombres,' ',
						cl.cli_paterno,' ',
						cl.cli_materno
					) as cliente_nombre,
					IF (
						c.cred_prenda > 0,
						(SELECT pren_descripcion FROM prenda WHERE pren_codigo = c.cred_prenda),
						'Sin Datos'
					) as prenda_descripcion
				FROM credito c 
				JOIN cliente cl ON cl.cli_codigo = c.cred_cliente
				WHERE 
					cred_estado = 'R' AND
					cred_sucursal = ?
			";
            $params = [$this->idsuc];
			switch ($data->mode) {
				case 'MES':
					$sql .= "AND YEAR(c.cred_liquidacion) = ? AND MONTH(c.cred_liquidacion) = ?";
                    array_push($params, $data->in_anio);
                    array_push($params, $data->in_mes);
					break;
				
				case 'ANIO':
					$sql .= "AND YEAR(c.cred_liquidacion) = ?";
                    array_push($params, $data->in_anio);
					break;

				case 'FECHA':
					$sql .= "AND DATE(c.cred_liquidacion) = ?";
                    array_push($params, $data->in_fecha);
					break;

				case 'FECHA_ACTUAL':

					$sql .= "AND DATE(c.cred_liquidacion) = ?";
                    array_push($params, date("Y-m-d"));
					break;

				case 'RANGO':
					$sql .= "AND DATE(c.cred_liquidacion) BETWEEN ? AND ?";
                    array_push($params, $data->in_rango_inicial);
                    array_push($params, $data->in_rango_final);
					break;
				
				case 'MES_ACTUAL':
					$mesAC = date("m");
					$anioAC = date("Y"); 
					$sql .= "
						AND YEAR(c.cred_liquidacion) = ? 
						AND MONTH(c.cred_liquidacion) = ?
					";
                    array_push($params, $anioAC);
                    array_push($params, $mesAC);
					break;
			}
			$res = $this->db->query($sql, $params);
			return $res->result();
		}
		function get_all_credito_recuperado_v2($data){			
			$sql = "
				SELECT 					
					c.cred_codigo as credito_id,
					c.cred_serie as credito_serie,
					DATE_FORMAT(c.cred_create_at, '%d/%m/%Y - %h:%i %p') as fecha_creacion,
					CONCAT(
						cl.cli_nombres,' ',
						cl.cli_paterno,' ',
						cl.cli_materno
					) as cliente_nombre,
					IF (
						c.cred_prenda > 0,
						(SELECT pren_descripcion FROM prenda WHERE pren_codigo = c.cred_prenda),
						'Sin Datos'
					) as prenda_descripcion,
					ROUND(c.cred_monto, 1) as monto_prestado,
					DATE_FORMAT(c.cred_liquidacion, '%d/%m/%Y - %h:%i %p') as fecha_liquidacion,
					c.cred_estado as estado,
					'RECUPERACION' as tipo,					
					p.pag_codigo as pago_id,
					p.pag_serie as pago_serie,
					DATE_FORMAT(p.pag_fecha, '%d-%m-%Y') as pago_fecha,
					p.pag_concepto as concepto,
					ROUND(p.pag_subtotal, 1) as sub_total,
					ROUND(p.pag_descuento, 1) as descuento,
					ROUND(p.pag_total, 1) as total,
					ROUND(pd.pdet_monto, 1) as monto_capital
				FROM pago_detalle pd 
				INNER JOIN pago p ON p.pag_codigo = pd.pdet_pago 
				INNER JOIN credito c on c.cred_codigo = p.pag_credito
				JOIN cliente cl ON cl.cli_codigo = c.cred_cliente
				WHERE 
					pd.pdet_tipo = 'CAP' AND
					p.pag_concepto = 'REC' AND
					cred_estado = 'R' AND
					cred_sucursal = ?
			";
            $params = [$this->idsuc];
			switch ($data->mode) {
				case 'MES':
					$sql .= " AND YEAR(c.cred_liquidacion) = ? AND MONTH(c.cred_liquidacion) = ?";
                    array_push($params, $data->in_anio);
                    array_push($params, $data->in_mes);
					break;
				
				case 'ANIO':
					$sql .= " AND YEAR(c.cred_liquidacion) = ?";
                    array_push($params, $data->in_anio);
					break;

				case 'FECHA':
					$sql .= " AND DATE(c.cred_liquidacion) = ?";
                    array_push($params, $data->in_fecha);
					break;

				case 'FECHA_ACTUAL':

					$sql .= " AND DATE(c.cred_liquidacion) = ?";
                    array_push($params, date("Y-m-d"));
					break;

				case 'RANGO':
					$sql .= " AND DATE(c.cred_liquidacion) BETWEEN ? AND ?";
                    array_push($params, $data->in_rango_inicial);
                    array_push($params, $data->in_rango_final);
					break;
				
				case 'MES_ACTUAL':
					$mesAC = date("m");
					$anioAC = date("Y"); 
					$sql .= " 
						AND YEAR(c.cred_liquidacion) = ?  
						AND MONTH(c.cred_liquidacion) = ?
					";
                    array_push($params, $anioAC);
                    array_push($params, $mesAC);
					break;
			}
			$res = $this->db->query($sql, $params);
			return $res->result();
		}
        function get_all_credito_rematado($data){			
			$sql = "
				SELECT 
					c.cred_codigo as id,
					c.cred_serie as serie,
					c.cred_monto as monto,
					c.cred_monto as monto_prestado,
					'REM' as tipo,
					CONCAT(
						cl.cli_nombres,' ',
						cl.cli_paterno,' ',
						cl.cli_materno
					) as cliente_nombre,
					IF (
						c.cred_prenda > 0,
						(SELECT pren_descripcion FROM prenda WHERE pren_codigo = c.cred_prenda),
						'Sin Datos'
					) as prenda_descripcion
				FROM credito c 
				JOIN cliente cl ON cl.cli_codigo = c.cred_cliente 
				WHERE 
					c.cred_estado = 'A' AND
					c.cred_sucursal = ?
			";
            $params = [$this->idsuc];
			switch ($data->mode) {
				case 'MES':
					$sql .= "AND YEAR(c.cred_liquidacion) = ? AND MONTH(c.cred_liquidacion) = ?";
                    array_push($params, $data->in_anio);
                    array_push($params, $data->in_mes);
					break;
				
				case 'ANIO':
					$sql .= "AND YEAR(c.cred_liquidacion) = ?";
                    array_push($params, $data->in_anio);
					break;

				case 'FECHA':
					$sql .= "AND DATE(c.cred_liquidacion) = ?";
                    array_push($params, $data->in_fecha);
					break;

				case 'FECHA_ACTUAL':
					$sql .= "AND DATE(c.cred_liquidacion) = ?";
                    array_push($params, date("Y-m-d"));
					break;

				case 'RANGO':
					$sql .= "AND DATE(c.cred_liquidacion) BETWEEN ? AND ?";
                    array_push($params, $data->in_rango_inicial);
                    array_push($params, $data->in_rango_final);
					break;
				
				case 'MES_ACTUAL':
					$mesAC = date("m");
					$anioAC = date("Y"); 
					$sql .= "
						AND YEAR(c.cred_liquidacion) = ? 
						AND MONTH(c.cred_liquidacion) = ?
					";
                    array_push($params, $anioAC);
                    array_push($params, $mesAC);
					break;
			}
			$res = $this->db->query($sql, $params);
			return $res->result();
		}
		function get_all_credito_rematado_v2($data){			
			$sql = "
				SELECT 
					c.cred_codigo as credito_id,
					c.cred_serie as credito_serie,
					DATE_FORMAT(c.cred_create_at, '%d/%m/%Y - %h:%i %p') as fecha_creacion,
					CONCAT(
						cl.cli_nombres,' ',
						cl.cli_paterno,' ',
						cl.cli_materno
					) as cliente_nombre,
					IF (
						c.cred_prenda > 0,
						(SELECT pren_descripcion FROM prenda WHERE pren_codigo = c.cred_prenda),
						'Sin Datos'
					) as prenda_descripcion,
					c.cred_monto as monto_prestado,
					DATE_FORMAT(c.cred_liquidacion, '%d/%m/%Y - %h:%i %p') as fecha_liquidacion,
					c.cred_estado as estado,
					'REMATE' as tipo					
				FROM credito c 
				JOIN cliente cl ON cl.cli_codigo = c.cred_cliente 
				WHERE 
					c.cred_estado = 'A' AND
					c.cred_sucursal = ?
			";
            $params = [$this->idsuc];
			switch ($data->mode) {
				case 'MES':
					$sql .= "AND YEAR(c.cred_liquidacion) = ? AND MONTH(c.cred_liquidacion) = ?";
                    array_push($params, $data->in_anio);
                    array_push($params, $data->in_mes);
					break;
				
				case 'ANIO':
					$sql .= "AND YEAR(c.cred_liquidacion) = ?";
                    array_push($params, $data->in_anio);
					break;

				case 'FECHA':
					$sql .= "AND DATE(c.cred_liquidacion) = ?";
                    array_push($params, $data->in_fecha);
					break;

				case 'FECHA_ACTUAL':
					$sql .= "AND DATE(c.cred_liquidacion) = ?";
                    array_push($params, date("Y-m-d"));
					break;

				case 'RANGO':
					$sql .= "AND DATE(c.cred_liquidacion) BETWEEN ? AND ?";
                    array_push($params, $data->in_rango_inicial);
                    array_push($params, $data->in_rango_final);
					break;
				
				case 'MES_ACTUAL':
					$mesAC = date("m");
					$anioAC = date("Y"); 
					$sql .= "
						AND YEAR(c.cred_liquidacion) = ? 
						AND MONTH(c.cred_liquidacion) = ?
					";
                    array_push($params, $anioAC);
                    array_push($params, $mesAC);
					break;
			}
			$res = $this->db->query($sql, $params);
			return $res->result();
		}


        function get_all_pagos($data){
			$sql = "
				SELECT 
					pd.pdet_codigo as id,
					pd.pdet_tipo as tipo,
					ROUND(pd.pdet_monto, 1) as monto,
					ROUND(p.pag_subtotal, 1) as subtotal,
					ROUND(p.pag_descuento, 1) as descuento,
					ROUND(p.pag_total, 1) as total,
					DATE_FORMAT(p.pag_fecha, '%d-%m-%Y') as pago_fecha,
					p.pag_codigo as pago_id,
					p.pag_serie as pago_serie,
					c.cred_codigo as credito_id,
					c.cred_serie as credito_serie,
					IF(
						c.cred_prenda > 0,
						(SELECT pren_descripcion FROM prenda WHERE pren_codigo = c.cred_prenda),
						'SIN GARANTIA'
					) as 'prenda_descripcion',
					CONCAT(
						cl.cli_nombres,' ',
						cl.cli_paterno,' ',
						cl.cli_materno
					) as cliente_nombre
				FROM pago_detalle pd
				INNER JOIN pago p ON pdet_pago = p.pag_codigo
				INNER JOIN credito c ON p.pag_credito = c.cred_codigo  
				INNER JOIN cliente cl ON cl.cli_codigo = c.cred_cliente
				WHERE
					(pd.pdet_tipo = 'CU' OR pd.pdet_tipo = 'MO') AND
					c.cred_sucursal = ?
			";
            $params = [$this->idsuc];
			switch ($data->mode) {
				case 'MES':
					$sql .= "AND YEAR(p.pag_fecha) = ? AND MONTH(p.pag_fecha) = ?";
                    array_push($params, $data->in_anio);
                    array_push($params, $data->in_mes);
					break;
				
				case 'ANIO':
					$sql .= "AND YEAR(p.pag_fecha) = ?";
                    array_push($params, $data->in_anio);
					break;

				case 'FECHA':
					$sql .= "AND DATE(p.pag_fecha) = ?";
                    array_push($params, $data->in_fecha);
					break;

				case 'RANGO':
					$sql .= "AND DATE(p.pag_fecha) BETWEEN ? AND ?";
                    array_push($params, $data->in_rango_inicial);
                    array_push($params, $data->in_rango_final);
					break;
				
				case 'MES_ACTUAL':
					$mesAC = date("m");
					$anioAC = date("Y"); 
					$sql .= "
						AND YEAR(p.pag_fecha) = ? 
						AND MONTH(p.pag_fecha) = ?
					";
                    array_push($params, $anioAC);
                    array_push($params, $mesAC);
					break;
				case 'DIA_ACTUAL':
					$da = date("Y-m-d");
					$sql .= "AND DATE(p.pag_fecha) = ?";
					array_push($params, $da);
					break;
			}
			$res = $this->db->query($sql, $params);
			return $res->result();
		}
		function get_all_pagos_v2($data){
			$sql = "
				SELECT 
					pd.pdet_codigo as id,
					pd.pdet_tipo as tipo,
					ROUND(pd.pdet_monto, 1) as pago_monto,
					ROUND(pd.pdet_monto, 1) as monto,
					ROUND(p.pag_subtotal, 1) as subtotal,
					ROUND(p.pag_descuento, 1) as descuento,
					ROUND(p.pag_total, 1) as total,
					DATE_FORMAT(p.pag_fecha, '%d-%m-%Y') as pago_fecha,
					p.pag_codigo as pago_id,
					p.pag_serie as pago_serie,
					c.cred_codigo as credito_id,
					c.cred_serie as credito_serie,
					c.cred_monto as monto_prestado,
					c.cred_estado as credito_estado,
					IF(
						c.cred_prenda > 0,
						(SELECT pren_descripcion FROM prenda WHERE pren_codigo = c.cred_prenda),
						'SIN GARANTIA'
					) as 'prenda_descripcion',
					CONCAT(
						cl.cli_nombres,' ',
						cl.cli_paterno,' ',
						cl.cli_materno
					) as cliente_nombre
				FROM pago_detalle pd
				INNER JOIN pago p ON pdet_pago = p.pag_codigo
				INNER JOIN credito c ON p.pag_credito = c.cred_codigo  
				INNER JOIN cliente cl ON cl.cli_codigo = c.cred_cliente
				WHERE
					(pd.pdet_tipo = 'CU' OR pd.pdet_tipo = 'MO') AND
					c.cred_sucursal = ?
			";
            $params = [$this->idsuc];
			switch ($data->mode) {
				case 'MES':
					$sql .= "AND YEAR(p.pag_fecha) = ? AND MONTH(p.pag_fecha) = ?";
                    array_push($params, $data->in_anio);
                    array_push($params, $data->in_mes);
					break;
				
				case 'ANIO':
					$sql .= "AND YEAR(p.pag_fecha) = ?";
                    array_push($params, $data->in_anio);
					break;

				case 'FECHA':
					$sql .= "AND DATE(p.pag_fecha) = ?";
                    array_push($params, $data->in_fecha);
					break;

				case 'RANGO':
					$sql .= "AND DATE(p.pag_fecha) BETWEEN ? AND ?";
                    array_push($params, $data->in_rango_inicial);
                    array_push($params, $data->in_rango_final);
					break;
				
				case 'MES_ACTUAL':
					$mesAC = date("m");
					$anioAC = date("Y"); 
					$sql .= "
						AND YEAR(p.pag_fecha) = ? 
						AND MONTH(p.pag_fecha) = ?
					";
                    array_push($params, $anioAC);
                    array_push($params, $mesAC);
					break;
				case 'DIA_ACTUAL':
					$da = date("Y-m-d");
					$sql .= "AND DATE(p.pag_fecha) = ?";
					array_push($params, $da);
					break;
			}
			$res = $this->db->query($sql, $params);
			return $res->result();
		}
		function get_all_descuentos($data){
			$sql = "
				SELECT 
					p.pag_codigo as id_pago,
					p.pag_serie as serie_pago,
					c.cred_codigo as id_credito,
					c.cred_serie as serie_credito,
					p.pag_descuento as monto,
					'DESC' as tipo,
					IF(
						c.cred_prenda > 0,
						(SELECT pren_descripcion FROM prenda WHERE pren_codigo = c.cred_prenda),
						'SIN GARANTIA'
					) as 'prenda_descripcion',
					CONCAT(
						cl.cli_nombres,' ',
						cl.cli_paterno,' ',
						cl.cli_materno
					) as cliente_nombre
				FROM pago p
				INNER JOIN credito c ON p.pag_credito = c.cred_codigo  
				INNER JOIN cliente cl ON cl.cli_codigo = c.cred_cliente
				WHERE
					p.pag_descuento > 0 AND
					c.cred_sucursal = ?
			";
            $params = [$this->idsuc];
			switch ($data->mode) {
				case 'MES':
					$sql .= "AND YEAR(p.pag_fecha) = ? AND MONTH(p.pag_fecha) = ?";
                    array_push($params, $data->in_anio);
                    array_push($params, $data->in_mes);
					break;
				
				case 'ANIO':
					$sql .= "AND YEAR(p.pag_fecha) = ?";
                    array_push($params, $data->in_anio);
					break;

				case 'FECHA':
					$sql .= "AND DATE(p.pag_fecha) = ?";
                    array_push($params, $data->in_fecha);
					break;

				case 'RANGO':
					$sql .= "AND DATE(p.pag_fecha) BETWEEN ? AND ?";
                    array_push($params, $data->in_rango_inicial);
                    array_push($params, $data->in_rango_final);
					break;
				
				case 'MES_ACTUAL':
					$mesAC = date("m");
					$anioAC = date("Y"); 
					$sql .= "
						AND YEAR(p.pag_fecha) = ? 
						AND MONTH(p.pag_fecha) = ?
					";
                    array_push($params, $anioAC);
                    array_push($params, $mesAC);
					break;
				case 'DIA_ACTUAL':
					$da = date("Y-m-d");
					$sql .= "AND DATE(p.pag_fecha) = ?";
					array_push($params, $da);
					break;
				
			}
			$res = $this->db->query($sql, $params);
			return $res->result();
		}
		function get_all_descuentos_v2($data){
			$sql = "
				SELECT 
					p.pag_codigo as id_pago,
					p.pag_serie as serie_pago,
					c.cred_codigo as id_credito,
					c.cred_serie as serie_credito,
					p.pag_codigo as pago_id,
					p.pag_serie as pago_serie,
					c.cred_codigo as credito_id,
					c.cred_serie as credito_serie,
					p.pag_descuento as monto,
					p.pag_descuento as pago_monto,
					'DESC' as tipo,
					c.cred_monto as monto_prestado,
					c.cred_estado as credito_estado,
					DATE_FORMAT(p.pag_fecha, '%d-%m-%Y') as pago_fecha,
					IF(
						c.cred_prenda > 0,
						(SELECT pren_descripcion FROM prenda WHERE pren_codigo = c.cred_prenda),
						'SIN GARANTIA'
					) as 'prenda_descripcion',
					CONCAT(
						cl.cli_nombres,' ',
						cl.cli_paterno,' ',
						cl.cli_materno
					) as cliente_nombre
				FROM pago p
				INNER JOIN credito c ON p.pag_credito = c.cred_codigo  
				INNER JOIN cliente cl ON cl.cli_codigo = c.cred_cliente
				WHERE
					p.pag_descuento > 0 AND
					c.cred_sucursal = ?
			";
            $params = [$this->idsuc];
			switch ($data->mode) {
				case 'MES':
					$sql .= "AND YEAR(p.pag_fecha) = ? AND MONTH(p.pag_fecha) = ?";
                    array_push($params, $data->in_anio);
                    array_push($params, $data->in_mes);
					break;
				
				case 'ANIO':
					$sql .= "AND YEAR(p.pag_fecha) = ?";
                    array_push($params, $data->in_anio);
					break;

				case 'FECHA':
					$sql .= "AND DATE(p.pag_fecha) = ?";
                    array_push($params, $data->in_fecha);
					break;

				case 'RANGO':
					$sql .= "AND DATE(p.pag_fecha) BETWEEN ? AND ?";
                    array_push($params, $data->in_rango_inicial);
                    array_push($params, $data->in_rango_final);
					break;
				
				case 'MES_ACTUAL':
					$mesAC = date("m");
					$anioAC = date("Y"); 
					$sql .= "
						AND YEAR(p.pag_fecha) = ? 
						AND MONTH(p.pag_fecha) = ?
					";
                    array_push($params, $anioAC);
                    array_push($params, $mesAC);
					break;
				case 'DIA_ACTUAL':
					$da = date("Y-m-d");
					$sql .= "AND DATE(p.pag_fecha) = ?";
					array_push($params, $da);
					break;
				
			}
			$res = $this->db->query($sql, $params);
			return $res->result();
		}
		function get_all_adicionales($data){
			$sql = "
				SELECT 
					p.pag_codigo as id_pago,
					c.cred_codigo as id_credito,
					p.pag_adicional as monto,
					'DESC' as tipo
				FROM pago p 
				INNER JOIN credito c ON p.pag_credito = c.cred_codigo  
				WHERE
					p.pag_adicional > 0 AND
					c.cred_sucursal = ?
			";
            $params = [$this->idsuc];
			switch ($data->mode) {
				case 'MES':
					$sql .= "AND YEAR(p.pag_fecha) = ? AND MONTH(p.pag_fecha) = ?";
                    array_push($params, $data->in_anio);
                    array_push($params, $data->in_mes);
					break;
				
				case 'ANIO':
					$sql .= "AND YEAR(p.pag_fecha) = ?";
                    array_push($params, $data->in_anio);
					break;

				case 'FECHA':
					$sql .= "AND DATE(p.pag_fecha) = ?";
                    array_push($params, $data->in_fecha);
					break;

				case 'RANGO':
					$sql .= "AND DATE(p.pag_fecha) BETWEEN ? AND ?";
                    array_push($params, $data->in_rango_inicial);
                    array_push($params, $data->in_rango_final);
					break;
				
				case 'MES_ACTUAL':
					$mesAC = date("m");
					$anioAC = date("Y"); 
					$sql .= "
						AND YEAR(p.pag_fecha) = ? 
						AND MONTH(p.pag_fecha) = ?
					";
                    array_push($params, $anioAC);
                    array_push($params, $mesAC);
					break;
				case 'DIA_ACTUAL':
					$da = date("Y-m-d");
					$sql .= "AND DATE(p.pag_fecha) = ?";
					array_push($params, $da);
					break;
				
			}
			$res = $this->db->query($sql, $params);
			return $res->result();
		}
        function get_entradas_total($data){
            $sql = "
                SELECT 
                    SUM(pag_total) AS total,
                    COUNT(*) AS cantidad 
                FROM pago 
                INNER JOIN credito ON cred_codigo = pag_credito
                WHERE 
                    cred_sucursal = ? 
            ";
            $params = [$this->idsuc];
            switch ($data->mode) {
				case 'MES':
					$sql .= "AND YEAR(pag_fecha) = ? AND MONTH(pag_fecha) = ?";
					array_push($params, $data->in_anio);
                    array_push($params, $data->in_mes);
					break;
				
				case 'ANIO':
					$sql .= "AND YEAR(pag_fecha) = ?";
					array_push($params, $data->in_anio);
					break;				

				case 'FECHA':
					$sql .= "AND DATE(pag_fecha) = ?";
					array_push($params, $data->in_fecha);
					break;

				case 'RANGO':
					$sql .= "AND DATE(pag_fecha) BETWEEN ? AND ?";
					array_push($params, $data->in_rango_inicial);
                    array_push($params, $data->in_rango_final);
					break;
					
				case 'MES_ACTUAL':
					$mesAC = date("m");
					$anioAC = date("Y");
					$sql .= "AND YEAR(pag_fecha) = ? AND MONTH(pag_fecha) = ?";
					array_push($params, $anioAC);
                    array_push($params, $mesAC);
					break;
			}
			$res = $this->db->query($sql, $params);
			return $res->row();
        }        
        function get_salidas_total($data){
            $sql = "
                SELECT 
                    SUM(pag_descuento) AS total,
                    COUNT(*) AS cantidad 
                FROM pago 
                INNER JOIN credito ON cred_codigo = pag_credito
                WHERE 
                    pag_descuento > 0 AND
                    cred_sucursal = ? 
            ";
            $params = [$this->idsuc];
            switch ($data->mode) {
				case 'MES':
					$sql .= "AND YEAR(pag_fecha) = ? AND MONTH(pag_fecha) = ?";
					array_push($params, $data->in_anio);
                    array_push($params, $data->in_mes);
					break;
				
				case 'ANIO':
					$sql .= "AND YEAR(pag_fecha) = ?";
					array_push($params, $data->in_anio);
					break;				

				case 'FECHA':
					$sql .= "AND DATE(pag_fecha) = ?";
					array_push($params, $data->in_fecha);
					break;

				case 'RANGO':
					$sql .= "AND DATE(pag_fecha) BETWEEN ? AND ?";
					array_push($params, $data->in_rango_inicial);
                    array_push($params, $data->in_rango_final);
					break;
					
				case 'MES_ACTUAL':
					$mesAC = date("m");
					$anioAC = date("Y");
					$sql .= "AND YEAR(pag_fecha) = ? AND MONTH(pag_fecha) = ?";
					array_push($params, $anioAC);
                    array_push($params, $mesAC);
					break;
			}
			$res = $this->db->query($sql, $params);
			return $res->row();
        }



		function get_clientes_registrados($data){
			$sql = "
				SELECT 
					cl.cli_codigo as id,
					cl.cli_documento as documento,
					cl.cli_nombres as nombres,
					cl.cli_paterno as apellido_paterno,
					cl.cli_materno as apellido_materno,
					cl.cli_telefono as telefono,
					cl.cli_email as email
				FROM cliente cl
				WHERE
					cl.cli_codigo > 0 
			";
            $params = [];
			switch ($data->mode) {
				case 'MES':
					$sql .= " AND YEAR(cl.cli_fecha) = ? AND MONTH(cl.cli_fecha) = ?";
                    array_push($params, $data->in_anio);
                    array_push($params, $data->in_mes);
					break;
				
				case 'ANIO':
					$sql .= " AND YEAR(cl.cli_fecha) = ?";
                    array_push($params, $data->in_anio);
					break;

				case 'FECHA':
					$sql .= " AND DATE(cl.cli_fecha) = ?";
                    array_push($params, $data->in_fecha);
					break;

				case 'RANGO':
					$sql .= " AND DATE(cl.cli_fecha) BETWEEN ? AND ?";
                    array_push($params, $data->in_rango_inicial);
                    array_push($params, $data->in_rango_final);
					break;
				
				case 'MES_ACTUAL':
					$mesAC = date("m");
					$anioAC = date("Y"); 
					$sql .= " 
						AND YEAR(cl.cli_fecha) = ? 
						AND MONTH(cl.cli_fecha) = ?
					";
                    array_push($params, $anioAC);
                    array_push($params, $mesAC);
					break;
				case 'DIA_ACTUAL':
					$da = date("Y-m-d");
					$sql .= " AND DATE(cl.cli_fecha) = ?";
					array_push($params, $da);
					break;
				
			}
			$res = $this->db->query($sql, $params);
			return $res->result();
		}
    }