<div class="container">
	<div class="card">
		<div class="card-header">
			<h4>
				<i class="fe fe-edit-2 text-blue"></i>
				Editar Empleado
			</h4>
		</div>
		<div class="card-body">
			<form id="form_editar" autocorrect="off" autocomplete="off">
				<input type="hidden" name="codigo" value="<?= $empleado->id; ?>">
				<input type="hidden" name="dni" value="<?= $empleado->tipo_documento; ?>">
				<div class="row">
					<div class="col-xs-12 col-md-4">
						<div class="form-group">
							<label for="" class="font-weight-bold">Nombres</label>
							<input type="text" class="form-control border-rock text-uppercase" name="nombre" value="<?= $empleado->nombres; ?>" >
							<span id="nombre_feed" class="feed-red"></span>
						</div>
					</div>
					<div class="col-xs-12 col-md-4">
						<div class="form-group">
							<label for="" class="font-weight-bold">Ape. Paterno</label>
							<input type="text" class="form-control border-rock text-uppercase" name="paterno" value="<?= $empleado->paterno; ?>" required>
							<span id="paterno_feed" class="feed-red"></span>
						</div>
					</div>
					<div class="col-xs-12 col-md-4">
						<div class="form-group">
							<label for="" class="font-weight-bold">Ape. Materno</label>
							<input type="text" class="form-control border-rock text-uppercase" name="materno" value="<?= $empleado->materno; ?>" required>
							<span id="materno_feed" class="feed-red"></span>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-xs-12 col-md-4">
						<div class="form-group">
							<label for="" class="font-weight-bold">Tipo Documento</label>
							<div id="tdoc_container" class="custom-controls-stacked">
		                    </div>
		                    <span id="tipodoc_feed" class="feed-red"></span>
						</div>
					</div>
					<div class="col-xs-12 col-md-4">
						<div class="form-group">
							<label for="" class="font-weight-bold">Documento</label>
							<input type="number" step="any" class="form-control border-rock text-uppercase" name="documento" value="<?= $empleado->documento; ?>" required>
							<span id="documento_feed" class="feed-red"></span>
						</div>
					</div>
					<div class="col-xs-12 col-md-4">
						<div class="form-group">
							<label for="" class="font-weight-bold">Ciudad</label>
							<div class="input-group">
	                            <input type="text" class="form-control text-uppercase border-rock" name="ciudad" value="<?= $empleado->ciudad; ?>" readonly>
	                            <span class="input-group-append">
	                            	<button id="btn_md_ciudad" class="btn " type="button">
	                            		<i class="fe fe-search"></i>
	                            		Buscar
	                            	</button>
	                            </span>
	                        </div>
							<input type="hidden" class="form-control border-rock" name="ubigeo" value="<?= $empleado->ubigeo; ?>">
							<span id="ubigeo_feed" class="feed-red"></span>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-xs-12 col-md-4">
						<div class="form-group">
							<label for="" class="font-weight-bold">Direccion</label>
							<input type="text" step="any" class="form-control border-rock text-uppercase" name="direccion" value="<?= $empleado->direccion; ?>">
							<span id="direccion_feed" class="feed-red"></span>
						</div>
					</div>
					<div class="col-xs-12 col-md-4">
						<div class="form-group">
							<label for="" class="font-weight-bold">Telefono</label>
							<input type="text" class="form-control border-rock text-uppercase" name="telefono" value="<?= $empleado->telefono; ?>">
							<span id="telefono_feed" class="feed-red"></span>
						</div>
					</div>
					<div class="col-xs-12 col-md-4">
						<div class="form-group">
							<label for="" class="font-weight-bold">Email</label>
							<input type="text" class="form-control border-rock" name="email" value="<?= $empleado->email; ?>">
							<span id="emailFeed" class="feed-red"></span>
						</div>
					</div>
					<div class="col-xs-12 col-md-6">
						<div class="form-group">
							<label for="" class="font-weight-bold">Sucursal</label>
							<div class="input-group">
	                            <input type="text" class="form-control text-uppercase border-rock" name="sede" value="<?= $empleado->sucursal; ?>" readonly>
	                            <span class="input-group-append">
	                            	<button id="btn_md_sucursal" class="btn" type="button">
	                            		<i class="fe fe-search"></i>
	                            		Buscar
	                            	</button>
	                            </span>
	                        </div>
							<input type="hidden" class="form-control border-rock" name="sucursal" value="<?= $empleado->id_sucursal; ?>">
							<span id="sucursal_feed" class="feed-red"></span>
						</div>
					</div>
				</div>
				<div class="py-2">
					<a class="btn btn-secondary text-blue" href="<?= base_url() ?>Empleado/detalle/<?= $empleado->id; ?>">
						<i class="fe fe-arrow-left"></i>
						Ir al detalle
					</a>
					<button class="btn btn-blue">
						<i class="fe fe-save"></i>
						Guardar cambios
					</button>
				</div>
			</form>
		</div>
	</div>
