<?php ini_set('display_errors', 0); ?>
<div class="container">
    <input type="hidden" id="idsuc" value="<?= $sucursal->id; ?>">
	<div class="row">
        <div class="col-lg-3">
            <div class="card mt-2">
                <div class="card-header">
                    <h3 class="card-title text-blue form-label">Opciones</h3>
                </div>
                <div class="list-group">
                    <?php if ($this->session->userdata('pres_ses_rol') === 'Administrador'): ?>
                    <a id="btn-data-editar" href="javascript:void(0)" class="list-group-item list-group-item-action text-blue py-2">
                        <span class="icon mr-3"><i class="fe fe-edit-2 text-blue"></i></span>
                        Editar
                    </a>
                    <a id="optEliminar" href="javascript:void(0)" data-id="<?= $sucursal->id; ?>" class="list-group-item list-group-item-action text-red">
                        <span class="icon mr-3"><i class="fe fe-trash text-red"></i></span>
                        Eliminar
                    </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>

		<div class="col-lg-9">
			<div class="card border-fx-1 my-2">
				<div class="card-header">
					<div class="card-title font-weight-bold text-indigo">
						<i class="fe fe-layers text-indigo font-weight-bold"></i>
                        <span id="dg-nombre">
                            <?php echo "{$sucursal->nombre}, {$sucursal->distrito}"; ?> 
                        </span>
					</div>
                    <div class="card-options">
                        <small class="font-weight-bold">Detalle de Sucursal</small>
                    </div>
				</div>
              	<div class="card-body">
					<div class="row">
						<div class="col-md-6 form-group">
							<label for="" class="font-weight-bold form-label mb-0">
								Serie
							</label>
							<span id="dg-serie"><?php echo $sucursal->serie; ?></span>
						</div>
						<div class="col-md-6 form-group">
							<label for="" class="font-weight-bold form-label mb-0">
								Serie de Pago
							</label>
							<span id="dg-seriepago"><?php echo $sucursal->serie_pago; ?></span>
						</div>
						<div class="col-md-6 form-group">
							<label for="" class="font-weight-bold form-label mb-0">
								Distrito
							</label>
							<span id="dg-distrito"><?php echo $sucursal->distrito.', '.$sucursal->departamento; ?></span>
						</div>
						<div class="col-md-6 form-group">
							<label for="" class="font-weight-bold form-label mb-0">
								Direccion
							</label>
							<span id="dg-direccion"><?php echo $sucursal->direccion; ?></span>
						</div>
						<div class="col-md-6 form-group">
							<label for="" class="font-weight-bold form-label mb-0">
								Telefono
							</label>
							<span id="dg-telefono"><?php echo $sucursal->telefono; ?></span>
						</div>
						<div class="col-md-6 form-group">
							<label for="" class="font-weight-bold form-label mb-0">
								Fecha Creacion
							</label>
							<span><?php echo $sucursal->creacion; ?></span>
						</div>
					</div>
                    <div class="row">
                        <div class="col-md-6">
                            <label for="" class="font-weight-bold form-label mb-0">
                                Tipo de cuenta
							</label>
							<span id="dg-cuentatipo">
                                <?= $sucursal->cuenta_tipo ? $sucursal->cuenta_tipo : 'Sin Datos'; ?>
                            </span>
                        </div>
                        <div class="col-md-6">
                            <label for="" class="font-weight-bold form-label mb-0">
								Banco
							</label>
							<span id="dg-banco">
                                <?= $sucursal->banco ? $sucursal->banco : 'Sin Datos'; ?>
                            </span>
                        </div>
                        <div class="col-md-6 mt-2">
                            <label for="" class="font-weight-bold form-label mb-0">
								Numero de cuenta
							</label>
							<span id="dg-cuentanumero">
                                <?= $sucursal->cuenta_numero ? $sucursal->cuenta_numero : 'Sin Datos'; ?>
                            </span>
                        </div>
                    </div>
				</div>				
			</div>

			<div class="card border-fx-1">
				<div class="card-header" style="border-bottom:none;">
					<h5 class="card-title text-indigo">
                        <i class="fe fe-user"></i>
                        Empleados
                    </h5>
					<div class="card-options">
                        <button id="btn-empleado-nuevo" class="enc2454-btn-option enc2454-btn-option-blue">
                            <i class="fe fe-plus"></i>
                            Nuevo Empleado 
                        </button>
					</div>
				</div>
                <div class="p-0" style="max-height: 300px; overflow-y: auto;">
                    <table class="card-table table text-nowrap">
                        <thead>
                            <th>#</th>
                            <th>Nombres y Apellidos</th>
                            <th>Documento</th>
                            <th>Telefono</th>
                            <th>Estado</th>
                            <th></th>
                        </thead>
                        <tbody id="datatable-empleados">
                            <tr class="text-center">
                                <td colspan="6">No se encontraron datos.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
			</div>

		</div>
		
	</div>
