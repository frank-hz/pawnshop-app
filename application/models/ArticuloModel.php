<?php  

class ArticuloModel extends CI_Model
{
	
	public function GetAll($sucursal){
		$sql = "
			SELECT 
				art_id as id,
				art_prenda as id_prenda,
				cred_codigo as credito_id,
				cred_serie as credito_serie,
				pren_descripcion as descripcion,
				cli_codigo as cliente_id,
				CONCAT(cli_nombres,' ',cli_paterno,cli_materno) as cliente,
				cli_documento as cliente_doc,
				cat_nombre as categoria,
				tpren_nombre as tipo,
				DATE_FORMAT(art_create_at, '%d/%m/%Y') as create_at,
				DATE_FORMAT(art_update_at, '%d/%m/%Y') as update_at,
				art_costo_inicial as costo_inicial,
				art_costo_reparacion as costo_reparacion,
				art_costo_total as costo_total,
				art_precio_venta as precio_venta,
				pren_valoracion as valoracion,
				art_estado as estado,
				IF(
					art_estado = 'V',
					(SELECT ven_id FROM venta 
					INNER JOIN venta_detalle ON vdet_venta = ven_id 
					WHERE vdet_articulo = art_id ORDER BY ven_id DESC LIMIT 1 ),
					0
				) as venta_id
			FROM articulo 
			INNER JOIN prenda ON pren_codigo = art_prenda
			INNER JOIN categoria ON cat_codigo = pren_categoria
			INNER JOIN tipo_prenda ON tpren_codigo = cat_tipo 
			INNER JOIN credito ON cred_codigo = art_credito 
			INNER JOIN cliente ON cli_codigo = pren_cliente
			WHERE cred_sucursal = ?
			ORDER BY art_id DESC
		";
		$res = $this->db->query($sql, [$sucursal]);
		return $res->result();
	}

	public function GetAllByCredit($id, $sucursal){
		$sql = "
			SELECT 
				art_id as id,
				art_prenda as id_prenda,
				cred_codigo as credito_id,
				cred_serie as credito_serie,
				pren_descripcion as descripcion,
				cli_codigo as cliente_id,
				CONCAT(cli_nombres,' ',cli_paterno,cli_materno) as cliente,
				cli_documento as cliente_doc,
				cat_nombre as categoria,
				tpren_nombre as tipo,
				DATE_FORMAT(art_create_at, '%d/%m/%Y') as create_at,
				DATE_FORMAT(art_update_at, '%d/%m/%Y') as update_at,
				art_costo_inicial as costo_inicial,
				art_costo_reparacion as costo_reparacion,
				art_costo_total as costo_total,
				art_precio_venta as precio_venta,
				pren_valoracion as valoracion,
				art_estado as estado,
				IF(
					art_estado = 'V',
					(SELECT ven_id FROM venta 
					INNER JOIN venta_detalle ON vdet_venta = ven_id 
					WHERE vdet_articulo = art_id ORDER BY ven_id DESC LIMIT 1 ),
					0
				) as venta_id
			FROM articulo 
			INNER JOIN prenda ON pren_codigo = art_prenda
			INNER JOIN categoria ON cat_codigo = pren_categoria
			INNER JOIN tipo_prenda ON tpren_codigo = cat_tipo 
			INNER JOIN credito ON cred_codigo = art_credito 
			INNER JOIN cliente ON cli_codigo = pren_cliente
			WHERE 
				cred_serie LIKE CONCAT(?, '%') AND 
				cred_sucursal = ?
			ORDER BY art_id DESC
		";
		$res = $this->db->query($sql, [$id, $sucursal]);
		return $res->result();
	}

