<?php  
	
	class NotaCredito extends CI_Controller
	{
		function __construct(){
			parent::__construct();
			if (!$this->session->userdata('pres_ses_sesion')) {
				redirect(base_url().'Acceso','refresh');
			}
			$this->load->model('NotaCreditoModel');
			$this->load->model('PermisoModel');
			$this->load->helper('credito_nota_util');
		}

		// CODIGO OPTIMIZADO 
		function registro(){
			$resp = [];
			if (isset($_POST['credito']) && isset($_POST['descripcion'])) {
				$data = array(
					'ncred_credito' => $_POST['credito'],
					'ncred_empleado' => $this->session->userdata('pres_ses_empleado'),
					'ncred_descripcion' => $_POST['descripcion'],
					'ncred_fecha' => date("Y-m-d"),
					'ncred_anclado' => $_POST['anclado']
				);
				$this->NotaCreditoModel->insertar($data);
				$rows = $this->db->affected_rows();
				if ($rows > 0) {
					$resp['status'] = 'OK';
					$resp['message'] = 'Nota agregada';
				}else{
					$resp['status'] = 'ERROR';
					$resp['message'] = 'No se pudo agregar la nota.';
				}
			}else{
				$resp['status'] = 'WARN';
				$resp['message'] = 'Datos incompletos';
				$resp['error'] = [];
			}
			echo json_encode($resp);
		}
		function lista(){
			$data = [];
			if ($_POST && $_POST['id']) {
				$data = $this->NotaCreditoModel->Listar($_POST['id']);
			}
			echo json_encode($data);
		}
		function detalle(){
			$data = [];
			if ($_POST && $_POST['id']) {
				$data = $this->NotaCreditoModel->Detalle($_POST['id']);
			}
			echo json_encode($data);
		}
		function eliminar(){
			$post = $this->input->post();
			$resp = [];
			$permiso = $this->PermisoModel->verificar_permiso(
				$this->session->userdata('pres_ses_usuario'),
				'cliente_editar'
			);
			if ($permiso && $permiso->codigo == 'P') {
				$this->NotaCreditoModel->eliminar($post['id']);
				$rows = $this->db->affected_rows();
				if ($rows > 0) {
					$resp['status'] = 'OK';
					$resp['message'] = 'Nota eliminada';
				}else{
					$resp['status'] = 'ERROR';
					$resp['message'] = 'No se pudo eliminar la nota.';
				}
			}else{
				$resp['status'] = 'WARN';
				$resp['message'] = 'Permisos insuficientes';
			}
			echo json_encode($resp);
		}
		function modificar(){
			try {
				if (!$_POST) { throw new Exception("Datos vacios"); }
			  	$resp = [];
				$this->NotaCreditoModel->Modificar($_POST['id'], [
			  		'ncred_descripcion' => $_POST['descripcion'],
			  		'ncred_anclado' => strtoupper($_POST['anclado'])
			  	]);
				$aff = ($this->db->affected_rows() > 0) ? 'Datos Actualizados' : 'Sin Cambios';
				$resp = ['status' => 'OK', 'message' => $aff];
				echo json_encode($resp);	
			} catch (Exception $e) {
				echo json_encode([
					'status' => 'ERROR',
					'message' => $e->getMessage()
				]);
			}
		}
		function ServiceConsulta(){
			$file = file_get_contents("php://input");
			$d_ent = json_decode($file); // Datos de Entrada
			// $resp = ($d_ent->id) ? $this->NotaCreditoModel->Listar($d_ent->id) : [];
			$resp = $this->NotaCreditoModel->Listar($d_ent->id);
			echo json_encode($resp);
		}
		


		// Update 17-07-22
		function service_get_all(){
			$file = file_get_contents('php://input');
			$data_in = json_decode($file);
			$data_get = $this->NotaCreditoModel->getAll($data_in->id);
			
			echo json_encode($data_get);	
		}
		function service_get_detalle(){
			$file = file_get_contents('php://input');
			$data_in = json_decode($file);
			$data_get = $this->NotaCreditoModel->Detalle($data_in->id);
			
			echo json_encode($data_get);	
		}
		function service_update(){
			$file = file_get_contents('php://input');
			$data_in = json_decode($file);
			$this->NotaCreditoModel->Modificar($data_in->id, [
				'ncred_descripcion' => $data_in->descripcion,
				'ncred_anclado' => $data_in->anclado
			]);
			if ($this->db->affected_rows() > 0) {
				echo json_encode([
					'status' => 'OK',
					'message' => 'Cambios Guardados.'
				]);
			} else {
				echo json_encode([
					'status' => 'ERROR',
					'message' => 'Los cambios no se aplicaron.'
				]);
			}
		}
		function service_insert(){
			$file = file_get_contents('php://input');
			$data_in = json_decode($file);
			$ins = $this->NotaCreditoModel->insertar([
				'ncred_credito' => $data_in->credito,
				'ncred_descripcion' => $data_in->descripcion,
				'ncred_anclado' => $data_in->anclado,
				'ncred_empleado' => $this->session->userdata('pres_ses_empleado'),
				'ncred_fecha' => date("Y-m-d")
			]);
			if ($ins) {
				echo json_encode([
					'status' => 'OK',
					'message' => 'Datos Guardados.'
				]);
			} else {
				echo json_encode([
					'status' => 'ERROR',
					'message' => 'El proceso de registro no se completo.'
				]);
			}
		}
		function service_delete(){
			$file = file_get_contents('php://input');
			$data_in = json_decode($file);
			$del = $this->NotaCreditoModel->delete($data_in->id);
			if ($del) {
				echo json_encode([
					'status' => 'OK',
					'message' => 'Nota eliminada con exito.'
				]);
			} else {
				echo json_encode([
					'status' => 'ERROR',
					'message' => 'El proceso de eliminacion no se completo.'
				]);
			}
		}
		// [Update] 15-10-2022
		function service_registro(){
			$file = file_get_contents("php://input");
			$data_in = json_decode($file);
			$save = $this->NotaCreditoModel->insertar([
				'ncred_credito' => $data_in->credito,
				'ncred_descripcion' => $data_in->descripcion,
				'ncred_anclado' => $data_in->tipo,
				'ncred_empleado' => $this->session->userdata('pres_ses_empleado'),
				'ncred_fecha' => date("Y-m-d")
			]);
			echo json_encode([
				'code' => ($save) ? 'OK' : 'WARN',
				'message' => ($save) ? 'Nota guardada' : 'Error al guardar'
			]);
		}




		// Rework 05-2023
		function get_by_credito(){
			try {
				if (!$this->session->userdata('pres_ses_sesion')) {
					die(json_encode([
						'warning' => 'Sesion no encontrada'
					]));
				} 
				if (empty($_POST['idcredito'])){
					die(json_encode([
						'warning' => 'Credito desconocido'
					]));
				}
				$setting = nota_creditou_data_search_setting();
				$data = $this->NotaCreditoModel->getByCredito(
					$_POST['idcredito'],
					$setting
				);
				echo json_encode([
					'success' => 'proceso completado',
					'result' => $data 
				]);
			} catch (Exception $e) {
				$code = $e->getCode();
				$mssg = $e->getMessage();
				die(json_encode([
					'error' => ($code==99) ? $mssg : 'Error al procesar',
					'log' => $e->getMessage()
				]));
			}
        }     
        function get_data(){
            $data = $this->NotaCreditoModel->getData($_POST['id']);
            echo json_encode($data);
        }
        function save_data(){
            try {
				$this->form_validation->set_rules(notacr_insert_validation());
            	if (!$this->form_validation->run()) throw new Exception("Datos incompletos o invalidos");
				$data = [
					'ncred_credito' => intval($_POST['credito']),
					'ncred_descripcion' => strtolower($_POST['descripcion']),
					'ncred_anclado' => $_POST['anclado'],
					'ncred_fecha' => date("Y-m-d"),
					'ncred_create_at' => date("Y-m-d"),
					'ncred_update_at' => date("Y-m-d"),
					'ncred_empleado' => $this->session->userdata('pres_ses_empleado')
				];
				// die(json_encode($data));
				$ins = $this->NotaCreditoModel->insert($data);
				if (!$ins)throw new Exception("Registro no completado");
				echo json_encode([
					'code' => 'OK',
					'message' => 'Datos registrados',
					'id' => $this->db->insert_id()
				]);

			} catch (Exception $e) {
				echo json_encode([
					'code' => 'ERR',
					'message' => 'Error al procesar',
					'log' => $e->getMessage()
				]);
			}
        }		
		function update_data(){
			try {
				$this->form_validation->set_rules(notacr_update_validation());
				if (!$this->form_validation->run()) throw new Exception("Datos incompletos o invalidos");

				$this->NotaCreditoModel->update($_POST['id'], [
					'ncred_descripcion' => strtolower($_POST['descripcion']),
					'ncred_anclado' => $_POST['anclado'],
					'ncred_update_at' => date("Y-m-d")
				]);
				if ($this->db->affected_rows() > 0) {
					echo json_encode([
						'code' => 'OK',
						'message' => 'Cambios Guardados.'
					]);
				} else {
					echo json_encode([
						'code' => 'ERROR',
						'message' => 'Los cambios no se aplicaron.'
					]);
				}
			} catch (Exception $e) {
				echo json_encode([
					'code' => 'ERR',
					'message' => 'Error al procesar',
					'log' => $e->getMessage()
				]);
			}
		}
		function delete_data(){
			try {
				if (!$_POST['id']) throw new Exception('Nota no identificada');
				$del = $this->NotaCreditoModel->delete($_POST['id']);
				echo json_encode([
					'code' => ($del) ? 'OK' : 'WARN',
					'message' => ($del) ? 'Nota Eliminada' : 'El proceso no se completo'
				]);
			} catch (Exception $e) {
				echo json_encode([
					'code' => 'ERR',
					'message' => 'Error al procesar',
					'log' => $e->getMessage()
				]);
			}
			
		}




	}
?>