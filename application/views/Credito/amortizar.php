<?php ini_set('display_errors', 0); ?>
<div class="container">
	<form id="form_pago" autocomplete="off">
		<div class="card">
			<div class="card-header">
				<h4 class="card-title font-weight-bold">
					<a href="<?= base_url()?>Credito/detalle/<?= $credito->id; ?>">
						<span class="text-blue">#<?php echo $credito->codigo; ?></span>
					</a>
					| AMORTIZACION
				</h4>
			</div>
			<div class="card-body">
				<div class="row">
					<div class="col-xs-12 col-md-4">
						<div class="card">
		                  	<div class="card-body p-3 text-center">
		                    	<div class="h2 m-0 text-primary">
		                    		S/ <?php echo $credito->monto; ?>
		                    	</div>
		                    	<div class="text-muted mb-4">Monto Prestado</div>
		                	</div>
		                </div>
					</div>
					<div class="col-xs-12 col-md-4">
						<div class="card">
		                  	<div class="card-body p-3 text-center">
		                    	<div class="h2 m-0 text-success">
		                    		S/ <?php echo $credito->amortizacion; ?>
		                    	</div>
		                    	<div class="text-muted mb-4">Amortizacion Actual</div>
		                	</div>
		                </div>
					</div>
					<div class="col-xs-12 col-md-4">
						<div class="card">
		                  	<div class="card-body p-3 text-center">
		                    	<div class="h2 m-0 text-error">
		                    		S/ <?php echo $credito->restante; ?>
		                    	</div>
		                    	<div class="text-muted mb-4">Restante por pagar</div>
		                	</div>
		                </div>
					</div>
				</div>
				<?php if ($credito->restante && $credito->restante > 0): ?>
				<input type="hidden" id="credito" value="<?= $credito->id; ?>" data-amortizacion="<?= $credito->amortizacion; ?>" data-restante="<?= $credito->restante; ?>" data-monto="<?= $credito->monto; ?>">
				<div class="row">
					<!-- <div class="col-xs-12 col">
						<div class="form-group">
							<label class="form-label">Aplicar Descuento</label>
							<label class="custom-switch">
	                          	<input type="checkbox" id="check_descuento" class="custom-switch-input">
	                          	<span class="custom-switch-indicator"></span>
	                          	<span class="custom-switch-description">
	                          		Aplicar
	                          	</span>
	                        </label>
						</div>
					</div> -->
					<div class="col-xs-12 col">
						<div class="form-group">
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
							</div>
						</div>
					</div>
				</div>
				<div class="row">
					<div id="sect_descuento" class="col-xs-12 col-md-6" style="display: none;">
						<div class="form-group">
							<label class="form-label">Descuento</label>
							<div class="input-group">
	                        	<span class="input-group-prepend" id="basic-addon1">
	                            	<span class="input-group-text">S/</span>
	                          	</span>
	                          	<input type="number" class="form-control border-rock" step="any" name="descuento">
	                        </div>
							<small id="descuento_feed" class="text-red"></small>
						</div>
					</div>
				</div>
				<div class="row" id="sect_deposito" style="display: none;">
					<div class="col-xs-12 col-md-6">
						<div class="form-group">
							<label for="">Nro de Operacion</label>
							<input type="text" class="form-control border-rock" name="comprobante">
						</div>
					</div>
					<div class="col-xs-12 col-md-6">
						<div class="form-group">
							<label class="form-label">Banco:</label>
							<select name="banco" class="form-control border-rock">
								<option value="">-Seleccionar-</option>
								<?php if (!empty($banco)): ?>
									<?php foreach ($banco as $b): ?>
										<option value="<?= $b->ban_codigo; ?>">
											<?= $b->ban_nombre; ?>
										</option>
									<?php endforeach ?>
								<?php endif ?>
							</select>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-xs-12 col-md-5 col-lg-4">
						<div class="form-group">
							<label class="font-weight-bold text-blue">Monto Recibido</label>
							<div class="input-group">
	                        	<span class="input-group-prepend" id="basic-addon1">
	                            	<span class="input-group-text">S/</span>
	                          	</span>
	                          	<input type="number" class="form-control border-rock" step="any" id="input_recibido" name="monto">
	                        </div>
							<small id="monto_feed" class="text-red"></small>
						</div>
					</div>
					<!-- <div class="col-xs-12 col-md-5 col-lg-4">
						<div class="form-group">
							<label class="font-weight-bold">Vuelto</label>
							<input id="input_vuelto" name="vuelto" class="form-control border-rock" disabled>
						</div>
					</div> -->
				</div>
				<button id="btn_envio" type="submit" class="btn btn-primary btn-lg">
					Procesar pago 
					<i class="fe fe-database"></i>
				</button>
				<?php else: ?>
				<div class="h3 text-muted text-center">
					Amortizacion no disponible
				</div>
				<?php endif ?>
			</div>
		</div>
	</form>
</div>

<script src="<?= base_url() ?>js_modules/credito/credito.amortizar.js?v=2"></script>