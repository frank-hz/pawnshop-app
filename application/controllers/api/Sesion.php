<?php
class Sesion extends CI_Controller {
    function __construct(){
        parent::__construct();
        $this->load->model('UsuarioModel');
    }

    function profile_update(){
        try {
            $id_sesion = (int) $this->session->userdata('usuario_id');
            if (!$id_sesion) {
                response_json("Sesion no encontrada");
                return;
            }
            
            $username = $this->input->post("username", TRUE);
            $password_current = $this->input->post("username_current", TRUE);
            $password_new = $this->input->post("username_new", TRUE);

            $user = $this->UsuarioModel->getById($id_sesion);
            $verify_password = !password_verify(
                $password_current,
                $user->password
            );
            if (!$user || !$verify_password) {
                response_json("La contraseña actual es incorrecta.");
                return;
            }

            $update_data = [];
            if (!empty($username)) {
                $update_data['usu_usuario'] = $username;
            }
            if (!empty($password_new)) {
                $update_data['usu_clave'] = password_hash(
                    $password_new, PASSWORD_DEFAULT
                );
            }

            // Resp: sin cambios que aplicar
            if (empty($update_data)) {
                response_json("No hay cambios que aplicar.");
                return;
            }

            // actualizar
            $update = $this->UsuarioModel->update(
                $id_sesion, $update_data
            );
            if (!$update) {
                throw new Exception("Error al actualizar.");
            }

            $rows = $this->db->affected_rows();
            echo json_encode([
                'success' => TRUE,
                'status' => 'OK',
                'message' => $rows>0 ? 'Datos guardados' : 'Sin cambios',
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