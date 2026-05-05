<?php  
	class AnalisisAPI extends CI_Controller
	{
		function __construct()
		{
			parent::__construct();
			$this->load->model('AnalisisAPIModel');
		}

		function data_general(){
			$dates = $this->AnalisisAPIModel->get_rango_fecha_sucursal();
			echo json_encode([
				'activos' => $this->AnalisisAPIModel->get_credito_activo()->value,
				'dinero' => $this->AnalisisAPIModel->get_dinero_activo()->value,
				'inicio' => date("d/m/Y", strtotime($dates->inicio)),
				'final' => date("d/m/Y", strtotime($dates->ultimo))
			]);
		}

		function data_balance(){
			$file = file_get_contents("php://input");
			$data_receive = json_decode($file);
			$cuota = $this->AnalisisAPIModel->get_data_utilidad_cuota($data_receive)->total;
			$mora  = $this->AnalisisAPIModel->get_data_utilidad_mora($data_receive)->total;
			$descuento = $this->AnalisisAPIModel->get_data_descuento($data_receive)->total;

			$cuota = $cuota ? $cuota : 0;
			$mora = $mora ? $mora : 0;
			$descuento = $descuento ? $descuento : 0; 
			echo json_encode([
				'cuota' => $cuota,
				'mora' => $mora,
				'descuento' => $descuento,
				'suma' => round(($cuota + $mora), 2),
				'neto' => round( (($cuota + $mora) - $descuento), 2)
			]);
		}

		function data_credito_estado(){
			$file = file_get_contents("php://input");
			$data_receive = json_decode($file);
			
			$total = $this->AnalisisAPIModel->get_creditos_estado('TOTAL');
			$activo = $this->AnalisisAPIModel->get_creditos_estado('E');
			$remate = $this->AnalisisAPIModel->get_creditos_estado('A');
			$recuperado = $this->AnalisisAPIModel->get_creditos_estado('R');
			$congelado = $this->AnalisisAPIModel->get_creditos_estado('CC');
			echo json_encode([
				'total_cantidad' => $total->cantidad,
				'total_monto' => $total->monto,
				'activo_cantidad' => $activo->cantidad,
				'activo_monto' => $activo->monto,
				'remate_cantidad' => $remate->cantidad,
				'remate_monto' => $remate->monto,
				'recuperado_cantidad' => $recuperado->cantidad,
				'recuperado_monto' => $recuperado->monto,
				'congelado_cantidad' => $congelado->cantidad,
				'congelado_monto' => $congelado->monto
			]);
		}

		function data_utilidad(){
			$file = file_get_contents("php://input");
			$data_receive = json_decode($file);
			$cuota = $this->AnalisisAPIModel->get_data_utilidad_cuota($data_receive);
			$mora  = $this->AnalisisAPIModel->get_data_utilidad_mora($data_receive);
			$descuento = $this->AnalisisAPIModel->get_data_descuento($data_receive);

			$cuota_cant = $cuota->cantidad ? $cuota->cantidad : 0;
			$cuota_monto = $cuota->total ? $cuota->total : 0;
			$mora_cant = $mora->cantidad ? $mora->cantidad : 0;
			$mora_monto = $mora->total ? $mora->total : 0;
			$descuento_cant = $descuento->cantidad ? $descuento->cantidad : 0;
			$descuento_monto = $descuento->total ? $descuento->total : 0;

			$mora = $mora ? $mora : 0;
			$descuento = $descuento ? $descuento : 0;
			echo json_encode([
				'cuota_cantidad' => $cuota_cant,
				'cuota_monto' => $cuota_monto,
				'mora_cantidad' => $mora_cant,
				'mora_monto' => $mora_monto,
				'descuento_cantidad' => $descuento_cant,
				'descuento_monto' => $descuento_monto,
				'suma' => round(($cuota_monto + $mora_monto), 2),
				'neto' => round( (($cuota_monto + $mora_monto) - $descuento_monto), 2)
			]);
		}

		function data_generacion_recuperacion(){
			$file = file_get_contents("php://input");
			$data_receive = json_decode($file);
			$gen = $this->AnalisisAPIModel->get_credito_generado($data_receive);
			$rec = $this->AnalisisAPIModel->get_credito_recuperado($data_receive);
			// $rec_cant = 0; $rec_mont = 0;
			// $gen_cant = 0; $gen_mont = 0;

			// foreach ($gen as $g) {
			// 	$gen_cant++;
			// 	$gen_mont = $gen_mont + $g->monto;
			// }			
			// foreach ($rec as $r) {
			// 	$rec_cant++;
			// 	$rec_mont = $rec_mont + $r->monto;
			// }

			echo json_encode(array(
				'genera_cantidad' => $gen->cantidad,
				'genera_monto' => $gen->total,
				'recupera_cantidad' => $rec->cantidad,
				'recupera_monto' => $rec->total,
				'diferencia' => round(($gen->total - $rec->total), 2)
			));
		}

	}
?>