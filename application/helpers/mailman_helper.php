<?php 
    
function MailmanEnviarCorreo($correo_destino, $titulo, $cuerpo){
    $CI =& get_instance();
    $CI->load->library('email');
    $config['mailtype'] = 'html'; 
    $config['charset']  = 'utf-8';
    // $CI->email->initialize($config);
    
    // $CI->email->from('contacto@electrocashperu.com', 'ElectrocashPerú');
    $CI->email->from('notificaciones@electrocashperu.com', 'ElectrocashPerú');
    $CI->email->to($correo_destino);
    $CI->email->subject($titulo);
    $CI->email->message($cuerpo);

    return $CI->email->send();
}