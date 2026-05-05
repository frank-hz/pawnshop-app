<div class="container">
	<div class="card">
		<div class="card-header">
			<div class="card-title font-weight-bold">
				<i class="fe fe-clipboard text-indigo"></i>
				Tipos de Prenda
			</div>
		</div>
		<div class="card-body">
			<div class="mb-2 btn-list">
				<button id="btn_tprenda_nuevo" class="btn btn-blue">
					Nuevo tipo de prenda
					<i class="fe fe-plus"></i>
				</button>
			</div>
			<div class="table-responsive">
				<table class="table text-nowrap table-bordered">
					<thead>
						<tr class="thead-primary">
							<th>Nombre</th>
							<th>Descripcion</th>
							<th class="text-center">Accion</th>
						</tr>
					</thead>
					<tbody id="tb_tprenda">
						<tr class="text-center">
							<td colspan="3">Datos no encontrados</td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>


<div id="md_tprenda_registro" class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      	<div class="modal-header">
	        <h4 class="modal-title">
	        	<i class="fe fe-file-plus text-blue"></i>
	        	Nuevo Tipo de Prenda
	        </h4>
	        <div class="card-options">
		        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
		        	<span aria-hidden="true"></span>
		        </button>
	        </div>
      	</div>
  		<form id="frm_tprenda_registro" autocomplete="off" autocorrect="off">
      		<div class="modal-body">
				<div class="row">
					<div class="col-xs-12 col-md-12">
						<div class="form-group">
							<label for="" class="font-weight-bold">Nombre</label>
							<input type="text" class="form-control border-rock text-uppercase" name="nombre">
						</div>
					</div>
					<div class="col-xs-12 col-md-12">
						<div class="form-group">
							<label for="" class="font-weight-bold">Descripcion</label>
							<textarea name="descripcion" cols="10" rows="5" class="form-control border-rock text-uppercase"></textarea>
						</div>
					</div>
				</div>
      		</div>
      		<div class="modal-footer text-right">
  				<button class="btn btn-secondary" data-dismiss="modal">
  					Cancelar
  				</button>
  				<button type="submit" class="btn btn-blue">
  					Guardar
  					<i class="fe fe-save"></i>
  				</button>
      		</div>
		</form>
    </div>
  </div>
</div>

<script src="<?= base_url() ?>js_modules/tipo_prenda/tipo_prenda.lista.js"></script>