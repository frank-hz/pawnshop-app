<?php  

	class Acceso extends CI_Controller{
		
		function __construct(){
			parent::__construct();
			if ($this->session->userdata('pres_ses_sesion')) {
				redirect(base_url().'Inicio','refresh');
			}
			$this->load->model('UsuarioModel');
			$this->load->model('MenuModel');
			$this->load->model('ConfiguracionModel');
		}

		public function index(){
			// if ($this->session->userdata('pres_ses_sesion')) {
			// 	redirect(base_url().'Inicio','refresh');
			// }else{
			// 	$this->login();
			// }
			echo "view not found";
		}

		public function login_form(){
			$this->load->view("Sesion/login.php");
		}

		public function Revision(){
			$this->load->view("Acceso/revision.php");
		}
	}
?>
