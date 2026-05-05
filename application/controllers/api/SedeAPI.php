<?php  

class SedeAPI extends CI_Controller {
    function __construct(){
        parent::__construct();
        $this->load->model('api/SedeAPIModel');
    }
    
    function bs_read_all(){
        $setts = $this->input->get();
        $data = $this->SedeAPIModel->basic_getAll($setts);
        echo json_encode($data);
    }
}
