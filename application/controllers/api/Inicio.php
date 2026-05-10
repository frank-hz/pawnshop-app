<?php
class Inicio extends CI_Controller {
    function __construct(){
        parent::__construct();

        $this->load->model("InicioModel");
    }


    function get_creditos_activos(){
        $proximoVencer = (bool) $this->input->get("proximo_vence", TRUE);
        $settings = Array(
            'PROX_VENCE' => $proximoVencer
        );
        $data = $this->InicioAPIModel->creditosActivos( $settings );
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                'status' => 'OK',
                'result' => $data
            ]));
    }

    function get_resumen_dia(){
        $fecha = $this->input->post('fecha', TRUE);
        if (empty($fecha) || !strtotime($fecha)) {
            $fecha = date('Y-m-d');
        }

        $settings = ['fecha_creacion' => $fecha];
        $creditos = $this->InicioModel->creditos_generados($settings);
        $pagos = $this->InicioModel->pagos_recibidos($settings);
        $total_ingreso = 0;
        $total_egreso = 0;
        if (!empty($pagos)) {
            $total_ingreso = round(array_sum(array_column($pagos, 'total')), 2);
        }
        if (!empty($creditos)) {
            $total_egreso = round(array_sum(array_column($creditos, 'monto')), 2);
        }
        $balance = round($total_ingreso - $total_egreso, 2);
        $response = [
            'status'  => 'OK',
            'message' => 'Datos procesados correctamente.',
            'result'  => [
                'sede'          => $this->session->userdata('sucursal_nombre') ?? 'Sin sede',
                'fecha'         => date('d-m-Y', strtotime($fecha)),
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