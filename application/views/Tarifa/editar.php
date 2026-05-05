<div class="container">
	<form id="form_tarifa_editar" autocomplete="off">
		<div class="card">
			<div class="card-header">
				<h4>
					<i class="fe fe-edit-2"></i>
					Editar Tarifa
				</h4>
			</div>
			<div class="card-body">
				<input type="hidden" value="<?= $tarifa->tar_codigo; ?>" name="codigo">
				<div class="row">
					<div class="col-xs-12 col-md-12 form-group">
						<label for="" class="font-weight-bold">Descripcion</label>
						<p>
							Minimo: S/ <?php echo $tarifa->tar_minimo; ?> - 
						Maximo: S/ <?php echo $tarifa->tar_maximo; ?>
						</p>
					</div>
					<div class="col-xs-12 col-md-4 form-group">
						<label for="" class="font-weight-bold">Interes Semanal:</label>
						<input type="number" class="form-control" name="interes" step="any" value="<?= $tarifa->tar_interes; ?>" required>
					</div>
					<div class="col-xs-12 col-md-4 form-group">
						<label for="" class="font-weight-bold">Penalidad por Mora:</label>
						<input type="number" class="form-control" name="mora" step="any" value="<?= $tarifa->tar_mora; ?>" required>
					</div>
					<div class="col-xs-12 col-md-4 form-group">
						<label for="" class="font-weight-bold">Tipo:</label>
						<select name="tipo" class="form-control" required>
							<?php if ($tarifa->tar_tipo === 'P'): ?>
								<option value="P" selected>Porcentual</option>
								<option value="F">Fijo</option>
							<?php else: ?>
								<option value="P">Porcentual</option>
								<option value="F" selected>Fijo</option>
							<?php endif ?>
						</select>
					</div>
				</div>
			</div>
			<div class="card-footer text-right">
				<div class="d-flex">
                	<a href="<?= base_url() ?>Tarifa" class="btn btn-link">
                		<i class="fe fe-arrow-left"></i>
                		Ir a la lista
                	</a>

                    <button type="submit" class="btn btn-primary ml-auto">
                    	Guardar
                    	<i class="fe fe-save"></i>
                    </button>
              	</div>
			</div>
		</div>
	</form>
</div>
<script src="<?= base_url() ?>js_modules/tarifa/tarifa.editar.js"></script>