<?php  
	
	class Reporte extends CI_Controller
	{	
		public $idsuc = 0; 
		function __construct(){
			parent::__construct();
			$this->load->model('ReporteModel');
			$this->load->model('AbsolverModel');
			$this->idsuc = $this->session->userdata('pres_ses_sucursal');
			// $this->load->library('pdf');
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


		// encode 01-2023
		function service_reporte_procesado(){
			$response = [];
			$creditos = $this->ReporteModel->general_data_creditos_activos($this->idsuc);
			foreach ($creditos as $c) {			
				if ($c->estado === 'CC') {
					$last_cuota = $this->ReporteModel->rep_ultima_cuota($c->id);
					array_push($response, [
						'class'   => 'file-congelado',
						'estado'  => 'CC',
						'sub_estado' => 'CC',
						'url' 	  => base_url()."Credito/detalle/".$c->id,
						'credito' => $c->id,
						'serie' => $c->serie,
						'cliente' => $c->cliente,
						'orden' => $last_cuota->orden,
						'prestamo' => $last_cuota->prestamo,
						'interes' => $last_cuota->interes,
						'penalidad' => $last_cuota->penalidad,
						'vencimiento' => $last_cuota->vencimiento,
						'mora' => $last_cuota->mora,
						'dias' => $last_cuota->dias,
						'prorrateo' => $last_cuota->prorrateo
					]);
				} elseif($c->estado === 'E'){
					$cuotas = $this->ReporteModel->rep_cuotas($c->id);
					if (!empty($cuotas)) {
						$class_estado = '';
						$curdate = date('d/m/Y');
						$cuotasven = 0;
						foreach ($cuotas as $cts) {
							if ($cts->estado === 'V') {
								$cuotasven++;
							}
						}
						foreach ($cuotas as $ct) {
							$sraw = $ct->estado;
							if ($ct->estado === 'E') $class_estado = '';
							if ($ct->estado === 'V') $class_estado = 'file-vencido';
							if ($ct->vencimiento === $curdate) {
								$class_estado = 'file-cumplido';
								$sraw = 'CPD';
							}
							array_push($response, [
								'class'   => $class_estado,
								'estado'  => $ct->estado,
								'sub_estado'  => $cuotasven >= 8 ? 'RPX' : $sraw,
								'url' 	  => base_url()."Credito/detalle/".$c->id,
								'credito' => $c->id,
								'serie' => $c->serie,
								'cliente' => $c->cliente,
								'orden' => $ct->orden,
								'prestamo' => $ct->prestamo,
								'interes' => $ct->interes,
								'penalidad' => $ct->penalidad,
								'vencimiento' => $ct->vencimiento,
								'mora' => $ct->mora,
								'dias' => $ct->dias,
								'prorrateo' => $ct->prorrateo
							]);
						}

					}else {
						$lstcuota = $this->ReporteModel->rep_ultima_cuota($c->id);
						$nxt_venc = date("Y-m-d", strtotime(date($lstcuota->vencimiento). ' + 7 days'));
						array_push($response, [
							'class'   => '',
							'estado'  => 'E',
							'sub_estado' => 'E',
							'url' 	  => base_url()."Credito/detalle/".$c->id,
							'credito' => $c->id,
							'serie' => $c->serie,
							'cliente' => $c->cliente,
							'orden' => $ct->orden,
							'prestamo' => $ct->prestamo,
							'interes' => $ct->interes,
							'penalidad' => $ct->penalidad,
							'vencimiento' => $nxt_venc,
							'mora' => $ct->mora,
							'dias' => $ct->dias,
							'prorrateo' => $ct->prorrateo
						]);
					}					
				}
			}

			echo json_encode($response);
		}		
		function service_dashboard_info(){	
			$suc = $this->session->userdata('pres_ses_sucursal');
			$response = [
				'cantidad_activos' => 0,
				'cantidad_vencidos' => 0,
				'cantidad_cumplidos' => 0,
				'movimiento_registros' => 0,
				'movimiento_prestado' => 0,
				'movimiento_recuperado' => 0,
				'movimiento_diferencia' => 0,
				'stash' => []
			];
			
			
			$creditos = $this->AbsolverModel->general_data_creditos_activos($suc);
			$response['cantidad_activos'] = count($creditos);
			foreach ($creditos as $c) {

				$cuotas = $this->AbsolverModel->cuotas_x_credito($c->id);
				$cont_venc = 0; $cont_cump = 0;
				foreach ($cuotas as $cu) {
					if ($cu->estado === 'V') $cont_venc++;
					if (strtotime($cu->vencimiento) === strtotime(date("Y-m-d"))) {
						$cont_cump++;
						array_push($response['stash'], $cu);
					}
				}
				if ($cont_venc > 0) $response['cantidad_vencidos']++;
				if ($cont_cump > 0) $response['cantidad_cumplidos']++;
			}

			$response['movimiento_registros'] = floatval(
				$this->AbsolverModel->registros_x_dia($suc)->valor);
			$response['movimiento_prestado'] = floatval(
				$this->AbsolverModel->mov_prestado_x_dia($suc)->valor);
			$response['movimiento_recuperado'] = floatval(
				$this->AbsolverModel->mov_recuperado_x_dia($suc)->valor);

			$response['movimiento_diferencia'] = $response['movimiento_prestado'] - $response['movimiento_recuperado'];
			echo json_encode($response);
		}

		// encode 05-2023
		function service_reporte_procesado_v2(){
			$creditos = $this->ReporteModel->general_data_creditos_activos($this->idsuc);
			$cuotas = [];
			$curdate = date('d/m/Y');
			foreach ($creditos as $credito) {
				if ($credito->estado === 'CC') {
					$ultima_cuota = $this->ReporteModel->rep_ultima_cuota($credito->id);
					if (empty($ultima_cuota)) continue;
					array_push($cuotas, [
						'vencido_hoy' => ($ultima_cuota->vencimiento == $curdate),
						'cuotas_vencidas' => 0,
						'estado' => 'CC',
						'url' 	  => base_url()."Credito/detalle/".$credito->id,
						'credito' => $credito->id,
						'serie' => $credito->serie,
						'cliente' => $credito->cliente,
						'orden' => $ultima_cuota->orden,
						'prestamo' => $ultima_cuota->prestamo,
						'interes' => $ultima_cuota->interes,
						'penalidad' => $ultima_cuota->penalidad,
						'vencimiento' => $ultima_cuota->vencimiento,
						'mora' => $ultima_cuota->mora,
						'dias' => $ultima_cuota->dias,
						'prorrateo' => $ultima_cuota->prorrateo
					]);
				}elseif ($credito->estado === 'E'){
					$cuotas_activas = $this->ReporteModel->rep_cuotas($credito->id);
					$cant_vencido = 0;

					foreach ($cuotas_activas as $cuotact) {
						if ( $cuotact->vencimiento <= $curdate ) $cant_vencido++;
					}
					foreach ($cuotas_activas as $cuotact) {
						$vencimiento = $cuotact->vencimiento;
						array_push($cuotas, [
							'vencido_hoy' => ($vencimiento == $curdate),
							'cuotas_vencidas' => $cant_vencido,
							'estado'  => $cuotact->estado,
							'url' 	  => base_url()."Credito/detalle/".$credito->id,
							'credito' => $credito->id,
							'serie' => $credito->serie,
							'cliente' => $credito->cliente,
							'orden' => $cuotact->orden,
							'prestamo' => $cuotact->prestamo,
							'interes' => $cuotact->interes,
							'penalidad' => $cuotact->penalidad,
							'vencimiento' => $cuotact->vencimiento,
							'mora' => $cuotact->mora,
							'dias' => $cuotact->dias,
							'prorrateo' => $cuotact->prorrateo
						]);
					}
				}
				
				
			}

			echo json_encode($cuotas);
		}
	}
?>