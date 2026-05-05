<?php
    class Cuota_api extends CI_Controller
    {
        function __construct(){
			parent::__construct();
			if (!$this->session->userdata('pres_ses_sesion')) {
				redirect(base_url().'Acceso','refresh');
			}
			$this->load->model('CuotaModel');
            $this->load->helper('cuota_util');
            // cuotau_tag_estado
		}
        
        // Rework 05-2023
		function get_by_credito(){
            try {
                if (empty($_POST['idcredito'])) throw new Exception('Credito no identificado',99);

                $setts = [];
                if (!empty($_POST['orden_tipo'])) $setts['orden_type'] = $_POST['orden_tipo'];
                if (!empty($_POST['orden_objetivo'])) $setts['orden_target'] = $_POST['orden_objetivo'];                
                $cuotas = $this->CuotaModel->getByCredito($_POST['idcredito'], $setts);
                if (!empty($cuotas)) {
                    foreach ($cuotas as $cuota) {
                        $cuota->estado_tag = cuotau_tag_estado($cuota->estado);
                    }
                }
                echo json_encode([
                    'code' => 'OK',
                    'result' => $cuotas,
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