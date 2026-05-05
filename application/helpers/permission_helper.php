<?php 

if ( !function_exists('get_user_details')){
    function permission_validate($action){
        $CI =& get_instance();
        $CI->load->model('PermisoModel');
        $uid = $CI->session->userdata('pres_ses_usuario');
        $user = $CI->PermisoModel->getUserData($uid);
        if (!$user || $user->estado != 'A') return;
        if (in_array($user->rol_keyname,['ADMIN','OWNER'])) return true;

        $prm = $CI->PermisoModel->getByUserAction($uid, $action);
        return $prm;
    }
}

?>