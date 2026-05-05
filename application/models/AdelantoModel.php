<?php  

	class AdelantoModel extends CI_Model
	{
		
		// NEW CODE
		function Insertar_entrada($data){
			return $this->db->insert('adelanto_entrada', $data);
		}

		function Buscar_entrada($id){
			$this->db->where('ent_credito', $id);
			$this->db->select('ent_codigo as id');
			$this->db->select('pag_serie as pago');
			$this->db->select('pag_codigo as id_pago');
			$this->db->select('ent_monto as monto');
			$this->db->select('DATE_FORMAT(ent_fecha, "%d/%m/%Y") as fecha');
			$this->db->join('pago', 'pag_codigo = ent_pago');
			$res = $this->db->get('adelanto_entrada');
			return $res->result();
		} 

		function Buscar_salida($id){
			$this->db->where('sal_credito', $id);
			$this->db->select('sal_codigo as id');
			$this->db->select('sal_tipo as tipo');
			$this->db->select('sal_modo as modo');
			$this->db->select('sal_detalle as detalle');
			$this->db->select('sal_monto as monto');
			$this->db->select('DATE_FORMAT(sal_fecha, "%d/%m/%Y") as fecha');
			$res = $this->db->get('adelanto_salida');
			return $res->result();
		}

		function Insertar_salida(array $data){
			return $this->db->insert('adelanto_salida', $data);
		}

		function Buscar_cuota_aplicable($id){
			$fecha = date("Y-m-d");
			$this->db->where("cuo_credito", $id);
			$this->db->where("cuo_vencimiento >= '$fecha'");
			$this->db->where("(cuo_estado = 'E' OR cuo_estado = 'V')");
			$this->db->select('cuo_codigo as id');
			$this->db->select('cuo_orden as orden');
			$this->db->select('cuo_monto as monto');
			$this->db->order_by('cuo_orden', 'ASC');
			$res = $this->db->get('cuota');
			return $res->result();
		}

		// function Buscar_libre($data, $cond){
		// 	$this->db->where($cond);
		// 	$this->db->select($data);
		// 	$res = $this->db->get('cuota');
		// 	return $res->row();
		// }

		// function Modificar_evaluacion_mora($credito){
		// 	$fecha = date("Y-m-d");
		// 	$this->db->where('cuo_credito', $credito);
		// 	$this->db->where('cuo_vencimiento <= "'.$fecha.'"');
		// 	$this->db->update('cuota', array('cuo_evalua_mora' => 'I'));
		// }

		// function Modificar($id, $data){
		// 	$this->db->where('cuo_codigo', $id);
		// 	$this->db->update('cuota', $data);
		// }

		// function Lista_regularizacion($id, $fecha){
		// 	$sql = "CALL proc_regularizacion_cuotas(?,?)";
		// 	$ins = $this->db->query($sql, array($id, $fecha));
		// 	mysqli_next_result( $this->db->conn_id );
		// 	return $ins->result();
		// }

		// function Lista_recuperacion($id, $fecha){
		// 	$sql = "CALL proc_recuperacion_cuota(?,?)";
		// 	$ins = $this->db->query($sql, array($id, $fecha));
		// 	mysqli_next_result( $this->db->conn_id );
		// 	return $ins->result();
		// }

		// function insertar(array $data){
		// 	return $this->db->insert('cuota', $data);
		// }

		

		// function Modificar_libre($data, $cond){
		// 	$this->db->where($cond);
		// 	$this->db->update('cuota', $data);
		// }

	}
?>