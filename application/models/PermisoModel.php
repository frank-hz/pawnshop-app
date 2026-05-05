<?php  

class PermisoModel extends CI_Model
{
	
	public function verificar_permiso2($usuario, $permiso)
	{
		$sql = "CALL proc_permiso_usuario_verificar(?,?)";
		$res = $this->db->query($sql, array($usuario, $permiso));
		mysqli_next_result($this->db->conn_id);
		return $res->row();
	}

	public function verificar_permiso($usuario, $permiso)
	{
		$rol = $this->session->userdata('pres_ses_rol');
		$rsp = new stdClass();
		if ($rol === 'Administrador') {
			$rsp->codigo = 'P';
		} else {
			$sql = "
				SELECT 
					up_codigo as id  
				FROM usuario_permiso up
				WHERE 
					up.up_usuario = ? AND 
					up.up_permiso = ? AND
					up.up_estado = 'A'
			";
			$get_data = $this->db->query($sql, array($usuario, $permiso));
			$data = $get_data->row();
			$rsp->codigo = ($data) ? 'P' : 'D';
		}

		return $rsp;
	}
	public function select($tipo = 'rest', $par1 = '', $par2 = ''){

		switch ($tipo) {
			case 'usuario':
				$sql = "CALL proc_permiso_usuario(?)";
				$res = $this->db->query($sql, array($par1));
				mysqli_next_result($this->db->conn_id);
				return $res->result();
				break;
			case 'disponible':
				$sql = "CALL proc_permiso_disponible(?,?)";
				$res = $this->db->query($sql, array($par1, $par2));
				mysqli_next_result($this->db->conn_id);
				return $res->result();
				break;
		}
	}
	public function insert($usuario, $permiso){

		$sql = "CALL proc_permiso_usuario_registro(?,?)";
		$ins = $this->db->query($sql, array($usuario, $permiso));
		mysqli_next_result($this->db->conn_id);
		return $ins->row();

	}
	public function remover($usuario, $permiso){

		$sql = "CALL proc_permiso_usuario_eliminar(?,?)";
		$res = $this->db->query($sql, array($usuario, $permiso));
		mysqli_next_result($this->db->conn_id);
		return $res->row();
	}



	// recode: 05-2023
	function getUserData($id){
		$sql = "
			SELECT 
				usu_codigo as id,
				usu_rol as rol_id,
				rol_titulo as rol_nombre,
				rol_keyname as rol_keyname,
				emp_estado as estado
			FROM usuario 
			INNER JOIN empleado ON emp_codigo = usu_empleado
			INNER JOIN roles ON usu_rol = rol_codigo 
			WHERE usu_codigo = ?
		";
		$res = $this->db->query($sql, [$id]);
		return $res->row();
	}
	function getByUserAction($user, $action){
		$sql = "
			SELECT 
				up_codigo as id  
			FROM usuario_permiso up
			WHERE 
				up.up_usuario = ? AND 
				up.up_permiso = ? AND
				up.up_estado = 'A'
		";
		$res = $this->db->query($sql, [$user, $action]);
		return $res->row();
	}

}
?>
