<?php 


    function render_view_error($title, $message = ''){
        $CI =& get_instance();
        $data['titulo'] = $title;
        $data['mensaje'] = $message;
        $CI->load->view("Layout/header.php");
        $CI->load->view("Error/no_access.php", $data);
        $CI->load->view("Layout/footer.php");
    }