<?php 
    class GastlyModel extends CI_Model
    {
        protected $id_suc;
        public function __construct(){
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
				case 'MES':
					$sql .= "AND YEAR(cred_creacion) = ? AND MONTH(cred_creacion) = ?";
					$array_attr = [$this->id_suc, $data->in_anio, $data->in_mes];
					break;
				
				case 'ANIO':
					$sql .= "AND YEAR(cred_creacion) = ?";
					$array_attr = [$this->id_suc, $data->in_anio];
					break;

				case 'FECHA':
					$sql .= "AND DATE(cred_creacion) = ?";
					$array_attr = [$this->id_suc, $data->in_fecha];
					break;

				case 'RANGO':
					$sql .= "AND DATE(cred_creacion) BETWEEN ? AND ?";
					$array_attr = [$this->id_suc, $data->in_rango_inicial, $data->in_rango_final];
					break;
				
				case 'MES_ACTUAL':
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
			$sql = "
				SELECT 
					COUNT(*) as cantidad,
					SUM(cred_monto) as total
				FROM credito 
				WHERE 
					cred_estado = 'R' AND
					cred_sucursal = ?
			";
            $params = [$this->id_suc];
			switch ($data->mode) {
				case 'MES':
					$sql .= "AND YEAR(cred_liquidacion) = ? AND MONTH(cred_liquidacion) = ?";
                    array_push($params, $data->in_anio);
                    array_push($params, $data->in_mes);
					break;
				
				case 'ANIO':
					$sql .= "AND YEAR(cred_liquidacion) = ?";
                    array_push($params, $data->in_anio);
					break;

				case 'FECHA':
					$sql .= "AND DATE(cred_liquidacion) = ?";
                    array_push($params, $data->in_fecha);
					break;

				case 'RANGO':
					$sql .= "AND DATE(cred_liquidacion) BETWEEN ? AND ?";
                    array_push($params, $data->in_rango_inicial);
                    array_push($params, $data->in_rango_final);
					break;
				
				case 'MES_ACTUAL':
					$mesAC = date("m");
					$anioAC = date("Y"); 
					$sql .= "
						AND YEAR(cred_liquidacion) = ? 
						AND MONTH(cred_liquidacion) = ?
					";
                    array_push($params, $anioAC);
                    array_push($params, $mesAC);
					break;
			}
			$res = $this->db->query($sql, $params);
			return $res->row();
		}

        function get_credito_rematado($data){			
			$sql = "
				SELECT 
					COUNT(*) as cantidad,
					SUM(cred_monto) as total
				FROM credito 
				WHERE 
					cred_estado = 'A' AND
					cred_sucursal = ?
			";
            $params = [$this->id_suc];
			switch ($data->mode) {
				case 'MES':
					$sql .= "AND YEAR(cred_liquidacion) = ? AND MONTH(cred_liquidacion) = ?";
                    array_push($params, $data->in_anio);
                    array_push($params, $data->in_mes);
					break;
				
				case 'ANIO':
					$sql .= "AND YEAR(cred_liquidacion) = ?";
                    array_push($params, $data->in_anio);
					break;

				case 'FECHA':
					$sql .= "AND DATE(cred_liquidacion) = ?";
                    array_push($params, $data->in_fecha);
					break;

				case 'RANGO':
					$sql .= "AND DATE(cred_liquidacion) BETWEEN ? AND ?";
                    array_push($params, $data->in_rango_inicial);
                    array_push($params, $data->in_rango_final);
					break;
				
				case 'MES_ACTUAL':
					$mesAC = date("m");
					$anioAC = date("Y"); 
					$sql .= "
						AND YEAR(cred_liquidacion) = ? 
						AND MONTH(cred_liquidacion) = ?
					";
                    array_push($params, $anioAC);
                    array_push($params, $mesAC);
					break;
			}
			$res = $this->db->query($sql, $params);
			return $res->row();
		}

		// 
		function get_all_credito_generado($data){
			$array_attr = [];
			$sql = "
				SELECT 
					cred_codigo as id,
					cred_serie as serie,
					cred_monto as monto,
					'GEN' as tipo
				FROM credito 
				WHERE 
					(cred_estado = 'E' OR cred_estado = 'R') AND
					cred_sucursal = ?
			";
			switch ($data->mode) {
				case 'MES':
					$sql .= "AND YEAR(cred_creacion) = ? AND MONTH(cred_creacion) = ?";
					$array_attr = [$this->id_suc, $data->in_anio, $data->in_mes];
					break;
				
				case 'ANIO':
					$sql .= "AND YEAR(cred_creacion) = ?";
					$array_attr = [$this->id_suc, $data->in_anio];
					break;

				case 'FECHA':
					$sql .= "AND DATE(cred_creacion) = ?";
					$array_attr = [$this->id_suc, $data->in_fecha];
					break;

				case 'RANGO':
					$sql .= "AND DATE(cred_creacion) BETWEEN ? AND ?";
					$array_attr = [$this->id_suc, $data->in_rango_inicial, $data->in_rango_final];
					break;
				
				case 'MES_ACTUAL':
					$mesAC = date("m");
					$anioAC = date("Y"); 
					$sql .= "AND YEAR(cred_creacion) = ? AND MONTH(cred_creacion) = ?";
					$array_attr = [$this->id_suc, $anioAC, $mesAC];
					break;
			}
			$res = $this->db->query($sql, $array_attr);
			return $res->result();
		}
		function get_all_credito_recuperado($data){			
			$sql = "
				SELECT 
					cred_codigo as id,
					cred_serie as serie,
					cred_monto as monto,
					'RECU' as tipo
				FROM credito 
				WHERE 
					cred_estado = 'R' AND
					cred_sucursal = ?
			";
            $params = [$this->id_suc];
			switch ($data->mode) {
				case 'MES':
					$sql .= "AND YEAR(cred_liquidacion) = ? AND MONTH(cred_liquidacion) = ?";
                    array_push($params, $data->in_anio);
                    array_push($params, $data->in_mes);
					break;
				
				case 'ANIO':
					$sql .= "AND YEAR(cred_liquidacion) = ?";
                    array_push($params, $data->in_anio);
					break;

				case 'FECHA':
					$sql .= "AND DATE(cred_liquidacion) = ?";
                    array_push($params, $data->in_fecha);
					break;

				case 'RANGO':
					$sql .= "AND DATE(cred_liquidacion) BETWEEN ? AND ?";
                    array_push($params, $data->in_rango_inicial);
                    array_push($params, $data->in_rango_final);
					break;
				
				case 'MES_ACTUAL':
					$mesAC = date("m");
					$anioAC = date("Y"); 
					$sql .= "
						AND YEAR(cred_liquidacion) = ? 
						AND MONTH(cred_liquidacion) = ?
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
					cred_codigo as id,
					cred_serie as serie,
					cred_monto as monto,
					'REM' as tipo
				FROM credito 
				WHERE 
					cred_estado = 'A' AND
					cred_sucursal = ?
			";
            $params = [$this->id_suc];
			switch ($data->mode) {
				case 'MES':
					$sql .= "AND YEAR(cred_liquidacion) = ? AND MONTH(cred_liquidacion) = ?";
                    array_push($params, $data->in_anio);
                    array_push($params, $data->in_mes);
					break;
				
				case 'ANIO':
					$sql .= "AND YEAR(cred_liquidacion) = ?";
                    array_push($params, $data->in_anio);
					break;

				case 'FECHA':
					$sql .= "AND DATE(cred_liquidacion) = ?";
                    array_push($params, $data->in_fecha);
					break;

				case 'RANGO':
					$sql .= "AND DATE(cred_liquidacion) BETWEEN ? AND ?";
                    array_push($params, $data->in_rango_inicial);
                    array_push($params, $data->in_rango_final);
					break;
				
				case 'MES_ACTUAL':
					$mesAC = date("m");
					$anioAC = date("Y"); 
					$sql .= "
						AND YEAR(cred_liquidacion) = ? 
						AND MONTH(cred_liquidacion) = ?
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
					pdet_codigo as id,
					pdet_tipo as tipo,
					pdet_monto as monto,
					pag_codigo as id_pago,
					pag_serie as serie_pago,
					cred_codigo as id_credito,
					cred_serie as serie_credito
				FROM pago_detalle 
				INNER JOIN pago ON pdet_pago = pag_codigo
				INNER JOIN credito ON pag_credito = cred_codigo  
				WHERE
					(pdet_tipo = 'CU' OR pdet_tipo = 'MO') AND
					cred_sucursal = ?
			";
            $params = [$this->id_suc];
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
					$sql .= "
						AND YEAR(pag_fecha) = ? 
						AND MONTH(pag_fecha) = ?
					";
                    array_push($params, $anioAC);
                    array_push($params, $mesAC);
					break;
				case 'DIA_ACTUAL':
					$da = date("Y-m-d");
					$sql .= "AND DATE(pag_fecha) = ?";
					array_push($params, $da);
					break;
			}
			$res = $this->db->query($sql, $params);
			return $res->result();
		}
		function get_all_descuentos($data){
			$sql = "
				SELECT 
					pag_codigo as id_pago,
					pag_serie as serie_pago,
					cred_codigo as id_credito,
					cred_serie as serie_credito,
					pag_descuento as monto,
					'DESC' as tipo
				FROM pago 
				INNER JOIN credito ON pag_credito = cred_codigo  
				WHERE
					pag_descuento > 0 AND
					cred_sucursal = ?
			";
            $params = [$this->id_suc];
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
					$sql .= "
						AND YEAR(pag_fecha) = ? 
						AND MONTH(pag_fecha) = ?
					";
                    array_push($params, $anioAC);
                    array_push($params, $mesAC);
					break;
				case 'DIA_ACTUAL':
					$da = date("Y-m-d");
					$sql .= "AND DATE(pag_fecha) = ?";
					array_push($params, $da);
					break;
				
			}
			$res = $this->db->query($sql, $params);
			return $res->result();
		}


        function get_data_utilidad_cuota($data){
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
            $params = [$this->id_suc];
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

        function get_data_utilidad_mora($data){
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
            $params = [$this->id_suc];
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

        function get_data_descuento($data){
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
            $params = [$this->id_suc];
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
            $params = [$this->id_suc];
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
            $params = [$this->id_suc];
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

    }
    