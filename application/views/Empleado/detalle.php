<link rel="stylesheet" href="<?= base_url('assets/plugins/bootstrap-select/css/bootstrap-select.min.css') ?>">
<script src="<?= base_url('assets/plugins/bootstrap-select/js/bootstrap-select.min.js') ?>"></script>
<script>
    window.UBIGEO = {
        id: <?= $empleado->ubigeo; ?>,
        distrito: "<?= $empleado->distrito; ?>"
    }
	window.ID = "<?= $empleado->id; ?>";
	window.SEDE_ID = "<?= $empleado->sede_id; ?>";
	window.TIPO_DOC = "<?= $empleado->tipo_documento; ?>";
</script>
<div class="container">
    <div class="card m-2">
        <div class="card-header">
            <div class="card-title font-weight-bold">
                <i class="fe fe-layers text-blue"></i> 
                Detalle de Empleado
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="user-detail-row">
                    <div class="user-detail-group">
                        <div class="user-detail-avatar">
                            <?php if(!empty($empleado->imagen)): ?>
                            <img src="<?= base_url($empleado->imagen) ?>" alt="<?= $empleado->nombres; ?>">
                            <?php else: ?>
                            <svg width="800px" height="800px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path opacity="0.4" d="M22 7.81V16.19C22 19 20.71 20.93 18.44 21.66C17.78 21.89 17.02 22 16.19 22H7.81C6.98 22 6.22 21.89 5.56 21.66C3.29 20.93 2 19 2 16.19V7.81C2 4.17 4.17 2 7.81 2H16.19C19.83 2 22 4.17 22 7.81Z" fill="#292D32"/>
                                <path d="M18.4406 21.66C17.7806 21.89 17.0206 22 16.1906 22H7.81055C6.98055 22 6.22055 21.89 5.56055 21.66C5.91055 19.02 8.67055 16.97 12.0005 16.97C15.3305 16.97 18.0906 19.02 18.4406 21.66Z" fill="#292D32"/>
                                <path d="M15.5799 11.58C15.5799 13.56 13.9799 15.17 11.9999 15.17C10.0199 15.17 8.41992 13.56 8.41992 11.58C8.41992 9.60002 10.0199 8 11.9999 8C13.9799 8 15.5799 9.60002 15.5799 11.58Z" fill="#292D32"/>
                            </svg>
                            <?php endif; ?>
                        </div>
                        <div class="user-detail-info">
                            <div class="user-detail-title" id="data_nombre_apellido">
                                <?= $empleado->nombres." ".$empleado->paterno." ".$empleado->materno; ?>
                            </div>
                            <div class="user-detail-subtitle">
                                Empleado 
                                <?php if($empleado->estado === 'A'): ?>
                                    <span class="stag stag-emp-A">Activo</span>
                                <?php elseif($empleado->estado === 'I'): ?>
                                    <span class="stag stag-emp-I">Inactivo</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <div class="user-detail-options">
                        <button id="EDIT_BUTTON" data-id="<?= $empleado->id; ?>" class="btn btn-primary">
                            <i class="fe fe-edit"></i> Editar
                        </button>
                        <button id="DELETE_BUTTON" class="btn btn-outline-danger" data-id="<?= $empleado->id; ?>">
                            <i class="fe fe-trash"></i> Eliminar
                        </button> 
                        <?php if($empleado->estado === 'A'): ?>
                            <button id="STATUS_BUTTON" class="btn btn-outline-danger" data-new_estado="I" data-message="¿Cambiar el estado a INACTIVO?">Desactivar</button>
                        <?php elseif($empleado->estado === 'I'): ?>
                            <button id="STATUS_BUTTON" class="btn btn-outline-success" data-new_estado="A" data-message="¿Cambiar el estado a ACTIVO?">Activar</button>
                        <?php endif; ?>                                       
                        
                    </div>
                </div>
                <div class="col-md-12 mt-2">
                    <div class="card">
                        <div class="card-body pb-2">
                            <h5 class="mb-3 font-weight-bold">Datos Generales</h5>
                            <div class="row">
                                <div class="col-xs-12 col-lg-4 form-group">
                                    <div class="form-label">Documento</div>
                                    <span id="data_documento"><?= $empleado->documento; ?></span>
                                </div>
                                <div class="col-xs-12 col-lg-4 form-group">
                                    <div class="form-label">Tipo de Documento</div>
                                    <span id="data_tipodoc"><?= $empleado->tipo_documento; ?></span>
                                </div>
                                <div class="col-xs-12 col-lg-4 form-group">
                                    <div class="form-label">Distrito</div>
                                    <span id="data_distrito">
                                        <?= $empleado->ciudad.", ".$empleado->departamento; ?>
                                    </span>
                                </div>            
                                <div class="col-xs-12 col-lg-4 form-group">
                                    <div class="form-label">Telefono</div>
                                    <span id="data_telefono"><?= $empleado->telefono; ?></span>
                                </div> 
                                <div class="col-xs-12 col-lg-4 form-group">
                                    <div class="form-label">Email</div>
                                    <span id="data_email"><?= $empleado->email; ?></span>
                                </div>
                                <div class="col-xs-12 col-lg-4 form-group">
                                    <div class="form-label">Direccion</div>
                                    <span id="data_direccion"><?= $empleado->direccion; ?></span>
                                </div>
                                <div class="col-xs-12 col-lg-4 form-group">
                                    <div class="form-label">Sede</div>
                                    <span id="data_sede"><?= $empleado->sede_nombre; ?></span>
                                </div>
                                <div class="col-xs-12 col-lg-4 form-group">
                                    <div class="form-label">Usuario</div>
                                    <span id="data_sede"><?= $empleado->usuario; ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
    
            </div>
        </div>
    </div>
