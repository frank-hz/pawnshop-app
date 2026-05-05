<?php 
class EmpleadoAPI extends CI_Controller {

    function __construct(){
        parent::__construct();
        $this->load->model('api/EmpleadoAPIModel');
        $this->load->model('api/UsuarioAPIModel');
        $this->load->model('api/SedeAPIModel');
        $this->load->model('api/UbigeoAPIModel');
        $this->config->load('form_validation');	        
        $this->load->helper('security');
    }
    
    // procesa y registra datos del empleado
    function create(){
        try {
            if (!$this->session->userdata('pres_ses_sesion')) {
				throw new Exception('Sesion no encontrada', 404);
			}
            if (!permission_validate('empleado_registrar')) {
				throw new Exception('Permisos insuficientes', 401);
			}

            $this->db->trans_begin();
            $validation_rules = $this->config->item('empleado_crear');
            $this->form_validation->set_rules($validation_rules);
            
            if ($this->form_validation->run() === FALSE){
                throw new Exception('Datos recibidos con errores.', 400);
            }

            $empleado_data = array(
                'emp_nombres' => trim(strtoupper($this->input->post('nombre'))),
                'emp_paterno' => trim(strtoupper($this->input->post('paterno'))),
                'emp_materno' => trim(strtoupper($this->input->post('materno'))),
                'emp_documento' => trim($this->input->post('documento')),
                'emp_tipo_documento' => $this->input->post('tipo_documento'),
                'emp_ubigeo' => $this->input->post('ubigeo'),
                'emp_direccion' => trim(strtoupper($this->input->post('direccion'))),
                'emp_telefono' => trim($this->input->post('telefono')),
                'emp_sucursal' => $this->input->post('sede'),
                'emp_email' => trim($this->input->post('email')) ?? trim(''),
                'emp_estado' => 'A'
            );

            $first_letter = substr($this->input->post('nombre'),0,1);
            $user_generate = $first_letter. "".$this->input->post('paterno');
            $pass_generate = $this->input->post('documento');

            if (!$this->EmpleadoAPIModel->insert($empleado_data)){
                throw new Exception('No se completo el registro.', 500);
            }
            $id_empleado = $this->db->insert_id();
            $usuario_data = array(
                'usu_usuario' => $user_generate,
                'usu_clave' => $pass_generate,
                'usu_empleado' => $id_empleado,
                'usu_rol' => 2,
                'usu_estado' => 'A'
            );
            if (!$this->UsuarioAPIModel->insert($usuario_data)){
                throw new Exception('No se completo el registro.', 500);
            }
            $id_usuario = $this->db->insert_id();
            $this->db->trans_commit();
            echo json_encode([
                'success' => TRUE,
                'id_empleado' => $id_empleado,
                'id_usuario' => $id_usuario,
                'message' => 'Empleado registrado con exito.'                
            ]);

            
        } catch (Exception $e) {
            $this->db->trans_rollback();

            http_response_code($e->getCode() ?: 500);
            echo json_encode([
                'success' => false, 
                'message' => $e->getMessage(),
                'errors' => $this->form_validation->error_array() ?? []
            ]);
        }

    }

    // obtiene datos de un empleado por id
    function read($id = null){
        try {
            if (!$this->session->userdata('pres_ses_sesion')) {
				throw new Exception('Sesion no encontrada', 404);
			}
            if ($id === null){
                throw new Exception('Empleado no encontrado', 400);
            }
            $data = $this->EmpleadoAPIModel->getById($id);
            echo json_encode([
                'success' => TRUE,
                'data' => $data
            ]);
        } catch (Exception $e) {
            http_response_code($e->getCode() ?: 500);
            echo json_encode([
                'success' => false, 
                'message' => $e->getMessage()
            ]);
        }
    }

    // obtiene lista de datos de empleados
    public function read_all(){
        $data = $this->input->get();
        $data = $this->EmpleadoAPIModel->getAll($data);
        echo json_encode($data);
    }

