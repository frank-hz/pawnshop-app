<div class="container">
	<form id="form_credito_editar" autocomplete="off">
		<div class="card">
			<div class="card-header">
				<div class="card-title text-blue">
					<i class="fe fe-code"></i>
					Editar Credito - 
					<a href="<?= base_url() ?>Credito/detalle/<?= $credito->id; ?>">
						#<?php echo $credito->codigo; ?>
					</a>
				</div>
			</div>
			<div class="card-body py-2">
				<div class="alert alert-icon alert-secondary" role="alert">
	                <i class="fe fe-info mr-2" aria-hidden="true"></i>
	                Los cambios seran aplicados a partir de la siguiente cuota generada. 
	            </div><br>
				<div class="row">
					<div class="col-xs-12 col-md-4">
						<div class="form-group">
							<div class="form-label">Prorroga General</div>
							<!-- <input type="text" class="form-control text-center border-rock" value="<?= $credito->prorroga; ?>"> -->
							<div class="input-group">
	                          	<input type="text" class="form-control border-rock text-center" value="<?= $credito->prorroga; ?>">
	                          	<span class="input-group-append" id="basic-addon2">
	                            	<span class="input-group-text border-rock"> dias</span>
	                          	</span>
	                        </div>
						</div>
					</div>
					<div class="col-xs-12 col-md-4">
						<div class="form-group">
							<div class="form-label">Tasa</div>
							<input type="text" class="form-control text-center border-rock" value="<?= $credito->tasa; ?>">
						</div>
					</div>
					<div class="col-xs-12 col-md-4">
						<div class="form-group">
							<div class="form-label">Codigo</div>
							<input type="text" class="form-control border-rock" value="<?= $credito->codigo; ?>">
						</div>
					</div>	
				</div>
				<div class="row">
					<div class="col-xs-12 col-md-4">
						<div class="form-group">
							<div class="form-label">Pago Semanal</div>
							<div class="input-group">
	                          	<span class="input-group-prepend" id="basic-addon2">
	                            	<span class="input-group-text border-rock"> S/</span>
	                          	</span>
	                          	<input type="text" class="form-control border-rock text-center" value="<?= $credito->pago; ?>">
	                        </div>
						</div>
					</div>
					<div class="col-xs-12 col-md-4">
						<div class="form-group">
							<div class="form-label">Aplicacion de penalidad</div>
							<?php if ($credito->aplica_penalidad === 'I'): ?>
								<div class="custom-controls-stacked">
		                        	<label class="custom-control custom-radio custom-control-inline">
		                            	<input type="radio" class="custom-control-input" name="aplica_penalidad" value="A">
		                            	<span class="custom-control-label">Activo</span>
		                          	</label>
		                          	<label class="custom-control custom-radio custom-control-inline">
		                            	<input type="radio" class="custom-control-input" name="aplica_penalidad" value="I" checked="">
		                            	<span class="custom-control-label">Inactivo</span>
		                          	</label>
		                        </div>
							<?php else: ?>
								<div class="custom-controls-stacked">
		                        	<label class="custom-control custom-radio custom-control-inline">
		                            	<input type="radio" class="custom-control-input" name="aplica_penalidad" value="A" checked="">
		                            	<span class="custom-control-label">Activo</span>
		                          	</label>
		                          	<label class="custom-control custom-radio custom-control-inline">
		                            	<input type="radio" class="custom-control-input" name="aplica_penalidad" value="I">
		                            	<span class="custom-control-label">Inactivo</span>
		                          	</label>
		                        </div>
							<?php endif ?>
						</div>
					</div>	
				</div>
			</div>
			<div class="card-body">
				<div class="row">
					<div class="col-xs-12 col-md-4">
						<div class="form-group">
							<div class="form-label">Cartera de Adelanto</div>
							<div class="input-group">
	                          	<span class="input-group-prepend" id="basic-addon2">
	                            	<span class="input-group-text border-rock"> S/</span>
	                          	</span>
	                          	<input type="text" class="form-control border-rock text-center" value="<?= $credito->adelanto; ?>">
	                        </div>
						</div>
					</div>
					<div class="col-xs-12 col-md-4">
						<div class="form-group">
							<div class="form-label">Cartera de Amortizacion</div>
							<div class="input-group">
	                          	<span class="input-group-prepend" id="basic-addon2">
	                            	<span class="input-group-text border-rock"> S/</span>
	                          	</span>
	                          	<input type="text" class="form-control border-rock text-center" value="<?= $credito->amortizacion; ?>">
	                        </div>
						</div>
					</div>	
				</div>
			</div>
			<div class="card-footer">
				<button class="btn btn-primary btn-lg">
					Guardar cambios
					<i class="fe fe-save"></i>
				</button>
			</div>
		</div>
	</form>
</div>