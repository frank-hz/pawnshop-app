<div class="p-2">
	<div class="card">
		<div class="card-header">
			<div class="card-title text-primary font-weight-bold">
				<i class="fe fe-credit-card"></i>
				Pagos
			</div>
		</div>
		<div class="card-body">
			<div class="row">
				<div class="col-xs-12 col-md-4 col-lg-3">
			  		<div class="form-group">
                        <div class="form-label">Estilo de tabla</div>
                        <label class="custom-switch">
                          	<input type="checkbox" id="_check_darkmode" class="custom-switch-input">
                          	<span class="custom-switch-indicator"></span>
                          	<span class="custom-switch-description">
                          		Modo Oscuro
                          	</span>
                        </label>
                  	</div>
			  	</div>
			</div>
			<div class="row">
				<div class="col-xs-12 col-auto">
					<div class="form-group">
			  			<label class="form-label">Definir Filtro</label>
			  			<div class="custom-controls-stacked">
                      		<label class="custom-control custom-radio custom-control-inline">
                        		<input type="radio" class="custom-control-input" name="_tipo_filtro_item" value="cliente" checked="">
                        		<span class="custom-control-label">Cliente</span>
                      		</label>
                      		<label class="custom-control custom-radio custom-control-inline">
                        		<input type="radio" class="custom-control-input" name="_tipo_filtro_item" value="credito">
                        		<span class="custom-control-label">Credito</span>
                      		</label>
                      		<label class="custom-control custom-radio custom-control-inline">
                        		<input type="radio" class="custom-control-input" name="_tipo_filtro_item" value="forma">
                        		<span class="custom-control-label">Forma de Pago</span>
                      		</label>
                      		<label class="custom-control custom-radio custom-control-inline">
                        		<input type="radio" class="custom-control-input" name="_tipo_filtro_item" value="fecha_rango">
                        		<span class="custom-control-label">Fecha</span>
                      		</label>
                    	</div>
			  		</div>
				</div>
				<div class="col-xs-12 col-md-4">
					<div id="_filtro_texto_section" class="form-group">
	  					<label class="form-label">Buscar</label>
		  				<input id="_input_buscar" type="text" class="form-control border-rock text-uppercase">
		  			</div>

	  				<div id="_filtro_select_section" class="form-group" style="display: none;">
	                    <div class="form-label">Formas de Pago</div>

	                    <div class="selectgroup w-100">
                          	<label class="selectgroup-item">
                            	<input type="radio" name="_forma_pago_item" value="TIEN" class="selectgroup-input">
                            	<span class="ndc_selectgroup-button selectgroup-button-icon py-1">
                        			<i class="fe fe-home"></i>
                        			TIENDA
                        		</span>
                          	</label>
                          	<label class="selectgroup-item">
                            	<input type="radio" name="_forma_pago_item" value="DEP" class="selectgroup-input">
                            	<span class="ndc_selectgroup-button selectgroup-button-icon py-1">
                            		<i class="fe fe-smartphone"></i>
                            		DEPOSITO
                            	</span>
                          	</label>
                        </div>
	              	</div>

					<form id="_form_filtro_rango" autocomplete="off" style="display: none;">
		  				<div class="form-group mx-2 d-inline float-left">
		  					<label class="form-label">Inicio</label>
		  					<input type="date" class="form-control border-rock" name="inicio" required>
		  				</div>
		  				<div class="form-group mx-2 d-inline float-left">
		  					<label class="form-label">Final</label>
		  					<input type="date" class="form-control border-rock" name="final" required>
		  				</div>
		  				<div class="form-group py-1 float-left">
		  					<br>
		  					<button class="btn btn-blue">
		  						Buscar 
		  						<i class="fe fe-search"></i>
		  					</button>
		  				</div>
		  			</form>
				</div>
				<div class="col-xs-12 col-auto d-flex align-items-center">
					<button id="_btn_dia_actual" class="btn btn-dark mt-2">
                    	<i class="fe fe-calendar"></i>
						Registrados Hoy
					</button>
				</div>
				<div class="col-xs-12 col-md-2 col-lg-1">
					<div class="form-group">
						<div class="form-label">Cantidad</div>
						<div class="input-icon mb-3">
							<select id="_select_cantidad" class="form-studio text-center">
								<option value="10">10</option>
								<option value="30">30</option>
			                  	<option value="50">50</option>
			                  	<option value="100">100</option>
			                  	<option value="1000">Todo</option>
							</select>
							<span class="input-icon-addon">
		                      	<i class="fe fe-hash"></i>
		                    </span>
						</div>
					</div>					
				</div>

			</div>
		</div>
		<table id="_pago_table" class="card-table table-studio">
			<caption id="_caption_dataset_pago" class="caption-loader" style="display: none;">
				<span class="mx-2">Cargando..</span>
				<i class="fas fa-spinner fa-pulse"></i>
			</caption>
			<thead>
				<th class="w-1">#</th>
				<th>Serie</th>
				<th>Credito</th>
				<th>Concepto</th>
				<th>Fecha</th>
				<th>Cliente</th>
				<th>Tipo</th>
				<th>Monto</th>
				<th class="text-center"></th>
			</thead>
			<tbody id="_pago_dataset">
				<tr>
					<td class="text-center" colspan="9">
						<i class="fe fe-bar-chart-2"></i>
						Sin resultados.
					</td>
				</tr>
			</tbody>
		</table>
	</div>
</div>

<script src="<?= base_url() ?>js_modules/pago/pago.lista.js"></script>