<?php 
class UbigeoAPI extends CI_Controller {
    
    function __construct(){
        parent::__construct();
        $this->load->model('api/UbigeoAPIModel');
    }

    function read_all(){
        $search = $this->input->get();
        $data = $this->UbigeoAPIModel->getAllSearch($search);
        echo json_encode($data);
    }
}
