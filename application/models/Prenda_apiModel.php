<?php  
	
	class Prenda_apiModel extends CI_Model
	{
        function getById($id){
			$sql = "
				SELECT 
					p.pren_codigo as 'id',
					p.pren_descripcion as 'descripcion',
					DATE_FORMAT(p.pren_fecha_registro, '%d/%m/%Y') as 'create_at',
					p.pren_fecha_registro as 'create_at_raw',
					p.pren_cliente as 'cliente_id',
					CONCAT(
                        c.cli_nombres,' ', 
                        c.cli_paterno,' ',
                        c.cli_materno
                    ) as 'cliente_nombre_full',
					ct.cat_nombre as 'categoria_nombre',
					p.pren_categoria as 'categoria_id',
					tp.tpren_nombre as 'tipo_nombre',
					tp.tpren_codigo as 'tipo_id',
					p.pren_serial as 'serial',
					p.pren_marca as 'marca',
					p.pren_modelo as 'modelo',
					p.pren_quilate as 'quilate',
					p.pren_peso as 'peso',
					p.pren_placa as 'placa',
					p.pren_anio as 'anio',
					p.pren_kilometraje as 'kilometraje',
					p.pren_transmision as 'transmision',
					p.pren_color as 'color',
					p.pren_carroceria as 'carroceria',
					p.pren_observacion as 'observacion',
					p.pren_comentario as 'comentario',
					p.pren_valoracion as 'valoracion',
					p.pren_reutilizacion as 'reutilizacion',
					p.pren_estado as 'estado'	
				FROM prenda p
				INNER JOIN cliente c ON c.cli_codigo = p.pren_cliente
				INNER JOIN categoria ct ON ct.cat_codigo = p.pren_categoria
				INNER JOIN tipo_prenda tp ON tp.tpren_codigo = ct.cat_tipo
				WHERE 
                    p.pren_codigo = ?
			";
			$res = $this->db->query($sql, [$id]);
			return $res->row();
		}
		function insert($data){
			return $this->db->insert('prenda', $data);
		}
		function update($id, $data){
			$this->db->where('pren_codigo', $id);
			$this->db->update('prenda', $data);
		}
		function delete($id, $idcliente){
			return $this->db->delete('prenda', [
				'pren_codigo' => $id,
				'pren_cliente' => $idcliente
			]); 
		}
		function getPropiedadesById($id){
			$sql = "
				SELECT
					cli_codigo as cliente_id,
					CONCAT(cl.cli_nombres, ' ', cl.cli_paterno, ' ', cl.cli_materno) as 'cliente_nombre_full',
					CONCAT(e.emp_nombres, ' ', e.emp_paterno, ' ', e.emp_materno) as 'empleado_nombre_full',
					DATE_FORMAT(pr.prop_inicio, '%d/%m/%Y') as 'inicio',
					IF(pr.prop_estado = 'A', 'En posesion', pr.prop_termino) as 'termino',
					pr.prop_motivo as 'motivo',
					pr.prop_estado as 'estado'
				FROM propiedad pr 
				INNER JOIN cliente cl ON cl.cli_codigo = pr.prop_cliente 
				INNER JOIN empleado e ON e.emp_codigo = pr.prop_empleado
				WHERE pr.prop_prenda = ?
			";
			$res = $this->db->query($sql, [$id]);
			return $res->result();
		} 
		function getCreditosById($id){
			$sql = "
				SELECT 
					c.cred_codigo as 'id',
					c.cred_serie as 'serie',
					DATE_FORMAT(c.cred_creacion, '%d/%m/%Y') as 'creacion',
					c.cred_monto as 'monto',
					c.cred_estado as 'estado',
					(SELECT COUNT(*) FROM cuota WHERE cuo_credito = c.cred_codigo) as 'cuotas'
				FROM credito c 
				WHERE cred_prenda = ? 
				ORDER BY cred_codigo DESC
			";
			$res = $this->db->query($sql, [$id]);
			return $res->result();
		}		
		function getAllByCliente($idcliente, $setting = []){
			$params = [$idcliente];
			$query_order = "ORDER BY pren_codigo ASC";
			$query_search = "";
			if (!empty($setting['order'])) {
				$query_order = $setting['order'];				
			}
			if (!empty($setting['search']) && !empty($setting['search_data'])) {
				$query_search .= " AND (".$setting['search'].")";
				$params = array_merge($params, $setting['search_data']);
			}
			$sql = "
				SELECT 
					p.pren_codigo as 'id',
					p.pren_descripcion as 'descripcion',
					DATE_FORMAT(p.pren_fecha_registro, '%d/%m/%Y') as 'create_at',
					p.pren_fecha_registro as 'create_at_raw',
					ct.cat_nombre as 'categoria_nombre',
					p.pren_categoria as 'categoria_id',
					tp.tpren_nombre as 'tipo_nombre',
					tp.tpren_codigo as 'tipo_id',
					p.pren_serial as 'serial',
					p.pren_marca as 'marca',
					p.pren_modelo as 'modelo',
					p.pren_quilate as 'quilate',
					p.pren_peso as 'peso',
					p.pren_placa as 'placa',
					p.pren_anio as 'anio',
					p.pren_kilometraje as 'kilometraje',
					p.pren_transmision as 'transmision',
					p.pren_color as 'color',
					p.pren_carroceria as 'carroceria',
					p.pren_observacion as 'observacion',
					p.pren_comentario as 'comentario',
					p.pren_valoracion as 'valoracion',
					p.pren_reutilizacion as 'reutilizacion',
					p.pren_estado as 'estado',
					ult.ultimo_credito_id,
					ult.ultimo_credito_serie,
					ult.ultimo_credito_monto
				FROM prenda p
				INNER JOIN categoria ct ON ct.cat_codigo = p.pren_categoria
				INNER JOIN tipo_prenda tp ON tp.tpren_codigo = ct.cat_tipo 
				LEFT JOIN (
					SELECT 
						cred_prenda,
						cred_codigo AS ultimo_credito_id,
						cred_serie  AS ultimo_credito_serie,
						cred_monto  AS ultimo_credito_monto
					FROM (
						SELECT 
							cred_prenda,
							cred_codigo,
							cred_serie,
							cred_monto,
							ROW_NUMBER() OVER (PARTITION BY cred_prenda ORDER BY cred_codigo DESC) as rn
						FROM credito
					) c
					WHERE rn = 1
				) ult ON ult.cred_prenda = p.pren_codigo 
				WHERE 
                    p.pren_cliente = ? 
					$query_search 
					$query_order 
			";
			$res = $this->db->query($sql, $params);
			return $res->result();
		}
		function getCreditosByPrenda($id){
			$sql = "
				SELECT 
					cred_codigo as id
				FROM credito 
				WHERE cred_prenda = ?
			";
			$res = $this->db->query($sql, [$id]);
			return $res->result();
		}
		function getAllByCredito($idcredito){
			
		}
		function verifyDuplicityNew($data){
			$this->db->where($data);
			$this->db->select('pren_codigo as id');
			$this->db->from('prenda');
			$res = $this->db->get(); 
			return $res->row();
		}
		function verifyDuplicityUpdate($id,$data){
			$this->db->where($data);
			$this->db->where('pren_codigo != '.$id);
			$this->db->select('pren_codigo as id');
			$this->db->from('prenda');
			$res = $this->db->get(); 
			return $res->row();
		}
		function verifyPrendaInUse($idprenda){
			$sql = "
				SELECT 
					cred_codigo 
				FROM credito 
				WHERE
					cred_estado = 'E' AND  
					cred_prenda = ?
			";
			$res = $this->db->query($sql, [$idprenda]);
			return $res->result();
		}
		
    }
?>