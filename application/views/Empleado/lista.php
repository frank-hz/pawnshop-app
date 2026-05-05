<style>
    th.sticky, td.sticky {
      position: sticky;
      right: 0;
      background-color: #fff; /* Fondo para que no se superponga */
      z-index: 1; /* Asegura que se mantenga encima de las otras celdas */
    }
</style>
<div class="my-2">
	<div class="col-xs-12 col-md-12">
		<div class="card">
			<div class="card-header">
		    	<h4 class="card-title font-weight-bold">
					<i class="fe fe-user text-blue"></i>
					Empleados
				</h4>
				<div class="card-options">
			        <div class="">
			        	<a href="<?= base_url() ?>Empleado/nuevo" class="btn btn-primary">
							<i class="fe fe-plus"></i>
							Nuevo Empleado
						</a>
			        </div>
			    </div>
		  	</div>
		  	<div class="card-body">
		  		<!-- <div class="row">
		          	<div class="col-sm-6 col-md-4 col-lg-3">
			            <div class="card p-3">
		                  	<div class="d-flex align-items-center">
			                    <span class="stamp stamp-md bg-blue mr-3">
			                      	<i class="fe fe-database"></i>
			                    </span>
			                    <div>
			                      	<h4 class="m-0"><a href="javascript:void(0)" id="info_total">???</a></h4>
			                      	<small class="text-muted">Registros totales</small>
			                    </div>
		                  	</div>
		                </div>
		          	</div>
		        	<div class="col-sm-6 col-md-4 col-lg-3">
			            <div class="card p-3">
		                  	<div class="d-flex align-items-center">
			                    <span class="stamp stamp-md bg-tree mr-3">
			                      	<i class="fe fe-calendar"></i>
			                    </span>
			                    <div>
			                      	<h4 class="m-0"><a href="javascript:void(0)" id="info_hoy">???</a></h4>
			                      	<small class="text-muted">Registrados hoy</small>
			                    </div>
		                  	</div>
		                </div>
		          	</div>
		        </div> -->
		        <div class="d-flex">
		        	<div class="col-xs-12 col-md-4 col-lg-3">
						<div class="form-group">
							<label class="form-label">Cant. Resultados</label>
							<select id="inp_limite" class="form-control custom-select text-center border-rock">
								<option value="10">10</option>
								<option value="20">20</option>
								<option value="50">50</option>
							</select>
						</div>
				  	</div>
				  	<div class="col-xs-12 col-md-4">
				  		<div class="form-group">
	                        <div class="form-label">Estilo de tabla</div>
	                        <label class="custom-switch">
	                          	<input type="checkbox" id="check_darkmode" class="custom-switch-input">
	                          	<span class="custom-switch-indicator"></span>
	                          	<span class="custom-switch-description">
	                          		Modo Oscuro
	                          	</span>
	                        </label>
                      	</div>
				  	</div>
		        </div>
			  	<div class="d-flex">
				  	<div class="col-xs-12 col-md-6 col-lg-4">
		              	<div class="form-group">
				  			<label class="form-label">Definir Filtro</label>
				  			<div class="selectgroup w-100">
		                      	<label class="selectgroup-item">
		                        	<input type="radio" name="tipo_filtro" value="nombres" class="selectgroup-input" checked>
		                        	<span class="selectgroup-button">Nombres</span>
		                      	</label>
		                      	<label class="selectgroup-item">
		                        	<input type="radio" name="tipo_filtro" value="doc" class="selectgroup-input">
		                        	<span class="selectgroup-button">Documento</span>
		                      	</label>
		                      	<!-- <label class="selectgroup-item">
		                        	<input type="radio" name="tipo_filtro" value="rol" class="selectgroup-input">
		                        	<span class="selectgroup-button">Rol</span>
		                      	</label> -->
		                    </div>
				  		</div>
				  	</div>
				  	<div class="col-xs-12 col-md-6">
				  		<div id="filtro_cliente" class="form-group">
					  		<label for="" class="form-label">Buscar</label>
							<input id="inp_buscar" type="text" class="form-control border-rock">							
						</div>
				  	</div>
			  	</div>
				<div id="tabla_empleado" class="table-responsive" style="max-height: 700px">
				    <table id="tabla_cliente" class="table table-bordered card-table table-vcenter text-nowrap">
				      	<thead>
				        	<tr>
					        	<th class="text-center w-1">#</th>
								<th>Nombres y Apellidos</th>
								<th>Documento</th>
								<th>Sucursal</th>
								<th>Direccion</th>
								<th class="text-center sticky">Accion</th>
				        	</tr>
				      	</thead>
				      	<tbody id="tb_empleado"></tbody>
				    </table>
			  </div>
		  	</div>
		</div>
	</div>			
</div>

<script src="<?= base_url() ?>js_modules/empleado/empleado.lista.js"></script>