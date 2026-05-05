<?php  
	
	class ConductaModel extends CI_Model
	{
		function get_data($id){
			$sql = "
				SELECT 
					cond_codigo as id,
					cond_cliente as cliente,
					cond_puntuacion as puntuacion,
					cond_bloqueo_atencion as bloqueo_atencion,
					cond_bloqueo_motivo as bloqueo_motivo,
					cond_update_at as update_at,
					cond_create_at as create_at 
				FROM conducta 
				WHERE cond_cliente = ?
			";
			$res = $this->db->query($sql, [$id]);
			return $res->row();
		}
		function insert_data($data){
			return $this->db->insert('conducta', $data);
		}
		function update_data($data, $id){
			$this->db->where('cond_codigo', $id);
			return $this->db->update('conducta', $data);
		}



		// encode 05-2023
		
		
	}
?>