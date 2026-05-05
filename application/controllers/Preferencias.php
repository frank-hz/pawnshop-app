<?php
    class Preferencias extends CI_Controller
    {
        private $user_id;
        function __construct(){
            parent::__construct();
            if (!$this->session->userdata('pres_ses_sesion')) {
				redirect(base_url().'Acceso','refresh');
			}

            header('Access-Control-Allow-Origin: *');
            $this->user_id = $this->session->userdata('usuario_id');
            $this->load->model('ViewLoginModel');
            $this->load->model('PreferenciasModel');
        }

        // V I E W S
        function create_update_form(){
            $this->load->view("Layout/header.php");
            $this->load->view('preferencias/create_update.php');
            $this->load->view("Layout/footer.php");
        }

        
        // A P I S
        function api_create_update(){
            try {
                if (empty($this->user_id)){
                    throw new Exception('usuario no identificado', 99);
                }
                if (!isset($_POST['sucursal_id'])){
                    throw new Exception('sucursal no especificada', 99);
                }

                $pref_data = $this->PreferenciasModel->getByUser($this->user_id);
                $response = [];
                if ($pref_data){
                    $update = $this->PreferenciasModel->updateByUser(
                        $this->user_id, 
                        ['sucursal_init' => $_POST['sucursal_id']]
                    );
                    if (!$update){
                        throw new Exception('Datos no actualizados', 99);
                    }
                    $rows = $this->db->affected_rows();
                    $response = [
                        'ok' => $rows > 0 ? 'Cambios aplicados' : ''
                    ];
                }else {
                    $create = $this->PreferenciasModel->insert([
                        'usuario_id' => $this->user_id,
                        'sucursal_init' => $_POST['sucursal_id']
                    ]);
                    if (!$create){
                        throw new Exception('Datos no guardados', 99);
                    }
                    $response = [
                        'ok' => 'Datos guardados'
                    ];
                }
                echo json_encode($response);
                // echo json_encode([
                //     'ok' => $rows > 0 ? 'Cambios aplicados' : '',
                //     'log' => [$update]
                // ]);                
            } catch (Exception $e) {
                $code = $e->getCode();
                $mssg = $code === 99 ? $e->getMessage() : 'ERROR_PROCESS';
                die(json_encode([
                    'error' => $mssg,
                    'log' => $e->getMessage()
                ]));
            }
        }

        function api_get_by_user(){
            $data = $this->PreferenciasModel->get($this->user_id);
            echo json_encode($data);
        }
    }
    