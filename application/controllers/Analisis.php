<?php 
	
	class Analisis extends CI_Controller
	{
		
		function __construct()
		{
			parent::__construct();
			if (!$this->session->userdata('pres_ses_sesion')) {
				redirect(base_url().'Acceso','refresh');
			}
			$this->load->model('AnalisisModel');
		}

		// vistas
		function index()
		{
		    if ($this->session->userdata('pres_ses_rol') === 'Administrador') {
				$this->load->view("Analisis/index.php");
			}
			//$this->load->view('Layout/header2.php');
			// $this->load->view('Analisis/new.php');
			//$this->load->view('Analisis/prototipo.php');
			//$this->load->view('Layout/footer.php');	
		}

		function prototipo(){
			// $this->load->view('Layout/analityc.php');
			$this->load->view('Layout/header2.php');
			$this->load->view('Analisis/prototipo.php');
			$this->load->view('Layout/footer.php');
		}

		function utilidad()
		{
			$this->load->view('Layout/dashboard_header.php');
			$this->load->view('Analisis/utilidad.php');
			$this->load->view('Layout/dashboard_footer.php');
		}

		function generacion()
		{
			$this->load->view('Layout/dashboard_header.php');
			$this->load->view('Analisis/generacion.php');
			$this->load->view('Layout/dashboard_footer.php');
		}

		function inventario()
		{
			$this->load->view('Layout/dashboard_header.php');
			$this->load->view('Analisis/inventario.php');
			$this->load->view('Layout/dashboard_footer.php');
		}

		function avanzado()
		{
			if ($this->session->userdata('pres_ses_rol') === 'Administrador') {
				//$this->load->view("Layout/header.php");
				$this->load->view("Analisis/template.php");
				//$this->load->view("Layout/footer.php");
			}
		}

		function rebuild()
		{
			if ($this->session->userdata('pres_ses_rol') === 'Administrador') {
				//$this->load->view("Layout/header.php");
				$this->load->view("Analisis/rebuild.php");
				//$this->load->view("Layout/footer.php");
			}
		}

		//Servicios-Funciones
		function EvalMes($mes)
		{
			$months = array(
				"01" => "Enero",
				"02" => "Febrero",
				"03" => "Marzo",
				"04" => "Abril",
				"05" => "Mayo",
				"06" => "Junio",
				"07" => "Julio",
				"08" => "Agosto",
				"09" => "Septiembre",
				"10" => "Octubre",
				"11" => "Noviembre",
				"12" => "Diciembre",
			);
			$data_out = [];
			return $months[$mes];
		}

		//Procesa la consulta de las temporadas anuales
		function ServiceTemporadas()
		{
			try {
				if (!$_POST) throw new Exception("Datos no encontrados", 1);
				$sucursal = $this->session->userdata('pres_ses_sucursal');
				$data = $this->AnalisisModel->Get_LineaTemporal($sucursal);

				echo json_encode($data);
			} catch (Exception $e) {
				echo json_encode([]);
			}
		}

		// Utilidad
		function ServiceUtilidadAnual()
		{	
			try {
				$sucursal = $this->session->userdata('pres_ses_sucursal');
				$temporadas = $this->AnalisisModel->Get_LineaTemporal($sucursal);
				$resp = [];
				foreach ($temporadas as $temp) {
					$ut = $this->AnalisisModel->Get_UtilidadBruta_Anual($sucursal, $temp->nombre);
					$ut_proc = ($ut->monto) ? $ut->monto : 0.00;
					array_push($resp, ['nombre' => $temp->nombre, 'monto' => $ut->monto]);
				}
				echo json_encode($resp);
			} catch (Exception $e) {
				echo json_encode([]);
			}			
		}
		
		function ServiceUtilidadMensual()
		{
			try {
				if (!isset($_POST) || !$_POST['a']) throw new Exception("Datos no encontrados", 1);
				$sucursal = $this->session->userdata('pres_ses_sucursal');

				$data_resp = [];
				for ($i=1; $i <= 12; $i++) { 
					$mes = ($i > 9) ? $i : "0".$i;
					$data = $this->AnalisisModel->Get_UtilidadBruta_Mensual(
						$_POST['a'],
						$mes,
						$sucursal
					);

					array_push($data_resp, [
						'mes' => $this->EvalMes($mes),
						'monto' => ($data->monto > 0) ? $data->monto : 0
					]);
				}

				echo json_encode($data_resp);
			} catch (Exception $e) {
				echo json_encode([]);
			}
		}

		function ServiceUtilidadResumen(){
			try {
				if(!isset($_POST) || !$_POST['inicio'] || !$_POST['final']) 
					throw new Exception('No se recibieron o detectaron los datos requeridos', 1);
				$suc = $this->session->userdata('pres_ses_sucursal');
				$cuota = $this->AnalisisModel->Get_UtilidadxCuota_Calculado($suc, $_POST['inicio'], $_POST['final']);
				$mora = $this->AnalisisModel->Get_UtilidadxMora_Calculado($suc, $_POST['inicio'], $_POST['final']);
				if ($cuota === null || $mora === null) throw new Exception('error al obtener datos', 1);
				
				echo json_encode([
					'code_resp' => 'OK',
					'mssg_public' => 'Datos Procesados.',
					'mssg_console' => '',
					'data_resp' => array(
						'cuota' => ($cuota->monto != null) ? $cuota->monto : 0,
						'mora' => ($mora->monto != null) ? $mora->monto : 0,
						'total' => 
							round(($cuota->monto + $mora->monto), 1) != null 
							? round(($cuota->monto + $mora->monto), 1) 
							: 0

					) 
				]);
			} catch (Exception $e) {
				echo json_encode([
					'code_resp' => 'ERROR',
					'mssg_public' => 'Ocurrio un error al procesar los datos.',
					'mssg_console' => $e->getMessage() 

				]);
			}	
		}		

		function ServiceGeneracionResumen(){
			try {
				if(!isset($_POST) || !$_POST['inicio'] || !$_POST['final']) 
					throw new Exception('No se recibieron o detectaron los datos requeridos');
				$suc = $this->session->userdata('pres_ses_sucursal');
				$in = $_POST['inicio'];
				$fin = $_POST['final'];
				
				$total = $this->AnalisisModel->Get_GeneracionTotal($suc, $in, $fin);
				$espera = $this->AnalisisModel->Get_GeneracionEstado($suc, $in, $fin, 'E');
				$congelado = $this->AnalisisModel->Get_GeneracionEstado($suc, $in, $fin, 'CC');
				$recupera = $this->AnalisisModel->Get_GeneracionEstado($suc, $in, $fin, 'R');
				$remate = $this->AnalisisModel->Get_GeneracionEstado($suc, $in, $fin, 'A');
				
				echo json_encode([
					'code_resp' => 'OK',
					'mssg_public' => 'Datos Procesados.',
					'mssg_console' => '',
					'data_resp' => array(
						'total' => $total->cantidad,
						'espera' => $espera->cantidad,
						'congelado' => $congelado->cantidad,
						'recuperado' => $recupera->cantidad,
						'remate' => $remate->cantidad
					) 
				]);
			} catch (Exception $e) {
				echo json_encode([
					'code_resp' => 'ERROR',
					'mssg_public' => 'Ocurrio un error al procesar los datos.',
					'mssg_console' => $e->getMessage() 

				]);
			}	
		}

		function ServiceGeneracionAnual()
		{	
			try {
				$sucursal = $this->session->userdata('pres_ses_sucursal');
				$temporadas = $this->AnalisisModel->Get_LineaTemporal($sucursal);
				$resp = [];
				foreach ($temporadas as $temp) {
					$gen = $this->AnalisisModel->Get_GeneracionAnual($sucursal, $temp->nombre);
					array_push($resp, ['nombre' => $temp->nombre, 'cantidad' => $gen->cantidad]);
				}
				echo json_encode($resp);
			} catch (Exception $e) {
				echo json_encode([]);
			}			
		}

		function ServiceGeneracionMensual()
		{
			try {
				if (!isset($_POST) || !$_POST['a']) throw new Exception("Datos no encontrados", 1);
				$sucursal = $this->session->userdata('pres_ses_sucursal');

				$data_resp = [];
				for ($i=1; $i <= 12; $i++) { 
					$mes = ($i > 9) ? $i : "0".$i;
					$genera = $this->AnalisisModel->Get_GeneracionMensual(
						$sucursal,
						$_POST['a'],
						$mes
					);

					array_push($data_resp, [
						'mes' => $this->EvalMes($mes),
						'monto' => ($genera->monto > 0) ? $genera->monto : 0
					]);
				}

				echo json_encode($data_resp);
			} catch (Exception $e) {
				echo json_encode([]);
			}
		}

		function ServiceInventarioPrendas(){
			try {
				$data_resp = [];
				$suc = $this->session->userdata('pres_ses_sucursal');
				$inventario = $this->AnalisisModel->Get_Inventario($suc);

				if (!empty($inventario)) {
					foreach ($inventario as $item) {
						$cantidad = $this->AnalisisModel->Get_PrendasxCategoria($item->id);
						$monto = $this->AnalisisModel->Get_MontoxCategoria($item->id);
						$max = $this->AnalisisModel->Get_CategoriaMontoMaximo($item->id);
						$min = $this->AnalisisModel->Get_CategoriaMontoMinimo($item->id);
						$promedio = floatval($monto->valor) / floatval($cantidad->valor);
						$promedio = round($promedio, 2);
						array_push($data_resp, array(
							'nombre' => $item->categoria,
							'cantidad' => $cantidad->valor,
							'monto' => $monto->valor,
							'maximo' => $max->valor,
							'minimo' => $min->valor,
							'promedio' => $promedio
						));
					}
				}
				echo json_encode($data_resp);
			} catch (Exception $e) {
				die($e->getMessage());
			}
		}

		function ServiceUtilidadDia(){
			try {
				$suc = $this->session->userdata('pres_ses_sucursal');
				$fecha = date("Y-m-d");
				// $fecha = "2020-04-07";

				$cuota = $this->AnalisisModel->Get_UtilidadxCuota_Dia($suc, $fecha);
				$mora = $this->AnalisisModel->Get_UtilidadxMora_Dia($suc, $fecha);
				$descuento = $this->AnalisisModel->Get_Descuento_Dia($suc, $fecha);

				$cu_monto = ($cuota->monto != null) ? $cuota->monto : 0;
				$mo_monto = ($mora->monto != null) ? $mora->monto : 0;
				$des_monto = ($descuento->monto != null) ? $descuento->monto : 0;
				$balance = (($cu_monto + $mo_monto) - $des_monto);
				$balance = round($balance, 1);
				echo json_encode([
					'code_resp' => 'OK',
					'mssg_public' => 'Datos Procesados.',
					'mssg_console' => '',
					'data_resp' => array(
						'cuota' => $cu_monto,
						'mora' => $mo_monto,
						'descuento' => $des_monto,
						'balance' => $balance,
						'balance_icon' => 
							($balance >= 0) 
							? '<span class="far fa-thumbs-up has-text-link"></span>'
							: '<span class="far thumbs-down has-text-danger"></span>' 
					) 
				]);
			} catch (Exception $e) {
				echo json_encode([
					'code_resp' => 'ERROR',
					'mssg_public' => 'Ocurrio un error al procesar los datos.',
					'mssg_console' => $e->getMessage() 

				]);
			}	
		}

		function ServGeneracionMensual(){
			try {
				if (!isset($_POST) || !$_POST['a']) throw new Exception("Datos no encontrados", 1);
				$sucursal = $this->session->userdata('pres_ses_sucursal');

				$data_resp = [];
				for ($i=1; $i <= 12; $i++) { 
					$mes = ($i > 9) ? $i : "0".$i;
					$data = $this->AnalisisModel->Get_GeneracionCreditosMensual(
						$_POST['a'],
						$mes,
						$sucursal
					);

					array_push($data_resp, [
						'mes' => $this->EvalMes($mes),
						'monto' => ($data->monto > 0) ? $data->monto : 0,
						'cantidad' => $data->cantidad
					]);
				}

				echo json_encode($data_resp);
			} catch (Exception $e) {
				die($e->getMessage());
			}
		}		

		function gallery(){
			$this->load->view('_ndc/gallery.php');
		}


		// Prototipo
		function prot__service_linea_temporal(){
			$suc = $this->session->userdata('pres_ses_sucursal');
			echo json_encode($this->AnalisisModel->Get_LineaTemporal($suc));
		}

		function tst_developer($yr){
			$suc = $this->session->userdata('pres_ses_sucursal');
			$pagos_res = $this->AnalisisModel->prot__get_pagos_anual($yr, $suc);
			echo json_encode($pagos_res);
		}

		function prot__generacion_general(){
			$suc = $this->session->userdata('pres_ses_sucursal');
			$file = file_get_contents("php://input");
			$body = json_decode($file);
			$result = [
				'registrado_cantidad' => 0,
				'registrado_monto' => 0,
				'recuperado_cantidad' => 0,
				'recuperado_monto' => 0,
				'diferencia_cantidad' => 0,
				'diferencia_monto' => 0,
				'global_cantidad' => 0,
				'global_monto' => 0
			];

			if (isset($body->filtro)) {
				$regdata = [];
				$recdata = [];
				switch ($body->filtro) {
					case 'year':
						if(!$body->year) die();
						$regdata = $this->AnalisisModel->prot__registros_anual($body->year, $suc);
						$recdata = $this->AnalisisModel->prot__recuperacion_anual($body->year, $suc);
						break;
					
					case 'month':
						if(!$body->year || !$body->month) die();
						$regdata = $this->AnalisisModel->prot__registros_mensual($body->year, $body->month, $suc);
						$recdata = $this->AnalisisModel->prot__recuperacion_mensual($body->year, $body->month, $suc);
						break;

					case 'day':
						$regdata = $this->AnalisisModel->prot__registros_fecha(date("Y-m-d"), $suc);
						$recdata = $this->AnalisisModel->prot__recuperacion_fecha(date("Y-m-d"), $suc);
						break;

					case 'date':
						$regdata = $this->AnalisisModel->prot__registros_fecha($body->date, $suc);
						$recdata = $this->AnalisisModel->prot__recuperacion_fecha($body->date, $suc);
						break;

					case 'date_range':
						$regdata = $this->AnalisisModel->prot__registros_rango($body->date_inicio, $body->date_final, $suc);
						$recdata = $this->AnalisisModel->prot__recuperacion_rango($body->date_inicio, $body->date_final, $suc);
						break;
				}

				$result['recuperado_cantidad'] = round($recdata->cantidad, 2);
				$result['recuperado_monto'] = round($recdata->monto, 2);
				$result['registrado_cantidad'] = round($regdata->cantidad, 2);
				$result['registrado_monto'] = round($regdata->monto, 2);
				$result['diferencia_cantidad'] = $result['registrado_cantidad'] - $result['recuperado_cantidad'];
				$result['diferencia_monto'] = round(($result['registrado_monto']-$result['recuperado_monto']), 2);				
			}

			echo json_encode($result);
		}

		function prot__generacion_global(){
			$suc = $this->session->userdata('pres_ses_sucursal');
			$result = [];
			$glob = $this->AnalisisModel->prot__registros_global($suc);
			$result['global_cantidad'] = $glob ? round($glob->cantidad, 2) : 0;
			$result['global_monto'] = $glob ? round($glob->monto, 2) : 0;
			$result['global_inicio'] = $this->AnalisisModel->prot__credito_olddate($suc)->fecha;
			$result['global_fin'] = date("d/m/Y");

			echo json_encode($result);
		}

		function prot__service_utilidad_procesada(){
			$suc = $this->session->userdata('pres_ses_sucursal');
			$file = file_get_contents("php://input");
			$body = json_decode($file); //an: a���o - ms: mes 
			$result = [];
			if(isset($body->filtro)){
				$pagos_res = [];
				switch ($body->filtro) {
					case 'year':
						if(!$body->year) die();
						$pagos_res = $this->AnalisisModel->prot__get_pagos_anual($body->year, $suc);
						break;
					
					case 'month':
						if(!$body->year || !$body->month) die();
						$pagos_res = $this->AnalisisModel->prot__get_pagos_mensual($body->year, $body->month, $suc);
						break;

					case 'day':
						$pagos_res = $this->AnalisisModel->prot__get_pagos_fecha(date("Y-m-d"), $suc);
						break;

					case 'date':
						$pagos_res = $this->AnalisisModel->prot__get_pagos_fecha($body->date, $suc);
						break;

					case 'date_range':
						$pagos_res = $this->AnalisisModel->prot__get_pagos_rango($body->date_inicio, $body->date_final, $suc);
						break;
				}
				$res = array(
					'cantidad_pagos' => 0,
					'cantidad_total' => 0,
					'cantidad_cuota' => 0,
					'cantidad_mora' => 0,
					'cantidad_capital' => 0,
					'suma_cuota' => 0,
					'suma_mora' => 0,
					'suma_capital' => 0,
					'suma_utilidad' => 0,
					'suma_descuento' => 0,
					'utilidad_neta' => 0,
					'class' => 'secondary',
					'icon' => 'minus'
				);

				if(count($pagos_res) > 0){				
					foreach ($pagos_res as $pago) {
						$res['cantidad_pagos']++;
						$res['suma_descuento'] = round(($res['suma_descuento'] + $pago->descuento),2);
						$detalle_res = $this->AnalisisModel->prot__get_pago_detalle($pago->id);

						if (!empty($detalle_res)) {
							foreach($detalle_res as $detalle) {
								if ($detalle->tipo === 'CU') {
									$res['cantidad_cuota']++;
									$res['suma_cuota'] = round(($res['suma_cuota']+$detalle->monto), 2); 
								}
								if ($detalle->tipo === 'MO') {
									$res['cantidad_mora']++;
									$res['suma_mora'] = round(($res['suma_mora'] + $detalle->monto), 2);
								}
								if ($detalle->tipo === 'CAP') {
									$res['cantidad_capital']++;
									$res['suma_capital'] = round(($res['suma_capital'] + $detalle->monto), 2);
								}
							}
						}
					}
					$res['suma_utilidad'] = round(($res['suma_cuota'] + $res['suma_mora']), 2);
					$res['utilidad_neta'] = round(($res['suma_utilidad'] - $res['suma_descuento']), 2);
					// $res['class'] = ($res['utilidad_neta'] > 0) ? 'primary' : 'danger';
					// $res['icon'] = ($res['utilidad_neta'] > 0) ? 'up' : 'down';
				}

				$result = $res;
			}
			echo json_encode($result);
		}

		function prot__service_creditos_resumen(){
			$suc = $this->session->userdata('pres_ses_sucursal');
			echo json_encode(array(
				'total' => $this->AnalisisModel->prot__get_credito_cont_total($suc)->cantidad,
				'activo' => $this->AnalisisModel->prot__get_credito_cont_estado('E', $suc)->cantidad,
				'remate' => $this->AnalisisModel->prot__get_credito_cont_estado('A', $suc)->cantidad,
				'recuperado' => $this->AnalisisModel->prot__get_credito_cont_estado('R', $suc)->cantidad,
				'congelado' => $this->AnalisisModel->prot__get_credito_cont_estado('CC', $suc)->cantidad
			));
		}

		function prot__nameday($par){
			date_default_timezone_set('America/Lima');
			$days = array(
				"Mon" => 1,
				"Tue" => 2,
				"Wed" => 3,
				"Thu" => 4,
				"Fri" => 5,
				"Sat" => 6,
				"Sun" => 7
			);

			return $days[$par];
		}

		function prot__namemonth($par){
			date_default_timezone_set('America/Lima');
			$days = array(
				1 => "Enero",
				2 => "Febrero",
				3 => "Marzo",
				4 => "Abril",
				5 => "Mayo",
				6 => "Junio",
				7 => "Julio",
				8 => "Agosto",
				9 => "Septiembre",
				10 => "Octubre",
				11 => "Noviembre",
				12 => "Diciembre"
			);

			return $days[$par];
		}

		function prot__service_utilidad_semanal(){
			$file = file_get_contents("php://input");
			$body = json_decode($file);
			$data = [];
			if (isset($body->year) && isset($body->month)) {
				$cant_dias = cal_days_in_month(CAL_GREGORIAN, $body->month, $body->year);
				$cant_sem = (($cant_dias % 7) > 0) ? ($cant_dias/7)+1 : $cant_dias/7;
				$in_sem = 1;
				for ($i=1; $i <= $cant_sem ; $i++) {
					$nom_dia = date('D', strtotime("{$body->year}-{$body->month}-{$in_sem}"));
					$nro_dia = $this->prot__nameday($nom_dia);

					$drest = ((7 - $nro_dia) > 0) ? 7 - $nro_dia : 7; 
					$in = $in_sem;
					$fn = ($i == 5) ? $cant_dias : $in + $drest;
					$in_sem = ($fn === $cant_dias) ? $cant_dias : $fn+1;

					$inx = ($in > 9) ? $in : '0'.$in;
					$fnx = ($fn > 9) ? $fn : '0'.$fn;
					$subdata = array(
						'pagos' => 0,
						'semana' => $i,
						'inicio' => "{$inx}/{$body->month}/{$body->year}",
						'fin' => "{$fnx}/{$body->month}/{$body->year}",
						'cuota_cantidad' => 0,
						'cuota_monto' => 0,
						'mora_cantidad' => 0,
						'mora_monto' => 0,
						'descuento' => 0,
						'utilidad_suma' => 0,
						'utilidad_neta' => 0,
						'class' => 'secondary',
						'icon' => 'activity'
					);
					$data_pagos = $this->AnalisisModel->prot__get_pagos_rango(
						"{$body->year}-{$body->month}-{$in}", 
						"{$body->year}-{$body->month}-{$fn}", 
						$this->session->userdata('pres_ses_sucursal')
					);
					if ($data_pagos) {
						$subdata['pagos'] = count($data_pagos);
						foreach ($data_pagos as $pago) {
							$subdata['descuento'] = round(($subdata['descuento'] + $pago->descuento), 2);
							$detalle_res = $this->AnalisisModel->prot__get_pago_detalle($pago->id);

							if (!empty($detalle_res)) {
								foreach($detalle_res as $detalle) {
									if ($detalle->tipo === 'CU') {
										$subdata['cuota_cantidad']++;
										$subdata['cuota_monto'] = round(($subdata['cuota_monto']+$detalle->monto), 2); 
									}
									if ($detalle->tipo === 'MO') {
										$subdata['mora_cantidad']++;
										$subdata['mora_monto'] = round(($subdata['mora_monto'] + $detalle->monto), 2);
									}
								}
							}
						}
						$subdata['utilidad_suma'] = round(($subdata['cuota_monto'] + $subdata['mora_monto']), 2);
						$subdata['utilidad_neta'] = round(($subdata['utilidad_suma'] - $subdata['descuento']), 2); 
						$subdata['class'] = ($subdata['utilidad_neta'] > 0) ? 'primary' : 'danger';
						$subdata['icon'] = ($subdata['utilidad_neta'] > 0) ? 'arrow-up' : 'arrow-down'; 

						if($subdata['utilidad_neta'] === 0) $subdata['class'] = 'secondary';
						if($subdata['utilidad_neta'] === 0) $subdata['icon'] = 'activity';
					}
					array_push($data, $subdata);
				}
			}
			echo json_encode($data);
		}

		function prot__service_utilidad_mensual(){
			$file = file_get_contents("php://input");
			$body = json_decode($file);
			$data = [];
			if (isset($body->year)) {
				for ($i=1; $i <= 12 ; $i++){
					$subdata = array(
						'pagos_cantidad' => 0,
						'cuota_cantidad' => 0,
						'mes' => $this->prot__namemonth($i),
						'cuota_cantidad' => 0,
						'cuota_monto' => 0,
						'mora_cantidad' => 0,
						'mora_monto' => 0,
						'descuento' => 0,
						'utilidad_suma' => 0,
						'utilidad_neta' => 0,
						'class' => 'secondary',
						'icon' => 'activity'
					);

					$ms = ($i > 9) ? $i : '0'+$i;
					$data_pagos = $this->AnalisisModel->prot__get_pagos_mensual(
						$body->year,
						$ms,
						$this->session->userdata('pres_ses_sucursal')
					);
					if (!empty($data_pagos)) {
						$subdata['pagos'] = count($data_pagos);
						foreach ($data_pagos as $pago) {
							$subdata['descuento'] = round(($subdata['descuento'] + $pago->descuento), 2);
							$detalle_res = $this->AnalisisModel->prot__get_pago_detalle($pago->id);

							if (!empty($detalle_res)) {
								foreach($detalle_res as $detalle) {
									if ($detalle->tipo === 'CU') {
										$subdata['cuota_cantidad']++;
										$subdata['cuota_monto'] = round(($subdata['cuota_monto']+$detalle->monto), 2); 
									}
									if ($detalle->tipo === 'MO') {
										$subdata['mora_cantidad']++;
										$subdata['mora_monto'] = round(($subdata['mora_monto'] + $detalle->monto), 2);
									}
								}
							}
						}
						$subdata['utilidad_suma'] = round(($subdata['cuota_monto'] + $subdata['mora_monto']), 2);
						$subdata['utilidad_neta'] = round(($subdata['utilidad_suma'] - $subdata['descuento']), 2); 
						$subdata['class'] = ($subdata['utilidad_neta'] > 0) ? 'primary' : 'danger';
						$subdata['icon'] = ($subdata['utilidad_neta'] > 0) ? 'arrow-up' : 'arrow-down'; 

						if($subdata['utilidad_neta'] === 0) $subdata['class'] = 'secondary';
						if($subdata['utilidad_neta'] === 0) $subdata['icon'] = 'activity';
					}

					array_push($data, $subdata);
				}
			}
			echo json_encode($data);
		}

		function prot__prueba_semanal($date){
			// 01 | 02 | 03 | 04 | 05 | 06 | 07
			$arr = explode("_", $date);
			$m = $arr[0];
			$a = $arr[1];
			$dias = cal_days_in_month(CAL_GREGORIAN, $m, $a);
			echo "Dias: {$dias}";
			echo "<table>";
			echo "<thead><tr>";
			echo "<th>nombre_dia</th>";
			echo "<th>numero_dia</th>";
			echo "<th>dias_restantes</th>";
			echo "<th>dia_inicial</th>";
			echo "<th>dia_final</th>";
			echo "</tr></thead>";
			$semanas = (($dias % 7) > 0) ? ($dias/7)+1 : $dias/7;
			$inicio_sem = 1;
			for ($i=1; $i <= $semanas; $i++) {
				$nom_dia = date('D', strtotime("{$inicio_sem}-{$m}-{$a}"));
				$nro_dia = $this->prot__nameday($nom_dia);

				$d_rest = ((7 - $nro_dia) > 0) ? 7 - $nro_dia : 7;
				$d_inicio = $inicio_sem;
				$d_fin = ($i == 5) ? $dias : $d_inicio + $d_rest;
				$inicio_sem = ($d_fin === $dias) ? $dias : $d_fin+1;
				
				echo "<tr>";
				echo "<td>{$nom_dia}</td>";
				echo "<td>{$nro_dia}</td>";
				echo "<td>{$d_rest}</td>";
				echo "<td>{$d_inicio}</td>";
				echo "<td>{$d_fin}</td>";
				echo "</tr>";
			}
			echo "</table>";
		}

		

	}
	
?>
