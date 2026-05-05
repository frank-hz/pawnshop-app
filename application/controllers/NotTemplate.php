<?php 
    class NotTemplate extends CI_Controller {
        function __construct(){
            parent::__construct();
            if (!$this->session->userdata('pres_ses_sesion')) {
				redirect(base_url().'Acceso','refresh');
			}
            $this->load->model("NotTemplateModel");
            $this->load->model("Credito_bzModel");
            $this->load->model("SucursalModel");
        }
        
        function get_all(){
            $file = json_decode(file_get_contents("php://input"));
            if (empty($file->credito_id)){
                exit();
            }
            $credito = $this->Credito_bzModel->getById($file->credito_id);
            $templates = $this->NotTemplateModel->getAll($file->credito_id);
            $cuota = $credito->cuotas_atraso;
            $cliente = $credito->cliente_nombre;
            $sucursal = $this->SucursalModel->get_data($credito->sucursal_id);
            $telefono = $sucursal->telefono;
            foreach($templates as $template){
                $template->cuerpo_repl = str_replace(
                    ['_CUOTAS_', '_CLIENTE_', '_WSP_'], 
                    [$cuota." cuotas", $cliente, "https://wa.me/$telefono"], 
                    $template->cuerpo
                );
            }
            
            echo json_encode($templates);
        }

    }