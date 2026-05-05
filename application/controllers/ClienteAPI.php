<?php
    class ClienteAPI extends CI_Controller
    {
        function __construct(){
			parent::__construct();
			if (!$this->session->userdata('pres_ses_sesion')) {
				redirect(base_url().'Acceso','refresh');
			}
			$this->load->model('ClienteModel');
			$this->load->model('ClienteAPIModel');
			$this->load->model('Credito_bzModel');

			// Helpers
			$this->load->helper('cliente_util');
			$this->load->helper('conducta_util');	
			$this->load->helper("mailman");
			$this->config->load('form_validation');			
		}


        // C R U D
		function get_all(){
			try {
				$setting = clienteu_data_search_setting();
				$clientes = $this->ClienteAPIModel->getAll($setting); 
				if (!empty($clientes)){
					foreach ($clientes as $cliente) {
						$cliente->url = base_url().'cliente/detalle/'.$cliente->id;
					}
				}
				echo json_encode([
					'code' => 'OK',
					'result' => $clientes,
					'message' => 'proceso completado.'
				]);
			} catch (Exception $th) {
				$code = $e->getCode();
				$mssg = $e->getMessage();
				echo json_encode([
					'code' => 'ERR',
					'message' => ($code==99) ? $mssg : 'Error al procesar',
					'log' => $e->getMessage()
				]);
			}
		}
		function get_data(){
			try {
				if (empty($_POST['id'])) throw new Exception('Cliente no identificado',99);
				$cliente = $this->ClienteAPIModel->getById($_POST['id']);
				if (!empty($cliente)) {
					$cliente->url = base_url().'cliente/detalle/'.$cliente->id;
				}
				echo json_encode([
					'code' => 'OK',
					'result' => $cliente,
					'message' => 'proceso completado.'
				]);
			} catch (Exception $e) {
				$code = $e->getCode();
				$mssg = $e->getMessage();
				echo json_encode([
					'code' => 'ERR',
					'message' => ($code==99) ? $mssg : 'Error al procesar',
					'log' => $e->getMessage()
				]);
			}
		}
        function save_data(){
            try {
				if (!permission_validate('cliente_registrar')) {
					throw new Exception('Permisos insuficientes',99);
				}
				$this->form_validation->set_rules(
					$this->config->item('cliente_register')
				);
				if ($this->form_validation->run() == FALSE) {
					$errors = $this->form_validation->error_array();
					die(json_encode([
						'warning' => "Datos incompletos o invalidos",
						'errors' => $errors
					]));
				}

				$data = clienteu_prepare_data_insert();
				$ins = $this->ClienteAPIModel->insert($data);	
				if (!$ins || $this->db->insert_id() == 0) {
					throw new Exception('El registro no se completo',99);
				}
				$id = $this->db->insert_id();
				echo json_encode([
					'success' => 'Cliente registrado',
					'ok' => 'Cliente registrado',
					'id' => $id
				]);
			} catch (Exception $e) {
				$code = $e->getCode();
				$mssg = $e->getMessage();
				echo json_encode([
					'error' => ($code==99) ? $mssg : 'Error al procesar',
					'log' => $e->getMessage()
				]);
			}
        }		
		function update_data(){
			try {
				if (empty($_POST['id'])) {
					die(json_encode([
						'warning' => "Cliente no identificado"
					]));
				}
                if (!permission_validate('cliente_editar')) {
					die(json_encode([
						'warning' => "Permisos insuficientes"
					]));
				}

				$this->form_validation->set_rules(clienteu_update_validation());
				if (!$this->form_validation->run()) {
					die(json_encode([
						'warning' => "Datos incompletos o invalidos"
					]));
				} 

                $data = clienteu_prepare_data_update();
                if (empty($data)) {
					die(json_encode([
						'warning' => "Error al procesar los datos"
					]));
				}
				$duplicity = $this->ClienteAPIModel->verifyDuplicityUpdate(
					$_POST['id'],
					$data
				);
				if (!empty($duplicity)) {
					die(json_encode([
						'warning' => "[Duplicidad] Ya existe un cliente con estos datos"
					]));
				}

				$duplicity_doc =$this->ClienteAPIModel->verifyDuplicityDocUpdate(
					$_POST['id'],
					[
						'cli_documento' => $_POST['documento'],
						'cli_tipo_documento' => $_POST['tipo_documento']
					]
				);
				if ($duplicity_doc) {
					die(json_encode([
						'warning' => "Ya existe un cliente con el mismo documento"
					]));
				}

				$this->ClienteAPIModel->update($_POST['id'], $data);
                $affrows = $this->db->affected_rows();
				echo json_encode([
                    'code' => 'OK',
                    'success' => ($affrows > 0) ? 'Cambios Guardados' : 'Sin Cambios'
                ]);
			} catch (Exception $e) {
				$code = $e->getCode();
				$mssg = $e->getMessage();
				echo json_encode([
					'code' => 'ERR',
					'message' => ($code==99) ? $mssg : 'Error al procesar',
					'log' => $e->getMessage()
				]);
			}
		}
		function delete_data(){
			try {
				if (!$_POST['id']) throw new Exception('Cliente no identificado');
                $creditos_asoc = $this->Credito_bzModel->getByCliente($_POST['id']);
                if ($creditos_asoc) throw new Exception('El cliente tiene creditos asociados',99);
				$del = $this->ClienteAPIModel->delete($_POST['id']);
				if (!$del || $this->db->affected_rows() == 0) throw new Exception('El proceso no se completo.',99);

				echo json_encode([
					'code' => 'OK',
					'message' => 'Cliente Eliminado',
					'log' => $this->db->affected_rows()
				]);
			} catch (Exception $e) {
                $code = $e->getCode();
				$mssg = $e->getMessage();
				echo json_encode([
					'code' => 'ERR',
					'message' => ($code==99) ? $mssg : 'Error al procesar',
					'log' => $e->getMessage()
				]);
			}
			
		}


		// C O N D U C T A
		function conducta_get_data(){
            try {
				if (empty($_POST['idcliente'])) throw new Exception('Cliente no identificado',99);
				$conducta = $this->ClienteAPIModel->getConductaById($_POST['idcliente']);
				if (!empty($conducta)) {
					$conducta->bloqueo_motivo = $conducta->bloqueo_motivo;
				}
				echo json_encode([
					'code' => 'OK',
					'result' => $conducta
				]);
			} catch (Exception $e) {
				$code = $e->getCode();
				$mssg = $e->getMessage();
				echo json_encode([
					'code' => 'ERR',
					'message' => ($code==99) ? $mssg : 'Error al procesar',
					'log' => $e->getMessage()
				]);
			}
        }
		function conducta_update_data(){
			try {
				if (empty($_POST['idcliente'])) {
					die(json_encode([
						'code' => 'WARN',
						'warning' => 'Cliente no identificado'
					]));
				}
                if (!permission_validate('cliente_editar')) {
					die(json_encode([
						'code' => 'WARN',
						'warning' => 'Permisos insuficientes'
					]));
				}
                $data = conductau_prepare_data_update();
                if (empty($data)) {
					die(json_encode([
						'code' => 'WARN',
						'warning' => 'Sin datos que actualizar',
						'data' => $data
					]));
				}	

				$this->ClienteAPIModel->update($_POST['idcliente'], $data);
                $affrows = $this->db->affected_rows();
				echo json_encode([
                    'code' => 'OK',
                    'success' => ($affrows > 0) ? 'Cambios Guardados' : 'Sin Cambios',
					'log' => $data
                ]);				
			} catch (Exception $e) {
				$code = $e->getCode();
				$mssg = $e->getMessage();
				echo json_encode([
					'code' => 'ERR',
					'error' => ($code==99) ? $mssg : 'Error de procesamiento',
					'log' => $e->getMessage()
				]);
			}

		}


		// C U S T O M
		function send_email(){
			try {
				$email = $this->input->post("correo_destino") ?? null;
				$asunto = $this->input->post("asunto") ?? null;
				$cuerpo = $this->input->post("cuerpo") ?? null;
				// $pie = $this->input->post("pie") ?? "";
				if ($email === null || $asunto === null || $cuerpo === null){
					throw new Exception("Parametros no recibidos o invalidos.", 99);
				}
				$response = [];
				

				if (MailmanEnviarCorreo($email, $asunto, $cuerpo)){
					$response['ok'] = 'Correo enviado correctamente.';
				}else {
					$response['error'] = 'El proceso de envio no se completo.';
				}
				echo json_encode($response);
			} catch (Exception $e) {
				$code = $e->getCode();
				$mssg = $e->getMessage();
				die(json_encode([
					'error' => ($code==99) ? $mssg : 'ERROR_PROCESS',
					'log' => $e->getMessage()
				]));
			}
		}

    }
    
?>