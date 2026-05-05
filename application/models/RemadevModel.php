<?php  

class RemadevModel extends CI_Model
{
	
	function get_creditos_remate(){
		$sql = "
			SELECT 
				cred_codigo as id,
				cred_liquidacion as liquidacion,
				cred_monto as monto,
				pren_codigo as prenda 
			FROM credito 
			inner join prenda on pren_codigo = cred_prenda
			WHERE cred_estado = 'A'
		";
		$res = $this->db->query($sql);
		return $res->result();
	}

	function update_prenda($data){
		$sql = "
			UPDATE prenda 
			SET 
				pren_precio_inicial = ?,
				pren_costo_total = ?,
				pren_fecha_liquida = ?,
				pren_fecha_venta = ?
			WHERE 
				pren_codigo = ? 
		";
		return $this->db->query($sql, $data);
	}

	function prenda_update_dyn(Array $params, Array $data, $id){
		// $fields = implode(", ", $params);
		$pars_string = "";
		$contAll = count($params);
		for ($i=0; $i < $contAll; $i++) { 
			$contInd = $i+1;
			$pars_string .= "$params[$i] = ?";
			$pars_string .= ($contInd===$contAll) ? "" : ",";
		}		
		array_push($data, $id);
		$sql = "UPDATE prenda SET $pars_string WHERE pren_codigo = ?";
		$this->db->query($sql, $data);
		// return $sql;
	}

	public function GetAll($sucursal){
		$sql = "
			SELECT 
				pren_codigo as id,
				cred_codigo as credito_id,
				cred_serie as credito_serie,
				pren_descripcion as descripcion,
				cli_codigo as cliente_id,
				CONCAT(cli_nombres,' ',cli_paterno,cli_materno) as cliente,
				cli_documento as cliente_doc,
				cat_nombre as categoria,
				tpren_nombre as tipo,
				pren_serial as serial,
				pren_modelo as modelo,
				pren_precio_inicial as costo_inicial,
				pren_costo_reparacion as costo_reparacion,
				pren_costo_total as costo_total,
				pren_precio_venta as precio_venta,
				pren_valoracion as valoracion,
				pren_estado as estado,
				DATE_FORMAT(pren_fecha_liquida, '%d-%m-%Y') as liquidacion				
			FROM prenda 
			INNER JOIN categoria ON cat_codigo = pren_categoria
			INNER JOIN tipo_prenda ON tpren_codigo = cat_tipo 
			INNER JOIN credito ON cred_prenda = pren_codigo 
			INNER JOIN cliente ON cli_codigo = pren_cliente
			WHERE 
				(pren_estado = 'A' OR pren_estado = 'V') AND
				cred_sucursal = ?
			ORDER BY pren_fecha_liquida DESC
		";
		$res = $this->db->query($sql, [$sucursal]);
		return $res->result();
	}

	public function GetAllByCredit($id, $sucursal){
		$sql = "
			SELECT 
				pren_codigo as id,
				cred_codigo as credito_id,
				cred_serie as credito_serie,
				pren_descripcion as descripcion,
				cli_codigo as cliente_id,
				CONCAT(cli_nombres,' ',cli_paterno,cli_materno) as cliente,
				cli_documento as cliente_doc,
				cat_nombre as categoria,
				tpren_nombre as tipo,
				pren_serial as serial,
				pren_modelo as modelo,
				pren_precio_inicial as costo_inicial,
				pren_costo_reparacion as costo_reparacion,
				pren_costo_total as costo_total,
				pren_precio_venta as precio_venta,
				pren_valoracion as valoracion,
				pren_estado as estado,
				pren_fecha_liquida as liquidacion				
			FROM prenda 
			INNER JOIN categoria ON cat_codigo = pren_categoria
			INNER JOIN tipo_prenda ON tpren_codigo = cat_tipo 
			INNER JOIN credito ON cred_prenda = pren_codigo 
			INNER JOIN cliente ON cli_codigo = pren_cliente
			WHERE 
				cred_serie LIKE CONCAT(?, '%') AND 
				cred_sucursal = ?
			ORDER BY pren_fecha_liquida DESC
		";
		$res = $this->db->query($sql, [$id, $sucursal]);
		return $res->result();
	}

