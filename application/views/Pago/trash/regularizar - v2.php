<div class="px-1">
	<div class="card">
		<div class="card-header">
			<h4 class="card-title font-weight-bold">
				<a href="<?= base_url()?>Credito/detalle/<?= $credito->id; ?>">
					<span class="text-blue">#<?php echo $credito->codigo; ?></span>
				</a>
				| REGULARIZACION
			</h4>
			<div class="card-options">
				<a href="<?= base_url() ?>pago/regularizacion/<?= $credito->id; ?>" class="btn btn-blue">
					Version 2
				</a>
			</div>
		</div>
		<div class="card-body">
			<div class="row">
				<div class="col-xs-12 col-md-6">
					<div class="card">
						<div class="card-header">
							<!-- <i class="fe fe-info text-blue"></i>&nbsp; -->
							<!-- <div class="card-title">
								Cuotas pendientes
								<div class="d-block text-primary font-weight-bold mt-1" style="font-size: .85rem">
									<?php echo count($cuotas)." Cuotas"; ?>
								</div>
							</div> -->
							<div class="d-flex align-items-center text-dark font-weight-bold mt-1" style="font-size: 1.1rem">
								Cuotas:&nbsp;
								<div class="text-danger font-weight-bold" style="font-size:1.4rem">
									<?php
										$cant = count($cuotas);
										echo ($cant < 10) ? '0'.$cant : $cant;
									?>
								</div>
							</div>
						</div>
						<div class="row p-2">
							<div class="col-xs-12 col-md-4 form-group">
								<div class="form-label">Estilo de tabla</div>
		                        <label class="custom-switch">
		                          	<input type="checkbox" id="check_darkmode" class="custom-switch-input">
		                          	<span class="custom-switch-indicator"></span>
		                          	<span class="custom-switch-description">
		                          		Modo Oscuro
		                          	</span>
		                        </label>
							</div>
							<?php if (!empty($cuotas)): ?>
							<div class="col-xs-12 col-md-4 form-group">
		                        <div class="form-label">Seleccionar todo</div>
		                        <div>
		                          	<label class="custom-control custom-checkbox custom-control-inline">
		                            	<input type="checkbox" class="custom-control-input" id="check_seleccion_total">
		                            	<span class="custom-control-label">Marcar</span>
		                          	</label>
		                        </div>
	                      	</div>							
							<?php endif ?>
						</div>
						<div class="table-responsive" style="max-height: 600px">
							<table id="tabla_pendiente" class="table table-sm table-bordered">
								<thead>
									<tr class="text-center">
										<th class="text-center">#</th>
										<th>Cuota</th>
										<th>Vencimiento</th>
										<th>Monto</th>
										<th>Estado</th>
										<th class="font-weight-bold text-center text-success">PAGAR</th>
									</tr>
								</thead>
								<tbody>
									<?php if (!empty($cuotas)): ?>
										<?php foreach ($cuotas as $c=>$val): ?>
											<tr class="text-center">
												<td class="text-center">
													<?php echo $c+1; ?>
												</td>
												<td>
													Cuota <?php echo $val->orden; ?>
												</td>
												<td><?php echo $val->vencimiento; ?></td>
												<td>S/ <?php echo $val->monto; ?></td>
												<td><?php echo $val->estado; ?></td>
												<td class="text-center">
													<input type="checkbox" name="cuota_item" data-id="<?= $val->id; ?>" data-monto="<?= $val->monto; ?>" data-orden="<?= $val->orden; ?>" <?php if (in_array($val->estado,['V','E','CC'])){echo 'checked';}  ?> >
												</td>
											</tr>
										<?php endforeach ?>
									<?php else: ?>
										<tr class="text-center">
											<td colspan="5">No hay cuotas pendientes</td>
										</tr>
									<?php endif ?>

									<?php if (!empty($pospuesto)): ?>
										<?php foreach ($pospuesto as $p): ?>
											<tr >
												<td colspan="2" class="text-center">MORA POSPUESTA</td>
												<td colspan="2" class="text-center">S/ <?php echo $p->monto; ?></td>
												<td class="text-center">
													<input type="checkbox" name="pospuesto_item" data-id="<?= $p->id; ?>" data-monto="<?= $p->monto; ?>">
												</td>
											</tr>
										<?php endforeach ?>
									<?php endif ?>
								</tbody>
							</table>
						</div>
					</div>
				</div>
				<div class="col-xs-12 col-md-6">
					<form id="form_pago_registro" autocomplete="off">
						<input type="hidden" name="credito" value="<?= $credito->id; ?>">
						<div class="card">
							<div class="card-header d-flex justify-between">
								<?php if (!empty($credito) && $credito->id): ?>
								<a href="<?= base_url() ?>credito/detalle/<?= $credito->id; ?>" class="btn-link">
									#<?= $credito->serie; ?>
								</a>
								<?php endif; ?>

								<?php if (!empty($credito) && $credito->cliente_id): ?>
								<a href="<?= base_url() ?>cliente/detalle/<?= $credito->cliente_id; ?>" class="btn-link">
									<?= $credito->cliente_nombre; ?>
								</a>
								<?php endif; ?>
							</div>
							<div class="card-header px-1">
								<ul class="nav nav-pills px-0">
									<li class="nav-item">
										<a class="nav-link p-2 active" href="#" data-toggle="tab" data-target="#CARD_INFO">
											<i class="fe fe-credit-card"></i> &nbsp;
											Informacion del Pago
										</a>
									</li>
									<li class="nav-item">
										<a class="nav-link p-2" href="#" data-toggle="tab" data-target="#CARD_ADVANCED">
											<i class="fe fe-chevrons-right"></i> 
											Avanzado
										</a>
									</li>
								</ul>
							</div>
							<div class="tab-content">
								<div id="CARD_INFO" class="tab-pane fade show active" role="tabpanel">
									<table class="table card-table table-sm">
										<tbody>
											<tr class="text-center">		                      		
												  <td colspan="4">
													  <h3 id="recibo_total" class="text-primary font-weight-bold p-0 m-0">S/ 0.00</h3>
													  <small class="text-blue font-weight-bold">Pago Total</small>
												  </td>
											</tr>
											<!-- <tr>
												  <td width="1"><i class="fa fa-check-circle text-primary"></i></td>
												  <td>Pago Total</td>
												  <td class="text-right">
													  <h3 id="recibo_total" class="text-primary font-weight-bold">S/ 0.00</h3>
												  </td>
											</tr> -->
											<tr>
												  <td width="1"><i class="fa fa-plus-circle text-success"></i></td>
												  <td>Cuotas</td>
												  <td id="total_cuotas_detalle">
													<?php 
														$cant = count($cuotas);
														$str = ($cant <= 9) ? '0'.$cant : $cant; 
														echo $str." cuotas";
													?>
												</td>
												  <td class="text-center">
													  <span id="recibo_cuota" class="text-dark">S/ 0.00</span>
												  </td>
											</tr>
											<tr>
												  <td width="1"><i class="fa fa-plus-circle text-success"></i></td>
												  <td colspan="2">Mora</td>
												  <td class="text-center">
													  <span id="recibo_mora" class="text-dark">S/ 0.00</span>
												  </td>
											</tr>
											<tr>
												<td width="1">
													<i class="fa fa-plus-circle text-primary"></i>
												</td>
												<td colspan="2">Cargos Adicionales</td>
												<td class="text-center">
													<span id="recibo_adicional" class="text-primary" style="font-weight: 600;">S/ 0.00</span>
												</td>
											</tr>
											<tr>
												  <td width="1"><i class="fa fa-minus-circle text-danger"></i></td>
												  <td colspan="2">Descuento</td>
												  <td class="text-center">
													  <span id="recibo_descuento" class="text-danger" style="font-weight: 600;">S/ 0.00</span>
												  </td>
											</tr>									
										  </tbody>
									  </table>
									  <?php if (!empty($mora)): ?>
									  <div class="">
										  <div class="alert alert-error alert-dismissible" role="alert">
											  <strong>Mora Pendiente</strong> 
											  <p class="m-0">
												  <?php echo $mora->dias; ?> dias <b>x</b>
												  S/ <?php echo $mora->penalidad; ?> <b>=</b>
												  S/ <?php echo $mora->monto; ?>
											  </p>
										</div>
									  </div>
									  <div class="d-flex px-3">
										<input type="hidden" id="input_mora" data-id="<?= $mora->id; ?>" data-monto="<?= $mora->monto; ?>">
										<div class="form-group">
											<label for="" class="font-weight-bold">Que hacer con la mora?</label>
											<div class="custom-controls-stacked">
												<label class="custom-control custom-radio custom-control-inline">
													<input type="radio" class="custom-control-input" name="mora_accion" value="C" data-id="<?= $mora->id; ?>" required checked>
													<span class="custom-control-label">Cobrar</span>
												</label>
												<label class="custom-control custom-radio custom-control-inline">
													<input type="radio" class="custom-control-input" name="mora_accion" value="P" data-id="<?= $mora->id; ?>" required>
													<span class="custom-control-label">Posponer</span>
												</label>
											</div>
										</div>
									  </div>
									<?php endif ?>
									<div class="card-body">
										<div class="row">
											<div class="col-xs-12 col-md-6 form-group">
												<label class="form-label">Descuento/Penalidad</label>
												<label class="custom-switch">
													  <input type="checkbox" id="check_custom" class="custom-switch-input">
													  <span class="custom-switch-indicator"></span>
													  <span class="custom-switch-description">
														  Aplicar
													  </span>
												</label>
											</div>
											<div class="col-xs-12 col-md-6 form-group">
												<label class="form-label">Forma de Pago</label>
												<div class="custom-controls-stacked">
													<label class="custom-control custom-radio custom-control-inline">
														<input type="radio" class="custom-control-input" name="forma_pago" value="TIEN" required checked>
														<span class="custom-control-label">Tienda</span>
													</label>
													<label class="custom-control custom-radio custom-control-inline">
														<input type="radio" class="custom-control-input" name="forma_pago" value="DEP" required>
														<span class="custom-control-label">Deposito</span>
													</label>
													<label class="custom-control custom-radio custom-control-inline">
														<input type="radio" class="custom-control-input" name="forma_pago" value="TRANF" required>
														<span class="custom-control-label">Transferencia</span>
													</label>
												</div>
											</div>
										</div>
										<div class="row">
											<div id="section_descuento" class="col-xs-12 col-md-6 col-lg-6 form-group" style="display: none;">
												<label class="form-label">Descuento</label>
												<div class="input-group">
													<span class="input-group-prepend icon-red">
														<span class="input-group-text">- S/</span>
													  </span>
													  <input type="number" class="form-control border-rock" step="any" id="input_descuento" name="descuento">
												</div>
												<small id="descuento_feed" class="text-red"></small>
											</div>
											<div id="section_adicional" class="col-xs-12 col-md-6 col-lg-6 form-group" style="display: none;">
												<label class="form-label">Penalidad/Mantenimiento</label>
												<div class="input-group">
													<span class="input-group-prepend icon-green">
														<span class="input-group-text" style="font-weight: 600;">+ S/</span>
													  </span>
													  <input type="number" class="form-control border-rock" step="any" id="input_adicional" name="adicional">
												</div>
												<small id="adicional_feed" class="text-red"></small>
											</div>
										</div>
										<div class="row" id="forma_operacion" style="display: none;">
											<div class="col-xs-12 col-md-6 form-group">
												<label class="form-label">Nro de Operacion</label>
												<input type="text" class="form-control border-rock" name="comprobante">
												<small id="_noperacion_feed" class="text-danger"></small>
											</div>
											<div class="col-xs-12 col-md-6">
												<div class="form-group">
													<label class="form-label">Banco (Seleccionar o ingresar)</label>
													<select id="banco_list" name="banco" class="form-control border-rock">
														<?php if (!empty($banco)): ?>
															<option value="">- Seleccionar -</option>
															<?php foreach ($banco as $b): ?>
																<option value="<?= $b->ban_codigo; ?>">
																	<?= $b->ban_nombre; ?>
																</option>
															<?php endforeach ?>
														<?php endif ?>
													</select>
													<small id="_banco_feed" class="text-danger"></small>
												</div>
											</div>
										</div>
										<div class="row">
											<div class="col-xs-12 col-md-6 form-group">
												<label class="form-label">Monto Recibido</label>
												<div class="input-group">
													<span class="input-group-prepend" id="basic-addon1">
														<span class="input-group-text">S/</span>
													  </span>
													  <input type="number" class="form-control border-rock" step="any" id="input_recibido" aria-label="Username" aria-describedby="basic-addon1">
												</div>
												<small id="monto_feed" class="text-red"></small>
											</div>
											<div class="col-xs-12 col-md-6 form-group">
												<label class="form-label">Vuelto</label>
												<input id="input_devuelto" name="vuelto" type="number" step="any" class="form-control border-rock" disabled>
											</div>
											<div id="section_comentario" class="col-xs-12 col-md-12 form-group" style="display: none;">
												<div class="form-label">Comentarios</div>
												<textarea id="pago_comentario" cols="10" rows="2" class="form-control border-rock" maxlength="300"></textarea>
											</div>
										</div>
									</div>
	
								</div>
								<div id="CARD_ADVANCED" class="tab-pane fade" role="tabpanel">
									<div class="card-body py-2">
										<button class="btn btn-blue btn-sm" type="button" id="PAYMENT_CHANNEL_NEW">
											<i class="fe fe-plus"></i> 
											Agregar 
										</button>
									</div>
									<div class="card-table">
										<table class="table table-sm">
											<thead>
												<th>Canal</th>
												<th>Nro Operacion</th>
												<th>Monto</th>
												<th>Accion</th>
											</thead>
											<tbody id="TABLE_PAYMENT_CHANNEL">
												<!-- <tr>
													<td>Efectivo</td>
													<td></td>
													<td>S/ 50.00</td>
													<td>
														<button class="btn btn-sm">Remove</button>
													</td>
												</tr>
												<tr>
													<td>YAPE</td>
													<td>OPE00454774</td>
													<td>S/ 23.50</td>
													<td>
														<button class="btn btn-sm">Remove</button>
													</td>
												</tr> -->
												<tr>
													<td colspan="4" class="text-center">
														Sin Datos
													</td>
												</tr>
											</tbody>
										</table>
									</div>
								</div>
							</div>

							<div class="card-footer text-left">
								<?php if (!empty($mora) || !empty($pospuesto) || !empty($cuotas)): ?>
									<button name="btn_envio" class="btn btn-dark btn-lg">
										Procesar Pago
										<i class="fe fe-send"></i>
									</button>
								<?php else: ?>
									<b>No se encontro nada que pagar</b>
								<?php endif ?>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
	