</div>



<div id="MODAL_EDIT" class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <form id="EDIT_FORM"  autocomplete="off" autocorrect="off" class="modal-content">
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
            <div id="modal_edicion_rendalert"></div>
            <div class="modal-body">
                <div class="row">
					<div class="col-xs-12 col-md-6">
						<div class="form-group">
							<label for="" class="font-weight-bold">
								Nombres 
								<span class="text-red">*</span>
							</label>
							<input type="text" class="form-control border-rock text-uppercase" name="nombre" value="<?= $empleado->nombres; ?>" required>
							<span id="feed_nombre" class="feed-red"></span>
						</div>
					</div>
					<div class="col-xs-12 col-md-6">
						<div class="form-group">
							<label for="" class="font-weight-bold">
								Ape. Paterno 
								<span class="text-red">*</span>
							</label>
							<input type="text" class="form-control border-rock text-uppercase" name="paterno" value="<?= $empleado->paterno; ?>" required>
							<span id="feed_paterno" class="feed-red"></span>
						</div>
					</div>
					<div class="col-xs-12 col-md-6">
						<div class="form-group">
							<label for="" class="font-weight-bold">
								Ape. Materno 
								<span class="text-red">*</span>
							</label>
							<input type="text" class="form-control border-rock text-uppercase" name="materno" value="<?= $empleado->materno; ?>" required>
							<span id="feed_materno" class="feed-red"></span>
						</div>
					</div>
					<div class="col-xs-12 col-md-6">
						<div class="form-group">
							<label for="" class="font-weight-bold">
								Tipo Documento 
								<span class="text-red">*</span>
							</label>
							<div id="TIPO_DOC_" class="custom-controls-stacked">
		                    </div>
		                    <span id="feed_tipodoc" class="feed-red"></span>
						</div>
					</div>
					<div class="col-xs-12 col-md-6">
						<div class="form-group">
							<label for="" class="font-weight-bold">
								Documento 
								<span class="text-red">*</span>
							</label>
							<input type="number" step="any" class="form-control border-rock text-uppercase" name="documento" value="<?= $empleado->documento; ?>" required>
							<span id="feed_documento" class="feed-red"></span>
						</div>
					</div>
					<div class="col-xs-12 col-md-6">
						<div id="UBIGEO_CONTAINER" class="form-group">
							<label for="" class="font-weight-bold">
								Ciudad 
								<span class="text-red">*</span>
							</label>
							<!-- value="<?= $empleado->ubigeo; ?>" -->
							<select id="UBIGEO_SELECT" name="ubigeo" class="form-control selectpicker" data-live-search="true"></select>
							<span id="feed_ubigeo" class="feed-red"></span>
						</div>
					</div>
					<div class="col-xs-12 col-md-6">
						<div class="form-group">
							<label for="" class="font-weight-bold">
								Direccion 
								<span class="text-red">*</span>
							</label>
							<input type="text" step="any" class="form-control border-rock text-uppercase" name="direccion" value="<?= $empleado->direccion; ?>">
							<span id="feed_direccion" class="feed-red"></span>
						</div>
					</div>
					<div class="col-xs-12 col-md-6">
						<div class="form-group">
							<label for="" class="font-weight-bold">
								Telefono 
								<span class="text-red">*</span>
							</label>
							<input type="text" class="form-control border-rock text-uppercase" name="telefono" value="<?= $empleado->telefono; ?>">
							<span id="feed_telefono" class="feed-red"></span>
						</div>
					</div>
					<div class="col-xs-12 col-md-6">
						<div class="form-group">
							<label for="" class="font-weight-bold">Email</label>
							<input type="text" class="form-control border-rock" name="email" value="<?= $empleado->email; ?>">
							<span id="feed_email" class="feed-red"></span>
						</div>
					</div>
					<div class="col-xs-12 col-md-6">
						<div class="form-group">
							<label for="" class="font-weight-bold">
								Sucursal 
								<span class="text-red">*</span>
							</label>
							<select name="sede" id="SEDE_SELECT" class="form-control"></select>
							<input type="hidden" class="form-control border-rock" name="sucursal">
							<span id="feed_sucursal" class="feed-red"></span>
						</div>
					</div>
				</div>
            </div>
            <div class="modal-footer text-right">
                <button class="btn btn-secondary" data-dismiss="modal">
                    Cancelar
                </button>
                <button id="SUBMIT_BUTTON" type="submit" class="btn btn-blue">
                    <i class="fe fe-check-circle"></i>
                    Guardar
                </button>
            </div>
        </form>
    </div>
