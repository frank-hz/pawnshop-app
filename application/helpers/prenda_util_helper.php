<?php 

    // recibe: datos de una prenda
    // devuelve: cadena html de previsualizacion con los datos recibidos
    function prendau_data_to_string($data) {
        if (!$data) return;
        $string = "";
        switch ($data->tipo_nombre) {
            case 'ELECTRO':
                $string .= "
                <div class='col-xs-12 col-lg-4 form-group'>
                    <div class='form-label'>Serie</div>
                    <span>$data->serial</span>
                </div> 
                <div class='col-xs-12 col-lg-4 form-group'>
                    <div class='form-label'>Marca</div>
                    <span>$data->marca</span>
                </div> 
                <div class='col-xs-12 col-lg-4 form-group'>
                    <div class='form-label'>Modelo</div>
                    <span>$data->modelo</span>
                </div> 
                ";
                break;
            
            case 'JOYA':
                $string .= "
                <div class='col-xs-12 col-lg-4 form-group'>
                    <div class='form-label'>Quilate</div>
                    <span>$data->quilate</span>
                </div> 
                <div class='col-xs-12 col-lg-4 form-group'>
                    <div class='form-label'>Peso</div>
                    <span>$data->peso</span>
                </div>
                ";
                break;
            
            case 'VEHICULO':
                $string .= "
                <div class='col-xs-12 col-lg-4 form-group'>
                    <div class='form-label'>Marca</div>
                    <span>$data->marca</span>
                </div> 
                <div class='col-xs-12 col-lg-4 form-group'>
                    <div class='form-label'>Modelo</div>
                    <span>$data->modelo</span>
                </div> 
                <div class='col-xs-12 col-lg-4 form-group'>
                    <div class='form-label'>Anio</div>
                    <span>$data->anio</span>
                </div> 
                <div class='col-xs-12 col-lg-4 form-group'>
                    <div class='form-label'>Placa</div>
                    <span>$data->placa</span>
                </div> 
                <div class='col-xs-12 col-lg-4 form-group'>
                    <div class='form-label'>Kilometraje</div>
                    <span>$data->kilometraje</span>
                </div> 
                <div class='col-xs-12 col-lg-4 form-group'>
                    <div class='form-label'>Transmision</div>
                    <span>$data->transmision</span>
                </div> 
                <div class='col-xs-12 col-lg-4 form-group'>
                    <div class='form-label'>Color</div>
                    <span>$data->color</span>
                </div> 
                <div class='col-xs-12 col-lg-4 form-group'>
                    <div class='form-label'>Carroceria</div>
                    <span>$data->carroceria</span>
                </div>
                ";
                break;
        }

        $comen = ucwords($data->comentario);
        $obsr = ucwords($data->observacion);
        $string .= "
            <div class='col-xs-12 col-lg-6 form-group'>
                <div class='form-label'>Comentarios</div>
                <span>$comen</span>
            </div> 
            <div class='col-xs-12 col-lg-6 form-group'>
                <div class='form-label'>Observaciones</div>
                <span>$obsr</span>
            </div> 
        ";

        $html = "
            <div class='row'>            
                <div class='col-xs-12 col-lg-4 form-group'>
                    <div class='form-label'>Descripcion</div>
                    <a href='$data->url'> $data->descripcion </a>
                </div>
                <div class='col-xs-12 col-lg-4 form-group'>
                    <div class='form-label'>Tipo</div>
                    <span>$data->tipo_nombre</span>
                </div>
                <div class='col-xs-12 col-lg-4 form-group'>
                    <div class='form-label'>Categoria</div>
                    <span>$data->categoria_nombre</span>
                </div> 
                <div class='col-xs-12 col-lg-4 form-group'>
                    <div class='form-label'>Estado/Valoracion</div>
                    <span>$data->valoracion</span>
                </div>   
                $string          
            </div>            
        ";
        return $html;
    }

    // recibe: datos de una prenda, tipo de prenda (nombre)
    // devuelve cadena html de un formulario con los datos y segun el tipo de prenda
    function prendau_string_form_edit($data, $tipo = null){
        $string = "";
        $t = ($tipo) ? $tipo : $data->tipo_nombre;
        switch ($t) {
            case 'ELECTRO':
                $string .= "
                <div class='row'>
                    <div class='col-xs-12 col-md-6 form-group'>
                        <div class='form-label'>Serial</div>
                        <input type='text' class='form-control text-uppercase border-fx-1' name='serial' maxlength='50' value='$data->serial'>
                        <span id='nprenda-feed-serial' class='feedtag feedtag-danger'></span>
                    </div>
                    <div class='col-xs-12 col-md-6 form-group'>
                        <div class='form-label'>Marca</div>
                        <input type='text' class='form-control text-uppercase border-fx-1' name='marca' maxlength='50' value='$data->marca'>
                        <span id='nprenda-feed-marca' class='feedtag feedtag-danger'></span>
                    </div>
                    <div class='col-xs-12 col-md-6 form-group'>
                        <div class='form-label'>Modelo</div>
                        <input type='text' class='form-control text-uppercase border-fx-1' name='modelo' maxlength='50' value='$data->modelo'>
                        <span id='nprenda-feed-modelo' class='feedtag feedtag-danger'></span>
                    </div>
                </div>
                ";
                break;
            
            case 'JOYA':
                $string .= "
                <div class='row'>
                    <div class='col-xs-12 col-md-6 form-group'>
                        <div class='form-label'>Quilate</div>
                        <input type='text' class='form-control text-uppercase border-fx-1' name='quilate' maxlength='20' value='$data->quilate'>
                        <span id='nprenda-feed-quilate' class='feedtag feedtag-danger'></span>
                    </div>
                    <div class='col-xs-12 col-md-6 form-group'>
                        <div class='form-label'>Peso</div>
                        <input type='text' class='form-control text-uppercase border-fx-1' name='peso' maxlength='10' value='$data->peso'>
                        <span id='nprenda-feed-peso' class='feedtag feedtag-danger'></span>
                    </div>
                </div>
                ";
                break;
            
            case 'VEHICULO':
                $string .= "
                <div class='row'>
                    <div class='col-xs-12 col-md-6 form-group'>
                        <div class='form-label'>Marca</div>
                        <input type='text' class='form-control text-uppercase border-fx-1' name='marca' maxlength='50' value='$data->marca'>
                        <span id='nprenda-feed-marca' class='feedtag feedtag-danger'></span>
                    </div>
                    <div class='col-xs-12 col-md-6 form-group'>
                        <div class='form-label'>Modelo</div>
                        <input type='text' class='form-control text-uppercase border-fx-1' name='modelo' maxlength='50' value='$data->modelo'>
                        <span id='nprenda-feed-modelo' class='feedtag feedtag-danger'></span>
                    </div>
                    <div class='col-xs-12 col-md-6 form-group'>
                        <div class='form-label'>Anio</div>
                        <input type='text' class='form-control text-uppercase border-fx-1' name='anio' maxlength='10' value='$data->anio'>
                        <span id='nprenda-feed-anio' class='feedtag feedtag-danger'></span>
                    </div>
                    <div class='col-xs-12 col-md-6 form-group'>
                        <div class='form-label'>Placa</div>
                        <input id='prendau_placa' type='text' class='form-control text-uppercase border-fx-1 licence-input' name='placa' maxlength='10' value='$data->placa'>
                        <span id='nprenda-feed-placa' class='feedtag feedtag-danger'></span>
                    </div>
                    <div class='col-xs-12 col-md-6 form-group'>
                        <div class='form-label'>Kilometraje</div>
                        <input type='text' class='form-control text-uppercase border-fx-1' name='kilometraje' maxlength='30' value='$data->kilometraje'>
                        <span id='nprenda-feed-kilometraje' class='feedtag feedtag-danger'></span>
                    </div>
                    <div class='col-xs-12 col-md-6 form-group'>
                        <div class='form-label'>Transmision</div>
                        <select name='transmision' class='form-control border-fx-1'>
                            <option value=''>Seleccionar</option>
                            <option value='MECANICA'>MECANICA</option>
                            <option value='AUTOMATICA/SECUENCIAL'>AUTOMATICA/SECUENCIAL</option>
                        </select>
                        <span id='nprenda-feed-transmision' class='feedtag feedtag-danger'></span>
                    </div>
                    <div class='col-xs-12 col-md-6 form-group'>
                        <div class='form-label'>Color</div>
                        <input type='text' class='form-control text-uppercase border-fx-1' name='color' maxlength='50' value='$data->color'>
                        <span id='nprenda-feed-color' class='feedtag feedtag-danger'></span>
                    </div>
                    <div class='col-xs-12 col-md-6 form-group'>
                        <div class='form-label'>Carroceria</div>
                        <input type='text' class='form-control text-uppercase border-fx-1' name='carroceria' maxlength='25' value='$data->carroceria'>
                        <span id='nprenda-feed-carroceria' class='feedtag feedtag-danger'></span>
                    </div>
                </div>
                ";
                break;
        }

        $string .= "
            <div class='row'>
                <div class='col-md-12'>
                    <div class='form-label'>Valoracion/Estado</div>
                    <div class='range-slider'>
                    <div class='range-value'>
                        <span id='valora-range-label'>$data->valoracion</span>
                    </div>
                    <div class='range-picker'>
                        <div class='range-value-left'>0</div>
                        <input type='range' min='0' max='10' step='.5' value='$data->valoracion' name='valoracion'>
                        <div class='range-value-right'>10</div>
                    </div>
                    </div>
                </div>
                <div class='col-md-12'>
                    <div class='form-group'>
                    <label for='' class='form-label'>Comentarios</label>
                    <textarea name='comentario' class='form-control text-uppercase border-rock' cols='3' rows='2' maxlength='120'>$data->comentario</textarea>
                    </div>
                </div>
                <div class='col-md-12'>
                    <div class='form-group'>
                    <label for='' class='form-label'>Observaciones</label>
                    <textarea name='observacion' class='form-control text-uppercase border-rock' cols='3' rows='2' maxlength='120'>$data->observacion</textarea>
                    </div>
                </div>
            </div>
        ";
        return $string;
    }

    // recibe: tipo de prenda (nombre)
    // devuelve cadena html de un formulario segun el tipo de prenda
    function prendau_string_form_new($tipo){
        $string = "";
        switch ($tipo) {
            case 'ELECTRO':
                $string .= "
                <div class='row'>
                    <div class='col-xs-12 col-md-6 form-group'>
                        <div class='form-label'>Serial</div>
                        <input type='text' class='form-control text-uppercase border-fx-1' name='serial' maxlength='50' value=''>
                        <span id='nprenda-feed-serial' class='feedtag feedtag-danger'></span>
                    </div>
                    <div class='col-xs-12 col-md-6 form-group'>
                        <div class='form-label'>Marca</div>
                        <input type='text' class='form-control text-uppercase border-fx-1' name='marca' maxlength='50' value=''>
                        <span id='nprenda-feed-marca' class='feedtag feedtag-danger'></span>
                    </div>
                    <div class='col-xs-12 col-md-6 form-group'>
                        <div class='form-label'>Modelo</div>
                        <input type='text' class='form-control text-uppercase border-fx-1' name='modelo' maxlength='50' value=''>
                        <span id='nprenda-feed-modelo' class='feedtag feedtag-danger'></span>
                    </div>
                </div>
                ";
                break;
            
            case 'JOYA':
                $string .= "
                <div class='row'>
                    <div class='col-xs-12 col-md-6 form-group'>
                        <div class='form-label'>Quilate</div>
                        <input type='text' class='form-control text-uppercase border-fx-1' name='quilate' maxlength='20' value=''>
                        <span id='nprenda-feed-quilate' class='feedtag feedtag-danger'></span>
                    </div>
                    <div class='col-xs-12 col-md-6 form-group'>
                        <div class='form-label'>Peso</div>
                        <input type='text' class='form-control text-uppercase border-fx-1' name='peso' maxlength='10' value=''>
                        <span id='nprenda-feed-peso' class='feedtag feedtag-danger'></span>
                    </div>
                </div>
                ";
                break;
            
            case 'VEHICULO':
                $string .= "
                <div class='row'>
                    <div class='col-xs-12 col-md-6 form-group'>
                        <div class='form-label'>Marca</div>
                        <input type='text' class='form-control text-uppercase border-fx-1' name='marca' maxlength='50' value=''>
                        <span id='nprenda-feed-marca' class='feedtag feedtag-danger'></span>
                    </div>
                    <div class='col-xs-12 col-md-6 form-group'>
                        <div class='form-label'>Modelo</div>
                        <input type='text' class='form-control text-uppercase border-fx-1' name='modelo' maxlength='50' value=''>
                        <span id='nprenda-feed-modelo' class='feedtag feedtag-danger'></span>
                    </div>
                    <div class='col-xs-12 col-md-6 form-group'>
                        <div class='form-label'>Anio</div>
                        <input type='text' class='form-control text-uppercase border-fx-1' name='anio' maxlength='10' value=''>
                        <span id='nprenda-feed-anio' class='feedtag feedtag-danger'></span>
                    </div>
                    <div class='col-xs-12 col-md-6 form-group'>
                        <div class='form-label'>Placa</div>
                        <input id='prendau_placa' type='text' class='form-control text-uppercase border-fx-1 licence-input' name='placa' maxlength='10' value=''>
                        <span id='nprenda-feed-placa' class='feedtag feedtag-danger'></span>
                    </div>
                    <div class='col-xs-12 col-md-6 form-group'>
                        <div class='form-label'>Kilometraje</div>
                        <input type='text' class='form-control text-uppercase border-fx-1' name='kilometraje' maxlength='30' value=''>
                        <span id='nprenda-feed-kilometraje' class='feedtag feedtag-danger'></span>
                    </div>
                    <div class='col-xs-12 col-md-6 form-group'>
                        <div class='form-label'>Transmision</div>
                        <select name='transmision' class='form-control border-fx-1'>
                            <option value=''>Seleccionar</option>
                            <option value='MECANICA'>MECANICA</option>
                            <option value='AUTOMATICA/SECUENCIAL'>AUTOMATICA/SECUENCIAL</option>
                        </select>
                        <span id='nprenda-feed-transmision' class='feedtag feedtag-danger'></span>
                    </div>
                    <div class='col-xs-12 col-md-6 form-group'>
                        <div class='form-label'>Color</div>
                        <input type='text' class='form-control text-uppercase border-fx-1' name='color' maxlength='50' value=''>
                        <span id='nprenda-feed-color' class='feedtag feedtag-danger'></span>
                    </div>
                    <div class='col-xs-12 col-md-6 form-group'>
                        <div class='form-label'>Carroceria</div>
                        <input type='text' class='form-control text-uppercase border-fx-1' name='carroceria' maxlength='25' value=''>
                        <span id='nprenda-feed-carroceria' class='feedtag feedtag-danger'></span>
                    </div>
                </div>
                ";
                break;
        }

        $string .= "
            <div class='row'>
                <div class='col-md-12'>
                    <div class='form-label'>Valoracion/Estado</div>
                    <div class='range-slider'>
                    <div class='range-value'>
                        <span id='valora-range-label'>0</span>
                    </div>
                    <div class='range-picker'>
                        <div class='range-value-left'>0</div>
                        <input type='range' min='0' max='10' step='.5' value='0' name='valoracion'>
                        <div class='range-value-right'>10</div>
                    </div>
                    </div>
                </div>
                <div class='col-md-12'>
                    <div class='form-group'>
                    <label for='' class='form-label'>Comentarios</label>
                    <textarea name='comentario' class='form-control text-uppercase border-rock' cols='3' rows='2' maxlength='120'></textarea>
                    </div>
                </div>
                <div class='col-md-12'>
                    <div class='form-group'>
                    <label for='' class='form-label'>Observaciones</label>
                    <textarea name='observacion' class='form-control text-uppercase border-rock' cols='3' rows='2' maxlength='120'></textarea>
                    </div>
                </div>
            </div>
        ";
        return $string;
    }

    // recibe: estado de prenda
    // devuelve: cadena html segun el estado de la prenda
    function prendau_tag_estado($state){
        $resp = "";
        $big = "stag-lg";
        switch ($state) {
            case 'D':
                $resp = "<span class='stag stag-pren-D'>Disponible</span>";
                break;
                
            case 'R':
                $resp = "<span class='stag stag-pren-R'>Recuperado</span>";
                break;

            case 'O':
                $resp = "<span class='stag stag-pren-O'>Ocupado</span>";
                break;

            case 'A':
                $resp = "<span class='stag stag-pren-A'>Remate</span>";
                break;
        }
        return $resp;
    }

    // devuelve: configuracion de validacion para la creacion de prenda
    function prendau_insert_validation(){
        $config = array(    
            [
                'field' => 'idcliente',
                'label' => 'Cliente',
                'rules' => 'required|integer'
            ],           
            [
                'field' => 'descripcion',
                'label' => 'Descripcion',
                'rules' => 'required'
            ],
            [
                'field' => 'categoria',
                'label' => 'Categoria',
                'rules' => 'required|integer'
            ]
        );
        return $config;        
    }

    // devuelve: configuracion de validacion para la actualizacion de prenda
    function prendau_update_validation(){
        $config = array(  
            [
                'field' => 'id',
                'label' => 'IDPrenda',
                'rules' => 'required|integer'
            ],
            [
                'field' => 'descripcion',
                'label' => 'Descripcion',
                'rules' => 'required'
            ],
            [
                'field' => 'categoria',
                'label' => 'Categoria',
                'rules' => 'required|integer'
            ]
        );
        return $config;        
    }

    // usa los datos POST recibidos para verificar y crear un arreglo con los datos a actualizar 
    function prendau_prepare_data_update(){
        $data = [];
        $pd = $_POST;
        if (!empty($pd['descripcion'])) $data['pren_descripcion'] = strtoupper($pd['descripcion']);
        if (!empty($pd['categoria'])) $data['pren_categoria'] = $pd['categoria'];
        if (!empty($pd['serial'])) $data['pren_serial'] = strtoupper($pd['serial']);
        if (!empty($pd['marca'])) $data['pren_marca'] = strtoupper($pd['marca']);
        if (!empty($pd['modelo'])) $data['pren_modelo'] = strtoupper($pd['modelo']);
        if (!empty($pd['quilate'])) $data['pren_quilate'] = $pd['quilate'];
        if (!empty($pd['peso'])) $data['pren_peso'] = $pd['peso'];
        if (!empty($pd['placa'])) $data['pren_placa'] = strtoupper($pd['placa']);
        if (!empty($pd['anio'])) $data['pren_anio'] = $pd['anio'];
        if (!empty($pd['kilometraje'])) $data['pren_kilometraje'] = $pd['kilometraje'];
        if (!empty($pd['transmision'])) $data['pren_transmision'] = $pd['transmision'];
        if (!empty($pd['color'])) $data['pren_color'] = $pd['color'];
        if (!empty($pd['carroceria'])) $data['pren_carroceria'] = $pd['carroceria'];
        if (!empty($pd['valoracion'])) $data['pren_valoracion'] = $pd['valoracion'];
        if (!empty($pd['observacion'])) $data['pren_observacion'] = strtoupper($pd['observacion']);
        if (!empty($pd['comentario'])) $data['pren_comentario'] = strtoupper($pd['comentario']);

        return $data;
    }

    // usa los datos POST recibidos para verificar y crear un arreglo con los datos a registrar 
    function prendau_prepare_data_insert(){
        $data = [];
        $pd = $_POST;      
        if (!empty($pd['idcliente'])) $data['pren_cliente'] = $pd['idcliente'];  
        if (!empty($pd['descripcion'])) $data['pren_descripcion'] = strtoupper($pd['descripcion']);
        if (!empty($pd['categoria'])) $data['pren_categoria'] = $pd['categoria'];
        if (!empty($pd['serial'])) $data['pren_serial'] = strtoupper($pd['serial']);
        if (!empty($pd['marca'])) $data['pren_marca'] = strtoupper($pd['marca']);
        if (!empty($pd['modelo'])) $data['pren_modelo'] = strtoupper($pd['modelo']);
        if (!empty($pd['quilate'])) $data['pren_quilate'] = $pd['quilate'];
        if (!empty($pd['peso'])) $data['pren_peso'] = $pd['peso'];
        if (!empty($pd['placa'])) $data['pren_placa'] = strtoupper($pd['placa']);
        if (!empty($pd['anio'])) $data['pren_anio'] = $pd['anio'];
        if (!empty($pd['kilometraje'])) $data['pren_kilometraje'] = $pd['kilometraje'];
        if (!empty($pd['transmision'])) $data['pren_transmision'] = $pd['transmision'];
        if (!empty($pd['color'])) $data['pren_color'] = $pd['color'];
        if (!empty($pd['carroceria'])) $data['pren_carroceria'] = $pd['carroceria'];
        if (!empty($pd['valoracion'])) $data['pren_valoracion'] = $pd['valoracion'];
        if (!empty($pd['observacion'])) $data['pren_observacion'] = strtoupper($pd['observacion']);
        if (!empty($pd['comentario'])) $data['pren_comentario'] = strtoupper($pd['comentario']);

        return $data;
    }

    // usa los datos POST recibidos para verificar y crear una cadena de  condicionales para la busqueda de datos
    function prendau_data_search_setting(){
        $query_order = "";

        $query_limit = "";
        $query_order = "";
        $query_search = "";
        $search_string = "";
        $search_params = [];
        $order_types = ['ASC','DESC'];
        $order_fields = [
            'descripcion' => 'pren_descripcion',
            'creacion' => 'pren_fecha_registro',
            'tipo' => 'tpren_nombre'
        ];
        if (!empty($_POST['order_by']) && 
            array_key_exists($_POST['order_by'], $order_fields)) {
            $ord = $_POST['order_by'];
            $query_order .= "ORDER BY $order_fields[$ord]";
        }
        if (!empty($_POST['order_type']) && 
            in_array(strtoupper($_POST['order_type']), $order_types)){
            $query_order .= " ".$_POST['order_type'];
        }
        $query_limit = (!empty($_POST['limit'])) ? 'limit '.$_POST['limit'] : '';

        if (!empty($_POST['search_descripcion_like'])){
            $search_string .= " 
                pren_descripcion LIKE CONCAT(?,'%')
            ";
            array_push($search_params, 
                $_POST['search_descripcion_like']
            );
        }
        if (!empty($_POST['search_estado'])) {
            if (gettype($_POST['search_estado']) == 'array') {
                $datasr = $_POST['search_estado'];
                $cont = 1;
                foreach ($datasr as $d => $ind) {
                    $cont++;
                    $search_string .= " pren_estado = ?";
                    if (count($datasr) > $count) {
                        $search_string .= " OR ";
                    }
                }
            }else {
                $search_string .= " pren_estado = ?";
                array_push($search_params,$_POST['search_estado']);
            }
        }
        if (!empty($search_string)) {
            $query_search = "($search_string)";
        }
        return [
            'search' => $query_search,
            'search_params' => $search_params,
            'order' => $query_order,
            'limit' => $query_limit
        ];


        // return "$query_order $query_limit";
    }

    function prendau_search_setting(){
        $query_order = "";
        $order_type = "ASC";
        $order_by = "pren_codigo";
        $query_search = "";
        $search_data = [];
        $log = "";

        $order_type_stash = ['ASC','DESC'];
        $order_field_stash = [
            'descripcion' => 'pren_descripcion',
            'creacion' => 'pren_fecha_registro',
            'tipo' => 'tipo_nombre',
            'categoria' => 'categoria_nombre',
            'estado' => 'pren_estado'
        ];

        if (!empty($_POST['order_type']) && 
            in_array(strtoupper($_POST['order_type']), $order_type_stash)){
            $order_type = $_POST['order_type'];
        }
        if (!empty($_POST['order_by']) && 
            array_key_exists($_POST['order_by'], $order_field_stash)) {
            $order_by = $order_field_stash[ $_POST['order_by'] ];
        }        

        $query_order = "ORDER BY $order_by $order_type";
        if (!empty($_POST['limit']) && $_POST['limit'] > 0) {
            $limit = intval($_POST['limit']);
            $query_order .= " LIMIT $limit";
        }

        if (!empty($_POST['search_estado'])) {
            $valid_states = ['A', 'D', 'R', 'O', 'RB', 'V'];
            $datasr = explode(",",$_POST['search_estado']);
            $lcount = 0;
            foreach ($datasr as $d) {
                $lcount++;
                if(in_array($d,$valid_states)){
                    $query_search .= " pren_estado = ?";
                    array_push($search_data,''.$d);
                    if (count($datasr) > $lcount) {
                        $query_search .= " OR ";
                    }
                }
            }  
            // $search_data = [$_POST['search_estado']];            
        }
        return [
            'order' => $query_order,
            'search' => $query_search,
            'search_data' => $search_data
        ];
    }

?>