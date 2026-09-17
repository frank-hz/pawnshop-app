<?php  
	
	class CategoriaModel extends CI_Model{
		
		// REWORK 2026
		function getAll($params = []){
			if (!empty($params['tipo_nombre'])){
				$this->db->where("t.tpren_nombre", $params['tipo_nombre']);
			}
			if (!empty($params['tipo'])){
				$this->db->where("c.cat_tipo", $params['tipo']);
			}
			if (!empty($params['tipos_group'])){
				$this->db->where_in('c.cat_tipo', $params['tipos_group']);
				$group = $params['tipos_group'];
				// $this->db->group_start();
				// foreach ($group as $tipo) {
				// 	$this->db->or_where("c.cat_tipo", $tipo);	
				// }
				// $this->db->group_end();
			}
			if (!empty($params['nombre'])){
				$this->db->like("c.cat_nombre", $params['nombre']);
			}
			$this->db->select("c.cat_codigo as id");
			$this->db->select("c.cat_nombre as nombre");
			$this->db->select("t.tpren_codigo as tipo_prenda_id");
			$this->db->select("t.tpren_nombre as tipo_prenda_nombre");
			$this->db->select("t.tpren_codigo as tipo_id");
			$this->db->select("t.tpren_nombre as tipo_nombre");
			$this->db->from("categoria c");
			$this->db->join("tipo_prenda t", "c.cat_tipo = t.tpren_codigo");
			
			$orden = !empty($params['orden']) ? $params['orden'] : 'ASC';
			$this->db->order_by("c.cat_nombre", $orden);                        
			$res = $this->db->get();
			return $res->result();
		}
		function getById($id){
			$this->db->where("c.cat_codigo", $id);
			$this->db->select("c.cat_codigo as id");
			$this->db->select("c.cat_nombre as nombre");
			$this->db->select("t.tpren_codigo as tipo_prenda_id");
			$this->db->select("t.tpren_nombre as tipo_prenda_nombre");
			$this->db->select("t.tpren_descripcion as tipo_prenda_descripcion");
			$this->db->select("t.tpren_codigo as tipo_id");
			$this->db->select("t.tpren_nombre as tipo_nombre");
			$this->db->select("t.tpren_descripcion as tipo_descripcion");
			$this->db->join("tipo_prenda t", "c.cat_tipo = t.tpren_codigo");
			$this->db->order_by("c.cat_nombre", "ASC");                        
			$res = $this->db->get("categoria c");
			return $res->row();
		}
		function insert(Array $data){
			$this->db->insert('categoria', $data);
			return ($this->db->affected_rows() > 0);
		}
		function update(int $id, $data){
			$this->db->where('cat_codigo', $id);
			return $this->db->update('categoria', $data);
			// return ($this->db->affected_rows());
		}
		function delete(int $id){
			$this->db->where('cat_codigo', $id);
			$this->db->delete('categoria');
			return ($this->db->affected_rows() > 0);
		}
		function EsDuplicado($data, $id = 0){
			if (empty($data) || !is_array($data)) {
				return FALSE;
			}
			$this->db->where($data);
			if (!empty($id)) {
				$this->db->where('cat_codigo !=', $id);
			}
			$this->db->from('categoria');
			return $this->db->count_all_results() > 0;
		}
		function countPrendasByCategoria($id){
			$this->db->from("prenda");
			$this->db->where("pren_categoria", $id);
			return $this->db->count_all_results();
		}


		// OLD
		function Lista($where, $buscar){
			if ($where === 'tipo') {
				$this->db->where("cat_tipo", $buscar);
			}
			$this->db->select('cat_codigo as id');
			$this->db->select('cat_nombre as nombre');
			$this->db->select('tpren_codigo as tipo_id');
			$this->db->select('tpren_nombre as tipo');
			$this->db->select('cat_estado as estado');
			$this->db->order_by('cat_nombre', 'ASC');
			$this->db->from('categoria');
			$this->db->join('tipo_prenda', 'tpren_codigo = cat_tipo');
			$this->db->order_by('cat_tipo', 'ASC');
			$res = $this->db->get();
			return $res->result();	
		}
		// recode 05-2023
		function getByTipo($id_or_name){
			$this->db->where('cat_tipo', $id_or_name);
			$this->db->or_where('tpren_nombre', $id_or_name);
			$this->db->select('cat_codigo as id');
			$this->db->select('cat_nombre as nombre');
			$this->db->select('tpren_codigo as tipo_id');
			$this->db->select('tpren_nombre as tipo');
			$this->db->select('cat_estado as estado');
			$this->db->order_by('cat_nombre', 'ASC');
			$this->db->from('categoria');
			$this->db->join('tipo_prenda', 'tpren_codigo = cat_tipo');
			$this->db->order_by('cat_tipo', 'ASC');
			$res = $this->db->get();
			return $res->result();	
		}

	}
?>