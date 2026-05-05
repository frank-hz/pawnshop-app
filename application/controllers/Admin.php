<?php
    class Admin extends CI_Controller
    {
        function __construct(){
            parent::__construct();
            // if (!$this->session->userdata('pres_ses_sesion')) {
			// 	redirect(base_url().'Acceso','refresh');
			// }
            $this->load->model('AdminModel');
            $this->load->model('UsuarioModel');
            $this->load->model('EmpleadoModel');
        }

        function index($cid = null){            
            if (isset($_SESSION['pres_ses_sesion'])) {
                if ($_SESSION['pres_ses_rol'] != 'Administrador') return;
                $this->load->view('Layout/header');
                $this->load->view('Admin/index');
                $this->load->view('Layout/footer');
            }

        }

        function uploader(){
            $this->load->view("tester/dropbox_upload");
        }

        // Encode: 05-05-23
        function users($code = null){
            $this->load->helper('encrypter');
            // if ($code === null || !encrypt_compare($code)) {
            //     return;
            // }
            $codecrypt = encrypt_compare($code);
            $permission = $this->session->userdata('pres_ses_rol') == 'Administrador';
            
            if ($permission || $codecrypt) {
                $data['users'] = $this->UsuarioModel->get_all_toadmin();
                $this->load->view('Layout/header');
                $this->load->view('Admin/users', $data);
                $this->load->view('Layout/footer');
            }else {
                return;
            }


        }
        function service_user_encrypt(){
            try {
                $d = file_get_contents('php://input');
                $d = json_decode($d);
                if (!$d || empty($d->id)) {
                    throw new Exception('Usuario no identificado.');
                }

                $data = $this->UsuarioModel->get_data_full($d->id);
                $newpass = password_hash($data->clave, PASSWORD_DEFAULT);
                $upd = $this->UsuarioModel->Modificar($data->id, [
                    'usu_clave' => $newpass
                ]);
                $ar = $this->db->affected_rows();
                echo json_encode([
                    'status' => 'OK',
                    'message' => ($ar) ? 'Cambios Aplicados.' : 'Sin Cambios'
                ]);

            } catch (Exception $e) {
                die([
                    'status' => 'ERR',
                    'message' => $e->getMessage()
                ]);
            }
        }
        function service_user_reset(){
            try {
                $d = file_get_contents('php://input');
                $d = json_decode($d);
                if (!$d || empty($d->id)) {
                    throw new Exception('Usuario no identificado.');
                }
                $data = $this->UsuarioModel->get_data_full($d->id);
                // $newpass = password_hash($data->documento, PASSWORD_DEFAULT);
                $newpass = $data->documento;
                
                $upd = $this->UsuarioModel->Modificar($data->id, [
                    'usu_clave' => $newpass
                ]);
                $afr = $this->db->affected_rows();
                echo json_encode([
                    'status' => 'OK',
                    'message' => ($afr) ? 'Reinicio Aplicado.' : 'Sin Cambios'
                ]);
            } catch (Exception $e) {
                die([
                    'status' => 'ERR',
                    'message' => $e->getMessage()
                ]);
            }
            
        }
        function service_user_set_state(){
            try {
                $d = file_get_contents('php://input');
                $d = json_decode($d);
                if (!$d || empty($d->id)) {
                    throw new Exception('Usuario no identificado.');
                }
                if (empty($d->estado)) {
                    throw new Exception('Parametros no recibidos.');
                }
                if (!in_array($d->estado, ['A','I'])) {
                    throw new Exception('Estado invalido.');
                }
                $upd = $this->EmpleadoModel->Modificar($d->id, [
                    'emp_estado' => $d->estado
                ]);
                $afr = $this->db->affected_rows();
                echo json_encode([
                    'status' => 'OK',
                    'message' => ($afr) ? 'Cambios Aplicados.' : 'Sin Cambios'
                ]);
            } catch (Exception $e) {
                die([
                    'status' => 'ERR',
                    'message' => $e->getMessage()
                ]);
            }
            
        }


        // 
        function service_remate_ordenamiento(){
            try {
                $this->db->trans_begin();
                $this->load->model('RemadevModel');
                $this->load->model('PrendaModel');
                $file = file_get_contents("php://input");
                $data = json_decode($file);
                if ($data->user && $data->user != $this->session->userdata('pres_ses_usuario')) {
                    throw new Exception('Sesion actual invalida');
                }
                $creditos = $this->RemadevModel->get_creditos_remate();
                $recibidos = count($creditos);
                $procesados = 0;
                $procesado_stash = [];

                
                foreach ($creditos as $c) {
                    $upd_obj = [
                        $c->monto,
                        $c->monto,
                        $c->liquidacion,
                        date('0000-00-00 00:00:00'),
                        $c->id,
                        $c->prenda
                    ];
                    $this->RemadevModel->update_prenda($upd_obj);
                    if ($this->db->affected_rows() > 0) {
                        $procesados++;
                    }
                }
                $this->db->trans_commit();
                echo json_encode([
                    'code' => 'OK',
                    'message' => 'Procesamiento Completado.',
                    'log' => "Recibidos: $recibidos - Procesados: $procesados"
                ]);
            } catch (PDOException $e) {
                $this->db->trans_rollback();
                die(json_encode([
                    'code' => 'ERROR',
                    'message' => 'Error al Procesar.',
                    'log' => $e->getMessage(),
                    'recibe' => $recibidos,
                    'procesa' => $procesados
                ]));
            }
            
        }
        function service_usuario_encriptacion(){
            try {
                $this->db->trans_begin();
                $file = file_get_contents("php://input");
                $data = json_decode($file);
                if ($data->user && $data->user != $this->session->userdata('pres_ses_usuario')) {
                    throw new Exception('Sesion actual invalida');
                }
                $usuarios = $this->AdminModel->get_data_usuarios();
                $news_password = [];
                $cont = 0;
                foreach ($usuarios as $ind => $us) {
                    $init_vector = base64_decode("C9fBxl1EWtYTL1/M8jfstw==");
                    $method = 'aes-256-cbc';
                    $key = "encrypt2049";
                    $encrp = openssl_encrypt($us->clave, $method, $key, false, $init_vector);
                    $dencrp = openssl_decrypt($encrp, $method, $key, false, $init_vector);
                    array_push($news_password, [
                        'original' => $us->clave,
                        'encriptado' => $encrp,
                        'desencriptado' => $dencrp
                    ]);
                    if(!$this->AdminModel->update_user_pass($us->id, $encrp)) {
                        throw new Exception('Error al actualizar ['.$ind.']');
                    }
                    $cont++;
                }
                $this->db->trans_commit();
                echo json_encode([
                    'code' => 'OK',
                    'message' => 'Procesamiento Completado: ['.$cont.']',
                    'log' => $news_password
                ]);

            } catch (PDOException $e) {
                $this->db->trans_rollback();
                die(json_encode([
                    'code' => 'ERROR',
                    'message' => 'Error al Procesar.',
                    'log' => $e->getMessage()
                ]));
            }
        }
        function service_encrypter(){
            // $file = file_get_contents("php://input");
            // $file = json_decode($file);

            $init_vector = base64_decode("C9fBxl1EWtYTL1/M8jfstw==");
            $method = 'aes-256-cbc';
            $key = "encrypt2049";

            if (!empty($_POST['value']) && !empty($_POST['action'])) {
                if ($_POST['action'] === 'ENCRPT') {
                    $res = openssl_encrypt($_POST['value'], $method, $key, false, $init_vector);
                }else if($_POST['action'] === 'DCRPT'){
                    $res = openssl_decrypt($_POST['value'], $method, $key, false, $init_vector);
                }
                echo json_encode($res);
            }            
        }
       



        function info(){            
            if (isset($_SESSION['pres_ses_sesion'])) {
                if ($_SESSION['pres_ses_rol'] != 'Administrador') return;
                $this->load->view('Layout/header');
                $this->load->view('Admin/info');
                $this->load->view('Layout/footer');
            }

        }

    }
    
?>