<?php  
	
	class AlertaClienteModel extends CI_Model
	{	

		function Listar($id){
			$this->db->where('acli_cliente', $id);
			$this->db->where('acli_estado', 'P');
			$this->db->select('acli_codigo as id');
			$this->db->select('DATE_FORMAT(acli_fecha, "%d/%m/%Y") as fecha');
			$this->db->select('acli_contenido as contenido');
			$this->db->select('acli_estado as estado');
			$this->db->order_by('acli_fecha', 'DESC');
			$res = $this->db->get('alerta_cliente');
			return $res->result();
		}

		function Modificar($id, $data){
			$this->db->where('acli_codigo', $id);
			$this->db->update('alerta_cliente', $data);
		}

		function Detalle($id){
			$this->db->where('ncred_codigo', $id);
			$this->db->select('ncred_codigo as id');
			$this->db->select('DATE_FORMAT(ncred_fecha, "%d/%m/%Y") as fecha');
			$this->db->select('ncred_descripcion as descripcion');
			$this->db->select('ncred_anclado as anclado');
			$res = $this->db->get('notas_credito');
			return $res->row();
		}

		function Insertar($data){
			return $this->db->insert('alerta_cliente', $data);
		}

	}
?>