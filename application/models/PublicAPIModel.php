<?php
    class PublicAPIModel extends CI_Model
    {
    	// Sucursal - Sede
        function sucursal__get_all(){
			$sql = "
				SELECT
					s.suc_codigo as id, 
					s.suc_nombre as nombre,
					CONCAT(u.ubi_distrito,', ',u.ubi_departamento) as ciudad, 
					s.suc_direccion as direccion,
					s.suc_telefono as telefono,
					s.suc_estado as estado,
					s.suc_sigla as sigla,
					s.suc_serie as serie,
					s.suc_pago_serie as pago_serie,
					u.ubi_codigo as ubigeo,
					u.ubi_distrito as distrito,
					u.ubi_departamento as departamento,
					u.ubi_provincia as provincia,
					DATE_FORMAT(s.suc_creacion, '%d/%m/%Y') as creacion,
					s.suc_cuenta_tipo as cuenta_tipo,
					s.suc_banco as banco,
					s.suc_cuenta_numero as cuenta_numero
				FROM sucursal s 
				INNER JOIN ubigeo u ON u.ubi_codigo = s.suc_ubigeo
				ORDER BY nombre ASC	
			";
			$res = $this->db->query($sql);
			return $res->result();
		}
		function sucursal__get_data($id){
			$sql = "
				SELECT
					s.suc_codigo as id, 
					s.suc_nombre as nombre,
					CONCAT(u.ubi_distrito,', ',u.ubi_departamento) as ciudad, 
					s.suc_direccion as direccion,
					s.suc_telefono as telefono,
					s.suc_estado as estado,
					s.suc_sigla as sigla,
					s.suc_serie as serie,
					s.suc_pago_serie as serie_pago,
					u.ubi_codigo as ubigeo,
					u.ubi_distrito as distrito,
					u.ubi_departamento as departamento,
					u.ubi_provincia as provincia,
					DATE_FORMAT(s.suc_creacion, '%d/%m/%Y') as creacion,
					s.suc_cuenta_tipo as cuenta_tipo,
					s.suc_banco as banco,
					s.suc_cuenta_numero as cuenta_numero
				FROM sucursal s 
				INNER JOIN ubigeo u ON u.ubi_codigo = s.suc_ubigeo 
				WHERE suc_codigo = ?
				ORDER BY nombre ASC	
			";
			$res = $this->db->query($sql, [$id]);
			return $res->row();
		}


		// Prenda 
		function prenda__get_all($setting = []){
			$sql = "
                SELECT 
                    pren_codigo as id,
                    cred_codigo as credito_id,
                    cred_serie as credito_serie,
                    DATE_FORMAT(
                    	cred_liquidacion, '%d-%m-%Y'
                    ) as credito_creacion,
                    DATE_FORMAT(
                    	cred_creacion, '%d-%m-%Y'
                    ) as credito_liquidacion,
                    pren_descripcion as descripcion,
                    cli_codigo as cliente_id,
                    CONCAT(cli_nombres,' ',cli_paterno,cli_materno) as cliente_nombre_full,
                    cli_documento as cliente_documento,
                    cat_nombre as categoria_nombre,
                    cat_codigo as categoria_id,
                    tpren_nombre as tipo_nombre,
                    tpren_codigo as tipo_id,
                    pren_serial as serial,
                    pren_modelo as modelo,
                    pren_precio_inicial as costo_inicial,
                    pren_costo_reparacion as costo_reparacion,
                    pren_costo_total as costo_total,
                    pren_precio_venta as precio_venta,
                    pren_valoracion as valoracion,
                    pren_estado as estado,
                    DATE_FORMAT(pren_fecha_liquida, '%d-%m-%Y') as prenda_liquidacion				
                FROM prenda 
                INNER JOIN categoria ON cat_codigo = pren_categoria
                INNER JOIN tipo_prenda ON tpren_codigo = cat_tipo 
                INNER JOIN credito ON cred_prenda = pren_codigo 
                INNER JOIN cliente ON cli_codigo = pren_cliente 
            ";

            $conditional = "";
            $order = "ORDER BY pren_codigo DESC";
            $limit = !empty($setting['limit']) ? $setting['limit'] :''; 
            $base_parameter = [];

			if (!empty($setting['query']) && !empty($setting['parameter'])){
				$wq = $setting['query'];
				$sql .= " WHERE $wq ";
				$base_parameter = $setting['parameter'];
			}


            $sql .= " $order $limit ";
            // return [
            // 	'log' => $setting,
            // 	'full' => $sql,
            // 	'pars' => $base_parameter
            // ];
            $res = $this->db->query($sql, $base_parameter);
            return $res->result();

            // return [
            // 	'sql' => $sql,
            // 	'pars' => $base_parameter,
            // 	'result' => $res->result(),
            // 	'ok' => 'ok result',
            // 	'log' => $setting
            // ];
		}



		// Tipos de Prenda
		function tipo_prenda__get_all(){
			$this->db->select('tpren_codigo as id');
			$this->db->select('tpren_nombre as nombre');
			$this->db->select('tpren_descripcion as descripcion');
			$this->db->select('tpren_estado as estado');
			$this->db->from('tipo_prenda');
			$res = $this->db->get();
			return $res->result();
		}

		
    }
    
?>