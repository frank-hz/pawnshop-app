<?php  
	
	class Categoria extends CI_Controller
	{
		
		function __construct()
		{
			parent::__construct();
			if (!$this->session->userdata('pres_ses_sesion')) {
				redirect(base_url().'Acceso','refresh');
			}
			$this->load->model('CategoriaModel');
			$this->load->model('PermisoModel');
		}

		public function index(){			
			$this->load->view("Layout/header.php");
			$this->load->view("Categoria/index.php");
			$this->load->view("Layout/footer.php");
		}



		
		public function consulta(){
			$res = $this->CategoriaModel->Lista($_POST['tipo'], $_POST['buscar']);
			echo json_encode($res);
		}
		// NEWCODE: 09-11-2020			
		function get_all(){
            $data = $this->CategoriaModel->getByTipo();
            echo json_encode($data);
        }
		// NEW - 01-2025
		function getByTipo(){
			if (empty($_POST['idtipo'])){
				exit(json_encode("NO_DATA"));
			}
			$data = $this->CategoriaModel->getByTipo($_POST['idtipo']);
			echo json_encode($data);
		}

	}
?>