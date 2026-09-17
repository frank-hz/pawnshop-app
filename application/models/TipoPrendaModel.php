<?php  
	
class TipoPrendaModel extends CI_Model {
		
	// rework 2026
	function getAll($params = []){
		if (!empty($params['nombre'])){
			$this->db->like("tpren_nombre", $params['nombre']);
		}
		$this->db->select("tpren_codigo as id");
		$this->db->select("tpren_nombre as nombre");
		$this->db->select("tpren_descripcion as descripcion");
		$this->db->select("tpren_estado as estado");
		$this->db->from("tipo_prenda");
		$orden = 'ASC';
		if (!empty($params['orden']) && in_array($params['orden'], ['ASC','DESC'])){
			$orden = $params['orden'];
		}
		$this->db->order_by("tpren_nombre", $orden);                        
		$res = $this->db->get();
		return $res->result();
	}
	function getById($id){
		$this->db->select("tpren_codigo as id");
		$this->db->select("tpren_nombre as nombre");
		$this->db->select("tpren_descripcion as descripcion");
		$this->db->select("tpren_estado as estado");
		$this->db->from("tipo_prenda");                     
		$res = $this->db->get();
		return $res->row();
	}
	function insert(Array $data){
		$this->db->insert('tipo_prenda', $data);
		return ($this->db->affected_rows() > 0);
	}
	function update(int $id, $data){
		$this->db->where('tpren_codigo', $id);
		return $this->db->update('tipo_prenda', $data);
		// return ($this->db->affected_rows());
	}
	function delete(int $id){
		$this->db->where('tpren_codigo', $id);
		$this->db->delete('tipo_prenda');
		return ($this->db->affected_rows() > 0);
	}



}
?>