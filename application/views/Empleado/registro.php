<div class="container">
	<form id="frm_registro" autocorrect="off" autocomplete="off">
		<div class="card">
			<div class="card-header">
				<h4>
					<i class="fe fe-plus text-blue"></i>
					Nuevo Empleado
				</h4>
			</div>
			<div class="card-body">
				<div class="row">
					<div class="col-xs-12 col-md-4">
						<div class="form-group">
							<label for="" class="font-weight-bold">
								Nombres 
								<span class="text-red">*</span>
							</label>
							<input type="text" class="form-control border-rock text-uppercase" name="nombre" >
							<span id="feed_nombre" class="feed-red"></span>
						</div>
					</div>
					<div class="col-xs-12 col-md-4">
						<div class="form-group">
							<label for="" class="font-weight-bold">
								Ape. Paterno 
								<span class="text-red">*</span>
							</label>
							<input type="text" class="form-control border-rock text-uppercase" name="paterno" >
							<span id="feed_paterno" class="feed-red"></span>
						</div>
					</div>
					<div class="col-xs-12 col-md-4">
						<div class="form-group">
							<label for="" class="font-weight-bold">
								Ape. Materno 
								<span class="text-red">*</span>
							</label>
							<input type="text" class="form-control border-rock text-uppercase" name="materno" >
							<span id="feed_materno" class="feed-red"></span>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-xs-12 col-md-4">
						<div class="form-group">
							<label for="" class="font-weight-bold">
								Tipo Documento 
								<span class="text-red">*</span>
							</label>
							<div id="tdoc_container" class="custom-controls-stacked">
		                    </div>
		                    <span id="feed_tipodoc" class="feed-red"></span>
						</div>
					</div>
					<div class="col-xs-12 col-md-4">
						<div class="form-group">
							<label for="" class="font-weight-bold">
								Documento 
								<span class="text-red">*</span>
							</label>
							<input type="number" step="any" class="form-control border-rock text-uppercase" name="documento" required>
							<span id="feed_documento" class="feed-red"></span>
						</div>
					</div>
					<div class="col-xs-12 col-md-4">
						<div class="form-group">
							<label for="" class="font-weight-bold">
								Ciudad 
								<span class="text-red">*</span>
							</label>
							<div class="input-group">
	                            <input type="text" class="form-control text-uppercase border-rock" name="ciudad" readonly>
	                            <span class="input-group-append">
	                            	<button id="btn_ciudad" class="btn btn-indigo" type="button">
	                            		<i class="fe fe-search"></i>
	                            		Buscar
	                            	</button>
	                            </span>
	                        </div>
							<input type="hidden" class="form-control border-rock" name="ubigeo">
							<span id="feed_ubigeo" class="feed-red"></span>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-xs-12 col-md-4">
						<div class="form-group">
							<label for="" class="font-weight-bold">
								Direccion 
								<span class="text-red">*</span>
							</label>
							<input type="text" step="any" class="form-control border-rock text-uppercase" name="direccion">
							<span id="feed_direccion" class="feed-red"></span>
						</div>
					</div>
					<div class="col-xs-12 col-md-4">
						<div class="form-group">
							<label for="" class="font-weight-bold">
								Telefono 
								<span class="text-red">*</span>
							</label>
							<input type="text" class="form-control border-rock text-uppercase" name="telefono">
							<span id="feed_telefono" class="feed-red"></span>
						</div>
					</div>
					<div class="col-xs-12 col-md-4">
						<div class="form-group">
							<label for="" class="font-weight-bold">Email</label>
							<input type="text" class="form-control border-rock" name="email">
							<span id="feed_email" class="feed-red"></span>
						</div>
					</div>
					<div class="col-xs-12 col-md-6">
						<div class="form-group">
							<label for="" class="font-weight-bold">
								Sucursal 
								<span class="text-red">*</span>
							</label>
							<div class="input-group">
	                            <input type="text" class="form-control text-uppercase border-rock" name="sede" readonly>
	                            <span class="input-group-append">
	                            	<button id="btn_sucursal" class="btn btn-indigo" onclick="javascript:void(0)">
	                            		<i class="fe fe-search"></i>
	                            		Buscar
	                            	</button>
	                            </span>
	                        </div>
							<input type="hidden" class="form-control border-rock" name="sucursal">
							<span id="feed_sucursal" class="feed-red"></span>
						</div>
					</div>
				</div>
			</div>
			<div class="card-footer">
				<button class="btn btn-blue">
					Registrar Empleado
					<i class="fe fe-save"></i>
				</button>
			</div>
		</div>
	</form>
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
						<tbody id="datatable_sucursal">
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


<script src="<?= base_url() ?>js_modules/empleado/empleado.registro.js?v=4568"></script>