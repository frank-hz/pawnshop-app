<?php  
	
	class Inicio extends CI_Controller
	{
		
		function __construct(){
			parent::__construct();
			if (!$this->session->userdata('pres_ses_sesion')) {
				redirect(base_url().'Acceso','refresh');
			}
			
			$this->currentDate = date("Y-m-d");
			$this->load->model('ElectroModel');
			$this->load->model('SucursalModel');
			$this->load->model('AppModel');
			$this->load->model('InicioModel');
		}

		function index(){
			// $id_suc = $this->session->userdata('pres_ses_sucursal');
			// $data = array(
			// 	'reporte' => $this->pre_procesado(),
			// 	'sucursal' => $this->SucursalModel->Buscar_detalle_breve(
			// 		array('suc_codigo' => $id_suc)
			// 	),
			// 	'info' => $this->AppModel->datos_generales(date("Y-m-d"), $id_suc)
			// );
			// $this->load->view("Layout/header.php");
			// $this->load->view("Inicio/inicio.php", $data);
			// $this->load->view("Layout/footer.php");


			$recuento = $this->InicioModel->creditosRecuento();
			$prestado = $this->InicioModel->creditosMontoPrestado()->monto ?? 0;
			$recuperado = $this->InicioModel->creditosMontoRecuperado()->monto ?? 0;
			$balance = (round(($prestado - $recuperado) * 100) / 100);
			
			$data = Array(
				'cantidad_nuevos' => $recuento->nuevos ?? 0,
				'cantidad_activos' => $recuento->activos ?? 0,
				'cantidad_vencidos' => $recuento->vencidos ?? 0,
				'cantidad_cumplidos' => $recuento->cumplidos ?? 0,
				'monto_recuperado' => (round($recuperado) * 100) / 100,
				'monto_prestado' => (round($prestado) * 100) / 100,
				'balance_total' => $balance,
				'creditos_activos' => $this->InicioModel->creditos_activos() ?? [],
				'balance_semanal' => $this->obtenerBalanceSemanal()
			);
			$this->load->view("Layout/header.php");
			// $this->load->view("Inicio/inicio.rework.php", $data);
			$this->load->view("Inicio/inicio.php", $data);
			$this->load->view("Layout/footer.php");
		}
		




		function ServiceReporte(){
			$sucursal = $this->session->userdata('pres_ses_sucursal');
			// $creditos = $this->AppModel->reporte_creditos($sucursal);
			// for ($i=0; $i < ; $i++) { 
				
			// }
			$creditos = $this->AppModel->reporte_cuotas_dia($sucursal);
			echo json_encode($creditos);
			
		}

		function pre_procesado(){
			date_default_timezone_set('America/Lima');
			$sucursal = $this->session->userdata('pres_ses_sucursal');
			$creditos = $this->AppModel->reporte_creditos($sucursal);
			$html = "";

			foreach ($creditos as $cd) {
				if ($cd->estado === 'CC') {
					$cuota = $this->AppModel->reporte_ultima_cuota($cd->id);
					$html .= "<tr style='background:#B9D8F8;'>";
					$html .= "<td>$cuota->estado</td>";
					$html .= "<td><a href='".base_url()."Credito/detalle/$cuota->credito_id'>
						#$cuota->credito
					</td>";
					$html .= "<td>$cuota->orden</td>";
					$html .= "<td>$cuota->cliente</td>";
					$html .= "<td>S/ $cuota->prestamo</td>";
					$html .= "<td>S/ $cuota->interes</td>";
					$html .= "<td>S/ $cuota->penalidad</td>";
					$html .= "<td>$cuota->vencimiento</td>";
					$html .= "<td>S/ $cuota->mora</td>";
					$html .= "<td>$cuota->dias</td>";
					$html .= "<td>S/ $cuota->prorrateo</td>";
					$html .= "</tr>";
				}elseif($cd->estado === 'E'){
					$cuotas = $this->AppModel->reporte_cuotas($cd->id);
					if (!empty($cuotas)) {
						foreach ($cuotas as $cu) {
							$css = '';
							$fecha = date("Y-m-d");
							if ($cu->estado === 'V') {
								$css = "background:#FAD2D4";
							}
							if ($cu->vence == $fecha) {
								$css = "background:#FCDF8E";
							}
							$html .= "<tr style=$css>";
							$html .= "<td>$cu->estado</td>";
							$html .= "<td><a href='".base_url()."Credito/detalle/$cu->credito_id'>
								#$cu->credito
							</td>";
							$html .= "<td>$cu->orden</td>";
							$html .= "<td>$cu->cliente</td>";
							$html .= "<td>S/ $cu->prestamo</td>";
							$html .= "<td>S/ $cu->interes</td>";
							$html .= "<td>S/ $cu->penalidad</td>";
							$html .= "<td>$cu->vencimiento</td>";
							$html .= "<td>S/ $cu->mora</td>";
							$html .= "<td>$cu->dias</td>";
							$html .= "<td>S/ $cu->prorrateo</td>";
							$html .= "</tr>";
						}
					}else{
						$cuota = $this->AppModel->reporte_ultima_cuota($cd->id);
						$vence = date("Y-m-d", strtotime(date($cuota->vencimiento). ' + 7 days'));
						$html .= "<tr>";
						$html .= "<td>E</td>";
						$html .= "<td><a href='".base_url()."Credito/detalle/$cuota->credito_id'>
							#$cuota->credito
						</td>";
						$html .= "<td>$cuota->orden</td>";
						$html .= "<td>$cuota->cliente</td>";
						$html .= "<td>S/ $cuota->prestamo</td>";
						$html .= "<td>S/ $cuota->interes</td>";
						$html .= "<td>S/ $cuota->penalidad</td>";
						$html .= "<td>$vence</td>";
						$html .= "<td>S/ $cuota->mora</td>";
						$html .= "<td>$cuota->dias</td>";
						$html .= "<td>S/ $cuota->prorrateo</td>";
						$html .= "</tr>";
					}
				}
			}
			return $html;
		}

		function reporte_generar(){
			$this->load->library('pdf');
			ini_set('memory_limit', '256M');
			set_time_limit(500);
			$estilo = base_url()."assets/css/reporte_diario.css";
			$fav = base_url()."assets/images/electrocash_favicon.png";
			$fecha = date("m-d-Y");
			$html = "";
			$cuota = $this->pre_procesado();

			$html .= "<!DOCTYPE html>
				<html lang='es'>
				<head>
					<meta name='viewport' content='width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0'>
					<meta charset='UTF-8'>
					<link rel='shortcut icon' type='image/png' href=$fav>
					<title>Reporte Diario $fecha</title>
					<link rel='stylesheet' href=$estilo>
				</head>
				<body>";
			$html .= "<div class='reporte'>";
			$html .= "	<div class='reporte-data'>
							<table class='tabla-reporte'>
								<thead>
									<tr>
										<th>STATUS</th>
								        <th>CREDITO</th>
										<th>NRO CUOTA</th>
								        <th>CLIENTE</th>
								        <th>PRESTAMO</th>
								        <th>INTERES</th>
								        <th>MORA</th>
								        <th>VENCIMIENTO</th>
								        <th>DIAS</th>
								        <th>MORA TOTAL</th
								        <th>MONTO A PAGAR</th>
								        <th style='background:#393e46;color:#fff;text-align:center;font-size:8px'>$fecha</th>
									</tr>
								</thead>
								<tbody>
									$cuota
								</tbody>
							</table>
						</div>";
			$html .= "</div>";
			$html .= "</body></html>";
			
			$this->pdf->loadHtml($html);
			$this->pdf->setPaper('A4', 'potrait');
			$this->pdf->render();
			$this->pdf->stream("Electrocash_reporte.pdf",array("Attachment" => false));
		}

		function reporte_pre(){
			date_default_timezone_set('America/Lima');
			$sucursal = $this->session->userdata('pres_ses_sucursal');
			$creditos = $this->AppModel->Lista_candidatos_reporte($sucursal);
			$fake_array = [];
			$second_array = [];

			if ($creditos != null) {
				foreach ($creditos as $cd) {
					$cuotas = $this->AppModel->Lista_reporte_diario($cd->id);
					if ($cuotas != null) {
						$activo = "NO";
						$orden = 0;
						$item = [];
						foreach ($cuotas as $c) {
							array_push($fake_array, $c);							
						}

						if ($activo === "NO") {
							$second_array = $item;
						}
					}
				}
			}
			return $fake_array;
		}

		function obtenerBalanceSemanal(){
			$fechas = [];
			$data = [];
			$days = array(
				"Monday" => "Lunes",
				"Tuesday" => "Martes",
				"Wednesday" => "Miércoles",
				"Thursday" => "Jueves",
				"Friday" => "Viernes",
				"Saturday" => "Sabado",
				"Sunday" => "Domingo",
			);

			for ($i = 6; $i >= 0; $i--) {
				// $fechas[] = date('Y-m-d', strtotime("-$i days"));
				$temp = new stdclass();
				$fecha = date('Y-m-d', strtotime("-$i days"));
				$nombre_en = date("l", strtotime($fecha));
				$prestado = $this->InicioModel->creditosMontoPrestado($fecha)->monto ?? 0;
				$recupera = $this->InicioModel->creditosMontoRecuperado($fecha)->monto ?? 0;
				$balance = (round(($prestado - $recupera) * 100) / 100);

				$temp->fecha = $fecha;
				$temp->nombre = $days[$nombre_en];
				$temp->prestado = $prestado;
				$temp->recuperado = $recupera;
				$temp->balance = $balance;
				$data[] = $temp;
			}
			// print_r($data);
			return $data;
		}
		
	}
?>