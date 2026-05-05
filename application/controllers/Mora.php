<?php  
	/**
	 * 
	 */
	class Mora extends CI_Controller
	{
		
		function __construct()
		{
			parent::__construct();
			$this->load->model('MoraModel');
		}

		function buscar(){
			// $res = $this->MoraModel->
		}

		function modificar(){
			$resp = [];
			if (!empty($_POST['id']) && !empty($_POST['dias']) && $_POST['dias'] > 0 && !empty($_POST['monto']) && $_POST['monto'] > 0) {
				$this->MoraModel->Modificar($_POST['id'], array(
					'mor_dias' => $_POST['dias'],
					'mor_monto' => $_POST['monto']
				));
				$msg = ($this->db->affected_rows() > 0) ? 'Cambios aplicados' : 'Sin cambios';
				$resp = array(
					'status' => 'OK',
					'message' => $msg,
					'error' => []
				);
			}else{
				$error = [];
				if (empty($_POST['id'])) {
					$error['mora'] = 'Mora desconocida';
				}
				if (empty($_POST['dias']) || $_POST['dias'] <= 0) {
					$error['dias'] = 'Dias de atraso erroneos';
				}
				if (empty($_POST['monto']) || $_POST['monto'] <= 0) {
					$error['monto'] = 'Monto erroneo';
				}
				$resp = array(
					'status' => 'WARN',
					'message' => 'Errores encontrados',
					'error' => $error
				);
			}
			echo json_encode($resp);
		}

		function eliminar(){
			$resp = [];
			if (!empty($_POST['id'])) {
				$this->MoraModel->Eliminar($_POST['id']);
				if ($this->db->affected_rows() > 0) {
					$resp = array(
						'status' => 'OK',
						'message' => 'Mora eliminada'
					);
				}else{
					$resp = array(
						'status' => 'WARN',
						'message' => 'No se pudo completar el proceso'
					);
				}
			}else{
				$resp = array(
					'status' => 'ERROR',
					'message' => 'Error al procesar'
				);
			}
			echo json_encode($resp);
		}

	}
?>