	public function GetAllByClient($value, $sucursal){
		$sql = "
			SELECT 
				art_id as id,
				art_prenda as id_prenda,
				cred_codigo as credito_id,
				cred_serie as credito_serie,
				pren_descripcion as descripcion,
				cli_codigo as cliente_id,
				CONCAT(cli_nombres,' ',cli_paterno,cli_materno) as cliente,
				cli_documento as cliente_doc,
				cat_nombre as categoria,
				tpren_nombre as tipo,
				DATE_FORMAT(art_create_at, '%d/%m/%Y') as create_at,
				DATE_FORMAT(art_update_at, '%d/%m/%Y') as update_at,
				art_costo_inicial as costo_inicial,
				art_costo_reparacion as costo_reparacion,
				art_costo_total as costo_total,
				art_precio_venta as precio_venta,
				pren_valoracion as valoracion,
				art_estado as estado,
				IF(
					art_estado = 'V',
					(SELECT ven_id FROM venta 
					INNER JOIN venta_detalle ON vdet_venta = ven_id 
					WHERE vdet_articulo = art_id ORDER BY ven_id DESC LIMIT 1 ),
					0
				) as venta_id
			FROM articulo 
			INNER JOIN prenda ON pren_codigo = art_prenda
			INNER JOIN categoria ON cat_codigo = pren_categoria
			INNER JOIN tipo_prenda ON tpren_codigo = cat_tipo  
			INNER JOIN credito ON cred_codigo = art_credito 
			INNER JOIN cliente ON cli_codigo = pren_cliente
			WHERE 
				(
				cli_documento LIKE CONCAT(?, '%') OR 
				cli_nombres LIKE CONCAT(?, '%') OR 
				cli_paterno LIKE CONCAT(?, '%') OR 
				cli_materno LIKE CONCAT(?, '%')
				) AND cred_sucursal = ?
			ORDER BY art_id DESC
		";
		$res = $this->db->query($sql, [$value, $value, $value, $value, $sucursal]);
		return $res->result();
	}

	public function GetAllByState($est, $sucursal){
		$sql = "
			SELECT 
				art_id as id,
				art_prenda as id_prenda,
				cred_codigo as credito_id,
				cred_serie as credito_serie,
				pren_descripcion as descripcion,
				cli_codigo as cliente_id,
				CONCAT(cli_nombres,' ',cli_paterno,cli_materno) as cliente,
				cli_documento as cliente_doc,
				cat_nombre as categoria,
				tpren_nombre as tipo,
				DATE_FORMAT(art_create_at, '%d/%m/%Y') as create_at,
				DATE_FORMAT(art_update_at, '%d/%m/%Y') as update_at,
				art_costo_inicial as costo_inicial,
				art_costo_reparacion as costo_reparacion,
				art_costo_total as costo_total,
				art_precio_venta as precio_venta,
				pren_valoracion as valoracion,
				art_estado as estado,
				IF(
					art_estado = 'V',
					(SELECT ven_id FROM venta 
					INNER JOIN venta_detalle ON vdet_venta = ven_id 
					WHERE vdet_articulo = art_id ORDER BY ven_id DESC LIMIT 1 ),
					0
				) as venta_id
			FROM articulo 
			INNER JOIN prenda ON pren_codigo = art_prenda
			INNER JOIN categoria ON cat_codigo = pren_categoria
			INNER JOIN tipo_prenda ON tpren_codigo = cat_tipo 
			INNER JOIN credito ON cred_codigo = art_credito 
			INNER JOIN cliente ON cli_codigo = pren_cliente
			WHERE 
				art_estado = ? AND 
				cred_sucursal = ? 
			ORDER BY art_create_at DESC
		";
		$res = $this->db->query($sql, [$est, $sucursal]);
		return $res->result();
	}
	
