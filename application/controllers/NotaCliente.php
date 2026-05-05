<?php  
	
	class NotaCliente extends CI_Controller
	{
		
		function __construct(){
			parent::__construct();
			if (!$this->session->userdata('pres_ses_sesion')) {
				redirect(base_url().'Acceso','refresh');
			}
			$this->load->model('NotaClienteModel');
			$this->load->model('PermisoModel');

			$this->load->helper('cliente_nota_util');
		}

		function consulta(){
			$post = $this->input->post();
			$rest = $this->NotaClienteModel->Buscar($post['id']);
			echo json_encode($rest);
		}

		function agregar(){
			$resp = [];
			if (isset($_POST['cliente']) && isset($_POST['descripcion'])) {
				try {
					$data = array(
						'ncli_cliente' => $_POST['cliente'],
						'ncli_empleado' => $this->session->userdata('pres_ses_empleado'),
						'ncli_descripcion' => strtolower($_POST['descripcion']),
						'ncli_fecha' => date("Y-m-d")
					);
					$this->NotaClienteModel->insertar($data);
					$resp['status'] = 'OK';
					$resp['message'] = 'Nota agregada';
				} catch (PDOException $e) {
					
				}
			}else{
				$resp['status'] = 'WARN';
				$resp['message'] = 'Datos imcompletos';
				$resp['error'] = $valida;
			}

			echo json_encode($resp);
		}

		function eliminar(){
			$resp = [];
			$permiso = $this->PermisoModel->verificar_permiso(
				$this->session->userdata('pres_ses_usuario'),
				'cliente_editar'
			);
			if ( $permiso && $permiso->codigo == 'P' ) {
				if ($this->NotaClienteModel->eliminar($_POST['id'])) {
					$resp['status'] = 'OK';
					$resp['message'] = 'Nota eliminada';
				}else{
					$resp['status'] = 'ERROR';
					$resp['message'] = 'No se pudo eliminar la nota.';
				}
			}else{
				$resp['status'] = 'WARN';
				$resp['message'] = 'Permisos insuficientes';
			}
			echo json_encode($resp);
		}




		// C R U D
		// encode 03-2023		

		function get_data(){
            try {
				if (empty($_POST['id'])) {
					die(json_encode([
						'warning' => 'Nota no identificada'
					]));
				}
				$nota = $this->NotaClienteModel->getById($_POST['id']);
				echo json_encode([
					'code' => 'OK',
					'result' => $nota
				]);
			} catch (Exception $e) {
				$code = $e->getCode();
				$mssg = $e->getMessage();
				echo json_encode([
					'code' => 'ERR',
					'error' => ($code==99) ? $mssg : 'Error al procesar',
					'log' => $e->getMessage()
				]);
			}
        }
        function save_data(){
            try {
            	if (empty($_POST['idcliente']) || empty($_POST['descripcion'])) {
					die(json_encode([
						'warning' => 'Datos incompletos o invalidos'
					]));
				}
				$data = [
					'ncli_cliente' => intval($_POST['idcliente']),
					'ncli_descripcion' => $_POST['descripcion'],
					'ncli_fecha' => date("Y-m-d"),
					'ncli_empleado' => $this->session->userdata('pres_ses_empleado'),
					'mensaje' => $_POST['descripcion'],
					'row_fixed' => isset($_POST['fixed']) ? $_POST['fixed'] : 0
				];
				$ins = $this->NotaClienteModel->insert($data);
				if (!$ins)throw new Exception("Registro no completado");
				echo json_encode([
					'code' => 'OK',
					'success' => 'Datos registrados',
					'id' => $this->db->insert_id()
				]);
			} catch (Exception $e) {
				$code = $e->getCode();
				$mssg = $e->getMessage();
				echo json_encode([
					'code' => 'ERR',
					'error' => ($code==99) ? $mssg : 'Error al procesar',
					'log' => $e->getMessage()
				]);
			}
        }		
		function update_data(){
			try {
				if (empty($_POST['id'])) {
					die(json_encode([
						'warning' => 'Nota no identificada'
					]));
				}
				$data_update = [];
				if (!empty($_POST['descripcion'])) {
					$data_update['descripcion'] = $_POST['descripcion'];
					$data_update['mensaje'] = $_POST['descripcion'];
				}
				if (isset($_POST['fixed'])) {
					$data_update['row_fixed'] = $_POST['fixed'];
				}
				if (empty($data_update)){
					die(json_encode([
						'warning' => 'Datos incompletos o invalidos'
					]));
				}
				$this->NotaClienteModel->update($_POST['id'], $data_update);
				$row_change = $this->db->affected_rows();
				echo json_encode([
					'code' => 'OK',
					'success' => ($row_change>0) ? 'Cambios aplicados' : 'Sin cambios',
					'ok' => ($row_change>0) ? 'Cambios aplicados' : 'Sin cambios'
				]);
			} catch (Exception $e) {
				$code = $e->getCode();
				$mssg = $e->getMessage();
				echo json_encode([
					'code' => 'ERR',
					'error' => ($code==99) ? $mssg : 'Error al procesar',
					'log' => $e->getMessage()
				]);
			}
		}
		function delete_data(){
			try {
				if (empty($_POST['id'])) {
					die(json_encode([
						'warning' => 'Nota no identificada'
					]));
				}				
				$deleted = $this->NotaClienteModel->delete($_POST['id']);
				if (!$deleted || $this->db->affected_rows() == 0) {
					die(json_encode([
						'warning' => 'El proceso no se completo'
					]));
				}
				echo json_encode([
					'code' => 'OK',
					'success' => 'Nota Eliminada'
				]);
			} catch (Exception $e) {
				echo json_encode([
					'code' => 'ERR',
					'message' => 'Error al procesar',
					'log' => $e->getMessage()
				]);
			}
			
		}



		// recode 05-2023
		function get_by_cliente(){
			try {
				if (empty($_POST['idcliente'])) {
					die(json_encode([
						'warning' => 'cliente no identificado'
					]));
				}
				// $setting = notaclienteu_data_search_setting();
				$notas = $this->NotaClienteModel->getByCliente(
					$_POST['idcliente'],
					'DESC'
				);
				if (!empty($notas)) {
					foreach ($notas as $nota) {
						$nota->empleado_url = base_url().'empleado/detalle/'.$nota->empleado_id;
						$nota->descripcion = $nota->descripcion;
						$nota->fixed = intval($nota->fixed);
					}
				}
				echo json_encode([
					'code' => 'OK',
					'result' => $notas,
					'message' => 'proceso completado'
				]);
			} catch (Exception $th) {
				$code = $e->getCode();
				$mssg = $e->getMessage();
				echo json_encode([
					'code' => 'ERR',
					'message' => ($code==99) ? $mssg : 'Error al procesar',
					'log' => $e->getMessage()
				]);
			}
		}

		

	}
?>