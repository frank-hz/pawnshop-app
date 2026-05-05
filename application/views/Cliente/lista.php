<div class="my-2">
	<div class="col-xs-12 col-md-12">
		<div class="card">
			<div class="card-header">
		    	<h4 class="card-title font-weight-bold">
					<i class="fe fe-users text-blue"></i>
					Clientes
				</h4>
				<div class="card-options">
			        <div class="">
			        	<a href="<?= base_url() ?>Cliente/nuevo" class="btn btn-primary">
							<i class="fe fe-plus"></i>
							Nuevo Cliente
						</a>
						<!-- <button class="btn btn-secondary">
							Estilo 
							<i class="fe fe-slack"></i>
						</button> -->
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
			                      	<h4 class="m-0"><a href="javascript:void(0)" id="info_total">132 <small>clientes</small></a></h4>
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
			                      	<h4 class="m-0"><a href="javascript:void(0)" id="info_hoy">132 <small>nuevos</small></a></h4>
			                      	<small class="text-muted">Registrados hoy</small>
			                    </div>
		                  	</div>
		                </div>
		          	</div>
		        </div> -->
			  	<div class="row">
				  	<div class="col-xs-12 col-md-5 col-lg-4">
		              	<div class="form-group">
				  			<label class="form-label">Definir Filtro</label>
				  			<div class="selectgroup w-100">
		                      	<label class="selectgroup-item">
		                        	<input type="radio" name="tipo_filtro" value="nombre" class="selectgroup-input" checked>
		                        	<span class="selectgroup-button">Nombres</span>
		                      	</label>
		                      	<label class="selectgroup-item">
		                        	<input type="radio" name="tipo_filtro" value="documento" class="selectgroup-input">
		                        	<span class="selectgroup-button">Documento</span>
		                      	</label>
		                    </div>
				  		</div>
				  	</div>
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
				  	<div class="col">
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
			  	<div class="row">
			  		<div class="col-xs-12 col-md-12 form-group">
				  		<label for="" class="form-label">Buscar</label>
						<input id="inp_buscar" type="text" class="form-control border-rock">
					</div>
			  	</div>
				<div class="table-responsive" style="max-height: 700px">
				    <table id="tabla_cliente" class="table table-bordered card-table table-vcenter text-nowrap">
				      	<thead>
				        	<tr>
					        	<th class="text-center w-1">#</th>
								<th>Nombres y Apellidos</th>
								<th>Documento</th>
								<th>Ciudad</th>
								<th>Telefono</th>
								<th>Creditos Activos</th>
								<!-- <th>Direccion</th> -->
								<th class="text-center">Accion</th>
				        	</tr>
				      	</thead>
				      	<tbody id="tb_cliente"></tbody>
				    </table>
			  </div>
		  	</div>
		</div>
	</div>			
</div>

<script src="<?= base_url() ?>js_modules/cliente/cliente.lista.js?v=2"></script>