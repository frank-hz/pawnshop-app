<?php  

class UbigeoModel extends CI_Model
{
	
	function consulta($tipo = 'rest', $p1 = '', $p2 = '')
	{
		switch ($tipo) {
			case 'muestra':
				// $sql = "CALL proc_ubigeo_muestra()";
				$sql = "
					SELECT 
						u.ubi_codigo as 'id', 
						u.ubi_departamento as 'departamento', 
						u.ubi_provincia as 'provincia', 
						u.ubi_distrito as 'distrito',
						u.ubi_preferencia as 'preferencia'
					FROM ubigeo u  
						ORDER BY ubi_preferencia ASC, ubi_distrito ASC LIMIT 8;	
				";
				$res = $this->db->query($sql);
				// mysqli_next_result( $this->db->conn_id );
				return $res->result();
				break;

			case 'buscar':
				// $sql = "CALL proc_ubigeo_buscar(?)";
				$sql = "
					SELECT 
						u.ubi_codigo as 'id', 
						u.ubi_departamento as 'departamento', 
						u.ubi_provincia as 'provincia', 
						u.ubi_distrito as 'distrito',
						u.ubi_preferencia as 'preferencia'
					FROM ubigeo u  
						WHERE 
							u.ubi_distrito LIKE CONCAT(?, '%')
						ORDER BY ubi_preferencia ASC, ubi_distrito ASC LIMIT 8;
				";
				$res = $this->db->query($sql, array($p1));
				// mysqli_next_result( $this->db->conn_id );
				return $res->result();
				break;
		}
	}

	function Lista($tipo, $buscar){
		// $sql = "CALL proc_ubigeo_lista(?,?,?)";
		$sql = "
			SELECT 
			u.ubi_codigo as 'id', 
			u.ubi_departamento as 'departamento', 
			u.ubi_provincia as 'provincia', 
			u.ubi_distrito as 'distrito',
			u.ubi_preferencia as 'preferencia'
		FROM ubigeo u  
		WHERE u.ubi_distrito LIKE CONCAT(?,'%')
			ORDER BY u.ubi_distrito ASC LIMIT ?

		";
		$res = $this->db->query($sql, array($buscar, 10));
		// mysqli_next_result( $this->db->conn_id );
		return $res->result();
	}

	function get_data_search($val){
		$sql = "
			SELECT 
				ubi_codigo as id,
				ubi_departamento as departamento,
				ubi_provincia as provincia,
				ubi_distrito as distrito,
				ubi_preferencia as preferencia 
			FROM ubigeo 
			WHERE ubi_distrito LIKE CONCAT(?,'%') 
			ORDER BY ubi_distrito ASC LIMIT 10 
		";
		$res = $this->db->query($sql, [$val]);
		return $res->result();
	}
}

?>
