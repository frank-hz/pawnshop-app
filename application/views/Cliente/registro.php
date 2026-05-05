<form id="_form" autocorrect="off" autocomplete="off" class="container px-4">
	<div class="card">
		<div class="card-header">
			<h4>
				<i class="fe fe-folder-plus text-blue"></i>
				Nuevo Cliente
			</h4>
		</div>
		<div class="card-body">
			<div class="d-flex flex-wrap">
				<div class="col-xs-12 col-md-4 col-lg-3">
					<div class="card">
						<div class="card-body">
							<div class="col-xs-12 col-md-12 text-center">
								<img id="imagePrev" src="<?= base_url() ?>uploads/default/sin_imagen.jpg" alt="Sin imagen" class="img-thumbnail" width="60%">
							</div>
							<div class="col-xs-12 col-md-12 p-0 m-0">
								<input type="file" class="custom-file-input" id="foto" name="foto" accept="image/png, image/jpeg, image/jpg">
								<label class="btn btn-secondary btn-block border-rock" for="foto">
									Cargar
									<i class="fe fe-camera"></i>
								</label>
							</div>
						</div>
					</div>
				</div>
				<div class="col-xs-12 col-md-8 col-lg-9">
					<div class="card">
						<div class="card-body">
							<div class="row my-1">
								<div class="col-xs-12 col-md-4">
									<div class="form-group">
										<label for="" class="font-weight-bold">Nombres</label>
										<input type="text" class="form-control border-rock text-uppercase" name="nombre" maxlength="50" required>
										<span id="feed_nombre" class="feed-red"></span>
									</div>
								</div>
								<div class="col-xs-12 col-md-4">
									<div class="form-group">
										<label for="" class="font-weight-bold">Ape. Paterno</label>
										<input type="text" class="form-control border-rock text-uppercase" name="paterno" maxlength="50" required>
										<span id="feed_paterno" class="feed-red"></span>
									</div>
								</div>
								<div class="col-xs-12 col-md-4">
									<div class="form-group">
										<label for="" class="font-weight-bold">Ape. Materno</label>
										<input type="text" class="form-control border-rock text-uppercase" name="materno" maxlength="50" required>
										<span id="feed_materno" class="feed-red"></span>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-xs-12 col-md-4">
									<div class="form-group">
										<label for="" class="font-weight-bold">Documento</label>
										<input type="number" step="any" class="form-control border-rock text-uppercase" name="documento" minlength="8" maxlength="20" required>
										<span id="feed_documento" class="feed-red"></span>
									</div>
								</div>
								<div class="col-xs-12 col">
									<div class="form-group">
										<label for="" class="font-weight-bold">Tipo Documento</label>
										<div id="tdoc_container" class="custom-controls-stacked">
					                    </div>
					                    <span id="feed_tipodoc" class="feed-red"></span>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-xs-12 col-md-4">
									<div class="form-group">
										<label for="" class="font-weight-bold">Distrito</label>
										<div class="input-group">
				                            <input type="text" class="form-control border-rock text-uppercase" name="ciudad"  readonly>
				                            <span class="input-group-append">
				                            	<button id="btn_md_ciudad" class="btn " type="button">
				                            		<i class="fe fe-search"></i>
				                            		Buscar
				                            	</button>
				                            </span>
				                        </div>
										<input type="hidden" class="form-control border-rock" name="ubigeo">
										<span id="feed_ubigeo" class="feed-red"></span>
									</div>
								</div>
								<div class="col-xs-12 col-md-4">
									<div class="form-group">
										<label for="" class="font-weight-bold">Direccion</label>
										<input type="text" step="any" class="form-control border-rock text-uppercase" name="direccion" required>
										<span id="feed_direccion" class="feed-red"></span>
									</div>
								</div>
								<div class="col-xs-12 col-md-4">
									<div class="form-group">
										<label for="" class="font-weight-bold">Telefono</label>
										<input type="text" class="form-control border-rock text-uppercase" name="telefono" maxlength="50">
										<span id="feed_telefono" class="feed-red"></span>
									</div>
								</div>
								<div class="col-xs-12 col-md-4">
									<div class="form-group">
										<label for="" class="font-weight-bold">Email</label>
										<input type="text" class="form-control border-rock" name="email" maxlength="50">
										<span id="feed_email" class="feed-red"></span>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="card-footer">
			<button id="btnSubmit" class="btn btn-blue">
				Registrar Cliente
				<i class="fe fe-save"></i>
			</button>
		</div>
	</div>
</form>

<div id="md_ciudad" class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="mdPrendaAgregarLabel" aria-hidden="true">
  	<div class="modal-dialog modal-lg" role="document">
	    <div class="modal-content">
	      	<div class="modal-header">
		        <h4 class="modal-title" id="mdCiudadLabel">
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
							<table class="table ">
								<thead>
									<tr>
										<th>Departamento</th>
										<th>Ciudad</th>
										<th></th>
									</tr>
								</thead>
								<tbody id="tdata__ciudad">
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
<script src="<?= base_url() ?>js_modules/cliente/cliente.registro.js?v=3"></script>