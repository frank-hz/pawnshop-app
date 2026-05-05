<input type="hidden" id="_id" value="<?= $tipo->id; ?>">
<div class="row px-2">
	<div class="col-xs-12 col-md-5">
		<div class="card">
			<div class="card-header">
				<div class="card-title">
					<i class="fe fe-edit-2 text-indigo"></i>
					Editar tipo de prenda
				</div>
			</div>
			<div class="card-body">
				<form id="frm_tprenda_editar">
					<div class="row">
						<div class="col-xs-12 col-md-12">
							<div class="form-group">
								<div class="form-label">Nombre</div>
								<input type="text" class="form-control border-rock" name="nombre" value="<?= $tipo->nombre; ?>">
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-xs-12 col-md-12">
							<div class="form-group">
								<div class="form-label">Descripcion</div>
								<textarea name="descripcion" cols="6" rows="4" class="form-control border-rock"><?= $tipo->descripcion; ?></textarea>
							</div>
						</div>
					</div>
				</form>
			</div>
			<div class="card-footer">
				<div class="text-right">
					<button type="submit" class="btn btn-primary">
						Guardar 
						<i class="fe fe-save"></i>
					</button>
				</div>
			</div>
		</div>
	</div>
	<div class="col-xs-12 col-md-7">
		<div class="card">
			<div class="card-header">
				<div class="card-title">
					<i class="fe fe-layout text-blue"></i>
					Formulario
				</div>
			</div>
			<div class="card-body">
				<div class="table-responsive">
					<table class="table text-nowrap table-bordered">
						<thead>
							<tr class="thead-primary">
								<th>Atributo</th>
								<th>Descripcion</th>
								<th class="text-center">Seleccion</th>
							</tr>
						</thead>
						<tbody id="tb_form">
							<tr class="text-center">
								<td colspan="3">Datos no encontrados</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>

<script src="<?= base_url() ?>js_modules/tipo_prenda/tipo_prenda.editar.js"></script>