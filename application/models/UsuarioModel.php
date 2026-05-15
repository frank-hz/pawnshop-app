<?php  
	
	class UsuarioModel extends CI_Model
	{



		// REWORK 2026
		function insert(Array $data){
            $this->db->insert('usuario', $data);
            return ($this->db->affected_rows() > 0);
        }
        function update(int $id, Array $data){
            $this->db->where('usu_codigo', $id);
            return $this->db->update('usuario', $data);
            // return ($this->db->affected_rows());
        }
        function delete(int $id){
            $this->db->where('usu_codigo', $id);
            $this->db->delete('usuario');
            return ($this->db->affected_rows() > 0);
        }
		function getById($id){			
			$this->db->where("u.usu_codigo", $id);
            $this->db->select("u.usu_codigo as id");
            $this->db->select("u.usu_usuario as usuario");
            $this->db->select("u.usu_clave as clave");
            $this->db->select("u.usu_usuario as username");
            $this->db->select("u.usu_clave as password");
            $this->db->select("u.usu_dev_work as dev");
            $this->db->select("DATE_FORMAT(u.usu_creacion, '%d/%m/%Y') as creacion");
            $this->db->select("DATE_FORMAT(u.create_at, '%d/%m/%Y %H:%i:%s') as create_at");

			$this->db->select("e.emp_codigo as empleado_id");
			$this->db->select("e.emp_nombres as nombres");
			$this->db->select("e.emp_paterno as paterno");
			$this->db->select("e.emp_materno as materno");
			$this->db->select("e.emp_imagen as imagen");
            $this->db->select("
                CONCAT(
                    e.emp_nombres,' ', 
                    e.emp_paterno,' ',
                    e.emp_materno
                ) as nombre_apellido
            ");
			$this->db->select("
                CONCAT(
                    e.emp_nombres,' ', 
                    e.emp_paterno,' ',
                    e.emp_materno
                ) as nombre_full
            ");
            $this->db->select("r.rol_codigo as rol_id");
            $this->db->select("r.rol_titulo as rol");
            $this->db->select("e.emp_sucursal as sucursal_id");
            $this->db->select("s.suc_nombre as sucursal_nombre");
            $this->db->select("s.suc_serie as sucursal_serie");

            $this->db->join("empleado e", "e.emp_codigo = u.usu_empleado");
            $this->db->join("sucursal s", "e.emp_sucursal = s.suc_codigo");
            $this->db->join("roles r", "r.rol_codigo = u.usu_rol");             
            $res = $this->db->get("usuario u");
            return $res->row();
        }





		function Buscar_detalle($id){
			// $sql = "CALL proc_usuario_detalle(?)";
			$sql = "
				SELECT
					u.usu_codigo as 'id',  
					u.usu_empleado as 'id_empleado',
					CONCAT(e.emp_nombres,' ',e.emp_paterno,' ',e.emp_materno) as 'empleado',
					u.usu_usuario as 'usuario',
					r.rol_titulo as 'rol',
					e.emp_imagen as 'imagen',
					CONCAT(e.emp_codigo, '__',e.emp_documento) as 'nombre_imagen',
					DATE_FORMAT(e.emp_creacion, '%d/%m/%Y') as 'creacion',
					e.emp_estado as 'estado'
				FROM usuario u
				INNER JOIN empleado e ON u.usu_empleado = e.emp_codigo 
				INNER JOIN roles r ON r.rol_codigo = u.usu_rol 
				WHERE usu_codigo = ? 
				ORDER BY rol ASC LIMIT 10
			";
			$res = $this->db->query($sql, array($id));
			return $res->row();
		}
		function Buscar_modulos($id){
			// $sql = "CALL proc_usuario_modulo(?)";
			$sql = "
				SELECT 
					IF( mod_codigo NOT IN 
						(SELECT 
							mo.mod_codigo 
							FROM menu me 
							INNER JOIN modulo mo ON mo.mod_codigo = me.men_modulo 
							WHERE me.men_usuario = ?), 
						'NO','SI') AS 'estado',
					m.mod_codigo as 'id', 
					m.mod_titulo as 'nombre', 
					m.mod_descripcion as 'descripcion', 
					m.mod_icono as 'icono'
				FROM modulo m 
				ORDER BY m.mod_titulo ASC
			";
			$res = $this->db->query($sql, array($id));
			return $res->result();
		}
		function Buscar_permisos($id){
			// $sql = "CALL proc_usuario_permiso(?)";
			$sql = "
				SELECT 
					p.perm_codigo as 'id',
					p.perm_titulo as 'titulo',
					p.perm_nombre as 'nombre',
					p.perm_descripcion as 'descripcion',
					IF(
						(SELECT COUNT(*) FROM usuario_permiso 
							WHERE up_permiso = p.perm_nombre AND up_usuario = ?) > 0,
						'SI', 'NO') AS 'estado' 
					FROM permiso p 
					ORDER BY p.perm_titulo ASC
			";
			$res = $this->db->query($sql, array($id));
			return $res->result();
		}
		function Insertar($data){
			return $this->db->insert('usuario', $data);
		}
		function Modificar($id, $data){
			$this->db->where('usu_codigo', $id);
			return $this->db->update('usuario', $data);
		}
		function Buscar_libre($data, $cond){
			$this->db->where($cond);
			$this->db->select($data);
			$res = $this->db->get('usuario');
			return $res->row();
		}
		function consulta($tipo = 'lista', $p1 = '', $p2 = ''){
			switch ($tipo) {
				case 'autenticar':
					$sql = "
						SELECT 
							u.usu_codigo as 'id',
							e.emp_codigo as 'empleado_id',
							e.emp_codigo as 'empleado',
							e.emp_estado as 'empleado_estado',
							concat(e.emp_nombres,' ',e.emp_paterno) as 'nombre_apellido',
							e.emp_imagen as 'imagen',
							r.rol_titulo as 'rol',
							e.emp_sucursal as 'sucursal',
							s.suc_nombre as 'sucursal_nombre',
							e.emp_estado as 'usuario_estado'
						FROM usuario u
						INNER JOIN roles r ON r.rol_codigo = u.usu_rol
						INNER JOIN empleado e ON e.emp_codigo = u.usu_empleado 
						INNER JOIN sucursal s ON s.suc_codigo = e.emp_sucursal 
						WHERE 
							u.usu_usuario = ? AND 
							u.usu_clave = ?
					";
					$cons = $this->db->query($sql, array($p1, $p2));
					// mysqli_next_result( $this->db->conn_id );
					return $cons->row();
					break;
				
				case 'lista':
					// $sql = "CALL proc_usuario_lista()";
					$sql = "
						SELECT
							u.usu_codigo as 'id',  
							e.emp_codigo as 'empleado_id',
							CONCAT(e.emp_nombres,' ',e.emp_paterno,' ',e.emp_materno) as 'empleado',
							u.usu_usuario as 'usuario',
							r.rol_titulo as 'rol',
							e.emp_estado as 'estado'
						FROM usuario u
						INNER JOIN empleado e ON u.usu_empleado = e.emp_codigo 
						INNER JOIN roles r ON r.rol_codigo = u.usu_rol
						ORDER BY rol ASC LIMIT 10
					";
					$res = $this->db->query($sql);
					return $res->result();
					break;

				case 'detalle':
					// $sql = "CALL proc_usuario_detalle(?)";
					$sql = "
						SELECT
							u.usu_codigo as 'id',  
							u.usu_empleado as 'id_empleado',
							e.emp_codigo as 'empleado_id',
							CONCAT(e.emp_nombres,' ',e.emp_paterno,' ',e.emp_materno) as 'empleado',
							u.usu_usuario as 'usuario',
							r.rol_titulo as 'rol',
							e.emp_imagen as 'imagen',
							CONCAT(e.emp_codigo, '__',e.emp_documento) as 'nombre_imagen',
							DATE_FORMAT(e.emp_creacion, '%d/%m/%Y') as 'creacion',
							e.emp_estado as 'estado'
						FROM usuario u
						INNER JOIN empleado e ON u.usu_empleado = e.emp_codigo 
						INNER JOIN roles r ON r.rol_codigo = u.usu_rol 
						WHERE usu_codigo = ? 
						ORDER BY rol ASC LIMIT 10
					";
					$res = $this->db->query($sql, array($p1));
					return $res->row();
					break;

				case 'modulos':
					// $sql = "CALL proc_usuario_modulo(?)";
					$sql = "
						SELECT 
							IF( mod_codigo NOT IN 
								(SELECT 
									mo.mod_codigo 
									FROM menu me 
									INNER JOIN modulo mo ON mo.mod_codigo = me.men_modulo 
									WHERE me.men_usuario = ?), 
								'NO','SI') AS 'estado',
							m.mod_codigo as 'id', 
							m.mod_titulo as 'nombre', 
							m.mod_descripcion as 'descripcion', 
							m.mod_icono as 'icono'
						FROM modulo m 
						ORDER BY m.mod_titulo ASC
					";
					$res = $this->db->query($sql, array($p1));
					return $res->result();
					break;

				case 'permisos':
					// $sql = "CALL proc_usuario_permiso(?)";
					$sql = "
						SELECT 
						p.perm_codigo as 'id',
						p.perm_titulo as 'titulo',
						p.perm_nombre as 'nombre',
						p.perm_descripcion as 'descripcion',
						IF(
							(SELECT COUNT(*) FROM usuario_permiso 
								WHERE up_permiso = p.perm_nombre AND up_usuario = ?) > 0,
							'SI','NO') AS 'estado' 
						FROM permiso p 
						ORDER BY p.perm_titulo ASC
					";
					$res = $this->db->query($sql, array($p1));
					return $res->result();
					break;

				case 'verificar_clave':
					$sql = "SELECT usu_codigo as 'id' FROM usuario WHERE usu_codigo = ? AND usu_clave = ?";
					$res = $this->db->query($sql, array($p1, $p2));
					return $res->result();
					break;

				case 'verificar_usuario':
					$sql = "SELECT usu_codigo as 'id' FROM usuario WHERE usu_codigo != ? AND usu_usuario = ?";
					$res = $this->db->query($sql, array($p1, $p2));
					return $res->result();
					break;
			}
		}
		function actualizarPermiso($tipo, $valorA, $valorB){
			try {
				if ($tipo === 'agregar') {

					// $sql = "CALL proc_usuario_permiso_agregar(?,?,?)";
					$sql = "
						INSERT INTO usuario_permiso(up_usuario, up_permiso, up_fecha, up_estado) 
						VALUES(?, ?, ?, 'A')
					";
					$this->db->query($sql, array($valorA, $valorB, date("Y-m-d")));
					return true;

				}elseif($tipo === 'remover'){
					$sql = "DELETE FROM usuario_permiso WHERE up_usuario = ? AND up_permiso = ?";
					$this->db->query($sql, array($valorA, $valorB));
					return true;
				}

			} catch (Exception $e) {
				return false;
			}
		}
		function actualizarModulo($tipo, $valorA, $valorB){
			try {
				if ($tipo === 'agregar') {
					// $sql = "CALL proc_usuario_modulo_agregar(?,?)";
					$sql = "
						INSERT INTO menu(men_usuario, men_modulo, men_estado) 
						VALUES(?, ?, 'A')
					";
					$this->db->query($sql, array($valorA, $valorB));
					return true;

				}elseif($tipo === 'remover'){
					$sql = "DELETE FROM menu WHERE men_usuario = ? AND men_modulo = ?";
					$this->db->query($sql, array($valorA, $valorB));
					return true;
				}

			} catch (Exception $e) {
				return false;
			}
		}
		function Reiniciar($id){
			$sql = "CALL proc_usuario_reiniciar(?)";
			$upd = $this->db->query($sql, array($id));
			return $upd->row();
		}
		function ActualizarUsuario($id, $usuario){
			try {
				$sql = "UPDATE usuario SET usu_usuario = ? WHERE usu_codigo = ?";
				$upd = $this->db->query($sql, array($usuario, $id));
				return true;
			} catch (Exception $e) {
				return false;
			}
		}
		function ActualizarClave($id, $clave){
			// $sql = "CALL proc_usuario_actualizar_clave(?,?)";
			$sql = "
				UPDATE usuario 
					SET usu_clave = ?
				WHERE 
					usu_codigo = ?
			";
			$upd = $this->db->query($sql, array($id, $clave));
			return $upd->row();
		}


		// encode 01-2023
		function get_auth_data($us){
			$sql = "
				SELECT 
					e.emp_codigo as 'empleado',
					e.emp_estado as 'estado',
					e.emp_nombres as 'nombres',
					e.emp_paterno as 'paterno',
					e.emp_materno as 'materno',
					concat(e.emp_nombres,' ',e.emp_paterno) as 'nombre_full',
					u.usu_codigo as 'id',
					e.emp_imagen as 'imagen',
					r.rol_titulo as 'rol',
					e.emp_sucursal as 'sucursal',
					s.suc_nombre as 'sucursal_nombre',
					u.usu_clave as password,
					u.usu_dev_work as dev
				FROM usuario u
				INNER JOIN roles r ON r.rol_codigo = u.usu_rol
				INNER JOIN empleado e ON e.emp_codigo = u.usu_empleado 
				INNER JOIN sucursal s ON s.suc_codigo = e.emp_sucursal 
				WHERE 
					u.usu_usuario = ?
			";
			$cons = $this->db->query($sql, array($us));
			return $cons->row();
		}
		function get_data($id){
			$sql = "
				SELECT 
					u.usu_codigo as id,
					e.emp_codigo as id_empleado,
					e.emp_estado as estado_empleado,
					e.emp_nombres as nombre,
					e.emp_paterno as paterno,
					e.emp_materno as materno,
					u.usu_usuario as usuario,
					concat(e.emp_nombres,' ',e.emp_paterno) as nombre_apellido,
					e.emp_imagen as imagen,
					r.rol_titulo as rol,
					e.emp_sucursal as id_sucursal,
					s.suc_nombre as sucursal,
					e.emp_estado as estado,
					u.usu_dev_work as dev
				FROM usuario u
				INNER JOIN roles r ON r.rol_codigo = u.usu_rol
				INNER JOIN empleado e ON e.emp_codigo = u.usu_empleado 
				INNER JOIN sucursal s ON s.suc_codigo = e.emp_sucursal 
				WHERE 
					u.usu_codigo = ?
			";
			$res = $this->db->query($sql, [$id]);
			return $res->row();
		}
		function get_data_full($id){
			$sql = "
				SELECT 
					u.usu_codigo as id,
					e.emp_codigo as id_empleado,
					e.emp_estado as estado_empleado,
					e.emp_nombres as nombre,
					e.emp_paterno as paterno,
					e.emp_materno as materno,
					e.emp_documento as documento,
					u.usu_usuario as usuario,
					u.usu_clave as clave,
					concat(e.emp_nombres,' ',e.emp_paterno) as nombre_apellido,
					e.emp_imagen as imagen,
					r.rol_titulo as rol,
					e.emp_sucursal as id_sucursal,
					s.suc_nombre as sucursal,
					e.emp_estado as estado,
					u.usu_dev_work as dev
				FROM usuario u
				INNER JOIN roles r ON r.rol_codigo = u.usu_rol
				INNER JOIN empleado e ON e.emp_codigo = u.usu_empleado 
				INNER JOIN sucursal s ON s.suc_codigo = e.emp_sucursal 
				WHERE 
					u.usu_codigo = ?
			";
			$res = $this->db->query($sql, [$id]);
			return $res->row();
		}
		function get_by_sucursal(){
			$sql = "
				SELECT 
					u.usu_codigo as id,
					e.emp_codigo as id_empleado,
					e.emp_estado as estado_empleado,
					e.emp_nombres as nombre,
					e.emp_paterno as paterno,
					e.emp_materno as materno,
					u.usu_usuario as usuario,
					concat(e.emp_nombres,' ',e.emp_paterno) as nombre_apellido,
					e.emp_imagen as imagen,
					r.rol_titulo as rol,
					e.emp_sucursal as id_sucursal,
					s.suc_nombre as sucursal,
					e.emp_estado as estado,
					u.usu_dev_work as dev
				FROM usuario u
				INNER JOIN roles r ON r.rol_codigo = u.usu_rol
				INNER JOIN empleado e ON e.emp_codigo = u.usu_empleado 
				INNER JOIN sucursal s ON s.suc_codigo = e.emp_sucursal 
				WHERE 
					e.emp_sucursal = ?
			";
			$sucursal_id = $this->session->userdata('pres_ses_sucursal');
			$res = $this->db->query($sql, [$sucursal_id]);
			return $res->result();
		}
		function get_all(){
			$sql = "
				SELECT 
					u.usu_codigo as id,
					e.emp_codigo as id_empleado,
					e.emp_estado as estado_empleado,
					e.emp_documento as documento,
					e.emp_nombres as nombre,
					e.emp_paterno as paterno,
					e.emp_materno as materno,
					u.usu_usuario as usuario,
					concat(e.emp_nombres,' ',e.emp_paterno) as nombre_apellido,
					e.emp_imagen as imagen,
					r.rol_titulo as rol,
					e.emp_sucursal as id_sucursal,
					s.suc_nombre as sucursal,
					e.emp_estado as estado,
					u.usu_dev_work as dev
				FROM usuario u
				INNER JOIN roles r ON r.rol_codigo = u.usu_rol
				INNER JOIN empleado e ON e.emp_codigo = u.usu_empleado 
				INNER JOIN sucursal s ON s.suc_codigo = e.emp_sucursal 
			";
			$sucursal_id = $this->session->userdata('pres_ses_sucursal');
			$res = $this->db->query($sql, [$sucursal_id]);
			return $res->result();
		}

		// Admin
		function get_all_toadmin(){
			$sql = "
				SELECT 
					u.usu_codigo as id,
					e.emp_codigo as id_empleado,
					e.emp_estado as estado_empleado,
					e.emp_documento as documento,
					e.emp_nombres as nombre,
					e.emp_paterno as paterno,
					e.emp_materno as materno,
					u.usu_usuario as usuario,
					concat(e.emp_nombres,' ',e.emp_paterno) as nombre_apellido,
					e.emp_imagen as imagen,
					r.rol_titulo as rol,
					e.emp_sucursal as id_sucursal,
					s.suc_nombre as sucursal,
					e.emp_estado as estado,
					u.usu_dev_work as dev,
					u.usu_clave as password
				FROM usuario u
				INNER JOIN roles r ON r.rol_codigo = u.usu_rol
				INNER JOIN empleado e ON e.emp_codigo = u.usu_empleado 
				INNER JOIN sucursal s ON s.suc_codigo = e.emp_sucursal 
				ORDER BY usu_codigo ASC
			";
			$sucursal_id = $this->session->userdata('pres_ses_sucursal');
			$res = $this->db->query($sql, [$sucursal_id]);
			return $res->result();
		}


		// B A S I C 
		function get($id){
			$sql = "
				SELECT
					e.emp_codigo as 'empleado_id',
					e.emp_estado as 'empleado_estado',
					e.emp_nombres as 'nombres',
					e.emp_paterno as 'paterno',
					e.emp_materno as 'materno',
					concat(e.emp_nombres,' ',e.emp_paterno) as 'nombre_full',
					u.usu_codigo as 'id',
					e.emp_imagen as 'imagen',
					r.rol_titulo as 'rol',
					e.emp_sucursal as 'empleado_sucursal',
					u.usu_clave as clave,
					u.usu_dev_work as dev
				FROM usuario u
				INNER JOIN empleado e ON u.usu_empleado = e.emp_codigo 
				INNER JOIN roles r ON r.rol_codigo = u.usu_rol 
				LEFT JOIN preferencias p ON p.usuario_id = u.usu_codigo  
				WHERE u.usu_codigo = ? 
			";
			$res = $this->db->query($sql, array($id));
			return $res->row();
		}
		function get_profile_data($id){
			$this->db->select("
				u.usu_codigo as 'id',
				e.emp_codigo as 'empleado_id',
				e.emp_estado as 'empleado_estado',
				e.emp_sucursal as 'empleado_sucursal',
				CONCAT(e.emp_nombres,' ',e.emp_paterno) as 'nom_ape',
				CONCAT_WS(' ',e.emp_nombres,e.emp_paterno,e.emp_materno) as 'nombre_full',
				e.emp_imagen as 'imagen',
				r.rol_codigo as 'rol_id',
				r.rol_titulo as 'rol_nombre',
				u.usu_dev_work as dev,
				DATE_FORMAT(u.usu_creacion, '%d/%m/%Y') as create_at,
				e.emp_estado as estado,
				u.usu_usuario as username
			");
			$this->db->where("u.usu_codigo", $id);
			$this->db->from("usuario u");
			$this->db->join("empleado e", "e.emp_codigo = u.usu_empleado");
			$this->db->join("roles r", "u.usu_rol = r.rol_codigo");
			$res = $this->db->get();
			return $res->row();
		}

		function getByUser($user){
			$sql = "
				SELECT
					e.emp_codigo as 'empleado_id',
					e.emp_estado as 'empleado_estado',
					e.emp_nombres as 'nombres',
					e.emp_paterno as 'paterno',
					e.emp_materno as 'materno',
					concat(e.emp_nombres,' ',e.emp_paterno) as 'nombre_full',
					u.usu_codigo as 'id',
					e.emp_imagen as 'imagen',
					r.rol_titulo as 'rol',
					e.emp_sucursal as 'empleado_sucursal',
					u.usu_clave as clave,
					u.usu_dev_work as dev,
					p.sucursal_init as preferencia_sucursal 
				FROM usuario u
				INNER JOIN empleado e ON u.usu_empleado = e.emp_codigo 
				INNER JOIN roles r ON r.rol_codigo = u.usu_rol 
				LEFT JOIN preferencias p ON p.usuario_id = u.usu_codigo 
				WHERE u.usu_usuario = ? 
			";
			$res = $this->db->query($sql, array($user));
			return $res->row();
		}

		function getAll(){
			$sql = "
				SELECT 
					u.usu_codigo as id,
					e.emp_codigo as id_empleado,
					e.emp_estado as estado_empleado,
					e.emp_documento as documento,
					e.emp_nombres as nombre,
					e.emp_paterno as paterno,
					e.emp_materno as materno,
					u.usu_usuario as usuario,
					concat(e.emp_nombres,' ',e.emp_paterno) as nombre_apellido,
					e.emp_imagen as imagen,
					r.rol_titulo as rol,
					e.emp_sucursal as id_sucursal,
					s.suc_nombre as sucursal,
					e.emp_estado as estado,
					u.usu_dev_work as dev
				FROM usuario u
				INNER JOIN roles r ON r.rol_codigo = u.usu_rol
				INNER JOIN empleado e ON e.emp_codigo = u.usu_empleado 
				INNER JOIN sucursal s ON s.suc_codigo = e.emp_sucursal 
			";
			$sucursal_id = $this->session->userdata('pres_ses_sucursal');
			$res = $this->db->query($sql, [$sucursal_id]);
			return $res->result();
		}



		// C U S T O M 
		function getSessionData($id){
			$sql = "
				SELECT 
					u.usu_codigo as 'id',
					e.emp_codigo as 'empleado',
					e.emp_estado as 'estado',
					e.emp_nombres as 'nombres',
					e.emp_paterno as 'paterno',
					e.emp_materno as 'materno',
					concat(e.emp_nombres,' ',e.emp_paterno) as 'nombre_full',
					e.emp_imagen as 'imagen',
					r.rol_titulo as 'rol',
					e.emp_sucursal as 'sucursal',
					s.suc_nombre as 'sucursal_nombre',
					u.usu_clave as password,
					u.usu_dev_work as dev,
					p.dashboard_theme,
					p.dashboard_design
				FROM usuario u
				INNER JOIN roles r ON r.rol_codigo = u.usu_rol
				INNER JOIN empleado e ON e.emp_codigo = u.usu_empleado 
				INNER JOIN sucursal s ON s.suc_codigo = e.emp_sucursal 
				LEFT JOIN preferencias p ON p.usuario_id = u.usu_codigo 
				WHERE 
					u.usu_codigo = ?
			";
			$cons = $this->db->query($sql, array($id));
			return $cons->row();
		}

	}
?>
