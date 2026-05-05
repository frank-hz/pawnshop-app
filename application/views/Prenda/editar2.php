<div class="container">
	<form id="frm_prenda_editar" autocomplete="off" class="card">
		<div class="card-header">
			<h4>
				<i class="fe fe-edit-2 text-blue"></i>
				Editar Prenda 2
			</h4>
		</div>
		<div id="prendaedt_sysmssg" class="p-0 m-0" style="display: none;"></div>
		<div class="card-body">
			<input type="hidden" value="<?= $prenda->id; ?>" name="id">
			<div class="row py-2">
				<div class="col-xs-12 col-md-8">
					<label for="" class="form-label font-weight-bold">Descripcion</label>
					<input type="text" class="form-control text-uppercase" name="descripcion" value="<?= $prenda->descripcion; ?>" required>
				</div>
				<div class="col-xs-12 col-md-4">
					<label for="" class="form-label font-weight-bold">Estado</label>
					<input type="number" step="any" class="form-control text-center" name="valoracion" value="<?= $prenda->valoracion; ?>">
				</div>
			</div>
			<div class="row">
				<div class="col-xs-12 col-md-4">
					<label for="" class="form-label font-weight-bold">Tipo</label>
					<input type="text" name="tipo" class="form-control" value="<?= $prenda->tipo; ?>" readonly>
				</div>
				<div class="col-xs-12 col-md-4">
					<label for="" class="form-label font-weight-bold">Categoria</label>
					<div class="input-icon mb-3">
						<select name="categoria" class="form-control custom-select" value="<?= $prenda->categoria; ?>">
							<?php if (!empty($categoria)): ?>
								<?php foreach ($categoria as $cat): ?>
									<?php if ($cat->tipo === $prenda->tipo): ?>									
										<?php if ($cat->id === $prenda->categoria_id): ?>
											<option value="<?= $cat->id; ?>" selected>
												<?php echo $cat->nombre; ?>
											</option>
										<?php else: ?>
											<option value="<?= $cat->id; ?>">
												<?php echo $cat->nombre; ?>
											</option>
										<?php endif ?>
									<?php endif ?>
								<?php endforeach ?>
							<?php endif ?>
						</select>
						<span class="input-icon-addon">
                      		<i class="fe fe-align-justify"></i>
                        </span>
					</div>
				</div>
			</div>

			<?php if ($prenda->tipo === 'ELECTRO'): ?>
			<div id="section_electro" class="row py-2">
				<div id="inp_serial" class="col-xs-12 col-md-4">
					<label for="" class="form-label font-weight-bold">Serial</label>
					<input type="text" name="serial" class="form-control" value="<?= $prenda->serial; ?>" required>
				</div>
				<div id="inp_marca" class="col-xs-12 col-md-4">
					<label for="" class="form-label font-weight-bold">Marca</label>
					<input type="text" name="marca" class="form-control" value="<?= $prenda->marca; ?>">
				</div>
				<div id="inp_modelo" class="col-xs-12 col-md-4">
					<label for="" class="form-label font-weight-bold">Modelo</label>
					<input type="text" name="modelo" class="form-control" value="<?= $prenda->modelo; ?>">
				</div>		
			</div>

			<?php elseif ($prenda->tipo === 'JOYA'): ?>
			<div id="section_joya" class="row py-2">
				<div class="col-xs-12 col-md-4">
					<label for="" class="form-label font-weight-bold">Quilate</label>
					<input type="text" name="quilate" class="form-control" value="<?= $prenda->quilate; ?>" required>
				</div>
				<div class="col-xs-12 col-md-4">
					<label for="" class="form-label font-weight-bold">Peso</label>
					<input type="text" name="peso" class="form-control" value="<?= $prenda->peso; ?>" required>
				</div>
			</div>

			<?php elseif ($prenda->tipo === 'VEHICULO'): ?>
			<div id="section_vehiculo" class="row">
				<div class="col-xs-12 col-md-4 mt-1">
					<label for="" class="form-label font-weight-bold">Marca</label>
					<input type="text" name="marca" class="form-control" value="<?= $prenda->marca; ?>" required>
				</div>
				<div class="col-xs-12 col-md-4 mt-1">
					<label for="" class="form-label font-weight-bold">Modelo</label>
					<input type="text" name="modelo" class="form-control" value="<?= $prenda->modelo; ?>" required>
				</div>
				<div class="col-xs-12 col-md-4 mt-1">
					<label for="" class="form-label font-weight-bold">Año</label>
					<input type="text" name="anio" class="form-control" value="<?= $prenda->anio; ?>" required>
				</div>
				<div class="col-xs-12 col-md-4 mt-1">
					<label for="" class="form-label font-weight-bold">Placa</label>
					<input type="text" name="placa" class="form-control" value="<?= $prenda->placa; ?>" required>
				</div>
				<div class="col-xs-12 col-md-4 mt-1">
					<label for="" class="form-label font-weight-bold">Kilometraje</label>
					<input type="text" name="kilometraje" class="form-control" value="<?= $prenda->kilometraje; ?>">
				</div>
			</div>
				
			<?php endif ?>
				
			<div class="row py-2">
				<div class="col-xs-12 col-md-6 form-group">
					<label for="" class="form-label font-weight-bold">Observacion</label>
					<textarea name="observacion" cols="10" rows="3" class="form-control text-uppercase"><?= $prenda->observacion; ?></textarea>
				</div>
				<div class="col-xs-12 col-md-6 form-group">
					<label for="" class="form-label font-weight-bold">Comentarios</label>
					<textarea name="comentario" cols="10" rows="3" class="form-control text-uppercase"><?= $prenda->comentario; ?></textarea>
				</div>
			</div>
		</div>
		<div class="card-footer">
			<a class="btn-link mx-2" href="<?= base_url() ?>Prenda/detalle/<?= $prenda->id; ?>">
				<i class="fe fe-layers"></i>
				Ver detalle
			</a>
			<button class="btn btn-blue">
				Guardar Cambios
				<i class="fe fe-save"></i>
			</button>
		</div>
	</form>
</div>


<script src="<?= base_url() ?>js_modules/prenda/prenda.editar2.js"></script>