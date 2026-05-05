<?php

    class RemateAPI extends CI_Controller {
        function __construct(){
            parent::__construct();
            
            // if (!$this->session->userdata('pres_ses_sesion')) {
			// 	redirect(base_url().'Acceso','refresh');
			// }
            header("Access-Control-Allow-Origin: *");
            $this->load->model('RemateAPIModel');
            $this->load->helper('prenda_util');
        }

        function prendas_get_all(){
            $sucursal = $this->session->userdata('pres_ses_sucursal') || 1;
            $prendas = $this->RemateAPIModel->getPrendaList($sucursal);
            echo json_encode([
                'ok' => 'proceso completado',
                'result' => $prendas
            ]);
        }    
        function prenda_get_data(){
            $data_received = json_decode(file_get_contents("php://input"));
            if (empty($data_received->id)) {
                die(json_encode([
                    'error' => 'prenda no identificada'
                ]));
            }
            $data = $this->RemateAPIModel->getPrendaData($data_received->id);
            echo json_encode([
                'success' => 'proceso completado',
                'result' => $data
            ]);
        }
        function prenda_update_data(){
            try {
                $data = json_decode(file_get_contents('php://input'));
                if (empty($data)){
                    throw new Exception("Datos no recibidos",99);
                }

                $data_upd = $this->get_fields_to_update($data);
                if (empty($data->id) || empty($data_upd)){
                    throw new Exception("Datos incompletos",99);
                }

                $update = $this->RemateAPIModel->updateAmountsPrenda(
                    $data->id, $data_upd
                );
                $rows_changed = $this->db->affected_rows();
                echo json_encode([
                    "ok" => ($rows_changed > 0) ? "Cambios aplicados" : "Sin cambios"                    
                ]);
            } catch (Exception $e) {
                $code = $e->getCode();
                $mssg = $e->getMessage();
                echo json_encode([
                    'error' => ($code==99) ? $mssg : 'Error al procesar',
                    'log' => $e->getMessage()
                ]);
            }
        }


        // util
        function get_fields_to_update($data){
            $update_array = [];
            if (is_numeric($data->precio_inicial)) {
                $update_array['pren_precio_inicial'] = $data->precio_inicial;
            }
            if (is_numeric($data->costo_reparacion)) {
                $update_array['pren_costo_reparacion'] = $data->costo_reparacion;
            }
            if (is_numeric($data->costo_total)) {
                $update_array['pren_costo_total'] = $data->costo_total;
            }
            if (is_numeric($data->precio_venta)) {
                $update_array['pren_precio_venta'] = $data->precio_venta;
            }
            return $update_array;
        }


    }
?>