<div class="container px-2 py-2">
	<div class="row">
		<div class="col-xs-12 col-md-7 col-lg-8">
			<div class="card">
				<div class="card-header">
					<h4 class="card-title">
						<i class="fe fe-layers text-blue"></i>
						Detalle del Usuario
					</h4>
				</div>
				<div class="card-body">
					<div class="row">
						<input id="id" type="hidden" value="<?= $usuario->id; ?>">
						<input id="rol" type="hidden" value="<?= $usuario->rol; ?>">
						<div class="col-xs-12 col-md-4 col-lg-3">
							<?php if (!empty($usuario->imagen)): ?>
								<img src="<?= base_url().$usuario->imagen ?>" class="img-thumbnail" width="65%" alt="">
							<?php else: ?>
								<img src="<?= base_url() ?>uploads/default/sin_imagen.jpg" class="img-thumbnail" width="65%" alt="">
							<?php endif ?>
						</div>
						<div class="col-xs-12 col-md-8">
							<div class="row">
								<div class="col-xs-12 col-md-12 form-group">
									<label for="" class="font-weight-bold form-label">Empleado</label>
									<span class="">
										<?php echo $usuario->empleado; ?>
									</span>
								</div>
							</div>
							<div class="row">
								<div class="col-xs-12 col-md-6 form-group">
									<label for="" class="font-weight-bold form-label">Usuario</label>
									<span><?php echo $usuario->usuario; ?></span>
								</div>
								<div class="col-xs-12 col-md-6 form-group">
									<label for="" class="font-weight-bold form-label">Fecha Creacion</label>
									<span><?php echo $usuario->creacion; ?></span>
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
		<div class="col-xs-12 col-md-5 col-lg-4">
			<div class="card">
				<div class="list-group list-group-transparent mb-0">
					<a href="javascript:void(0)" class="list-group-item list-group-item-action active">
						Opciones
					</a>
				</div>
				<div class="list-group list-group-transparent mb-0">
					<!-- <a href="<?= base_url() ?>Usuario/editar/<?= $usuario->id; ?>" class="list-group-item list-group-item-action text-blue">
						<span class="icon mr-3"><i class="fe fe-edit-2 text-blue"></i></span>
						Editar
					</a> -->
					<a id="btn_reiniciar" href="javascript:void(0)" data-empleado="<?= $usuario->id_empleado; ?>" data-usuario="<?= $usuario->id; ?>" class="list-group-item list-group-item-action text-blue">
						<span class="icon mr-3"><i class="fe fe-zap text-blue"></i></span>
						Reiniciar
					</a>
					<?php if ($usuario->estado === 'A'): ?>
						<a id="btn_usuario_estado" href="javascript:void(0)" data-id="<?= $usuario->id; ?>" data-idemp="<?= $usuario->empleado_id; ?>" class="list-group-item list-group-item-action text-red" data-nestado="I" data-mssg="¿Desactivar usuario?">
							<span class="icon mr-3"><i class="fe fe-slash text-red"></i></span>
							Desactivar
						</a>
					<?php else: ?>
						<a id="btn_usuario_estado" href="javascript:void(0)" data-id="<?= $usuario->id; ?>" data-idemp="<?= $usuario->empleado_id; ?>" class="list-group-item list-group-item-action text-blue" data-nestado="A" data-mssg="¿Activar usuario?">
							<span class="icon mr-3"><i class="fe fe-slash text-blue"></i></span>
							Activar
						</a>
					<?php endif ?>
				</div>
			</div>
		</div>
	</div>
	<div class="row px-2">
		<div class="col-xs-12 col-md-6">
			<div class="card">
				<div class="card-header">
					<h4 class="card-title">
						<i class="fe fe-layout text-blue"></i>
						Modulos
					</h4>
					<div class="card-options">
						<div id="ModuloLoader"></div>
					</div>
				</div>
				<div class="card-body">
					<div class="table-responsive overflow-auto" style="max-height: 400px">
						<table class="table text-nowrap">
							<thead>
								<th>#</th>
								<th>Descripcion</th>
								<th>Acceso</th>
								<th></th>
							</thead>
							<tbody id="tb_modulo">
								<tr class="text-center">
									<td colspan="4">No se encontraron resultados.</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
		<div class="col-xs-12 col-md-6">
			<div class="card">
				<div class="card-header">
					<h4 class="card-title">
						<i class="fe fe-shield text-indigo"></i>
						Permisos
					</h4>
					<div class="card-options">
						<div id="PermisoLoader"></div>
					</div>
				</div>
				<div class="card-body">
					<div class="table-responsive overflow-auto" style="max-height: 400px">
						<table class="table text-nowrap">
							<thead>
								<th>Nombre</th>
								<th>Descripcion</th>
								<th>Estado</th>
								<th></th>
							</thead>
							<tbody id="tb_permiso">
								<tr class="text-center">
									<td colspan="4">No se encontraron resultados.</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<script src="<?= base_url() ?>js_modules/usuario/usuario.detalle.js?v=45648"></script>
