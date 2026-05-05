<?php 

	class PPago extends CI_Controller 
	{
		function __construct()
		{
			parent::__construct();
			if (!$this->session->userdata('pres_ses_sesion')) {
				redirect(base_url().'Acceso', 'refresh');
			}
			$this->load->model('PermisoModel');
			$this->load->model('SucursalModel');
			$this->load->model('PPagoModel');
		}

		public function procesar_pago(){
			$file = file_get_contents('php://input');
			$data_catch = json_decode($file);

			$suc = $this->session->userdata('pres_ses_sucursal');
			$emp_user = $this->session->userdata('pres_ses_usuario');
			$v_perm = $this->PermisoModel->verificar_permiso($emp_user, 'Pago_registrar');
			if ($v_perm && $v_perm->codigo === 'P') {
				try {
					$this->db->trans_begin();
					$ins_data = array(
						'pag_credito' => $data_catch->credito,
						'pag_fecha' => date('Y-m-d H-i-s'),
						'pag_forma_pago' => $data_catch->forma,
						'pag_comprobante' => $data_catch->comprobante,
						'pag_banco' => strtoupper($data_catch->banco),
						'pag_subtotal' => $data_catch->subtotal,
						'pag_descuento' => $data_catch->descuento,
						'pag_total' => $data_catch->total,
						'pag_recibido' => $data_catch->recibido,
						'pag_devuelto' => $data_catch->devuelto,
						'pag_empleado' => $this->session->userdata('pres_ses_empleado'),
						'pag_concepto' => $data_catch->concepto,
						'pag_adicional' => $data_catch->adicional,
						'pag_comentario' => $data_catch->comentario
					);
					$this->PPagoModel->Insertar($ins_data);
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
						die(json_encode(array('code' => 'ERROR', 'message' => 'Registro de pago incompleto')));
					}

					// Detalle Cuota
					if (isset($data_catch->detalle_cuota)) {
						if (!empty($data_catch->detalle_cuota)) {
							foreach ($data_catch->detalle_cuota as $d) {
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

					// Detalle de Mora
					if (isset($data_catch->detalle_mora)) {
						$d_mora = json_decode($data_catch->detalle_mora);					
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

					



				} catch (\Throwable $th) {
					//throw $th;
				}

			} else {
				die([
					'code' => 'WARN',
					'message' => 'Permisos Insuficientes'
				]);
			}
		}
	}
	