<?php 

	class PPagoModel extends CI_Model
	{
		function Insertar(array $data){
			return $this->db->insert('pago', $data);
		}
		
		function Insertar_detalle(array $data){
			return $this->db->insert('pago_detalle', $data);
		}

		function Modificar($id, $data){
			$this->db->where('pag_codigo', $id);
			$this->db->update('pago', $data);
		}



		// 
		function Buscar_orden_actual($nox, $suc){
			$this->db->where(
				array(
					'pag_nomen' => $nox,
					'cred_sucursal' => $suc
				)
			);
			$this->db->select('MAX(pag_orden) as orden');
			$this->db->join('credito', 'cred_codigo = pag_credito');
			$res = $this->db->get('pago');
			return $res->row();
		}
	}
	