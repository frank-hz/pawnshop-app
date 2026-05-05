<?php  

class RemateModel extends CI_Model
{
	function SetInsertVenta($data){
        $sql = "
			INSERT INTO venta(
                `ven_cliente_dni`, `ven_cliente_nombre`, `ven_cliente_paterno`, 
				`ven_cliente_materno`, `ven_empleado`, `ven_subtotal`, 
				`ven_descuento`, `ven_total`,
				`ven_create_at`, `ven_update_at`
            )
			VALUES(?,?,?,?,?,?,?,?,?,?)
		";
		$ins = $this->db->query($sql, $data);
		return $ins;
    }	

    function SetInsertVentaDetalle($data){
        $sql = "
			INSERT INTO venta_detalle(
				`vdet_venta`,
                `vdet_articulo`, `vdet_precio`, 
                `vdet_cantidad`, `vdet_total`
            )
			VALUES(?,?,?,?,?)
		";
		$ins = $this->db->query($sql, $data);
		return $ins;
    }

	function GetVenta($id){
		$sql = "
			SELECT  
				ven_cliente_dni as cliente_dni,
				ven_cliente_nombre as cliente_nombre,
				ven_cliente_paterno as cliente_paterno,
				ven_cliente_materno as cliente_materno,
				CONCAT(emp_nombres,' ',emp_paterno,' ',emp_materno) as empleado,
				ven_subtotal as subtotal,
				ven_descuento as descuento,
				ven_total as total,
				DATE(ven_create_at) as creacion,
				pren_descripcion as descripcion,
				tpren_nombre as tipo,
				cat_nombre as categoria,
				vdet_precio as precio,
				vdet_cantidad as cantidad,
				vdet_total as total
			FROM venta
			INNER JOIN empleado ON emp_codigo = ven_empleado
			INNER JOIN venta_detalle ON vdet_venta = ven_id
			INNER JOIN articulo ON art_id = vdet_articulo
			INNER JOIN prenda ON pren_codigo = art_id
			INNER JOIN categoria ON cat_codigo = pren_categoria
			INNER JOIN tipo_prenda ON tpren_codigo = cat_tipo 
			WHERE vdet_articulo = ?
		";
		$res = $this->db->query($sql, [$id]);
		return $res->row();
	}

}
?>
