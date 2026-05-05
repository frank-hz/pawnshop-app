<div class="container px-2 py-2">	
	<div class="row px-2">
		<div class="col-xs-12 col-md-9 col-lg-7">
			<div class="card">
				<div class="card-header">
					<h3 class="card-title">
						<i class="fe fe-layers text-primary"></i>
						Detalle del empleado
					</h3>
				</div>
				<div class="card-body">
					<div class="row">
						<input id="__empleado" type="hidden" value="<?= $empleado->id; ?>">
						<div class="col-xs-12 col-md-4 col-lg-3">
							<?php if (!empty($empleado->imagen)): ?>
								<img src="<?= base_url().$empleado->imagen ?>" class="img-thumbnail" alt="">
							<?php else: ?>
								<img src="<?= base_url() ?>uploads/default/sin_imagen.jpg" class="img-thumbnail" alt="">
							<?php endif ?>
						</div>
						<div class="col-xs-12 col-md-8">
							<div class="row">
								<div class="col-xs-12 col-md-12 form-group">
									<label for="" class="font-weight-bold form-label">Nombres y Apellidos</label>
									<span class="">
										<?php echo $empleado->nombres." ".$empleado->paterno." ".$empleado->materno; ?>
									</span>
								</div>
							</div>
							<div class="row">
								<div class="col-xs-12 col-md-6 form-group">
									<label for="" class="font-weight-bold form-label">Documento</label>
									<span><?php echo $empleado->documento; ?></span>
								</div>
								<div class="col-xs-12 col-md-6 form-group">
									<label for="" class="font-weight-bold form-label">Tipo de Documento</label>
									<span><?php echo $empleado->tipo_documento; ?></span>
								</div>
								<div class="col-xs-12 col-md-6 form-group">
									<label for="" class="font-weight-bold form-label">Ciudad</label>
									<span><?php echo $empleado->ciudad; ?></span>
								</div>
								<div class="col-xs-12 col-md-6 form-group">
									<label for="" class="font-weight-bold form-label">Direccion</label>
									<span><?php echo $empleado->direccion; ?></span>
								</div>
								<div class="col-xs-12 col-md-6 form-group">
									<label for="" class="font-weight-bold form-label">Telefono</label>
									<span><?php echo $empleado->telefono; ?></span>
								</div>
								<div class="col-xs-12 col-md-6 form-group">
									<label for="" class="font-weight-bold form-label">Email</label>
									<span><?php echo $empleado->email; ?></span>
								</div>
								<div class="col-xs-12 col-md-6 form-group">
									<label for="" class="font-weight-bold form-label">Fecha Creacion</label>
									<span><?php echo $empleado->creacion; ?></span>
								</div>
								<div class="col-xs-12 col-md-6 form-group">
									<label for="" class="font-weight-bold form-label">Estado</label>
									<?php  
										switch ($empleado->estado) {
											case 'A':
												echo "<span class='stag stag-emp-A'>Activo</span>";
												break;
											
											case 'I':
												echo "<span class='stag stag-emp-I'>Inactivo</span>";
												break;
										}
									?>
								</div>
							</div>
							<div class="row">
								<div class="col-xs-12 col-md-6">
									<div class="form-label">Usuario</div>
									<span><?php echo $empleado->usuario; ?></span>
									<a class="btn btn-secondary text-blue" href="<?= base_url() ?>Usuario/detalle/<?= $empleado->id_usuario; ?>">
										Ver detalle
									</a>
								</div>
								<div class="col-xs-12 col-md-6">
									<div class="form-label">Sucursal</div>
									<span><?php echo $empleado->sucursal; ?></span>
									<a class="btn btn-secondary text-blue" href="<?= base_url() ?>Sucursal/detalle/<?= $empleado->id_sucursal; ?>">
										Ver detalle
									</a>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-xs-12 col-md-3 col-lg-5">
			<div class="card">
				<div class="list-group list-group-transparent mb-0">
					<a href="javascript:void(0)" class="list-group-item list-group-item-action active">
						  Opciones
					  </a>
				</div>
				<div class="list-group list-group-transparent mb-0">
					<a href="<?= base_url() ?>empleado/editar/<?= $empleado->id; ?>" class="list-group-item list-group-item-action text-blue">
						<span class="icon mr-3"><i class="fe fe-edit-2 text-blue"></i></span>
						Editar
					</a>
					<a id="optDesactivar" href="javascript:void(0)" data-id="<?= $empleado->id; ?>" class="list-group-item list-group-item-action text-red">
						<span class="icon mr-3"><i class="fe fe-thumbs-down text-red"></i></span>
						Desactivar
					</a>
					<a id="optEliminar" href="javascript:void(0)" data-id="<?= $empleado->id; ?>" class="list-group-item list-group-item-action text-red">
						<span class="icon mr-3"><i class="fe fe-trash text-red"></i></span>
						Eliminar
					</a>
				</div>			
			</div>
		</div>
	</div>
</div>
