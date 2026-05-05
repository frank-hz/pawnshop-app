<?php  
	
	class SucursalModel extends CI_Model
	{

		// Lista de empleados asociados
		function Buscar_empleados($id){
			// $sql = "CALL proc_sucursal_empleado(?)";
			$sql = "
				SELECT
					e.emp_codigo as 'id', 
					e.emp_documento as 'documento',
					e.emp_tipo_documento as 'tipo_documento', 
					e.emp_nombres as 'nombres',
					e.emp_paterno as 'paterno',
					e.emp_materno as 'materno',
					e.emp_direccion as 'direccion',
					e.emp_telefono as 'telefono',
					e.emp_estado as 'estado'
				FROM empleado e
				INNER JOIN sucursal s ON s.suc_codigo = e.emp_sucursal 
				WHERE s.suc_codigo = ? 
				ORDER BY emp_nombres ASC
			";
			$res = $this->db->query($sql, array($id));
			return $res->result();
		}
		// 
		function Buscar_libre($data, $cond){
			$this->db->where($cond);
			$this->db->select($data);
			$res = $this->db->get('sucursal');
			return $res->row();
		}


		function validar_sucursal($dir, $ubi, $tipo, $id = ''){
			if ($tipo === 'upd') {
				$sql = "
					SELECT 
						suc_codigo as 'id' 
					FROM sucursal 
					WHERE 
						suc_direccion = ? AND 
						suc_ubigeo = ? AND 
						suc_codigo != ?
				";
				$res = $this->db->query($sql, array($dir, $ubi, $id));
			}else{
				$sql = "
					SELECT 
						suc_codigo as 'id' 
					FROM sucursal 
					WHERE 
						suc_direccion = ? AND 
						suc_ubigeo = ?
				";
				$res = $this->db->query($sql, array($dir, $ubi));
			}
			return $res->result();
		}

		function validar_sigla($sigla, $tipo, $id = ''){
			if ($tipo === 'upd') {
				$sql = "
					SELECT suc_codigo as 'id',
					FROM sucursal 
					WHERE suc_sigla = ? and suc_codigo != $id 
				";
				$res = $this->db->query($sql, array($sigla, $id));
			}else{
				$sql = "
					SELECT suc_codigo as 'id',
					FROM sucursal 
					WHERE 
						suc_sigla = ?
				";
				$res = $this->db->query($sql, array($sigla));
			}
			return $res->result();
		}		

		

		// NEW CODE
		// Listar todas las sucursales disponibles
		function Lista(){
			$sql = "CALL proc_sucursal_lista(?,?)";
			$res = $this->db->query($sql, array('',0));
			return $res->result();
		}
		// Lista corta de todas las sucursales disponibles
		function Lista_breve(){
			// $this->db->where($w_data);
			$this->db->select('
				suc_codigo as id, 
				suc_nombre as nombre, 
				ubi_distrito as ciudad, 
				ubi_departamento as departamento');
			$this->db->join('ubigeo', 'ubigeo.ubi_codigo = sucursal.suc_ubigeo');
			$this->db->from('sucursal');
			$res = $this->db->get();
			return $res->result();	
		}
		
		// Obtener la informacion corta de una sucursal 
		function Buscar_detalle($id){
			$sql = "call proc_sucursal_detalle(?)";
			$res = $this->db->query($sql,array($id));
			mysqli_next_result( $this->db->conn_id );
			return $res->row();		
		}
		// Obtener la informacion corta de una sucursal 
		function Buscar_detalle_breve($w_data){
			$this->db->where($w_data);
			$this->db->select('
				suc_codigo as id, 
				suc_nombre as descripcion, 
				ubi_distrito as ciudad, 
				ubi_departamento as departamento');
			$this->db->join('ubigeo', 'ubigeo.ubi_codigo = sucursal.suc_ubigeo');
			$this->db->from('sucursal');
			$res = $this->db->get();
			return $res->row();		
		}
		// 
		function insertar(array $data){
			return $this->db->insert('sucursal', $data);
		}

		function modificar($id, $data){
			try {
				$this->db->where('suc_codigo', $id);
				$this->db->update('sucursal', $data);
				return true;
			} catch (PDOException $e) {
				return false;				
			}
		}

		function verificar_serie($serie){
			$sql = "SELECT suc_codigo as 'id' FROM sucursal WHERE suc_serie = ?";
			$res = $this->db->query($sql, array($serie));
			return $res->result();
		}

		function sucursal_ciudad($cod){
			$sql = "
				SELECT 
					suc_orden as 'orden',
					suc_sigla as 'sigla',
					suc_serie as 'seria'
				FROM sucursal 
				WHERE 
					suc_ubigeo = ?
				ORDER BY suc_orden DESC LIMIT 1";
			$res = $this->db->query($sql, array($cod));
			return $res->row();
		}

		function eliminar($id){
			mysqli_next_result( $this->db->conn_id );
			$this->db->delete('sucursal', array('suc_codigo' => $id));
		}

		// encode 01-2023
		function get_data_all(){
			$sql = "
				SELECT
					s.suc_codigo as id, 
					s.suc_nombre as nombre,
					CONCAT(u.ubi_distrito,', ',u.ubi_departamento) as ciudad, 
					s.suc_direccion as direccion,
					s.suc_telefono as telefono,
					s.suc_estado as estado,
					s.suc_sigla as sigla,
					s.suc_serie as serie,
					s.suc_pago_serie as pago_serie,
					u.ubi_codigo as ubigeo,
					u.ubi_distrito as distrito,
					u.ubi_departamento as departamento,
					u.ubi_provincia as provincia,
					DATE_FORMAT(s.suc_creacion, '%d/%m/%Y') as creacion,
					s.suc_cuenta_tipo as cuenta_tipo,
					s.suc_banco as banco,
					s.suc_cuenta_numero as cuenta_numero
				FROM sucursal s 
				INNER JOIN ubigeo u ON u.ubi_codigo = s.suc_ubigeo
				ORDER BY nombre ASC	
			";
			$res = $this->db->query($sql);
			return $res->result();
		}
		function get_data($id){
			$sql = "
				SELECT
					s.suc_codigo as id, 
					s.suc_nombre as nombre,
					CONCAT(u.ubi_distrito,', ',u.ubi_departamento) as ciudad, 
					s.suc_direccion as direccion,
					s.suc_telefono as telefono,
					s.suc_estado as estado,
					s.suc_sigla as sigla,
					s.suc_serie as serie,
					s.suc_pago_serie as serie_pago,
					u.ubi_codigo as ubigeo,
					u.ubi_distrito as distrito,
					u.ubi_departamento as departamento,
					u.ubi_provincia as provincia,
					DATE_FORMAT(s.suc_creacion, '%d/%m/%Y') as creacion,
					s.suc_cuenta_tipo as cuenta_tipo,
					s.suc_banco as banco,
					s.suc_cuenta_numero as cuenta_numero
				FROM sucursal s 
				INNER JOIN ubigeo u ON u.ubi_codigo = s.suc_ubigeo 
				WHERE suc_codigo = ?
				ORDER BY nombre ASC	
			";
			$res = $this->db->query($sql, [$id]);
			return $res->row();
		}

		function update_data($id, $data){
			$this->db->where('suc_codigo', $id);
			$this->db->update('sucursal', $data);
		}
		


		// B A S I C
		function get($id){
			$sql = "
				SELECT
					s.suc_codigo as id, 
					s.suc_nombre as nombre,
					CONCAT(u.ubi_distrito,', ',u.ubi_departamento) as ciudad, 
					s.suc_direccion as direccion,
					s.suc_telefono as telefono,
					s.suc_estado as estado,
					s.suc_sigla as sigla,
					s.suc_serie as serie,
					s.suc_pago_serie as serie_pago,
					u.ubi_codigo as ubigeo,
					u.ubi_distrito as distrito,
					u.ubi_departamento as departamento,
					u.ubi_provincia as provincia,
					DATE_FORMAT(s.suc_creacion, '%d/%m/%Y') as creacion,
					s.suc_cuenta_tipo as cuenta_tipo,
					s.suc_banco as banco,
					s.suc_cuenta_numero as cuenta_numero
				FROM sucursal s 
				INNER JOIN ubigeo u ON u.ubi_codigo = s.suc_ubigeo 
				WHERE suc_codigo = ?
				ORDER BY nombre ASC	
			";
			$res = $this->db->query($sql, array($id));
			return $res->row();
		}

		function getAll(){
			$sql = "
				SELECT
					s.suc_codigo as id, 
					s.suc_nombre as nombre,
					CONCAT(u.ubi_distrito,', ',u.ubi_departamento) as ciudad, 
					s.suc_direccion as direccion,
					s.suc_telefono as telefono,
					s.suc_estado as estado,
					s.suc_sigla as sigla,
					s.suc_serie as serie,
					s.suc_pago_serie as pago_serie,
					u.ubi_codigo as ubigeo,
					u.ubi_distrito as distrito,
					u.ubi_departamento as departamento,
					u.ubi_provincia as provincia,
					DATE_FORMAT(s.suc_creacion, '%d/%m/%Y') as creacion,
					s.suc_cuenta_tipo as cuenta_tipo,
					s.suc_banco as banco,
					s.suc_cuenta_numero as cuenta_numero
				FROM sucursal s 
				INNER JOIN ubigeo u ON u.ubi_codigo = s.suc_ubigeo
				ORDER BY nombre ASC	
			";
			$res = $this->db->query($sql);
			return $res->result();
		}

		function getAllByUser($id){
			$sql = "
				SELECT
					s.suc_codigo as id, 
					s.suc_nombre as nombre,
					CONCAT(u.ubi_distrito,', ',u.ubi_departamento) as ciudad, 
					s.suc_direccion as direccion,
					s.suc_telefono as telefono,
					s.suc_estado as estado,
					s.suc_sigla as sigla,
					s.suc_serie as serie,
					s.suc_pago_serie as serie_pago,
					u.ubi_codigo as ubigeo,
					u.ubi_distrito as distrito,
					u.ubi_departamento as departamento,
					u.ubi_provincia as provincia,
					s.suc_cuenta_tipo as cuenta_tipo,
					s.suc_banco as banco,
					s.suc_cuenta_numero as cuenta_numero,
					p.sucursal_init as active
				FROM empleado s 
				INNER JOIN ubigeo u ON u.ubi_codigo = s.suc_ubigeo 
				LEFT JOIN preferencias p ON s.suc_codigo = p.sucursal_init
				WHERE suc_codigo = ?
				ORDER BY nombre ASC	
			";
			$res = $this->db->query($sql, array($id));
			return $res->result();
		}

	}
?>