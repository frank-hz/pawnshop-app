<?php
    function error_render_view($title, $message = null){
        $data['titulo'] = $title;
        $data['mensaje'] = $message;

        $CI = &get_instance();
        $CI->load->view("Layout/header.php");
        $CI->load->view("Error/no_access.php", $data);
        $CI->load->view("Layout/footer.php");
    }

?>