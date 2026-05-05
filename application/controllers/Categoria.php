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
			$this->load->view("Categoria/lista.php");
			$this->load->view("Layout/footer.php");
		}

		public function consulta(){
			$res = $this->CategoriaModel->Lista($_POST['tipo'], $_POST['buscar']);
			echo json_encode($res);
		}

		public function registrar(){
			$post = $this->input->post();
			$resp = [];
			if ( $post['nombre'] && $post['tipo'] ) {
				try {
					$this->CategoriaModel->Insertar(array(
						'cat_nombre' => strtoupper($post['nombre']), 'cat_tipo' => $post['tipo']
					));
					$resp = array('status' => 'OK', 'message' => 'Categoria registrada');
				} catch (PDOException $e) {
					die('ERROR');
				}
			}else{
				$resp = array('status' => 'WARN', 'message' => 'Datos incompletos');
			}
			echo json_encode($resp);
		}

		public function modificar(){
			$post = $this->input->post();
			$resp = [];
			if ( $post['id'] && $post['nombre'] && $post['tipo'] ) {
				try {
					$this->CategoriaModel->Modificar(
						$post['id'], 
						array('cat_tipo' => $post['tipo'], 'cat_nombre' => strtoupper($post['nombre']))
					);
					$rows = $this->db->affected_rows();
					$msg = ($rows > 0) ? 'Cambios aplicados' : 'Sin cambios';
					$resp = array('status' => 'OK', 'message' => $msg);
				} catch (PDOException $e) {
					die('ERROR');
				}				
			}else{
				$resp = array('status' => 'OK', 'message' => 'Datos incompletos');
			}
			echo json_encode($resp);
		}

		public function eliminar(){
			$post = $this->input->post();
			$resp = [];
			if (!empty($post['id'])) {
				$del = $this->CategoriaModel->Eliminar($post['id']);
				$rows = $this->db->affected_rows();
				if ($rows > 0) {
					$resp = array('status' => 'OK', 'message' => 'Categoria eliminada');
				}else{
					$resp = array('status' => 'ERROR', 'message' => 'El proceso no se completo');
				}
			}else{
				$resp = array('status' => 'ERROR', 'message' => 'Datos incompletos');
			}
			echo json_encode($resp);
		}

		// NEWCODE: 09-11-2020

		function ServiceListarPorTipo(){
			try {
				$json = file_get_contents('php://input');
				$form_data = json_decode($json);
				$data = $this->CategoriaModel->SelectByTipo($form_data->tipo);
				echo json_encode($data);
			} catch (Exception $e) {
				echo json_encode([]);
			}		
		}
		function ServiceConsulta(){
			$data = $this->CategoriaModel->SelectAll();
			echo json_encode($data);		
		}
		
		function get_all(){
            $data = $this->CategoriaModel->getByTipo();
            echo json_encode($data);
        }    
		function get_by_tipo(){
            $data = $this->CategoriaModel->getByTipo($_POST['idtipo']);
            echo json_encode($data);
        } 
        function get_data(){
            $data = $this->TipoPrendaModel->getData($_POST['id']);
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