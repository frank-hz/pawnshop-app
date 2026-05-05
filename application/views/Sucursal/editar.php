<div class="container">
	<form id="frm_editar" autocorrect="off" autocomplete="off" data-id="<?= $sucursal->id; ?>">
		<div class="card">
			<div class="card-header">
				<h4 class="card-title">
					<i class="fe fe-edit-2 text-indigo"></i>
					Editar Sucursal
				</h4>
			</div>
			<div class="card-body">
				<div class="row">
					<div class="col-xs-12 col-md-12">
						<div class="form-group">
							<label for="" class="form-label">
								Nombre
								<span class="text-red">*</span>
							</label>
							<input type="text" class="form-control border-rock text-uppercase" name="nombre" value="<?= $sucursal->nombre; ?>">
							<span id="descripcion_feed" class="feed-red"></span>
						</div>
					</div>
					<div class="col-xs-12 col-md-6 col-lg-4">
						<div class="form-group">
							<label for="" class="form-label">
								Ciudad
								<span class="text-red">*</span>
							</label>
							<div class="input-group">
	                            <input type="text" class="form-control text-uppercase border-rock" name="ciudad" value="<?= $sucursal->ciudad; ?>" readonly>
	                            <span class="input-group-append">
	                            	<button id="btn_ciudad_modal" class="btn btn-indigo" type="button">
	                            		<i class="fe fe-search"></i>
	                            		Buscar
	                            	</button>
	                            </span>
	                        </div>
							<input type="hidden" class="form-control border-rock" name="ubigeo" value="<?= $sucursal->ubigeo; ?>">
							<span id="ubigeo_feed" class="feed-red"></span>
						</div>
					</div>
					<div class="col-xs-12 col-md-6 col-lg-4">
						<div class="form-group">
							<label for="" class="form-label">
								Direccion
								<span class="text-red">*</span>
							</label>
							<input type="text" step="any" class="form-control border-rock" name="direccion" value="<?= $sucursal->direccion; ?>" required>
							<span id="direccion_feed" class="feed-red"></span>
						</div>
					</div>
					<div class="col-xs-12 col-md-6 col-lg-4">
						<div class="form-group">
							<label for="" class="form-label">Telefono</label>
							<input type="text" class="form-control border-rock" name="telefono" value="<?= $sucursal->telefono; ?>">
		                    <span id="telefono_feed" class="feed-red"></span>
						</div>
					</div>
				</div>				
			</div>
			<div class="card-body">
				<div class="row">
					<div class="col-xs-12 col-md-4">
						<div class="form-label">Codigo</div>
						<input type="text" class="form-control text-uppercase border-rock" name="serie" value="<?= $sucursal->serie; ?>">
					</div>
					<div class="col-xs-12 col-md-4">
						<div class="form-label">Codigo de pago</div>
						<input type="text" class="form-control text-uppercase border-rock" name="serie_pago" value="<?= $sucursal->pago_serie; ?>">
					</div>
				</div>
			</div>
			<div class="card-body">
				<h5>Forma de pago</h5>
				<div class="row">
					<div class="col-xs-12 col-md-4">
						<div class="form-group">
							<label class="form-label">
								Tipo de cuenta
								<span class="text-red">*</span>
							</label>
							<input type="text" name="cuenta_tipo" class="form-control border-rock" value="<?= $sucursal->cuenta_tipo; ?>" required>
							<span id="cuenta_tipo_feed" class="feed-red"></span>
						</div>
					</div>
					<div class="col-xs-12 col-md-4">
						<div class="form-group">
							<label class="form-label">
								Banco
								<span class="text-red">*</span>
							</label>
							<input list="banco_list" name="banco" class="form-control border-rock text-uppercase" value="<?= $sucursal->banco; ?>" required>
							<datalist id="banco_list">
								<?php if (!empty($banco)): ?>
									<?php foreach ($banco as $b): ?>
										<option value="<?= $b->ban_nombre; ?>">
									<?php endforeach ?>
								<?php endif ?>
							</datalist>
							<span id="banco_feed" class="feed-red"></span>
						</div>
					</div>
					<div class="col-xs-12 col-md-4">
						<div class="form-group">
							<label class="form-label">
								Nro de cuenta
								<span class="text-red">*</span>
							</label>
							<input type="text" name="cuenta_numero" class="form-control border-rock" value="<?= $sucursal->cuenta_numero; ?>" required>
							<span id="cuenta_numero_feed" class="feed-red"></span>
						</div>
					</div>
				</div>
			</div>
			<div class="card-footer text-right">
				<button class="btn btn-blue">
					Guardar cambios
					<i class="fe fe-save"></i>
				</button>
				<a class="btn btn-secondary text-blue" href="<?= base_url() ?>Sucursal/detalle/<?= $sucursal->id; ?>">
					Ir al detalle
					<i class="fe fe-layers"></i>
				</a>
			</div>
		</div>
	</form>
</div>

<div id="md_ciudad" class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="md_ciudadLabel" aria-hidden="true">
  	<div class="modal-dialog modal-lg" role="document">
	    <div class="modal-content">
	      	<div class="modal-header">
		        <h4 class="modal-title" id="md_ciudadLabel">
		        	<i class="fe fe-chrome text-blue"></i>
					Seleccionar Ciudad
		        </h4>
		        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
		        	<span aria-hidden="true"></span>
		        </button>
	      	</div>
	      	<div class="modal-body">
	      		<div class="row">
					<div class="col-xs-12 col-md-12">
						<div class="col-xs-12 col-md-12">
							<label for="">Buscar</label>
							<input id="input_ciudad_buscar" type="text" class="form-control" autofocus="">
						</div>
						<div class="table-responsive">
							<table class="table">
								<thead>
									<tr>
										<th>Departamento</th>
										<th>Ciudad</th>
										<th></th>
									</tr>
								</thead>
								<tbody id="tb_ciudad">
									<tr class="text-center">
										<td colspan="3">Datos no cargados.</td>
									</tr>
								</tbody>
							</table>
						</div>
					</div>
				</div>
	      	</div>
	      	<div class="modal-footer">
		        <button type="button" class="btn btn-danger" data-dismiss="modal">
		        	Salir
		        	<i class="fe fe-corner-right-down"></i>
		        </button>
		    </div>
	    </div>
  	</div>
</div>

<div id="windowCiudad" class="window-overlay">
	<div class="window">
		<div class="window-header">
			<span class="window-close" data-window="windowCiudad">X</span>
			<h3 class="window-title">
				<i class="far fa-file"></i>
				Seleccionar ciudad
			</h3>
		</div>
		<div class="window-content">
			<div class="row p-3">
				<div class="col-xs-12 col-md-12">
					<label for="">Buscar</label>
					<input id="input_ciudad_buscar" type="text" class="form-control" autofocus="">
				</div>
			</div>
			<table id="ciudadTabla" class="table table-striped">
				<tr class="text-center">
					<td colspan="5">Sin ciudades</td>
				</tr>
			</table>
		</div>
		<div class="window-footer">
		</div>
	</div>
</div>


<script src="<?= base_url() ?>js_modules/sucursal/sucursal.editar.js"></script>