<form id="frm_editar" autocorrect="off" autocomplete="off" class="px-4">
	<input type="text" name="id" value="<?= $cliente->id ?>" hidden>
	<div class="card">
		<div class="card-header">
			<h4>
				<i class="fe fe-edit-2 text-blue"></i>
				Editar Cliente
			</h4>
		</div>
		<div class="card-body">
			<div class="d-flex">
				<div class="col-xs-12 col-md-4 col-lg-3">
					<div class="card">
						<div class="card-body">
							<div class="col-xs-12 col-md-12 text-center">
								<?php if (!empty($cliente->imagen)): ?>
									<img id="img_preview" src="<?= base_url().$cliente->imagen; ?>" alt="Sin imagen" class="img-thumbnail" width="60%">
								<?php else: ?>
									<img id="img_preview" src="<?= base_url() ?>uploads/default/sin_imagen.jpg" alt="Sin imagen" class="img-thumbnail" width="60%">
								<?php endif ?>
							</div>
							<div class="col-xs-12 col-md-12 p-0 m-0">
								<input type="file" class="custom-file-input" id="imagen" name="imagen" accept="image/png, image/jpeg, image/jpg" data-id="<?= $cliente->id ?>" data-doc="<?= $cliente->documento ?>">
								<label class="btn btn-secondary btn-block border-rock" for="imagen">
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
							<div class="d-flex my-1">
								<div class="col-xs-12 col-md-4">
									<div class="form-group">
										<label for="" class="font-weight-bold">Nombres</label>
										<input type="text" class="form-control border-rock text-uppercase" name="nombre" value="<?= $cliente->nombres; ?>">
										<span id="feed_nombre" class="feed-red"></span>
									</div>
								</div>
								<div class="col-xs-12 col-md-4">
									<div class="form-group">
										<label for="" class="font-weight-bold">Ape. Paterno</label>
										<input type="text" class="form-control border-rock text-uppercase" name="paterno" value="<?= $cliente->paterno; ?>">
										<span id="feed_paterno" class="feed-red"></span>
									</div>
								</div>
								<div class="col-xs-12 col-md-4">
									<div class="form-group">
										<label for="" class="font-weight-bold">Ape. Materno</label>
										<input type="text" class="form-control border-rock text-uppercase" name="materno" value="<?= $cliente->materno; ?>">
										<span id="feed_materno" class="feed-red"></span>
									</div>
								</div>
							</div>
							<div class="d-flex">
								<div class="col-xs-12 col-md-6">
									<div class="form-group">
										<label for="" class="font-weight-bold">Documento</label>
										<input type="number" step="any" class="form-control border-rock text-uppercase" name="documento" value="<?= $cliente->documento; ?>">
										<span id="feed_documento" class="feed-red"></span>
									</div>
								</div>
								<div class="col-xs-12 col-md-6">
									<div class="form-group">
										<label for="" class="font-weight-bold">Tipo Documento</label>
										<div id="tdoc_container" class="custom-controls-stacked">
											<?php if (!empty($tipo_doc)): ?>
												<?php foreach ($tipo_doc as $td): ?>
													<?php if ($td->nombre == $cliente->tipo_documento): ?>
														<label class="custom-control custom-radio custom-control-inline">
								                            <input type="radio" class="custom-control-input" name="tipo_documento" value="<?= $td->nombre; ?>" checked required>
								                            <span class="custom-control-label">
								                            	<?php echo $td->nombre; ?>
								                            </span>
								                        </label>
													<?php else: ?>
														<label class="custom-control custom-radio custom-control-inline">
								                            <input type="radio" class="custom-control-input" name="tipo_documento" value="<?= $td->nombre; ?>" required>
								                            <span class="custom-control-label">
								                            	<?php echo $td->nombre; ?>
								                            </span>
								                        </label>
													<?php endif ?>
													
												<?php endforeach ?>
											<?php endif ?>
					                    </div>
					                    <span id="feed_tipodoc" class="feed-red"></span>
									</div>
								</div>
							</div>
							<div class="d-flex">
								<div class="col-xs-12 col-md-4">
									<div class="form-group">
										<label for="" class="font-weight-bold">Distrito</label>
										<div class="input-group">
				                            <input type="text" class="form-control border-rock text-uppercase" name="ciudad" value="<?= $cliente->ciudad; ?>" readonly>
				                            <span class="input-group-append">
				                            	<button id="btn_md_ciudad" class="btn " type="button">
				                            		<i class="fe fe-search"></i>
				                            		Buscar
				                            	</button>
				                            </span>
				                        </div>
										<input type="hidden" class="form-control border-rock" name="ubigeo" value="<?= $cliente->ubigeo; ?>">
										<span id="feed_ubigeo" class="feed-red"></span>
									</div>
								</div>
								<div class="col-xs-12 col-md-8">
									<div class="form-group">
										<label for="" class="font-weight-bold">Direccion</label>
										<input type="text" step="any" class="form-control border-rock text-uppercase" name="direccion" value="<?= $cliente->direccion; ?>">
										<span id="feed_ubigeo" class="feed-red"></span>
									</div>
								</div>
							</div>
							<div class="d-flex">
								<div class="col-xs-12 col-md-6">
									<div class="form-group">
										<label for="" class="font-weight-bold">Telefono</label>
										<input type="text" class="form-control border-rock text-uppercase" name="telefono" value="<?= $cliente->telefono; ?>">
										<span id="feed_telefono" class="feed-red"></span>
									</div>
								</div>
								<div class="col-xs-12 col-md-6">
									<div class="form-group">
										<label for="" class="font-weight-bold">Email</label>
										<input type="email" class="form-control border-rock" name="email" value="<?= $cliente->email; ?>">
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
			<button class="btn btn-blue">
				Guardar Cambios
				<i class="fe fe-save"></i>
			</button>
			<a href="<?= base_url() ?>Cliente/detalle/<?= $cliente->id; ?>" class="btn btn-secondary text-blue" data-dismiss="modal">
	        	Ver detalle
	        	<i class="fe fe-layers"></i>
	        </a>
		</div>
	</div>
