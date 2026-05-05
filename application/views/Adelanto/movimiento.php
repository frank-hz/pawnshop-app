<div class="container">
	<div class="card">
		<div class="card-header">
			<div class="card-title">
				<a href="<?= base_url() ?>Credito/detalle/<?= $credito->id; ?>" class="btn-link text-blue">
					<?php echo $credito->codigo; ?>
				</a>&nbsp;
				Adelanto - Movimientos
			</div>
		</div>
		<div class="card-body">
			<div class="form-label">
				Entradas 
				<i class="fe fe-arrow-up-circle text-blue"></i>
			</div>
			<div class="table-responsive">
				<?php if (!empty($entrada)): ?>
					<table class="table">
						<thead>
							<tr>
								<th>Pago</th>
								<th>Fecha</th>
								<th>Monto</th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ($entrada as $e): ?>
								<tr>
									<td>
										<a href="<?= base_url() ?>/Pago/detalle/<?= $e->id_pago; ?>" class="btn-link text-blue">
											<?= $e->pago; ?>
										</a>
									</td>
									<td><?= $e->fecha; ?></td>
									<td>S/ <?= $e->monto; ?></td>
								</tr>
							<?php endforeach ?>
						</tbody>
					</table>
				<?php else: ?>
					<span>No se encontraron resultados</span>
				<?php endif ?>
			</div>
		</div>
		<div class="card-body">
			<div class="form-label">
				Salidas 
				<i class="fe fe-arrow-down-circle text-red"></i>
			</div>
			<div class="table-responsive">
				<?php if (!empty($salida)): ?>
					<table class="table">
						<thead>
							<tr>
								<th>Tipo</th>
								<th>Modo</th>
								<th>Detalle</th>
								<th>Monto</th>
								<th>Fecha</th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ($salida as $sal): ?>
								<tr>
									<td><?= $sal->tipo; ?></td>
									<td><?= $sal->modo; ?></td>
									<td><?= $sal->detalle; ?></td>
									<td>S/ <?= $sal->monto; ?></td>
									<td><?= $sal->fecha; ?></td>
								</tr>
							<?php endforeach ?>
						</tbody>
					</table>
				<?php else: ?>
					<span>No se encontraron resultados</span>
				<?php endif ?>
			</div>
		</div>
	</div>
</div>