<?php 

    function creditou_tag_estado($state){
        $resp = "";
        switch ($state) {
            case 'E':
                $resp = "<span class='stag stag-cred-E'>Activo</span>";
                break;
            case 'CC':
                $resp = "<span class='stag stag-cred-CC'>Congelado</span>";
                break;
            case 'R':
                $resp = "<span class='stag stag-cred-R'>Recuperado</span>";
                break;
            case 'A':
                $resp = "<span class='stag stag-cred-A'>Remate</span>";
                break;
            case 'AN':
                $resp = "<span class='stag stag-cred-AN'>Anulado</span>";
                break;
        }
        return $resp;
    }

    function creditou_insert_validation(){
        $config = array(    
            // [
            //     'field' => 'hash',
            //     'label' => 'Hash',
            //     'rules' => 'required',
            //     'errors' => [
            //         'required' => 'el hash es requerido'
            //     ]
            // ],
            [
                'field' => 'idcliente',
                'label' => 'Cliente',
                'rules' => 'required|integer',
                'errors' => [
                    'required' => 'el cliente es requerido',
                    'integer' => 'el cliente debe ser entero'
                ]
            ],           
            [
                'field' => 'monto',
                'label' => 'Monto',
                'rules' => 'required|numeric',
                'errors' => [
                    'required' => 'el monto es requerido',
                    'integer' => 'el monto a prestar debe ser numerico'
                ]
            ],            
            [
                'field' => 'pago_cuota',
                'label' => 'Pago',
                'rules' => 'required|numeric',
                'errors' => [
                    'required' => 'el pago por cuota es requerido',
                    'numeric' => 'el pago debe ser numerico'

                ]
            ],
            [
                'field' => 'penalidad',
                'label' => 'Penalidad',
                'rules' => 'required|numeric',
                'errors' => [
                    'required' => 'la penalidad es requerida',
                    'integer' => 'la penalidad debe ser numerica'
                ]
            ],
            [
                'field' => 'interes',
                'label' => 'Interes',
                'rules' => 'required|numeric',
                'errors' => [
                    'required' => 'el interes es requerido',
                    'integer' => 'el interes debe ser numerico'
                ]
            ],
            [
                'field' => 'tasa',
                'label' => 'Tasa',
                'rules' => 'required',
                'errors' => [
                    'required' => 'la tasa es requerida',
                ]
            ],
            [
                'field' => 'tipo_tasa',
                'label' => 'Tipo Tasa',
                'rules' => 'required',
                'errors' => [
                    'required' => 'el tipo de tasa es requerido',
                ]
            ],                       
            [
                'field' => 'ciclo_pago',
                'label' => 'Ciclo Pago',
                'rules' => 'required',
                'errors' => [
                    'required' => 'el ciclo de pago es requerido'
                ]
            ], 
            [
                'field' => 'prorroga',
                'label' => 'Prorroga',
                'rules' => 'required|integer',
                'errors' => [
                    'required' => 'la prorroga es requerida',
                    'integer' => 'la prorroga debe ser entero'
                ]
            ],
            [
                'field' => 'cargo',
                'label' => 'Cargo',
                'rules' => 'numeric',
                'errors' => [
                    'integer' => 'el cargo debe ser numerico'
                ]
            ]
        );
        return $config;        
    }

    function creditou_update_validation(){
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



    
    function creditou_generate_vencimiento($date, $ciclo){
        $string_ = '+ 7 days';
        if ($ciclo == 'MENSUAL') {
            $string_ = '+ 1 month';
        }

        $new_date = date('Y-m-d', strtotime($date.$string_));

        $name = date_format(date_create($new_date),"l");
        return $new_date.": ".$name;
    }
?>