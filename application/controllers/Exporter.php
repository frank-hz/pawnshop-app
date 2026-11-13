<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Exporter extends CI_Controller {
    // maximo de registros procesador por tabla para evitar
    // desbordamiento de la memoria del server y navegador
    const MAX_FILAS = 50000;
    private $tablas = [
        'cliente' => [
            'columnas' => [
                'nombres','ape_paterno', 'ape_materno', 'documento',
                'tipo_documento','email','telefono','fecha_creacion'
            ],
            'numericos' => [],
            'filtros' => ['fecha_creacion' => 'fecha']
        ],
        'prenda' => [
            'columnas' => [
                'descripcion','serial', 'marca', 'modelo', 'quilate',
                'peso','placa','anio','kilometraje', 'color','estado',
                'valoracion', 'observacion', 'comentario', 'fecha_creacion'
            ],
            'numericos' => [],
            'filtros' => ['fecha_creacion' => 'fecha']
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
    

    /** GET exportacion/datos?tablas[]=ventas&tablas[]=clientes&fecha_desde=... */
    function data_exportacion(){
        $tablas_solicitud = (array) $this->input->get('tablas');
        $tablas_elegidas = array_values(array_intersect(
            $tablas_solicitud, array_keys($this->tablas))
        );

        if (empty($tablas_elegidas)) {
            return $this->responder(['error' => 'Selecion de tabla vacia.', 400]);
        }

        try {
            $resultado = [];
            foreach ($tablas_elegidas as $tabla) {
                $resultado[$tabla] = $this->obtener_datos($tabla);
            }
        } catch (LengthException $e) {
            return $this->responder(['error' => $e->getMessage()], 413);
        }
        return $this->responder(['tablas' => $resultado]);
    }
    // Consultar tabla usando filtros
    function obtener_datos($tabla){
        $config = $thi->tablas[$tabla];
        $this->db->select( implode(',', $config['columnas'])->from($tabla) );
        $this->aplicar_filtros($config['filtros']);

        if ($this->input->get('sin_duplicados')) {
            // Evitar filas identicas o repetidas
            $his->db->distinct();
        }
        $this->db->order_by($config['columnas'][0], 'ASC');
        $total= $this->db->count_all_results('', FALSE);
        if ($total > self::MAX_FILAS) {
            throw new LengthException(
                "La tabla '$tabla' tiene $total filas - (max. permitido: " . self::MAX_FILAS .').';
            );
        }

        $filas = $this->db->get()->result_array();
        return [
            'columnas' => $config['columnas'],
            'filas' => $this->formatear_filas($filas, $config['numericas'])
        ];
        
    }

    
    /** Filtros opcionales (el Query Builder escapa los valores). */
    private function aplicar_filtros(array $campos){
        $desde  = $this->input->get('fecha_desde');
        $hasta  = $this->input->get('fecha_hasta');
        $estado = $this->input->get('estado');
        $min    = $this->input->get('monto_min');
        $max    = $this->input->get('monto_max');

        if (isset($campos['fecha'])) {
            if ($desde) { $this->db->where($campos['fecha'] . ' >=', $desde . ' 00:00:00'); }
            if ($hasta) { $this->db->where($campos['fecha'] . ' <=', $hasta . ' 23:59:59'); }
        }
        if (isset($campos['estado']) && $estado) {
            $this->db->where($campos['estado'], $estado);
        }
        if (isset($campos['monto'])) {
            if (is_numeric($min)) { $this->db->where($campos['monto'] . ' >=', $min); }
            if (is_numeric($max)) { $this->db->where($campos['monto'] . ' <=', $max); }
        }
    }
    /** Convierte cada fila en un array simple y las columnas numéricas en números. */
    private function formatear_filas(array $filas, array $numericas){
        foreach ($filas as &$fila) {
            foreach ($numericas as $col) {
                if (isset($fila[$col])) { $fila[$col] += 0; }   // "12.50" -> 12.5
            }
            $fila = array_values($fila);
        }
        return $filas;
    }
    private function responder(array $datos, $codigo = 200){
        return $this->output
            ->set_status_header($codigo)
            ->set_content_type('application/json', 'utf-8')
            ->set_output(json_encode($datos));
    }

}