<?php ini_set('display_errors', 0); ?>
<div class="container">
	<div class="row">
		<div class="col-xs-12 col-lg-9">
			<div class="card">
				<div class="card-header">
					<h4 class="mt-0 mb-4">
						<i class="fe fe-layers text-blue"></i>
						Detalle de la Sucursal
					</h4>
				</div>
              	<div class="card-body">
					<div class="row">
						<div class="col-xs-12 col-md-12 form-group">
							<label for="" class="font-weight-bold form-label">
								Descripcion
							</label>
							<span class="">
								<?php echo $sucursal->nombre; ?>
							</span>
						</div>
						<div class="col-xs-12 col-md-6 form-group">
							<label for="" class="font-weight-bold form-label">
								Codigo
							</label>
							<span><?php echo $sucursal->serie; ?></span>
						</div>
						<div class="col-xs-12 col-md-6 form-group">
							<label for="" class="font-weight-bold form-label">
								Codigo de Pago
							</label>
							<span><?php echo $sucursal->pago_serie; ?></span>
						</div>
						<div class="col-xs-12 col-md-6 form-group">
							<label for="" class="font-weight-bold form-label">
								Ciudad
							</label>
							<span><?php echo $sucursal->ciudad; ?></span>
						</div>
						<div class="col-xs-12 col-md-6 form-group">
							<label for="" class="font-weight-bold form-label">
								Direccion
							</label>
							<span><?php echo $sucursal->direccion; ?></span>
						</div>
						<div class="col-xs-12 col-md-6 form-group">
							<label for="" class="font-weight-bold form-label">
								Telefono
							</label>
							<span><?php echo $sucursal->telefono; ?></span>
						</div>
						<div class="col-xs-12 col-md-6 form-group">
							<label for="" class="font-weight-bold form-label">
								Fecha Creacion
							</label>
							<span><?php echo $sucursal->creacion; ?></span>
						</div>
					</div>
				</div>
				<div class="card-body">
					<h5>Cuenta de Pago</h5>
					<div class="row">
						<div class="col-xs-12 col-md-6 form-group">
							<label for="" class="form-label">
								Tipo de cuenta
							</label>
							<span><?php echo $sucursal->cuenta_tipo; ?></span>
						</div>
					</div>
					<div class="row">
						<div class="col-xs-12 col-md-6 form-group">
							<label for="" class="form-label">
								Banco
							</label>
							<span><?php echo $sucursal->banco; ?></span>
						</div>
						<div class="col-xs-12 col-md-6 form-group">
							<label for="" class="form-label">
								Numero de cuenta
							</label>
							<span><?php echo $sucursal->cuenta_numero; ?></span>
						</div>
					</div>
				</div>
			</div>
			<div class="card">
				<div class="card-header">
					<h4 class="card-title">
						<i class="fe fe-user text-blue"></i>
						Empleados
					</h4>
					<div class="card-options">
						<div class="">
							<a href="<?= base_url() ?>Empleado/nuevo" class="btn btn-blue">
								Nuevo Empleado
								<i class="fe fe-plus"></i>
							</a>
							<button id="EmpleadoCargar" class="btn btn-secondary">
								Refresca
								<i class="fe fe-rotate-ccw"></i>
							</button>
						</div>
					</div>
				</div>
				<div class="card-body">
					<div class="table-responsive">
						<table class="table">
							<thead>
								<th>#</th>
								<th>Nombres y Apellidos</th>
								<th>Documento</th>
								<th>Telefono</th>
								<th>Estado</th>
								<th></th>
							</thead>
							<tbody id="tb_empleado">
								<tr class="text-center">
									<td colspan="6">No se encontraron datos.</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
		<div class="col-xs-12 col-lg-3">

            <div class="list-group list-group-transparent mb-0">
            	<a href="javascript:void(0)" class="list-group-item list-group-item-action active">
              		Opciones
              	</a>
            </div>
            <div class="list-group list-group-transparent mb-0">
            	<a href="<?= base_url() ?>Sucursal/editar/<?= $sucursal->id; ?>" class="list-group-item list-group-item-action text-blue">
            		<span class="icon mr-3"><i class="fe fe-edit-2 text-blue"></i></span>
            		Editar
            	</a>
            	<a id="optEliminar" href="javascript:void(0)" data-id="<?= $sucursal->id; ?>" class="list-group-item list-group-item-action text-red">
            		<span class="icon mr-3"><i class="fe fe-trash text-red"></i></span>
            		Eliminar
            	</a>
            </div>
		</div>
	</div>
</div>



<script src="<?= base_url() ?>js_modules/sucursal/sucursal.detalle_trash.js"></script>
