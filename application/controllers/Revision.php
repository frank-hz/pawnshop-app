<?php
    class Revision extends CI_Controller
    {
        function __construct(){
            parent::__construct();
			if (!$this->session->userdata('pres_ses_sesion')) {
				redirect(base_url().'Acceso','refresh');
			}
            $this->load->model('Credito_bzModel');
            $this->load->helper('revision_util');
        } 
        function revision_unitaria($id){
            $credito = $this->Credito_bzModel->getById($id);
            // revisionu_verificacion_cuotas($credito);
        }
        function revision_sede(){

        }
        function revision_completa(){

        }

        function generador_cuotas(){

        }

        // function estimacion_
    }
    
?>