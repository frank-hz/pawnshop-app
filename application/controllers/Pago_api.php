<?php
    class Pago_api extends CI_Controller
    {
        function __construct(){
			parent::__construct();
			if (!$this->session->userdata('pres_ses_sesion')) {
				redirect(base_url().'Acceso','refresh');
			}
			$this->load->model('PagoModel');
            $this->load->model('Pago_bzModel');
            $this->load->helper('pago_util');
		}
        
        // Rework 05-2023
		function get_by_credito(){
            try {
                if (empty($_POST['idcredito'])) throw new Exception('Credito no identificado',99);

                $setts = [];
                if (!empty($_POST['orden_tipo'])) $setts['orden_type'] = $_POST['orden_tipo'];
                if (!empty($_POST['orden_objetivo'])) $setts['orden_target'] = $_POST['orden_objetivo'];                
                $pagos = $this->Pago_bzModel->getByCredito($_POST['idcredito'], $setts);
                
                if (!empty($pagos)) {
                    foreach ($pagos as $pago) {
                        $pago->url = base_url()."pago/detalle/".$pago->id;
                        $pago->forma_pago = recognize_forma_pago($pago->forma_pago_raw);
                        $pago->concepto = recognize_concepto($pago->concepto_raw);
                    }
                }
                echo json_encode([
                    'code' => 'OK',
                    'result' => $pagos,
                    'message' => 'proceso completado'
                ]);

            } catch (Exception $e) {
                $code = $e->getCode();
				$mssg = $e->getMessage();
				die(json_encode([
					'code' => 'ERR',
					'message' => ($code==99) ? $mssg : 'Error al procesar',
					'log' => $e->getMessage()
				]));
            }
        }     
        function get_data(){
            $data = $this->CuotaModel->getData($_POST['id']);
            echo json_encode($data);
        }
    }
    
?>