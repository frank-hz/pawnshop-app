<?php  
	
	class ConfiguracionModel extends CI_Model
	{
		
		function consulta($tipo = 'inicial', $p1 = '', $p2 = ''){
			switch ($tipo) {
				case 'actual':
					$sql = "CALL proc_configuracion()";
					$cons = $this->db->query($sql, array($p1, $p2));
					// mysqli_next_result( $this->db->conn_id );
					return $cons->row();
					break;
				case 'login':
					$sql = "SELECT conf_valor as 'url' FROM configuracion WHERE conf_nombre = 'LOGIN_FONDO'";
					$cons = $this->db->query($sql, array($p1, $p2));
					// mysqli_next_result( $this->db->conn_id );
					return $cons->row();
					break;
			}
		}

	}
?>	