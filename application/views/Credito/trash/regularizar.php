<div class="px-1">
	<div class="card">
		<div class="card-header">
			<h4 class="card-title font-weight-bold">
				<a href="<?= base_url()?>Credito/detalle/<?= $credito->id; ?>">
					<span class="text-blue">#<?php echo $credito->codigo; ?></span>
				</a>
				| REGULARIZACION
			</h4>
		</div>
		<div class="card-body">
			<div class="row">
				<div class="col-xs-12 col-md-6">
					<div class="card">
						<div class="card-header">
							<i class="fe fe-info text-blue"></i>&nbsp;
							Cuotas Pendientes
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
										<th>Cuota</th>
										<th>Vencimiento</th>
										<th>Monto</th>
										<th>Estado</th>
										<th class="font-weight-bold text-center text-success">PAGAR</th>
									</tr>
								</thead>
								<tbody>
									<?php if (!empty($cuotas)): ?>
										<?php foreach ($cuotas as $c): ?>
											<tr class="text-center">
												<td>
													Cuota <?php echo $c->orden; ?>
												</td>
												<td><?php echo $c->vencimiento; ?></td>
												<td>S/ <?php echo $c->monto; ?></td>
												<td><?php echo $c->estado; ?></td>
												<td class="text-center">
													<input type="checkbox" name="cuota_item" data-id="<?= $c->id; ?>" data-monto="<?= $c->monto; ?>" data-orden="<?= $c->orden; ?>">
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
							<div class="card-header">
								<h4 class="card-title">
									<i class="fe fe-credit-card text-blue"></i>&nbsp;
									Pago
								</h4>
							</div>
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
			                      		<td></td>
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
			                      		<td width="1"><i class="fa fa-minus-circle text-danger"></i></td>
			                      		<td colspan="2">Descuento</td>
			                      		<td class="text-center">
			                      			<span id="recibo_descuento" class="text-danger">S/ 0.00</span>
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
										<label class="form-label">Descuento</label>
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
				                        	<span class="input-group-prepend" id="basic-addon1">
				                            	<span class="input-group-text">S/</span>
				                          	</span>
				                          	<input type="number" class="form-control border-rock" step="any" id="input_descuento" name="descuento">
				                        </div>
										<small id="descuento_feed" class="text-red"></small>
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
								</div>
							</div>
							<div class="card-footer text-left">
								<?php if (!empty($mora) || !empty($pospuesto) || !empty($cuotas)): ?>
									<button name="btn_envio" class="btn btn-blue btn-lg">
										Procesar Pago
										<i class="fe fe-database"></i>
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


<script src="<?= base_url() ?>js_modules/credito/credito.regularizar.v1.js"></script>