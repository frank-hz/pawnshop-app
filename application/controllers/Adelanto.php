<?php  
	/**
	 * 
	 */
	class Adelanto extends CI_Controller
	{
		
		function __construct()
		{
			parent::__construct();
			if (!$this->session->userdata('pres_ses_sesion')) {
				redirect(base_url().'Acceso','refresh');
			}
			$this->load->model('CreditoModel');
			$this->load->model('AdelantoModel');
		}

		function movimientos($id = null){
			if ($id != null) {
				$credito = $this->CreditoModel->Buscar_libre(
					'cred_codigo as id, cred_serie as codigo, cred_estado as estado',
					array('cred_codigo' => $id)
				);
				if (!empty($credito)) {
					$data['credito'] = $credito;
					$data['entrada'] = $this->AdelantoModel->Buscar_entrada($id);
					$data['salida'] = $this->AdelantoModel->Buscar_salida($id);
					$this->load->view("Layout/header.php");
					$this->load->view("Adelanto/movimiento.php", $data);
					$this->load->view("Layout/footer.php");
				}else{
					$data['titulo'] = 'Credito Desconocido';
					$data['mensaje'] = 'No se encontraron los datos del credito';
					$this->load->view("Layout/header.php");
					$this->load->view("Error/no_access.php", $data);
					$this->load->view("Layout/footer.php");
				}
			}else{
				$data['titulo'] = 'Credito Desconocido';
				$data['mensaje'] = 'No se encontraron los datos del credito';
				$this->load->view("Layout/header.php");
				$this->load->view("Error/no_access.php", $data);
				$this->load->view("Layout/footer.php");
			}
		}

	}
?>