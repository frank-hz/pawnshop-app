<?php
    class ClienteAPIModel extends CI_Model
    {
		// Basic - CRUD
		function getAll($setting = []){
			$query_order = "ORDER BY cli_codigo ASC";
			$query_limit = " LIMIT 20";
			if (!empty($setting['order'])) {
				$query_order = $setting['order'];
			} 
			if (!empty($setting['limit'])) {
				$query_limit = $setting['limit'];
			}
			$query_search = "";
			$search_data = [];
			if (!empty($setting['search']) && !empty($setting['search_params'])){
				$query_search = $setting['search'];
				$search_data = $setting['search_params'];
			}
			$sql = "
				SELECT
					cli_codigo as 'id', 
					cli_documento as 'documento',
					cli_tipo_documento as 'tipo_documento', 
					cli_nombres as 'nombres',
					cli_paterno as 'paterno',
					cli_materno as 'materno',
					concat(
						cli_nombres,' ',
						cli_paterno,' ',
						cli_materno
					) as nombre_completo,
					ubi_distrito as 'ciudad',
					ubi_distrito as 'distrito',
					ubi_departamento as 'departamento',
					ubi_codigo as 'ubigeo',
					cli_direccion as 'direccion',
					cli_telefono as 'telefono',
					cli_email as 'email',
					cli_bloqueo_atencion as bloqueo_atencion,
					cli_bloqueo_motivo as bloqueo_motivo,
					cli_puntuacion as puntuacion			
				FROM cliente 
				INNER JOIN ubigeo ON ubi_codigo = cli_ubigeo 
				$query_search
				$query_order $query_limit
			";

			// if
			// die($sql);
			$res = $this->db->query($sql,$search_data);
			// die($res);
			return $res->result();
		}
		function getById($id){
			$sql = "
				SELECT
					cli_codigo as 'id', 
					cli_documento as 'documento',
					cli_tipo_documento as 'tipo_documento', 
					cli_nombres as 'nombres',
					cli_paterno as 'paterno',
					cli_materno as 'materno',
					CONCAT(
						cli_nombres,' ',
						cli_paterno,' ',
						cli_materno
					) as nombre_full,
					ubi_distrito as 'ciudad',
					ubi_distrito as 'distrito',
					ubi_departamento as 'departamento',
					ubi_codigo as 'ubigeo',
					cli_direccion as 'direccion',
					cli_telefono as 'telefono',
					cli_email as 'email',
                    cli_bloqueo_atencion as bloqueo_atencion,
                    cli_bloqueo_motivo as bloqueo_motivo,
                    cli_puntuacion as puntuacion			
				FROM cliente 
				INNER JOIN ubigeo ON ubi_codigo = cli_ubigeo 
				WHERE 
                    cli_codigo = ?
			";
			$res = $this->db->query($sql,[$id]);
			return $res->row();
		}
		function insert($data){
			return $this->db->insert('cliente', $data);
		}
		function update($id,$data){
			$this->db->where('cli_codigo', $id);
			$this->db->update('cliente', $data);
		}
		function delete($id){
			return $this->db->delete('cliente', ['cli_codigo' => $id]); 
		}


		// C U S T O M 
		function getByDocumento($doc, $tdoc){	
			$sql = "
				SELECT 
					cli_codigo as id
				FROM cliente 
				WHERE 
					cli_documento = ? AND 
					cli_tipo_documento = ?
			";
			$res = $this->db->query($sql, [$doc,$tdoc]);
			return $res->row();
		}
		function verifyDuplicityNew($data){
			$this->db->where($data);
			$this->db->select('cli_codigo as id');
			$this->db->from('cliente');
			$res = $this->db->get(); 
			return $res->row();
		}
		function verifyDuplicityUpdate($id,$data){
			$this->db->where($data);
			$this->db->where('cli_codigo != '.$id);
			$this->db->select('cli_codigo as id');
			$this->db->from('cliente');
			$res = $this->db->get(); 
			return $res->row();
		}
		function verifyDuplicityDocNew($doc, $tipo_doc){
			$this->db->where('cli_documento',$doc);
			$this->db->where('cli_tipo_documento',$tipo_doc);
			$this->db->select('cli_codigo as id');
			$this->db->from('cliente');
			$res = $this->db->get(); 
			return $res->row();
		}
		function verifyDuplicityDocUpdate($id,$data){
			$this->db->where($data);
			$this->db->where('cli_codigo != '.$id);
			$this->db->select('cli_codigo as id');
			$this->db->from('cliente');
			$res = $this->db->get(); 
			return $res->row();
		}
		function is_unique_fullname($nombres, $paterno, $materno) {
			$this->db->where('cli_nombres', $nombres);
			$this->db->where('cli_paterno', $paterno);
			$this->db->where('cli_materno', $materno);
			$query = $this->db->get('cliente');	
			if ($query->num_rows() > 0) {
				return FALSE;
			} else {
				return TRUE;
			}
		}


		// Conducta
		function getConductaById($id){
			$sql = "
				SELECT 
					cli_codigo as id,
					cli_puntuacion as puntuacion,
					cli_bloqueo_atencion as bloqueo_atencion,
					cli_bloqueo_motivo as bloqueo_motivo
				FROM cliente 
				WHERE cli_codigo = ?
			";
			$res = $this->db->query($sql, [$id]);
			return $res->row();
		}
        
    }
    
?>