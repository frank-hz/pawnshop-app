<?php  
	
	class AnalisisModel extends CI_Model
	{	
		// Consulta la lista de a«Ðos basado en las 
		// fechas minimas y maximas de registro de creditos
		function Get_LineaTemporal($suc)
		{
			$sql = "
				SELECT DISTINCT YEAR(cred_creacion) as nombre FROM credito 
				WHERE YEAR(cred_creacion) != 0 AND cred_sucursal = ? ORDER BY YEAR(cred_creacion) DESC
			";
			$res = $this->db->query($sql, array($suc));
			return $res->result();
		}

		// Bloque 1
		function Get_UtilidadBruta_Anual($suc, $an){
			$sql = "
				SELECT 
					SUM(pd.pdet_monto) as monto 
				FROM pago p 
				INNER JOIN pago_detalle pd ON pd.pdet_pago = p.pag_codigo 
				INNER JOIN credito c ON c.cred_codigo = p.pag_credito
				WHERE 
					(pd.pdet_tipo = 'CU' OR pd.pdet_tipo = 'MO') AND
					YEAR(p.pag_fecha) != 0 AND 
					YEAR(p.pag_fecha) = ? AND
					c.cred_sucursal = ?
			";
			$res = $this->db->query($sql, array($an, $suc));
			return $res->row();
		}

		function Get_UtilidadxCuota_Anual($suc, $an){
			$sql = "
				SELECT 
					SUM(pd.pdet_monto) as monto 
				FROM pago p 
				INNER JOIN pago_detalle pd ON pd.pdet_pago = p.pag_codigo 
				INNER JOIN credito c ON c.cred_codigo = p.pag_credito
				WHERE 
					pd.pdet_tipo = 'CU' AND
					YEAR(p.pag_fecha) != 0 AND 
					YEAR(p.pag_fecha) = ? AND
					c.cred_sucursal = ?
			";
			$res = $this->db->query($sql, array($an, $suc));
			return $res->row();
		}

		function Get_UtilidadxMora_Anual($suc, $an){
			$sql = "
				SELECT 
					SUM(pd.pdet_monto) as monto 
				FROM pago p 
				INNER JOIN pago_detalle pd ON pd.pdet_pago = p.pag_codigo 
				INNER JOIN credito c ON c.cred_codigo = p.pag_credito
				WHERE 
					pd.pdet_tipo = 'MO' AND
					YEAR(p.pag_fecha) != 0 AND 
					YEAR(p.pag_fecha) = ? AND
					c.cred_sucursal = ?
			";
			$res = $this->db->query($sql, array($an, $suc));
			return $res->row();
		}
		
		function Get_UtilidadBruta_Mensual($a, $m, $suc)
		{
			$sql = "
				SELECT 
					SUM(pd.pdet_monto) as monto 
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
			$res = $this->db->query($sql, array($a, $m, $suc));
			return $res->row();
		}

		function Get_UtilidadxCuota_Calculado($suc, $inicio, $final){
			$sql = "
				SELECT 
					SUM(pd.pdet_monto) as monto 
				FROM pago p 
				INNER JOIN pago_detalle pd ON pd.pdet_pago = p.pag_codigo 
				INNER JOIN credito c ON c.cred_codigo = p.pag_credito
				WHERE 
					pd.pdet_tipo = 'CU' AND
					YEAR(p.pag_fecha) != 0 AND 
					c.cred_sucursal = ? AND
					DATE(p.pag_fecha) BETWEEN ? AND ? 
			";
			$res = $this->db->query($sql, array($suc, $inicio, $final));
			return $res->row();
		}

		function Get_UtilidadxMora_Calculado($suc, $inicio, $final){
			$sql = "
				SELECT 
					SUM(pd.pdet_monto) as monto 
				FROM pago p 
				INNER JOIN pago_detalle pd ON pd.pdet_pago = p.pag_codigo 
				INNER JOIN credito c ON c.cred_codigo = p.pag_credito
				WHERE 
					pd.pdet_tipo = 'MO' AND
					YEAR(p.pag_fecha) != 0 AND 
					c.cred_sucursal = ? AND
					DATE(p.pag_fecha) BETWEEN ? AND ? 
			";
			$res = $this->db->query($sql, array($suc, $inicio, $final));
			return $res->row();
		}

		function Get_GeneracionTotal($suc, $inicio, $final){
			$sql = "
				SELECT 
					COUNT(*) as cantidad,
					SUM(cred_monto) as monto
				FROM credito 
				WHERE 
					YEAR(cred_creacion) != 0
					AND cred_sucursal = ? 
					AND cred_creacion BETWEEN ? AND ?
				order by YEAR(cred_creacion)
			";
			$res = $this->db->query($sql, array($suc, $inicio, $final));
			return $res->row();
		}

		function Get_GeneracionEstado($suc, $inicio, $final, $estado){
			$sql = "
				SELECT 
					COUNT(*) as cantidad,
					SUM(cred_monto) as monto
				FROM credito 
				WHERE 
					YEAR(cred_creacion) != 0
					AND cred_sucursal = ? 
					AND cred_estado = ?
					AND cred_creacion BETWEEN ? AND ? 
				order by YEAR(cred_creacion)
			";
			$res = $this->db->query($sql, array($suc, $estado, $inicio, $final));
			return $res->row();
		}

		function Get_GeneracionAnual($suc, $a){
			$sql = "
				SELECT 
					COUNT(*) as cantidad,
					SUM(cred_monto) as monto
				FROM credito 
				WHERE 
					YEAR(cred_creacion) != 0
					AND cred_sucursal = ? 
					AND YEAR(cred_creacion) = ?
				order by YEAR(cred_creacion)
			";
			$res = $this->db->query($sql, array($suc, $a));
			return $res->row();
		}

		function Get_GeneracionMensual($suc, $a, $m){
			$sql = "
				SELECT 
					COUNT(*) as cantidad,
					SUM(cred_monto) as monto
				FROM credito 
				WHERE 
					YEAR(cred_creacion) != 0
					AND cred_sucursal = ? 
					AND YEAR(cred_creacion) = ?
					AND MONTH(cred_creacion) = ?
				order by YEAR(cred_creacion)
			";
			$res = $this->db->query($sql, array($suc, $a, $m));
			return $res->row();
		}

		function Get_UtilidadxCuota_Dia($suc, $fecha){
			$sql = "
				SELECT 
					SUM(pd.pdet_monto) as monto 
				FROM pago p 
				INNER JOIN pago_detalle pd ON pd.pdet_pago = p.pag_codigo 
				INNER JOIN credito c ON c.cred_codigo = p.pag_credito
				WHERE 
					pd.pdet_tipo = 'CU' AND
					YEAR(p.pag_fecha) != 0 AND 
					c.cred_sucursal = ? AND
					DATE(p.pag_fecha) = ?
			";
			$res = $this->db->query($sql, array($suc, $fecha));
			return $res->row();
		}

		function Get_UtilidadxMora_Dia($suc, $fecha){
			$sql = "
				SELECT 
					SUM(pd.pdet_monto) as monto 
				FROM pago p 
				INNER JOIN pago_detalle pd ON pd.pdet_pago = p.pag_codigo 
				INNER JOIN credito c ON c.cred_codigo = p.pag_credito
				WHERE 
					pd.pdet_tipo = 'MO' AND
					YEAR(p.pag_fecha) != 0 AND 
					c.cred_sucursal = ? AND
					DATE(p.pag_fecha) = ?
			";
			$res = $this->db->query($sql, array($suc, $fecha));
			return $res->row();
		}

		function Get_Descuento_Dia($suc, $fecha){
			$sql = "
				SELECT 
					SUM(pag_descuento) as monto 
				FROM pago p  
				INNER JOIN credito c ON c.cred_codigo = p.pag_credito
				WHERE 
					YEAR(p.pag_fecha) != 0 AND 
					c.cred_sucursal = ? AND
					DATE(p.pag_fecha) = ?
			";
			$res = $this->db->query($sql, array($suc, $fecha));
			return $res->row();
		}

		

		// --------------

		function Get_GeneracionCreditosMensual($y, $m, $suc)
		{
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

		function Get_UtilidadMensual($y, $m, $suc)
		{
			$sql = "
				SELECT 
					SUM(pd.pdet_monto) as suma 
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
			return $res->row();
		}

		function Get_Inventario($suc){
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

		function Get_PrendasxCategoria($id){
			$sql = "
				SELECT 
					COUNT(*) as valor FROM credito 
				INNER JOIN prenda ON pren_codigo = cred_prenda 
				WHERE pren_categoria = ?
			";
			$res = $this->db->query($sql, array($id));
			return $res->row();
		}

		function Get_MontoxCategoria($id){
			$sql = "
				SELECT 
					SUM(cred_monto) as valor FROM credito 
				INNER JOIN prenda ON pren_codigo = cred_prenda 
				WHERE pren_categoria = ?
			";
			$res = $this->db->query($sql, array($id));
			return $res->row();
		}

		function Get_CategoriaMontoMaximo($id){
			$sql = "
				SELECT 
					MAX(cred_monto) as valor FROM credito 
				INNER JOIN prenda ON pren_codigo = cred_prenda 
				WHERE pren_categoria = ?
			";
			$res = $this->db->query($sql, array($id));
			return $res->row();
		}

		function Get_CategoriaMontoMinimo($id){
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

		function prot__get_pagos_anual($year, $suc){
			$this->db->where('YEAR(pag_fecha)', $year);
			$this->db->where('cred_sucursal', $suc);
			$this->db->select('pag_codigo as id');
			$this->db->select('cred_codigo as credito');
			$this->db->select('pag_serie as serie');
			$this->db->select('pag_forma_pago as forma_pago');
			$this->db->select('pag_descuento as descuento');
			$this->db->join('credito', 'cred_codigo = pag_credito');
			$this->db->from('pago');
			$this->db->order_by('pag_fecha', 'ASC');
			$res = $this->db->get();
			return $res->result();
		}

		function prot__get_pagos_mensual($year, $month, $suc){
			$this->db->where('YEAR(pag_fecha)', $year);
			$this->db->where('MONTH(pag_fecha)', $month);
			$this->db->where('cred_sucursal', $suc);
			$this->db->select('pag_codigo as id');
			$this->db->select('cred_codigo as credito');
			$this->db->select('pag_serie as serie');
			$this->db->select('pag_forma_pago as forma_pago');
			$this->db->select('pag_descuento as descuento');
			$this->db->join('credito', 'cred_codigo = pag_credito');
			$this->db->from('pago');
			$this->db->order_by('pag_fecha', 'ASC');
			$res = $this->db->get();
			return $res->result();
		}



		function prot__get_pago_detalle($id){
			$this->db->where('pdet_pago', $id);
			$this->db->select('pdet_codigo as id');
			$this->db->select('pag_serie as serie');
			$this->db->select('pdet_tipo as tipo');
			$this->db->select('pdet_monto as monto');

			$this->db->join('credito', 'cred_codigo = pag_credito');
			$this->db->join('pago_detalle', 'pdet_pago = pag_codigo');
			$this->db->from('pago');
			$this->db->order_by('pag_fecha', 'ASC');
			$res = $this->db->get();
			return $res->result();
		}

		function prot__get_pagosdet_mensual($a, $m, $suc){
			$this->db->where('YEAR(pag_fecha)', $a);
			$this->db->where('MONTH(pag_fecha)', $m);
			$this->db->where('cred_sucursal', $suc);
			$this->db->select('pdet_codigo as id');
			$this->db->select('pag_serie as serie');
			$this->db->select('pdet_tipo as tipo');
			$this->db->select('pdet_monto as monto');

			$this->db->join('credito', 'cred_codigo = pag_credito');
			$this->db->join('pago_detalle', 'pdet_pago = pag_codigo');
			$this->db->from('pago');
			$this->db->order_by('pag_fecha', 'ASC');
			$res = $this->db->get();
			return $res->result();
		}

	}
?>