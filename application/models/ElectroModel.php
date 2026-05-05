<?php  
	
	class ElectroModel extends CI_Model
	{	

		function consulta($tipo, $p1 = ''){
			switch ($tipo) {
				case 'credito_reciente':
					$sql = "CALL proc_electro_creditos_recientes(?)";
					$res = $this->db->query($sql, array($p1));
					mysqli_next_result( $this->db->conn_id );
					return $res->result();
					break;
				case 'cuota_reciente':
					$sql = "CALL proc_electro_cuotas_recientes(?)";
					$res = $this->db->query($sql, array($p1));
					mysqli_next_result( $this->db->conn_id );
					return $res->result();
					break;
				case 'revision_estimado':
					$sql = "CALL proc_revision_cuotas_vencidas()";
					$res = $this->db->query($sql);
					return $res->result();
					break;
				default:
					# code...
					break;
			}
		}

	}
?>