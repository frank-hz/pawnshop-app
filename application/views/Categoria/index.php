<div class="container px-0">
	<div class="card">
		<div class="card-header">
	    	<h4 class="card-title font-weight-bold">
				<i class="fe fe-flag text-blue"></i>
				Categorias
			</h4>
	  	</div>
	  	<div class="card-body">
	  		<div class="row">
				<div class="col-xs-12 col-md-12">
					<div class="row">
					  	<div class="col-xs-12 col-md-6 p-3">
					  		<label for="" class="font-weight-bold">Buscar</label>
							<input id="input_search" type="text" class="form-control" autofocus>
					  	</div>
						<div class="col-xs-12 col-md-3 form-group py-3">
							<label for="">&nbsp;</label><br>
							<select id="select_tipos" class="form-control">
								<option value="">-ELEGIR-</option>
								<option value="ELECTRO">ELECTRO</option>
								<option value="JOYA">JOYA</option>
								<option value="VEHICULO">VEHICULO</option>
								<option value="PREDIO">PREDIO</option>
							</select>
						</div>
						<div class="col-xs-12 col-md-3 form-group py-3">
							<label for="">&nbsp;</label><br>
							<button id="btn_categoria_agregar" class="btn btn-blue">
								<i class="fe fe-plus"></i>
								Nueva Categoria
							</button>
						</div>
					</div>
					<div class="table-responsive" style="max-height: 900px">
					    <table class="table card-table table-vcenter text-nowrap">
					      	<thead>
								<th class="text-center">#</th>
								<th>Nombre</th>
								<th>Tipo</th>
								<th class="text-center">Accion</th>
							</thead>
							<tbody id="dataset_categoria">
								<tr class="text-center">
									<!-- <td colspan="4">No hay categorias</td> -->
									<td colspan="4" class="text-indigo">
										Cargando
										<span class="spinner-v1-mini"></span>
									</td>
								</tr>
							</tbody>
					    </table>
				  	</div>
				</div>
	  			
	  		</div>
	  	</div>
	</div>
</div>


<!-- Modal -->
<div id="md_categoria_nuevo" class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content" >
      	<div class="modal-header">
	        <h3 class="modal-title">
	        	<i class="fe fe-plus"></i>
	        	Nueva Categoria
	        </h3>
	        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	        	<span aria-hidden="true"></span>
	        </button>
      	</div>
  		<form id="frm_registro" autocomplete="off">
      		<div class="modal-body">
      			<div class="row">
      				<div class="col-xs-12 col-md-12 my-2">
						<label class="font-weight-bold">Nombre</label>
						<input type="text" class="form-control text-uppercase" name="nombre" required>
					</div>					
					<div class="col-xs-12 col-md-12 my-2">
						<label class="font-weight-bold">Tipo</label>
						<div id="tprenda_nuevo_area" class="custom-controls-stacked">
							<span>Datos no encontrados</span>
					    </div>
					</div>
				</div>
      		</div>
      		<div class="modal-footer">      			
		        <button type="submit" class="btn btn-primary">
					Registrar&nbsp;
					<i class="fe fe-save"></i>
				</button>
				<button type="button" class="btn btn-secondary" data-dismiss="modal">
		        	Cancelar
		        	<i class="fe fe-x-circle"></i>
		        </button>
      		</div>
  		</form>
    </div>
  </div>
</div>

<div id="md_categoria_editar" class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="mdEditarLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      	<div class="modal-header">
	        <h4 class="modal-title" id="mdEditarLabel">
	        	<i class="fe fe-edit-2"></i>
	        	Editar Categoria
	        </h4>
	        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	        	<span aria-hidden="true"></span>
	        </button>
      	</div>
  		<form id="frm_editar" autocomplete="off">
      		<div class="modal-body">
      			<input type="hidden" name="id" class="form-control">
				<div class="row">
					<div class="col-xs-12 col-md-12">
						<label class="font-weight-bold">Nombre</label>
						<input type="text" class="form-control text-uppercase" name="nombre" required>
					</div>
					<div class="col-xs-12 col-md-12">
						<label class="font-weight-bold">Tipo</label>
						<div id="tprenda_edita_area" class="custom-controls-stacked">
							<span>Datos no encontrados</span>
					    </div>
					</div>
				</div>
      		</div>
      		<div class="modal-footer">
      			<button type="submit" class="btn btn-warning">
					Guardar &nbsp;
					<i class="fe fe-save"></i>
				</button>
				<button type="button" class="btn btn-secondary" data-dismiss="modal">
		        	Cancelar
		        	<i class="fe fe-x-circle"></i>
		        </button>
      		</div>
  		</form>
    </div>
  </div>
</div>


<script src="<?= base_url() ?>js_modules/controller/mix.js"></script>
<script src="<?= base_url() ?>js_modules/categoria/index.js"></script>
