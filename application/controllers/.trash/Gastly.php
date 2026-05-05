<?php 
    class Gastly extends CI_Controller
    {
        public function __construct(){
            parent::__construct();
            $this->load->model('GastlyModel');
        }

        public function service_data_general(){
			$dates = $this->GastlyModel->get_rango_fecha_sucursal();
			echo json_encode([
				'activos' => $this->GastlyModel->get_credito_activo()->value,
				'dinero' => $this->GastlyModel->get_dinero_activo()->value,
				'inicio' => date("d/m/Y", strtotime($dates->inicio)),
				'final' => date("d/m/Y", strtotime($dates->ultimo))
			]);
		}

        public function service_generacion_recuperacion(){
            $file = file_get_contents("php://input");
			$data_receive = json_decode($file);
			// $gen = $this->GastlyModel->get_credito_generado($data_receive);
			// $rec = $this->GastlyModel->get_credito_recuperado($data_receive);
            // $rem = $this->GastlyModel->get_credito_rematado($data_receive);

			$rsp = [
				'genera_cantidad' => 0,
				'genera_monto' => 0,
				'recupera_cantidad' => 0,
				'recupera_monto' => 0,
                'remate_cantidad' => 0,
                'remate_monto' => 0,
				'diferencia' => 0,
                'ident_filtro' => $this->identFilter($data_receive),
				'genera_log' => [],
				'recupera_log' => [],
				'remate_log' => []
			];

			$gens = $this->GastlyModel->get_all_credito_generado($data_receive);
			$recs = $this->GastlyModel->get_all_credito_recuperado($data_receive);
			$rems = $this->GastlyModel->get_all_credito_rematado($data_receive);
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

            // echo json_encode(array(
			// 	'genera_cantidad' => $gen ? $gen->cantidad : 0,
			// 	'genera_monto' => $gen ? $gen->total : 0,
			// 	'recupera_cantidad' => $rec ? $rec->cantidad : 0,
			// 	'recupera_monto' => $rec ? $rec->total : 0,
            //     'remate_cantidad' => $rem ? $rem->cantidad : 0,
            //     'remate_monto' => $rem != null ? $rem->total : 0,
			// 	'diferencia' => round(($gen->total - $rec->total), 2),
            //     'ident_filtro' => $this->identFilter($data_receive)
			// ));
        }

		public function service_balance_diario(){
			$rsp = [
				'cuota_cantidad' => 0,
				'cuota_suma' => 0,
				'mora_cantidad' => 0,
				'mora_suma' => 0,
				'descuento_cantidad' => 0,
				'descuento_suma' => 0,
				'utilidad_neta' => 0,
				'log' => []
			];
			$fk_data = new stdClass();
			$fk_data->mode = 'DIA_ACTUAL';
			$pagos_dets = $this->GastlyModel->get_all_pagos($fk_data);
			$descuentos = $this->GastlyModel->get_all_descuentos($fk_data);
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
			$rsp['cuota_suma'] = round($rsp['cuota_suma'], 2);
			$rsp['mora_suma'] = round($rsp['mora_suma'], 2);
			$rsp['descuento_suma'] = round($rsp['descuento_suma'], 2);
			$rsp['utilidad_neta'] = ($rsp['cuota_suma'] + $rsp['mora_suma'] - $rsp['descuento_suma']);
			echo json_encode($rsp);


			// $cuota = $this->AnalisisAPIModel->get_data_utilidad_cuota($data_receive)->total;
			// $mora  = $this->AnalisisAPIModel->get_data_utilidad_mora($data_receive)->total;
			// $descuento = $this->AnalisisAPIModel->get_data_descuento($data_receive)->total;

			// $cuota = $cuota ? $cuota : 0;
			// $mora = $mora ? $mora : 0;
			// $descuento = $descuento ? $descuento : 0; 
			// echo json_encode([
			// 	'cuota' => $cuota,
			// 	'mora' => $mora,
			// 	'descuento' => $descuento,
			// 	'suma' => round(($cuota + $mora), 2),
			// 	'neto' => round( (($cuota + $mora) - $descuento), 2)
			// ]);

		}

		public function service_data_utilidad(){
			try {
				$file = file_get_contents("php://input");
				$data_receive = json_decode($file);
				
				$result = [
					'cuota_cantidad' => 0,
					'cuota_suma' => 0,
					'mora_cantidad' => 0,
					'mora_suma' => 0,
					'descuento_cantidad' => 0,
					'descuento_suma' => 0,
					'utilidad_neta' => 0,
					'log' => []
				];
				$pagos_dets = $this->GastlyModel->get_all_pagos($data_receive);
				$descuentos = $this->GastlyModel->get_all_descuentos($data_receive);
				foreach ($pagos_dets as $pd) {
					if ($pd->tipo === 'CU') {
						$result['cuota_cantidad']++;
						$result['cuota_suma'] = $result['cuota_suma'] + floatval($pd->monto);
					}
					if ($pd->tipo === 'MO') {
						$result['mora_cantidad']++;
						$result['mora_suma'] = $result['mora_suma'] + floatval($pd->monto);
					}
					array_push($result['log'], $pd);
				}
				foreach ($descuentos as $ds) {
					$result['descuento_cantidad']++;
					$result['descuento_suma'] = $result['descuento_suma'] + floatval($ds->monto);
					array_push($result['log'], $ds);
				}
				$result['cuota_suma'] = round($result['cuota_suma'], 2);
				$result['mora_suma'] = round($result['mora_suma'], 2);
				$result['descuento_suma'] = round($result['descuento_suma'], 2);
				$result['utilidad_neta'] = ($result['cuota_suma'] + $result['mora_suma'] - $result['descuento_suma']);
				echo json_encode([
					'status' => 'OK',
					'message' => 'i work and i ok',
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

		public function service_data_utilidad_bk(){
			$file = file_get_contents("php://input");
			$data_receive = json_decode($file);
			$cuota = $this->GastlyModel->get_data_utilidad_cuota($data_receive);
			$mora  = $this->GastlyModel->get_data_utilidad_mora($data_receive);
			$descuento = $this->GastlyModel->get_data_descuento($data_receive);

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

		public function service_total_ingresos(){
            $file = file_get_contents("php://input");
			$data_receive = json_decode($file);

            $entrada = $this->GastlyModel->get_entradas_total($data_receive);
            $salida = $this->GastlyModel->get_salidas_total($data_receive);

            echo json_encode([
                'entrada_cantidad' => $entrada ? $entrada->cantidad : 0,
                'entrada_monto' => $entrada ? $entrada->total : 0,
                'salida_cantidad' => $salida ? $salida->cantidad : 0,
                'salida_monto' => $salida ? $salida->total : 0,
            ]);

        }


        public function identFilter($data){
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

        public function identMes($nro){
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

    }
    