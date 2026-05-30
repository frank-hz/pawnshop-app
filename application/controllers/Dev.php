<?php 
    class Dev extends CI_Controller {
        function __construct(){
            parent::__construct();
            if (!$this->session->userdata('pres_ses_sesion')) {
				redirect(base_url().'Acceso','refresh');
			}
            $this->load->model('CreditoModel');
            $this->load->model('PrendaModel');
        }

        function index(){
            $this->load->view("dev/index");
        }

        
        function credito_restrigir($id = null){
            try {
                if ($id === null) {
                    response_json("No se enviaron datos.");
                    return;
                }
                
                $credito = $this->CreditoModel->getById($id, 'basic');
                if (empty($credito)) {
                    response_json("Credito no encontrado.");
                    return;
                }
                if (!in_array($credito->estado, ['R','CC','A'])){
                    response_json("El credito debe estar liquidado antes de restrigirlo.");
                    return;
                }
                $this->CreditoModel->update($id, [
                    'cred_estado' => 'HIDE'
                ]);
                if ($this->db->affected_rows() === 0) {
                    response_json("Credito no encontrado.");
                    return;
                }
                // if (!empty($credito->prenda_id)) {
                //     $this->PrendaModel->update($credito->prenda_id, [
                //         'pren_estado' => 'D'
                //     ]);
                // }

                echo json_encode([
                    'status' => 'OK',
                    'success' => true,
                    'message' => "Credito actualizado con exito."
                ], JSON_UNESCAPED_UNICODE);
                
            } catch (Exception $e) {
                $this->session->sess_destroy();
                http_response_code(500);
                echo json_encode([
                    'status' => 'ERR',
                    'message' => $e->getMessage()
                ], JSON_UNESCAPED_UNICODE);
            }
        }
    }