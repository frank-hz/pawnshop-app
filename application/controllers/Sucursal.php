<?php  
	
	class Sucursal extends CI_Controller
	{
		private $user_id;
		private $user_rol;
		function __construct(){
			parent::__construct();
			if (!$this->session->userdata('pres_ses_sesion')) {
				redirect(base_url().'Acceso','refresh');
			}
			$this->load->model('SucursalModel');
			$this->load->model('PermisoModel');
			$this->load->model('BancoModel');		
			$this->load->model('EmpleadoModel');	
			$this->load->model('PreferenciasModel');
			$this->user_id = $this->session->userdata('usuario_id');
			$this->user_rol = $this->session->userdata('usuario_rol');
		}

		function index(){
			$rol = $this->session->userdata('pres_ses_rol');
			$data = [];
			if ($rol === 'Administrador') {
				$data['sucursales'] = $this->SucursalModel->get_data_all();
			}else {
				$ids = $this->session->userdata('pres_ses_sucursal');
				$data['sucursales'] = [
					$this->SucursalModel->get_data($ids)
				];
			}
			$this->load->view('Layout/header.php');
			// $this->load->view('Sucursal/lista.php', $data);
			$this->load->view('Sucursal/index', $data);
			$this->load->view('Layout/footer.php');
		}
		function nuevo(){
			$permiso = $this->PermisoModel->verificar_permiso(
				$this->session->userdata('pres_ses_usuario'),
				'sucursal_registrar'
			);
			if ( $permiso && $permiso->codigo == 'P' ) {
				$data['banco'] = $this->BancoModel->activos();
				$this->load->view("Layout/header.php");
				$this->load->view("Sucursal/registro.php", $data);
				$this->load->view("Layout/footer.php");
			}else{
				$data['titulo'] = 'Permisos insuficientes';
				$data['mensaje'] = 'El usuario actual no tiene los privilegios suficientes para registrar sucursales.';
				$this->load->view("Layout/header.php");
				$this->load->view("Error/no_access.php", $data);
				$this->load->view("Layout/footer.php");
			}
		}
		function detalle($id = null){
			if ($id != null) {
				ini_set('display_errors', 0);
				$data['sucursal'] = $this->SucursalModel->get_data($id);
				if (!empty($data['sucursal'])) {
					$this->load->view("Layout/header.php");
					$this->load->view("Sucursal/detalle.php", $data);
					$this->load->view("Layout/footer.php");
				}else{
					$data['titulo'] = 'Sucursal desconocida';
					$data['mensaje'] = 'No se encontraron datos de la sucursal';
					$this->load->view("Layout/header.php");
					$this->load->view("Error/no_found.php", $data);
					$this->load->view("Layout/footer.php");
				}
			}else{
				$data['titulo'] = 'Sucursal desconocida';
				$data['mensaje'] = 'No se encontraron datos para el seguimiento de la sucursal';
				$this->load->view("Layout/header.php");
				$this->load->view("Error/no_found.php", $data);
				$this->load->view("Layout/footer.php");
			}
		}
		function editar($id = null){
			if ($id != null) {
				ini_set('display_errors', 0);
				$data['banco'] = $this->BancoModel->activos();
				$data['sucursal'] = $this->SucursalModel->get_data($id);
				$this->load->view('Layout/header.php');
				$this->load->view('Sucursal/editar.php', $data);
				$this->load->view('Layout/footer.php');
			}else{
				$data['titulo'] = 'Sucursal desconocida';
				$data['mensaje'] = 'No se encontraron datos para el seguimiento de la sucursal';
				$this->load->view("Layout/header.php");
				$this->load->view("Error/no_found.php", $data);
				$this->load->view("Layout/footer.php");
			}
		}



		function consulta(){
			if (isset($_POST['tipo'])) {
				if ($_POST['tipo'] === 'breve') {
					$res = $this->SucursalModel->Lista_breve();
				}else {
					$res = $this->SucursalModel->Lista();
				}
				$suc = $this->session->userdata('pres_ses_sucursal');
				foreach ($res  as $item) {
					$item->estado = ($item->id === $suc) ? 'A' : 'I';
				}
			}else{
				$res = [];
			}
			echo json_encode($res);
		}
		// 
		function consulta_empleados(){
			$res = $this->SucursalModel->Buscar_empleados($_POST['id']);
			echo json_encode($res);
		}		
		
		// Funcion de registro de sucursal
		function registrar(){
			$post = $this->input->post();
			$resp = [];
			$permiso = $this->PermisoModel->verificar_permiso(
				$this->session->userdata('pres_ses_usuario'),
				'sucursal_registro'
			);
			if ( $permiso && $permiso->codigo == 'P' ) {
				$valida = $this->validar_creacion($post);
				if ( count($valida) == 0 ) {
					try {
						$this->db->trans_begin();
						$serie = '';
						$sigla = '';
						$orden = 0;
						$vsuc = $this->SucursalModel->sucursal_ciudad($_POST['ubigeo']);
						if (!empty($vsuc)) {
							$orden = $vsuc->orden + 1;
							$sigla = $vsuc->sigla;
							$serie = ($orden > 9) ? $vsuc->sigla.$orden :  $vsuc->sigla."0".$orden;
						}else{
							$upp_ciudad = strtoupper($_POST['ciudad']);
							$upp_ciudad = str_replace(' ', '', $upp_ciudad);
							$inicial = substr($upp_ciudad, 0, 1);
							$intent = 0;
							while ($serie === '') {
								if ($intent === 10) {
									die("error al procesar");
								}
								$intent++;
								$rnd = rand(1, strlen($upp_ciudad));
								$final = substr($upp_ciudad, $rnd, 1);
								$eval = $inicial.$final."01";
								if (empty($this->SucursalModel->verificar_serie($eval))) {
									$serie = $eval;
									$sigla = $inicial.$final;
									$orden = 1;
								}
							}				

						}
						$v_orden = $this->SucursalModel->Buscar_libre('COUNT(*) as numero', '1=1');
						$p_serie = "T".($v_orden->numero + 1)."-P"; 
						$data = array(
							'suc_nombre' => strtoupper($post['descripcion']),
							'suc_ubigeo' => $post['ubigeo'],
							'suc_sigla' => $sigla,
							'suc_orden' => $orden,
							'suc_serie' => $serie,
							'suc_pago_serie' => $p_serie,
							'suc_cuenta_tipo' => $_POST['cuenta_tipo'],
							'suc_cuenta_numero' => $_POST['cuenta_numero'],
							'suc_banco' => strtoupper($_POST['banco']),
							'suc_direccion' => strtoupper($post['direccion']),
							'suc_telefono' => $post['telefono'],
							'suc_creacion' => date("Y-m-d")
						);
						// die(json_encode($data));
						$this->SucursalModel->insertar($data);
						$id = $this->db->insert_id();
						if ($id == null) {
							die(json_encode(array(
								'status' => 'ERROR',
								'message' => 'error al registrar'
							)));
						}
						$resp = array(
							'status' => 'OK',
							'message' => 'Sucursal creada',
							'id' => $id
						);
						$this->db->trans_commit();
					} catch (PDOException $e) {
						$this->db->trans_rollback();
						die($e->getMessage());
					}
				}else{
					$resp['status'] = 'ERROR';
					$resp['message'] = 'Errores encontrados';
					$resp['error'] = $valida; 
				} 

			}else{
				$resp['status'] = 'WARN';
				$resp['message'] = 'Permisos insuficientes';
				$resp['error'] = ''; 
			}
			echo json_encode($resp);
		}
		// Funcion de actualizacion de sucursal
		function modificar(){
			$post = $this->input->post();
			$resp = [];
			$permiso = $this->PermisoModel->verificar_permiso(
				$this->session->userdata('pres_ses_usuario'),
				'sucursal_editar'
			);
			if ( $permiso && $permiso->codigo == 'P' ) {
				$valida = $this->validar_editar($post);
				if ( count($valida) == 0 ) {
					$upd_data = array(
						'suc_descripcion' => strtoupper($post['descripcion']),
						'suc_ubigeo' => $post['ubigeo'],
						'suc_direccion' => strtoupper($post['direccion']),
						'suc_telefono' => $post['telefono'],
						'suc_estado' => 'A',
						'suc_cuenta_tipo' => $post['cuenta_tipo'],
						'suc_cuenta_numero' => $post['cuenta_numero'],
						'suc_serie' => strtoupper($post['serie']),
						'suc_pago_serie' => strtoupper($post['serie_pago']),
						'suc_banco' => strtoupper($post['banco'])
					);
					$this->SucursalModel->modificar($post['id'], $upd_data);
					$rows = $this->db->affected_rows();
					$mssg = ($rows > 0) ? 'Cambios aplicados' : 'Sin cambios';
					$resp = array('status' => 'OK', 'message' => $mssg);
				}else{
					$resp['status'] = 'ERROR';
					$resp['message'] = 'Errores encontrados';
					$resp['error'] = $valida; 
				} 

			}else{
				$resp['status'] = 'WARN';
				$resp['message'] = 'Permisos insuficientes';
				$resp['error'] = ''; 
			}
			echo json_encode($resp);
		}
		// Funcion de eliminacion de sucursal
		function eliminar(){
			$resp = [];
			$permiso = $this->PermisoModel->verificar_permiso(
				$this->session->userdata('pres_ses_usuario'),
				'sucursal_eliminar'
			);
			if ( $permiso && $permiso->codigo == 'P' ) {
				$v_empleado = $this->SucursalModel->Buscar_empleados($_POST['id']);
				if (count($v_empleado) === 0) {
					$this->SucursalModel->eliminar($_POST['id']);
					$verifica = $this->SucursalModel->Buscar_detalle($_POST['id']);
					if (empty($verifica)) {
						$resp = array(
							'status' => 'OK',
							'message' => 'Sucursal eliminada'
						);	
					}else{
						$resp = array(
							'status' => 'ERROR',
							'message' => 'No se completo el proceso de eliminacion'
						);
					}
				}else{
					$resp = array(
						'status' => 'ERROR',
						'message' => 'La sucursal tiene empleados asignados'
					);
				}
			}else{
				$resp = array(
					'status' => 'WARN',
					'message' => 'Permisos insuficientes'
				);
			}
			echo json_encode($resp);
		}
		function validar_creacion($data){

			$errors = [];
			if ( empty($data['descripcion']) ) {
				$errors['descripcion'] = 'La descripcion es requerida.';
			}
			if ( empty($data['ubigeo']) ) {
				$errors['ubigeo'] = 'La ciudad es requerida.';
			}
			if ( empty($data['direccion']) ) {
				$errors['direccion'] = 'La direccion es requerida.';
			}
			if ( empty($data['cuenta_numero']) ) {
				$errors['cuenta_numero'] = 'El numero de cuenta es requerido.';
			}
			if ( empty($data['banco']) ) {
				$errors['banco'] = 'El banco de la cuenta es requerido.';
			}
			if (isset($data['descripcion']) && isset($data['ubigeo'])) {
				$vdatos = $this->SucursalModel->validar_sucursal($data['descripcion'], $data['ubigeo'],'ins');
				if (count($vdatos) > 0) {
					$errors['direccion'] = 'Ya existe una sucursal en la misma ciudad y direccion';
				}
			}
			return $errors;
		}
		function validar_editar($data){

			$errors = [];
			if ( empty($data['id']) ) {
				$errors['id'] = 'Sucursal desconocida.';
			}
			if ( empty($data['descripcion']) ) {
				$errors['descripcion'] = 'La descripcion es requerida.';
			}
			if ( empty($data['ubigeo']) ) {
				$errors['ubigeo'] = 'La ciudad es requerida.';
			}
			if ( empty($data['direccion']) ) {
				$errors['direccion'] = 'La direccion es requerida.';
			}
			if ( empty($data['cuenta_numero']) ) {
				$errors['cuenta_numero'] = 'El numero de cuenta es requerido.';
			}
			if ( empty($data['banco']) ) {
				$errors['banco'] = 'El banco de la cuenta es requerido.';
			}
			if (isset($data['descripcion']) && isset($data['ubigeo'])) {
				$vdatos = $this->SucursalModel->validar_sucursal($data['descripcion'], $data['ubigeo'],'upd');
				if (count($vdatos) > 0) {
					$errors['direccion'] = 'Ya existe una sucursal en la misma ciudad y direccion';
				}
			}
			return $errors;
		}


		// recode 01-2023
		function service_get_all(){
			$rol = $this->session->userdata('pres_ses_rol');
			$idc = $this->session->userdata('pres_ses_sucursal');
			$data = [];
			if ($rol === 'Administrador') {
				$data = $this->SucursalModel->get_data_all();
			}else {
				$ids = $this->session->userdata('pres_ses_sucursal');
				$data = [
					$this->SucursalModel->get_data($ids)
				];
			}
			echo json_encode([
				'result' => $data,
				'idc' => $idc
			]);
		}
		function service_get_data(){
			$file = json_decode(file_get_contents('php://input'));
			$data = $this->SucursalModel->get_data($file->id);
			echo json_encode($data);
		}
		
		function service_update_data(){
			$data = [
				'suc_nombre' => strtoupper($_POST['nombre']),
				'suc_ubigeo' => $_POST['ubigeo'],
				'suc_direccion' => $_POST['direccion'],
				'suc_telefono' => $_POST['telefono'],
				'suc_cuenta_tipo' => strtoupper($_POST['cuenta_tipo']),
				'suc_cuenta_numero' => strtoupper($_POST['cuenta_numero']),
				'suc_banco' => strtoupper($_POST['banco'])
			];
			$upd = $this->SucursalModel->update_data($_POST['id'],$data);
			$chgs = $this->db->affected_rows();
			echo json_encode([
				'status' => ($chgs > 0) ? 'OK' : 'WARN',
				'message' => ($chgs > 0) ? 'Cambios Aplicados' : 'Sin Cambios'
			]);
		}

		
		// A P I S
		function get_available_for_user(){
			$data = [];
			$preferencias = $this->PreferenciasModel->getByUser($this->user_id);
			$current_sede_init = $preferencias->sede_init ?? 0; 
			
			if (in_array($this->user_rol, ['ADMIN','Administrador','Owner'])){
				$data = $this->SucursalModel->getAll();
			}else {
				$empleado = $this->EmpleadoModel->getByUser($this->user_id);
				if ($empleado){				
					$obj = new stdClass();
					$obj->id = $empleado->sucursal_id;
					$obj->nombre = $empleado->sucursal_nombre;	
					array_push($data, $obj);
				}
				if ($preferencias && $preferencias->sucursal_init){
					$obj = new stdClass();
					$obj->id = $preferencias->sede_init;
					$obj->nombre = $preferencias->sede_nombre;
					array_push($data, $obj);
				}
			}
			// die(json_encode($preferencias));
			foreach ($data as $item) {
				if ($item->id === $current_sede_init){
					$item->active = 1;
				}
			}

			echo json_encode($data);
		}

		function get_for_public(){
			$data = $this->SucursalModel->getAll();
			echo json_encode($data);
		}

	}
?>
