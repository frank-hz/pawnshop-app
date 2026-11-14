<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Exporter extends CI_Controller {
    // maximo de registros procesador por tabla para evitar
    // desbordamiento de la memoria del server y navegador
    const MAX_FILAS = 50000;
    const TABLAS = [
        'cliente' => [
            'numericos' => [],
            'filtros' => ['fecha_creacion' => 'fecha'],
            'funcion' => 'get_clientes'
        ],
        'prenda' => [
            'numericos' => [],
            'filtros' => ['fecha_creacion' => 'fecha'],
            'source' => 'get_prendas'
        ],
    ];

    function __construct(){
        parent::__construct();
        // $this->load->helpder();
    }

    function index(){
        $this->load->view('Layout/header');
        $this->load->view('exporter/index', ['tablas' => array_keys($this->tablas)]);
        $this->load->view('Layout/footer');
    }
    
    function test(){
        echo "tester";
    }

    /** GET exportacion/datos?tablas[]=ventas&tablas[]=clientes&fecha_desde=... */
    function data_exportacion(){

        $tablas_permitidas = self::TABLAS;
        $entrada = json_decode($this->input->raw_input_stream,true);
        if (!is_array($entrada)) {
            return $this->responder(400, ['error' => 'JSON invalido.']);
        }
        $tablas = isset($entrada['tablas']) 
            ? $entrada['tablas'] : [];
        $filtros = isset($entrada['filtros']) 
            ? $entrada['filtros'] : [];
        
        if (!is_array($tablas) || $tablas === []) {
            return $this->responder(400, ['error' => 'Se requiere al menos una tabla.']);
        }
        if (!is_array($filtros)) {
            return $this->responder(400, ['error' => 'Filtros invalidos.']);
        }

        $result = [];
        foreach ($tablas as $tb){
            if (!is_string($tb) || !array_key_exists($tb, $tablas_permitidas)) {
                return $this->responder(422, ['error' => 'Tabla no permitida.']);
            }
            // $result[$tb] = 
        }
        $tablas = array_values(array_unique( $tablas ));

        // foreach $tablas 
        echo json_encode($tablas);

        // try {
        //     $resultado = [];
        //     foreach ($tablas_elegidas as $tabla) {
        //         $resultado[$tabla] = $this->obtener_datos($tabla);
        //     }
        // } catch (LengthException $e) {
        //     return $this->responder(['error' => $e->getMessage()], 413);
        // }
        // return $this->responder(['tablas' => $resultado]);
    }
    
    private function responder($code, array $body) {
        $this->output
            ->set_status_header($code)
            ->set_content_type('application/json', 'utf-8')
            ->set_output(json_encode($body, JSON_UNESCAPED_UNICODE | JSON_PRESERVE_ZERO_FRACTION));
    }

}