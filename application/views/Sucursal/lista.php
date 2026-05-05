<div class="container">
	<!-- <pre>
		<?php print_r($sucursales); ?>
	</pre> -->
	<div class="card">
		<div class="card-header">
	    	<h4 class="card-title font-weight-bold">
				<i class="fe fe-compass text-blue"></i>
				Sucursales
			</h4>
			<div class="card-options">
				<a href="<?= base_url() ?>Sucursal/nuevo" class="btn btn-blue text-light">
			  		Nueva Sucursal 
			  		<i class="fe fe-plus"></i>
			  	</a>
			</div>
	  	</div>
	  	<div class="card-body">
			<div class="table-responsive">
			    <table class="table card-table table-vcenter text-nowrap">
			      	<thead>
						<th class="text-center">#</th>
						<th>Nombre</th>
						<th>Ciudad</th>
						<th>Direccion</th>
						<th class="text-center">Estado</th>
						<th class="text-center">Accion</th>
					</thead>
					<tbody id="sucursalLista">
						<!-- <tr class="text-center">
							<td colspan="6">No se encontraron resultados.</td>
						</tr> -->
						<?php if (!empty($sucursales)): ?>
							<?php foreach ($sucursales as $ind => $s): ?>
								<tr>
									<td><?php echo $ind+1; ?></td>
									<td><?php echo $s->nombre; ?></td>
									<td><?php echo $s->ciudad; ?></td>
									<td><?php echo $s->direccion; ?></td>
									<td class="text-center"><?php echo $s->estado; ?></td>
									<td class="text-center">
										<a class="btn btn-primary" href="<?= base_url() ?>Sucursal/detalle/<?= $s->id; ?>" title="Ver Detalle">
	                                    	<i class="fe fe-layers"></i> 
	                                    </a>
									</td>
								</tr>
							<?php endforeach ?>
						<?php else: ?>
							<tr class="text-center">
								<td colspan="6">No se encontraron resultados.</td>
							</tr>
						<?php endif ?>
					</tbody>
			    </table>
		  	</div>
	  		
	  	</div>
	</div>
</div>

<script src="<?= base_url() ?>js_modules/sucursal/sucursal.lista.js"></script>