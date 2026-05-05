<?php
    class SystemConfig extends CI_Controller
    {
        private $user_id;
        private $user_rol; 
        // private $user
        function __construct(){
            parent::__construct();
            if (!$this->session->userdata('pres_ses_sesion')) {
				redirect(base_url().'Acceso','refresh');
			}
            $this->load->model('SystemConfigModel');
            $this->load->model('UsuarioModel');
            $this->load->model('ViewLoginModel');
            $this->user_id = $this->session->userdata('usuario_id');
        }

        // V I E W S
        function create_update(){
            if (!in_array($this->session->userdata('pres_ses_rol'), ['ADMIN','DEV','Administrador'])){
                render_view_error(
                    'Permisos insuficientes',
                    'El usuario actual no tiene los privilegios suficientes para esta funcion.'
                );
                return;
            }
            $data['views_login'] = $this->ViewLoginModel->getAll();
            $this->load->view("Layout/header.php");
            $this->load->view('system-config/create_update.php', $data);
            $this->load->view("Layout/footer.php");
        }




        // A P I
        function api_get(){
            $data = $this->ConfigSystemModel->get();
            echo json_encode($data);
        }
        
        function api_update(){
            try {
                $user = $this->UsuarioModel->get($this->user_id);
                
                if (empty($user)){
                    throw new Exception('Usuario no identificado', 99); 
                }
                if (!in_array($user->rol, ['Administrador','ADMIN','DEV'])){
                    throw new Exception('Permisos insuficientes', 99);     
                }
                if (empty($_POST['view'])) {
                    throw new Exception('Opcion no definida', 99);
                }
                
                $config = $this->SystemConfigModel->get();
                if ($config){
                       
                    if ($this->SystemConfigModel->update($config->id, ['view_login'=>$_POST['view']])){
                        throw new Exception('Error al actualizar datos',99);
                    }
                    $rows = $this->db->affected_rows();
                    die(json_encode([
                        'ok' => ($rows > 0) ? 'Los datos se actualizaron' : 'Sin cambios',
                        'log' => 'UPDATE_SUCCESS'
                    ]));
                }
                // die(json_encode('ad')); 
                $create = $this->SystemConfigModel->insert(
                    ['view_login'=> $_POST['view']]
                );
                if (!$create) throw new Exception('Error al guardar datos', 99);
                echo json_encode([
                    'ok' => 'Los datos se guardaron',
                    'log' => 'CREATE_SUCCESS'
                ]);
            } catch (Exception $e) {
                $code = $e->getCode();
                $mssg = $e->getMessage();
                die(json_encode([
                    'error' => ($code == 99) ? $mssg : 'Error de procesamiento',
                    'message' => 'ERROR_CATCH',
                    'log' => $mssg
                ]));
            }
        }
    }
    
    