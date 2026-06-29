<?php
class Categoria extends CI_Controller {
    function __construct(){
        parent::__construct();
        $this->load->model("CategoriaModel");
    }

    function get_all(){
        try {
            // if (!verificar_permiso('CATEGORIA_LISTAR')) {
			// 	response_json('Acceso Denegado!.');
			// 	return;
			// }
            if ($this->input->method() !== 'get'){
                throw new Exception("Metodo no permitido"); 
            }
            $params = [
                'nombre' => $this->input->get('nombre'),
                'tipo' => (int) $this->input->get('tipo'),
                'tipo_nombre' => $this->input->get('tipo_nombre'),
                'orden' => $this->input->get('orden'),
            ];
            $params = array_filter($params, function($val){
                return $val !== null && $val !== '' && $val !== 0;
            });
            $result = $this->CategoriaModel->getAll($params);
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
    function get_all_by_tipo(){
        try {
            if (!verificar_permiso('CATEGORIA_LISTAR')) {
				response_json('Acceso Denegado!.');
				return;
			}
            if ($this->input->method() !== 'post'){
                throw new Exception("Metodo no permitido"); 
            }
            $tipo_id = (int) $this->input->post("tipo_id", TRUE);
            if (empty($tipo_id)){
                throw new Exception("Tipo de prenda no identificado.");
            }
            $result = $this->CategoriaModel->getAll(['tipo' => $tipo_id]);
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
            if (!verificar_permiso('CATEGORIA_VER')) {
				response_json('Acceso Denegado!.');
				return;
			}
            $id = (int) $id;
            if ($id <= 0){
                throw new Exception("Prenda no identificada.");
            }
            $data = $this->CategoriaModel->getById($id);
            if (!$data){
                response_json("Categoria no encontrada");
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
    function create(){
        try {
            if (!$this->session->userdata('pres_ses_sesion')) {
				throw new Exception('Sesion no encontrada', 404);
			}
            // if (!verificar_permiso('CATEGORIA_CREAR')) {
			// 	response_json('Acceso Denegado!.');
			// 	return;
			// }
            if ($this->input->method() !== 'post'){
                throw new Exception("Metodo no permitido"); 
            }
            
            // $validation_rules = $this->config->item('categoria_create');
            // $this->form_validation->set_rules($validation_rules);
            // if ($this->form_validation->run() == FALSE){
            //     $errors = $this->form_validation->error_array();
            //     $errors = validation_errors();
            //     response_json("Datos invalidos", ["errors" => $errors]);
            //     return;
            // }

            $nombre = mb_strtoupper($this->input->post('nombre', TRUE));
            $tipo_id = (int) $this->input->post('tipo_prenda', TRUE);
            $categoria_data = [
                'cat_nombre' => $nombre,
                'descripcion' => $this->input->post('descripcion', TRUE),
                'cat_tipo' => $tipo_id,
                'cat_estado' => 'A'
            ];

            $duplicado = $this->CategoriaModel->EsDuplicado($categoria_data);
            if ($duplicado){
                response_json("Registro duplicado: la operación fue cancelada.", ['status' => 'ERR']);
                return;
            }

            $create = $this->CategoriaModel->insert($categoria_data);
            if (!$create){
                throw new Exception("No se completo el registro.");
            }

            $prenda_id = $this->db->insert_id();
            echo json_encode([
                'status' => 'OK',
                'success' => true,
                'message' => 'Datos registrados con exito.',
                'id' => $prenda_id
            ], JSON_UNESCAPED_UNICODE);
            return;
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'status' => 'ERR',
                'message' => $e->getMessage()
            ], JSON_UNESCAPED_UNICODE);
        }
    }
    function update(){
        try {
            if (!verificar_permiso('CATEGORIA_EDITAR')) {
				response_json('Acceso Denegado!.');
				return;
			}
            if ($this->input->method() !== 'post'){
                throw new Exception("Metodo no permitido"); 
            }

            $id = (int) $this->input->post('id', TRUE);
            if (empty($id)){
                throw new Exception("Categoria no identificada.");
            }

            $nombre = mb_strtoupper($this->input->post('nombre', TRUE));
            $tipo_id = (int) $this->input->post('tipo_prenda', TRUE);
            $categoria_data = [
                'cat_nombre' => $nombre,
                'descripcion' => mb_strtoupper($this->input->post('descripcion', TRUE)),
                'cat_tipo' => $tipo_id,
                'cat_estado' => $this->input->post('estado', TRUE)
            ];            

            // Limpiar elementos nulos o vacios
            $categoria_data = array_filter($categoria_data, function($val){
                return $val !== null && $val !== '';
            });

            $duplicado = $this->CategoriaModel->EsDuplicado($categoria_data, $id);
            if ($duplicado){
                response_json("Registro duplicado: la operación fue cancelada.", ['status' => 'ERR']);
                return;
            }

            $this->CategoriaModel->update($id, $categoria_data);
            $rows = (int) $this->db->affected_rows(); 
            echo json_encode([
                'status' => 'OK',
                'success' => true,
                'message' => ($rows > 0) ? 'Datos guardados.' : 'Sin cambios.'
            ], JSON_UNESCAPED_UNICODE);
            return;
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'status' => 'ERR',
                'message' => $e->getMessage()
            ], JSON_UNESCAPED_UNICODE);
        }
    }
    function delete(){
        try {
            if (!verificar_permiso('CATEGORIA_ELIMINAR')) {
				response_json('Acceso Denegado!.');
				return;
			}
            if ($this->input->method() !== 'post'){
                response_json('Metodo no permitido!.', ['status' => 'ERR']);
                return;
            }

            $id = (int) $this->input->post('id', TRUE);
            if (empty($id)){
                response_json('Categoria no identificada.', ['status' => 'ERR']);
                return;
            }

            $restriccion = $this->CategoriaModel->countPrendasByCategoria($id);
            if ($restriccion > 0){
                response_json('Eliminacion no permitida.', ['status' => 'ERR']);
                return;
            }

            if (!$this->CategoriaModel->delete($id)){
                throw new Exception("Error al eliminar categoria.");
            }
            $categoria = $this->CategoriaModel->getById($id);
            if (!empty($categoria)){
                throw new Exception("No se pudo eliminar el registro.");
                return;
            }

            echo json_encode([
                'status' => 'OK',
                'success' => true,
                'message' => 'Registro eliminado con exito.'
            ], JSON_UNESCAPED_UNICODE);

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'status' => 'ERR',
                'message' => $e->getMessage()
            ], JSON_UNESCAPED_UNICODE);
        }
    }


}