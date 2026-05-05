<?php ini_set('display_errors', 0); ?>
<div class="container">
	<form id="form_adelanto" autocomplete="off">
		<input type="hidden" name="credito" value="<?= $credito->id; ?>">
		<input type="hidden" name="pago" value="<?= $credito->pago; ?>">

		<div class="card">
			<div class="card-header">
				<h4 class="card-title font-weight-bold">
					<a href="<?= base_url()?>Credito/detalle/<?= $credito->id; ?>">
						<span class="text-blue">#<?php echo $credito->codigo; ?></span>
					</a>
					| ADELANTO
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
		                    	<div class="h2 m-0 text-primary">
		                    		S/ <?php echo $credito->pago; ?>
		                    	</div>
		                    	<div class="text-muted mb-4">Pago Semanal</div>
		                	</div>
		                </div>
					</div>
				</div>
			</div>
			<!-- <div class="card-body">
				<div class="row">
					<div class="col-xs-12 col-md-6 col-lg-7">
                      	<div class="form-group">
	                        <label class="form-label">Tipo de adelanto</label>
	                        <div class="selectgroup w-100">
	                          <label class="selectgroup-item">
	                            <input type="radio" name="tipo_adelanto" value="libre" class="selectgroup-input" checked>
	                            <span class="selectgroup-button selectgroup-button-icon">
	                            	<i class="fe fe-message-square"></i>
	                            	Adelanto Libre
	                            </span>
	                          </label>
	                          <label class="selectgroup-item">
	                            <input type="radio" name="tipo_adelanto" value="cuota" class="selectgroup-input">
	                            <span class="selectgroup-button selectgroup-button-icon">
	                            	<i class="fe fe-calendar"></i>
	                            	Por cuotas
	                            </span>
	                          </label>
	                        </div>
	                      </div>
					</div>
					<div id="adelanto_section_cuota" class="col-xs-12 col-md-6 col-lg-5" style="display: none;">
						<div class="form-group">
							<div class="form-label">Cantidad de cuotas</div>
							<input id="cuota_cantidad" type="number" step="1" class="form-control border-rock" placeholder="ingrese una cantidad valida">
						</div>
					</div>
				</div>
			</div> -->
			<div class="card-body">
				<div class="row">
					<div class="col-xs-12 col-md-5 col-lg-4">
						<div class="form-group">
							<div class="form-label">Cantidad de cuotas</div>
							<input id="cuota_cantidad" name="cantidad_adelanto" type="number" step="1" min="1" class="form-control form-control-lg border-rock text-center" placeholder="ingrese una cantidad valida">
						</div>
					</div>
					<div class="col-xs-12 col-md-5 col-lg-4">
						<div class="form-group">
							<div class="form-label">Monto</div>
	                        <div class="input-group">
	                        	<span class="input-group-prepend">
	                            	<span class="input-group-text border-rock">S/</span>
	                          	</span>
	                          	<input type="number" step="any" class="form-control form-control-lg text-center border-rock text-right" name="monto" min="1" required disabled>
	                        </div>
	                        <small id="monto_feed" class="text-red"></small>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-xs-12 col-md-4">
						<div class="form-group">
							<label class="form-label">Forma de Pago</label>
							<div class="custom-controls-stacked">
								<label class="custom-control custom-radio custom-control-inline">
			                        <input type="radio" class="custom-control-input" name="forma" value="TIEN" required checked>
			                        <span class="custom-control-label">Tienda</span>
			                    </label>
			                    <label class="custom-control custom-radio custom-control-inline">
			                        <input type="radio" class="custom-control-input" name="forma" value="DEP" required>
			                        <span class="custom-control-label">Deposito</span>
			                    </label>
			                    <label class="custom-control custom-radio custom-control-inline">
			                        <input type="radio" class="custom-control-input" name="forma" value="TRANF" required>
			                        <span class="custom-control-label">Transferencia</span>
			                    </label>
							</div>
						</div>
					</div>
					<div id="adelanto_nro_operacion" class="col-xs-12 col-md-4" style="display: none;">
						<div class="form-group">
							<label class="form-label">Nro de Operacion</label>
							<input type="text" class="form-control border-rock" name="comprobante">
						</div>
					</div>
					<div id="adelanto_banco" class="col-xs-12 col-md-4" style="display: none;">
						<div class="form-group">
							<label class="form-label">Banco (Seleccionar o ingresar)</label>
							<input list="banco_list" name="banco" class="form-control border-rock text-uppercase">
							<datalist id="banco_list">
								<?php if (!empty($banco)): ?>
									<?php foreach ($banco as $b): ?>
										<option value="<?= $b->ban_nombre; ?>">
									<?php endforeach ?>
								<?php endif ?>
							</datalist>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-xs-12 col-md-5 col-lg-4">
						<div class="form-group">
							<div class="form-label">Monto Recibido</div>
							<div class="input-group">
	                        	<span class="input-group-prepend">
	                            	<span class="input-group-text border-rock">S/</span>
	                          	</span>
	                          	<input type="number" step="any" class="form-control form-control-lg text-center border-rock text-right" name="recibido" min="1" required>
	                        </div>
	                        <small id="recibido_feed" class="text-red"></small>
						</div>
					</div>
					<div class="col-xs-12 col-md-5 col-lg-4">
						<div class="form-group">
							<div class="form-label">Vuelto</div>
							<div class="input-group">
	                        	<span class="input-group-prepend">
	                            	<span class="input-group-text border-rock">S/</span>
	                          	</span>
	                          	<input type="number" step="any" class="form-control form-control-lg text-center border-rock text-right" name="vuelto" disabled>
	                        </div>
	                        <small id="vuelto_feed" class="text-red"></small>
						</div>
					</div>
				</div>
			</div>
			<div class="card-footer">
				<button class="btn btn-blue btn-lg">
					Procesar adelanto
					<i class="fe fe-save"></i>
				</button>
			</div>
		</div>
	</form>
</div>

<script src="<?= base_url() ?>js_modules/credito/credito.adelanto.js"></script>