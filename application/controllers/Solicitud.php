<?php  
	/**
	 * 
	 */
	class Solicitud extends CI_Controller
	{
		
		function __construct()
		{
			parent::__construct();
			if (!$this->session->userdata('pres_ses_sesion')) {
				redirect(base_url().'Acceso','refresh');
			}
			$this->load->model('SolicitudModel');
			// $this->load->model('AdelantoModel');
		}

		function index(){
			$this->load->view('Layout/header.php');
			$this->load->view('Solicitud/lista.php');
			$this->load->view('Layout/footer.php');
		}

		function consulta(){
			$res = $this->SolicitudModel->Listar(
				$_POST['limite'],
				$this->session->userdata('pres_ses_sucursal')
			);
			echo json_encode($res);
		}

		function detalle(){
			if (isset($_POST['id']) && is_numeric($_POST['id'])) {
				$det_data = $this->SolicitudModel->Detalle($_POST['id']);
				
				$resp = [];
				if (!empty($det_data)) {
					$resp = array(
						'status' => 'OK',
						'data' => $det_data,
						'message' => 'Datos encontrados'
					);
				}else{
					$resp = array(
						'status' => 'WARN',
						'data' => [],
						'message' => 'Datos no encontrados'
					);
				}
				echo json_encode($resp);
			}else{
				die();
			}
		}

	}
?>