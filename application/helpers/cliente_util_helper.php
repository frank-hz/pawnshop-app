<?php
    function clienteu_insert_validation(){
        $config = array(    
            [
                'field' => 'documento',
                'label' => 'Documento',
                'rules' => 'required|min_length[8]|max_length[20]'
            ],           
            [
                'field' => 'tipo_documento',
                'label' => 'TipoDocumento',
                'rules' => 'required|max_length[20]'
            ],
            [
                'field' => 'nombre',
                'label' => 'Nombre',
                'rules' => 'required|max_length[20]'
            ],
            [
                'field' => 'paterno',
                'label' => 'Ape. Paterno',
                'rules' => 'required|max_length[20]'
            ],
            [
                'field' => 'materno',
                'label' => 'Ape. Materno',
                'rules' => 'required|max_length[20]'
            ],
            [
                'field' => 'ubigeo',
                'label' => 'Ciudad/Distrito',
                'rules' => 'required|integer'
            ],
            [
                'field' => 'telefono',
                'label' => 'Telefono',
                'rules' => 'required|max_length[100]'
            ]
        );
        return $config;        
    }

    function clienteu_update_validation(){
        $config = array(  
            [
                'field' => 'id',
                'label' => 'Cliente',
                'rules' => 'required|integer'
            ],
            [
                'field' => 'documento',
                'label' => 'Documento',
                'rules' => 'required|min_length[8]|max_length[20]'
            ],           
            [
                'field' => 'tipo_documento',
                'label' => 'TipoDocumento',
                'rules' => 'required'
            ],
            [
                'field' => 'nombre',
                'label' => 'Nombre',
                'rules' => 'required|max_length[50]'
            ],
            [
                'field' => 'paterno',
                'label' => 'Ape. Paterno',
                'rules' => 'required|max_length[50]'
            ],
            [
                'field' => 'materno',
                'label' => 'Ape. Materno',
                'rules' => 'required|max_length[50]'
            ],
            [
                'field' => 'ubigeo',
                'label' => 'Ciudad/Distrito',
                'rules' => 'required|integer'
            ],
            [
                'field' => 'telefono',
                'label' => 'Telefono',
                'rules' => 'required|max_length[100]'
            ]
        );
        return $config;        
    }

    function clienteu_prepare_data_update(){
        $data = [];
        $pd = $_POST;
        if (!empty($pd['documento'])) $data['cli_documento'] = $pd['documento'];
        if (!empty($pd['tipo_documento'])) $data['cli_tipo_documento'] = $pd['tipo_documento'];
        if (!empty($pd['nombre'])) $data['cli_nombres'] = mb_strtoupper($pd['nombre']);
        if (!empty($pd['paterno'])) $data['cli_paterno'] = mb_strtoupper($pd['paterno']);
        if (!empty($pd['materno'])) $data['cli_materno'] = mb_strtoupper($pd['materno']);
        
        if (!empty($pd['ubigeo'])) $data['cli_ubigeo'] = $pd['ubigeo'];
        if (!empty($pd['direccion'])) $data['cli_direccion'] = mb_strtoupper($pd['direccion']);
        if (!empty($pd['telefono'])) $data['cli_telefono'] = $pd['telefono'];
        if (!empty($pd['email'])) $data['cli_email'] = $pd['email'];

        return $data;
    }

    function clienteu_prepare_data_insert(){
        $data = [];
        $pd = $_POST;       
        if (!empty($pd['documento'])) $data['cli_documento'] = $pd['documento'];
        if (!empty($pd['tipo_documento'])) $data['cli_tipo_documento'] = $pd['tipo_documento'];
        if (!empty($pd['nombre'])) $data['cli_nombres'] = mb_strtoupper($pd['nombre']);
        if (!empty($pd['paterno'])) $data['cli_paterno'] = mb_strtoupper($pd['paterno']);
        if (!empty($pd['materno'])) $data['cli_materno'] = mb_strtoupper($pd['materno']);
        
        if (!empty($pd['ubigeo'])) $data['cli_ubigeo'] = $pd['ubigeo'];
        if (!empty($pd['direccion'])) $data['cli_direccion'] = mb_strtoupper($pd['direccion']);
        if (!empty($pd['telefono'])) $data['cli_telefono'] = $pd['telefono'];
        if (!empty($pd['email'])) $data['cli_email'] = $pd['email'];
        
        return $data;
    }

   
    function clienteu_query_order(){
        $valid_fields = ['codigo','documento','nombres','paterno','materno'];
        $order_by = (!empty($_POST['orden_by'])) ? $_POST['orden_by'] : '';
        $order_by = (in_array($order_by, $valid_fields)) ? 'cuo_'.$order_by : 'cli_codigo';
        $order_type = (!empty($_POST['orden_type'])) ? $_POST['orden_type'] : '';
        $order_type = (in_array($order_type,['ASC','DESC'])) ? $order_type : 'ASC';
        
        $limit = (!empty($_POST['limit'])) ? "LIMIT ".$_POST['limit'] : 'LIMIT 30';

        return "ORDER BY $order_by $order_type $limit";
    }
    function clienteu_query_conditions(){
        $query = "";
        if (!empty($_POST['search_quick'])) {
            $quick_val = $_POST['search_quick'];
            $query .= "
                cli_nombres LIKE CONCAT('$quick_val','%') OR 
                cli_paterno LIKE CONCAT('$quick_val','%') OR 
                cli_materno LIKE CONCAT('$quick_val','%') OR 
                cli_documento LIKE CONCAT('$quick_val','%') 
            ";
        }

        return (!empty($query)) ? "WHERE ".$query : "";
    }
    function clienteu_data_search_setting(){
        $query_limit = "";
        $query_order = "";
        $query_search = "";
        $search_string = "";
        $search_params = [];

        $order_types = ['ASC','DESC'];
        $order_fields = [
            'nombre' => 'cli_nombres',
            'paterno' => 'cli_paterno',
            'materno' => 'cli_materno',
            'creacion' => 'cli_fecha',
            'documento' => 'cli_documento'
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

        if (!empty($_POST['search_nombre_like'])){
            $search_string .= " (
                cli_nombres LIKE CONCAT(?,'%') OR 
                cli_paterno LIKE CONCAT(?,'%') OR 
                cli_materno LIKE CONCAT(?,'%') )
            ";
            array_push($search_params, 
                $_POST['search_nombre_like'],
                $_POST['search_nombre_like'],
                $_POST['search_nombre_like']
            );
        }
        if (!empty($search_string)) {
            $query_search = "WHERE $search_string";
        }
        return [
            'search' => $query_search,
            'search_params' => $search_params,
            'order_' => $query_order,
            'limit' => $query_limit
        ];
    }
?>