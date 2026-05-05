<?php  

	class PropiedadModel extends CI_Model
	{
		

		// insertar registro de propiedad de la prenda
		function Insertar($data){
			return $this->db->insert('propiedad', $data);
		}
		// Modificar los datos de la propiedad
		function Modificar($data, $cond){
			$this->db->where($cond);
			$this->db->update('propiedad', $data);
		}
		// 
		function Eliminar($where){
			$this->db->delete('propiedad', $where); 
		}
	}
?>