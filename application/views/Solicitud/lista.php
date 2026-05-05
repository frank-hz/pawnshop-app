<div class="container">
	<div class="card">
		<div class="card-header">
	    	<h4 class="card-title font-weight-bold">
				<i class="fe fe-share-2 text-blue"></i>
				Solicitudes
			</h4>
	  	</div>
	  	<div class="card-body">
			<div class="table-responsive">
			    <table class="table card-table table-bordered table-vcenter text-nowrap">
			      	<thead>
			      		<tr class="">
							<th>Solicitante</th>
							<th>Telefono</th>
							<th>Tipo de Prenda</th>
							<th>Descripcion</th>
							<th>Monto</th>
							<th class="text-center">Accion</th>
			      		</tr>
					</thead>
					<tbody id="tb_solicitud"></tbody>
			    </table>
		  	</div>
	  		
	  	</div>
	</div>
</div>

<div id="md_solicitud_detalle" class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content" >
      	<div class="modal-header">
	        <h3 class="modal-title">
	        	<i class="fe fe-shared-2"></i>
	        	Detalle de Solicitud
	        </h3>
	        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	        	<span aria-hidden="true"></span>
	        </button>
      	</div>
      		<div class="modal-body">
      			<div class="row">
      				<div class="col-xs-12 col-md-6 my-2">
						<label class="font-weight-bold">Solicitante</label>
						<p id="detalle_cliente"></p>
					</div>					
					<div class="col-xs-12 col-md-6 my-2">
						<label class="font-weight-bold">Telefono</label>
						<p id="detalle_telefono"></p>
					</div>
					<div class="col-xs-12 col-md-6 my-2">
						<label class="font-weight-bold">Fecha de Solicitud</label>
						<p id="detalle_fecha"></p>
					</div>
					<div class="col-xs-12 col-md-6 my-2">
						<label class="font-weight-bold">Sucursal</label>
						<p id="detalle_sucursal"></p>
					</div>
					<div class="col-xs-12 col-md-6 my-2">
						<label class="font-weight-bold">Tipo de Prenda</label>
						<p id="detalle_tipo"></p>
					</div>
					<div class="col-xs-12 col-md-12 my-2">
						<label class="font-weight-bold">Descripcion de la prenda</label>
						<p id="detalle_prenda"></p>
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
    </div>
  </div>
</div>

<script src="<?= base_url() ?>js_modules/solicitud/solicitud.lista.js"></script>