<?php  
	
	class AnalisisModel extends CI_Model
	{	

		function BPago_cuota($fecha, $suc){
			$this->db->where("cred_sucursal", $suc);
			$this->db->where("pdet_tipo = 'CU'");
			$this->db->where("DATE(pag_fecha) = '".$fecha."'");
			$this->db->select("SUM(pdet_monto) as monto");
			$this->db->join('pago', 'pdet_pago = pag_codigo');
			$this->db->join('credito', 'cred_codigo = pag_credito');
			$res = $this->db->get('pago_detalle');
			return $res->row();
		}
		function BPago_mora($fecha, $suc){
			$this->db->where("cred_sucursal", $suc);
			$this->db->where("pdet_tipo = 'MO'");
			$this->db->where("DATE(pag_fecha) = '".$fecha."'");
			$this->db->select("SUM(pdet_monto) as monto");
			$this->db->join('pago', 'pdet_pago = pag_codigo');
			$this->db->join('credito', 'cred_codigo = pag_credito');
			$res = $this->db->get('pago_detalle');
			return $res->row();
		}
		function BPago_descuento($fecha, $suc){
			$this->db->where("cred_sucursal", $suc);
			$this->db->where("DATE(pag_fecha) = '".$fecha."'");
			$this->db->select("SUM(pag_descuento) as monto");
			$this->db->join('credito', 'cred_codigo = pag_credito');
			$res = $this->db->get('pago');
			return $res->row();
		}


		// Nuevo
		function consulta_tiempos($p = 0){
			if ($p > 0) {
				$sql = "
					SELECT DISTINCT YEAR(cred_creacion) as anio FROM credito 
					where YEAR(cred_creacion) != 0 AND cred_sucursal = ? order by YEAR(cred_creacion)
				";
				$res = $this->db->query($sql, array($p));
			}else{
				$sql = "
					SELECT DISTINCT YEAR(cred_creacion) as anio FROM credito 
					where YEAR(cred_creacion) != 0 order by YEAR(cred_creacion)
				";
				$res = $this->db->query($sql);
			}			
			return $res->result();
		}

		function consulta_registro_mensual($y, $m, $suc){
			$sql = "
				SELECT COUNT(*) as cantidad FROM credito 
				where 
					YEAR(cred_creacion) != 0 AND 
					YEAR(cred_creacion) = ? AND 
					MONTH(cred_creacion) = ? 
					AND cred_sucursal = ?
				order by YEAR(cred_creacion)
			";
			$res = $this->db->query($sql, array($y, $m, $suc));
			return $res->result();
		}

		function consulta_registro_anual($y){
			$sql = "
				SELECT COUNT(*) as cantidad FROM credito 
				where 
					YEAR(cred_creacion) != 0 AND 
					YEAR(cred_creacion) = ?
				order by YEAR(cred_creacion)
			";
			$res = $this->db->query($sql, array($y));
			return $res->row();
		}

		function consulta_credito_libre($sel, $wh){
			$this->db->where($wh);
			$this->db->select($sel);
			$res = $this->db->get('credito');
			return $res->row();
		}

		function consulta_utilidad_mensual($y, $m, $suc){
			$sql = "
				SELECT 
					SUM(pd.pdet_monto) as total 
				FROM pago p 
				INNER JOIN pago_detalle pd ON pd.pdet_pago = p.pag_codigo 
				INNER JOIN credito c ON c.cred_codigo = p.pag_credito
				WHERE 
					(pd.pdet_tipo = 'CU' OR pd.pdet_tipo = 'MO') AND
					YEAR(p.pag_fecha) != 0 AND 
					YEAR(p.pag_fecha) = ? AND 
					MONTH(p.pag_fecha) = ? AND 
					c.cred_sucursal = ?
			";
			$res = $this->db->query($sql, array($y, $m, $suc));
			return $res->result();
		}

		function categorias_prendas_registradas($suc){
			$sql = "
				SELECT 
					DISTINCT ct.cat_nombre as categoria, ct.cat_codigo as id
				FROM credito c 
				INNER JOIN prenda p ON p.pren_codigo = c.cred_prenda
				INNER JOIN categoria ct ON ct.cat_codigo = p.pren_categoria
				WHERE 
					c.cred_prenda > 0 AND 
					c.cred_sucursal = ?
				ORDER BY ct.cat_nombre ASC
			";
			$res = $this->db->query($sql, array($suc));
			return $res->result();
		}

		function categoria_info_cantidad($id){
			$sql = "
				SELECT 
					COUNT(*) as valor FROM credito 
				INNER JOIN prenda ON pren_codigo = cred_prenda 
				WHERE pren_categoria = ?
			";
			$res = $this->db->query($sql, array($id));
			return $res->row();
		}

		function categoria_info_monto($id){
			$sql = "
				SELECT 
					SUM(cred_monto) as valor FROM credito 
				INNER JOIN prenda ON pren_codigo = cred_prenda 
				WHERE pren_categoria = ?
			";
			$res = $this->db->query($sql, array($id));
			return $res->row();
		}

		function categoria_info_maximo($id){
			$sql = "
				SELECT 
					MAX(cred_monto) as valor FROM credito 
				INNER JOIN prenda ON pren_codigo = cred_prenda 
				WHERE pren_categoria = ?
			";
			$res = $this->db->query($sql, array($id));
			return $res->row();
		}

		function categoria_info_minimo($id){
			$sql = "
				SELECT 
					MIN(cred_monto) as valor FROM credito 
				INNER JOIN prenda ON pren_codigo = cred_prenda 
				WHERE pren_categoria = ?
			";
			$res = $this->db->query($sql, array($id));
			return $res->row();
		}



		// 
		function utilidad_cuota($suc, $tipo = 'general', $inicio = null, $fin = null){
			$res = [];
			if ($tipo === 'calculo') {
				$sql = "
					SELECT 
						SUM(pdet_monto) as valor 
					FROM pago_detalle
					INNER JOIN pago ON pag_codigo = pdet_pago
					INNER JOIN credito ON cred_codigo = pag_credito
					WHERE 
						cred_sucursal = ? AND 
						pdet_tipo = 'CU' AND 
						pag_fecha BETWEEN ? AND ?
				";
				$res = $this->db->query($sql, array($suc, $inicio, $fin));
			}else{
				$sql = "
					SELECT 
						SUM(pdet_monto) as valor 
					FROM pago_detalle
					INNER JOIN pago ON pag_codigo = pdet_pago
					INNER JOIN credito ON cred_codigo = pag_credito
					WHERE 
						cred_sucursal = ? AND
						pdet_tipo = 'CU'
				";
				$res = $this->db->query($sql, array($suc));
			}
			return $res->row();
		}

		function utilidad_mora($suc, $tipo = 'general', $inicio = null, $fin = null){
			$res = [];
			if ($tipo === 'calculo') {
				$sql = "
					SELECT 
						SUM(pdet_monto) as valor 
					FROM pago_detalle
					INNER JOIN pago ON pag_codigo = pdet_pago
					INNER JOIN credito ON cred_codigo = pag_credito
					WHERE 
						cred_sucursal = ? AND 
						pdet_tipo = 'MO' AND 
						pag_fecha BETWEEN ? AND ?
				";
				$res = $this->db->query($sql, array($suc, $inicio, $fin));
			}else{
				$sql = "
					SELECT 
						SUM(pdet_monto) as valor 
					FROM pago_detalle
					INNER JOIN pago ON pag_codigo = pdet_pago
					INNER JOIN credito ON cred_codigo = pag_credito
					WHERE 
						cred_sucursal = ? AND
						pdet_tipo = 'MO'
				";
				$res = $this->db->query($sql, array($suc));
			}
			return $res->row();
		}

		function utilidad_capital($suc, $tipo = 'general', $inicio = null, $fin = null){
			$res = [];
			if ($tipo === 'calculo') {
				$sql = "
					SELECT 
						SUM(pdet_monto) as valor 
					FROM pago_detalle
					INNER JOIN pago ON pag_codigo = pdet_pago
					INNER JOIN credito ON cred_codigo = pag_credito
					WHERE 
						cred_sucursal = ? AND 
						pdet_tipo = 'CAP' AND 
						pag_fecha BETWEEN ? AND ?
				";
				$res = $this->db->query($sql, array($suc, $inicio, $fin));
			}else{
				$sql = "
					SELECT 
						SUM(pdet_monto) as valor 
					FROM pago_detalle
					INNER JOIN pago ON pag_codigo = pdet_pago
					INNER JOIN credito ON cred_codigo = pag_credito
					WHERE 
						cred_sucursal = ? AND
						pdet_tipo = 'CAP'
				";
				$res = $this->db->query($sql, array($suc));
			}
			return $res->row();
		}

		function utilidad_descuento($suc, $tipo = 'general', $inicio = null, $fin = null){
			$res = [];
			if ($tipo === 'calculo') {
				$sql = "
					SELECT 
						SUM(pag_descuento) as valor 
					FROM pago
					INNER JOIN credito ON cred_codigo = pag_credito
					WHERE 
						cred_sucursal = ? AND 
						pag_fecha BETWEEN ? AND ?
				";
				$res = $this->db->query($sql, array($suc, $inicio, $fin));
			}else{
				$sql = "
					SELECT 
						SUM(pag_descuento) as valor 
					FROM pago
					INNER JOIN credito ON cred_codigo = pag_credito
					WHERE 
						cred_sucursal = ?
				";
				$res = $this->db->query($sql, array($suc));
			}
			return $res->row();
		}


		// MODIFICADO
		 function Rp_generacion_mensual($y, $m, $suc){
			$sql = "
				SELECT 
					COUNT(*) as cantidad,
					SUM(cred_monto) as monto
	
				FROM credito 
				where 
					YEAR(cred_creacion) != 0 AND 
					YEAR(cred_creacion) = ? AND 
					MONTH(cred_creacion) = ? 
					AND cred_sucursal = ?
				order by YEAR(cred_creacion)
			";
			$res = $this->db->query($sql, array($y, $m, $suc));
			return $res->row();
		}

	}
?>