</div>

<div id="modal-edit" class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <form id="form-edit"  autocomplete="off" autocorrect="off" class="modal-content">
            <div class="modal-header">
                <h4 id="modal-nota-title" class="modal-title text-blue">
                    <i class="fe fe-edit-2 text-blue"></i>
                    Editar Datos
                </h4>
                <div class="card-options">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true"></span>
                    </button>
                </div>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12 mb-2">
                        <label class="form-label">Nombre</label>
                        <input type="text" class="form-control text-uppercase border-fx-1" name="nombre" placeholder="" required>
                    </div>
                    <!-- <div class="col-md-6 mb-2">
                        <label class="form-label">Serie</label>
                        <input type="text" class="form-control text-uppercase border-fx-1" name="serie" placeholder="" required>
                    </div>
                    <div class="col-md-6 mb-2">
                        <label class="form-label">Serie de Pago</label>
                        <input type="text" class="form-control text-uppercase border-fx-1" name="pago_serie" placeholder="" required>
                    </div> -->
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Distrito</label>
                        <div class="tooltip-form-group">
                        <input type="text" class="form-control text-uppercase border-fx-1" name="distrito" placeholder="" required>
                        <input type="hidden" name="ubigeo">
                        <ul id="distrito-search-list" class="tooltip-form-list">
                            <!-- <li class="tooltip-item" name="distrito-item">
                            <div class="tooltip-item-title">Casma</div> 
                            <div class="tooltip-item-subtitle">Ancash</div>    
                            </li> -->
                        </ul>
                        </div>
                    </div>
                    <div class="col-md-6 mb-2">
                        <label class="form-label">Direccion</label>
                        <input type="text" class="form-control text-uppercase border-fx-1" name="direccion" placeholder="" required>
                    </div>
                    <div class="col-md-6 mb-2">
                        <label class="form-label">Telefono</label>
                        <input type="text" class="form-control text-uppercase border-fx-1" name="telefono" placeholder="" required>
                    </div>
                    <div class="col-md-6 mb-2">
                        <label class="form-label">Tipo de Cuenta</label>
                        <input type="text" class="form-control text-uppercase border-fx-1" name="cuenta_tipo" placeholder="" required>
                    </div>
                    <div class="col-md-6 mb-2">
                        <label class="form-label">Banco</label>
                        <input type="text" class="form-control text-uppercase border-fx-1" name="banco" placeholder="" required>
                    </div>
                    <div class="col-md-6 mb-2">
                        <label class="form-label">Numero Cuenta</label>
                        <input type="text" class="form-control text-uppercase border-fx-1" name="cuenta_numero" placeholder="" required>
                    </div>
                </div>
            </div>
            <div class="modal-footer text-right">
                <button class="btn btn-secondary" data-dismiss="modal">
                    Cancelar
                </button>
                <button type="submit" class="btn btn-blue">
                    <i class="fe fe-check-circle"></i>
                    Guardar
                </button>
            </div>
        </form>
    </div>
</div>



<script src="<?= base_url() ?>js_modules/sucursal/sucursal.detalle.js"></script>
