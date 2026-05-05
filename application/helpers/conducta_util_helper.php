<?php
    function conductau_insert_validation(){
        $config = array(    
            [
                'field' => 'idcliente',
                'label' => 'Cliente',
                'rules' => 'required|integer'
            ]
        );
        return $config;        
    }
    function conductau_update_validation(){
        $config = array(    
            [
                'field' => 'id',
                'label' => 'IdConducta',
                'rules' => 'required|integer'
            ]
        );
        return $config;        
    }

    function conductau_prepare_data_update(){
        $data = [];
        $scores = ['A','B','C','-','N'];
        $pd = $_POST;
        if (!empty($pd['puntuacion']) && in_array($pd['puntuacion'],$scores)) {
            $data['cli_puntuacion'] = strtoupper($pd['puntuacion']);
        }
        if (!empty($pd['bloqueo_atencion'])) {
            $data['cli_bloqueo_atencion'] = $pd['bloqueo_atencion'];            
            if ($pd['bloqueo_atencion'] == 'SI'){
                $data['cli_puntuacion'] = 'C';
                $data['cli_bloqueo_motivo'] = (!empty($pd['bloqueo_motivo'])) ? $pd['bloqueo_motivo'] : '';
            }
        }
        // $data['cli_bloqueo_atencion'] = (!empty($pd['bloqueo_atencion'])) ? $pd['bloqueo_atencion'] : '';
        //     $data['cli_bloqueo_motivo'] = (!empty($pd['bloqueo_motivo'])) ? $pd['bloqueo_motivo'] : '';
        return $data;
    }

?>