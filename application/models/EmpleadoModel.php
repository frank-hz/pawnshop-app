<?php  
	
	class EmpleadoModel extends CI_Model
	{
		
		// NEW CODE

		// 
		function Listar($tipo, $buscar, $limite, $sucursal){
			// $sql = "CALL proc_empleado_lista(?,?,?,?)";
			$sql = "
				SELECT
					e.emp_codigo as 'id', 
					e.emp_documento as 'documento',
					e.emp_tipo_documento as 'tipo_documento', 
					e.emp_nombres as 'nombres',
					e.emp_paterno as 'paterno',
					e.emp_materno as 'materno',
					u.ubi_distrito as 'ciudad',
					e.emp_direccion as 'direccion',
					e.emp_telefono as 'telefono',
					e.emp_email as 'email',
					s.suc_nombre as 'sucursal',
					e.emp_estado as 'estado',
					e.emp_creacion as creacion
				FROM empleado e 
				INNER JOIN ubigeo u ON u.ubi_codigo = e.emp_ubigeo 
				INNER JOIN sucursal s ON s.suc_codigo = e.emp_sucursal
			";
			$where_string = "
				WHERE e.emp_sucursal = ? 
			";
			$pars = Array($sucursal);
			switch ($tipo) {
				case 'doc':
					$where_string .= "
						AND e.emp_documento LIKE CONCAT(?, '%')
					";
					array_push($pars, $buscar);
					break;
				
				case 'nombres':
					$where_string .= "
						AND (
							e.emp_nombres LIKE CONCAT('%',?,'%') OR 
							CONCAT(
								e.emp_paterno,' ',
								e.emp_materno
							) LIKE CONCAT('%',?,'%')
						)
					";
					array_push($pars, $buscar, $buscar);
					break;
			}
			$sql .= $where_string; 
			// return [
			// 	'log' => $sql,
			// 	'pars' => $pars
			// ];
			$res = $this->db->query($sql, $pars);
			return $res->result();
		}
		// 
		function Insertar($data){
			return $this->db->insert('empleado', $data);
		}
		// 
		function Buscar_detalle($id)
		{
			$sql = "
				SELECT 
					e.emp_codigo as 'id',
					e.emp_documento as 'documento',
					e.emp_tipo_documento as 'tipo_documento',
					e.emp_nombres as 'nombres',
					e.emp_paterno as 'paterno',
					e.emp_materno as 'materno',
					e.emp_ubigeo as 'ubigeo',
					u.ubi_distrito as 'ciudad',
					u.ubi_departamento as 'departamento',
					e.emp_direccion as 'direccion',
					e.emp_telefono as 'telefono',
					e.emp_imagen as 'imagen',
					e.emp_email as 'email',
					e.emp_estado 'estado',
					DATE_FORMAT(e.emp_creacion, '%d/%m/%Y')	as 'creacion',
					us.usu_usuario as 'usuario',
					us.usu_codigo as 'id_usuario',
					s.suc_nombre as 'sucursal',
					e.emp_sucursal as 'id_sucursal',
					s.suc_codigo as sede_id,
					s.suc_nombre as sede_nombre,
					e.emp_imagen as imagen
				FROM empleado e 
				INNER JOIN ubigeo u ON u.ubi_codigo = e.emp_ubigeo
				INNER JOIN usuario us ON us.usu_empleado = e.emp_codigo
				INNER JOIN sucursal s ON s.suc_codigo = e.emp_sucursal
				WHERE e.emp_codigo = ?
			";
			$res = $this->db->query($sql, array($id));
			return $res->row();
		}
		// 
		function Modificar($id, $data){
			$this->db->where('emp_codigo', $id);
			$this->db->update('empleado', $data);
		}
		// 
		function Buscar_libre($data, $cond){
			$this->db->where($cond);
			$this->db->select($data);
			$res = $this->db->get('empleado');
			return $res->row();
		}

		function actualiza_imagen($id, $url){
			try {
				$sql = "UPDATE empleado SET emp_imagen = ? WHERE emp_codigo = ?";
				$upd = $this->db->query($sql, array($url, $id));
				return true;
			} catch (Exception $e) {
				return false;
			}
		}
		
		function eliminar($id){
			$sql = "CALL proc_cliente_eliminar(?)";
			$ins = $this->db->query($sql, array($id));
			return $ins->row();
		}

		function ins_verificarDocumento($doc, $tipo){
			$sql = "SELECT emp_codigo as 'id' FROM empleado WHERE emp_documento = ? AND emp_tipo_documento = ?";
			$ins = $this->db->query($sql, array($doc, $tipo));
			return $ins->result();
		}

		function upd_verificarDocumento($doc, $tipo, $id){
			$sql = "SELECT emp_codigo as 'id' FROM empleado WHERE emp_documento = ? AND emp_tipo_documento = ? AND emp_codigo != ?";
			$ins = $this->db->query($sql, array($doc, $tipo, $id));
			return $ins->result();
		}

		


		// encode 01-2023
		function get_data($id){
			$sql = "
				SELECT
					e.emp_codigo as 'id', 
					e.emp_documento as 'documento',
					e.emp_tipo_documento as 'tipo_documento', 
					e.emp_nombres as 'nombres',
					e.emp_paterno as 'paterno',
					e.emp_materno as 'materno',
					e.emp_ubigeo as ubigeo,
					ub.ubi_departamento as 'departamento',
					ub.ubi_distrito as distrito,
					ub.ubi_distrito as 'ciudad',
					e.emp_direccion as 'direccion',
					e.emp_telefono as 'telefono',
					e.emp_email as 'email',
					s.suc_codigo as id_sucursal,
					s.suc_nombre as 'sucursal',
					e.emp_estado as 'estado',
					e.emp_creacion as creacion,
					u.usu_usuario as usuario,
					u.usu_codigo as id_usuario,
					s.suc_codigo as sede_id,
					s.suc_nombre as sede_nombre,
					e.emp_imagen as imagen
				FROM empleado e
				INNER JOIN ubigeo ub ON ub.ubi_codigo = e.emp_ubigeo 
				INNER JOIN sucursal s ON s.suc_codigo = e.emp_sucursal 
				LEFT JOIN usuario u ON u.usu_empleado = e.emp_codigo
				WHERE
					e.emp_codigo = ?
				ORDER BY emp_nombres ASC;
			";
			$res = $this->db->query($sql, [$id]);
			return $res->row();
		}
		function get_data_all(){
			$sql = "
				SELECT
					e.emp_codigo as 'id', 
					e.emp_documento as 'documento',
					e.emp_tipo_documento as 'tipo_documento', 
					e.emp_nombres as 'nombres',
					e.emp_paterno as 'paterno',
					e.emp_materno as 'materno',
					u.ubi_distrito as 'ciudad',
					e.emp_direccion as 'direccion',
					e.emp_telefono as 'telefono',
					e.emp_email as 'email',
					s.suc_nombre as 'sucursal',
					e.emp_estado as 'estado',
					e.emp_creacion as creacion
				FROM empleado e
				INNER JOIN ubigeo u ON u.ubi_codigo = e.emp_ubigeo 
				INNER JOIN sucursal s ON s.suc_codigo = e.emp_sucursal
				ORDER BY emp_nombres ASC;
			";
			$res = $this->db->query($sql);
			return $res->result();
		}
		function get_data_all_by_sucursal($idsuc){
			$sql = "
				SELECT
					e.emp_codigo as 'id', 
					e.emp_documento as 'documento',
					e.emp_tipo_documento as 'tipo_documento', 
					e.emp_nombres as 'nombres',
					e.emp_paterno as 'paterno',
					e.emp_materno as 'materno',
					u.ubi_distrito as 'ciudad',
					e.emp_direccion as 'direccion',
					e.emp_telefono as 'telefono',
					e.emp_email as 'email',
					s.suc_nombre as 'sucursal',
					e.emp_estado as 'estado',
					e.emp_creacion as creacion
				FROM empleado e
				INNER JOIN ubigeo u ON u.ubi_codigo = e.emp_ubigeo 
				INNER JOIN sucursal s ON s.suc_codigo = e.emp_sucursal
				WHERE
					e.emp_sucursal = ?
				ORDER BY emp_nombres ASC;
			";
			$res = $this->db->query($sql, [$idsuc]);
			return $res->result();
		}



		// B A S I C
		function get($id){
			$sql = "
				SELECT
					e.emp_codigo as 'id', 
					e.emp_documento as 'documento',
					e.emp_tipo_documento as 'tipo_documento', 
					e.emp_nombres as 'nombres',
					e.emp_paterno as 'paterno',
					e.emp_materno as 'materno',
					ub.ubi_distrito as 'ciudad',
					e.emp_direccion as 'direccion',
					e.emp_telefono as 'telefono',
					e.emp_email as 'email',
					s.suc_codigo as id_sucursal,
					s.suc_nombre as 'sucursal',
					e.emp_estado as 'estado',
					e.emp_creacion as creacion,
					u.usu_usuario as usuario,
					u.usu_codigo as id_usuario
				FROM empleado e
				INNER JOIN ubigeo ub ON ub.ubi_codigo = e.emp_ubigeo 
				INNER JOIN sucursal s ON s.suc_codigo = e.emp_sucursal 
				LEFT JOIN usuario u ON u.usu_empleado = e.emp_codigo
				WHERE
					e.emp_codigo = ?
				ORDER BY e.emp_codigo ASC;
			";
			$res = $this->db->query($sql, [$id]);
			return $res->row();
		}
		function getByUser($id){
			$sql = "
				SELECT
					e.emp_codigo as 'id', 
					e.emp_documento as 'documento',
					e.emp_tipo_documento as 'tipo_documento', 
					e.emp_nombres as 'nombres',
					e.emp_paterno as 'paterno',
					e.emp_materno as 'materno',
					ub.ubi_distrito as 'ciudad',
					e.emp_direccion as 'direccion',
					e.emp_telefono as 'telefono',
					e.emp_email as 'email',
					s.suc_codigo as sucursal_id,
					s.suc_nombre as 'sucursal_nombre',
					e.emp_estado as 'estado',
					e.emp_creacion as creacion,
					u.usu_usuario as usuario,
					u.usu_codigo as id_usuario
				FROM empleado e
				INNER JOIN ubigeo ub ON ub.ubi_codigo = e.emp_ubigeo 
				INNER JOIN sucursal s ON s.suc_codigo = e.emp_sucursal 
				LEFT JOIN usuario u ON u.usu_empleado = e.emp_codigo
				WHERE
					e.emp_codigo = ?
				ORDER BY u.usu_codigo ASC;
			";
			$res = $this->db->query($sql, [$id]);
			return $res->row();
		}

	}
?>	