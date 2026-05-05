<?php  

	class Absolver extends CI_Controller
	{
		public $suc = 0; 
		function __construct(){
			parent::__construct();
			$this->load->model('AbsolverModel');
			$this->suc = $this->session->userdata('pres_ses_sucursal');
		}	
		// activos | vencidos | cumplidos
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

		function service_reporte_procesado(){
			$response = [];
			$creditos = $this->AbsolverModel->general_data_creditos_activos($this->suc);
			foreach ($creditos as $c) {			
				if ($c->estado === 'CC') {
					$last_cuota = $this->AbsolverModel->rep_ultima_cuota($c->id);
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
					$cuotas = $this->AbsolverModel->rep_cuotas($c->id);
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
						$lstcuota = $this->AbsolverModel->rep_ultima_cuota($c->id);
						$nxt_venc = date("Y-m-d", strtotime(date($lstcuota->vencimiento). ' + 7 days'));
                        //die(json_encode($nxt_venc));
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
		
		function generate_pdf(){
			try {
				ob_start();
				error_reporting(E_ALL & ~E_NOTICE);
				ini_set('display_errors', 0);
				ini_set('log_errors', 1);
				$this->load->library('MYPDF');
				$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
				$pdf->SetCreator('Electrocash Inc.');
				$pdf->SetAuthor('Electrocash Admin');
				$pdf->SetTitle('Reporte Diario');
				$pdf->SetSubject('Data Actualizada: '.date("m-d-Y"));

				$pdf->setFooterData(array(0,64,0), array(0,64,128));
				$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

				$pdf->SetMargins(7,13,0);

				$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

				$pdf->SetAutoPageBreak(TRUE, 8);
				$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
				if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
					require_once(dirname(__FILE__).'/lang/eng.php');
					$pdf->setLanguageArray($l);
				}
				$pdf->SetFont('dejavusans', '', 5.5);
				$pdf->AddPage();
				

				$resp_html = "";
				$creditos = $this->AbsolverModel->general_data_creditos_activos($this->suc);
				foreach ($creditos as $c) {
					if ($c->estado === 'CC') {
						$ucuota = $this->AbsolverModel->rep_ultima_cuota($c->id);
						$resp_html .= '
							<tr class="file-congelado">
								<td width="33px" align="center">'.$ucuota->estado.'</td>
								<td width="55px" align="left">'.$c->serie.'</td>
								<td align="center">'.$ucuota->orden.'</td>
								<td width="200px" align="left">'.$c->cliente.'</td>
								<td width="55px" align="left">S/ '.$ucuota->prestamo.'</td>
								<td width="50px" align="left">S/ '.$ucuota->interes.'</td>
								<td width="45px" align="left">S/ '.$ucuota->penalidad.'</td>
								<td width="50px"align="left">'.$ucuota->vencimiento.'</td>
								<td width="50px" align="left">S/ '.$ucuota->mora.'</td>
								<td width="35px" align="center">'.$ucuota->dias.'</td>
								<td width="45px" align="left">S/ '.$ucuota->prorrateo.'</td>
								<td width="70px" bgcolor="#fff"></td>
							</tr>
						';
					} elseif($c->estado === 'E'){
						$cuotas = $this->AbsolverModel->rep_cuotas($c->id);
						if (!empty($cuotas)) {
							$class_estado = '';
							$curdate = date('d/m/Y');
							$cuotasven = 0;
							foreach ($cuotas as $cts) {
								if ($cts->estado === 'V') $cuotasven++;
							}
							foreach ($cuotas as $ct) {
								if ($ct->estado === 'E') $class_estado = '';
								if ($ct->estado === 'V') $class_estado = 'file-vencido';
								if ($ct->vencimiento === $curdate) {
									$class_estado = 'file-cumplido';
								}
								$resp_html .= '
									<tr class="'.$class_estado.'">
										<td width="33px" align="center">'.$ct->estado.'</td>
										<td width="55px" align="left">'.$c->serie.'</td>
										<td align="center">'.$ct->orden.'</td>
										<td width="200px" align="left">'.$c->cliente.'</td>
										<td width="55px" align="left">S/ '.$ct->prestamo.'</td>
										<td width="50px" align="left">S/ '.$ct->interes.'</td>
										<td width="45px" align="left">S/ '.$ct->penalidad.'</td>
										<td width="50px"align="left">'.$ct->vencimiento.'</td>
										<td width="50px" align="left">S/ '.$ct->mora.'</td>
										<td width="35px" align="center">'.$ct->dias.'</td>
										<td width="45px" align="left">S/ '.$ct->prorrateo.'</td>
										<td width="70px" bgcolor="#fff" border="black"></td>
									</tr>
								';
							}
	
						}else {
							$utcuota = $this->AbsolverModel->rep_ultima_cuota($c->id);
							$nxt_venc = date("Y-m-d", strtotime(date($utcuota->vencimiento). ' + 7 days'));
							
							$resp_html .= '
								<tr class="file-blank">
									<td width="33px" align="center">'.$utcuota->estado.'</td>
									<td width="55px" align="left">'.$c->serie.'</td>
									<td align="center">'.$utcuota->orden.'</td>
									<td width="200px" align="left">'.$c->cliente.'</td>
									<td width="55px" align="left">S/ '.$utcuota->prestamo.'</td>
									<td width="50px" align="left">S/ '.$utcuota->interes.'</td>
									<td width="45px" align="left">S/ '.$utcuota->penalidad.'</td>
									<td width="50px"align="left">'.$utcuota->vencimiento.'</td>
									<td width="50px" align="left">S/ '.$utcuota->mora.'</td>
									<td width="35px" align="center">'.$utcuota->dias.'</td>
									<td width="45px" align="left">S/ '.$utcuota->prorrateo.'</td>
									<td width="70px" bgcolor="#fff" border="black"></td>
								</tr>
							';
						}					
					}
				}

				$final_html = '
					<style>						
						.reporte {
							width: 100%;
							color: #10253D;
							border-collapse: collapse;
							border: 1px solid #E3E9F2;
						}
						table.reporte th {
							border: 1px solid black;
						}
						table.reporte th, table.reporte td {
							text-align: left;							
							word-wrap: break-word;
						}
						tr.file-congelado td {
							background-color: #B9D8F8;
						}
						tr.file-vencido td {
							background-color: #FAD2D4;
						}
						tr.file-cumplido td {
							background-color: #FCDF8E;
						}
						
					</style>
					<table class="reporte" cellpadding="2.5">						
						<tr>
							<th width="33px">Status</th>
							<th width="55px" align="left">Credito</th>
							<th width="50px">Nro Cuota</th>
							<th width="200px" align="left">Cliente</th>
							<th width="55px" align="left">Prestamo</th>
							<th width="50px" align="left">Interes</th>
							<th width="45px" align="left">Mora</th>
							<th width="50px" align="left">Vencimiento</th>
							<th width="50px" align="left">Mora Total</th>
							<th width="35px" align="center">Dias</th>
							<th width="45px" align="left">Prorrateo</th>
							<th width="70px" bgcolor="#141414" color="#fff">'.date("d-m-Y").'</th>
						</tr>
						'.$resp_html.'
					</table>
					';
				$pdf->writeHTML($final_html, true, false, true, false, '');
				$pdf->Output('reporte.pdf', 'I');
			} catch (Exception $e) {
				echo $e->getMessage();
			}
		}
	}
?>