</div>


<!-- Modal -->
<div id="MODAL_PAYMENT_CHANNEL" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="MODAL_PAYMENT_CHANNEL" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<form id="FORM_PAYMENT_CHANNEL" class="modal-content" autocomplete="off">
			<div class="modal-header">
				<h5 class="modal-title" id="MODAL_PAYMENT_CHANNEL">Agregar Canal de Pago</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-md-12 form-group">
						<label for="" class="form-label">Tipo:</label>
						<select id="" name="tipo" class="form-control">
							<option value="">Seleccionar</option>
							<option value="EFECTIVO">EFECTIVO</option>
							<option value="VIRTUAL">VIRTUAL</option>
						</select>
						<small class="text-danger" id="tipo_feedback"></small>
					</div>
					<div class="col-md-12 form-group">
						<label for="" class="form-label">Descripcion (opcional):</label>
						<textarea name="descripcion" class="form-control" maxlength="200"></textarea>
					</div>
					<div class="col-md-12 form-group">
						<label for="" class="form-label">Monto:</label>
						<input name="monto" type="text" class="form-control">
						<small class="text-danger" id="monto_feedback"></small>
					</div>
					<!-- <button type="submit"></button> -->
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
				<button type="submit" class="btn btn-primary">Agregar</button>
			</div>
		</form>
	</div>
</div>


<style>
	/* --blankx: #ecf0f3; */
	.icon-green span {
		background: rgba(46, 178, 101, 9);
		color: #fff;
		font-weight: 700;
		font-size: 1rem;
		text-transform: uppercase;
	}
	.icon-red span {
		background: rgba(219, 22, 74, 9);
		color: #fff;
		font-weight: 700;
		font-size: 1rem;
		text-transform: uppercase;
	}
</style>

<script src="<?= base_url() ?>js_modules/ppago/ppago.regularizar.js"></script>
