<?php 
    class PagoAPI extends CI_Controller
    {
        public function __construct(){
            parent::__construct();
            $this->load->model('PagoAPIModel');
        }


        function create(){
            ini_set('display_errors', 0);
            $id_sede = $this->session->userdata('pres_ses_sucursal');
            $id_usuario = $this->session->userdata('pres_ses_usuario');

            $permiso = $this->PermisoModel->verificar_permiso(
				$this->session->userdata('pres_ses_usuario'),
				'pago_registrar'
			);
            // $validacion = $this
            

            
        }
        

    }
    