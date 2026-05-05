<?php 

	class CodeModel extends CI_Model
	{
		function validate_user_permission($action){
			$user = $this->session->userdata('pres_ses_usuario');
			$sql = "
				SELECT 
					COUNT(*) FROM usuario_permiso up
				WHERE 
					up.up_usuario = ? AND 
					up.up_permiso = ? AND
					up.up_estado = 'A'
			";
			$res = $this->db->query($sql, [$user, $action]);
			return $res;
		}

		
	}
	