	public function GetAllByType($type, $sucursal){
		$sql = "
			SELECT 
				art_id as id,
				art_prenda as id_prenda,
				cred_codigo as credito_id,
				cred_serie as credito_serie,
				pren_descripcion as descripcion,
				cli_codigo as cliente_id,
				CONCAT(cli_nombres,' ',cli_paterno,cli_materno) as cliente,
				cli_documento as cliente_doc,
				cat_nombre as categoria,
				tpren_nombre as tipo,
				DATE_FORMAT(art_create_at, '%d/%m/%Y') as create_at,
				DATE_FORMAT(art_update_at, '%d/%m/%Y') as update_at,
				art_costo_inicial as costo_inicial,
				art_costo_reparacion as costo_reparacion,
				art_costo_total as costo_total,
				art_precio_venta as precio_venta,
				pren_valoracion as valoracion,
				art_estado as estado,
				IF(
					art_estado = 'V',
					(SELECT ven_id FROM venta 
					INNER JOIN venta_detalle ON vdet_venta = ven_id 
					WHERE vdet_articulo = art_id ORDER BY ven_id DESC LIMIT 1 ),
					0
				) as venta_id
			FROM articulo 
			INNER JOIN prenda ON pren_codigo = art_prenda
			INNER JOIN categoria ON cat_codigo = pren_categoria
			INNER JOIN tipo_prenda ON tpren_codigo = cat_tipo 
			INNER JOIN credito ON cred_codigo = art_credito 
			INNER JOIN cliente ON cli_codigo = pren_cliente
			WHERE 
				tpren_nombre = ? AND 
				cred_sucursal = ?
			ORDER BY art_create_at DESC
		";
		$res = $this->db->query($sql, [$type, $sucursal]);
		return $res->result();
	}

	public function GetAllByDesc($value, $sucursal){
		$sql = "
			SELECT 
				art_id as id,
				art_prenda as id_prenda,
				cred_codigo as credito_id,
				cred_serie as credito_serie,
				pren_descripcion as descripcion,
				cli_codigo as cliente_id,
				CONCAT(cli_nombres,' ',cli_paterno,cli_materno) as cliente,
				cli_documento as cliente_doc,
				cat_nombre as categoria,
				tpren_nombre as tipo,
				DATE_FORMAT(art_create_at, '%d/%m/%Y') as create_at,
				DATE_FORMAT(art_update_at, '%d/%m/%Y') as update_at,
				art_costo_inicial as costo_inicial,
				art_costo_reparacion as costo_reparacion,
				art_costo_total as costo_total,
				art_precio_venta as precio_venta,
				pren_valoracion as valoracion,
				art_estado as estado,
				IF(
					art_estado = 'V',
					(SELECT ven_id FROM venta 
					INNER JOIN venta_detalle ON vdet_venta = ven_id 
					WHERE vdet_articulo = art_id ORDER BY ven_id DESC LIMIT 1 ),
					0
				) as venta_id
			FROM articulo 
			INNER JOIN prenda ON pren_codigo = art_prenda
			INNER JOIN categoria ON cat_codigo = pren_categoria
			INNER JOIN tipo_prenda ON tpren_codigo = cat_tipo 
			INNER JOIN credito ON cred_codigo = art_credito 
			INNER JOIN cliente ON cli_codigo = pren_cliente
			WHERE 
				pren_descripcion LIKE CONCAT(?, '%') AND 
				cred_sucursal = ? 
			ORDER BY art_create_at DESC
		";
		$res = $this->db->query($sql, [$value, $sucursal]);
		return $res->result();
	}

	public function GetSearchById($id){
		$sql = "
			SELECT 
				art_id as id,
				art_prenda as id_prenda,
				pren_descripcion as descripcion,
				cat_nombre as categoria,
				pren_serial as serial,
				pren_marca as marca,
				pren_modelo as modelo,
				pren_peso as peso,
				pren_quilate as quilate,
				pren_kilometraje as kilometraje,
				pren_placa as placa,
				pren_anio as anio,
				tpren_nombre as tipo,
				DATE_FORMAT(art_create_at, '%d/%m/%Y') as create_at,
				DATE_FORMAT(art_update_at, '%d/%m/%Y') as update_at,
				art_costo_inicial as costo_inicial,
				art_costo_reparacion as costo_reparacion,
				art_costo_total as costo_total,
				art_precio_venta as precio_venta,
				pren_valoracion as valoracion,
				pren_observacion as observacion,
				pren_estado as estado_prenda,
				pren_comentario as comentario,
				art_estado as estado
			FROM articulo 
			INNER JOIN prenda ON pren_codigo = art_prenda
			INNER JOIN categoria ON cat_codigo = pren_categoria
			INNER JOIN tipo_prenda ON tpren_codigo = cat_tipo
            WHERE art_id = ?
		";
		$res = $this->db->query($sql, [ $id ]);
		return $res->row();
	}

