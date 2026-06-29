<?php  
	
	class TipoDocumento extends CI_Controller
	{
		
		function __construct()
		{
			parent::__construct();
			if (!$this->session->userdata('pres_ses_sesion')) {
				redirect(base_url().'Acceso','refresh');
			}
			$this->load->model('TipoDocumentoModel');
			$this->load->model('PermisoModel');
		}

		public function index(){
			
		}

		public function rest(){
			$rest = $this->TipoDocumentoModel->consulta('rest');
			echo json_encode($rest);
		}


	}
?>