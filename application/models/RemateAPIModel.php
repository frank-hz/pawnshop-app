<?php
    class RemateAPIModel extends CI_Model
    {
        function getPrendaList($sucursal){
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
                ORDER BY pren_fecha_liquida DESC LIMIT 12
            ";
            $res = $this->db->query($sql, [$sucursal]);
            return $res->result();
        }
        function getPrendaData($id){
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
                    pren_estado as estado,
                    pren_precio_inicial as precio_inicial,
                    pren_costo_reparacion as costo_reparacion,
                    pren_costo_total as costo_total,
                    pren_precio_venta as precio_venta 
                FROM prenda 
                INNER JOIN credito ON cred_prenda = pren_codigo 
                INNER JOIN categoria ON cat_codigo = pren_categoria
                INNER JOIN tipo_prenda ON tpren_codigo = cat_tipo
                INNER JOIN cliente ON cli_codigo = cred_cliente 
                WHERE pren_codigo = ?
            ";
            $res = $this->db->query($sql, [$id]);
            return $res->row();
        }
        function updateAmountsPrenda($id, $data){
            // $sql = "
			//     UPDATE prenda 
            //     SET 
            //         pren_precio_inicial = ?,
            //         pren_costo_reparacion = ?,
            //         pren_costo_total = ?,
            //         pren_precio_venta = ?,

            //         pren_fecha_liquida = ?,
            //         pren_fecha_venta = ?
            //     WHERE 
            //         pren_codigo = ? 
            // ";
            $this->db->where('pren_codigo', $id);
            return $this->db->update('prenda', $data);

            // return $this->db->query($sql, array_merge($id,$data));
        }
    }
    
?>