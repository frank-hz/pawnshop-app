<?php  
	
	class SolicitudModel extends CI_Model
	{

		function Listar($limite = 25, $suc){
			$this->db->select('sol_codigo as id');
			$this->db->select('sol_tipo as tipo');
			$this->db->select('sol_monto as monto');
			$this->db->select('sol_fecha as fecha');
			$this->db->select('sol_cliente as cliente');
			$this->db->select('sol_telefono as telefono');
			$this->db->select('sol_descripcion as descripcion');
			$this->db->from('solicitud');
			$this->db->where('sol_sucursal', $suc);
			$this->db->limit($limite);
			$this->db->order_by('sol_fecha', 'ASC');
			$this->db->order_by('sol_estado', 'ASC');
			$res = $this->db->get();
			return $res->result();	
		}

		function Detalle($id){
			$this->db->select('sol_codigo as id');
			$this->db->select('sol_tipo as tipo');
			$this->db->select('sol_monto as monto');
			$this->db->select('DATE_FORMAT(sol_fecha, "%d/%m/%Y")  as fecha');
			$this->db->select('sol_cliente as cliente');
			$this->db->select('sol_telefono as telefono');
			$this->db->select('sol_descripcion as prenda');
			$this->db->select('suc_descripcion as sucursal');
			$this->db->select('suc_codigo as id_sucursal');
			$this->db->where('sol_codigo', $id);
			$this->db->from('solicitud');
			$this->db->join('sucursal', 'suc_codigo = sol_sucursal');
			$res = $this->db->get();
			return $res->row();	
		}

	}
?>