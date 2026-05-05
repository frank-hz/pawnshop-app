<?php  
	/**
	 * 
	 */
	class ProrrogaModel extends CI_Model
	{
		
		function Buscar_aplicable($fecha)
		{
			$this->db->where('pror_credito', $id);
			$this->db->where("'".$inicio_mora."' >= pror_inicio");
			$this->db->where("'".$inicio_mora."' <= pror_termino");
			$this->db->select('pror_inicio as inicio');
			$this->db->select('pror_termino as termino');
			$res = $this->db->get('prorroga');
			return $res->row();

		}

		function Insertar($data){
			return $this->db->insert('prorroga', $data);
		}


	}
?>