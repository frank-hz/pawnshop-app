<?php

    // public services to use electrocash database 
    class PublicAPI extends CI_Controller
    {   
        function __construct(){
            parent::__construct();            
            $this->load->model("PublicAPIModel");
            header("Access-Control-Allow-Origin: *");
        }
        
        // Sucursal - Sede
        function sucursal__get_all(){            
			$data = $this->PublicAPIModel->sucursal__get_all();
			echo json_encode($data);
        }
        function sucursal__get_data(){
			$file = json_decode(file_get_contents('php://input'));
			$data = $this->PublicAPIModel->sucursal_get_data($file->id);
			echo json_encode($data);
		}


        // Prenda 
        function prenda__get_all(){
            header("Access-Control-Allow-Origin: *");
            $parms = json_decode(file_get_contents('php://input'));
            $data = [];

            if (empty($parms)) {
                die(json_encode(['error' => 'parameters not found']));
            }

            $query_limit = 'LIMIT ';
            $query_limit .= !empty($parms->limit) ? $parms->limit :'25';
            if ($parms->limit === 'ALL') {
                $query_limit = '';
            }

            // $query_limit = '5';
            $query_string = "";
            $query_where = [];
            $query_pars = [];
            if (!empty($parms->search)){
                $query_string .= "
                    pren_descripcion LIKE(?, '%') OR 
                    cred_serie LIKE(?, '%') OR 
                    pren_modelo LIKE(?, '%') OR 
                    pren_serie LIKE(?, '%') ";
                array_push($query_where, " (
                    pren_descripcion LIKE CONCAT(?, '%') OR 
                    cred_serie LIKE CONCAT(?, '%') OR 
                    pren_modelo LIKE CONCAT(?, '%') OR 
                    pren_serial LIKE CONCAT(?, '%')
                ) ");
                array_push(
                    $query_pars, 
                    $parms->search,
                    $parms->search,
                    $parms->search,
                    $parms->search
                );
            }

            if (!empty($parms->type_prenda)){
                $query_string .= "tpren_codigo = ? ";
                array_push($query_where, "tpren_codigo = ? ");
                array_push($query_pars, $parms->type_prenda);
            }

            if (!empty($parms->date_liquidacion)){
                // if (!validate_date($$parms->date_liquidacion)) return;
                $query_string .= "cred_liquidacion = ? ";
                array_push($query_where, "cred_liquidacion = ? ");
                array_push($query_pars, $parms->date_liquidacion);
            }

            if (!empty($parms->state)){
                if (!in_array($parms->state, ["A","RT","O","R"])) return;
                $query_string .= "pren_estado = ? ";
                array_push($query_where, "pren_estado = ? ");
                array_push($query_pars, $parms->state);
            }

            if (!empty($parms->sucursal)){
                $query_string .= "cred_sucursal = ? ";
                array_push($query_where, "cred_sucursal = ? ");
                array_push($query_pars, $parms->sucursal);
            }
            
            $data = $this->PublicAPIModel->prenda__get_all([
                'query' => implode(" AND ", $query_where),
                'parameter' => $query_pars,
                'limit' => $query_limit
            ]);

            echo json_encode([
                'ok' => 'process completed',
                'result' => $data
            ]);
            // echo json_encode($data);
        }

        function validate_date($date_i = ''){
            // $rgex = '/d{1,2}-\d{1,2}-\d{4}';
            $rgex = '/\d{1,2}-\d{1,2}-\d{4}/';
            if (!preg_match($rgex, $date_i)) {
                echo "No Match";
                return;
            }

            // $dateString = $date_i;
            // $dateFormat = 'd-m-Y';
            // $dateTimeObj = DateTime::createFromFormat($dateFormat, $dateString);

            // if (!$dateTimeObj) {
            //     echo "Invalid date string format";
            // } else {
            //     echo "Valid date string format";
            // }


            // echo date($date_i);
        }



        // Tipos de Prenda
        function tipo_prenda__get_all(){
            header("Access-Control-Allow-Origin: *");
            $data = $this->PublicAPIModel->tipo_prenda__get_all();
            echo json_encode([
                'ok' => 'process completed',
                'result' => $data
            ]);
        }
    }
    
?>