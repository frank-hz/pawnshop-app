<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends CI_Controller {
    function __construct(){
        parent::__construct();
        $this->load->model("AuthModel");
        $this->load->model("UsuarioModel");
        $this->load->model("MenuModel");
        // $this->load->model("PermitsModel");
        // $this->load->model("RoleUsuarioModel");
        // $this->load->model("ModulesModel");
        // $this->load->library("Permission");
    }

    function login(){
        try {
            // echo "hi";
            if ($this->session->userdata('logged_in')) {
                response_json("Ya hay una sesion activa.");
                return;
            }

            $username = trim($this->input->post('username', TRUE));
            $password = trim($this->input->post('password', TRUE));

            if (empty($username) || empty($password)) {
                response_json("Credenciales incompletas.");
                return;
            }

            $user = $this->AuthModel->validate_user($username);
            if (!$user) {
                response_json("Usuario o contraseña incorrectos.");
                return;
            }
            if ($password!=$user->clave && !password_verify($password, $user->password)) {
                response_json("Usuario o contraseña incorrectos.");
                return;
            }
            
            if (password_needs_rehash($user->clave, PASSWORD_DEFAULT)) {
                $new_hash = password_hash($password, PASSWORD_DEFAULT);
                $this->UsuarioModel->update($user->id, ['usu_clave' => $new_hash]);
            }

            $is_dev = (bool) $user->dev;
            $menu = ($is_dev || $user->rol_nombre === 'Administrador') 
                ? $this->MenuModel->get_data_modules_admin()
                : $this->MenuModel->get_data_modules($user->id);

            $nombres = $user->nombres;
            $paterno = $user->paterno;
            $acronimo = strtoupper(substr($nombres, 0, 1). substr($paterno, 0, 1));
            $this->load->model('SucursalModel');
            $sucursal_default = 1;
            $sucursal = $this->SucursalModel->get_by_id($sucursal_default);
            
            $sesion = [
                'usuario_id'        => $user->id,
                'empleado_id'       => $user->empleado_id,
                'nombre_full'       => $user->nombre_apellido,
                'nombre_short'      => $user->nombres.' '.$user->paterno,
                'rol_id'            => $user->id,
                'rol_nombre'        => $user->rol_nombre,
                'dev'               => (bool) $user->dev,

                'sucursal_id'       => $sucursal->id,
                'sucursal_nombre'   => $sucursal->nombre,
                'sucursal_serie'    => $sucursal->serie,

                'pres_ses_usuario'  => $user->id,
                'pres_ses_empleado' => $user->empleado_id,
                'pres_ses_rol'      => $user->rol_nombre,
                'pres_ses_sucursal' => $sucursal->id,
                'pres_ses_sede'     => $sucursal->nombre,
                'pres_ses_acronimo' => $acronimo,
                'pres_ses_imagen'   => $user->imagen,
                'pres_acceso_fecha' => date("Y-m-d"),
                'pres_ses_sesion'   => true,
                'logged_in'         => true,

                'pres_ses_menu'     => $menu ?? [],
            ];
            $this->session->set_userdata($sesion);


            $nombre_saludo = "{$user->nombres} {$user->paterno}";
            echo json_encode([
                'status' => 'OK',
                'success' => true,
                'message' => "Bienvenido <b>".$nombre_saludo."</b>"
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
    
    public function logout() {
        $this->session->sess_destroy();
        if ($this->input->is_ajax_request()) {
            $this->_send_response(['message' => 'Sesión cerrada'], 200);
        } else {
            redirect('/');
            // echo "adsdas";
        }
    }

}
?>