    // procesa y actualiza datos del empleado
    function update(){
        try {
            if (!$this->session->userdata('pres_ses_sesion')) {
				throw new Exception('Sesion no encontrada', 404);
			}
            if (!permission_validate('empleado_editar')) {
				throw new Exception('Permisos insuficientes', 401);
			}

            $validation_rules = $this->config->item('empleado_editar');
            $this->form_validation->set_rules($validation_rules);
            
            if ($this->form_validation->run() === FALSE){
                throw new Exception('Datos recibidos con errores.', 400);
            }

            // verificacion de documento repetido
            $id = $this->input->post('id');
            $column_doc = ['emp_documento'=> $this->input->post('documento')];
            if (!empty($this->EmpleadoAPIModel->verifyForUpdate($column_doc,$id))){
                throw new Exception('El documento ya fue registrado', 400);
            }

            $empleado_data = array(
                'emp_nombres' => trim(strtoupper($this->input->post('nombre'))),
                'emp_paterno' => trim(strtoupper($this->input->post('paterno'))),
                'emp_materno' => trim(strtoupper($this->input->post('materno'))),
                'emp_documento' => trim($this->input->post('documento')),
                'emp_tipo_documento' => $this->input->post('tipo_documento'),
                'emp_ubigeo' => $this->input->post('ubigeo'),
                'emp_direccion' => trim(strtoupper($this->input->post('direccion'))),
                'emp_telefono' => trim($this->input->post('telefono')),
                'emp_sucursal' => $this->input->post('sede'),
                'emp_email' => trim($this->input->post('email')) ?? trim(''),
                'emp_estado' => 'A'
            );
            // die(json_encode($empleado_data));
            if (!$this->EmpleadoAPIModel->update($id, $empleado_data)){
                throw new Exception('No se completo el registro.', 500);
            }

            $rows = $this->db->affected_rows();
            echo json_encode([
                'success' => TRUE,
                'message' => $rows > 0 ? 'Cambios Aplicados' : 'Sin Cambios'
            ]);
        } catch (Exception $e) {
            http_response_code($e->getCode() ?: 500);
            echo json_encode([
                'success' => false, 
                'message' => $e->getMessage(),
                'errors' => $this->form_validation->error_array() ?? []
            ]);
        }
    }

    // elimina datos del empleado
    function delete($id = null){
        try {
            if (!$this->session->userdata('pres_ses_sesion')) {
                throw new Exception('Sesion no encontrada', 404);
            }
            if (!permission_validate('empleado_eliminar')) {
                throw new Exception('Permisos insuficientes', 401);
            }
            if ($id === null){
                throw new Exception('Empleado no identificado', 400);
            }

            $rol = $this->UsuarioAPIModel->getRol($id);
            if (empty($rol) || $rol->keyname === 'ADMIN' || intval($rol->dev_work)){
                throw new Exception("Usuario no disponible para la accion.", 401);
            }

            if (!$this->EmpleadoAPIModel->delete($id)){
                throw new Exception('No se completo el proceso de eliminacion', 500);
            }

            echo json_encode([
                'success' => TRUE,
                'message' => 'Empleado eliminado con exito!'
            ]);
        } catch (Exception $e) {
            http_response_code($e->getCode() ?: 500);
            echo json_encode([
                'success' => FALSE,
                'message' => $e->getMessage()
            ]);
        }

    }

    
    function updateStatus(){
        try {
            if (!$this->session->userdata('pres_ses_sesion')) {
                throw new Exception('Sesion no encontrada', 404);
            }
            if (!permission_validate('empleado_editar')) {
                throw new Exception('Permisos insuficientes', 401);
            }

            $id = $this->input->post("id");
            $estado = $this->input->post("estado");
            if ($id === null){
                throw new Exception('Empleado no identificado', 400);
            }

            $rol = $this->UsuarioAPIModel->getRol($id);
            if (empty($rol) || $rol->keyname === 'ADMIN' || intval($rol->dev_work)){
                throw new Exception("Usuario no disponible para la accion.", 401);
            }
            if (!in_array($estado, ["A","I"])){
                throw new Exception('Valor no procesable', 400);
            }
            if (!$this->EmpleadoAPIModel->update($id, ['emp_estado' => $estado])){
                throw new Exception('No se completo el proceso de eliminacion', 500);
            }

            echo json_encode([
                'success' => TRUE,
                'message' => 'Estado actualizado con exito!'
            ]);
        } catch (Exception $e) {
            http_response_code($e->getCode() ?: 500);
            echo json_encode([
                'success' => FALSE,
                'message' => $e->getMessage()
            ]);
        }
    }


    

    // funciones callback
    function verificar_sede($id_sede) {
        if (!$this->SedeAPIModel->verifyExists($id_sede)) {
            $this->form_validation->set_message('validar_sede', 'Sede no es válida.');
            return FALSE;
        }
        return TRUE;
    }

    function verificar_ubigeo($id) {
        if (!$this->UbigeoAPIModel->verifyExists($id)) {
            $this->form_validation->set_message('validar_sede', 'Sede no es válida.');
            return FALSE;
        }
        return TRUE;
    }
}
    