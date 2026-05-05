<?php  
	
	class InformacionModel extends CI_Model
	{	

		function cliente_total(){
			return $this->db->count_all('cliente');
		}

		function cliente_hoy($fecha){
			// $this->db->where('cli_fecha', $fecha);
			// return $this->db->count_all('cliente');
			$sql = "select count(*) as 'cant' from cliente where cli_fecha = ?";
			$res = $this->db->query($sql, array($fecha));
			return $res->row()->cant;
		}

	}
?>