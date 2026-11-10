<?php 

    use OpenSpout\Writer\Common\Creator\WriterEntityFactory;
    class Exporter extends CI_Controller {
        function __construct(){
            parent::__construct();
            $this->load->model('ExporterModel');
        }
        private $tablas = [
            'cliente' => [
                'columnas'  => ['id', 'documento', 'nombres', 'paterno', 'materno'],
                'numericas' => ['id'],
                'filtros'   => [],
            ],
        ];

        function index(){
            $data = $this->ExporterModel->get_clientes();
            $this->load->view('Layout/header');
            $this->load->view('exporter/index', [
                'clientes' => $data,
                'tablas' => array_keys($this->tablas)
            ]);
            $this->load->view('Layout/footer');
        }

        function generate($tabla = 'cliente'){
            $formatos = ['xlsx','csv'];
            $formato = 'xlsx';
            $data = $this->ExporterModel->get_clientes();
            $nombre_archivo = $tabla. "_". date("Y-m-d");
            // print_r($data);
            $this->exportar_xlsx($tabla, $data, $nombre_archivo);
            // $this->{'exportar_'. $formato}($tabla, $data, $nombre);
            // set_time_limit(0);

            
        }

        function exportar_xlsx($tabla, array $data, $nombre){
            $config = $this->tablas[$tabla];
            $writer = WriterEntityFactory::createXLSXWriter();
            $writer->openToBrowser($nombre, '.xlsx');
            $writer->addRow(WriterEntityFactory::createRowFromArray($config['columnas']));

            foreach($data as $item) {
                foreach($config['numericas'] as $col) {
                    if (isset($fila[$col])) {
                        $fila[$col] += 0;
                    }
                }
                $writer->addRow(WriterEntityFactory::createRowFromArray(array_values($item)));
            }
            $writer->close();
            exit;
        }
    }