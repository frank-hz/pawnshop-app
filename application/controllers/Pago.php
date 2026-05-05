<?php  
	
	class Pago extends CI_Controller
	{
		
		function __construct()
		{
			parent::__construct();
			if (!$this->session->userdata('pres_ses_sesion')) {
				redirect(base_url().'Acceso','refresh');
			}
			$this->load->model('PagoModel');
			$this->load->model('CuotaModel');
			$this->load->model('MoraModel');
			$this->load->model('PrendaModel');
			$this->load->model('CreditoModel');
			$this->load->model('SucursalModel');
			$this->load->model('AdelantoModel');
			$this->load->model('ProrrogaModel');
			$this->load->model('PermisoModel');

			$this->load->model('CreditoAPIModel');
		}


		// V I E W S

		function index(){
			$this->load->view('Layout/header.php');
			$this->load->view('Pago/index.php');
			$this->load->view('Layout/footer.php');
		}

		function registrar(){
			ini_set('display_errors', 0);
			$post = $_POST;
			$suc = $this->session->userdata('pres_ses_sucursal');
			$resp = []; 
			$permiso = $this->PermisoModel->verificar_permiso(
				$this->session->userdata('pres_ses_usuario'),
				'pago_registrar'
			);
			if ($permiso && $permiso->codigo==='P') {
				$validar = $this->validate_registro($post);
				if (count($validar)===0) {
					try {
						$this->db->trans_begin();
						$adc = ($post['adicional']!=null) ? $post['adicional'] : 0;
						$comt = ($post['comentario']!=null) ? $post['comentario'] : '';
						$pdata = array(
							'pag_credito' => $post['credito'],
							'pag_fecha' => date("Y-m-d H:i:s"),
							'pag_forma_pago' => $post['forma'],
							'pag_comprobante' => $post['comprobante'],
							'pag_banco' => strtoupper($post['banco']),
							'pag_subtotal' => $post['subtotal'],
							'pag_descuento' => $post['descuento'],
							'pag_total' => $post['total'],
							'pag_recibido' => $post['recibido'],
							'pag_devuelto' => $post['devuelto'],
							'pag_empleado' => $this->session->userdata('pres_ses_empleado'),
							'pag_concepto' => $post['concepto'],
							'pag_adicional' => $adc,
							'pag_comentario' => $comt
						);
						// die(json_encode($pdata));
						$this->PagoModel->Insertar($pdata);
						$id_pago = $this->db->insert_id();
						$nomen = $this->SucursalModel->Buscar_libre(
							'suc_pago_serie as serie', array('suc_codigo'=>$suc)
						);
						if (empty($nomen)) {
							die(json_encode(array('status' => 'ERROR', 'message' => 'Identificacion de sucursal erronea')));
						}
						$orden = $this->PagoModel->Buscar_orden_actual($nomen->serie, $suc);
						$n_orden = $orden->orden + 1;
						$p_serie = $nomen->serie.str_pad($n_orden, 7,"0", STR_PAD_LEFT);
						$this->PagoModel->Modificar($id_pago, array(
							'pag_serie' => $p_serie,
							'pag_orden' => $n_orden,
							'pag_nomen' => $nomen->serie
						));
						if ($this->db->affected_rows() <= 0) {
							die(json_encode(array('status' => 'ERROR', 'message' => 'Registro de pago incompleto')));
						}


						// Detalle de tipo CUOTA
						if (isset($post['detalle_cuota'])) {
							$d_cuota = json_decode($post['detalle_cuota']);
							if (!empty($d_cuota)) {
								foreach ($d_cuota as $d) {
									$info_cuota = $this->CuotaModel->Buscar_libre(
										'cuo_estado as estado',
										array('cuo_codigo' => $d->id)
									);
									if ($info_cuota->estado === 'E' || $info_cuota->estado == 'V' || $info_cuota->estado == 'CC') {
										$this->PagoModel->Insertar_detalle(
											array(
												'pdet_pago' => $id_pago,
												'pdet_referencia' => $d->referencia,
												'pdet_tipo' => $d->tipo,
												'pdet_id_ref' => $d->id,
												'pdet_monto' => $d->monto
											)
										);
										$this->CuotaModel->Modificar($d->id ,array('cuo_estado' => 'P'));
										if ($this->db->affected_rows() === 0) {
											die(json_encode(array('status'=>'ERROR','message'=>'error al procesar las cuotas')));
										}
									}else{
										die(json_encode(array(
											'status' => 'ERROR',
											'message' => 'Cuotas no aptas para pagar, posible duplicidad de pago. Revise el detalle del credito'
										)));
									}
								}
							}
						}
						// Detalle de tipo MORA
						if (isset($post['detalle_mora'])) {
							$d_mora = json_decode($post['detalle_mora']);							
							if (!empty($d_mora)) {
								foreach ($d_mora as $m) {
									$mora_info = $this->MoraModel->Buscar_libre(
										'mor_estado as estado',
										array('mor_codigo' => $m->id)
									);
									if ($mora_info->estado === 'PEN' OR $mora_info->estado === 'POS') {
										if ($m->accion === 'C') {
											$this->PagoModel->Insertar_detalle(
												array(
													'pdet_pago' => $id_pago,
													'pdet_referencia' => $m->referencia,
													'pdet_tipo' => $m->tipo,
													'pdet_id_ref' => $m->id,
													'pdet_monto' => $m->monto
												)
											);
											$this->MoraModel->Modificar($m->id ,array(
												'mor_estado' => 'PAG',
												'mor_estado_previo' => $mora_info->estado
											));
											if ($this->db->affected_rows() === 0) {
												die(json_encode(array('status'=>'ERROR','message'=>'error al procesar la mora')));
											}
										}elseif($m->accion === 'P'){
											$this->MoraModel->Modificar($m->id ,array(
												'mor_estado' => 'POS',
												'mor_estado_previo' => $mora_info->estado
											));
											$inicio = date("Y-m-d");
											$termino = date('Y-m-d', strtotime($inicio. ' + 10 days'));
											
											$v_prorroga = $this->MoraModel->Verifica_prorroga($post['credito'], date("Y-m-d")); 
											if (count($v_prorroga) === 0) {
												$this->ProrrogaModel->Insertar(
													array(
														'pror_credito' => $post['credito'],
														'pror_inicio' => $inicio,
														'pror_termino' => $termino,
														'pror_estado' => 'A'
													)
												);
											}
											$this->CuotaModel->Modificar_evaluacion_mora($_POST['credito']);
										}
									}else{
										die(json_encode(array(
											'status' => 'ERROR',
											'message' => 'Mora no apta para pagar'
										)));
									}
								}
							}
						}
						// Detalle de tipo CAPITAL
						if (isset($post['detalle_capital'])) {
							$d_capital = json_decode($post['detalle_capital']);
							if (!empty($d_capital)) {
								foreach ($d_capital as $cp) {
									$this->PagoModel->Insertar_detalle(
										array(
											'pdet_pago' => $id_pago,
											'pdet_referencia' => $cp->referencia,
											'pdet_tipo' => $cp->tipo,
											'pdet_id_ref' => $cp->id,
											'pdet_monto' => $cp->monto
										)
									);
									$this->CreditoModel->Modificar_amortizacion($cp->id,$cp->monto);
									/*if ($this->db->affected_rows() === 0) {
										die(json_encode(array('status'=>'ERROR','message'=>'error al procesar el capital')));
									}*/
								}
							}
						}

						$mensaje = 'Pago registrado';
						if ($post['concepto'] === 'REC') {
							$this->CreditoModel->Modificar($post['credito'], array('cred_estado' => 'R', 'cred_liquidacion' => date("Y-m-d")));
							if ($this->db->affected_rows() > 0) {	
								$prenda = intval($post['prenda']);
								if ($prenda > 0) {
									$this->PrendaModel->Modificar($prenda, array('pren_estado' => 'R'));
								}
								$mensaje = 'Credito Recuperado';
							}else{
								die(json_encode(array('status'=>'ERROR','message'=>'error al establecer la recuperacion')));
							}
						}elseif ($post['concepto'] === 'ADEL') {
							$this->CreditoModel->Modificar_adelanto($_POST['credito'], $_POST['total'], 'SUMA');
							$this->AdelantoModel->Insertar_entrada(
								array(
									'ent_credito' => $_POST['credito'],
									'ent_pago' => $id_pago,
									'ent_monto' => $_POST['total'],
									'ent_fecha' => date("Y-m-d")
								)
							);
							$cant_adel = $_POST['cantidad_adelanto'];

							$cuota_aplica = $this->CuotaModel->Buscar_libre(
								"cuo_codigo as id, cuo_orden as orden, cuo_monto as monto",
								"cuo_credito = ".$post['credito']." AND cuo_vencimiento >= '".date("Y-m-d")."' AND (cuo_estado = 'E' OR cuo_estado = 'V')",'list'
							);
							
							if ($cuota_aplica != null) {
								foreach ($cuota_aplica as $cu) {
									$adelanto = $this->CreditoModel->Buscar_libre(
										'cred_adelanto as monto', 
										'cred_codigo = '.$post['credito']
									);
									// die(json_encode($cu->monto));
									if ($adelanto->monto >= $cu->monto) {
										$this->CuotaModel->Modificar($cu->id, array(
											'cuo_estado' => 'P'
										));
										$this->PagoModel->Insertar_detalle(
											array(
												'pdet_pago' => $id_pago,
												'pdet_referencia' => "Cuota $cu->orden - Adelanto",
												'pdet_tipo' => 'CU',
												'pdet_id_ref' => $cu->id,
												'pdet_monto' => $cu->monto
											)
										);
										$this->CreditoModel->Modificar_adelanto($post['credito'], $cu->monto, 'RESTA');
										$this->AdelantoModel->Insertar_salida(array(
											'sal_credito' => $post['credito'],
											'sal_tipo' => 'ND',
											'sal_modo' => 'AUTO',
											'sal_detalle' => 'ADELANTO APLICADO : CUOTA '.$cu->orden,
											'sal_monto' => $cu->monto,
											'sal_id_ref' => $cu->id,
											'sal_fecha' => date("Y-m-d")
										));
										$cant_adel--;
									}
								}
								
							}
							if ($cant_adel > 0) {
								$credito_data = $this->CreditoModel->Buscar_libre(
									'cred_prorroga as prorroga, cred_pago_semanal as pago, cred_penalidad as penalidad', 
									array("cred_codigo" => $post['credito'])
								); 
								for ($i=0; $i < $cant_adel; $i++) {
									$cuota_reciente = $this->CuotaModel->Buscar_libre(
										"MAX(cuo_vencimiento) as vence, MAX(cuo_orden) as orden", 
										array('cuo_credito' => $post['credito'])
									);
									$vence = date("Y-m-d", strtotime($cuota_reciente->vence. ' + 7 days'));
									if (date('w', strtotime($vence)) === 0) {
										while(date('w', strtotime($vence)) === 0) {
											$vence = date("Y-m-d", strtotime($vence. ' + 1 days'));
										}
									}
									$ini_mora = date('Y-m-d', strtotime($vence. " + ".$credito_data->prorroga." days"));
									if (date('w', strtotime($ini_mora)) === 0) {
										while(date('w', strtotime($ini_mora)) === 0) {
											$ini_mora = date("Y-m-d", strtotime($ini_mora. ' + 1 days'));
										}
									}
									$norden = $cuota_reciente->orden + 1;
									$this->CuotaModel->insertar(
										array(
											'cuo_credito' => $post['credito'],
											'cuo_orden' => $norden,
											'cuo_monto' => $credito_data->pago,
											'cuo_penalidad' => $credito_data->penalidad,
											'cuo_vencimiento' => $vence,
											'cuo_inicio_mora' => $ini_mora,
											'cuo_estado' => 'P'
										)
									);
									$id_cuota = $this->db->insert_id();
									$this->PagoModel->Insertar_detalle(
										array(
											'pdet_pago' => $id_pago,
											'pdet_referencia' => "Cuota $norden - Adelanto",
											'pdet_tipo' => 'CU',
											'pdet_id_ref' => $id_cuota,
											'pdet_monto' => $credito_data->pago
										)
									);
									$this->CreditoModel->Modificar_adelanto($post['credito'], $credito_data->pago, 'RESTA');
									if ($this->db->affected_rows() == 0) {
										die(json_encode(array('status'=>'ERROR','message'=>'Error al procesar')));
									}
									$this->AdelantoModel->Insertar_salida(array(
										'sal_credito' => $post['credito'],
										'sal_tipo' => 'ND',
										'sal_modo' => 'AUTO',
										'sal_detalle' => 'ADELANTO APLICADO : CUOTA '.$norden,
										'sal_monto' => $credito_data->pago,
										'sal_id_ref' => $id_cuota,
										'sal_fecha' => date("Y-m-d")
									));

								}
							}
						}

						$resp = array(
							'status' => 'OK',
							'message' => $mensaje,
							'id' => $id_pago,
							'error' => $validar
						);
						$this->db->trans_commit();
					} catch (PDOException $e) {
						$this->db->trans_rollback();
						die(json_encode(array(
							'status' => 'ERROR',
							'message' => $e->getMessage()
						)));
					}					
				}else{
					$resp['status'] = 'ERROR';
					$resp['message'] = 'Errores encontrados';
					$resp['error'] = $validar;
				}
			}else{
				$resp['status'] = 'WARN';
				$resp['message'] = 'Permisos insuficientes.';
				$resp['id'] = '';
			}
			echo json_encode($resp);
		}

		function consulta(){
			$sucursal = $this->session->userdata('pres_ses_sucursal');
			$res = [];
			if ($_POST['tipo'] === 'fecha_rango') {
				$res = $this->PagoModel->Rango_fecha(
					array(
						$_POST['inicio'],
						$_POST['final'],
						$_POST['limite'],
						$sucursal
					)
				);
			}else{
				$buscar = ($_POST['tipo'] === 'fecha') ? date("Y-m-d") : $_POST['buscar'];
				$res = $this->PagoModel->RecodeListar(
					$_POST['tipo'],
					$buscar,
					$_POST['limite'],
					$sucursal
				);
			}
			echo json_encode($res);
		}

		function regularizacion($idcredito = null){
			if ($idcredito == null){
				error_render_view("CUIDADO","Credito no identificado");
				return;
			}			
			if (!permission_validate('pago_registrar')){
				error_render_view("PERMISOS INSUFICIENTES","Accion no permitida.");
				return;
			}
			
			$credito = $this->CreditoAPIModel->getById($idcredito);
			if (empty($credito)){
				error_render_view("CUIDADO","Credito no encontrado.");
				return;
			}
			if ($credito->estado != 'E'){
				error_render_view("CUIDADO","El credito no esta activo.");
				return;
			}
			$cuotas = $this->CuotaModel->getForRegularizar($idcredito);			
			$mora = $this->MoraModel->Buscar_pendiente($idcredito);
			$mora_pospuesta = $this->MoraModel->Buscar_pospuesto($idcredito);

			if (empty($cuotas) && empty($mora) && empty($mora_pospuesta)){
				error_render_view("AVISO", "El credito no tiene pagos pendientes.");
				return;
			}

			// $pendiente = Array();
			// foreach($cuotas as $cuota){
			// 	$cu = new stdClass();
			// 	$cu->id = $cuota->id;
			// 	$cu->tipo = "CUOTA";
			// 	$cu->descripcion = "Cuota ". $cuota->orden;
			// 	$cu->orden = $cuota->orden;
			// 	$cu->vencimiento = $cuota->vencimiento;
			// 	$cu->monto = $cuota->monto;
			// 	$cu->estado = $cuota->estado;
			// 	array_push($pendiente, $cu);
			// }

			// foreach($mora_pospuesta as $pospuesto){
			// 	$mo = new stdClass();
			// 	$mo->id = $pospuesto->id;
			// 	$mo->tipo = "MORA_POSPUESTA";
			// 	$mo->descripcion = "MORA POSPUESTA";
			// 	$mo->monto = $pospuesto->monto;
			// }
			
			// $data['pendiente'] = $pendiente;
			$data['credito'] = $credito;
			$data['cuotas'] = $cuotas;
			$data['mora'] = $mora;
			$data['mora_pospuesta'] = $mora_pospuesta;

			$this->load->view("Layout/header");
			$this->load->view("Pago/regularizar", $data);
			$this->load->view("Layout/footer");

		}
		
		// function procesar_adelanto(){
		// 	$resp = []; 
		// 	$permiso = $this->PermisoModel->verificar_permiso(
		// 		$this->session->userdata('pres_ses_usuario'),
		// 		'credito_liquidar'
		// 	);
		// 	if ($permiso && $permiso->codigo==='P') {
		// 		$validar_pago = $this->
		// 	}else{
		// 		$resp = array(
		// 			'status' => 'WARN',
		// 			'message' => 'Permisos insuficientes'
		// 		);
		// 	}
		// 	echo json_encode($resp);
		// }

		function detalle($id = null){
			if ($id != null) {
				$data['pago'] = $this->PagoModel->getPagoDetail($id);
				$data['detalle'] = $this->PagoModel->obtener_detalle($id);

				if (!empty($data['pago'])) {
					$this->load->view("Layout/header.php");
					$this->load->view("Pago/detalle.php", $data);
					$this->load->view("Layout/footer.php");
				}else{
					$data['titulo'] = 'Pago desconocido';
					$data['mensaje'] = 'No se encontraron datos del pago';
					$this->load->view("Layout/header.php");
					$this->load->view("Error/no_found.php", $data);
					$this->load->view("Layout/footer.php");
				}
			}else{
				$data['titulo'] = 'Pago desconocido';
				$data['mensaje'] = 'No se encontraron datos para el seguimiento del pago';
				$this->load->view("Layout/header.php");
				$this->load->view("Error/no_found.php", $data);
				$this->load->view("Layout/footer.php");
			}
		}

		function enviarEmail(){
			try {
				if (!$_POST['id']) throw new Exception("Pago desconocido.");
				$idx = $_POST['id'];
				$pago = $this->PagoModel->obtener_pago($idx);
				$detalle = $this->PagoModel->obtener_detalle($idx);
				$email = $_POST['email'];

				if ($email && strlen($email) > 0) {
					$concepto = "";
					switch($pago->concepto){
		                case "REG":
		                  $concepto = "REGULARIZACION";
		                  break;
		                case "REC":
		                  $concepto = "LIQUIDACION";
		                  break;
		                case "AMOR":
		                  $concepto = "AMORTIZACION";
		                  break;
		                case "ADEL":
		                  $concepto = "ADELANTO";
		                  break;
	              	}
	              	$htmlx = "
	              		<html>
							<head>
								<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
								<title></title>
					  		</head>
					  		<body>
	              	";              	
					
					$htmlx .= "
					<div style='width:450px;padding: .5em;font-family: 'Maven Pro', sans-serif;border-radius: 2px;'>
						<div style='padding: 4px 2px;'>
			            	<span style='display: block;text-align: center;font-weight: 600;'>PAGO DE CREDITO</span>
			          	</div>
			          	<div style='display:block;overflow:hidden;padding: 2px 0;'>            
			            	<span style='float: left;'>CREDITO: $pago->codigo_credito</span>
			            	<span style='float: right;'>PAGO: $pago->serie</span>
			          	</div>
			          	<div>
			            	<span>
			              		CONCEPTO:
			              		$concepto
			            	</span>
			            	<span style='display: block;padding: 2px 0;'>CLIENTE: $pago->cliente</span>
			            	<span style='display: block;padding: 2px 0;'>FECHA: $pago->fecha</span>
			          	</div>
			          	<div style='border-bottom: 1px dashed rgb(173, 181, 189);margin: 2px 0;'></div>				          	
				        ";
				    $htmlx .= '<table style="width: 100%;">';
		            if (!empty($detalle)){
		            	foreach ($detalle as $ind => $d) {
		            		$ord = $ind+1;
		            		$htmlx .= "
		            			<tr>
					                <td style='text-align:center;'>$ord</td>
					                <td>
					                  <p style='margin-bottom:2px; padding: 2px 8px;'>
					                    $d->referencia $d->subref
					                  </p>
					                </td>
					                <td style='text-align:right'>S/ $d->monto</td>
				              	</tr>
		            		";
		            	}
		            }else{
		            	$htmlx .= '
		            		<tr style="text-align:center;">
				              <td colspan="3">Datos no encontrados</td>
				            </tr>
		            	';
		            }
			        $htmlx .= "
			        	<tr style='border-bottom: 1px dashed rgb(173, 181, 189);margin: 2px 0;'>
			            	<td colspan='3'></td>
			          	</tr>
			          	<tr>
			            	<td colspan='2' style='text-align:right;'>Subtotal</td>
			            	<td style='text-align:right'>S/ $pago->subtotal</td>
			          	</tr>
			          	<tr>
			            	<td colspan='2' style='text-align:right;'>Descuento</td>
			            	<td style='text-align:right'>S/ $pago->descuento</td>
			          	</tr>
			          	<tr>
			            	<td colspan='2' style='font-weight:600; text-transform:uppercase;text-align:right'>Total</td>
			            	<td style='font-weight:600;text-align:right'>S/ $pago->total</td>
			          	</tr>
			          	<tr>
			            	<td colspan='2' style='text-transform:uppercase;text-align:right;'>Recibido</td>
			            	<td style='color:blue;text-align:right;'>S/ $pago->recibido</td>
			          	</tr>
			          	<tr>
			            	<td colspan='2' style='text-transform:uppercase;text-align:right;'>Vuelto</td>
			            	<td style='color:red;text-align:right;'>S/ $pago->devuelto</td>
			          	</tr>				          
					";

					$htmlx .= '</table></div></body></html>';
					

					$config = Array(
				        'mailtype'  => 'html'
				    );

					$this->load->library('email', $config);
					$this->email->initialize($config);
					$this->email->from('contacto@electrocashperu.com', 'Electrocash');
					$this->email->to($email);
					$this->email->cc('Correo de Prueba');
					$this->email->bcc($email);

					$this->email->subject('Recibo de Pago');
					$this->email->message($htmlx);

					$this->email->send();
					echo json_encode([
						'code' => 'OK',
						'message' => 'Voucher de Pago enviado a '.$email,
					]);
				}else{
					echo json_encode([
						'code' => 'ERROR',
						'message' => 'Email del cliente no definido.'
					]);
				}
			} catch (Exception $e) {
				echo json_encode([
					'code' => 'ERROR',
					'message' => 'Se produjo un error al procesar'
				]);
			}			
		}

		function validate_registro($data){
			$errors = [];
			if(empty($data['credito'])) $errors['credito'] = 'Credito desconocido';
			if(empty($data['forma'])) $errors['forma'] = 'La forma de pago es requerida';
			if(empty($data['total']) || $data['total'] <= 0) $errors['total'] = 'Total a pagar no definido.';
			if(!isset($data['concepto']) || empty($data['concepto']) ) $errors['concepto'] = 'Concepto de pago desconocido.';
			if(empty($data['recibido']) || $data['recibido'] <= 0 || $data['recibido'] < $data['total']) {
				if (empty($data['recibido'])) {
					$errors['recibido'] = 'Monto recibido no definido';
				}elseif ($data['recibido'] <= 0) {
					$errors['recibido'] = 'Monto recibido no valido';
				}
				if ($data['concepto'] === 'REC' || $data['concepto'] === 'REG') {
					if ($data['recibido'] < $data['total']) {
						$errors['recibido'] = 'El monto recibido no es suficiente';
					}
				}
			}
			return $errors;
		}

		function voucher_generar($id = null){
			if ($id != null) {
				$this->load->library('pdf');
				ini_set('memory_limit', '256M');
				set_time_limit(300);
				$pago = $this->PagoModel->consulta_pago($id);
				$detalle = $this->PagoModel->consulta_detalle($id);

				$fecha 	= date("d/m/Y");
		    	$hora  	= date("h:i:sa");
		    	$concepto = (empty($pago->concepto)) ? 'PAGO' : $pago->concepto;

				$estilo = base_url()."assets/css/voucher.css";
				$fav = base_url()."assets/images/electrocash_favicon.png";
				$html = "";
				$html .= "<!DOCTYPE html>
					<html lang='es'>
					<head>
						<meta name='viewport' content='width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0'>
						<meta charset='UTF-8'>
						<link rel='shortcut icon' type='image/png' href=$fav>
						<title>$pago->serie</title>
						<link rel='stylesheet' href=$estilo>
					</head>
					<body>";
				
				$html .= "<h3 class='titulo'>$concepto</h3>";
				$html .= "<div class='header'>";
				$html .= "<p><b>CREDITO:</b> $pago->codigo_credito</p>";
				$html .= "<p><b>CLIENTE:</b> $pago->cliente</p>";
				$html .= "</div>";
				$html .= "<table class='tabla-v'>";;
				$html .= "<tbody>";
				$html .= "<tr>";
				$html .= "<td>DESCRIPCION<td>";
				$html .= "<td>TIPO<td>";
				$html .= "<td>MONTO<td>";
				$html .= "</tr>";
				$html .= "</tbody>";
				$html .= "</table>";
				$html .= "</body></html>";
				
				$this->pdf->loadHtml($html);
				// $this->pdf->setPaper('A4', 'potrait');
				$this->pdf->set_paper(array(0,0,500,1000));
				// $paper_size = array(0,0,100,360);
				// $this->pdf->set_paper($paper_size);
				$this->pdf->render();
				$this->pdf->stream("voucher_".$pago->serie.".pdf",array("Attachment" => false));
			}
		}

		function evaluar_adelanto($id){
			try {
				$this->db->trans_begin();
				$suc = $this->session->userdata('pres_ses_sucursal');
				$cuotas = $this->AdelantoModel->Buscar_cuota_aplicable($id);
				if (!empty($cuotas)) {
					foreach ($cuotas as $cu) {
						$adelanto = $this->CreditoModel->Buscar_libre('cred_adelanto as monto', array(
							'cred_codigo' => $id
						));
						if ($adelanto != null && $adelanto->monto > 0) {
							$sucdata = $this->SucursalModel->Buscar_libre(
								'suc_pago_serie as serie', array('suc_codigo'=>$suc)
							);
							if (empty($sucdata) && $sucdata != null) 
								die(json_encode(array('status'=>'ERROR','message'=>'Identificacion de sucursal erronea')));

							$orden = $this->PagoModel->Buscar_orden_actual($sucdata->serie, $suc);
							$n_orden = $orden->orden + 1;
							$p_serie = $sucdata->serie.str_pad($n_orden, 7,"0", STR_PAD_LEFT);
							$pdata = array(
								'pag_credito' => $id,
								'pag_fecha' => date("Y-m-d H:i:s"),
								'pag_forma_pago' => 'REV',
								'pag_subtotal' => $cu->monto,
								'pag_descuento' => 0,
								'pag_total' => $cu->monto,
								'pag_recibido' => $cu->monto,
								'pag_devuelto' => 0,
								'pag_empleado' => $this->session->userdata('pres_ses_empleado'),
								'pag_concepto' => 'ADEL_AUTO',
								'pag_serie' => $p_serie,
								'pag_orden' => $n_orden,
								'pag_nomen' => $sucdata->serie
							);
							$this->PagoModel->Insertar($pdata);
							$id_pago = $this->db->insert_id();
							$this->PagoModel->Insertar_detalle(
								array(
									'pdet_pago' => $id_pago,
									'pdet_referencia' => 'CUOTA '.$cu->orden.' - ADELANTO AUTOMATICO',
									'pdet_tipo' => 'CU',
									'pdet_id_ref' => $cu->id,
									'pdet_monto' => $cu->monto
								)
							);
							$this->CuotaModel->Modificar($cu->id ,array('cuo_estado' => 'P'));
							$this->CreditoModel->Modificar_adelanto($id, $cu->monto, 'RESTA');

							$this->AdelantoModel->Insertar_salida(array(
								'sal_credito' => $id,
								'sal_tipo' => 'ADEL_AUTO',
								'sal_modo' => 'AUTO',
								'sal_detalle' => 'PAGO AUTOMATICO POR ADELANTO : CUOTA '.$cu->orden,
								'sal_fecha' => date("Y-m-d")
							));
						}
					}						
				}
				$this->db->trans_commit();
				return 'TRUE';
			} catch (PDOException $e) {
				$this->db->trans_rollback();
				return 'FALSE';
			}
		}

		function anulacion(){
			$rsp = [];
			if (!empty($_POST['credito_id']) && !empty($_POST['pago_id'])) {
				$cdata = $this->CreditoModel->Buscar_libre(
					'cred_estado as estado',
					'cred_codigo = '.$_POST['credito_id']
				);
				if (!empty($cdata) && $cdata->estado === 'E') {
					try {
						$this->db->trans_begin();
						$p_data = $this->PagoModel->Buscar_libre(
							'DATE_FORMAT(pag_fecha, "%Y-%m-%d") as fecha, pag_concepto as concepto',
							'pag_codigo = '.$_POST['pago_id']
						);
						$fhoy = date("Y-m-d");
						if ($p_data->fecha === $fhoy) {
							$d_pago = $this->PagoModel->Buscar_detalle($_POST['pago_id']);
							if(empty($d_pago)){
								die(json_encode(array(
									'status' => 'ERROR',
									'message' => 'Error al procesar'
								)));
							}
							// die(json_encode($dpago));
							foreach ($d_pago as $d) {
								switch ($d->tipo) {
									case 'CU':
										$cudata = $this->CuotaModel->Buscar_libre(
											'cuo_vencimiento as vence', 
											'cuo_codigo = '.$d->id_ref
										);
										$nestado = ($cudata->vence >= $fhoy) ? 'E' : 'V';
										$this->CuotaModel->Modificar($d->id_ref, array('cuo_estado' => $nestado));
										if ($this->db->affected_rows() === 0) die("Error: cuota");
										break;
									
									case 'CAP':
										$this->CreditoModel->Reducir_amortizacion($d->id_ref, $d->monto);
										if($this->db->affected_rows() === 0) die("Error: capital");
										break;

									case 'MO':
										$m_data = $this->MoraModel->Buscar_libre(
											'mor_estado_previo as estado_prev', 
											'mor_codigo = '.$d->id_ref
										);
										$this->MoraModel->Modificar($d->id_ref, array(
											'mor_estado' => $m_data->estado_prev
										));
										if($this->db->affected_rows() === 0) die("Error: mora");
										break;
								}

								if ($p_data->concepto === 'ADEL') {
									// $this->CuotaModel->Eliminar($d->id_ref);
									die(json_encode(array(
										'status' => 'WARN',
										'message' => 'Anulacion no disponible'
									)));
								}
							}
							$this->PagoModel->Eliminar_detalle($_POST['pago_id']);
							$this->PagoModel->Eliminar($_POST['pago_id']);
							if($this->db->affected_rows() === 0) die("Error: pago");
							$rsp = array(
								'status' => 'OK',
								'message' => 'Pago anulado'
							);
						}else{
							die(json_encode(array(
								'status' => 'ERROR',
								'message' => 'Fecha limite para anulacion finalizado'
							)));
						}
						$this->db->trans_commit();
					} catch (PDOException $e) {
						$this->db->trans_rollback();
						die(json_encode(array(
							'status' => 'ERROR',
							'message' => 'Error al procesar'
						)));
					}
				}else{
					$rsp = array(
						'status' => 'ERROR',
						'message' => (empty($cdata)) ? 'Credito no encontrado' : 'El credito no esta activo'
					);
				}
			}else{
				$rsp = array(
					'status' => 'ERROR',
					'message' => 'Pago no reconocido'
				);
			}
			echo json_encode($rsp);
		}



		// Update 05-06-22

		function service_all(){
			$file = file_get_contents('php://input');
			$data_rcv = json_decode($file);

			$data_out = $this->PagoModel->GetAll($data_rcv);
			echo json_encode($data_out);
			
		}

		// Update 17/07/22
		function service_get_all(){
			$file = file_get_contents('php://input');
			$data_in = json_decode($file);
            $data = $this->PagoModel->GetSearchAll($data_in->id);
            echo json_encode($data);
		}

		function service_get_data(){
			$file = file_get_contents("php://input");
			$datain = json_decode($file);
			$data = $this->PagoModel->get_data($datain);
            echo json_encode($data);
		}

	}
?>
