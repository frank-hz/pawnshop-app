<?php
    class ViewLogin extends CI_Controller
    {
        function __construct(){
            parent::__construct();
            if (!$this->session->userdata('pres_ses_sesion')) {
				redirect(base_url().'Acceso','refresh');
			}
            $this->load->model('ViewLoginModel');
        }     
        


        // A P I
        function api_get(){
            $id = $_POST['id'];
        }

        function api_get_all(){
            $data = $this->ViewLoginModel->getAll();
            echo json_encode($data);
        }

        function api_create(){

        }

        function api_update(){

        }

        function api_delete(){

        }

    }
    