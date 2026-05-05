<?php  
	
	class TipoPrendaModel extends CI_Model
	{
		
		function Lista()
		{
			// $this->db->where('tpren_codigo', $id);
			$this->db->select('tpren_codigo as id');
			$this->db->select('tpren_nombre as nombre');
			$this->db->select('tpren_descripcion as descripcion');
			$this->db->select('tpren_estado as estado');
			$this->db->from('tipo_prenda');
			$res = $this->db->get();
			return $res->result();
		}

		function Buscar_detalle($id)
		{
			$this->db->where('tpren_codigo', $id);
			$this->db->select('tpren_codigo as id');
			$this->db->select('tpren_nombre as nombre');
			$this->db->select('tpren_descripcion as descripcion');
			$this->db->select('tpren_estado as estado');
			$this->db->from('tipo_prenda');
			$res = $this->db->get();
			return $res->row();
		}	

		function Buscar_formulario_verifica($id)
		{
			$sql = "CALL proc_prenda_formulario_verifica(?)";
			$res = $this->db->query($sql, array($id));
			return $res->result();
		}

		function Buscar_formulario($id)
		{
			$sql = "CALL proc_prenda_formulario(?)";
			$res = $this->db->query($sql, array($id));
			return $res->result();
		}

		function Formulario_agregar($data){
			return $this->db->insert('prenda_formulario', $data);
		}

		function Formulario_remover($tipo, $atributo){
			$this->db->delete('prenda_formulario', array(
				'pform_tipo' => $tipo,
				'pform_atributo' => $atributo
			)); 
		}

		function Buscar_libre($data, $cond){
			$this->db->where($cond);
			$this->db->select($data);
			$res = $this->db->get('tipo_prenda');
			return $res->row();
		}

		function Insertar(array $data){
			return $this->db->insert('tipo_prenda', $data);
		}

		function Eliminar($id){
			$this->db->delete('tipo_prenda', array('tpren_codigo' => $id)); 
		}
		
		function SelectAll(){
			$this->db->select('tpren_codigo as id');
			$this->db->select('tpren_nombre as nombre');
			$this->db->select('tpren_estado as estado');
			$this->db->order_by('tpren_nombre', 'ASC');
			$this->db->from('tipo_prenda');
			$res = $this->db->get();
			return $res->result();	
		}



		// recode 05-2023
		function get_all(){
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