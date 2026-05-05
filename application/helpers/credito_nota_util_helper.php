<?php
    
    function notacr_insert_validation(){
        $config = array(                
            [
                'field' => 'credito',
                'label' => 'Credito',
                'rules' => 'required|integer'
            ],
            [
                'field' => 'descripcion',
                'label' => 'Descripcion',
                'rules' => 'required'
            ],
            [
                'field' => 'anclado',
                'label' => 'Anclaje',
                'rules' => 'required'
            ]
        );
        return $config;        
    }
    function notacr_update_validation(){
        $config = array(  
            [
                'field' => 'id',
                'label' => 'IDNota',
                'rules' => 'required|integer'
            ], 
            [
                'field' => 'descripcion',
                'label' => 'Descripcion',
                'rules' => 'required'
            ],
            [
                'field' => 'anclado',
                'label' => 'Anclaje',
                'rules' => 'required'
            ]
        );
        return $config;        
    }

    function nota_creditou_data_search_setting(){
        // $query_limit = "";
        // $query_search = "";
        // $search_stash = [];
        $query_order = "";

        $order_type = "ASC";
        $order_by = "ncred_codigo";

        $order_type_stash = ['ASC','DESC'];
        $order_field_stash = [
            'descripcion' => 'ncred_descripcion',
            'creacion' => 'ncred_fecha'
        ];

        if (!empty($_POST['order_type']) && 
            in_array(strtoupper($_POST['order_type']), $order_type_stash)){
            // $query_order .= " ".$_POST['order_type'];
            $order_type = $_POST['order_type'];
        }
        if (!empty($_POST['order_by']) && 
            array_key_exists($_POST['order_by'], $order_field_stash)) {
                // $query_order .= "ORDER BY $order_fields[$ordindx]";
            $ordindx = $_POST['order_by'];
            $order_by = $order_field_stash[$ordindx];
        }        

        $query_order = "ORDER BY $order_by $order_type";
        if (!empty($_POST['limit']) && $_POST['limit'] > 0) {
            $limit = intval($_POST['limit']);
            $query_order .= " LIMIT $limit";
        }

        // $query_limit = (!empty($_POST['limit'])) ? 'limit '.$_POST['limit'] : '';
        return "$query_order";

    }

?>