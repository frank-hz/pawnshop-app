<?php
class Resumen extends CI_Controller {
    function __construct(){
        parent::__construct();
        $this->load->model("ResumenModel");
    } 


    public function get_resumen_dia(){
        $fecha = $this->input->post("fecha", TRUE);
        if (empty($fecha) || !strtotime($fecha)) {
            $fecha = date("Y-m-d");
        }
        $settings = ['fecha_creacion' => $fecha];
        $creditos = $this->ResumenModel->getCreditosGenerados($settings);
        $pagos    = $this->ResumenModel->getPagosRecibidos($settings);
        $total_ingreso = 0;
        $total_egreso = 0;
        
        if (!empty($pagos)) {
            $total_ingreso = round(array_sum(array_column($pagos, 'total')), 2);
        }
        if (!empty($creditos)) {
            $total_egreso = round(array_sum(array_column($creditos, 'total')), 2);
        }
        $balance = round($total_ingreso - $total_egreso, 2);
        $sede = $this->session->userdata('sucursal_nombre') ?? 'Sin Sucursal';
        $response = [
            'status' => 'OK',
            'message' => 'Datos procesados.',
            'result' => [
                'sede'          => $sede,
                'fecha'         => date("d-m-Y", strtotime($fecha)),
                'creditos'      => $creditos,
                'pagos'         => $pagos,
                'total_ingreso' => $total_ingreso,
                'total_egreso'  => $total_egreso,
                'balance'       => $balance
            ]
        ];

        echo json_encode($response);
    }

}