<?php 
	
	class AnalisisRest extends CI_Controller
	{        
        function __construct(){
            parent::__construct();
			$this->load->model('AnalisisRestModel');
        }

        // Services
		function service_get_timeline(){
			$resp = $this->AnalisisRestModel->get_line_time();
			echo json_encode($resp);
		}
        function service_data_general(){
			$dates = $this->AnalisisRestModel->get_rango_fecha_sucursal();
			// echo json_encode($dates);
			echo json_encode([
				'activos' => $this->AnalisisRestModel->get_credito_activo()->value,
				'dinero' => $this->AnalisisRestModel->get_dinero_activo()->value,
				'inicio' => date("d/m/Y", strtotime($dates->inicio)),
				'final' => date("d/m/Y", strtotime($dates->final))				
			]);
		}
        function service_generacion_recuperacion_bk(){
            $file = file_get_contents("php://input");
			$data_receive = json_decode($file);
			$rsp = [
				'genera_cantidad' => 0,
				'genera_monto' => 0,
				'recupera_cantidad' => 0,
				'recupera_monto' => 0,
                'remate_cantidad' => 0,
                'remate_monto' => 0,
				'diferencia' => 0,
				'genera_log' => [],
				'recupera_log' => [],
				'remate_log' => []
			];

			$gens = $this->AnalisisRestModel->get_all_credito_generado($data_receive);
			$recs = $this->AnalisisRestModel->get_all_credito_recuperado_v2($data_receive);
			$rems = $this->AnalisisRestModel->get_all_credito_rematado($data_receive);

			
			foreach ($gens as $g) {
				$rsp['genera_cantidad']++;
				$rsp['genera_monto'] = $rsp['genera_monto'] + $g->monto;
				array_push($rsp['genera_log'], $g);
			}
			foreach ($recs as $rc) {
				$rsp['recupera_cantidad']++;
				$rsp['recupera_monto'] = $rsp['recupera_monto'] + $rc->monto_prestado;
				array_push($rsp['recupera_log'], $rc);
			}
			foreach ($rems as $rm) {
				$rsp['remate_cantidad']++;
				$rsp['remate_monto'] = $rsp['remate_monto'] + $rm->monto;
				array_push($rsp['remate_log'], $rm);
			}	
			$rsp['diferencia'] = round( ($rsp['genera_monto'] - $rsp['recupera_monto']),2 );
			echo json_encode($rsp);
        }
		function service_generacion_recuperacion(){
			$filters = json_decode($this->input->raw_input_stream);
			if (!is_object($filters)) {
				return $this->output
				->set_status_header(400)
				->set_content_type('application/json')
				->set_output(json_encode(['error' => 'Solucitud invalida.']));
			}

			$gens = $this->AnalisisRestModel->get_all_credito_generado_v2($filters);
			$recs = $this->AnalisisRestModel->get_all_credito_recuperado_v2($filters);
			$rems = $this->AnalisisRestModel->get_all_credito_rematado_v2($filters);
			$clientes = $this->AnalisisRestModel->get_clientes_registrados($filters);
			
			$genera_monto    = array_sum(array_column($gens, 'monto_prestado'));
			$recupera_monto  = array_sum(array_column($recs, 'monto_capital'));
			$remate_monto    = array_sum(array_column($rems, 'monto_prestado'));

			$rsp = [
				'genera_cantidad'   => count($gens),
				'genera_monto'      => $genera_monto,
				'recupera_cantidad' => count($recs),
				'recupera_monto'    => $recupera_monto,
				'remate_cantidad'   => count($rems),
				'remate_monto'      => $remate_monto,
				'diferencia'        => round($genera_monto - $recupera_monto, 2),
				'genera_log'        => $gens,
				'recupera_log'      => $recs,
				'remate_log'        => $rems,
				'clientes' => $clientes
			];

			$this->output
				->set_content_type('application/json')
				->set_output(json_encode($rsp));
        }
        function service_generacion_recuperacion_dia(){
            $file = file_get_contents("php://input");
			$data_receive = json_decode($file);

			$rsp = [
				'genera_cantidad' => 0,
				'genera_monto' => 0,
				'recupera_cantidad' => 0,
				'recupera_monto' => 0,
                'remate_cantidad' => 0,
                'remate_monto' => 0,
				'diferencia' => 0,
                'ident_filtro' => $this->filter_recogn($data_receive),
				'genera_log' => [],
				'recupera_log' => [],
				'remate_log' => []
			];

			$gens = $this->AnalisisRestModel->get_all_credito_generado($data_receive);
			$recs = $this->AnalisisRestModel->get_all_credito_recuperado($data_receive);
			$rems = $this->AnalisisRestModel->get_all_credito_rematado($data_receive);
			foreach ($gens as $g) {
				$rsp['genera_cantidad']++;
				$rsp['genera_monto'] = $rsp['genera_monto'] + intval($g->monto);
				array_push($rsp['genera_log'], $g);
			}
			foreach ($recs as $rc) {
				$rsp['recupera_cantidad']++;
				$rsp['recupera_monto'] = $rsp['recupera_monto'] + intval($rc->monto);
				array_push($rsp['recupera_log'], $rc);
			}
			foreach ($rems as $rm) {
				$rsp['remate_cantidad']++;
				$rsp['remate_monto'] = $rsp['remate_monto'] + intval($rm->monto);
				array_push($rsp['remate_log'], $rm);
			}	
			$rsp['diferencia'] = round( ($rsp['genera_monto'] - $rsp['recupera_monto']),2 );
			echo json_encode($rsp);
        }
        function service_data_utilidad(){
			try {
				$filters = json_decode($this->input->raw_input_stream);
				if (!is_object($filters)) {
					return $this->output
					->set_status_header(400)
					->set_content_type('application/json')
					->set_output(json_encode([
						'status' => 'ERR',
						'message' => 'Solucitud invalida.',
					]));
				}
				$pagos       = $this->AnalisisRestModel->get_all_pagos_v2($filters);
				$descuentos  = $this->AnalisisRestModel->get_all_descuentos_v2($filters);
				$adicionales = $this->AnalisisRestModel->get_all_adicionales($filters);

				$cuotaCant = $moraCant = 0;
				$cuotaSum  = $moraSum  = 0.0;
				$log = [];

				foreach ($pagos as $pd) {
					$monto = (float) $pd->monto;
					if ($pd->tipo === 'CU') {
						$cuotaCant++;
						$cuotaSum += $monto;
					} elseif ($pd->tipo === 'MO') {
						$moraCant++;
						$moraSum += $monto;
					}
					$log[] = $pd;
				}

				$descCant = 0;
				$descSum  = 0.0;
				foreach ($descuentos as $ds) {
					$descCant++;
					$descSum += (float) $ds->monto;
					$log[] = $ds;
				}

				$adicCant = 0;
				$adicSum  = 0.0;
				foreach ($adicionales as $ad) {
					$adicCant++;
					$adicSum += (float) $ad->monto;
				}

				// Redondeo único al final
				$result = [
					'cuota_cantidad'     => $cuotaCant,
					'cuota_suma'         => round($cuotaSum, 2),
					'mora_cantidad'      => $moraCant,
					'mora_suma'          => round($moraSum, 2),
					'adicional_cantidad' => $adicCant,
					'adicional_suma'     => round($adicSum, 2),
					'descuento_cantidad' => $descCant,
					'descuento_suma'     => round($descSum, 2),
					'utilidad_neta'      => round($cuotaSum + $moraSum + $adicSum - $descSum, 2),
					'log'                => $log,
				];
				echo json_encode([
					'status' => 'OK',
					'ident_filter' => $this->filter_recogn($filters),
					'result' => $result
				]);
			} catch (Exception $e) {
				echo json_encode([
					'status' => 'ERR',
					'message' => $e->getMessage(),
					'result' => []
				]);
			}
		}
        function service_total_ingresos(){
            $file = file_get_contents("php://input");
			$data_receive = json_decode($file);

            $entrada = $this->AnalisisRestModel->get_entradas_total($data_receive);
            $salida = $this->AnalisisRestModel->get_salidas_total($data_receive);

            echo json_encode([
                'entrada_cantidad' => $entrada ? $entrada->cantidad : 0,
                'entrada_monto' => $entrada ? round($entrada->total,1) : 0,
                'salida_cantidad' => $salida ? $salida->cantidad : 0,
                'salida_monto' => $salida ? round($salida->total,1) : 0,
				'total_diferencia' => round(($entrada->total - $salida->total),1)
            ]);

        }
        function service_balance_diario(){
			$rsp = [
				'cuota_cantidad' => 0,
				'cuota_suma' => 0,
				'mora_cantidad' => 0,
				'mora_suma' => 0,
				'adicional_cantidad' => 0,
				'adicional_suma' => 0,
				'descuento_cantidad' => 0,
				'descuento_suma' => 0,
				'utilidad_neta' => 0,
				'log' => []
			];
			$fk_data = new stdClass();
			$fk_data->mode = 'DIA_ACTUAL';
			$pagos_dets = $this->AnalisisRestModel->get_all_pagos($fk_data);
			$descuentos = $this->AnalisisRestModel->get_all_descuentos($fk_data);
			$adicionales = $this->AnalisisRestModel->get_all_adicionales($fk_data);
			foreach ($pagos_dets as $pd) {
				if ($pd->tipo === 'CU') {
					$rsp['cuota_cantidad']++;
					$rsp['cuota_suma'] = $rsp['cuota_suma'] + floatval($pd->monto);
				}
				if ($pd->tipo === 'MO') {
					$rsp['mora_cantidad']++;
					$rsp['mora_suma'] = $rsp['mora_suma'] + floatval($pd->monto);
				}
				array_push($rsp['log'], $pd);
			}
			foreach ($descuentos as $ds) {
				$rsp['descuento_cantidad']++;
				$rsp['descuento_suma'] = $rsp['descuento_suma'] + floatval($ds->monto);
				array_push($rsp['log'], $ds);
			}
			foreach ($adicionales as $ad) {
				$rsp['adicional_cantidad']++;
				$rsp['adicional_suma'] = $rsp['adicional_suma'] + floatval($ad->monto);
				array_push($rsp['log'], $ad);
			}
			$rsp['cuota_suma'] = round($rsp['cuota_suma'], 2);
			$rsp['mora_suma'] = round($rsp['mora_suma'], 2);
			$rsp['descuento_suma'] = round($rsp['descuento_suma'], 2);
			$rsp['adicional_suma'] = round($rsp['adicional_suma'], 2);
			$rsp['utilidad_neta'] = $rsp['cuota_suma'] + $rsp['mora_suma'] + $rsp['adicional_suma'] - $rsp['descuento_suma'];

			echo json_encode($rsp);
		}




        function filter_recogn($data){
            $resp = "";
            switch ($data->mode) {
                case 'MES':
                    $ms = $this->identMes($data->in_mes);
                    $resp = "$ms del $data->in_anio";
                    break;
                
                case 'ANIO':
                    $resp = "$data->in_anio";
                    break;
                
                case 'RANGO':
					$id_format = date_format(date_create($data->in_rango_inicial), "d/m/Y");
					$fd_format = date_format(date_create($data->in_rango_final), "d/m/Y");

                    $resp = "$id_format &nbsp;<span class='text-black'>al</span>&nbsp; $fd_format";
                    break;

                case 'FECHA':
					$d_format = date_format(date_create($data->in_fecha), "d/m/Y");
                    $resp = "$d_format";
                    break;
                
                case 'MES_ACTUAL':
					$anioAC = date("Y");
                    $mesAC = date("m");
                    $mesAC = $this->identMes($mesAC);
                    $resp = "$mesAC del $anioAC";
                    break;
            }
            return $resp;
        }
        function identMes($nro){
            $month_bank = [
                '01' => 'ENERO', '02' => 'FEBRERO',
                '03' => 'MARZO', '04' => 'ABRIL',
                '05' => 'MAYO', '06' => 'JUNIO',
                '07' => 'JULIO', '08' => 'AGOSTO',
                '09' => 'SEPTIEMBRE', '10' => 'OCTUBRE',
                '11' => 'NOVIEMBRE', '12' => 'DICIEMBRE',
            ];
            return $month_bank[$nro]; 
        }
		function service_regognize_filter(){
			$data = json_decode(file_get_contents('php://input'));
			switch ($data->mode) {
                case 'MES':
                    $ms = $this->identMes($data->in_mes);
                    $resp = "$ms del $data->in_anio";
                    break;
                
                case 'ANIO':
                    $resp = "$data->in_anio";
                    break;
                
                case 'RANGO':
					$id_format = date_format(date_create($data->in_rango_inicial), "d/m/Y");
					$fd_format = date_format(date_create($data->in_rango_final), "d/m/Y");

                    $resp = "$id_format &nbsp;<span class='text-black'>al</span>&nbsp; $fd_format";
                    break;

                case 'FECHA':
					$d_format = date_format(date_create($data->in_fecha), "d/m/Y");
                    $resp = "$d_format";
                    break;
                
                case 'MES_ACTUAL':
					$anioAC = date("Y");
                    $mesAC = date("m");
                    $mesAC = $this->identMes($mesAC);
                    $resp = "$mesAC del $anioAC";
                    break;
            }
			echo json_encode([
				'status' => 'OK',
				'response' => $resp
			]);
		}
    }

?>