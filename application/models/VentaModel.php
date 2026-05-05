<?php 
	class VentaModel extends CI_Model
	{
		
		public function SetInsert($data){
			$sql = "
				INSERT INTO venta(
					`ven_empleado`, `ven_cliente_dni`, `ven_cliente_nombre`,
					`ven_cliente_celular`, `ven_metodo_pago`, `ven_subtotal`,
					`ven_descuento`, `ven_total`, `ven_comentario`,
					`ven_create_at`, `ven_update_at`
				) 
				VALUES(?,?,?,?,?,?,?,?,?,?,?)
			";
			$ins = $this->db->query($sql, $data);
			return $ins;
		}

		public function SetInsertVentaDetalle($data){
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

		public function SetDeleteVenta($id) {
			$sql = "DELETE FROM venta WHERE ven_id = ?";
			$del = $this->db->query($sql, [$id]);
			return $del;
		}

		function GetVenta($id){
			$sql = "
				SELECT  
					ven_id as id,
					ven_cliente_dni as cliente_dni,
					CONCAT(emp_nombres,' ',emp_paterno,' ',emp_materno) as empleado,
					ven_cliente_nombre as cliente_nombre,
					ven_cliente_celular as cliente_celular,
					ven_metodo_pago as metodo_pago,
					ven_subtotal as subtotal,
					ven_descuento as descuento,
					ven_comentario as comentario,
					ven_total as total,					
					DATE(ven_create_at) as creacion
				FROM venta
				INNER JOIN empleado ON emp_codigo = ven_empleado
				WHERE ven_id = ?
			";
			$res = $this->db->query($sql, [$id]);
			return $res->row();
		}

		function GetVentaDetalle($id){
			$sql = "
				SELECT  
					vdet_id as id,
					pren_descripcion as descripcion,
					tpren_nombre as tipo,
					cat_nombre as categoria,
					vdet_precio as precio,
					vdet_cantidad as cantidad,
					vdet_total as total
				FROM venta_detalle
				INNER JOIN articulo ON art_id = vdet_articulo
				INNER JOIN prenda ON pren_codigo = art_prenda
				INNER JOIN categoria ON cat_codigo = pren_categoria
				INNER JOIN tipo_prenda ON tpren_codigo = cat_tipo 
				WHERE vdet_venta = ?
			";
			$res = $this->db->query($sql, [$id]);
			return $res->result();
		}

	}
