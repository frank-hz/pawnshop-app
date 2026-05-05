<?php
    class Prenda_api extends CI_Controller
    {
        function __construct(){
			parent::__construct();
			if (!$this->session->userdata('pres_ses_sesion')) {
				redirect(base_url().'Acceso','refresh');
			}
			$this->load->model('PrendaModel');
            $this->load->model('CreditoModel');
            $this->load->model('Prenda_apiModel');
			$this->load->model('PropiedadModel');

            // Helpers
            $this->load->helper('prenda_util');
			$this->load->helper('permission_helper');
		}

		// CRUD
		function get_all(){
			
		}
        function get_data(){
            $data = $this->Prenda_apiModel->getById($_POST['id']);
            if (!empty($data)) {
                $data->url = base_url().'prenda/detalle/'.$data->id;
                $data->estado_tag = prendau_tag_estado($data->estado);
                $data->prenda_data_string = prendau_data_to_string($data);
				$data->prenda_form_string = prendau_string_form_edit($data);	
            }
            echo json_encode($data);
        }
        function save_data(){
            try {
				if (!permission_validate('prenda_registrar')) throw new Exception('Permisos insuficientes');
				$this->form_validation->set_rules(prendau_insert_validation());
				if (!$this->form_validation->run()) throw new Exception("Datos incompletos o invalidos",99);

				$data = prendau_prepare_data_insert();
				if (empty($data)) throw new Exception('Datos no encontrados',99);
				$duplicity = $this->Prenda_apiModel->verifyDuplicityNew($data);
				if (!empty($duplicity)) {
					die(json_encode([
						'code' => 'WARN',
						'log' => $duplicity,
						'warning' => "
							[Duplicidad] El cliente ya tiene registrado una prenda con estos datos.
						"
					]));
				}
				$data['pren_fecha_registro'] = date("Y-m-d");
				$ins = $this->Prenda_apiModel->insert($data);
				$id = $this->db->insert_id();	
				$this->PropiedadModel->Insertar([
					'prop_empleado' => $this->session->userdata('pres_ses_empleado'),
					'prop_cliente' => $_POST['idcliente'],
					'prop_prenda' => $id,
					'prop_inicio' => date("Y-m-d"),
					'prop_motivo' => 'REGISTRO DE PRENDA',
					'prop_estado' => 'A'
				]);
				echo json_encode([
					'code' => 'OK',
					'success' => 'Datos guardados',
					'id' => $id
				]);

			} catch (Exception $e) {
				$code = $e->getCode();
				$mssg = $e->getMessage();
				die(json_encode([
					'code' => 'ERR',
					'error' => ($code==99) ? $mssg : 'Error al procesar',
					'log' => $e->getMessage()
				]));
			}
        }		
		function update_data(){
			try {
                if (!permission_validate('prenda_editar')) throw new Exception('Permisos insuficientes');
				$this->form_validation->set_rules(prendau_update_validation());
				if (!$this->form_validation->run()) throw new Exception("Datos incompletos o invalidos",99);

                $data = prendau_prepare_data_update();
                if (empty($data)) throw new Exception('Datos no encontrados',99);
				$duplicity = $this->Prenda_apiModel->verifyDuplicityUpdate($_POST['id'],$data);
				if (!empty($duplicity)) {
					die(json_encode([
						'code' => 'WARN',
						'warning' => "
							[Duplicidad] El cliente ya tiene registrado una prenda con estos datos.
						"
					]));
				}

				$this->Prenda_apiModel->update($_POST['id'], $data);
                $affrows = $this->db->affected_rows();
				echo json_encode([
                    'code' => 'OK',
                    'success' => ($affrows) ? 'Cambios Guardados' : 'Sin Cambios'
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
				if (empty($_POST['id']) || empty($_POST['idcliente'])) throw new Exception('Prenda no identificada');

				if (!permission_validate('prenda_registrar')) {
					die(json_encode([
						'warning' => 'Permisos insuficientes'
					]));
				}

                $creditos_asoc = $this->Prenda_apiModel->getCreditosByPrenda($_POST['id']);

                if (!empty($creditos_asoc)) {
					die(json_encode([
						'warning' => 'La prenda tiene creditos asociados'
					]));
				}

				$del = $this->Prenda_apiModel->delete($_POST['id'],$_POST['idcliente']);
				if (!$del || $this->db->affected_rows() == 0) throw new Exception('El proceso no se completo.',99);

				echo json_encode([
					'code' => 'OK',
					'success' => 'Prenda Eliminada',
					'log' => $this->db->affected_rows(),
					'url' => base_url().'cliente/detalle/'.$_POST['idcliente']
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


		// CUSTOM
		function get_by_cliente(){
            try {
				// $setting = prendau_data_search_setting();
				$setting = prendau_search_setting();
				$prendas = $this->Prenda_apiModel->getAllByCliente(
					$_POST['idcliente'],
					$setting
				);
				if (!empty($prendas)) {
					foreach ($prendas as $prenda) {
						$prenda->url = base_url().'prenda/detalle/'.$prenda->id;
						$prenda->estado_tag = prendau_tag_estado($prenda->estado);
					}
				}
				echo json_encode([
					'code' => 'OK',
					'result' => $prendas,
					'message' => 'proceso completado.'
				]);
			} catch (Exception $th) {
				$code = $e->getCode();
				$mssg = $e->getMessage();
				die(json_encode([
					'code' => 'ERR',
					'message' => ($code==99) ? $mssg : 'Error al procesar',
					'log' => $e->getMessage()
				]));
			}
        }
		function get_by_credito(){
            $data = $this->Prenda_apiModel->getAllByCredito($_POST['idcredito']);
            echo json_encode($data);
        }
		function get_form_by_tipo(){
			try {
				if (empty($_POST['tipo'])) throw new Exception('Tipo desconocido',99);
				$form = prendau_string_form_new($_POST['tipo']);
				echo json_encode([
					'code' => 'OK',
					'message' => 'proceso completado',
					'result' => $form
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
		function get_form_edit(){
			try {
				if (empty($_POST['id'])) throw new Exception('Prenda desconocida',99);
				if (empty($_POST['tipo'])) throw new Exception('Tipo desconocido',99);
				$prenda = $this->Prenda_apiModel->getById($_POST['id']);
				// $tipo = (empty($_POST['tipo'])) ? $_POST['tipo'] : null;
				$tipo = $_POST['tipo'];
				$form_data = prendau_string_form_edit($prenda, $tipo);

				echo json_encode([
					'code' => 'OK',
					'message' => 'proceso completado',
					'result' => $form_data,
					'categoria_default' => $prenda->categoria_id
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

    }
    
?>