</form>

<div id="md_ciudad" class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="mdPrendaAgregarLabel" aria-hidden="true">
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
							<table class="table ">
								<thead>
									<tr>
										<th>Departamento</th>
										<th>Ciudad</th>
										<th></th>
									</tr>
								</thead>
								<tbody id="tb_md_ciudad">
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
<div id="md_imagen" class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="mdPrendaAgregarLabel" aria-hidden="true">
  	<div class="modal-dialog modal-lg" role="document">
	    <div class="modal-content">
	      	<div class="modal-header">
		        <h4 class="modal-title" id="md_ciudadLabel">
		        	<i class="fe fe-camera text-blue"></i>
					Imagen
		        </h4>
		        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
		        	<span aria-hidden="true"></span>
		        </button>
	      	</div>
	      	<div class="modal-body">
	      		<div class="d-flex">
	      			<div class="col-xs-12 col-md-6">
			      		<div id="md_imagen_preview" class="d-flex justify-content-center">

			      		</div>
	      			</div>
	      			<div class="col-xs-12 col-md-6">
	      				<ul class="list-group">
							<li class="list-group-item" id="md_imagen_size"></li>
						  	<li class="list-group-item" id="md_imagen_format"></li>
						</ul>
	      				<form id="frm_imagen">
	      					<!-- <input type="hidden" value="<?= $cliente->id ?>" name="codigo"> -->
	      					<div class="btn-list py-2">
	      						<button class="btn btn-blue" type="submit">
	      							Actualizar 
	      							<i class="fe fe-upload"></i>
	      						</button>
	      						<button type="button" class="btn btn-danger" data-dismiss="modal">
						        	Cancelar
						        	<i class="fe fe-corner-right-down"></i>
						        </button>
	      					</div>
	      				</form>
	      			</div>
	      		</div>
	      	</div>
	      	<!-- <div class="modal-footer">
		        <button type="button" class="btn btn-danger" data-dismiss="modal">
		        	Salir
		        	<i class="fe fe-corner-right-down"></i>
		        </button>
		    </div> -->
	    </div>
  	</div>
</div>
<script src="<?= base_url() ?>js_modules/cliente/cliente.editar.js"></script>