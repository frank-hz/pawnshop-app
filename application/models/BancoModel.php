<?php  
	
	class BancoModel extends CI_Model
	{	

		function activos($c_back = ''){
			$sql = "SELECT * FROM banco WHERE ban_estado = 'A'";
			$res = $this->db->query($sql);
			// if (callback == 'reset') {
				
			// }
			return $res->result();
		}

	}
?>