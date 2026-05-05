<div class="container">
	<div class="card">
		<div class="card-header">
			<h4 class="card-title">
				<i class="fas fa-calculator text-blue"></i>
				Tarifas
			</h4>
			<div class="card-options">
				<button id="btn_nuevo" class="btn btn-primary">
					Nueva Tarifa
					<i class="fe fe-plus"></i>
				</button>
			</div>
		</div>
		<div class="card-body">
			<div class="table-responsive">
				<table class="table">
					<thead>
						<tr>
							<th>Tipo</th>
							<th>Monto Minimo</th>
							<th>Monto Maximo</th>
							<th>Interes</th>
							<th>Mora</th>
							<th>Accion</th>
						</tr>
					</thead>
					<tbody id="tdata_tarifa">
						<tr class="text-center">
							<td colspan="6">No se encontraron resultados.</td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>


<div id="md_tarifa_nuevo" class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      	<div class="modal-header">
	        <h4 class="modal-title">
	        	<i class="fe fe-file-plus text-blue"></i>
	        	Nueva Tarifa
	        </h4>
	        <div class="card-options">
		        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
		        	<span aria-hidden="true"></span>
		        </button>
	        </div>
      	</div>
  		<form id="form_tarifa_nueva" autocomplete="off" autocorrect="off">
      		<div class="modal-body">
      			<div id="md_nuevo_notifica"></div>
				<div class="row">
					<div class="col-xs-12 col-md-10">
						<div class="form-group">
							<label for="" class="form-label">Tipo de tarifa</label>
							<div class="custom-controls-stacked">
	                        	<label class="custom-control custom-radio custom-control-inline">
	                            	<input type="radio" class="custom-control-input" name="tipo" value="F" checked>
	                            	<span class="custom-control-label">Fija</span>
	                          	</label>
	                          	<label class="custom-control custom-radio custom-control-inline">
	                            	<input type="radio" class="custom-control-input" name="tipo" value="P">
	                            	<span class="custom-control-label">Porcentual</span>
	                          	</label>
	                        </div>
	                        <small id="tipo_feed" class="text-red"></small>
						</div>
					</div>
					<div class="col-xs-12 col-md-6">
						<div class="form-group">
							<label for="" class="form-label">Monto minimo igual (<=)</label>
							<input type="number" step="any" class="form-control border-rock" name="minimo" min="1" required="">
							<small id="minimo_feed" class="text-red"></small>
						</div>
					</div>
					<div class="col-xs-12 col-md-6">
						<div class="form-group">
							<label for="" class="form-label">Monto maximo igual (>=)</label>
							<input type="number" step="any" class="form-control border-rock" name="maximo" min="1" required="">
							<small id="maximo_feed" class="text-red"></small>
						</div>
					</div>
					<div class="col-xs-12 col-md-6">
						<div class="form-group">
							<label for="" class="form-label">Interes</label>
							<input type="number" step="any" class="form-control border-rock" name="interes" min="0" required="">
							<small id="interes_feed" class="text-red"></small>
						</div>
					</div>
					<div class="col-xs-12 col-md-6">
						<div class="form-group">
							<label for="" class="form-label">Mora</label>
							<input type="number" step="any" class="form-control border-rock" name="mora" min="0" required="">
							<small id="mora_feed" class="text-red"></small>
						</div>
					</div>
				</div>
      		</div>
      		<div class="modal-footer text-right">
  				<button class="btn btn-secondary" data-dismiss="modal">
  					Cancelar
  				</button>
  				<button type="submit" class="btn btn-blue">
  					Registrar
  					<i class="fe fe-save"></i>
  				</button>
      		</div>
		</form>
    </div>
  </div>
</div>

<div id="md_tarifa_edita" class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      	<div class="modal-header">
	        <h4 class="modal-title">
	        	<i class="fe fe-edit-2 text-blue"></i>
	        	Editar Tarifa
	        </h4>
	        <div class="card-options">
		        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
		        	<span aria-hidden="true"></span>
		        </button>
	        </div>
      	</div>
  		<form id="form_tarifa_editar" autocomplete="off" autocorrect="off">
  			<input type="hidden" name="id">
      		<div class="modal-body">
      			<div id="md_edita_notifica"></div>
				<div class="row">
					<div class="col-xs-12 col-md-10">
						<div class="form-group">
							<label for="" class="form-label">Tipo de tarifa</label>
							<div class="custom-controls-stacked">
	                        	<label class="custom-control custom-radio custom-control-inline">
	                            	<input type="radio" class="custom-control-input" name="tipo" value="F" checked>
	                            	<span class="custom-control-label">Fija</span>
	                          	</label>
	                          	<label class="custom-control custom-radio custom-control-inline">
	                            	<input type="radio" class="custom-control-input" name="tipo" value="P">
	                            	<span class="custom-control-label">Porcentual</span>
	                          	</label>
	                        </div>
	                        <small id="tipo_feed2" class="text-red"></small>
						</div>
					</div>
					<div class="col-xs-12 col-md-6">
						<div class="form-group">
							<label for="" class="form-label">Monto minimo igual (<=)</label>
							<input type="number" step="any" class="form-control border-rock" name="minimo" min="1">
							<small id="minimo_feed2" class="text-red"></small>
						</div>
					</div>
					<div class="col-xs-12 col-md-6">
						<div class="form-group">
							<label for="" class="form-label">Monto maximo igual (>=)</label>
							<input type="number" step="any" class="form-control border-rock" name="maximo" min="1">
							<small id="maximo_feed2" class="text-red"></small>
						</div>
					</div>
					<div class="col-xs-12 col-md-6">
						<div class="form-group">
							<label for="" class="form-label">Interes</label>
							<input type="number" step="any" class="form-control border-rock" name="interes" min="0">
							<small id="interes_feed2" class="text-red"></small>
						</div>
					</div>
					<div class="col-xs-12 col-md-6">
						<div class="form-group">
							<label for="" class="form-label">Mora</label>
							<input type="number" step="any" class="form-control border-rock" name="mora" min="0">
							<small id="mora_feed2" class="text-red"></small>
						</div>
					</div>
				</div>
      		</div>
      		<div class="modal-footer text-right">
  				<button class="btn btn-secondary" data-dismiss="modal">
  					Cancelar
  				</button>
  				<button type="submit" class="btn btn-indigo">
  					Guardar cambios
  					<i class="fe fe-save"></i>
  				</button>
      		</div>
		</form>
    </div>
  </div>
</div>

<script src="<?= base_url() ?>js_modules/tarifa/tarifa.lista.v1.js"></script>