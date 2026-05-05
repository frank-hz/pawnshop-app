<div class="px-1">
	<div class="card">
		<div class="card-header">
			<h4 class="card-title font-weight-bold">
				<a href="<?= base_url()?>Credito/detalle/<?= $credito->id; ?>">
					<span class="text-blue">#<?php echo $credito->codigo; ?></span>
				</a>
				| RECUPERACION
			</h4>
		</div>
		<div class="card-body p-2">
			<div class="row">
				<div class="col-xs-12 col-md-7">
					<div class="card">
						<div class="card-header">
							<p class="card-title">
								<i class="fe fe-info text-blue"></i>&nbsp;
								Pendientes
							</p>
							<div class="card-options">
								<label class="custom-switch">
		                          	<input type="checkbox" id="check_darkmode" class="custom-switch-input">
		                          	<span class="custom-switch-indicator"></span>
		                          	<span class="custom-switch-description">
		                          		Modo Oscuro
		                          	</span>
		                        </label>
							</div>
						</div>
						<div class="table-responsive" style="max-height: 600px">
							<table id="tabla_pendiente" class="table table-sm table-bordered">
								<thead>
									<tr class="text-center">
										<th>Estado</th>
										<th>Cuota</th>
										<th>Vencimiento</th>
										<th>Monto</th>
									</tr>
								</thead>
								<tbody>
									<?php if (!empty($cuotas)): ?>
										<?php foreach ($cuotas as $c): ?>
											<tr class="text-center">
												<td><?php echo $c->estado; ?></td>
												<td>
													Cuota <?php echo $c->orden; ?>
													<input type="hidden" name="pendiente_item" data-id="<?= $c->id; ?>" data-tipo="CU" data-orden="<?= $c->orden; ?>" data-cobro="<?= $c->tipo_cobro; ?>" value="<?= $c->monto; ?>">
												</td>
												<td><?php echo $c->vencimiento; ?></td>
												<td>S/ <?php echo $c->monto; ?></td>
											</tr>
										<?php endforeach ?>
									<?php else: ?>
										<tr class="text-center">
											<td colspan="5">No hay cuotas pendientes</td>
										</tr>
									<?php endif ?>

									<?php if (!empty($moras)): ?>
										<?php foreach ($moras as $m): ?>
											<tr >
												<td colspan="3" class="text-center">
													<?php echo $m->descripcion; ?>
													<input type="hidden" data-tipo="MO" name="pendiente_item" data-id="<?= $m->id; ?>" data-atraso="<?= $m->dias; ?>" value="<?= $m->monto; ?>">
												</td>
												<td class="text-center">S/ <?php echo $m->monto; ?></td>
											</tr>
										<?php endforeach ?>
									<?php endif ?>
									<?php if (!empty($capital)): ?>
										<tr >
											<td colspan="3" class="text-center">
												CAPITAL
												<input type="hidden" data-tipo="CAP" name="pendiente_item" data-id="<?= $capital->id; ?>" 
												value="<?= $capital->monto; ?>">
											</td>
											<td class="text-center">S/ <?php echo $capital->monto; ?></td>
										</tr>
									<?php endif ?>
								</tbody>
							</table>
						</div>
					</div>
				</div>
				<div class="col-xs-12 col-md-5">
					<div class="card">
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
		                      		<td class="font-weight-bold">Pago Total</td>
		                      		<td></td>
		                      		<td class="text-left">
		                      			<h3 id="recibo_total" class="text-primary font-weight-bold">S/ 0.00</h3>
		                      		</td>
		                    	</tr> -->
								<tr>
									<td class="px-3 py-2"><b>CLIENTE:</b></td>
									<td colspan="3" class="py-2">
										<?= $credito->cliente_nombre; ?>
									</td>
								</tr>
								<tr>
									<td class="px-3 py-2"><b>PRENDA:</b></td>
									<td colspan="3" class="py-2">
										<?= $prenda->descripcion ?? "Sin Datos"; ?>
									</td>
								</tr>
		                    	<tr>
		                      		<td width="1"><i class="fa fa-plus-circle text-success"></i></td>
		                      		<td>Cuotas</td>
		                      		<td class="text-center">
		                      			<span id="recibo_cuota_sub"></span>
		                      		</td>
		                      		<td class="text-left">
		                      			<span id="recibo_cuota" class="text-dark">S/ 0.00</span>
		                      		</td>
		                    	</tr>
		                    	<tr>
		                      		<td width="1"><i class="fa fa-plus-circle text-success"></i></td>
		                      		<td>Prorrateo</td>
		                      		<td class="text-center">
		                      			<span id="recibo_prorrateo_sub"></span>
		                      		</td>
		                      		<td class="text-left">
		                      			<span id="recibo_prorrateo" class="text-dark">S/ 0.00</span>
		                      		</td>
		                    	</tr>
		                    	<tr>
		                      		<td width="1"><i class="fa fa-plus-circle text-success"></i></td>
		                      		<td>Capital</td>
		                      		<td>
		                      			<span id="recibo_capital_sub"></span>
		                      		</td>
		                      		<td class="text-left">
		                      			<span id="recibo_capital" class="text-dark">S/ 0.00</span>
		                      		</td>
		                    	</tr>
		                    	<tr>
		                      		<td width="1"><i class="fa fa-plus-circle text-success"></i></td>
		                      		<td>Mora</td>
		                      		<td class="text-center">
		                      			<span id="recibo_mora_sub"></span>
		                      		</td>
		                      		<td class="text-left">
		                      			<span id="recibo_mora" class="text-dark">S/ 0.00</span>
		                      		</td>
		                    	</tr>
								<tr>
		                      		<td width="1"><i class="fa fa-plus-circle text-primary"></i></td>
		                      		<td>Cargos Adicionales</td>
		                      		<td></td>
		                      		<td class="text-left">
		                      			<span id="recibo_adicional" class="text-primary" style="font-weight: 600;">S/ 0.00</span>
		                      		</td>
		                    	</tr>
		                    	<tr>
		                      		<td width="1"><i class="fa fa-minus-circle text-danger"></i></td>
		                      		<td>Descuento</td>
		                      		<td></td>
		                      		<td class="text-left">
		                      			<span id="recibo_descuento" class="text-danger" style="font-weight: 600;">S/ 0.00</span>
		                      		</td>
		                    	</tr>								
		                  	</tbody>
		              	</table>
	              		<form id="form_pago_registro" autocomplete="off">
	              			<input type="hidden" name="credito" value="<?= $credito->id; ?>">
	              			<input type="hidden" name="prenda" value="<?= $prenda->id ?? 0; ?>">
		              		<div class="card-body pb-1">
			              		<div class="row">
									<div class="col-xs-12 col-md-6 form-group">
										<label class="form-label">Descuento/Penalidad</label>
										<label class="custom-switch">
				                          	<input type="checkbox" id="check_descuento" class="custom-switch-input">
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
						                        <span class="custom-control-label">TIENDA</span>
						                    </label>
						                    <label class="custom-control custom-radio custom-control-inline">
						                        <input type="radio" class="custom-control-input" name="forma_pago" value="TRANF" required>
						                        <span class="custom-control-label">VIRTUAL</span>
						                    </label>
											<!-- <label class="custom-control custom-radio custom-control-inline">
						                        <input type="radio" class="custom-control-input" name="forma_pago" value="DEP" required>
						                        <span class="custom-control-label">Deposito</span>
						                    </label> -->
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
				                          	<input type="number" class="form-control border-rock" step="any" id="input_recibido">
				                        </div>
										<small id="monto_feed" class="text-red"></small>
									</div>
									<div class="col-xs-12 col-md-6 form-group">
										<label class="form-label">Vuelto</label>
										<input id="input_devuelto" name="vuelto" type="number" step="any" class="form-control border-rock" disabled>
									</div>
									<div id="section_comentario" class="col-xs-12 col-md-12 form-group">
										<div class="form-label">Comentarios</div>
										<textarea id="pago_comentario" cols="10" rows="2" class="form-control border-rock" maxlength="300"></textarea>
									</div>
								</div>
		              		</div>
		              		<div class="card-footer text-left">
								<?php if (!empty($moras) || !empty($capital) || !empty($cuotas)): ?>
									<button name="btn_envio" class="btn btn-blue btn-lg px-6">
										<i class="fe fe-zap"></i> 
										Procesar Pago
									</button>
								<?php else: ?>
									<b>No se encontro nada que pagar</b>
								<?php endif ?>
							</div>
	              		</form>
					</div>
				</div>
			</div>	
		</div>
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

<script src="<?= base_url() ?>js_modules/ppago/ppago.recuperar.js?v=23012025"></script>
