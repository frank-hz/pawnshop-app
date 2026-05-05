<?php  
	
	class DebitacionModel extends CI_Model
	{	

		// 
		function Buscar($id){
			$this->db->where('deb_credito', $id);
			$this->db->select('deb_codigo as id');
			$this->db->select('DATE_FORMAT(deb_fecha, "%d/%m/%Y") as fecha');
			$this->db->select('deb_monto as monto');
			$this->db->select('deb_detalle as detalle');
			$this->db->select('deb_tipo as tipo');
			$this->db->select('pag_serie as pago_serie');
			$this->db->select('pag_codigo as pago_id');
			$this->db->join('pago', 'pag_codigo = deb_pago');
			$this->db->from('debitacion');
			// $this->db->limit($limite);
			$this->db->order_by('deb_codigo', 'DESC');
			$res = $this->db->get();
			return $res->result();
		}
		// 
		function Insertar($data){
			return $this->db->insert('debitacion', $data);
		}

	}
?>