</div>


<style>
    .user-detail-row {
        display: flex;
        justify-content: space-between;
        padding: 1rem .5rem;
        flex-wrap: wrap;
        width: 100%;
    }
    @media only screen and (min-width: 481px) and (max-width: 768px) {
        .user-detail-row {
            gap: .5rem;
        }
    }
    .user-detail-group {
        display: flex;
    }
    .user-detail-avatar {
        max-width: 100px;
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 0 .3rem;
    }
    .user-detail-avatar::after {
        /* content: '';
        width: 100%;
        height: 100%;
        background: gray;
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 99; */
        /* border: 4px solid blue; */
    }
    .user-detail-avatar img,
    .user-detail-avatar svg {
        /* border-radius: 50%; */
        border-radius: 8px;
        width: 60px;
        height: 60px;
    }
    .user-detail-info {
        display: flex;
        flex-direction: column;
        justify-content:center;
        padding: .5rem .8rem;
    }
    .user-detail-title {
        display: inline-block;
        font-size: 1.2rem;
        font-weight: 600;
    }
    .user-detail-subtitle {
        display: inline-block;
        font-weight: 600;
        font-size: .9rem;
        color: var(--blue);
    }
</style>

<script src="<?= base_url('js_modules/empleado/empleado.detalle.js') ?>"></script>