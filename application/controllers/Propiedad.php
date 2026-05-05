<?php  

class Propiedad extends CI_Controller
{
	
	function __construct()
	{
		parent::__construct();
		$this->load->model('PermisoModel');
		$this->load->model('PrendaModel');
		$this->load->model('PropiedadModel');
		if (!$this->session->userdata('pres_ses_sesion')) {
			redirect(base_url().'Acceso','refresh');
		}
	}

	public function Establecer(){
		$post = $this->input->post();
		$resp = [];
		$permiso = $this->PermisoModel->verificar_permiso(
			$this->session->userdata('pres_ses_usuario'),
			'prenda_transferir'
		);
		if ( $permiso && $permiso->codigo == 'P' ) {
			if (!empty($post['cliente']) && !empty($post['prenda']) && isset($post['motivo'])) {
				$verifica = $this->PrendaModel->Buscar_libre(
					'pren_estado as estado', array('pren_codigo' => $_POST['prenda'])
				);
				if ($verifica->estado === 'D' OR $verifica->estado === 'R') {
					try {
						$this->db->trans_begin();
						$this->PropiedadModel->Modificar(
							array('prop_estado' => 'I', 'prop_termino' => date('Y-m-d')),
							array('prop_prenda' => $_POST['prenda'], 'prop_estado' => 'A')
						);
						$propiedad_data = array(
							'prop_empleado' => $this->session->userdata('pres_ses_empleado'),
							'prop_cliente' => $_POST['cliente'],
							'prop_prenda' => $_POST['prenda'],
							'prop_inicio' => date("Y-m-d"),
							'prop_motivo' => strtoupper($_POST['motivo']),
							'prop_estado' => 'A'
						);
						$this->PropiedadModel->Insertar($propiedad_data);
						$this->PrendaModel->Modificar($_POST['prenda'], array('pren_cliente' => $_POST['cliente']));
						$resp = array(
							'status' => 'OK',
							'message' => 'Prenda transferida con exito',
							'id' => $_POST['prenda']
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
						'status' => 'WARN', 'message' => 
						'el estado actual de la prenda no permite transferencias'
					);
				}
			}else{
				$resp['status'] = 'ERROR';
				$resp['message'] = 'Datos incompletos.';
			}
		}else{
			$resp['status'] = 'WARN';
			$resp['message'] = 'Permisos insuficientes.';
		}
		echo json_encode($resp);
	}

}
?>
