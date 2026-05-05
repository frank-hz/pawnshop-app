<div class="container">
	<div class="card">
		<div class="card-header">
			<h4>
				<i class="fe fe-edit-2 text-blue"></i>
				Editar Prenda
			</h4>
		</div>
		<div class="card-body">
			<form id="frm_prenda_editar" autocomplete="off">
				<input type="hidden" value="<?= $prenda->id; ?>" name="id">
				<div class="row">
					<div class="col-xs-12 col-md-8">
						<label for="" class="form-label font-weight-bold">Descripcion</label>
						<input type="text" class="form-control text-uppercase" name="descripcion" value="<?= $prenda->descripcion; ?>">
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
						<select name="categoria" class="form-control" value="<?= $prenda->categoria; ?>">
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
					</div>
				</div>

				<?php if (strtoupper($prenda->tipo) === 'JOYA'): ?>
					<div class="row">
						<div class="col-xs-12 col-md-4 form-group">
							<label for="" class="form-label font-weight-bold">Quilates</label>
							<input type="text" class="form-control text-uppercase" name="quilate" value="<?= $prenda->quilate; ?>">
						</div>
						<div class="col-xs-12 col-md-4 form-group">
							<label for="" class="form-label font-weight-bold">Peso</label>
							<input type="text" class="form-control text-uppercase" name="peso" value="<?= $prenda->peso; ?>">
						</div>
					</div>
				<?php elseif (strtoupper($prenda->tipo) === 'ELECTRO'): ?>
					<div class="row">
						<div class="col-xs-12 col-md-4 form-group">
							<label for="" class="form-label font-weight-bold">Serie</label>
							<input type="text" class="form-control text-uppercase" name="serial" value="<?= $prenda->serial; ?>">
						</div>
						<div class="col-xs-12 col-md-4 form-group">
							<label for="" class="form-label font-weight-bold">Marca</label>
							<input type="text" class="form-control text-uppercase" name="marca" value="<?= $prenda->marca; ?>">
						</div>
						<div class="col-xs-12 col-md-4 form-group">
							<label for="" class="form-label font-weight-bold">Modelo</label>
							<input type="text" class="form-control text-uppercase" name="modelo" value="<?= $prenda->modelo; ?>">
						</div>
					</div>
				<?php elseif (strtoupper($prenda->tipo) === 'VEHICULO'): ?>
					<div class="row">
						<div class="col-xs-12 col-md-4 form-group">
							<label for="" class="form-label font-weight-bold">Año</label>
							<input type="text" class="form-control text-uppercase" name="anio" value="<?= $prenda->anio; ?>">
						</div>
						<div class="col-xs-12 col-md-4 form-group">
							<label for="" class="form-label font-weight-bold">Placa</label>
							<input type="text" class="form-control text-uppercase" name="placa" value="<?= $prenda->placa; ?>">
						</div>
						<div class="col-xs-12 col-md-4 form-group">
							<label for="" class="form-label font-weight-bold">Marca</label>
							<input type="text" class="form-control text-uppercase" name="marca" value="<?= $prenda->marca; ?>">
						</div>
						<div class="col-xs-12 col-md-4 form-group">
							<label for="" class="form-label font-weight-bold">Modelo</label>
							<input type="text" class="form-control text-uppercase" name="modelo" value="<?= $prenda->modelo; ?>">
						</div>
						<div class="col-xs-12 col-md-4 form-group">
							<label for="" class="form-label font-weight-bold">Kilometraje</label>
							<input type="text" class="form-control text-uppercase" name="kilometraje" value="<?= $prenda->kilometraje; ?>">
						</div>
					</div>
				<?php endif ?>
				<div class="row">
					<div class="col-xs-12 col-md-6 form-group">
						<label for="" class="form-label font-weight-bold">Observacion</label>
						<textarea name="observacion" cols="10" rows="3" class="form-control text-uppercase"><?= $prenda->observacion; ?></textarea>
					</div>
					<div class="col-xs-12 col-md-6 form-group">
						<label for="" class="form-label font-weight-bold">Comentarios</label>
						<textarea name="comentario" cols="10" rows="3" class="form-control text-uppercase"><?= $prenda->comentario; ?></textarea>
					</div>
				</div>
				<div class="">
					<a class="btn btn-secondary" href="<?= base_url() ?>Prenda/detalle/<?= $prenda->id; ?>">
						<i class="fe fe-layers"></i>
						Ir al detalle
					</a>
					<button class="btn btn-blue">
						Guardar Cambios
						<i class="fe fe-save"></i>
					</button>
				</div>
			</form>
		</div>
	</div>
</div>


<script src="<?= base_url() ?>js_modules/prenda/prenda.editar.js"></script>
