<?php  
	
	class Cliente extends CI_Controller
	{
		function __construct(){
			parent::__construct();
			if (!$this->session->userdata('pres_ses_sesion')) {
				redirect(base_url().'Acceso','refresh');
			}
			$this->load->model('ClienteModel');
			$this->load->model('ClienteAPIModel');
			$this->load->model('SucursalModel');
			$this->load->model('PermisoModel');
			$this->load->model('TipoDocumentoModel');
		}


		// Views
		function index(){
			$this->load->view("Layout/header-rw.php");
			$this->load->view("Cliente/lista.php");
			$this->load->view("Layout/footer.php");
		}
		function nuevo(){
			$permiso = $this->PermisoModel->verificar_permiso(
				$this->session->userdata('pres_ses_usuario'),
				'cliente_registrar'
			);
			if ( $permiso && $permiso->codigo == 'P' ) {
				$data['tipo_doc'] = $this->TipoDocumentoModel->consulta('rest');
				$this->load->view("Layout/header.php");
				$this->load->view("Cliente/registro.php", $data);
				$this->load->view("Layout/footer.php");
			}else{
				$data['titulo'] = 'Permisos insuficientes';
				$data['mensaje'] = 'El usuario actual no tiene los privilegios suficientes para registrar clientes.';
				$this->load->view("Layout/header.php");
				$this->load->view("Error/no_access.php", $data);
				$this->load->view("Layout/footer.php");
			}
		}
		function editar($id = null){
			$permiso = $this->PermisoModel->verificar_permiso(
				$this->session->userdata('pres_ses_usuario'),
				'cliente_editar'
			);
			if ( $permiso && $permiso->codigo == 'P' ) {
				if ($id != null) {
					$data['tipo_doc'] = $this->TipoDocumentoModel->consulta('rest');
					$data['cliente'] = $this->ClienteModel->Buscar_detalle($id);
					$this->load->view("Layout/header.php");
					$this->load->view("Cliente/editar.php", $data);
					$this->load->view("Layout/footer.php");
				}else{
					$data['titulo'] = 'Cliente desconocido';
					$data['mensaje'] = 'No se encontraron datos para el seguimiento del cliente';
					$this->load->view("Layout/header.php");
					$this->load->view("Error/no_found.php", $data);
					$this->load->view("Layout/footer.php");
				}
			}else{
				$data['titulo'] = 'Permisos insuficientes';
				$data['mensaje'] = 'El usuario actual no tiene los privilegios suficientes para editar los datos del cliente.';
				$this->load->view("Layout/header.php");
				$this->load->view("Error/no_access.php", $data);
				$this->load->view("Layout/footer.php");
			}
		}
		function detalle($id = null){
			if ($id != null) {
				$data['cliente'] = $this->ClienteAPIModel->getById($id);
				// $data['cliente'] = $this->ClienteModel->Buscar_detalle($id);
				if (!empty($data['cliente'])) {
					$this->load->view("Layout/header.php");
					// $this->load->view("Cliente/detalle2.php", $data);
					$this->load->view("Cliente/detalle.php", $data);
					$this->load->view("Layout/footer.php");
				}else{
					$data['titulo'] = 'Cliente desconocido';
					$data['mensaje'] = 'No se encontraron datos del cliente';
					$this->load->view("Layout/header.php");
					$this->load->view("Error/no_found.php", $data);
					$this->load->view("Layout/footer.php");
				}
			}else{
				$data['titulo'] = 'Cliente desconocido';
				$data['mensaje'] = 'No se encontraron datos para el seguimiento del cliente';
				$this->load->view("Layout/header.php");
				$this->load->view("Error/no_found.php", $data);
				$this->load->view("Layout/footer.php");
			}
		}


		// Process
		function registrar(){
			$resp = [];
			$permiso = $this->PermisoModel->verificar_permiso(
				$this->session->userdata('pres_ses_usuario'),
				'cliente_registrar'
			);
			if ($permiso && $permiso->codigo == 'P') {
				$valida = $this->validar_registro($_POST);
				if (count($valida) == 0) {
					try {
						$this->db->trans_begin();
						$data = array(
							'cli_documento' => $_POST['documento'],
							'cli_tipo_documento' => $_POST['tipo_documento'],
							'cli_nombres' => mb_strtoupper($_POST['nombre']),
							'cli_paterno' => mb_strtoupper($_POST['paterno']),
							'cli_materno' => mb_strtoupper($_POST['materno']),
							'cli_ubigeo' => $_POST['ubigeo'],
							'cli_direccion' => mb_strtoupper($_POST['direccion']),
							'cli_telefono' => $_POST['telefono'],
							'cli_email' => $_POST['email'],
							'cli_fecha' => date("Y-m-d")
						);
						$this->ClienteModel->Insertar($data);
						$id = $this->db->insert_id();

						if (isset($_FILES['foto']['name'])) {
							$dir = "uploads/clientes/";
							$ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);

							$nombre_foto = $id."__".$_POST['documento'].".".$ext;
							$archivo = $dir.$nombre_foto;
							if (move_uploaded_file($_FILES['foto']['tmp_name'], $archivo)) {
								$this->ClienteModel->Modificar($id, array('cli_imagen' => $archivo));
							}
						}
						$this->db->trans_commit();
						$resp['status'] = 'OK';
						$resp['message'] = 'Cliente registrado';
						$resp['id'] = intval($id);
						$resp['error'] = $valida;
					} catch (PDOException $e) {
						$this->db->trans_rollback();
						die($e->getMessage());	
					}
				}else{
					$resp = array(
						'status' => 'ERROR', 
						'message' => 'Errores encontrados', 
						'error' => $valida
					);
				}
			}else{
				$resp = array(
					'status' => 'WARN', 
					'message' => 'Permisos insuficientes', 
					'error' => []
				);
			}
			echo json_encode($resp);
		}
		function eliminar(){
			$post = $this->input->post();
			$usuario_id = $this->session->userdata('pres_ses_usuario');
			$resp = [];
			$permiso = $this->PermisoModel->verificar_permiso($usuario_id, 'cliente_eliminar');
			if ( $permiso && $permiso->codigo == 'P' ) {
				$del = $this->ClienteModel->elimina($post['id']);
				if ($del) {
					$resp = array(
						'code' => $del->codigo,
						'message' => $del->mensaje
					);
				}else{
					$resp = array(
						'code' => 'Error',
						'message' => 'No se pudo completar la solicitud'
					);
				}
			}else{
				$resp = array(
					'code' => 'Warn',
					'message' => 'Permisos insuficientes'
				);
			}
			echo json_encode($resp);
		}
		function modificar(){
			$post = $this->input->post();
			$resp = [];
			$permiso = $this->PermisoModel->verificar_permiso(
				$this->session->userdata('pres_ses_usuario'),
				'cliente_editar'
			);
			if ( $permiso && $permiso->codigo == 'P' ) {
				$validacion = $this->validar_modificar($post);

				if ( count($validacion) == 0 ) {
						$send = array(
							'cli_documento' => $_POST['documento'],
							'cli_tipo_documento' => $_POST['tipo_documento'],
							'cli_nombres' => mb_strtoupper($_POST['nombre']),
							'cli_paterno' => mb_strtoupper($_POST['paterno']),
							'cli_materno' => mb_strtoupper($_POST['materno']),
							'cli_ubigeo' => $_POST['ubigeo'],
							'cli_direccion' => mb_strtoupper($_POST['direccion']),
							'cli_telefono' => $_POST['telefono'],
							'cli_email' => $_POST['email']
						);
						$this->ClienteModel->Modificar($post['id'], $send);
						$rows = $this->db->affected_rows();
						$msg = ($rows > 0) ? 'Cambios aplicados' : 'Sin cambios';
						$resp = array(
							'status' => 'OK', 
							'message' => $msg, 
							'error' => $validacion
						);
				}else{
					$resp = array(
						'status' => 'ERROR', 
						'message' => 'Errores encontrados', 
						'error' => $validacion
					);
				}
			}else{ 
				$resp = array('status' => 'WARN', 'message' => 'Permisos insuficientes', 'error' => []);
			}
			echo json_encode($resp);
		}
		function modificar_imagen($id = null){
			$post = $this->input->post();
			$resp = [];

			if (isset($_FILES['imagen']['name']) && $post['id'] && $post['doc']) {
				$dir = "uploads/clientes/";
				$ext = pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION);
				$nombre_foto = $post['id']."__".$post['doc'].".".$ext;
				$archivo = $dir.$nombre_foto;
				if (move_uploaded_file($_FILES['imagen']['tmp_name'], $archivo)) {
					$this->ClienteModel->Modificar(
						$post['id'],
						array('cli_imagen' => $archivo)
					);
					$resp = array(
						'status' => 'OK',
						'message' => 'Imagen actualizada',
						'url' => $archivo
					);
				}else{
					$resp = array(
						'status' => 'ERROR',
						'message' => 'Error al procesar'
					);
				}
			}else{
				$resp = array(
					'status' => 'WARN',
					'message' => 'Datos no definidos'
				);
			}
			echo json_encode($resp);
		}
		function validar_registro($data){

			$errors = [];
			if ( empty($data['documento']) ) {
				$errors['documento'] = 'El documento es requerido';
			}

			if ( empty($data['tipo_documento']) ) {
				$errors['tipo_documento'] = 'El tipo de documento es requerido';
			}

			if ( empty($data['nombre']) ) {
				$errors['nombre'] = 'El nombre es requerido';
			}

			if ( empty($data['paterno']) ) {
				$errors['paterno'] = 'El apellido paterno es requerido';
			}else{
				if (!preg_match('/^[a-zA-Z\s]+/', $data['paterno'])) {
					$errors['paterno'] = 'Ingrese solo letras';		
				}
			}

			if ( empty($data['materno']) ) {
				$errors['materno'] = 'El apellido materno es requerido';
			}else{
				if (!preg_match('/^[a-zA-Z\s]+/', $data['materno'])) {
					$errors['materno'] = 'Ingrese solo letras';		
				}
			}

			if ( empty($data['telefono']) ) {
				$errors['telefono'] = 'Debes ingresar al menos 1 numero telefonico->'.$data['telefono'];
			}

			if ( empty($data['direccion']) ) {
				$errors['direccion'] = 'La direccion es requerida';
			}

			// if ( empty($data['email']) ) {
			// 	$errors['email'] = 'El email es requerido';
			// }

			if ( empty($data['ubigeo']) ) {
				$errors['ubigeo'] = 'La ciudad es requerida';
			}

			if ( count($this->ClienteModel->Buscar_dni_v1($data['documento'],$data['tipo_documento'])) > 0 ) {
				$errors['documento'] = 'Ya existe un cliente con el documento.';
			}

			return $errors;
		}
		function validar_modificar($data){

			$errors = [];
			if ( empty($data['id']) ) {
				$errors['id'] = 'Cliente desconocido';
			}

			if ( empty($data['documento']) ) {
				$errors['documento'] = 'El documento es requerido';
			}

			if ( empty($data['tipo_documento']) ) {
				$errors['tipo_documento'] = 'El tipo de documento es requerido';
			}

			if ( empty($data['nombre']) ) {
				$errors['nombre'] = 'El nombre es requerido';
			}

			if ( empty($data['paterno']) ) {
				$errors['paterno'] = 'El apellido paterno es requerido';
			}else{
				if (!preg_match('/^[a-zA-Z\s]+/', $data['paterno'])) {
					$errors['paterno'] = 'Ingrese solo letras';		
				}
			}

			if ( empty($data['materno']) ) {
				$errors['materno'] = 'El apellido materno es requerido';
			}else{
				if (!preg_match('/^[a-zA-Z\s]+/', $data['materno'])) {
					$errors['materno'] = 'Ingrese solo letras';		
				}
			}

			if ( empty($data['telefono']) ) {
				$errors['telefono'] = 'Debes ingresar al menos 1 numero telefonico';
			}

			if ( empty($data['direccion']) ) {
				$errors['direccion'] = 'La direccion es requerida';
			}

			if ( empty($data['ubigeo']) ) {
				$errors['ubigeo'] = 'La ciudad es requerida';
			}

			if ( count($this->ClienteModel->Buscar_dni_v2($data['documento'],$data['tipo_documento'],$data['id'])) > 0 ) {
				$errors['documento'] = 'ya existe un cliente con el nuevo documento.';
			}

			return $errors;
		}


		// Services
		function consulta(){
			$data = $this->ClienteModel->Lista($_POST['tipo'], $_POST['buscar'], $_POST['limite']);
			echo json_encode($data);
		}

		function consulta_prendas(){
			$res = $this->ClienteModel->Buscar_prendas($_POST['id'], $_POST['tipo']);
			echo json_encode($res);
		}

		function consulta_creditos(){
			$res = $this->ClienteModel->Buscar_creditos($_POST['id'], $_POST['orden'], $_POST['limite']);
			echo json_encode($res);
		}

		function ServBuscar(){
			try {
				if(!$_POST) throw new Exception("Error al obtener solicitud");
				$data = $this->ClienteModel->buscar_cliente($_POST['tipo'], $_POST['buscar'], intval($_POST['limite']));
				echo json_encode([
					'status' => 'OK',
					'mssg' => 'Consulta procesada',
					'result' => $data
				]);

			} catch (Exception $e) {
				echo json_encode(['status' => 'ERROR', 'mssg' => $e->getMessage()]);
			}
		}

		// NEWCODE: 18-11-2020
		function ServiceConsulta(){
			// ini_set('display_errors', 0);
			$file = file_get_contents("php://input");
			$json = json_decode($file);
			$data_res = [];

			switch ($json->filtro) {
				case 'nombre_apellido':
					$data_res = $this->ClienteModel->SelectByNombreApellido($json->buscar, $json->limite);
					break;
				case 'documento':
					$data_res = $this->ClienteModel->SelectByDocumento($json->buscar, $json->limite);
					break;
				case 'distrito':
					$data_res = $this->ClienteModel->SelectByDistrito($json->buscar, $json->limite);
					break;
			}
			echo json_encode($data_res);			
		}

		// function ServiceConducta(){
		// 	$file = file_get_contents("php://input");
		// 	$json = json_decode($file);
		// 	$conducta = $this->ClasificacionModel->Buscar(intval($json->id));
		// 	$alerta = $this->ClienteModel->GetAlerta(intval($json->id));
		// 	echo json_encode(array(
		// 		'conducta' => $conducta,
		// 		'alerta' => $alerta
		// 	));
		// }

		// NEWCODE: 15-01-2023

		function get_data_port_v1(){
			$req = json_decode(file_get_contents("php://input")); 
			$resp = [];
			if (!empty($req->id)) {
				$this->load->model('ConductaModel');
				$data = $this->ClienteModel->fast_get_data($req->id);
				$conducta = $this->ConductaModel->get_data($req->id);
				$resp = [
					'status' => 'OK',
					'message' => '',
					'data' => $data,
					'conducta' => $conducta
				];
			}else {
				$resp = [
					'status' => 'WARN',
					'message' => 'Datos de busqueda no definidos.'
				];
			}
			echo json_encode($resp);
		}

		function get_data_port(){
			$req = json_decode(file_get_contents("php://input"));
			if (!$req->id) die();
			$data = $this->ClienteModel->get_data($req->id);
			echo json_encode($data);
		}



		
		// recode 01-2023
		function service_get_data(){
			$file = json_decode(file_get_contents('php://input'));
			$data = $this->ClienteModel->get_data($file->id);
			echo json_encode($data);
		}
		function service_get_search_all_data(){
			$file = json_decode(file_get_contents('php://input'));
			$data = $this->ClienteModel->get_data_all_search($file->search_value);
			echo json_encode($data);
		}
		function service_update_data(){
			$data = [
				'cli_documento' => $_POST['documento'],
				'cli_tipo_documento' => $_POST['tipo_documento'],
				'cli_nombres' => mb_strtoupper($_POST['nombre']),
				'cli_paterno' => mb_strtoupper($_POST['paterno']),
				'cli_materno' => mb_strtoupper($_POST['materno']),
				'cli_ubigeo' => $_POST['ubigeo'],
				'cli_direccion' => mb_strtoupper($_POST['direccion']),
				'cli_telefono' => $_POST['telefono'],
				'cli_email' => $_POST['email']
			];
			$upd = $this->ClienteModel->update_data($_POST['id'],$data);
			$chgs = $this->db->affected_rows();
			echo json_encode([
				'status' => ($chgs > 0) ? 'OK' : 'WARN',
				'message' => ($chgs > 0) ? 'Cambios Aplicados' : 'Sin Cambios'
			]);
		}





		// Trash
		// function registro_form(){
		// 	$permiso = $this->PermisoModel->verificar_permiso(
		// 		$this->session->userdata('pres_ses_usuario'),
		// 		'cliente_registrar'
		// 	);
		// 	$data['tipo_doc'] = $this->TipoDocumentoModel->consulta('rest');
		// 	$this->load->view("Layout/dashboard_header.php");
		// 	$this->load->view("Cliente/nregistro.php", $data);
		// 	$this->load->view("Layout/dashboard_footer.php");
		// }
	}
?>