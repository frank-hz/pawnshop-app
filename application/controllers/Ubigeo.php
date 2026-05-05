<?php  

class Ubigeo extends CI_Controller
{
	
	function __construct()
	{
		parent::__construct();
		$this->load->model('UbigeoModel');
		if (!$this->session->userdata('pres_ses_sesion')) {
			redirect(base_url().'Acceso','refresh');
		}
	}

	function index(){
		$this->load->view("layout/admin/header.php");
		$this->load->view("dashboard/home.php");
		$this->load->view("layout/admin/footer.php");
	}
	
	function consulta(){
		if (isset($_POST['tipo'])) {
			$res = $this->UbigeoModel->Lista($_POST['tipo'], $_POST['buscar']);
		}else $res = [];
		echo json_encode($res);
	}

	function service_search(){
		$data = json_decode(file_get_contents("php://input"));
		$resp = $this->UbigeoModel->get_data_search($data->search);
		echo json_encode($resp);
	}

}
?>
