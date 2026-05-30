<?php  
	
	class App extends CI_Controller
	{
		
		function __construct(){
			parent::__construct();
			if (!$this->session->userdata('pres_ses_sesion')) {
				redirect(base_url().'Acceso','refresh');
			}
			$this->load->model('AppModel');
			$this->load->model('CreditoModel');
			$this->load->model('CuotaModel');
			$this->load->model('AdelantoModel');
			$this->load->model('SucursalModel');
			$this->load->model('PagoModel');
			$this->load->model('MoraModel');
			$this->load->model('DebitacionModel');
			$this->load->model('ProrrogaModel');
			$this->load->model('ConfiguracionModel');
		}
		// VISTAS
		function revision(){
			$this->load->view("System/revision_diaria.php");
		}
		function configuracion(){
			if ($this->session->userdata('pres_ses_rol') === 'Administrador') {
				$data['config'] = $this->ConfiguracionModel->consulta('actual');
				$this->load->view("Layout/header.php");
				$this->load->view("App/configuracion.php", $data);
				$this->load->view("Layout/footer.php");
			}else{
				$data['titulo'] = 'Acceso Denegado ';
				$data['mensaje'] = 'El usuario no tiene los permisos suficientes.';
				$this->load->view("Layout/header.php");
				$this->load->view("Error/no_found.php", $data);
				$this->load->view("Layout/footer.php");
			}			
		}

		// FUNCIONES
		function revision_completa(){
			ini_set('memory_limit', '256M');
			set_time_limit(300);
			$takes_off = [];
			try {
				$this->db->trans_begin();
				$resp = [];
				$cont_generado = 0;
				$cont_calculado = 0;
				$creditos = $this->AppModel->creditos_activos(); 
				foreach ($creditos as $cd) {
					$ciclo = intval($cd->ciclo_pago);
					// array_push($takes_off, ['id' => $cd->id, 'ciclo' => $ciclo, 'type' => gettype($ciclo)]);
					$genera_progreso = "PEN";
					$total_generado = 0;
					while ($genera_progreso === "PEN") {
						$contador = 0;
						$cuotas_proceso = $this->AppModel->cuotas_recientes($cd->id, 5);
						foreach ($cuotas_proceso as $c) {
							$prox_vencimiento = date(
								"Y-m-d", 
								strtotime($c->vencimiento. " + $ciclo days")
							);
							$prox_verifica = $this->CuotaModel->Buscar_libre('cuo_codigo as id', array(
								'cuo_vencimiento' => $prox_vencimiento,
								'cuo_credito' => $c->credito
							));
							if ($prox_verifica == null) {
								$inicio_mora = date("Y-m-d", strtotime($prox_vencimiento. " + $c->prorroga days"));
								$cuota_nuevo_orden = $c->orden + 1;
								$this->CuotaModel->insertar(array(
									'cuo_credito' => $c->credito,
									'cuo_orden' => $cuota_nuevo_orden,
									'cuo_monto' => $c->monto,
									'cuo_penalidad' => $c->penalidad,
									'cuo_vencimiento' => $prox_vencimiento,
									'cuo_inicio_mora' => $inicio_mora,
									'cuo_cobra_mora' => 'A',
									'cuo_estado' => 'E'
								));
								$id_cuota = $this->db->insert_id();
								$adelanto = $this->CreditoModel->Buscar_libre('cred_adelanto as monto', array(
									'cred_codigo' => $c->credito
								));
								if ($adelanto != null && $adelanto->monto > 0) {
									$suc = $this->session->userdata('pres_ses_sucursal');
									$sucursal_data = $this->SucursalModel->Buscar_libre(
										'suc_pago_serie as serie', array('suc_codigo'=>$suc)
									);
									if ($sucursal_data == null) die("Sucursal no definida");

									$orden = $this->PagoModel->Buscar_orden_actual($sucursal_data->serie, $suc);
									$n_orden = $orden->orden + 1;
									$p_serie = $sucursal_data->serie.str_pad($n_orden, 7,"0", STR_PAD_LEFT);
									$pdata = array(
										'pag_credito' => $c->credito,
										'pag_fecha' => date("Y-m-d H:i:s"),
										'pag_forma_pago' => 'REV',
										'pag_subtotal' => $c->monto,
										'pag_descuento' => 0,
										'pag_total' => $c->monto,
										'pag_recibido' => $c->monto,
										'pag_devuelto' => 0,
										'pag_empleado' => $this->session->userdata('pres_ses_empleado'),
										'pag_concepto' => 'ADEL_AUTO',
										'pag_serie' => $p_serie,
										'pag_orden' => $n_orden,
										'pag_nomen' => $sucursal_data->serie
									);
									$this->PagoModel->Insertar($pdata);
									$id_pago = $this->db->insert_id();
									$this->PagoModel->Insertar_detalle(
										array(
											'pdet_pago' => $id_pago,
											'pdet_referencia' => 'CUOTA '.$cuota_nuevo_orden.' - ADELANTO AUTOMATICO',
											'pdet_tipo' => 'CU',
											'pdet_id_ref' => $id_cuota,
											'pdet_monto' => $c->monto
										)
									);

									$this->AdelantoModel->Insertar_salida(array(
										'sal_credito' => $c->credito,
										'sal_tipo' => 'ADEL_AUTO',
										'sal_modo' => 'AUTO',
										'sal_detalle' => 'PAGO AUTOMATICO POR ADELANTO : CUOTA '.$cuota_nuevo_orden,
										'sal_fecha' => date("Y-m-d")
									));

									$this->CreditoModel->Modificar_adelanto($c->credito, $c->monto, 'RESTA');
									$this->CuotaModel->Modificar($id_cuota, array(
										'cuo_estado' => 'P'
									));								
								}
								// Fin --Creacion de pago y salida_adelanto
								$contador++;
							}
							// Fin creacion de cuota
						}
						$total_generado = $total_generado + $contador;
						if ($contador == 0) {
							$genera_progreso = 'OK';
						}
					}
					$cont_generado = $cont_generado + $total_generado;
					
					
					$calculo = $this->revision_cuota_calculo_v2($cd->id, $cd->ciclo_pago);
					if ($calculo == 'ERROR') {
						die("Error al procesar el calculo");
					}
				}

				$resp = array(
					'ok' => 'proceso completado',
					'generacion' => $cont_generado,
					'status' => 'OK',
					'message' => 'Revision Completa',
					'cuota_generacion' => 'Cuotas generadas: '.$cont_generado,
					'cuota_calculo' => 'Calculo de cuotas aplicado'
				);
				$this->db->trans_commit();
				echo json_encode($resp);
				// echo json_encode($takes_off);
			} catch (PDOException $e) {
				$this->db->trans_rollback();
				die(json_encode(array(
					'status' => 'ERROR',
					'message' => 'error al procesar',
					'log' => $e->getMessage()
				)));
			}
		}

		function revision_unitaria(){
			ini_set('memory_limit', '256M');
			set_time_limit(300);
			try {
				$this->db->trans_begin();
				$resp = [];
				$cuota_generacion_log = '';
				$cuota_calculo_log = '';

				$genera_progreso = "PEN";
				$total_generado = 0;
				while ($genera_progreso === "PEN") {
					$contador = 0;
					$data_credito = $this->AppModel->getSearchCredito($_POST['id']);
					$ciclo = intval($data_credito->ciclo_pago);
					$cuotas_proceso = $this->AppModel->cuotas_recientes($_POST['id'], 10);
					foreach ($cuotas_proceso as $c) {
						$prox_vencimiento = date("Y-m-d", strtotime($c->vencimiento. " + $ciclo days"));
						$prox_verifica = $this->CuotaModel->Buscar_libre('cuo_codigo as id', array(
							'cuo_vencimiento' => $prox_vencimiento,
							'cuo_credito' => $c->credito
						));
						if ($prox_verifica == null) {
							$inicio_mora = date("Y-m-d", strtotime($prox_vencimiento. " + $c->prorroga days"));
							$cuota_nuevo_orden = $c->orden + 1;
							$this->CuotaModel->insertar(array(
								'cuo_credito' => $c->credito,
								'cuo_orden' => $cuota_nuevo_orden,
								'cuo_monto' => $c->monto,
								'cuo_penalidad' => $c->penalidad,
								'cuo_vencimiento' => $prox_vencimiento,
								'cuo_inicio_mora' => $inicio_mora,
								'cuo_cobra_mora' => 'A',
								'cuo_estado' => 'E'
							));
							$id_cuota = $this->db->insert_id();
							$adelanto = $this->CreditoModel->Buscar_libre('cred_adelanto as monto', array(
								'cred_codigo' => $c->credito
							));
							if ($adelanto != null && $adelanto->monto >= $c->monto) {
								$suc = $this->session->userdata('pres_ses_sucursal');
								$sucursal_data = $this->SucursalModel->Buscar_libre(
									'suc_pago_serie as serie', array('suc_codigo'=>$suc)
								);
								if ($sucursal_data == null) die("Sucursal no definida");

								$orden = $this->PagoModel->Buscar_orden_actual($sucursal_data->serie, $suc);
								$n_orden = $orden->orden + 1;
								$p_serie = $sucursal_data->serie.str_pad($n_orden, 7,"0", STR_PAD_LEFT);
								$pdata = array(
									'pag_credito' => $c->credito,
									'pag_fecha' => date("Y-m-d H:i:s"),
									'pag_forma_pago' => 'REV',
									'pag_subtotal' => $c->monto,
									'pag_descuento' => 0,
									'pag_total' => $c->monto,
									'pag_recibido' => $c->monto,
									'pag_devuelto' => 0,
									'pag_empleado' => $this->session->userdata('pres_ses_empleado'),
									'pag_concepto' => 'ADEL_AUTO',
									'pag_serie' => $p_serie,
									'pag_orden' => $n_orden,
									'pag_nomen' => $sucursal_data->serie
								);
								$this->PagoModel->Insertar($pdata);
								$id_pago = $this->db->insert_id();
								$this->PagoModel->Insertar_detalle(
									array(
										'pdet_pago' => $id_pago,
										'pdet_referencia' => 'CUOTA '.$cuota_nuevo_orden.' - ADELANTO AUTOMATICO',
										'pdet_tipo' => 'CU',
										'pdet_id_ref' => $id_cuota,
										'pdet_monto' => $c->monto
									)
								);

								$this->AdelantoModel->Insertar_salida(array(
									'sal_credito' => $c->credito,
									'sal_tipo' => 'ADEL_AUTO',
									'sal_modo' => 'AUTO',
									'sal_detalle' => 'PAGO AUTOMATICO POR ADELANTO : Cuota '.$cuota_nuevo_orden,
									'sal_fecha' => date("Y-m-d")
								));

								$this->CreditoModel->Modificar_adelanto($c->credito, $c->monto, 'RESTA');
								$this->CuotaModel->Modificar($id_cuota, array(
									'cuo_estado' => 'P'
								));								
							}
							// Fin --Creacion de pago y salida_adelanto
							$contador++;
						}
						// Fin creacion de cuota
					}
					$total_generado = $total_generado + $contador;
					if ($contador == 0) {
						$genera_progreso = 'OK';
					}
				}
				$cuota_generacion_log = 'Cuotas generadas: '.$total_generado;

				$calculo = $this->revision_cuota_calculo_v2($_POST['id'], $data_credito->ciclo_pago);
				if ($calculo == 'ERROR') {
					die("Error al procesar el calculo");
				}
				$resp = array(
					'status' => 'OK',
					'message' => 'Revision Completa',
					'cuota_generacion' => $cuota_generacion_log,
					'cuota_calculo' => $cuota_calculo_log
				);

				$this->db->trans_commit();
				echo json_encode($resp);
			} catch (PDOException $e) {
				$this->db->trans_rollback();
				die(json_encode(array(
					'status' => 'ERROR',
					'message' => 'error al procesar'
				)));
			}
		}

		function revision_cuota_calculo($id){
			try {
				$this->db->trans_begin();
				$fecha_actual = date("Y-m-d");
				$activo = $this->AppModel->cuotas_activas($id);
				$procedado = 0;
				if (!empty($activo)) {
					foreach ($activo as $act) {
						if ($act->vencimiento >= $fecha_actual) {
							$restante = date_diff(date_create($act->vencimiento), date_create($fecha_actual))->format('%a');
							$preced = 7 - $restante;
							if ($preced < 7) {
								$prorrateo = $act->monto * $preced * 0.15;
							}else{
								$prorrateo = $act->monto;
							}
							$this->CuotaModel->Modificar(
								$act->id,
								array(
									'cuo_prorrateo' => $prorrateo,
									'cuo_dias_atraso' => 0,
									'cuo_mora' => 0,
									'cuo_estado' => 'E'
								)
							);
						}else{
							if ($act->inicio_mora <= $fecha_actual && $act->cobra_mora == 'A' && $act->aplica_penalidad == 'A') {
								// $prorroga_activa = $this->CuotaModel->Buscar_prorroga_aplicada($act->credito, $act->inicio_mora);
								$amortizacion = $this->CreditoModel->Buscar_libre('cred_amortizacion as monto', array(
									'cred_codigo' => $act->credito
								));

								if ($amortizacion != null && $amortizacion->monto >= $act->monto) {
									$suc = $this->session->userdata('pres_ses_sucursal');
									$sucursal_data = $this->SucursalModel->Buscar_libre(
										'suc_pago_serie as serie', array('suc_codigo'=>$suc)
									);
									if ($sucursal_data == null) die("Sucursal no definida");

									$orden = $this->PagoModel->Buscar_orden_actual($sucursal_data->serie, $suc);
									$n_orden = $orden->orden + 1;
									$p_serie = $sucursal_data->serie.str_pad($n_orden, 7,"0", STR_PAD_LEFT);
									$pdata = array(
										'pag_credito' => $act->credito,
										'pag_fecha' => date("Y-m-d H:i:s"),
										'pag_forma_pago' => 'REV',
										'pag_subtotal' => $act->monto,
										'pag_descuento' => 0,
										'pag_total' => $act->monto,
										'pag_recibido' => $act->monto,
										'pag_devuelto' => 0,
										'pag_empleado' => $this->session->userdata('pres_ses_empleado'),
										'pag_concepto' => 'DEB_AUTO',
										'pag_serie' => $p_serie,
										'pag_orden' => $n_orden,
										'pag_nomen' => $sucursal_data->serie
									);
									$this->PagoModel->Insertar($pdata);
									$id_pago = $this->db->insert_id();
									$this->PagoModel->Insertar_detalle(
										array(
											'pdet_pago' => $id_pago,
											'pdet_referencia' => 'CUOTA '.$act->orden.' - DEBITACION AUTOMATICA',
											'pdet_tipo' => 'CU',
											'pdet_id_ref' => $act->id,
											'pdet_monto' => $act->monto
										)
									);

									$this->DebitacionModel->Insertar(array(
										'deb_credito' => $act->credito,
										'deb_pago' => $id_pago,
										'deb_id_ref' => $act->id,
										'deb_tipo' => 'DEB_AUTO',
										'deb_detalle' => "DEBITACION AUTOMATICA - Cuota ".$act->orden,
										'deb_monto' => $act->monto,
										'deb_fecha' => date("Y-m-d")
									));

									$this->CuotaModel->Modificar($act->id, array(
										'cuo_estado' => 'P',
										'cuo_dias_atraso' => 0,
										'cuo_prorrateo' => 0
									));	
									$this->CreditoModel->code_free(
										"UPDATE credito SET cred_amortizacion = cred_amortizacion - $act->monto 
										WHERE cred_codigo = ".$act->credito
									);
									if ($this->db->affected_rows() == 0) {
										die("Error al aplicar cambios");
									}
								}else{
									$proc_dias_atraso = date_diff(date_create($fecha_actual), date_create($act->inicio_mora))->format('%a');
									$proc_dias_atraso = $proc_dias_atraso + 1;
									$proc_mora = $proc_dias_atraso * $act->penalidad;
									$this->CuotaModel->Modificar(
										$act->id,
										array(
											'cuo_dias_atraso' => $proc_dias_atraso,
											'cuo_mora' => $proc_mora,
											'cuo_estado' => 'V'
										)
									);
									$mora_activa = $this->MoraModel->Buscar_libre('mor_codigo as id', array(
										'mor_credito' => $act->credito,
										'mor_estado' => 'PEN'
									)); 
									if ($mora_activa != null && $mora_activa->id > 0) {
										$this->MoraModel->Modificar_libre(
											array(
												'mor_cuota' => $act->id,
												'mor_dias' => $proc_dias_atraso,
												'mor_monto' => $proc_mora,
												'mor_estado_previo' => date("Y-m-d") 
											), 
											$proc_dias_atraso." > mor_dias AND mor_codigo = ".$mora_activa->id
										);
									}else{
										$this->MoraModel->Insertar(array(
											'mor_credito' => $act->credito,
											'mor_cuota' => $act->id,
											'mor_dias' => $proc_dias_atraso,
											'mor_monto' => $proc_mora,
											'mor_estado_previo' => 'PEN',
											'mor_actualizacion' => date("Y-m-d"),
											'mor_estado' => 'PEN'
										));
									}
								}
							}else{
								$this->CuotaModel->Modificar(
									$act->id,
									array(
										'cuo_dias_atraso' => 0,
										'cuo_mora' => 0,
										'cuo_estado' => 'V'
									)
								);
							}
						}
					}
				}
				$this->db->trans_commit();
				return "OK";
			} catch (Exception $e) {
				$this->db->trans_rollback();
				return 'ERROR';
			}
		}

		function revision_cuota_calculo_v2($id, $ciclo){
			try {
				$this->db->trans_begin();
				$fecha_actual = date("Y-m-d");
				$activo = $this->AppModel->cuotas_activas($id);
				$procedado = 0;
				if (!empty($activo)) {
					foreach ($activo as $act) {
						$deb_app = 'NO';
		
						// Verificacion - aplicacion de debitacion
						if ($act->vencimiento <= $fecha_actual) {
							$amortizacion = $this->CreditoModel->Buscar_libre('cred_amortizacion as monto', array(
								'cred_codigo' => $act->credito
							));
							if ($amortizacion != null && $amortizacion->monto >= $act->monto) {
								$suc = $this->session->userdata('pres_ses_sucursal');
								$sucursal_data = $this->SucursalModel->Buscar_libre(
									'suc_pago_serie as serie', array('suc_codigo'=>$suc)
								);
								if ($sucursal_data == null) die("Sucursal no definida");
								$orden = $this->PagoModel->Buscar_orden_actual($sucursal_data->serie, $suc);
								$n_orden = $orden->orden + 1;
								$p_serie = $sucursal_data->serie.str_pad($n_orden, 7,"0", STR_PAD_LEFT);
								$pdata = array(
									'pag_credito' => $act->credito,
									'pag_fecha' => date("Y-m-d H:i:s"),
									'pag_forma_pago' => 'REV',
									'pag_subtotal' => $act->monto,
									'pag_descuento' => 0,
									'pag_total' => $act->monto,
									'pag_recibido' => $act->monto,
									'pag_devuelto' => 0,
									'pag_empleado' => $this->session->userdata('pres_ses_empleado'),
									'pag_concepto' => 'DEB_AUTO',
									'pag_serie' => $p_serie,
									'pag_orden' => $n_orden,
									'pag_nomen' => $sucursal_data->serie
								);
								$this->PagoModel->Insertar($pdata);
								$id_pago = $this->db->insert_id();
								$this->PagoModel->Insertar_detalle(
									array(
										'pdet_pago' => $id_pago,
										'pdet_referencia' => 'CUOTA '.$act->orden.' - DEBITACION',
										'pdet_tipo' => 'CU',
										'pdet_id_ref' => $act->id,
										'pdet_monto' => $act->monto
									)
								);
								$this->DebitacionModel->Insertar(array(
									'deb_credito' => $act->credito,
									'deb_pago' => $id_pago,
									'deb_id_ref' => $act->id,
									'deb_tipo' => 'DEB_AUTO',
									'deb_detalle' => "DEBITACION AUTOMATICA - Cuota ".$act->orden,
									'deb_monto' => $act->monto,
									'deb_fecha' => date("Y-m-d")
								));
								$this->CuotaModel->Modificar($act->id, array(
									'cuo_estado' => 'P',
									'cuo_dias_atraso' => 0,
									'cuo_prorrateo' => 0
								));	
								$this->CreditoModel->code_free(
									"UPDATE credito SET cred_amortizacion = cred_amortizacion - $act->monto 
									WHERE cred_codigo = ".$act->credito
								);
								if ($this->db->affected_rows() == 0) {
									die("Error al aplicar cambios");
								}
								$deb_app = 'SI';
							}
						}
		
						if ($deb_app === 'NO') {
							if ($act->vencimiento >= $fecha_actual) {
								$restante = date_diff(date_create($act->vencimiento), date_create($fecha_actual))->format('%a');
								
								$prorrateo = 0;
								$log = "";
								if ($ciclo == '30') {
									// cuota ÷ 30 x días transcurridos desde su último vencimiento
									$m_preced = 30 - $restante;
									if ($m_preced < 30) {
										$prorrateo = $act->monto / 30 * $m_preced;
										// $log .= "Mensual x debajo: $act->monto / 30 * $m_preced";
									}elseif($m_preced) {
										$prorrateo = $act->monto;
										// $log .= "Mensual arriba igual";
									}
									$log = "Prorrateo: dias($m_preced) - monto($prorrateo)";
								}else if($ciclo == '7'){
									$preced = 7 - $restante;
									if ($preced < 7) {
										$prorrateo = $act->monto * $preced * 0.15;
										$log .= "Semanal x debajo";
									}else{
										$prorrateo = $act->monto;
										$log .= "Semanal arriba igual";
									}   
									$log = "Prorrateo: dias($preced) - monto($prorrateo)";                         
								}
								$this->CuotaModel->Modificar(
									$act->id,
									array(
										'cuo_prorrateo' => $prorrateo,
										'cuo_dias_atraso' => 0,
										'cuo_mora' => 0,
										'cuo_estado' => 'E',
										'cuo_log' => $log
									)
								);
		
								
							}else{
								if ($act->inicio_mora < $fecha_actual && $act->cobra_mora == 'A' && $act->aplica_penalidad == 'A') {
									// $prorroga_activa = $this->CuotaModel->Buscar_prorroga_aplicada($act->credito, $act->inicio_mora);
									$proc_dias_atraso = date_diff(date_create($fecha_actual), date_create($act->inicio_mora))->format('%a');
									$proc_dias_atraso = $proc_dias_atraso + 1;
									$proc_mora = $proc_dias_atraso * $act->penalidad;
									$this->CuotaModel->Modificar(
										$act->id,
										array(
											'cuo_dias_atraso' => $proc_dias_atraso,
											'cuo_mora' => $proc_mora,
											'cuo_estado' => 'V'
										)
									);
									$mora_activa = $this->MoraModel->Buscar_libre('mor_codigo as id', array(
										'mor_credito' => $act->credito,
										'mor_estado' => 'PEN'
									)); 
									if ($mora_activa != null && $mora_activa->id > 0) {
										$this->MoraModel->Modificar_libre(
											array(
												'mor_cuota' => $act->id,
												'mor_dias' => $proc_dias_atraso,
												'mor_monto' => $proc_mora,
												'mor_estado_previo' => date("Y-m-d") 
											), 
											$proc_dias_atraso." > mor_dias AND mor_codigo = ".$mora_activa->id
										);
									}else{
										$this->MoraModel->Insertar(array(
											'mor_credito' => $act->credito,
											'mor_cuota' => $act->id,
											'mor_dias' => $proc_dias_atraso,
											'mor_monto' => $proc_mora,
											'mor_estado_previo' => 'PEN',
											'mor_actualizacion' => date("Y-m-d"),
											'mor_estado' => 'PEN'
										));
									}									
								}else{
									$this->CuotaModel->Modificar(
										$act->id,
										array(
											'cuo_dias_atraso' => 0,
											'cuo_mora' => 0,
											'cuo_estado' => 'V'
										)
									);
								}
							}
						}
		
		
		
					}
				}
				$this->db->trans_commit();
				return "OK";
			} catch (Exception $e) {
				$this->db->trans_rollback();
				return 'ERROR';
			}
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
							// if($c->orden > $orden){
							// 	$orden = $c->orden;
							// 	echo var_dump($c);
							// 	$item = $c;
								
							// }
							
							// if ($c->estado === 'E' OR $c->estado === 'CC') {
							// 	$activo = "SI";
							// }
							
						}

						if ($activo === "NO") {
							// // $termino = date('Y-m-d', strtotime($inicio. ' + 10 days'));
							// $ven = date('d-m-Y', strtotime($item->vencimiento. ' + 7 days'));
							// $item->vencimiento = $ven;
							// $item->estado = 'E';
							// // $item->cliente = 'E';
							// // $item->orden = $orden + 1;
							// $item->css = 'activo';
							// array_push($fake_array, $item);
							$second_array = $item;
						}
					}
				}
			}
			// print_r($fake_array);
			// echo json_encode($fake_array);
			return $fake_array;
		}

		function reporte_generar(){
			$this->load->library('pdf');
			ini_set('memory_limit', '256M');
			set_time_limit(500);
			$reporte = $this->reporte_pre();
			$estilo = base_url()."assets/css/reporte_diario.css";
			$fav = base_url()."assets/images/electrocash_favicon.png";
			$fecha = date("m-d-Y");
			$html = "";
			$cuota = "";
			foreach ($reporte as $r) {
				$nroden = ($r->orden <= 9) ? "0".$r->orden : $r->orden;
				$cuota .="
                <tr class='reporte-fila-$r->css'>
                    <td width=30px>
						$r->estado
					</td>
					<td width=40px>$r->credito</td>
					<td width=35px>$nroden</td>
                    <td width=140px style='text-align:left'>$r->cliente</td>
                    <td width=40px>S/ $r->prestamo</td>
					<td width=40px>S/ $r->interes</td>
					<td width=40px>S/ $r->penalidad</td>
					<td width=55px class=reporte-celda-$r->css>$r->vencimiento</td>
					<td width=20px>$r->dias</td>
					<td width=40px>S/ $r->mora</td>
					<td width=45px>S/ $r->interes</td>
					<td></td>
                </tr>
                ";
			}


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
			$this->pdf->stream("Electrocash_reporte_".$fecha.".pdf",array("Attachment" => false));
		}
		
		function sendEmail(){
			$this->load->library('email');

			$this->email->from('contacto@electrocashperu.com', 'Carlos');
			$this->email->to('blackrap13@gmail.com');
			$this->email->cc('Correo de Prueba');
			$this->email->bcc('blackrap13@gmail.com');

			$this->email->subject('Email Test');
			$this->email->message('Testing the email class.');

			$this->email->send();
		}



		function generate_anexo($id = null){
			$this->load->library('Pdf_dompdf');
			$logo_path  = FCPATH. 'assets/images/electrocash_logo.png';
			$firma_path = FCPATH. 'assets/images/firma.png';

			$logo_b64 = null; $firma_b64 = null;
			if (is_file($logo_path) && is_readable($logo_path)) {
				$type = pathinfo($logo_path, PATHINFO_EXTENSION);
				$file = file_get_contents($logo_path);
				if ($file !== false) {
					$logo_b64 = 'data:image/' .$type. ';base64,'. base64_encode($file);
				}
			}
			if (is_file($firma_path) && is_readable($firma_path)) {
				$type = pathinfo($firma_path, PATHINFO_EXTENSION);
				$file = file_get_contents($firma_path);
				if ($file !== false) {
					$firma_b64 = 'data:image/' .$type. ';base64,'. base64_encode($file);
				}
			}
			

			$credito  = $this->CreditoModel->getById($id);
			$sucursal = $this->SucursalModel->get_by_id($credito->sucursal_id);
			$week_html = $this->week_simulation($credito->ciclo_pago, $credito->creacion_raw);

			$data = [
			'nombres'      => $credito->cliente_nombres,
			'apellidos'    => $credito->cliente_paterno. ' ' .$credito->cliente_materno,
			'nom_ape'      => $credito->cliente_nom_ape,
			'documento'    => $credito->cliente_documento,
			'direccion'    => $credito->cliente_direccion,
			'distrito'     => $credito->cliente_distrito,
			'dir_dist' => $credito->cliente_direccion. ', ' .$credito->cliente_distrito,
			'telefono'     => $credito->cliente_telefono,
			'serie' 	   => $credito->serie,
			'monto'      => $credito->monto,
			'cuota'        => $credito->pago,
			'penalidad'    => $credito->penalidad,
			'creacion'	   => $credito->creacion,
			'creacion_raw' => $credito->creacion_raw,
			'liquidacion'  => $credito->liquidacion,
			'categoria'    => $credito->prenda_categoria,
			'prenda_tipo'  => $credito->prenda_tipo,
			'descripcion'  => $credito->prenda_descripcion,
			'marca'        => $credito->prenda_marca,
			'modelo'       => $credito->prenda_modelo,
			'serial'       => $credito->prenda_serie,
			'quilate'       => $credito->prenda_quilate,
			'peso'       => $credito->prenda_peso,
			'valoracion'   => $credito->prenda_valoracion,
			'observacion'=> $credito->prenda_observacion,
			'comentario'   => $credito->prenda_comentario,
			'logo' 		   => $logo_b64,
			'firma' 	   => $firma_b64,
			'sede' 		   => $sucursal,
			'ciclo'		   => $credito->ciclo_pago,
			'week_html'    => $week_html,
			'fecha_desc'   => $this->FechaDescriptiva()
			];

			$html = $this->load->view('pdf/anexo.php', $data, TRUE);
			$filename = "ANEXO_CREDITO_$credito->serie.pdf";
			$this->pdf_dompdf->generate($html, $filename, false);
		}
		function week_simulation($ciclo, $creacion, $cantidad = 15){
			$html = "";
			for ($i=1; $i<=$cantidad; $i++){
				$dias_ciclo = $ciclo === 'MENSUAL' ? 30 : 7;
				$dias_total = $dias_ciclo * $i;
				$fecha_str  = " + $dias_total days";
				$vencimiento = date("d-m-Y", strtotime(
					$creacion. $fecha_str
				));
				$orden = $i > 9 ? $i : '0'.$i;
				$html .= "
					<tr>
						<td style='text-align:center;font-weight:bold;'>$orden</td>
						<td style='text-align:center;'>$vencimiento</td>
					</tr>
				";
			}
			return $html;
		}
		function FechaDescriptiva(){
			$meses = [
				1 => 'enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio',
				'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'
			];

			$dia = date('j');  
			$numMes = date('n'); 
			$anio = date('Y');
			$mesTexto = $meses[$numMes];
			return "{$dia} de {$mesTexto} del {$anio}";
		}
		function cuota_simulation(){
			try {
				$this->load->model('TarifaModel');
				$monto 	 = $this->input->post("monto");
				$ciclo	 = $this->input->post("ciclo", TRUE) ?? 'SEMANAL';
				// $semanas = $this->input->post("semanas", TRUE);
				$cargo	 = $this->input->post("cargo") ?? 0;
				// $cargo = 0;
				// $ciclo = 'SEMANAL';
				$sede_id = $this->session->userdata('sucursal_id');				
				if ($monto <= 0) {
					response_json("Monto invalido.");
					return;
				}
				if (!in_array($ciclo, ['SEMANAL','MENSUAL'])){
					response_json("Ciclo invalido.");
					return;
				}

				$dias = 7;
				$tarifa = $this->TarifaModel->search_by_monto($sede_id, $monto);
				if (empty($tarifa)) {
					response_json("Tarifario no encontrado");
					return;
				}
				$tipo = $tarifa->tipo;
				$interes = floatval($tarifa->interes) + $cargo;
				$penalidad = round($tarifa->mora, 2);
				$mora = round($tarifa->mora, 2);
				$cuota = ($tipo === 'P') ? (($monto * $interes) /100 ) : $interes;
				$fecha = date("d-m-Y");
				$simulacion = $this->week_simulation($ciclo, $fecha, 4);

				echo json_encode([
					'success' => TRUE,
					'result' => [
						'monto' => round_money($monto),
						'tipo_tasa' => $tipo,
						'interes' => $interes,
						'penalidad' => round_money($penalidad),
						'cuota' => round_money($cuota),
						'mora' => $mora,
					]
				]);				
			} catch (Exception $e) {
				$this->session->sess_destroy();
				http_response_code(500);
				echo json_encode([
					'status' => 'ERR',
					'message' => $e->getMessage()
				], JSON_UNESCAPED_UNICODE);
			}
		}

		
	}
?>