	public function SetInsert($data){
		$sql = "
			INSERT INTO articulo(
                `art_prenda`, `art_credito`, 
				`art_estado`, `art_costo_inicial`, `art_costo_total`,
				`art_precio_venta`, `art_create_at`, `art_update_at`
            )
			VALUES(?,?,?,?,?,?,?,?)
		";
		$ins = $this->db->query($sql, $data);
		return $ins;
	}

	public function SetUpdate($id, $data){
		$sql = "
			UPDATE articulo 
				SET
					art_costo_reparacion = ?,
					art_costo_total = ?,
					art_precio_venta = ?,
					art_update_at = ?
			WHERE art_id = ?
		";
		$upd = $this->db->query($sql, [ 
			$data->costo_reparacion,
			$data->costo_total,
			$data->precio_venta,
			date("Y-m-d H:i:s"),
			$id 
		]);
		return $upd;
	}

	public function SetUpdateIDK($id, $columns, $values) {
		$cant = count($columns);
		$cols_query = "";

		for ($i=0; $i < $cant; $i++) { 
			$cols_query .= "art_".$columns[$i]." = ?";
			if ( ($cant - $i) > 1 ) $cols_query .= ",";
		}		
		$cols_query .= ",art_update_at = ?";
		array_push($values, date("Y-m-d H:i:s"));
		$sql = "
			UPDATE articulo 
				SET $cols_query 
			WHERE 
				art_id = ?
		";		
		array_push($values, $id);
		$upd = $this->db->query($sql, $values);
		return $upd;  
	}

	public function GetArticuloCreditoOrigen($id){
		$sql = "
			SELECT 
				cred_codigo as id,
				cred_monto as monto,
				cred_liquidacion as fecha
			FROM credito 
			WHERE 
				cred_prenda = ? AND 
				cred_estado = 'A'
			ORDER BY cred_codigo DESC LIMIT 1
		";
		$res = $this->db->query($sql, [$id]);
		return $res->row();
	}

	public function GetStateById($id) {
		$sql = "
			SELECT 
				art_estado as estado
			FROM articulo 
			WHERE art_id = ?
		";
		$res = $this->db->query($sql, [$id]);
		return $res->row();
	}


	function getCreditosRemate(){
		$sql = "SELECT * from credito where cred_estado = 'A'";
		$res = $this->db->query($sql);
		return $res->result();
	}

	// OLD

	// public function GetSearchFree($cols, $whr, $mode_return){
	// 	$sql = "SELECT ";
	// 	for ($i=0; $i < count($cols); $i++) {
	// 		$sql .= "art_".$cols[$i];
			
	// 		$ind = count($cols) - 1; 
	// 		if ($ind > $i) $sql .= ",";
	// 	}
	// 	$sql .= " FROM articulo WHERE ".$whr;
		
	// 	$res = $this->db->query($sql);
	// 	if ($mode_return === 'row') {
	// 		return $res->row();
	// 	}else {
	// 		return $res->result();
	// 	}
	// }

	
    // function get_by_origen($id){
	// 	$sql = "
	// 		SELECT 
	// 			art_id as id,
	// 			art_prenda as id_prenda,
	// 			pren_descripcion as descripcion,
	// 			cat_nombre as categoria,
	// 			tpren_nombre as tipo,
	// 			art_create_at as creado,
	// 			art_update_at as actualizado,
	// 			art_precio_venta as precio,
	// 			pren_valoracion as valoracion,
	// 			art_estado as estado
	// 		FROM articulo 
	// 		INNER JOIN prenda ON pren_codigo = art_prenda
	// 		INNER JOIN categoria ON cat_codigo = pren_categoria
	// 		INNER JOIN tipo_prenda ON tpren_codigo = cat_tipo
    //         WHERE art_prenda = ?
	// 	";
	// 	$res = $this->db->query($sql, [ $id ]);
	// 	return $res->row();
	// }
	
}
?>
