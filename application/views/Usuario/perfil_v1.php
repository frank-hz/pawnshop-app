<div class="container">
	<div class="row">
		<div class="col-xs-12 col-lg-9">
			<div class="card">
				<div class="card-header">
					<h4 class="mt-0 mb-4">
						<i class="fe fe-user text-blue"></i>
						Mi Perfil
					</h4>
				</div>
				<!-- <pre>
					<?= print_r($usuario); ?>
				</pre> -->
              	<div class="card-body">
					<div class="row">
						<input id="_id" type="hidden" value="<?= $usuario->id; ?>">
						<div class="col-xs-12 col-md-4">
							<?php if (!empty($usuario->imagen)): ?>
								<img src="<?= base_url().$usuario->imagen ?>" class="img-thumbnail" alt="">
							<?php else: ?>
								<img src="<?= base_url() ?>uploads/default/sin_imagen.jpg" class="img-thumbnail" alt="">
							<?php endif ?>
							<div class="p-0">
								<label for="imagen" class="btn btn-outline-primary btn-block font-weight-bold">
									Cambiar 
									<i class="fe fe-camera"></i>
								</label>
								<input type="file" class="custom-file-input" id="imagen" data-id="<?= $usuario->empleado_id; ?>" data-url="<?= $usuario->imagen; ?>" data-nombre="<?= $usuario->nombre_full; ?>" accept="image/png, image/jpeg, image/jpg" style="visibility: hidden;">
							</div>

							<!-- <div class="custom-file">
	                          	<input type="file" class="custom-file-input" name="foto" accept="image/png, image/jpeg, image/jpg">
	                          	<label class="custom-file-label border-rock">
	                          		<i class="fe fe-upload"></i>
	                          		Cargar
	                          	</label>
	                        </div> -->

						</div>
						<div class="col-xs-12 col-md-8">
							<div class="row">
								<div class="col-xs-12 col-md-12 form-group">
									<label for="" class="font-weight-bold form-label">Nombres y Apellidos</label>
									<span class="">
										<?= $usuario->nombre_full; ?>
									</span>
								</div>
							</div>
							<div class="row">
								<div class="col-xs-12 col-md-6 form-group">
									<label for="" class="font-weight-bold form-label">Usuario</label>
									<span><?php echo $usuario->username; ?></span>
								</div>
								<div class="col-xs-12 col-md-6 form-group">
									<label for="" class="font-weight-bold form-label">Fecha Creacion</label>
									<span><?= $usuario->create_at; ?></span>
								</div>
								<div class="col-xs-12 col-md-6 form-group">
									<label for="" class="font-weight-bold form-label">Rol</label>
									<span><?php echo $usuario->rol; ?></span>
								</div>
								<div class="col-xs-12 col-md-6 form-group">
									<label for="" class="font-weight-bold form-label">Estado</label>
									<?php  
										switch ($usuario->estado) {
											case 'A':
												echo "<span class='stag stag-usu-A'>Activo</span>";
												break;
											
											case 'I':
												echo "<span class='stag stag-usu-I'>Inactivo</span>";
												break;
										}
									?>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>


		</div>
		<div class="col-xs-12 col-lg-3">
	    	<div class="card">
	    		<div class="list-group list-group-transparent mb-0">
	            	<a href="javascript:void(0)" class="list-group-item list-group-item-action active">
	              		Opciones
	              	</a>
	            </div>
	            <div class="list-group list-group-transparent mb-0">
	            	<a id="btn_usuario_editar" href="javascript:void(0)" data-id="<?= $usuario->id; ?>" class="list-group-item list-group-item-action text-blue">
	            		<span class="icon mr-3"><i class="fe fe-edit-2 text-blue"></i></span>
	            		Editar datos
	            	</a>
	            </div>
	    	</div>
		</div>
	</div>
</div>

<div id="md_imagen" class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="mdPrendaAgregarLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      	<div class="modal-header">
	        <h4 class="modal-title" id="mdImagenLabel">
	        	<i class="far fa-file text-blue"></i>
				Cambiar imagen
	        </h4>
	        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	        	<span aria-hidden="true"></span>
	        </button>
      	</div>
      	<div class="modal-body">
      		<div class="row">
      			<div class="col-xs-12 col-md-6">
      				<img id="imagePreview" src="" alt="Nueva Imagen" class="img-thumbnail">
      			</div>
      			<div class="col-xs-12 col-md-6">
      				<div class="form-group">
      					<label for="" class="font-weight-bold">Peso</label>
      					<p id="imagenPeso">Sin datos</p>
      				</div>
      				<div class="form-group">
      					<label for="" class="font-weight-bold">Formato</label>
      					<p id="imagenFormato">Sin datos</p>
      				</div>
      			</div>
      		</div>
      	</div>
      	<div class="modal-footer text-right">
      		<button id="btn_imagen" type="button" class="btn btn-blue">
	        	Actualizar
	        	<i class="fe fe-save"></i>
	        </button>
	        <button type="button" class="btn btn-danger" data-dismiss="modal">
	        	<i class="fe fe-arrow-down-circle"></i>
	        	Cancelar
	        </button>
	    </div>
    </div>
  </div>
</div>

<div id="md_editar" class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      	<div class="modal-header">
	        <h4 class="modal-title">
	        	<i class="fe fe-edit-2 text-blue"></i>
				Editar datos
	        </h4>
	        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	        	<span aria-hidden="true"></span>
	        </button>
      	</div>
  		<form id="form_editar" autocomplete="off">
      		<div class="modal-body">
      			<input type="hidden" name="id" value="<?= $usuario->id; ?>">
      			<input type="hidden" name="tipo">
      			<div class="row p-2">
      				<div class="col-xs-12 col-md-12">
      					<label for="" class="font-weight-bold">Nuevo Usuario</label>
      					<input type="text" class="form-control border-rock" name="usuario" placeholder="<?= $usuario->usuario; ?>">
      				</div>
      			</div>
      			<div class="row p-2">
      				<div class="col-xs-12 col-md-12">
      					<label for="" class="font-weight-bold">Nueva Contraseña</label>
      					<input type="password" class="form-control border-rock" name="clave" placeholder="**********">
      				</div>
      			</div>
      			<div class="divider"></div><br>
      			<div class="row p-2">
      				<div class="col-xs-12 col-md-12">
      					<label for="" class="font-weight-bold">
      						Contraseña Actual <span class="form-required">*</span>
      					</label>
      					<input type="password" class="form-control border-rock" name="clave_actual" required>
      				</div>
      			</div>
      		</div>
      		<div class="modal-footer text-right">
      			<button type="submit" class="btn btn-blue">
		        	Guardar cambios
		        	<i class="fe fe-save"></i>
		        </button>
					<button type="button" class="btn btn-danger" data-dismiss="modal">
		        	<i class="fe fe-arrow-down-circle"></i>
		        	Cancelar
		        </button>
      		</div>
  		</form>
    </div>
  </div>
</div>

<script src="<?= base_url() ?>js_modules/usuario/usuario.perfil.js"></script>
