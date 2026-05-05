<div class="container">
	<div class="card">
		<div class="card-header">
	    	<h4 class="">
				<i class="fe fe-user text-blue"></i>
				Usuarios
			</h4>
	  	</div>
	  	<div class="row p-3">
	  		<!-- <div class="col-xs-12 col-md-8 ">
		  		<label for="" class="font-weight-bold">Buscar</label>
				<input id="inputBuscar" type="text" class="form-control" autofocus>
		  	</div> -->
		  	<div class="col-xs-12 col-md-4">
		  		<label for="" class="font-weight-bold">&nbsp;</label><br>
				<a href="<?= base_url() ?>Empleado/nuevo" class="btn btn-primary">
					<i class="fe fe-plus"></i>
					Nuevo Empleado
				</a>
		  	</div>
	  	</div>
		<div class="table-responsive">
		    <table class="table card-table table-vcenter text-nowrap">
		      	<thead>
		        	<tr>
			        	<th class="text-center w-1">#</th>
						<th>Empleado</th>
						<th>Usuario</th>
						<th>Rol</th>
						<th>Estado</th>
						<th></th>
		        	</tr>
		      	</thead>
		      	<tbody id="tdata__usuario">
					<tr class="text-center">
						<td colspan="5">No se encontraron resultados.</td>
					</tr>
		      	</tbody>
		    </table>
	  </div>
	</div>
</div>

<script src="<?= base_url() ?>js_modules/usuario/usuario.lista.js"></script>