<?php  

	class Sesion extends CI_Controller{
		
		function __construct(){
			parent::__construct();
			$this->load->model('UsuarioModel');
			$this->load->model('MenuModel');
			$this->load->model('ConfiguracionModel');
			$this->load->model('SucursalModel');
			$this->load->model('SystemConfigModel');
			// header('Access-Control-Allow-Origin: *');

		}	
		
		// V I E W S
		function login_form(){
			if ($this->session->userdata('pres_ses_sesion')) {
				redirect(base_url().'Inicio','refresh');
			}
			// $view = 'login_view_1.php';
			// $config = $this->SystemConfigModel->get();
			// if (!empty($config) && $config->view_login){
			// 	$view = $config->view_login;
			// }			
			// $this->load->view("Sesion/" . $view);
			$this->load->view("Sesion/login.php");

		}
		function revision_screen(){
			$this->load->view("system/revision_global.php");
		}
		function profile_view(){
			$user_id = $this->session->userdata('usuario_id');
			$user = $this->UsuarioModel->get_profile_data($user_id);
			if (empty($user)) {
				$data['titulo'] = 'Datos no encontrados';
				$data['mensaje'] = 'No se encontraron datos personales del usuario';
				$this->load->view("Layout/header.php");
				$this->load->view("Error/no_found.php", $data);
				$this->load->view("Layout/footer.php");
				return;
			}
			$data['usuario'] = $user;
			$this->load->view("Layout/header.php");
			// $this->load->view("Usuario/perfil_v2.php", $data);
			$this->load->view("Usuario/profile.php", $data);
			$this->load->view("Layout/footer.php");
		}


		// A P I S

		function autenticar(){
			try {
				// header('Access-Control-Allow-Origin: *');
				$post = $this->input->post();	
				$resp = [];

				if (empty($post['_username']) || empty($post['_password'])){
					die(json_encode([
						'status' => 'WARN',
						'message' => 'Credenciales no proporcionadas.'
					]));
				}

				$user_data = $this->UsuarioModel->getByUser($post['_username']);
				if (empty($user_data)) {
					die(json_encode([
						'status' => 'WARN',
						'message' => 'Usuario no encontrado.'
					]));
				}
				if ($post['_password'] != $user_data->clave){
					if (!password_verify($post['_password'], $user_data->clave)) {
						die(json_encode([
							'status' => 'WARN',
							'message' => 'Credenciales Incorrectas.',
						]));
					}
				}
				if ($user_data->empleado_estado != 'A') {
					die(json_encode([
						'status' => 'WARN',
						'message' => 'Usuario no valido.'
					]));
				}

				if (!$user_data->preferencia_sucursal && !$user_data->empleado_sucursal) {
					die(json_encode([
						'status' => 'WARN',
						'message' => 'El usuario no tiene sucursal asignada.'
					]));
				}
				
				$sede_id = $user_data->preferencia_sucursal > 0 ? $user_data->preferencia_sucursal : $user_data->empleado_sucursal;
				$sede_data = $this->SucursalModel->get($sede_id);
				// die(json_encode($sede_id));
				if (empty($sede_data)) {
					die(json_encode([
						'status' => 'WARN',
						'message' => 'Sucursal de acceso no identificada.'
					]));
				}


				$this->create_session_data($user_data, $sede_data);
				$resp['status'] = 'OK';
				$resp['message'] = 'Bienvenido <b>'.$user_data->nombre_full."</b>";

				echo json_encode($resp);
			} catch (Exception $e) {
				die([
					'status' => 'ERROR',
					'message' => 'Error al procesar'
				]);
			}
		}

		function create_session_data($UserData, $sede){
			$menu = ($UserData->rol === 'Administrador') 
			? $this->MenuModel->get_data_modules_admin() 
			: $this->MenuModel->get_data_modules($UserData->id);
			
			$nombre_array = explode(" ", $UserData->nombre_full);
			$acronimo = substr($nombre_array[0], 0, 1).substr($nombre_array[1], 0, 1);
			$sesion = array(
				'usuario_id' => $UserData->id,
				'usuario_rol' => $UserData->rol,
				'usuario_nombre' => $UserData->nombre_full,
				'sucursal_id' => $sede->id,
				'sucursal_nombre' => $sede->nombre,
				'sucursal_serie' => $sede->serie,
				'pres_ses_menu' => $menu,
				'pres_ses_usuario'  => $UserData->id,
				'pres_ses_imagen'   => $UserData->imagen,
				'pres_ses_sucursal' => $sede->id,
				'pres_ses_sede' => $sede->nombre,
				'pres_ses_empleado' => $UserData->empleado_id,
				'pres_ses_rol' => $UserData->rol,
				'pres_ses_nombre'   => $UserData->nombre_full,
				'pres_ses_acronimo' => strtoupper($acronimo),
				'pres_acceso_fecha' => date("Y-m-d"),
				'pres_ses_sesion'   => true,
				'pres_ses_dev'   => $UserData->dev
			);
			$this->session->set_userdata($sesion);
		}

		function desconectar(){
			$resp = [];
			try {
				$this->session->unset_userdata('usuario_id');
				$this->session->unset_userdata('usuario_rol');
				$this->session->unset_userdata('usuario_nombre');
				$this->session->unset_userdata('sucursal_id');
				$this->session->unset_userdata('sucursal_nombre');

				$this->session->unset_userdata('pres_ses_menu');
				$this->session->unset_userdata('pres_ses_usuario');
				$this->session->unset_userdata('pres_ses_imagen');
				$this->session->unset_userdata('pres_ses_sucursal');
				$this->session->unset_userdata('pres_ses_empleado');
				$this->session->unset_userdata('pres_ses_nombre');
				$this->session->unset_userdata('pres_ses_rol');
				$this->session->unset_userdata('pres_ses_sesion');
				
				$this->session->sess_destroy();
				if ($this->session->userdata('pres_ses_sesion')) {	
					die(json_encode([
						'code' => 'ERR',
						'message' => 'Cierre de sesion incompleto'
					]));				
				}
				echo json_encode([
					'code' => 'OK',
					'message' => 'Sesion Finalizada'
				]);
			} catch (Exception $e) {
				$resp['code'] = 'ERR';
				$resp['message'] = 'Cierre de sesion incompleto.';
				echo json_encode($resp);
			}
		}		
		// Establece la sucursal de la sesion
		// Opcion valida solo para el 'Administrador'
		function service_establecer_sede(){
			try {
				$file = file_get_contents('php://input');
				$file = json_decode($file);
				$rol = $this->session->userdata('pres_ses_rol');
				if ($rol != 'Administrador') {
					throw new Exception('[user invalid]');
				}
				if (!$file->id || !is_numeric($file->id)) {
					throw new Exception('[id not found]');
				}
				$data = $this->SucursalModel->get_data($file->id);
				if (!$data) {
					throw new Exception('[sede not exist]');
				}
				$_SESSION['pres_ses_sucursal'] = $data->id;
				$_SESSION['pres_ses_sede'] = $data->nombre;
				$_SESSION['sucursal_id'] = $data->id;
				$_SESSION['sucursal_nombre'] = $data->nombre;
				$_SESSION['sucursal_serie'] = $data->serie;
				echo json_encode([
					'status' => 'OK',
					'message' => 'Cambio Aplicado'
				]);
				
			} catch (Exception $e) {
				die(json_encode([
					'status' => 'ERR',
					'message' => 'Error al procesar',
					'console' => $e->getMessage()
				]));
			}
		}
	}
?>
