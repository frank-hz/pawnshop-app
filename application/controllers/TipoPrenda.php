<?php  
	
	class TipoPrenda extends CI_Controller {
		
		function __construct()
		{
			parent::__construct();
			if (!$this->session->userdata('pres_ses_sesion')) {
				redirect(base_url().'Acceso','refresh');
			}
			$this->load->model('TipoPrendaModel');
			$this->load->model('PermisoModel');
			$this->load->model('CategoriaModel');
		}
		// Vistas
		function index(){
			$data['tipos'] = $this->TipoPrendaModel->getAll();
			$this->load->view("Layout/header.php");
			$this->load->view("TipoPrenda/lista.php", $data);
			$this->load->view("Layout/footer.php");
		}
		function editar($id = null){
			if ($id != null) {
				$data['tipo'] = $this->TipoPrendaModel->getById($id);
				if (!empty($data['tipo'])) {
					$this->load->view("Layout/header.php");
					$this->load->view("TipoPrenda/detalle.php", $data);
					$this->load->view("Layout/footer.php");
				}else{
					$data['titulo'] = 'Cliente desconocido';
					$data['mensaje'] = 'No se encontraron datos del cliente';
					$this->load->view("Layout/header.php");
					$this->load->view("Error/no_found.php", $data);
					$this->load->view("Layout/footer.php");
				}
			}else{
				$data['titulo'] = 'Tipo de prenda desconocido';
				$data['mensaje'] = 'No se encontraron datos para el seguimiento del registro';
				$this->load->view("Layout/header.php");
				$this->load->view("Error/no_found.php", $data);
				$this->load->view("Layout/footer.php");
			}
		}



		// OLD
		// Servicios
		function registrar(){
			$rsp = [];
			if (!empty($_POST['nombre'])) {
				$ver = $this->TipoPrendaModel->Buscar_libre(
					'tpren_codigo as id', 
					array('tpren_nombre' => $_POST['nombre'])
				);
				if (empty($ver)) {
					$this->TipoPrendaModel->Insertar(array(
						'tpren_nombre' => strtoupper($_POST['nombre']), 
						'tpren_descripcion' => $_POST['descripcion'],
						'tpren_estado' => 'A'
					));
					$id = $this->db->insert_id();
					$rsp = array(
						'status' => 'OK',
						'message' => 'Proceso completado con exito',
						'id' => $id
					);
				}else{
					$rsp = array(
						'status' => 'WARN',
						'message' => 'Ya existe un tipo de prenda con el mismo nombre',
						'id' => ''
					);
				}
			}else{
				$rsp = array(
					'status' => 'WARN',
					'message' => 'Datos incompletos',
					'id' => ''
				);
			}
			echo json_encode($rsp);
		}
		function eliminar(){
			$rsp = [];
			if (!empty($_POST['id'])) {
				$this->TipoPrendaModel->Eliminar($_POST['id']);
				if ($this->db->affected_rows() > 0) {
					$rsp = array(
						'status' => 'OK',
						'message' => 'Proceso completado con exito'
					);
				}else{
					$rsp = array(
						'status' => 'WARN',
						'message' => 'No se pudo completar el proceso'
					);
				}
			}else{
				$rsp = array(
					'status' => 'WARN',
					'message' => 'Datos incompletos'
				);
			}
			echo json_encode($rsp);
		}
		// Recode 05-2023
		function get_all(){
            $data = $this->TipoPrendaModel->get_all();
            echo json_encode($data);
        }
		
    
	}
?>