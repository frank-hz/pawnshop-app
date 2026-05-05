<div class="container">
	<div class="card">
		<div class="card-header">
			<div class="card-title">
				<a href="<?= base_url() ?>Credito/detalle/<?= $credito->id; ?>" class="btn-link text-blue">
					#<?php echo $credito->codigo; ?>
				</a> - Movimientos
			</div>
		</div>
		<div class="card-body">
			<div class="form-label">
				<i class="fe fe-arrow-up text-indigo"></i>
				Amortizaciones 
			</div>
			<div class="table-responsive">
				<?php if (!empty($amortizacion)): ?>
					<?php $amortiza_total = 0; ?>
					<table class="table table-bordered">
						<thead>
							<tr>
								<th>Fecha</th>
								<th>Detalle</th>
								<th>Pago</th>
								<th>Monto</th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ($amortizacion as $am): ?>
								<tr>
									<td><?= $am->fecha; ?></td>
									<td><?= $am->referencia; ?></td>
									<td>
										<a href="<?= base_url() ?>Pago/detalle/<?= $am->pago_id; ?>">
											#<?= $am->pago_serie; ?>
										</a>
									</td>
									<td>S/ <?= $am->monto; ?></td>
								</tr>
								<?php $amortiza_total = $amortiza_total + $am->monto; ?>
							<?php endforeach ?>
						</tbody>
						<tfoot>
							<tr>
								<td colspan="3" style="text-align: right;font-weight: bold;">TOTAL</td>
								<td style="font-weight: bold;">S/ <?php echo $amortiza_total; ?></td>
							</tr>
						</tfoot>		
					</table>
				<?php else: ?>
					<span>No se encontraron resultados</span>
				<?php endif ?>
			</div>
		</div>
		<div class="card-body">
			<div class="form-label">
				<i class="fe fe-arrow-down text-red"></i>
				Debitaciones 
			</div>
			<div class="table-responsive">
				<?php if (!empty($debitacion)): ?>
					<?php $debita_total = 0; ?>
					<table class="table table-bordered">
						<thead>
							<tr>
								<th>Fecha</th>
								<th>Detalle</th>
								<th>Pago</th>
								<th>Monto</th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ($debitacion as $deb): ?>
								<tr>
									<td><?= $deb->fecha; ?></td>
									<td><?= $deb->detalle; ?></td>
									<td>
										<a href="<?= base_url() ?>Pago/detalle/<?= $deb->pago_id; ?>">
											#<?= $deb->pago_serie; ?>
										</a>
									</td>
									<td>S/ <?= $deb->monto; ?></td>
								</tr>
								<?php $debita_total = floatval($debita_total + $deb->monto);?>
							<?php endforeach ?>
						</tbody>
						<tfoot>
							<tr>
								<td colspan="3" style="text-align: right;font-weight: bold;">TOTAL</td>
								<td style="font-weight: bold;">
									S/ <?php echo $debita_total; ?>
								</td>
							</tr>
						</tfoot>
					</table>
				<?php else: ?>
					<span>No se encontraron resultados</span>
				<?php endif ?>
			</div>
		</div>
	</div>
</div>