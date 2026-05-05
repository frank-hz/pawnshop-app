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
	<form id="SUBMIT_FORM_" autocorrect="off" autocomplete="off">
		<div class="card">
			<div class="card-header">
				<h4>
					<i class="fe fe-edit text-blue"></i>
					Edicion Empleado
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
							<input type="text" class="form-control border-rock text-uppercase" name="nombre" value="<?= $empleado->nombres; ?>" required>
							<span id="feed_nombre" class="feed-red"></span>
						</div>
					</div>
					<div class="col-xs-12 col-md-4">
						<div class="form-group">
							<label for="" class="font-weight-bold">
								Ape. Paterno 
								<span class="text-red">*</span>
							</label>
							<input type="text" class="form-control border-rock text-uppercase" name="paterno" value="<?= $empleado->paterno; ?>" required>
							<span id="feed_paterno" class="feed-red"></span>
						</div>
					</div>
					<div class="col-xs-12 col-md-4">
						<div class="form-group">
							<label for="" class="font-weight-bold">
								Ape. Materno 
								<span class="text-red">*</span>
							</label>
							<input type="text" class="form-control border-rock text-uppercase" name="materno" value="<?= $empleado->materno; ?>" required>
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
							<div id="TIPO_DOC_" class="custom-controls-stacked">
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
							<input type="number" step="any" class="form-control border-rock text-uppercase" name="documento" value="<?= $empleado->documento; ?>" required>
							<span id="feed_documento" class="feed-red"></span>
						</div>
					</div>
					<div class="col-xs-12 col-md-4">
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
				</div>
				<div class="row">
					<div class="col-xs-12 col-md-4">
						<div class="form-group">
							<label for="" class="font-weight-bold">
								Direccion 
								<span class="text-red">*</span>
							</label>
							<input type="text" step="any" class="form-control border-rock text-uppercase" name="direccion" value="<?= $empleado->direccion; ?>">
							<span id="feed_direccion" class="feed-red"></span>
						</div>
					</div>
					<div class="col-xs-12 col-md-4">
						<div class="form-group">
							<label for="" class="font-weight-bold">
								Telefono 
								<span class="text-red">*</span>
							</label>
							<input type="text" class="form-control border-rock text-uppercase" name="telefono" value="<?= $empleado->telefono; ?>">
							<span id="feed_telefono" class="feed-red"></span>
						</div>
					</div>
					<div class="col-xs-12 col-md-4">
						<div class="form-group">
							<label for="" class="font-weight-bold">Email</label>
							<input type="text" class="form-control border-rock" name="email" value="<?= $empleado->email; ?>">
							<span id="feed_email" class="feed-red"></span>
						</div>
					</div>
					<div class="col-xs-12 col-md-4">
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
			<div class="card-footer">
				<a href="<?= base_url('empleado/detalle/'.$empleado->id) ?>" class="btn">
					Volver
				</a>
				<button id="SUBMIT_BUTTON" class="btn btn-dark" type="submit">
					Guardar Cambios
					<i class="fe fe-save"></i>
				</button>
			</div>
		</div>
	</form>
</div>

<style>
	.bg-transp {
		background: transparent;
	}
</style>



<script src="<?= base_url() ?>js_modules/empleado/empleado.edicion.js?v=4322"></script>