<?php  
	
	class Prenda extends CI_Controller
	{
		function __construct()
		{
			parent::__construct();
			if (!$this->session->userdata('pres_ses_sesion')) {
				redirect(base_url().'Acceso','refresh');
			}
			$this->load->model('PrendaModel');
			$this->load->model('Prenda_apiModel');
			$this->load->model('CreditoModel');
			$this->load->model('PermisoModel');
			$this->load->model('PropiedadModel');

			// Helpers
			$this->load->helper('prenda_util');
			$this->load->helper('credito_util');
		}

		function index(){
			$this->load->view('Layout/header.php');
			$this->load->view('Prenda/index.php');
			$this->load->view('Layout/footer.php');
		}

		function detallev1($id = null){
			if ($id != null) {
				// $data['prenda'] = $this->PrendaModel->SelectDetalle($id);
				$data['prenda'] = $this->PrendaModel->get_data($id);
				$data['credito'] = $this->PrendaModel->Buscar_creditos($id, 'clear');
				$data['propiedad'] = $this->PrendaModel->Lista_propiedad($id);
				if (!empty($data['prenda'])) {
					$this->load->view('Layout/header.php');
					$this->load->view('Prenda/detalle_trash.php', $data);
					$this->load->view('Layout/footer.php');
				}else{
					$data['titulo'] = 'Prenda desconocida.';
					$data['mensaje'] = 'No se encontraron datos del la prenda.';
					$this->load->view("Layout/header.php");
					$this->load->view("Error/no_found.php", $data);
					$this->load->view("Layout/footer.php");
				}
			}else{
				$data['titulo'] = 'Prenda desconocida';
				$data['mensaje'] = 'No se encontraron datos para el seguimiento de la prenda';
				$this->load->view("Layout/header.php");
				$this->load->view("Error/no_found.php", $data);
				$this->load->view("Layout/footer.php");
			}
		}

		function detalle($id = null){
			$prenda = $this->Prenda_apiModel->getById($id);
			if (empty($prenda)) {
				error_render_view('Datos no encontrados', '');
				return;
			}
			$prenda->url = base_url().'prenda/detalle/'.$prenda->id;
			$prenda->estado_tag = prendau_tag_estado($prenda->estado);
			$data['prenda_data_string'] = prendau_data_to_string($prenda);			
			$data['prenda'] = $prenda;	
			$data['propiedades'] = $this->Prenda_apiModel->getPropiedadesById($id);
			$data['creditos'] = $this->Prenda_apiModel->getCreditosById($id);
			if (!empty($data['creditos'])) {
				foreach ($data['creditos'] as $credito) {
					$credito->url = base_url().'credito/detalle/'.$credito->id;
					$credito->estado_tag = creditou_tag_estado($credito->estado);
				}
			}
			$this->load->view('Layout/header.php');
			$this->load->view('Prenda/detalle.php', $data);
			$this->load->view('Layout/footer.php');
		}





		function consulta(){
			$res = $this->PrendaModel->Lista(
				$_POST['tipo'], 
				$_POST['buscar'],
				$_POST['limite']
			);
			echo json_encode($res);
		}

		function consulta_propiedad(){
			$res = $this->PrendaModel->Lista_propiedad($_POST['id']);
			echo json_encode($res);
		}

		function consulta_creditos(){
			$res = $this->PrendaModel->Lista_propiedad($_POST['id']);
			echo json_encode($res);
		}		

		function registrar(){
			$post = $this->input->post();
			$resp = [];
			$permiso = $this->PermisoModel->verificar_permiso(
				$this->session->userdata('pres_ses_usuario'),
				'prenda_registrar'
			);
			if ($permiso && $permiso->codigo == 'P') {
				$validar = $this->validate_registro($_POST);
				// if (count($validar) === 0) {
					try {
						$this->db->trans_begin();
						// if (empty($_POST['tipo_prenda'])){
						// 	die(json_encode(array(
						// 		'status' => 'ERROR',
						// 		'message' => 'Error al procesar. '.$e->getMessage()
						// 	)));
						// }

						$data = array(
							'pren_cliente' => $_POST['cliente'],
							'pren_descripcion' => isset($_POST['descripcion']) ? strtoupper($_POST['descripcion']):'',
							'pren_categoria' => $_POST['categoria'],
							'pren_serial' => isset($_POST['serial']) ? strtoupper($_POST['serial']) : '',
							'pren_marca' => isset($_POST['marca']) ? strtoupper($_POST['marca']) : '',
							'pren_modelo' => isset($_POST['modelo']) ?  strtoupper($_POST['modelo']) : '',
							'pren_quilate' => isset($_POST['quilate']) ? strtoupper($_POST['quilate']) : '',
							'pren_peso' => isset($_POST['peso']) ? strtoupper($_POST['peso']) : '',

							'pren_kilometraje' => isset($_POST['kilometraje']) ? strtoupper($_POST['kilometraje']) : '',
							'pren_transmision' => isset($_POST['transmision']) ? strtoupper($_POST['transmision']) : '',
							'pren_placa' => isset($_POST['placa']) ? strtoupper($_POST['placa']) : '',
							'pren_anio' => isset($_POST['anio']) ? strtoupper($_POST['anio']) : '',

							'pren_valoracion' => isset($_POST['valoracion']) ? $_POST['valoracion'] : '',
							'pren_observacion' => isset($_POST['observacion']) ? strtoupper($_POST['observacion']):'',
							'pren_comentario' => isset($_POST['comentario'])?strtoupper($_POST['comentario']) : '',
							'pren_fecha_registro' => date("Y-m-d")
						);
						$this->PrendaModel->Insertar($data);
						$id = $this->db->insert_id();
						$propiedad_data = array(
							'prop_empleado' => $this->session->userdata('pres_ses_empleado'),
							'prop_cliente' => $_POST['cliente'],
							'prop_prenda' => $id,
							'prop_inicio' => date("Y-m-d"),
							'prop_motivo' => 'REGISTRO DE PRENDA',
							'prop_estado' => 'A'
						);
						$this->PropiedadModel->Insertar($propiedad_data);
						$resp = array(
							'status' => 'OK',
							'message' => 'Prenda registrada',
							'id' => $id
						);
						$this->db->trans_commit();
					} catch (PDOException $e) {
						$this->db->trans_rollback();
						die(json_encode(array(
							'status' => 'ERROR',
							'message' => 'Error al procesar. '.$e->getMessage()
						)));
					}
				// }else{
				// 	$resp = array(
				// 		'status' => 'WARN',
				// 		'message' => 'Errores encontrados',
				// 		'error' => $validar
				// 	);
				// }
			}else{
				$resp = array(
					'status' => 'WARN',
					'message' => 'Permisos insuficientes'
				);
			}
			echo json_encode($resp);
		}

		function modificar(){
			$post = $_POST;
			$resp = [];
			$permiso = $this->PermisoModel->verificar_permiso(
				$this->session->userdata('pres_ses_usuario'),
				'prenda_editar'
			);
			if ( $permiso && $permiso->codigo == 'P' ) {
				try {
					$datasend = array(
						'pren_descripcion' => strtoupper($post['descripcion']),
						'pren_categoria' => $post['categoria'],
						'pren_valoracion' => $post['valoracion'],
						'pren_observacion' => strtoupper($post['observacion']),
						'pren_comentario' => strtoupper($post['comentario'])
					);
					switch ($post['tipo']) {
						case 'JOYA':
							$datasend['pren_quilate'] = strtoupper($post['quilate']);
							$datasend['pren_peso'] = strtoupper($post['peso']);
							break;
						
						case 'ELECTRO':
							$datasend['pren_serial'] = strtoupper($post['serial']);
							$datasend['pren_marca'] = strtoupper($post['marca']);
							$datasend['pren_modelo'] = strtoupper($post['modelo']);
							break;

						case 'VEHICULO':
							$datasend['pren_anio'] = strtoupper($post['anio']);
							$datasend['pren_placa'] = strtoupper($post['placa']);
							$datasend['pren_modelo'] = strtoupper($post['modelo']);
							$datasend['pren_kilometraje'] = strtoupper($post['kilometraje']);
							$datasend['pren_marca'] = strtoupper($post['marca']);
							break;

						case 'PREDIO':
							# code...
							break;
					}

					$this->PrendaModel->Modificar($post['id'], $datasend);
					$rows = $this->db->affected_rows();
					$msg = ($rows > 0) ? 'Cambios aplicados' : 'Sin cambios';
					$resp = array(
						'status' => 'OK', 
						'message' => $msg
					);
					
				} catch (PDOException $e) {
					die();
				}
			}else{
				$resp = array(
					'status' => 'ERROR', 
					'message' => 'Permisos insuficientes'
				);
			}
			echo json_encode($resp);
		}

		function eliminar(){
			$post = $this->input->post();
			$resp = [];
			$permiso = $this->PermisoModel->verificar_permiso(
				$this->session->userdata('pres_ses_usuario'),
				'prenda_eliminar'
			);
			if ($permiso && $permiso->codigo == 'P' ) {
				try {
					$this->db->trans_begin();
					$credito = $this->CreditoModel->Buscar_libre('count(*) as cantidad', array('cred_prenda' => $post['id']));
					if ($credito->cantidad == 0) {
						$del = $this->PrendaModel->Eliminar($post['id']);
						if ($this->db->affected_rows() > 0) {
							$this->PropiedadModel->Eliminar(array('prop_prenda' => $post['id']));
							$resp = array(
								'status' => 'OK',
								'message' => 'Prenda eliminada'
							);
							$this->db->trans_commit();
						}else{
							die(json_encode(array(
								'status' => 'ERROR',
								'message' => 'No se pudo completar la eliminacion de la prenda'
							)));	
						}
					}else{
						die(json_encode(array(
							'status' => 'ERROR',
							'message' => 'La prenda tiene creditos asociados'
						)));
					}
				} catch (Exception $e) {
					$this->db->trans_rollback();
					die(json_encode(array(
						'status' => 'ERROR',
						'message' => $e->getMessage()
					)));
				}
			}else{
				$resp['status'] = 'WARN';
				$resp['message'] = 'Permisos insuficientes';
			}
			echo json_encode($resp);
		}

		function Transferir($id = null){
			if ($id != null) {
				$permiso = $this->PermisoModel->verificar_permiso(
					$this->session->userdata('pres_ses_usuario'),
					'prenda_transferir'
				);
				if ( $permiso && $permiso->codigo == 'P' ) {
					$data['prenda'] = $this->PrendaModel->Buscar_detalle($id);
					if (!empty($data['prenda'])) {
						$this->load->view('Layout/header.php');
						$this->load->view('Prenda/transferir.php', $data);
						$this->load->view('Layout/footer.php');
					}else{
						$data['titulo'] = 'Prenda desconocida.';
						$data['mensaje'] = 'No se encontraron datos del la prenda.';
						$this->load->view("Layout/header.php");
						$this->load->view("Error/no_found.php", $data);
						$this->load->view("Layout/footer.php");
					}
				}else{
					$data['titulo'] = 'Permisos insuficientes.';
					$data['mensaje'] = 'El usuario actual no tiene permisos para esta accion.';
					$this->load->view("Layout/header.php");
					$this->load->view("Error/no_access.php", $data);
					$this->load->view("Layout/footer.php");
				}
			}else{
				$data['titulo'] = 'Prenda desconocida';
				$data['mensaje'] = 'No se encontraron datos para el seguimiento de la prenda';
				$this->load->view("Layout/header.php");
				$this->load->view("Error/no_found.php", $data);
				$this->load->view("Layout/footer.php");
			}
		}

		function validate_registro($data){
			$error = [];
			if (!isset($data['cliente'])) {
				$error['cliente'] = 'el cliente no esta definido';
			}
			if (!isset($data['descripcion'])) {
				$error['descripcion'] = 'descripcion requerida';
			}
			if (!isset($data['categoria'])) {
				$error['categoria'] = 'categoria requerida';
			}
			if (!empty($data['serial']) && !empty($data['marca'])) {
				$rep = $this->PrendaModel->Buscar_libre('pren_codigo as id', array('pren_serial' => $data['serial'], 'pren_marca' => $data['marca']));
				if (!empty($rep)) {
					$error['repetido'] = $rep->id;
				}
			}
			return $error;
		}




		// NEWCODE: 09-11-2020
		function ServiceConsulta(){
			$json = file_get_contents("php://input");
			$form = json_decode($json);
			$data_resp = [];
			switch ($form->filtro) {
				case 'cliente':
					$data_resp = $this->PrendaModel->SelectByCliente($form->buscar, $form->cantidad);
					break;
				case 'tipo':
					$data_resp = $this->PrendaModel->SelectByTipo($form->buscar, $form->cantidad);
					break;
				case 'categoria':
					$data_resp = $this->PrendaModel->SelectByCategoria($form->buscar, $form->cantidad);
					break;
				case 'estado':
					$data_resp = $this->PrendaModel->SelectByEstado($form->buscar, $form->cantidad);
				default:
					$dataResp = $this->PrendaModel->SelectAll();
					break;
			}
			echo json_encode($data_resp);
		}
		
		function ServiceRegistrar(){
			try {
				$error_log = [];
				if (!$_POST) throw new Exception("Datos no recibidos.", 1);
				
				$validacion = $this->ValidarActualizacion($_POST);
				if (count($validacion['data_error']) > 0) {
					$error_log = $validacion['data_error'];
					throw new Exception("Se encontraron errores al validar los datos.", 1);
				} else{
					$this->PrendaModel->Update($_POST['id'], $validacion['data_success']);
					echo json_encode([
						'status' => ($this->db->affected_rows() > 0) ? 'OK' : 'WARN',
						'message' => ($this->db->affected_rows() > 0) ? 'Datos actualizados.' : 'Sin cambios.',
						'error_log' => $validacion['data_error']
					]);
				}

			} catch (Exception $e) {
				echo json_encode([
					'status' => 'ERROR',
					'message' => $e->getMessage(),
					'error_log' => $error_log
				]);
			}
		}

		function ValidarActualizacion($data){
			try {
				$data_error = []; $data_success = [];
				// if($data) throw new Exception('Datos no recibidos.', 1);

				if(!isset($data['descripcion']) || strlen($data['descripcion']) == 0){
					$data_error['descripcion'] = 'La descripcion de la prenda es requerida.';
				}else{ $data_success['pren_descripcion'] = $data['descripcion']; }

				if(!isset($data['categoria']) || strlen($data['categoria']) == 0){
					$data_error['categoria'] = 'La categoria de la prenda es requerida.';
				}else{ $data_success['pren_categoria'] = $data['categoria']; }

				if(!isset($data['tipo']) || strlen($data['tipo']) == 0){
					$data_error['tipo'] = 'El tipo de la prenda es requerido.';
				}
				$data_success['pren_comentario'] = $data['comentario'];
				$data_success['pren_observacion'] = $data['observacion'];

				switch ($data['tipo']) {
					case 'ELECTRO':
						if(!isset($data['serial']) || strlen($data['serial']) == 0){
							$data_error['serial'] = 'La serie de la prenda electro es requerida.';
						}else{ $data_success['pren_serial'] = $data['serial']; }

						if (!isset($data['marca']) || strlen($data['marca']) == 0){
							$data_error['marca'] = 'La marca de la prenda electro es requerida.';
						}else{ $data_success['pren_marca'] = $data['marca']; }

						if (!isset($data['modelo']) || strlen($data['modelo']) == 0){
							$data_error['modelo'] = 'El modelo de la prenda electro es requerido.';
						}else{ $data_success['pren_modelo'] = $data['modelo']; }

						break;						
					case 'JOYA':
						if(!isset($data['quilate']) || strlen($data['quilate']) == 0){
							$data_error['quilate'] = 'El quilate de la joya es requerido.';
						}else{ $data_success['pren_quilate'] = $data['quilate']; }

						if(!isset($data['peso']) || strlen($data['peso']) == 0){
							$data_error['peso'] = 'El peso de la joya es requerido.';
						}else{ $data_success['pren_peso'] = $data['peso']; }

						break;

					case 'VEHICULO':
						if(!isset($data['marca']) || strlen($data['marca']) == 0){
							$data_error['marca'] = 'La marca del vehiculo es requerida.';
						}else{ $data_success['pren_marca'] = $data['marca']; }

						if(!isset($data['modelo']) || strlen($data['modelo']) == 0){
							$data_error['modelo'] = 'El modelo del vehiculo es requerido.';
						}else{ $data_success['pren_modelo'] = $data['modelo']; }

						if(!isset($data['anio']) || strlen($data['anio']) == 0){
							$data_error['anio'] = 'El año del vehiculo es requerido.';
						}else{ $data_success['pren_anio'] = $data['anio']; }

						if(!isset($data['placa']) || strlen($data['placa']) == 0){
							$data_error['placa'] = 'La placa del vehiculo es requerida.';
						}else{ $data_success['pren_placa'] = $data['placa']; }

						if(!isset($data['kilometraje']) || strlen($data['kilometraje']) == 0){
							$data_error['kilometraje'] = 'El kilometraje del vehiculo es requerido.';
						}else{ $data_success['pren_kilometraje'] = $data['kilometraje']; }

						break;
				}			
			} catch (Exception $e) {
				$data_error['validacion'] = $e->getMessage();
			} finally {
				return [
					'data_error' => $data_error,
					'data_success' => $data_success
				];
			}
		}

		function prot_registro(){
			$resp = [];
			$permiso = $this->PermisoModel->verificar_permiso(
				$this->session->userdata('pres_ses_usuario'),
				'prenda_registrar'
			);
			if ($permiso && $permiso->codigo == 'P') {
				try {
					$this->db->trans_begin();
					$val_error = $this->prot_validate_registro($_POST);
					if ($val_error) {
						die(json_encode(
							[
								'status' => 'ERROR',
								'message' => 'Errores encontrados.',
								'error' => $val_error
							]
						));
					}

					$data = array(
						'pren_cliente' => $_POST['cliente'],
						'pren_descripcion' => isset($_POST['descripcion']) ? strtoupper($_POST['descripcion']):'',
						'pren_categoria' => $_POST['categoria'],
						'pren_serial' => isset($_POST['serie']) ? strtoupper($_POST['serie']) : '',
						'pren_marca' => isset($_POST['marca']) ? strtoupper($_POST['marca']) : '',
						'pren_modelo' => isset($_POST['modelo']) ?  strtoupper($_POST['modelo']) : '',
						'pren_quilate' => isset($_POST['quilate']) ? strtoupper($_POST['quilate']) : '',
						'pren_peso' => isset($_POST['peso']) ? strtoupper($_POST['peso']) : '',

						'pren_kilometraje' => isset($_POST['kilometraje']) ? strtoupper($_POST['kilometraje']) : '',
						'pren_transmision' => isset($_POST['transmision']) ? strtoupper($_POST['transmision']) : '',
						'pren_placa' => isset($_POST['placa']) ? strtoupper($_POST['placa']) : '',
						'pren_anio' => isset($_POST['anio']) ? strtoupper($_POST['anio']) : '',

						'pren_valoracion' => isset($_POST['estado']) ? $_POST['estado'] : '',
						'pren_observacion' => isset($_POST['observacion']) ? strtoupper($_POST['observacion']):'',
						'pren_comentario' => isset($_POST['comentario'])?strtoupper($_POST['comentario']) : '',
						'pren_fecha_registro' => date("Y-m-d")
					);
					$this->PrendaModel->Insertar($data);
					$id = $this->db->insert_id();
					$propiedad_data = array(
						'prop_empleado' => $this->session->userdata('pres_ses_empleado'),
						'prop_cliente' => $_POST['cliente'],
						'prop_prenda' => $id,
						'prop_inicio' => date("Y-m-d"),
						'prop_motivo' => 'REGISTRO DE PRENDA',
						'prop_estado' => 'A'
					);
					$this->PropiedadModel->Insertar($propiedad_data);
					$resp = array(
						'status' => 'OK',
						'message' => 'Prenda registrada',
						'id' => $id
					);
					$this->db->trans_commit();
					echo json_encode($resp);
				} catch (Exception $e) {
					$this->db->trans_rollback();
					die(json_encode(
						array(
							'status' => 'ERROR',
							'message' => 'No se pudo completar el registro de la prenda.'
						)
					));
				}
			}else{
				echo json_encode(
					array(
						'status' => 'WARN',
						'message' => 'Permisos insuficientes'
					)
				);
			}
		}

		function prot_validate_registro($data){
			$error = [];

			if(strlen($data['descripcion']) <= 0) 
				$error['descripcion'] = 'Campo requerido.';
			if(strlen($data['categoria']) <= 0 || $data['categoria'] <= 0) 
				$error['categoria'] = 'Campo requerido.';
			// if(strlen($data['estado']) <= 0 || $data['estado'] <= 0 || $data['estado'] > 10) 
			// 			$error['estado'] = 'Campo requerido.';
			switch ($data['tipo']) {
				case 'ELECTRO':
					if(strlen($data['serie']) <= 0) 
						$error['serie'] = 'Campo requerido.';
					if(strlen($data['marca']) <= 0) 
						$error['marca'] = 'Campo requerido.';
					if(strlen($data['modelo']) <= 0) 
						$error['modelo'] = 'Campo requerido.';
					break;
				
				case 'JOYA':
					if(strlen($data['quilate']) <= 0) 
						$error['quilate'] = 'Campo requerido.';
					if(strlen($data['peso']) <= 0) 
						$error['peso'] = 'Campo requerido.';
					break;

				case 'VEHICULO':
					if(strlen($data['marca']) <= 0) 
						$error['marca'] = 'Campo requerido.';
					if(strlen($data['modelo']) <= 0) 
						$error['modelo'] = 'Campo requerido.';
					if(strlen($data['anio']) <= 0) 
						$error['anio'] = 'Campo requerido.';
					if(strlen($data['placa']) <= 0) 
						$error['placa'] = 'Campo requerido.';
					if(strlen($data['kilometraje']) <= 0) 
						$error['kilometraje'] = 'Campo requerido.';
					break;
			}

			return $error;
		}
		
		function prot_service_data(){
			$file = file_get_contents('php://input');
			$req_data = json_decode($file);

			$data = Array();
			if ($req_data) {
				switch ($req_data->filtro) {
					case 'cliente':
						$data = $this->PrendaModel->SelectByCliente($req_data->buscar, $req_data->cantidad);
						break;
					
					case 'tipo':
						$data = $this->PrendaModel->SelectByTipo($req_data->buscar, $req_data->cantidad);
						break;

					case 'categoria':
						$data = $this->PrendaModel->SelectByCategoria($req_data->buscar, $req_data->cantidad);
						break;

					case 'estado':
						$data = $this->PrendaModel->SelectByEstado($req_data->buscar, $req_data->cantidad);
						break;

					default:
						$data = $this->PrendaModel->SelectAll($req_data->cantidad);
						break;
				}
			}
			echo json_encode($data);
		}
		

		// Encode: 01-03-2023
		function service_all_get_by_cliente(){
			$data = json_decode(file_get_contents('php://input'));
			$resp = $this->PrendaModel->get_data_by_cliente($data->idcliente);
			echo json_encode($resp);
		}
		function service_insert(){
			// $data = file_get_contents("php://input");
			// $data = json_encode($data);
			try {
				$this->db->trans_begin();

				$pd = $_POST;
				$data_save = [
					'pren_cliente' => $pd['cliente'],
					'pren_descripcion' => strtoupper($pd['descripcion']),
					'pren_categoria' => $pd['categoria'],
					'pren_serial' => strtoupper($pd['serial']),
					'pren_marca' => strtoupper($pd['marca']),
					'pren_modelo' => strtoupper($pd['modelo']),
					'pren_quilate' => $pd['quilate'],
					'pren_peso' => $pd['peso'],
					'pren_anio' => $pd['anio'],
					'pren_placa' => strtoupper($pd['placa']),
					'pren_valoracion' => $pd['valoracion'],
					'pren_kilometraje' => $pd['kilometraje'],
					'pren_transmision' => strtoupper($pd['transmision']),
					'pren_color' => strtoupper($pd['color']),
					'pren_carroceria' => strtoupper($pd['carroceria']),
					'pren_comentario' => strtoupper($pd['comentario']),
					'pren_observacion' => strtoupper($pd['observacion'])
				];
				$this->PrendaModel->Insertar($data_save);
				$id = $this->db->insert_id();
				$propiedad_data = array(
					'prop_empleado' => $this->session->userdata('pres_ses_empleado'),
					'prop_cliente' => $_POST['cliente'],
					'prop_prenda' => $id,
					'prop_inicio' => date("Y-m-d"),
					'prop_motivo' => 'REGISTRO DE PRENDA',
					'prop_estado' => 'A'
				);
				$this->PropiedadModel->Insertar($propiedad_data);
				$resp = array(
					'status' => 'OK',
					'message' => 'Prenda registrada',
					'id' => $id
				);
				$this->db->trans_commit();
				echo json_encode($resp);
				
			} catch (PDOException $pdo) {
				$this->db->trans_rollback();
				die(json_encode(array(
					'status' => 'ERROR',
					'message' => 'Error al procesar. '.$e->getMessage()
				)));
				
			}
			
		}
		function validate_insert($data){
			$error = [];

			if(empty($data['descripcion'])) {
				$error['descripcion'] = 'Campo requerido.';
			}				
			if(empty($data['categoria']) || $data['categoria'] <= 0) {
				$error['categoria'] = 'Campo requerido.';
			}

			return $error;
		}
		function service_get_data(){
			$id = $_POST['id'];
			$data = $this->PrendaModel->get_data($id);
			echo json_encode($data);
		}		
		function service_update(){
			$pd = $_POST;
			$data = [
				'pren_descripcion' => strtoupper($pd['descripcion']),
				'pren_categoria' => $pd['categoria'],
				'pren_serial' => strtoupper($pd['serial']),
				'pren_marca' => strtoupper($pd['marca']),
				'pren_modelo' => strtoupper($pd['modelo']),
				'pren_quilate' => $pd['quilate'],
				'pren_peso' => $pd['peso'],
				'pren_anio' => $pd['anio'],
				'pren_placa' => strtoupper($pd['placa']),
				'pren_valoracion' => $pd['valoracion'],
				'pren_kilometraje' => $pd['kilometraje'],
				'pren_transmision' => strtoupper($pd['transmision']),
				'pren_color' => strtoupper($pd['color']),
				'pren_carroceria' => strtoupper($pd['carroceria']),
				'pren_comentario' => strtoupper($pd['comentario']),
				'pren_observacion' => strtoupper($pd['observacion'])
			];

			$upd = $this->PrendaModel->update_data($data, $pd['idprenda']);
			$chgs = $this->db->affected_rows();
			echo json_encode([
				'status' => ($chgs) ? 'OK' : 'WARN',
				'message' => ($chgs) ? 'Cambios Aplicados' : 'Sin Cambios'
			]);
		}



	}
?>
