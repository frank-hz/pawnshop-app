<?php

    class Admin_api extends CI_Controller
    {
        function __construct(){
            parent::__construct();
            if (!$this->session->userdata('pres_ses_sesion')) {
				redirect(base_url().'Acceso','refresh');
			}
            $this->load->model('PermisoModel');
            // $this->load->model('Credito_bzModel');
            // $this->load->model('CreditoModel');
            // $this->load->model('CuotaModel');
            // $this->load->model('PrendaModel');
            // $this->load->model('AlertaClienteModel');
			// $this->load->model('SucursalModel');

			// Helpers
        }


		// U T I L 
        function cuota_calculo_proximo(){
            try {
                if (empty($_POST['fecha'])) {
                    die(json_encoe([
                        'warning' => 'fecha de indicada'
                    ]));
                }

                $dias = ($_POST['ciclo'] == 'MENSUAL') ? 30 : 7;
                $prox_vencimiento = date(
                    "d-m-Y", 
                    strtotime($_POST['fecha']. " + $dias days")
                );

                echo json_encode([
                    'success' => 'proceso completado',
                    'result' => $prox_vencimiento
                ]);
            } catch (Exception $e) {
                die(json_encode([
                    'error' => 'Error al procesar',
                    'log' => $e->getMessage()
                ]));
            }
        }

		function save_data(){
			try {
				if (!permission_validate('credito_registrar')) throw new Exception('Permisos insuficientes',99);
				$this->form_validation->set_rules(creditou_insert_validation());
				if (!$this->form_validation->run()) throw new Exception("Datos incompletos o invalidos",99);
				
				$this->db->trans_begin();
				// Generacion de Serie
				$sede_id = $this->session->userdata('pres_ses_sucursal');
				$sede_data = $this->SucursalModel->get_data($sede_id);
				if (empty($sede_data) || $sede_data->serie == null) throw new Exception('Sucursal no identificada',99);
				$orden_rastro = $this->Credito_bzModel->getLastOrdenBySucursal($sede_id);
				$orden_rastro = intval($orden_rastro->value);
				$orden_new = $orden_rastro + 1;
				$serie_new = $sede_data->serie.'-'.$orden_new;
				if (empty($orden_rastro) || !intval($orden_rastro)) throw new Exception('Error al generar serie',99);
				if (!empty($this->Credito_bzModel->getBySerieQuick($serie_new))) throw new Exception('Conflicto de generacion de serie',99);
				
				$data = array(
					'cred_serie' => $serie_new,
					'cred_orden' => $orden_new,
					'cred_creacion' => date("Y-m-d "),
					'cred_empleado' => $this->session->userdata('pres_ses_empleado'),
					'cred_cliente' => $_POST['idcliente'],
					'cred_prenda' => $_POST['idprenda'],
					'cred_monto' => $_POST['monto'],
					'cred_prorroga' => $_POST['prorroga'],
					'cred_cargo' => $_POST['cargo'],
					'cred_tasa' => $_POST['interes'],
					'cred_tipo_tasa' => $_POST['tipo'],
					'cred_pago_semanal' => $_POST['pago'],
					'cred_penalidad' => $_POST['mora'],
					'cred_sucursal' => $this->session->userdata('pres_ses_sucursal'),
					'cred_estado' => 'E',
					'cred_ciclo_pago' => $ciclo_cant,
					'cred_ciclo_ref' => $_POST['ciclo'],
					'cred_create_at' => date("Y-m-d H:i:s"),
					'cred_update_at' => date("Y-m-d H:i:s")
				);
				// $ins = $this->Credito_bzModel->insert($data);
				// $id = $this->db->insert_id();
				// if (!$ins || $this->db->insert_id() < 0) throw new Exception('El registro no se completo',99);
				// echo json_encode([
				// 	'code' => 'OK',
				// 	'log' => $ins,
				// 	'id' => $this->db->insert_id()
				// ]);

				// Generacion de cuota
				// $periodo = 'SEMANAL';
				// $ciclo = 7; 
				// if ($_POST['ciclo_pago'] == 'MENSUAL') {
				// 	$periodo = 'MENSUAL';
				// 	$ciclo = 30;
				// }
				$cuota_vence = creditou_generate_vencimiento(date("Y-m-d"),'SEMANAL');

				die(json_encode([
					'new' => $serie_new,
					'last' => $ciclo_string,
					'data' => $data
				]));


				
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
		function delete_data(){
			try {
				$this->db->trans_begin();
				if (!permission_validate('credito_eliminar')) throw new Exception('Permisos insuficientes',99);
				if (!$_POST['id']) throw new Exception('Credito no identificado');

				$credito = $this->Credito_bzModel->getById($_POST['id']);
				if ($credito->fecha_creacion !== date("d/m/Y")) throw new Exception('El credito ha sobrepasado el tiempo limite disponible para la eliminacion: '.$credito->fecha_creacion."=".date("Y-m-d"),99);

				
				$del = $this->Credito_bzModel->delete($_POST['id']);
				if (!$del || $this->db->affected_rows() <= 0) {
					throw new Exception('Proceso no completado',99);
				}
				if (intval($credito->prenda_id) > 0) {
					$this->Prenda_apiModel->update($credito->prenda_id, [
						'pren_estado' => 'D'
					]);
				}
				$this->db->trans_commit();
				echo json_encode([
					'code' => 'OK',
					'message' => 'Credito Eliminado',
					'log' => $this->db->affected_rows()
				]);
			} catch (Exception $e) {
				$this->db->trans_rollback();
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