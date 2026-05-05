<?php  
	
	class Conducta extends CI_Controller
	{
        function __construct()
		{
			parent::__construct();
			if (!$this->session->userdata('pres_ses_sesion')) {
				redirect(base_url().'Acceso','refresh');
			}
			$this->load->model('ConductaModel');
			$this->load->helper('conducta_util');
		}

        function service_get_data(){
			$data = file_get_contents("php://input");
			$data = json_decode($data);
			$resp = [];
			$id = $data->idcliente;
			$resp = $this->ConductaModel->get_data($id);			
			echo json_encode($resp);
		}
		function service_insert(){
			$ps = $_POST;
			$recv = file_get_contents('php://input');
			$recv = json_decode($recv);
			$dval = $this->ConductaModel->get_data($recv->idcliente);
			if ($dval) {
				$upd = $this->ConductaModel->update_data([
					'cond_puntuacion' => $recv->puntuacion,
					'cond_bloqueo_atencion' => $recv->bloqueo,
					'cond_bloqueo_motivo' => $recv->motivo
				],$dval->id);

				$chgs = $this->db->affected_rows();
				echo json_encode([
					'status' => $chgs ? 'OK' : 'WARN',
					'message' => $chgs ? 'Cambios Aplicados' : 'Sin Cambios',
				]);
			}else {
				$ins = $this->ConductaModel->insert_data([
					'cond_cliente' => $recv->idcliente,
					'cond_puntuacion' => $recv->puntuacion,
					'cond_bloqueo_atencion' => $recv->bloqueo,
					'cond_bloqueo_motivo' => $recv->motivo
				]);
				echo json_encode([
					'status' => 'OK',
					'message' => 'Datos Guardados',
					'id' => $this->db->insert_id()
				]);
			}

		}



		// encode 05-2023
		function get_data(){
            try {
				if (empty($_POST['idcliente'])) throw new Exception('Cliente no identificado',99);
				$conducta = $this->ConductaModel->get_data($_POST['idcliente']);
				if (!empty($conducta)) {
					$conducta->bloqueo_motivo = ucwords(strtolower($conducta->bloqueo_motivo));
				}
				echo json_encode([
					'code' => 'OK',
					'result' => $conducta
				]);
			} catch (Exception $e) {
				$code = $e->getCode();
				$mssg = $e->getMessage();
				echo json_encode([
					'code' => 'ERR',
					'message' => ($code==99) ? $mssg : 'Error al procesar',
					'log' => $e->getMessage()
				]);
			}
        }
		function save_data(){
			try {
				if (empty($_POST['idcliente'])) 
					throw new Exception("Cliente no identificado",99);

				$conducta = $this->ConductaModel->get_data($_POST['idcliente']);
				if (!empty($conducta)) {
					$_POST['id'] = $conducta->id;
					$this->form_validation->set_rules(
						conductau_update_validation()
					);
					if (!$this->form_validation->run()) 
						throw new Exception("Datos incompletos o invalidos",99);

					$pdata = conductau_prepare_data_update();
					if (empty($pdata)) 
						throw new Exception('Datos incompletos o invalidos',99);

					$update = $this->ConductaModel->update_data(
						$pdata, $conducta->id
					);
					if (!$update) 
						throw new Exception('Error al guardar cambios',99);

					$log = $this->db->affected_rows();
					echo json_encode([
						'code' => 'OK',
						'message' => ($log > 0) ? 'Cambios aplicados' : 'Sin cambios',
						'type' => 'update',
						'rows' => $this->db->affected_rows(),
						'id' => $this->db->insert_id()
					]);
				}else {
					$this->form_validation->set_rules(
						conductau_insert_validation()
					);
					if (!$this->form_validation->run()) 
						throw new Exception("Datos incompletos o invalidos",99);

					$pdata = conductau_prepare_data_insert();
					if (empty($pdata)) 
						throw new Exception('Datos incompletos o invalidos',99);

					$ins = $this->ConductaModel->insert_data($pdata);

					if (!$ins || $this->db->affected_rows() == 0) 
						throw new Exception('Los datos no se guardaron',99);
						
					echo json_encode([
						'code' => 'OK',
						'message' => 'Datos guardados',
						'type' => 'insert',
						'id' => $this->db->insert_id()
					]);
				}
			} catch (Exception $e) {
				$code = $e->getCode();
				$mssg = $e->getMessage();
				echo json_encode([
					'code' => 'ERR',
					'message' => ($code==99) ? $mssg : 'Error de procesamiento',
					'log' => $e->getMessage()
				]);
			}

		}

    }

?>