	public function GetAllByClient($value, $sucursal){
		$sql = "
			SELECT 
				pren_codigo as id,
				cred_codigo as credito_id,
				cred_serie as credito_serie,
				pren_descripcion as descripcion,
				cli_codigo as cliente_id,
				CONCAT(cli_nombres,' ',cli_paterno,cli_materno) as cliente,
				cli_documento as cliente_doc,
				cat_nombre as categoria,
				tpren_nombre as tipo,
				pren_serial as serial,
				pren_modelo as modelo,
				pren_precio_inicial as costo_inicial,
				pren_costo_reparacion as costo_reparacion,
				pren_costo_total as costo_total,
				pren_precio_venta as precio_venta,
				pren_valoracion as valoracion,
				pren_estado as estado,
				pren_fecha_liquida as liquidacion				
			FROM prenda 
			INNER JOIN categoria ON cat_codigo = pren_categoria
			INNER JOIN tipo_prenda ON tpren_codigo = cat_tipo 
			INNER JOIN credito ON cred_prenda = pren_codigo 
			INNER JOIN cliente ON cli_codigo = pren_cliente
			WHERE 
				(
				cli_documento LIKE CONCAT(?, '%') OR 
				cli_nombres LIKE CONCAT(?, '%') OR 
				cli_paterno LIKE CONCAT(?, '%') OR 
				cli_materno LIKE CONCAT(?, '%')
				) AND cred_sucursal = ?
			ORDER BY pren_fecha_liquida DESC
		";
		$res = $this->db->query($sql, [$value, $value, $value, $value, $sucursal]);
		return $res->result();
	}

	public function GetAllByState($est, $sucursal){
		$sql = "
			SELECT 
				pren_codigo as id,
				cred_codigo as credito_id,
				cred_serie as credito_serie,
				pren_descripcion as descripcion,
				cli_codigo as cliente_id,
				CONCAT(cli_nombres,' ',cli_paterno,cli_materno) as cliente,
				cli_documento as cliente_doc,
				cat_nombre as categoria,
				tpren_nombre as tipo,
				pren_serial as serial,
				pren_modelo as modelo,
				pren_precio_inicial as costo_inicial,
				pren_costo_reparacion as costo_reparacion,
				pren_costo_total as costo_total,
				pren_precio_venta as precio_venta,
				pren_valoracion as valoracion,
				pren_estado as estado,
				pren_fecha_liquida as liquidacion				
			FROM prenda 
			INNER JOIN categoria ON cat_codigo = pren_categoria
			INNER JOIN tipo_prenda ON tpren_codigo = cat_tipo 
			INNER JOIN credito ON cred_prenda = pren_codigo 
			INNER JOIN cliente ON cli_codigo = pren_cliente
			WHERE 
				pren_estado = ? AND 
				cred_sucursal = ? 
			ORDER BY pren_fecha_liquida DESC
		";
		$res = $this->db->query($sql, [$est, $sucursal]);
		return $res->result();
	}
	
	public function GetAllByType($type, $sucursal){
		$sql = "
			SELECT 
				pren_codigo as id,
				cred_codigo as credito_id,
				cred_serie as credito_serie,
				pren_descripcion as descripcion,
				cli_codigo as cliente_id,
				CONCAT(cli_nombres,' ',cli_paterno,cli_materno) as cliente,
				cli_documento as cliente_doc,
				cat_nombre as categoria,
				tpren_nombre as tipo,
				pren_serial as serial,
				pren_modelo as modelo,
				pren_precio_inicial as costo_inicial,
				pren_costo_reparacion as costo_reparacion,
				pren_costo_total as costo_total,
				pren_precio_venta as precio_venta,
				pren_valoracion as valoracion,
				pren_estado as estado,
				pren_fecha_liquida as liquidacion				
			FROM prenda 
			INNER JOIN categoria ON cat_codigo = pren_categoria
			INNER JOIN tipo_prenda ON tpren_codigo = cat_tipo 
			INNER JOIN credito ON cred_prenda = pren_codigo 
			INNER JOIN cliente ON cli_codigo = pren_cliente
			WHERE 
				tpren_nombre LIKE CONCAT(?, '%') AND 
				cred_sucursal = ?
			ORDER BY pren_fecha_liquida DESC
		";
		$res = $this->db->query($sql, [$type, $sucursal]);
		return $res->result();
	}

