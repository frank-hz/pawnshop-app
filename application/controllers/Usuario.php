<?php  

	class Usuario extends CI_Controller
	{
		private $user_id;	
		function __construct()
		{
			parent::__construct();
			$this->load->model('PermisoModel');
			$this->load->model('UsuarioModel');
			$this->load->model('EmpleadoModel');
			if (!$this->session->userdata('pres_ses_sesion')) {
				redirect(base_url().'Acceso','refresh');
			}
			$this->user_id = $this->session->userdata('pres_ses_usuario');
		}

		// V I E W S
		function index(){
			$this->load->view("Layout/header.php");
			// $this->load->view("Usuario/lista.php");
			$this->load->view("Usuario/index.php");
			$this->load->view("Layout/footer.php");
		}
		function detalle($id = null){
			if ($id != null) {
				$data['usuario'] = $this->UsuarioModel->consulta('detalle', $id);
				if (!empty($data['usuario'])) {
					$this->load->view("Layout/header.php");
					$this->load->view("Usuario/detalle.php", $data);
					$this->load->view("Layout/footer.php");
				}else{
					$data['titulo'] = 'Usuario desconocido';
					$data['mensaje'] = 'No se encontraron datos del usuario';
					$this->load->view("Layout/header.php");
					$this->load->view("Error/no_found.php", $data);
					$this->load->view("Layout/footer.php");
				}
			}else{
				$data['titulo'] = 'Usuario desconocido';
				$data['mensaje'] = 'No se encontraron datos para el seguimiento del usuario';
				$this->load->view("Layout/header.php");
				$this->load->view("Error/no_found.php", $data);
				$this->load->view("Layout/footer.php");
			}
		}
		function editar($id = null){
			if ($id != null) {
				$permiso = $this->PermisoModel->verificar_permiso(
					$this->session->userdata('pres_ses_usuario'),
					'usuario_editar'
				);
				if ($permiso->codigo == 'P') {
					$data['usuario'] = $this->UsuarioModel->get($id);
					$this->load->view("Layout/header.php");
					$this->load->view("Usuario/editar.php", $data);
					$this->load->view("Layout/footer.php");
				}else{
					$data['titulo'] = 'Permisos insuficientes';
					$data['mensaje'] = 'el usuario actual no tiene acceso a la edicion de usuarios';
					$this->load->view("Layout/header.php");
					$this->load->view("Error/no_found.php", $data);
					$this->load->view("Layout/footer.php");
				}
			}else{
				$data['titulo'] = 'Usuario desconocido';
				$data['mensaje'] = 'No se encontraron datos para el seguimiento del usuario';
				$this->load->view("Layout/header.php");
				$this->load->view("Error/no_found.php", $data);
				$this->load->view("Layout/footer.php");
			}
		}
		function view_perfil(){
			$data['usuario'] = $this->UsuarioModel->getForProfile($this->user_id);
			if (!empty($data['usuario'])) {
				$this->load->view("Layout/header.php");
				$this->load->view("Usuario/perfil_v2.php", $data);
				$this->load->view("Layout/footer.php");
			}else{
				$data['titulo'] = 'Datos no encontrados';
				$data['mensaje'] = 'No se encontraron datos personales del usuario';
				$this->load->view("Layout/header.php");
				$this->load->view("Error/no_found.php", $data);
				$this->load->view("Layout/footer.php");
			}
		}




		function consulta_modulos(){
			$res = $this->UsuarioModel->Buscar_modulos($_POST['id']);
			echo json_encode($res);
		}
		function consulta_permisos(){
			$res = $this->UsuarioModel->Buscar_permisos($_POST['id']);
			echo json_encode($res);
		}
		function REST(){
			$post = $this->input->post();
			$rest = $this->UsuarioModel->consulta($post['tipo'], $post['buscar']);
			echo json_encode($rest);
		}	
		function administrarPermiso(){
			$post = $this->input->post();
			$resp = [];
			if ( $this->session->userdata('pres_ses_rol') === 'Administrador' ) {

				$upd = $this->UsuarioModel->actualizarPermiso($post['tipo'], $post['valorA'], $post['valorB']);
				if ($upd) {
					$resp['code'] = 'Ok';
					$resp['message'] = 'Permisos actualizados.';
					$resp['error'] = ''; 
				}else{
					$resp['code'] = 'Error';
					$resp['message'] = 'Error al procesar.';
					$resp['error'] = ''; 
				}
			}else{
				$resp['code'] = 'Warn';
				$resp['message'] = 'Accion permitida solo para administradores.';
				$resp['error'] = ''; 
			}
			echo json_encode($resp);
		}
		function administrarModulo(){
			$post = $this->input->post();
			$resp = [];
			if ( $this->session->userdata('pres_ses_rol') === 'Administrador' ) {

				$upd = $this->UsuarioModel->actualizarModulo($post['tipo'], $post['valorA'], $post['valorB']);
				if ($upd) {
					$resp['code'] = 'Ok';
					$resp['message'] = 'Modulos actualizados.';
					$resp['error'] = ''; 
				}else{
					$resp['code'] = 'Error';
					$resp['message'] = 'Error al procesar.';
					$resp['error'] = ''; 
				}
			}else{
				$resp['code'] = 'Warn';
				$resp['message'] = 'Accion permitida solo para administradores.';
				$resp['error'] = ''; 
			}
			echo json_encode($resp);
		}
		function reinicio(){
			$resp = [];
			// $permiso = $this->PermisoModel->verificar_permiso(
			// 	$this->session->userdata('pres_ses_usuario'),
			// 	'usuario_editar'
			// );
			if ($this->session->userdata('pres_ses_rol') === 'Administrador') {
				try {
					$this->db->trans_begin();
					$emp_data = $this->EmpleadoModel->Buscar_libre('emp_nombres as nombre, emp_paterno as paterno, emp_materno as materno, emp_documento as dni', array(
						'emp_codigo' => $_POST['empleado']
					));
					if (empty($emp_data)) {
						die(json_encode(array('status'=>'ERROR', 'message'=>'error al reiniciar')));
					}
					$letra = substr($emp_data->nombre, 0, 1);
					$ape_paterno = $emp_data->paterno;
					$ape_materno = $emp_data->materno;
					$gen_usuario = strtolower( $letra . $ape_paterno . $ape_materno );
					$update = $this->UsuarioModel->Modificar($_POST['usuario'], array(
						'usu_usuario' => $gen_usuario,
						'usu_clave' => $emp_data->dni
					));

					$changes = $this->db->affected_rows();
					$resp = array(
						'status' => 'OK', 
						'message' => $update ? 'usuario reiniciado' : 'Sin cambios'
					);
					$this->db->trans_commit();
				} catch (PDOException $e) {
					$this->db->trans_rollback();
					die(json_encode(array('status'=>'ERROR', 'message'=>'error al reiniciar: '.$e->getMessage())));
				}
			}else{
				$resp['status'] = 'WARN';
				$resp['message'] = 'Accion permitida solo para administradores.';
				$resp['error'] = '';
			}
			echo json_encode($resp);
		}
		function modificar(){
			$post = $this->input->post();
			$resp = [];
			$error = '';
			if ($this->session->userdata('pres_ses_usuario') === $_POST['id']) {
				if (!empty($_POST['usuario']) || !empty($_POST['clave'])) {
					if (!empty($_POST['usuario'])) $mod_data['usu_usuario'] = $_POST['usuario'];
					if (!empty($_POST['clave'])) $mod_data['usu_clave'] = $_POST['clave'];

					$verifica = $this->UsuarioModel->Buscar_libre('usu_codigo as id', array(
						'usu_clave' => $_POST['clave_actual'],
						'usu_codigo' => $_POST['id']
					));
					if (!empty($verifica)) {
						$this->UsuarioModel->Modificar($_POST['id'], $mod_data);
						if ($this->db->affected_rows() > 0) {
							$msg = 'Cambios aplicado';
						}else{
							$msg = 'Sin cambios';
						}
						$resp = array(
							'status' => 'OK',
							'message' => $msg
						);
					}else{
						$resp = array(
							'status' => 'ERROR',
							'message' => 'error al validar la contraseña actual'
						);
					}
				}else{
					die('Error');
				}
			}else{
				$resp['status'] = 'Warn';
				$resp['message'] = 'Credenciales alteradas.';
				$resp['error'] = ''; 
			}
			echo json_encode($resp);
		}
		function modificar_imagen($id = null){
			$post = $this->input->post();
			$resp = [];
			if (isset($_FILES['imagen']['name']) && $post['id']) {
				$dir = "uploads/empleados/";
				$fecha = date("Y-m-d_His");
				$ext = pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION);
				$nombre = $post['nombre_imagen'].$fecha.".".$ext;
				$archivo = $dir.$nombre;
				if (move_uploaded_file($_FILES['imagen']['tmp_name'], $archivo)) {
					$this->EmpleadoModel->actualiza_imagen(
						$post['id'],
						$archivo
					);
					if (!empty($post['url'])) {
						unlink($post['url']);
					}
					$this->session->set_userdata('pres_ses_imagen', $archivo);
					$resp['status'] = 'OK';
					$resp['message'] = 'Foto actualizada';
				}else{
					$resp['status'] = 'ERROR';
					$resp['message'] = 'Actualizacion incompleta';
				}
			}else{
				$resp['status'] = 'WARN';
				$resp['message'] = 'Datos no especificados';
			}
			echo json_encode($resp);
		}
		function modificar_estado(){
			$rsp = [];
			$rol = $this->session->userdata('pres_ses_rol');
			// echo $rol;
			if (in_array($rol, ['Administrador','ADMIN','DEV'])) {
				if (!empty($_POST['id']) && !empty($_POST['estado'])) {
					
					$id = $_POST['id'];
					$new_estado = $_POST['estado'];
					$id_emp = $_POST['id_empleado'];
					$data = ['usu_estado' => $new_estado];
					$update_usuario = $this->UsuarioModel->Modificar($id, $data);
					$update_empleado = $this->EmpleadoModel->Modificar($id_emp, [
						'emp_estado' => $new_estado
					]);
					// echo json_encode($data);
					if ($this->db->affected_rows() > 0) {
						$rsp = array(
							'status' => 'OK',
							'message' => 'Proceso completado con exito'
						);
					}else{
						$rsp = array(
							'status' => 'ERROR',
							'message' => 'No se pudo completar el proceso',
							'data' => $data
						);
					}
				}
			}else{
				$rsp = array(
					'status' => 'WARN',
					'message' => 'Permisos insuficientes'
				);
			}
			echo json_encode($rsp);
		}

		// encode 01-2023
		function service_get_all(){
			$data = $this->UsuarioModel->get_by_sucursal();
			echo json_encode($data);
		}

	}
?>