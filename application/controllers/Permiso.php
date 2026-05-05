<?php  
	
	class Permiso extends CI_Controller
	{
		
		function __construct()
		{
			parent::__construct();
			if (!$this->session->userdata('pres_ses_sesion')) {
				redirect(base_url().'Acceso','refresh');
			}
			$this->load->model('PermisoModel');
		}

		public function verificacion(){
			$verifica = $this->PermisoModel->consulta('rest');
			echo json_encode($rest);
		}


	}
?>