<div class="container">
	<div class="card">
		<div class="card-header">
			<h4 class="card-title font-weight-bold">
				<a href="<?= base_url()?>Credito/detalle/<?= $credito->id; ?>">
					<span class="text-blue">#<?php echo $credito->codigo; ?></span>
				</a>
				| Administrar Mora
			</h4>
			<input type="hidden" id="hash_id" value="<?= $credito->id; ?>">
		</div>
		<div class="card-body">
			<div class="table-responsive">
				<table class="table text-nowrap">
					<thead>
						<tr>
							<th>Cuota</th>
							<th>Vencimiento</th>
							<th>Dias</th>
							<th>Monto</th>
							<th>Estado</th>
							<th>Accion</th>
						</tr>
					</thead>
					<tbody id="tb_pendiente">
						<tr class="text-center">
							<td colspan="6">Resultados no encontrados</td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>	
	</div>
</div>

<div id="md_mora_editar" class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      	<div class="modal-header">
	        <h4 class="modal-title">
	        	<i class="fe fe-edit-2 text-blue"></i>
	        	Editar mora
	        </h4>
	        <div class="card-options">
		        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
		        	<span aria-hidden="true"></span>
		        </button>
	        </div>
      	</div>
  		<form id="form_mora_editar" autocomplete="off" autocorrect="off">
  			<input type="hidden" name="id">
      		<div class="modal-body">
				<div class="row">
					<div class="col-xs-12 col-md-6">
						<div class="form-group">
							<label for="" class="font-weight-bold">Dias atrasados</label>
							<input type="number" name="dias" class="form-control text-center" min="1" required="">
						</div>
					</div>
					<div class="col-xs-12 col-md-6">
						<div class="form-group">
							<label for="" class="font-weight-bold">Monto</label>
							<div class="input-group">
	                          	<span class="input-group-prepend" id="basic-addon2">
	                            	<span class="input-group-text">S/</span>
	                          	</span>
	                        	<input type="number" class="form-control" name="monto" aria-label="Recipient's username" aria-describedby="basic-addon2" min="1" required="">
	                        </div>
						</div>
					</div>
				</div>
      		</div>
      		<div class="modal-footer text-right">
  				<button class="btn btn-secondary" data-dismiss="modal">
  					Cancelar
  				</button>
  				<button type="submit" class="btn btn-blue">
  					Aplicar Cambios
  					<i class="fe fe-save"></i>
  				</button>
      		</div>
		</form>
    </div>
  </div>
</div>

<script src="<?= base_url() ?>js_modules/credito/credito.mora.js"></script>