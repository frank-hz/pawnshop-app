<?php  
	
	class AlertaCliente extends CI_Controller
	{
		
		function __construct()
		{
			parent::__construct();
			if (!$this->session->userdata('pres_ses_sesion')) {
				redirect(base_url().'Acceso','refresh');
			}
			$this->load->model('AlertaClienteModel');
		}

		function lista(){
			$data = [];
			if ($_POST && $_POST['id']) {
				$data = $this->AlertaClienteModel->Listar(intval($_POST['id']));
			}
			echo json_encode($data);
		}

		function modificar(){
			try {
				if (!$_POST || !$_POST['id'] || !$_POST['estado']) throw new Exception('Datos vacios');
				$this->AlertaClienteModel->Modificar($_POST['id'], [
			  		'acli_estado' => strtoupper($_POST['estado'])
			  	]);
				$aff = ($this->db->affected_rows() > 0) ? 'Alerta Actualizada' : 'Sin Cambios';
				$resp = ['status' => 'OK', 'message' => $aff];
				echo json_encode($resp);
			} catch (Exception $e) {
				echo json_encode(['status' => 'ERROR', 'message' => $e->getMessage()]);
			}
		}

	}
?>

 