<div class="container px-0">
	<div class="card">
		<div class="card-header">
	    	<h4 class="card-title font-weight-bold">
				<i class="fe fe-flag text-blue"></i>
				Categorias
			</h4>
			<div class="card-options" style="gap: 0 3px">
				<button class="btn btn-dark" id="btn_test_1">Test 1</button>
				<button class="btn btn-dark" id="btn_test_2">Test 2</button>
				<button id="btn_categoria_nuevo" class="btn btn-indigo">
					<i class="fe fe-plus"></i>
					Nueva Categoria
				</button>
			</div>
	  	</div>
	  	<div class="card-body">
	  		<div class="row">
				<div class="col-12 col-md-7 col-lg-6">
					<label for="" class="form-label">Buscar:</label>
						<input id="input_categoria_search" type="text" class="form-control" autofocus>
				</div>
				<div class="col-12 col-md-5 col-lg-6">
					<!-- <label for="" class="font-weight-bold">Tipo:</label>
					<select id="select_tipos" class="form-control">
						<option value="">-ELEGIR-</option>
					</select> -->
					<div class="form-label">Tipos:</div>
					<div id="section_filters_tipos" class="align-vertical pt-2" style="gap: 0 8px;">
						Sin Datos
					</div>
					<small id="log_select_tipos" class=""></small>
				</div>
	  		</div>
	  	</div>
		<div class="card-body p-0">
			<table class="table card-table table-center text-nowrap">
				<thead class="text-left">
					<tr>
						<th class="text-center">#</th>
						<th class="text-left">Nombre</th>
						<th class="text-left">Tipo</th>
						<th class="text-center">Accion</th>
					</tr>
				</thead>
				<tbody id="tbody_categorias">
					<tr>
						<!-- <td class="align-middle">01</td>
						<td class="align-middle text-left">LAPTOPS</td>
						<td class="align-middle text-left">ELECTRO</td>
						<td>
							<button class="btn btn-light" title="Editar">
								<i class="fe fe-edit"></i>
							</button>
							<button class="btn btn-light" title="Eliminar">
								<i class="fe fe-trash"></i>
							</button>
						</td> -->
						<td colspan="4" class="text-center">Sin Datos.</td>
					</tr>
				</tbody>
			</table>
		</div>

	</div>
</div>


<!-- Modal -->
<div id="modal_categoria_nuevo" class="modal fade bd-example-modal-lg" data-backdrop="static" tabindex="-1" role="dialog" aria-hidden="true">
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
  		<form id="form_categoria_nuevo" autocomplete="off">
			<fieldset id="fieldset_categoria_nuevo">
				<div class="modal-body py-4 px-5">
					<div class="row">
						<div class="col-xs-12 col-md-12 my-2">
						  	<label class="font-weight-bold">Nombre</label>
						  	<input type="text" class="form-control text-uppercase" name="nombre" required>
							<small id="" class="text-danger"></small>
					  </div>					
					  <div class="col-xs-12 col-md-12 my-2">
						  <label class="font-weight-bold">Tipo</label>
						  <div id="modal_nuevo_tipos_prenda" class="d-flex" style="gap: 0 3px;">
							  <span>Datos no encontrados</span>
						  </div>
					  </div>
				  </div>
				</div>
				<div class="modal-footer">      			
					<button type="button" class="btn btn-light" data-dismiss="modal">
						Cancelar
					</button>
					<button type="submit" class="btn btn-primary">
						<i class="fe fe-save"></i> &nbsp;
						Guardar
					</button>
				</div>
			</fieldset>
  		</form>
    </div>
  </div>
</div>

<div id="modal_categoria_editar" class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" data-backdrop="static" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      	<div class="modal-header">
	        <h4 class="modal-title">
	        	<i class="fe fe-edit-2"></i>
	        	Editar Categoria
	        </h4>
	        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	        	<span aria-hidden="true"></span>
	        </button>
      	</div>
  		<form id="form_categoria_editar" autocomplete="off">
      		<fieldset id="fieldset_categoria_editar">
				<div class="modal-body">
					<input type="hidden" name="id" class="form-control">
					<div class="row">
						<div class="col-xs-12 col-md-12">
							<label class="font-weight-bold">Nombre</label>
							<input type="text" class="form-control text-uppercase" name="nombre" required>
						</div>
						<div class="col-xs-12 col-md-12">
							<label class="font-weight-bold">Tipo</label>
							<div id="modal_editar_tipos_prenda" class="custom-controls-stacked">
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
			</fieldset>
  		</form>
    </div>
  </div>
</div>


<script src="<?= base_url() ?>js_modules/controller/mix.js"></script>
<script src="<?= base_url() ?>js_modules/categoria/index.js"></script>
