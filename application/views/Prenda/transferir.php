<div class="container">
	<form id="frm_transferencia" autocomplete="off">
		<div class="card">
			<div class="card-header">
				<h4 class="card-title">
					<i class="fas fa-exchange-alt text-blue"></i>
					Transferencia de Prenda
				</h4>
			</div>
			<div class="card-body">
				<div class="d-flex mb-4">
					<div class="col-xs-12 col-md-12">
						<label for="" class="font-weight-bold">Prenda:</label>
						<input type="hidden" value="<?= $prenda->id; ?>" name="prenda">
						<input type="text" class="form-control" value="<?= $prenda->descripcion." - ".$prenda->tipo." - ".$prenda->categoria; ?>" readonly>
					</div>
				</div>
				<div class="d-flex">
					<div class="col-xs-12 col-md-6 form-group">
						<label for="" class="font-weight-bold">Propietario Actual</label>
						<input type="text" class="form-control" value="<?= $prenda->cliente; ?>" readonly="">
						<input type="hidden" class="form-control" name="propietario" value="<?= $prenda->cliente_id; ?>">
					</div>
					<div class="col-xs-12 col-md-6">
						<label for="" class="font-weight-bold">Nuevo Propietario</label>
						<div class="input-group">
	                        <input type="text" class="form-control text-uppercase" name="clienteNombre" required>
	                        <input type="hidden" class="form-control" name="cliente">
	                        <span class="input-group-append">
	                        	<button id="btn_md_cliente" class="btn btn-indigo" type="button">
	                        		<i class="fe fe-search"></i>
	                        		Buscar
	                        	</button>
	                        </span>
	                    </div>
					</div>
				</div>
				<div class="col-xs-12 col-md-12">
					<label for="" class="font-weight-bold">Razon / Motivo de la transferencia:</label>
					<textarea name="motivo" cols="10" rows="4" class="form-control text-uppercase" maxlength="50"></textarea>
				</div>
			</div>
			<div class="card-footer">
				<button class="btn btn-blue">
					Transferir Prenda
					<i class="fe fe-send"></i>
				</button>
				<a class="btn btn-secondary" href="<?= base_url() ?>Prenda/detalle/<?= $prenda->id; ?>">
					<i class="fe fe-layers"></i>
					Ir al detalle
				</a>
			</div>
		</div>
	</form>
</div>

<div id="md_cliente" class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-hidden="true">
  	<div class="modal-dialog modal-lg" role="document">
	    <div class="modal-content">
	      	<div class="modal-header">
		        <h4 class="modal-title">
		        	<i class="fe fe-user text-indigo"></i>
					Nuevo Propietario
		        </h4>
		        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
		        	<span aria-hidden="true"></span>
		        </button>
	      	</div>
	      	<div class="modal-body">
	      		<div class="row">
					<div class="col-xs-12 col-md-12">
						<div class="d-flex">
							<div class="col-xs-12 col-md-4">
								<div class="form-group">
									<label class="form-label">Buscar por:</label>
									<select id="md_cliente_filtro" class="form-control">
										<option value="nombre">Nombre</option>
										<option value="documento">Documento</option>
									</select>
								</div>	
							</div>
							<div class="col-xs-12 col-md-8">
								<label for="">Buscar</label>
								<input id="md_cliente_input" type="text" class="form-control" autofocus="">
							</div>
						</div>
						<div class="table-responsive">
							<table class="table">
								<thead>
									<tr>
										<th>Nombres y Apellidos</th>
										<th>Documento</th>
										<th></th>
									</tr>
								</thead>
								<tbody id="md_tb_cliente">
									<tr class="text-center">
										<td colspan="3">Datos no encontrados.</td>
									</tr>
								</tbody>
							</table>
						</div>
					</div>
				</div>
	      	</div>
	      	<div class="modal-footer">
		        <button type="button" class="btn btn-danger" data-dismiss="modal">
		        	Salir
		        	<i class="fe fe-corner-right-down"></i>
		        </button>
		    </div>
	    </div>
  	</div>
</div>

<script src="<?= base_url() ?>js_modules/prenda/prenda.transferir2.js"></script>
