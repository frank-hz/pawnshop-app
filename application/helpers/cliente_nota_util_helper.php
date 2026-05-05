<?php

    function notaclienteu_data_search_setting(){
        $query_limit = "";
        $query_order = "";
        $query_search = "";
        $search_stash = [];

        $order_types = ['ASC','DESC'];
        $order_fields = [
            'descripcion' => 'ncli_descripcion',
            'creacion' => 'ncli_fecha'
        ];

        if (!empty($_POST['order_by']) && 
            array_key_exists($_POST['order_by'], $order_fields)) {
            $ordindx = $_POST['order_by'];
            $query_order .= "ORDER BY $order_fields[$ordindx]";
        }
        if (!empty($_POST['order_type']) && 
            in_array(strtoupper($_POST['order_type']), $order_types)){
            $query_order .= " ".$_POST['order_type'];
        }

        $query_limit = (!empty($_POST['limit'])) ? 'limit '.$_POST['limit'] : '';

        // if (!empty($_POST['w_estado'])) {
        //     array_push($search_stash, 'ncli_estado = '.$_POST['w_estado']);
        // }
        // if (!empty($_POST['w_creacion'])) {
        //     array_push($search_stash, 'ncli_creacion = '.$_POST['w_creacion']);
        // }        
        // if (!empty($search_stash)) { 
        //     for ($i=0; $i < count($search_stash); $i++) { 
        //         $query_search .= $search_stash[$id];
        //     }
        // }
        return "$query_order $query_limit";
    }
?>