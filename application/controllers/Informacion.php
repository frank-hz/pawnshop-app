<?php  
	
	class Informacion extends CI_Controller
	{
		
		function __construct(){
			parent::__construct();
			$this->load->model('InformacionModel');
		}

		public function Cliente(){
			$res['total'] = $this->InformacionModel->cliente_total();
			$res['hoy'] = $this->InformacionModel->cliente_hoy(date("Y-m-d"));
			echo json_encode($res);
		}
	}
?>