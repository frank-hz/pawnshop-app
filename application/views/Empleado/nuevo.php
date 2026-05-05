<link rel="stylesheet" href="<?= base_url('assets/plugins/bootstrap-select/css/bootstrap-select.min.css') ?>">
<script src="<?= base_url('assets/plugins/bootstrap-select/js/bootstrap-select.min.js') ?>"></script>

<div class="container">
	<form id="SUBMIT_FORM_" autocorrect="off" autocomplete="off">
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
							<input type="text" class="form-control border-rock text-uppercase" name="nombre" required>
							<span id="feed_nombre" class="feed-red"></span>
						</div>
					</div>
					<div class="col-xs-12 col-md-4">
						<div class="form-group">
							<label for="" class="font-weight-bold">
								Ape. Paterno 
								<span class="text-red">*</span>
							</label>
							<input type="text" class="form-control border-rock text-uppercase" name="paterno" required>
							<span id="feed_paterno" class="feed-red"></span>
						</div>
					</div>
					<div class="col-xs-12 col-md-4">
						<div class="form-group">
							<label for="" class="font-weight-bold">
								Ape. Materno 
								<span class="text-red">*</span>
							</label>
							<input type="text" class="form-control border-rock text-uppercase" name="materno" required>
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
							<input type="number" step="any" class="form-control border-rock text-uppercase" name="documento" required>
							<span id="feed_documento" class="feed-red"></span>
						</div>
					</div>
					<div class="col-xs-12 col-md-4">
						<div id="UBIGEO_CONTAINER" class="form-group">
							<label for="" class="font-weight-bold">
								Ciudad 
								<span class="text-red">*</span>
							</label>
							<select id="UBIGEO_SELECT" name="ubigeo" class="form-control selectpicker" data-live-search="true" title="Buscar.."></select>
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
					<div class="col-xs-12 col-md-4">
						<div class="form-group">
							<label for="" class="font-weight-bold">
								Sucursal 
								<span class="text-red">*</span>
							</label>
							<!-- <input type="text" class="form-control text-uppercase border-rock" name="sede"> -->
							<select name="sede" id="SEDE_SELECT" class="form-control"></select>
							<input type="hidden" class="form-control border-rock" name="sucursal">
							<span id="feed_sucursal" class="feed-red"></span>
						</div>
					</div>
				</div>
			</div>
			<div class="card-footer">
				<button id="SUBMIT_BUTTON" class="btn btn-blue" type="submit">
					Registrar Empleado
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



<script src="<?= base_url() ?>js_modules/empleado/empleado.nuevo.js?v=4568"></script>