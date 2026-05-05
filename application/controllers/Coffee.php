<?php  
	/**
	 * 
	 */
	class Coffee extends CI_Controller
	{
		
		function __construct()
		{
			parent::__construct();
			$this->load->model('TarifaModel');
		}

		function prestamo_calculo(){
			$resp = [];
			$sucursal = $this->session->userdata('pres_ses_sucursal');
			if (isset($_POST['monto']) && $_POST['monto'] > 0) {
				$tarifa = $this->TarifaModel->Buscar_tarifa_aplicable($_POST['monto'], $sucursal);
				if (!empty($tarifa)) {
					try {
						$id = $tarifa->id;
						$pago = 0;
						$mora = 0;
						if ($tarifa->tipo === 'P') {
							$porcentaje = $tarifa->interes + $_POST['cargo'];
							$pago = floatval($_POST['monto'] * $porcentaje)/100;
							$mora = floatval($_POST['monto'] * $tarifa->mora)/100;
							$tipo = 'Porcentual';
							$tasa = $porcentaje;
						}elseif ($tarifa->tipo === 'F') {
							$pago = $tarifa->interes;
							$mora = $tarifa->mora;
							$tipo = 'Fijo';
							$tasa = '---';
						}
						$mora = ($mora > 3) ? 3 : $mora;
						$resp = array(
							'status' => 'OK',
							'message' => 'Calculo aplicado',
							'result' => array(
								'pago' => round($pago, 1), 
								'mora' => round($mora, 1), 
								'tasa_tipo' => $tipo, 
								'tasa' => $tasa, 
								'tarifa' => $id
							)
						);
					} catch (Exception $e) {
						$resp = array(
							'status' => 'ERROR',
							'message' => 'Error al procesar',
							'result' => []
						);
					}
				}else{
					$resp = array(
						'status' => 'WARN',
						'message' => 'No se encontro tarifa aplicable',
						'result' => []
					);
				}
			}else{
				$resp = array(
					'status' => 'WARN',
					'message' => 'monto no valido',
					'result' => []
				);
			}
			echo json_encode($resp);
		}

	}
?>