	public function GetAllByDesc($value, $sucursal){
		$sql = "
			SELECT 
				pren_codigo as id,
				cred_codigo as credito_id,
				cred_serie as credito_serie,
				pren_descripcion as descripcion,
				cli_codigo as cliente_id,
				CONCAT(cli_nombres,' ',cli_paterno,cli_materno) as cliente,
				cli_documento as cliente_doc,
				cat_nombre as categoria,
				tpren_nombre as tipo,
				pren_serial as serial,
				pren_modelo as modelo,
				pren_precio_inicial as costo_inicial,
				pren_costo_reparacion as costo_reparacion,
				pren_costo_total as costo_total,
				pren_precio_venta as precio_venta,
				pren_valoracion as valoracion,
				pren_estado as estado,
				pren_fecha_liquida as liquidacion				
			FROM prenda 
			INNER JOIN categoria ON cat_codigo = pren_categoria
			INNER JOIN tipo_prenda ON tpren_codigo = cat_tipo 
			INNER JOIN credito ON cred_prenda = pren_codigo 
			INNER JOIN cliente ON cli_codigo = pren_cliente
			WHERE 
				pren_descripcion LIKE CONCAT(?, '%') AND 
				cred_sucursal = ? 
			ORDER BY pren_fecha_liquida DESC
		";
		$res = $this->db->query($sql, [$value, $sucursal]);
		return $res->result();
	}

	public function GetSearchById($id){
		$sql = "
			SELECT 
				pren_codigo as id,
				cred_codigo as credito_id,
				cred_serie as credito_serie,
				pren_descripcion as descripcion,
				cli_codigo as cliente_id,
				CONCAT(cli_nombres,' ',cli_paterno,cli_materno) as cliente,
				cli_documento as cliente_doc,
				cat_nombre as categoria,
				tpren_nombre as tipo,
				pren_serial as serial,
				pren_modelo as modelo,
				pren_precio_inicial as costo_inicial,
				pren_costo_reparacion as costo_reparacion,
				pren_costo_total as costo_total,
				pren_precio_venta as precio_venta,
				pren_valoracion as valoracion,
				pren_estado as estado,
				pren_fecha_liquida as liquidacion				
			FROM prenda 
			INNER JOIN categoria ON cat_codigo = pren_categoria
			INNER JOIN tipo_prenda ON tpren_codigo = cat_tipo 
			INNER JOIN credito ON cred_prenda = pren_codigo 
			INNER JOIN cliente ON cli_codigo = pren_cliente
            WHERE pren_codigo = ?
		";
		$res = $this->db->query($sql, [ $id ]);
		return $res->row();
	}

	public function GetAllByModelo($value, $sucursal){
		$sql = "
			SELECT 
				pren_codigo as id,
				cred_codigo as credito_id,
				cred_serie as credito_serie,
				pren_descripcion as descripcion,
				cli_codigo as cliente_id,
				CONCAT(cli_nombres,' ',cli_paterno,cli_materno) as cliente,
				cli_documento as cliente_doc,
				cat_nombre as categoria,
				tpren_nombre as tipo,
				pren_serial as serial,
				pren_modelo as modelo,
				pren_precio_inicial as costo_inicial,
				pren_costo_reparacion as costo_reparacion,
				pren_costo_total as costo_total,
				pren_precio_venta as precio_venta,
				pren_valoracion as valoracion,
				pren_estado as estado,
				pren_fecha_liquida as liquidacion				
			FROM prenda 
			INNER JOIN categoria ON cat_codigo = pren_categoria
			INNER JOIN tipo_prenda ON tpren_codigo = cat_tipo 
			INNER JOIN credito ON cred_prenda = pren_codigo 
			INNER JOIN cliente ON cli_codigo = pren_cliente
			WHERE 
				pren_modelo LIKE CONCAT(?, '%')  
				AND cred_sucursal = ?
			ORDER BY pren_fecha_liquida DESC
		";
		$res = $this->db->query($sql, [$value, $sucursal]);
		return $res->result();
	}

