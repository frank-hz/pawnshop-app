<?php 


    if (!function_exists('validar_sede')){
        function validar_sede($id_sede) {
            $CI =& get_instance();
            $CI->load->model('api/SedeAPIModel');
            if (!$CI->SedeAPIModel->verifyExists($id_sede)) {
                $CI->form_validation->set_message('validar_sede', 'Sede no es válida.');
                return FALSE;
            }
            return TRUE;
        }
    }

    if (!function_exists('validar_ubigeo')){
        $CI =& get_instance();
        function validar_ubigeo($id) {
            $CI->load->model('api/UbigeoAPIModel');
            if (!$CI->UbigeoAPIModel->verifyExists($id)) {
                $CI->form_validation->set_message('validar_sede', 'Sede no es válida.');
                return FALSE;
            }
            return TRUE;
        }
    }