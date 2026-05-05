<?php  
	
	class CategoriaModel extends CI_Model
	{
		
		function consulta($tipo = 'lista', $p1 = '', $p2 = '')
		{
			switch ($tipo) {
				case 'lista':
					$sql = "CALL proc_categoria_lista(?,?)";
					$res = $this->db->query($sql, array($p1, $p2));
					mysqli_next_result( $this->db->conn_id );
					return $res->result();
					break;

				case 'por_tipo':
					$sql = "CALL proc_categoria_lista_tipo(?)";
					$res = $this->db->query($sql, array($p1));
					return $res->result();
					break;

				case 'buscar_nombre':
					$sql = "CALL proc_categoria_buscar_nombre(?)";
					$res = $this->db->query($sql, array($p1));
					return $res->result();
					break;
				
				case 'informacion':
					$sql = "CALL proc_categoria_informacion()";
					$res = $this->db->query($sql);
					mysqli_next_result( $this->db->conn_id );
					return $res->row();
					break;
			}
		}

		function Eliminar($id){
			$this->db->where('cat_codigo', $id);
			$this->db->delete('categoria');
		}

		function Eliminar_x_tipo($tipo){
			$this->db->where('cat_tipo', $tipo);
			$this->db->delete('categoria');
		}

		function Insertar($data){
			return $this->db->insert('categoria', $data);
		}

		function Modificar($id, $data){
			$this->db->where('cat_codigo', $id);
			$this->db->update('categoria', $data);
		}

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

		// NEW CODE
		function SelectAll(){
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

		function SelectByTipo($tipo){
			$this->db->where('cat_tipo', $tipo);
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