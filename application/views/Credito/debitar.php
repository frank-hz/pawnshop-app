<?php ini_set('display_errors', 0); ?>
<div class="container">
	<form id="form_pago" autocomplete="off">
		<div class="card">
			<div class="card-header">
				<h4 class="card-title font-weight-bold">
					<a href="<?= base_url()?>Credito/detalle/<?= $credito->id; ?>">
						<span class="text-blue">#<?php echo $credito->codigo; ?></span>
					</a>
					| DEBITAR
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
		                    	<div class="text-muted">
		                    		Amortizacion Actual
		                    	</div>
	                    		<b class="text-blue">(Debitacion Maxima)</b>
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
				<?php if ($credito->amortizacion && $credito->amortizacion > 0): ?>
				<input type="hidden" id="credito" value="<?= $credito->id; ?>" data-amortizacion="<?= $credito->amortizacion; ?>" data-restante="<?= $credito->restante; ?>" data-monto="<?= $credito->monto; ?>">
				<div class="row">
					<div class="col-xs-12 col-md-5 col-lg-4">
						<div class="form-group">
							<label class="form-label">Monto debitado</label>
							<div class="input-group">
	                        	<span class="input-group-prepend" id="basic-addon1">
	                            	<span class="input-group-text">S/</span>
	                          	</span>
	                          	<input type="number" class="form-control border-rock" step="any" id="input_recibido">
	                        </div>
							<small id="monto_feed" class="text-red"></small>
						</div>
					</div>
					<div class="col-xs-12 col-md-5 col-lg-4">
						<div class="form-group">
							<label class="form-label">Amortizacion restante</label>
							<input id="input_restante" class="form-control border-rock" disabled>
						</div>
					</div>
					<div class="col-xs-12 col-md-12 mb-2">
						<div class="form-label">Descripcion (motivo)</div>
						<textarea name="descripcion" class="form-control text-uppercase border-rock" cols="10" rows="3"></textarea>
					</div>
				</div>
				<button id="btn_envio" type="submit" class="btn btn-primary btn-lg" disabled>
					Aplicar debitacion 
					<i class="fe fe-database"></i>
				</button>
				<?php else: ?>
				<div class="h3 text-muted text-center">
					Debitacion no disponible
				</div>
				<?php endif ?>
			</div>
		</div>
	</form>
</div>

<script src="<?= base_url() ?>js_modules/credito/credito.debitar.js"></script>