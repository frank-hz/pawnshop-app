<?php  

	class Empleado extends CI_Controller
	{
		
		function __construct(){
			parent::__construct();
			if (!$this->session->userdata('pres_ses_sesion')) {
				redirect(base_url().'Acceso','refresh');
			}
			$this->load->model('PermisoModel');
			$this->load->model('EmpleadoModel');
			$this->load->model('TipoDocumentoModel');
			$this->load->model('SucursalModel');
			$this->load->model('UsuarioModel');
			$this->load->model('api/EmpleadoAPIModel');		
				
			
		}

		public function index(){
			$this->load->view("Layout/header.php");
			$this->load->view("Empleado/lista.php");
			$this->load->view("Layout/footer.php");
		}

		public function consulta(){
			$sucursal = $this->session->userdata('pres_ses_sucursal');
			$res = $this->EmpleadoModel->Listar(
				$_POST['tipo'], $_POST['buscar'], $_POST['limite'], $sucursal
			);
			// $res = $this->EmpleadoModel->get_data_all();
			echo json_encode($res);
		}		

		public function nuevo(){
			$permiso = $this->PermisoModel->verificar_permiso(
				$this->session->userdata('pres_ses_usuario'),
				'empleado_registrar'
			);
			if ( $permiso && $permiso->codigo == 'P' ) {
				$data['tipo_doc'] = $this->TipoDocumentoModel->consulta('rest');
				$this->load->view("Layout/header.php");
				$this->load->view("Empleado/nuevo.php", $data);
				$this->load->view("Layout/footer.php");
			}else{
				$data['titulo'] = 'Permisos insuficientes';
				$data['mensaje'] = 'El usuario actual no tiene los privilegios suficientes para registrar empleados.';
				$this->load->view("Layout/header.php");
				$this->load->view("Error/no_access.php", $data);
				$this->load->view("Layout/footer.php");
			}
		}

		public function detalle($id = null){
			if ($id != null) {
				// $data['empleado'] = $this->EmpleadoModel->Buscar_detalle($id);
				$data['empleado'] = $this->EmpleadoModel->get_data($id);
				if (!empty($data['empleado'])) {
					$this->load->view("Layout/header.php");
					$this->load->view("Empleado/detalle.php", $data);
					$this->load->view("Layout/footer.php");
				}else{
					$data['titulo'] = 'Empleado desconocido';
					$data['mensaje'] = 'No se encontraron datos del empleado';
					$this->load->view("Layout/header.php");
					$this->load->view("Error/no_found.php", $data);
					$this->load->view("Layout/footer.php");
				}
			}else{
				$data['titulo'] = 'Empleado desconocido';
				$data['mensaje'] = 'No se encontraron datos para el seguimiento del empleado';
				$this->load->view("Layout/header.php");
				$this->load->view("Error/no_found.php", $data);
				$this->load->view("Layout/footer.php");
			}
		}

		public function editar($id = null){
			$permiso = $this->PermisoModel->verificar_permiso(
				$this->session->userdata('pres_ses_usuario'),
				'empleado_editar'
			);
			if ( $permiso && $permiso->codigo == 'P' ) {

				if ($id != null) {
					$data['tipo_doc'] = $this->TipoDocumentoModel->consulta('rest');
					// $data['empleado'] = $this->EmpleadoModel->Buscar_detalle($id);
					$data['empleado'] = $this->EmpleadoModel->get_data($id);
					$this->load->view("Layout/header.php");
					$this->load->view("Empleado/edicion.php", $data);
					$this->load->view("Layout/footer.php");
				}else{
					$data['titulo'] = 'Empleado desconocido';
					$data['mensaje'] = 'No se encontraron datos para el seguimiento del empleado';
					$this->load->view("Layout/header.php");
					$this->load->view("Error/no_found.php", $data);
					$this->load->view("Layout/footer.php");
				}
			}else{
				$data['titulo'] = 'Permisos insuficientes';
				$data['mensaje'] = 'El usuario actual no tiene los privilegios suficientes para editar los datos del empleado.';
				$this->load->view("Layout/header.php");
				$this->load->view("Error/no_access.php", $data);
				$this->load->view("Layout/footer.php");
			}
		}

		public function registrar(){
			$post = $this->input->post();
			$resp = [];
			$permiso = $this->PermisoModel->verificar_permiso(
				$this->session->userdata('pres_ses_usuario'),
				'empleado_registro'
			);
			if ( $permiso && $permiso->codigo == 'P' ) {
				$valida = $this->validar_registro($post);
				if ( count($valida) == 0 ) {

					try {
						$this->db->trans_begin();
						$datasend = array(
							'emp_documento' => $post['documento'],
							'emp_tipo_documento' => $post['tipo_documento'],
							'emp_nombres' => strtoupper($post['nombre']),
							'emp_paterno' => strtoupper($post['paterno']),
							'emp_materno' => strtoupper($post['materno']),
							'emp_ubigeo' => $post['ubigeo'],
							'emp_direccion' => strtoupper($post['direccion']),
							'emp_telefono' => $post['telefono'],
							'emp_email' => $post['email'],
							'emp_sucursal' => $post['sucursal'],
							'emp_creacion' => date("Y-m-d")
						);
						$this->EmpleadoModel->Insertar($datasend);
						$id = $this->db->insert_id();
						$letra = substr($post['nombre'], 0, 1);
						$gen_usuario = $letra.$post['paterno'].$post['materno'];

						$data_user = $this->UsuarioModel->Insertar(array(
							'usu_usuario' => strtolower($gen_usuario), 
							'usu_empleado' => $id,
							'usu_clave' => $post['documento'],
							'usu_creacion' => date('Y-m-d'),
							'usu_rol' => 2,
							'usu_estado' => 'A'
						));
						$resp = array(
							'status' => 'OK', 
							'message' => 'Empleado registrado', 
							'error' => $valida,
							'id' => $id
						);
						$this->db->trans_commit();
					} catch (PDOException $e) {
						$this->db->trans_rollback();
						die(json_encode(
							array('status' => 'ERROR', 'message' => $e->getMessage())
						));	
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

		public function eliminar(){
			$post = $this->input->post();
			$resp = [];
			$permiso = $this->PermisoModel->verificar_permiso(
				$this->session->userdata('pres_ses_usuario'),
				'cliente_eliminar'
			);

			if ( $permiso && $permiso->codigo == 'P' ) {

				$del = $this->ClienteModel->elimina($post['id']);
				if ($del) {
					$resp['code'] = $del->codigo;
					$resp['message'] = $del->mensaje;
				}else{
					$resp['code'] = 'Error';
					$resp['message'] = 'No se pudo completar la solicitud.';
				}	

			}else{
				$resp['code'] = 'Warn';
				$resp['message'] = 'Permisos insuficientes';
			}
			echo json_encode($resp);
		}

		public function modificar(){
			$post = $this->input->post();
			$resp = [];
			$permiso = $this->PermisoModel->verificar_permiso(
				$this->session->userdata('pres_ses_usuario'),
				'empleado_editar'
			);
			if ( $permiso && $permiso->codigo == 'P' ) {
				$valida = $this->validar_modificacion($post);
				if ( count($valida) == 0 ) {
					$data_modifica = array(
						'emp_documento' => $post['documento'],
						'emp_tipo_documento' => $post['tipo_documento'],
						'emp_nombres' => $post['nombre'],
						'emp_paterno' => $post['paterno'],
						'emp_materno' => $post['materno'],
						'emp_ubigeo' => $post['ubigeo'],
						'emp_direccion' => $post['direccion'],
						'emp_telefono' => $post['telefono'],
						'emp_email' => $post['email'],
						'emp_sucursal' => $post['sucursal']
					);

					$update = $this->EmpleadoModel->Modificar($_POST['codigo'], $data_modifica);
					$msg = ($this->db->affected_rows() > 0) ? 'Cambios aplicado' : 'Sin cambios';
					$resp = array('status' => 'OK', 'message' => $msg);
				}else{
					$resp = array('status' => 'ERROR', 'message' => 'Errores encontrados', 'error' => $valida);
				}
			}else{
				$resp = array('status' => 'WARN', 'message' => 'Permisos insuficientes', 'error' => []); 
			}
			echo json_encode($resp);
		}

		public function modificar_imagen($id = null){
			$post = $this->input->post();
			$resp = [];

			if (isset($_FILES['foto']['name']) && $post['codigo'] && $post['doc']) {
				$dir = "uploads/clientes/";
				$ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
				$nombre_foto = $post['codigo']."__".$post['doc'].".".$ext;
				$archivo = $dir.$nombre_foto;
				if (move_uploaded_file($_FILES['foto']['tmp_name'], $archivo)) {
					$this->ClienteModel->actualiza_foto(
						$post['codigo'],
						$archivo
					);
					$resp['code'] = 'Ok';
					$resp['message'] = 'Foto actualizada';
				}else{
					$resp['code'] = 'Error';
					$resp['message'] = 'Actualizacion incompleta';
				}
			}else{
				$resp['code'] = 'Error';
				$resp['message'] = 'Datos no especificados';
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
				$errors['telefono'] = 'Debes ingresar al menos 1 numero telefonico';
			}

			if ( empty($data['direccion']) ) {
				$errors['direccion'] = 'La direccion es requerida';
			}

			if ( empty($data['ubigeo']) ) {
				$errors['ubigeo'] = 'La ciudad es requerida';
			}

			if ( empty($data['sucursal']) ) {
				$errors['sucursal'] = 'La sucursal es requerida';
			}

			if ( count($this->EmpleadoModel->ins_verificarDocumento($data['documento'],$data['tipo_documento'])) > 0 ) {
				$errors['documento'] = 'Ya existe un empleado con el documento.';
			}

			return $errors;
		}

		function validar_modificacion($data){

			$errors = [];
			if ( empty($data['codigo']) ) {
				$errors['codigo'] = 'Empleado desconocido';
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

			if ( empty($data['sucursal']) ) {
				$errors['sucursal'] = 'La sucursal es requerida';
			}

			if ( count($this->EmpleadoModel->upd_verificarDocumento($data['documento'],$data['tipo_documento'],$data['codigo'])) > 0 ) {
				$errors['documento'] = 'ya existe un empleado con el nuevo documento.';
			}

			return $errors;
		}

		// encode 10-2023
		function service_get_all_by_sucursl(){
			$file = json_decode(file_get_contents('php://input'));
			$data = $this->EmpleadoModel->get_data_all_by_sucursal($file->id);
			echo json_encode($data);
		}

	}
?>
