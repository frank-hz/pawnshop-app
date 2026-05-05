<?php  
	
	class NotaClienteModel extends CI_Model
	{	

		function Buscar($id){
			// $sql = "CALL proc_nota_cliente_lista(?)";
			$sql = "
				SELECT
					n.ncli_codigo as 'id',
					concat(e.emp_nombres, ' ',e.emp_paterno) as 'empleado',
					n.ncli_descripcion as 'descripcion',
					DATE_FORMAT(n.ncli_fecha, '%d/%m/%Y') as 'fecha'
				FROM notas_cliente n 
				INNER JOIN empleado e ON e.emp_codigo = n.ncli_empleado  
				WHERE n.ncli_cliente = ?
				ORDER BY n.ncli_codigo DESC
			";
			$res = $this->db->query($sql, array($id));
			return $res->result();
		}

		function insertar(array $data){
			return $this->db->insert('notas_cliente', $data);
		}

		function eliminar($id){
			return $this->db->delete('notas_cliente', array(
				'ncli_codigo' => $id
			)); 
		}

		// encode 03-2023
		function get_data($id){
			$sql = "
				SELECT
					n.ncli_codigo as 'id',
					concat(e.emp_nombres, ' ',e.emp_paterno) as 'empleado',
					n.ncli_descripcion as 'descripcion',
					DATE_FORMAT(n.ncli_fecha, '%d/%m/%Y') as 'fecha'
				FROM notas_cliente n 
				INNER JOIN empleado e ON e.emp_codigo = n.ncli_empleado  
				WHERE n.ncli_codigo = ?
				ORDER BY n.ncli_codigo DESC
			";
			$res = $this->db->query($sql, array($id));
			return $res->row();
		}
		function set_update($data, $id){
			$this->db->where('ncli_codigo', $id);
			$this->db->update('notas_cliente', $data);
		}

		// recode 05-2023

		function getById($id){
			$sql = "
				SELECT
					n.ncli_codigo as 'id',
					n.ncli_empleado as empleado_id,
					concat(e.emp_nombres, ' ',e.emp_paterno,' ',e.emp_materno) as 'empleado_nombre_full',
					n.ncli_descripcion as 'descripcion',
					DATE_FORMAT(n.ncli_fecha, '%d/%m/%Y') as 'fecha',
					n.ncli_fecha as 'fecha_raw'
				FROM notas_cliente n 
				INNER JOIN empleado e ON e.emp_codigo = n.ncli_empleado  
				WHERE n.ncli_codigo = ?
				ORDER BY n.ncli_codigo DESC
			";
			$res = $this->db->query($sql, [$id]);
			return $res->row();
		}
		
		


		// B A S I C
		function get($id){
			$sql = "
				SELECT
					n.ncli_codigo as 'id',
					n.ncli_empleado as empleado_id,
					concat(e.emp_nombres, ' ',e.emp_paterno,' ',e.emp_materno) as 'empleado_nombre_full',
					n.ncli_descripcion as 'descripcion',
					DATE_FORMAT(n.ncli_fecha, '%d/%m/%Y') as 'fecha',
					n.ncli_fecha as 'fecha_raw'
				FROM notas_cliente n 
				INNER JOIN empleado e ON e.emp_codigo = n.ncli_empleado  
				WHERE n.ncli_codigo = ?
			";
			$res = $this->db->query($sql, [$id]);
			return $res->row();
		}
		function insert($data){
			return $this->db->insert('notas_cliente', $data);
		}
		function update($id, $data){
			$this->db->where('ncli_codigo', $id);
			return $this->db->update('notas_cliente', $data);
		}
		function delete($id){
			$this->db->where('ncli_codigo', $id);
			return $this->db->delete('notas_cliente');
		}

		

		// C U S T O M

		function getByCliente($idcliente, $order = "ASC"){
			$order_type = in_array($order, ['ASC','DESC']) ? $order : 'ASC';
			$order_query = "ORDER BY ncli_codigo ". $order_type;
			$sql = "
				SELECT
					n.ncli_codigo as 'id',
					n.ncli_empleado as empleado_id,
					concat(e.emp_nombres, ' ',e.emp_paterno,' ',e.emp_materno) as 'empleado_nombre_full',
					n.ncli_descripcion as 'descripcion',
					DATE_FORMAT(n.ncli_fecha, '%d/%m/%Y') as 'fecha',
					n.ncli_fecha as 'fecha_raw',
					n.row_fixed as fixed,
					n.mensaje
				FROM notas_cliente n 
				INNER JOIN empleado e ON e.emp_codigo = n.ncli_empleado  
				WHERE n.ncli_cliente = ? 
				$order_query
			";
			$res = $this->db->query($sql, [$idcliente]);
			return $res->result();
		}




	}
?>