</div>


<div id="md_ciudad" class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      	<div class="modal-header">
	        <h4 class="modal-title">
	        	<i class="fe fe-compass text-blue"></i>
	        	Elegir Ciudad
	        </h4>
	        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	        	<span aria-hidden="true"></span>
	        </button>
      	</div>
      	<div class="modal-body">
			<div class="col-xs-12 col-md-12">
				<div class="form-group">
					<label class="form-label">Buscar</label>
					<input type="text" class="form-control border-rock" id="input_ciudad_buscar" autocomplete="off">
				</div>
			</div>
			<div class="d-flex">
				<div class="table-responsive">
					<table class="table ">
						<thead>
							<tr>
								<th>Descripcion</th>
								<th>Ciudad</th>
								<th>Direccion</th>
								<th></th>
							</tr>
						</thead>
						<tbody id="datatable_ciudad">
							<tr class="text-center">
								<td colspan="3">Datos no cargados.</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
      	</div>
      	<div class="modal-footer">
	        <button type="button" class="btn btn-danger" data-dismiss="modal">
	        	<i class="fe fe-x-circle"></i>
	        	Cerrar
	        </button>
	    </div>
    </div>
  </div>
</div>


<div id="md_sucursal" class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      	<div class="modal-header">
	        <h4 class="modal-title">
	        	<i class="fe fe-compass text-blue"></i>
	        	Elegir Sucursal
	        </h4>
	        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	        	<span aria-hidden="true"></span>
	        </button>
      	</div>
      	<div class="modal-body">
			<div class="d-flex">
				<div class="table-responsive">
					<table class="table ">
						<thead>
							<tr>
								<th>Descripcion</th>
								<th>Ciudad</th>
								<th></th>
							</tr>
						</thead>
						<tbody id="tb_sucursal">
							<tr class="text-center">
								<td colspan="3">Datos no cargados.</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
      	</div>
      	<div class="modal-footer">
	        <button type="button" class="btn btn-danger" data-dismiss="modal">
	        	<i class="fe fe-x-circle"></i>
	        	Cerrar
	        </button>
	    </div>
    </div>
  </div>
</div>

<div id="mdImagen" class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="mdPrendaAgregarLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      	<div class="modal-header">
	        <h4 class="modal-title" id="mdImagenLabel">
	        	<i class="fe fe-compass text-blue"></i>
	        	Elegir Sucursal
	        </h4>
	        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	        	<span aria-hidden="true"></span>
	        </button>
      	</div>
      	<div class="modal-body">
      		<div class="row">
				
			</div>
      	</div>
      	<div class="modal-footer">
      		<button type="button" class="btn btn-green">
	        	<i class="fe fe-save"></i>
	        	Actualizar
	        </button>
	        <button type="button" class="btn btn-danger" data-dismiss="modal">
	        	<i class="fe fe-x-circle"></i>
	        	Cancelar
	        </button>
	    </div>
    </div>
  </div>
</div>


<script src="<?= base_url() ?>js_modules/empleado/empleado.editar.js?v=2454"></script>