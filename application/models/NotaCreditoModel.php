<?php  
	
	class NotaCreditoModel extends CI_Model
	{	

		function Buscar($id){
			$sql = "CALL proc_nota_credito_lista(?)";
			$res = $this->db->query($sql, array($id));
			return $res->result();
		}
		function insertar(array $data){
			return $this->db->insert('notas_credito', $data);
		}
		function eliminar($id){
			return $this->db->delete('notas_credito', array('ncred_codigo' => $id)); 
		}
		// NEW CODE
		function Listar($id){
			$this->db->where('ncred_credito', $id);
			$this->db->select('ncred_codigo as id');
			$this->db->select('ncred_credito as credito_id');
			$this->db->select('DATE_FORMAT(ncred_fecha, "%d/%m/%Y") as fecha');
			$this->db->select('ncred_descripcion as descripcion');
			$this->db->select('ncred_anclado as anclado');
			$this->db->select('emp_codigo as empleado_id');
			$this->db->select('concat(emp_nombres," ",emp_paterno," ",emp_materno) as empleado_nombre_full');
			$this->db->order_by('ncred_fecha', 'DESC');
			$this->db->join('empleado', 'emp_codigo = ncred_empleado');
			$res = $this->db->get('notas_credito');
			return $res->result();
		}
		function Modificar($id, $data){
			$this->db->where('ncred_codigo', $id);
			$this->db->update('notas_credito', $data);
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

		function getAll($idcredito){
			$sql = "
				SELECT 
					ncred_codigo as id,
					ncred_descripcion as descripcion,
					DATE_FORMAT(ncred_fecha, '%d/%m/%Y') as fecha,
					ncred_anclado as anclado
				FROM notas_credito 
				WHERE 
					ncred_credito = ? 
				ORDER BY ncred_codigo DESC  
			";
			$res = $this->db->query($sql, [$idcredito]);
			return $res->result();
		}

		function delete($id){
			return $this->db->delete('notas_credito', array('ncred_codigo' => $id)); 
		}




		// encode: 05-2023
		function getAllByCredito($idcredito){
			$sql = "
				SELECT 
					ncred_codigo as id,
					ncred_descripcion as descripcion,
					DATE_FORMAT(ncred_fecha, '%d/%m/%Y') as fecha,
					ncred_anclado as anclado,
					ncred_empleado as empleado_id,
					CONCAT(emp_nombres,' ',emp_paterno,' ',emp_materno) as empleado_nombre_full
				FROM notas_credito 
				INNER JOIN empleado ON emp_codigo = ncred_empleado
				WHERE 
					ncred_credito = ? 
				ORDER BY ncred_codigo DESC  
			";
			$res = $this->db->query($sql, [$idcredito]);
			return $res->result();
		}
		function getData($id){
			$this->db->where('ncred_codigo', $id);
			$this->db->select('ncred_codigo as id');
			$this->db->select('DATE_FORMAT(ncred_fecha, "%d/%m/%Y") as fecha');
			$this->db->select('ncred_descripcion as descripcion');
			$this->db->select('ncred_anclado as anclado');
			$res = $this->db->get('notas_credito');
			return $res->row();
		}
		function insert(array $data){
			return $this->db->insert('notas_credito', $data);
		}
		function update($id, $data){
			$this->db->where('ncred_codigo', $id);
			$this->db->update('notas_credito', $data);
		}

		function getByCredito($idcliente, $setting = ""){
			$order_sett = (!empty($setting)) ? $setting : "ORDER BY ncli_codigo DESC";
			$sql = "
				SELECT 
					ncred_codigo as id,
					ncred_credito as credito_id,
					ncred_descripcion as descripcion,
					DATE_FORMAT(ncred_fecha, '%d/%m/%Y') as fecha,
					ncred_anclado as anclado,
					ncred_empleado as empleado_id,
					CONCAT(emp_nombres,' ',emp_paterno,' ',emp_materno) as empleado_nombre_full
				FROM notas_credito 
				INNER JOIN empleado ON emp_codigo = ncred_empleado
				WHERE 
					ncred_credito = ?  
					$order_sett
			";
			$res = $this->db->query($sql, [$idcliente]);
			return $res->result();
		}

	}
?>