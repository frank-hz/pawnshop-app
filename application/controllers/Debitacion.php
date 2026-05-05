<?php  
	
	class Debitacion extends CI_Controller
	{
		
		function __construct(){
			parent::__construct();
			if (!$this->session->userdata('pres_ses_sesion')) {
				redirect(base_url().'Acceso','refresh');
			}
			$this->load->model('DebitacionModel');
			$this->load->model('CreditoModel');
			$this->load->model('PermisoModel');
		}

		function consulta(){
			$res = $this->DebitacionModel->Buscar($_POST['id']);
			echo json_encode($res);
		}

		function guardar(){
			$resp = [];
			$permiso = $this->PermisoModel->verificar_permiso(
				$this->session->userdata('pres_ses_usuario'),
				'credito_debitar'
			);
			if ($permiso && $permiso->codigo == 'P') {
				$valida = $this->validar_guardar($_POST);
				if (count($valida) == 0) {
					$credito = $this->CreditoModel->Buscar_libre(
						'cred_amortizacion as amortiza, cred_estado as estado',
						array('cred_codigo' => $_POST['credito'])
					);
					if ($credito->estado === 'E' && $credito->amortiza > 0) {
						try {
							$this->db->trans_begin();
							$deb_data = array(
								'deb_credito' => $_POST['credito'],
								'deb_fecha' => date('Y-m-d'),
								'deb_monto' => $_POST['monto'],
								'deb_descripcion' => strtoupper($_POST['descripcion']),
								'deb_empleado' => $this->session->userdata('pres_ses_empleado')
							);

							$this->DebitacionModel->Insertar($deb_data);
							$id = $this->db->insert_id();
							$this->CreditoModel->Modificar($_POST['credito'], array(
								'cred_amortizacion' => $_POST['monto']
							));
							if ($this->db->affected_rows() == 0) {
								die(json_encode(array(
									'status' => 'ERROR',
									'message' => 'Error al establecer la debitacion'
								)));
							}
							$resp = array(
								'status' => 'OK', 
								'message' => 'Debitacion aplicada',
								'id' => $id,
								'error' => $valida
							);
							$this->db->trans_commit();
						} catch (PDOException $e) {
							$this->db->trans_rollback();
							die(array(
								'status' => 'ERROR',
								'message' => "Error al procesar. [".$e->getMessage()."]"
							));
						}
					}else{
						$resp = array(
							'status' => 'ERROR', 
							'message' => 'Debitacion no disponible para este credito',
							'error' => $valida
						);
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
					'message' => 'permisos insuficientes',
					'error' => []
				);
			}
			echo json_encode($resp);
		}

		function validar_guardar($data){
			$error = [];
			if (!isset($data['credito']) || empty($data['credito'])) {
				$error['credito'] = 'Credito no especificado';
			}
			if (!isset($data['monto']) || empty($data['monto']) || $data['monto'] <= 0) {
				$error['monto'] = 'monto no valido';
			}
			return $error;
		}


	}
?>