	public function GetAllBySerie($value, $sucursal){
		$sql = "
			SELECT 
				pren_codigo as id,
				cred_codigo as credito_id,
				cred_serie as credito_serie,
				pren_descripcion as descripcion,
				cli_codigo as cliente_id,
				CONCAT(cli_nombres,' ',cli_paterno,cli_materno) as cliente,
				cli_documento as cliente_doc,
				cat_nombre as categoria,
				tpren_nombre as tipo,
				pren_serial as serial,
				pren_modelo as modelo,
				pren_modelo as modelo,
				pren_precio_inicial as costo_inicial,
				pren_costo_reparacion as costo_reparacion,
				pren_costo_total as costo_total,
				pren_precio_venta as precio_venta,
				pren_valoracion as valoracion,
				pren_estado as estado,
				pren_fecha_liquida as liquidacion				
			FROM prenda 
			INNER JOIN categoria ON cat_codigo = pren_categoria
			INNER JOIN tipo_prenda ON tpren_codigo = cat_tipo 
			INNER JOIN credito ON cred_prenda = pren_codigo 
			INNER JOIN cliente ON cli_codigo = pren_cliente
			WHERE 
				pren_serial LIKE CONCAT(?, '%') 
				AND cred_sucursal = ?
			ORDER BY pren_fecha_liquida DESC
		";
		$res = $this->db->query($sql, [$value, $sucursal]);
		return $res->result();
	}

	public function getTiposPrenda(){
		$sql = "
			SELECT 
				DISTINCT tpren_nombre as value
			FROM tipo_prenda 
			INNER JOIN categoria ON cat_tipo = tpren_codigo 
			INNER JOIN prenda ON pren_categoria = cat_codigo
		";
		$res = $this->db->query($sql);
		return $res->result();
	}
	
	public function prenda_cantidad_total($suc){
		$sql = "
			SELECT count(*) as value FROM prenda 
			INNER JOIN credito ON cred_prenda = pren_codigo 
			WHERE 
				(pren_estado = 'A' OR pren_estado = 'V') AND 
				cred_sucursal = ? 
		";	
		$res = $this->db->query($sql, [$suc]);
		return $res->row();
	}

	public function prenda_cantidad_vendido($suc){
		$sql = "
			SELECT count(*) as value FROM prenda 
			INNER JOIN credito ON cred_prenda = pren_codigo 
			WHERE 
				pren_estado = 'V' AND 
				cred_sucursal = ? 
		";	
		$res = $this->db->query($sql, [$suc]);
		return $res->row();
	}

	public function prenda_cantidad_disponible($suc){
		$sql = "
			SELECT count(*) as value FROM prenda 
			INNER JOIN credito ON cred_prenda = pren_codigo 
			WHERE 
				pren_estado = 'A' AND 
				cred_sucursal = ? 
		";	
		$res = $this->db->query($sql, [$suc]);
		return $res->row();
	}


	public function get_prenda_detail($id){
		$sql = "
			SELECT 
				pren_codigo as id,
				cred_codigo as credito_id,
				cred_serie as credito_serie,
				CONCAT(
					cli_nombres,' ',
					cli_paterno,' ',
					cli_materno
				) as cliente,
				cli_codigo as cliente_id,
				pren_descripcion as descripcion,
				tpren_nombre as tipo,
				cat_nombre as categoria,
				pren_serial as serial,
				pren_modelo as modelo,
				pren_comentario as comentario,
				pren_observacion as observacion,
				pren_precio_inicial as precio_inicial,
				pren_costo_reparacion as costo_reparacion,
				pren_costo_total as costo_total,
				pren_precio_venta as precio_venta 
			FROM prenda 
			INNER JOIN credito ON cred_prenda = pren_codigo 
			INNER JOIN categoria ON cat_codigo = pren_categoria
			INNER JOIN tipo_prenda ON tpren_codigo = cat_tipo
			INNER JOIN cliente ON cli_codigo = cred_cliente 
			WHERE 
				pren_codigo = ?
		";
		$res = $this->db->query($sql, [$id]);
		return $res->row();
	}

}
?>
