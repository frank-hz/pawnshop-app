<?php  

class MenuModel extends CI_Model
{
	
	function consulta($tipo = 'rest', $p1 = '', $p2 = '')
	{
		switch ($tipo) {
			case 'usuario':
				$sql  = "CALL proc_menu_usuario(?)";
				$cons = $this->db->query($sql, array($p1));
				mysqli_next_result( $this->db->conn_id );
				return $cons->result();
				break;
		}
	}

	function get_data_modules_admin(){
		$sql = "
			SELECT 
				mod_codigo as id,
				mod_titulo as titulo,
				mod_descripcion as descripcion,
				mod_ruta as ruta,
				mod_url_ref as url_ref,
				mod_icono as icono,
				'admin' AS 'rol'
			FROM modulo 
			WHERE mod_estado = 'A' 
			ORDER BY mod_titulo ASC
		";
		$res = $this->db->query($sql);
		return $res->result();
	}

	function get_data_modules($id){
		$sql = "
			SELECT 
				mod_codigo as id,
				men_usuario as usuario,
				mod_titulo as titulo,
				mod_descripcion as descripcion,
				mod_ruta as ruta,
				mod_icono as icono,
				mod_url_ref as url_ref,
				'empleado' AS 'mod_rol'
			FROM menu me  
			INNER JOIN modulo ON mod_codigo = men_modulo  
			WHERE 
				men_usuario = ?  
			ORDER BY mod_titulo ASC
		";
		$res = $this->db->query($sql, [$id]);
		return $res->result();
	}


}
?>