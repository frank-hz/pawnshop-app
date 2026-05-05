<?php
    class Procesador extends CI_Controller
    {
        function __construct()
		{
			parent::__construct();
			if (!$this->session->userdata('pres_ses_sesion')) {
				redirect(base_url().'Acceso','refresh');
			}
			$this->load->model('ProcesadorModel');
        }
        
        function Serv_ResumenDia(){
            $sucursal = $this->session->userdata('pres_ses_sucursal');
            $credito_hoy = $this->ProcesadorModel->Get_PrestamoRegistroDiaCantidad($sucursal);
            $credito_activo = $this->ProcesadorModel->Get_PrestamoActivosCantidad($sucursal);
            $cliente_hoy  = $this->ProcesadorModel->Get_ClienteRegistroDiaCantidad();
            $prestamo_fin = $this->ProcesadorModel->Get_PrestamoFinalizaCantidad($sucursal);
            echo json_encode([
                'credito_nuevo' => $credito_hoy->cantidad,
                'credito_activo' => $credito_activo->cantidad,
                'cliente_hoy' => $cliente_hoy->cantidad,
                'credito_finaliza' => $prestamo_fin->cantidad
            ]);
        }

        function Serv_CreditosEstado(){
            $sucursal = $this->session->userdata('pres_ses_sucursal');
            $activo = $this->ProcesadorModel->Get_CreditoEstadoCantidad($sucursal, 'E');
            $remate = $this->ProcesadorModel->Get_CreditoEstadoCantidad($sucursal, 'A');
            $congelado = $this->ProcesadorModel->Get_CreditoEstadoCantidad($sucursal, 'CC');
            $curso = $this->ProcesadorModel->Get_CreditoEstadoCantidad($sucursal, 'en_curso');

            echo json_encode([
                'activo' => $activo->cantidad,
                'remate' => $remate->cantidad,
                'congelado' => $congelado->cantidad,
                'en_curso' => $curso->cantidad
            ]);
        }

    }
    

?>