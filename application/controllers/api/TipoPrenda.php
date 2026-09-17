<?php
class TipoPrenda extends CI_Controller {
    function __construct(){
        parent::__construct();
        if (!$this->session->userdata('logged_in')) {
            exit("Sesion no valida.");
        }
        $this->load->model("TipoPrendaModel");
    }

    function get_all(){
        try {
            // if (!verificar_permiso('TIPO_PRENDA_LISTAR')) {
			// 	response_json('Acceso Denegado!.');
			// 	return;
			// }
            if ($this->input->method() !== 'get'){
                throw new Exception("Metodo no permitido"); 
            }
            $params = [
                'nombre' => $this->input->get('nombre'),
                'orden' => $this->input->get('orden'),
            ];
            $params = array_filter($params, function($val){
                return $val !== null && $val !== '' && $val !== 0;
            });
            $result = $this->TipoPrendaModel->getAll($params);
            echo json_encode([
                'status' => 'OK',
                'success' => true,
                'message' => 'Consulta completada.',
                'result' => $result
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'status' => 'ERR',
                'message' => $e->getMessage()
            ], JSON_UNESCAPED_UNICODE);
        }
    }
    function get_by_id($id = null){
        try {
            // if (!verificar_permiso('TIPO_PRENDA_VER')) {
			// 	response_json('Acceso Denegado!.');
			// 	return;
			// }
            $id = (int) $id;
            if ($id <= 0){
                throw new Exception("Tipo no identificada.");
            }
            $data = $this->TipoPrendaModel->getById($id);
            if (!$data){
                response_json("Tipo no encontrado");
                return;
            }

            echo json_encode([
                'status' => 'OK',
                'success' => true,
                'message' => 'Consulta completada.',
                'result' => $data
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'status' => 'ERR',
                'message' => $e->getMessage()
            ], JSON_UNESCAPED_UNICODE);
        }
    }


}