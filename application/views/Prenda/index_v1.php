<link rel="stylesheet" href="<?= base_url() ?>assets/plugins/gridjs/mermaid.min.css">
<script src="<?= base_url() ?>assets/plugins/gridjs/gridjs.production.min.js"></script>
<script src="<?= base_url() ?>assets/plugins/JsPDF/jspdf.umd.js"></script>
<script src="<?= base_url() ?>assets/plugins/JsPDF/jspdf.plugin.autotable.js"></script>
<style>
	.preload-md {
		display: flex !important;
		align-items: center !important;
		padding: 4px;
	}
</style>
<div class="vx-bg">
	<div class="vx-header">
		<div class="vx-title">
			<i class="fe fe-tv"></i>
			Prendas <i class="fe fe-chevrons-right"></i> Lista
		</div>
		<div class="vx-nav">
			<div class="vx-filter">
				<div class="vx-area text-primary">
					Filtrar Por		
				</div>
				<div class="vx-area">
					<div class="custom-controls-stacked">
                      	<label class="custom-control custom-radio custom-control-inline">
                        	<input type="radio" class="custom-control-input" name="filter_item" value="cliente">
                            <span class="custom-control-label">Cliente</span>
                      	</label>
                      	<label class="custom-control custom-radio custom-control-inline">
                        	<input type="radio" class="custom-control-input" name="filter_item" value="tipo">
                            <span class="custom-control-label">Tipo de Prenda</span>
                      	</label>
                      	<label class="custom-control custom-radio custom-control-inline">
                            <input type="radio" class="custom-control-input" name="filter_item" value="categoria">
                            <span class="custom-control-label">Categoria</span>
                      	</label>
                      	<label class="custom-control custom-radio custom-control-inline">
                            <input type="radio" class="custom-control-input" name="filter_item" value="estado">
                            <span class="custom-control-label">Estado</span>
                      	</label>
                    </div>
				</div>
				<div class="vx-area">
					<div id="sectionSearch" class="form-group" style="display: none;">
						<div class="input-icon">
	                        <input id="input_search" type="text" class="vx-input" placeholder="Ingresar busqueda..." style="min-width: 300px;" autocomplete="off">
	                        <span class="input-icon-addon">
	                          <i class="fe fe-search"></i>
	                        </span>
	                  	</div>
					</div>
					<div id="sectionSelect" class="form-group" style="display: none;">
						<div class="input-icon">
	                        <select id="select_search" class="form-control custom-select border-rock" style="min-width: 300px;">
	                        	<option value="">Sin Datos</option>
	                        </select>
	                        <span class="input-icon-addon">
	                          <i class="fe fe-chevrons-left"></i>
	                        </span>
	                  	</div>
					</div>
					<div id="sectionRadio" class="form-group" style="display: none;">
						<span class="text-center text-muted">Sin Datos</span>					
						<!-- <div class="custom-controls-stacked">
                          	<label class="custom-control custom-radio custom-control-inline">
                            	<input type="radio" class="custom-control-input" name="radio_item" value="ELECTRO">
                            	<span class="custom-control-label">ELECTRO</span>
                          	</label>
                          	<label class="custom-control custom-radio custom-control-inline">
                            	<input type="radio" class="custom-control-input" name="radio_item" value="JOYA">
                            	<span class="custom-control-label">JOYA</span>
                          	</label>
                          	<label class="custom-control custom-radio custom-control-inline">
                            	<input type="radio" class="custom-control-input" name="radio_item" value="VEHICULO">
                            	<span class="custom-control-label">VEHICULO</span>
                          	</label>
                        </div> -->
					</div>
				</div>
				<div id="search_preload" class="d-flex align-items-center text-primary font-weight-bold" style="visibility: hidden;">
					<i class="fas fa-spinner fa-pulse"></i>&nbsp;
					Procesando...
				</div>
			</div>
			<div class="vx-options">
				<a href="javascript:void(0)" id="link_config_tabla" class="vx-link">
					<i class="fe fe-layout"></i>
					Config. Tabla
				</a>
				<a href="javascript:void(0)" id="__download_data_pdf" class="vx-link">
					<i class="fe fe-download"></i>
					Descargar
				</a>
			</div>			
		</div>
	</div>
	<div id="dataset_prenda"></div>
</div>

<!-- Modals -->
<div class="modal fade" id="md_tabla_config" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
  	<div class="modal-dialog modal-lg">
	    <div class="modal-content">
	      	<div class="modal-header">
	        	<h5 class="modal-title">
	        		<i class="fe fe-layout text-bluevx"></i>
	        		Config. Tabla
	        	</h5>
		        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
	          		<!-- <span aria-hidden="true">&times;</span> -->
		        </button>
	      	</div>
	      	<div class="modal-body">
	      		<div class="row">
	      			<div class="col-md-12 form-group">
                        <div class="form-label">Columnas:</div>
                        <div id="config_cols" class="row">
                        	<span class="text-muted p-2 text-center">Sin Datos</span>
                        </div>
                        <div class="form-group">
	                        <div class="form-label">Temas:</div>
	                        <div class="custom-controls-stacked">
	                          	<label class="custom-control custom-radio custom-control-inline">
	                            	<input type="radio" class="custom-control-input" name="theme-item" value="" checked="">
	                            	<span class="custom-control-label">Normal</span>
	                          	</label>
	                          	<label class="custom-control custom-radio custom-control-inline">
	                            	<input type="radio" class="custom-control-input" name="theme-item" value="table-theme-dark">
	                            	<span class="custom-control-label">Dark</span>
	                          	</label>
	                          	<label class="custom-control custom-radio custom-control-inline">
	                            	<input type="radio" class="custom-control-input" name="theme-item" value="table-theme-material">
	                            	<span class="custom-control-label">Material UI</span>
	                          	</label>
	                        </div>
                      	</div>
                      	<div class="row form-group">
                      		<div class="form-label">Dise���o:</div>
                      		<div class="col-auto col-xs-12">
                      			<label class="custom-control custom-checkbox custom-control-inline">
	                            	<input id="config_sub_busqueda" type="checkbox" class="custom-control-input" name="config-item" value="">
	                            	<span class="custom-control-label">Sub Busqueda</span>
	                          	</label>
                      		</div>
                      		<div class="col-auto col-xs-12">
                      			<label class="custom-control custom-checkbox custom-control-inline">
	                            	<input id="config_head_static" type="checkbox" class="custom-control-input" name="config-item" value="">
	                            	<span class="custom-control-label">Cabecera Estatica</span>
	                          	</label>
                      		</div>
                      	</div>
                      	<div class="row form-group">
                      		<!-- <div class="form-label">Otros:</div> -->
                      		<div class="col-md-4 col-xs-12">
                      			<div class="form-label">Registros por Bloque:</div>
                      			<div class="input-group">
		                            <input id="config_sub_limit" type="number" class="form-control border-rock text-center" value="8">
		                            <span class="input-group-append">
		                              	<button id="btn_sub_limit" class="btn btn-primary" type="button">
		                              		Guardar 
		                              		<i class="fe fe-save"></i>
		                              	</button>
		                            </span>
	                          	</div>
	                          	<small id="feed_sub_limit" class="text-danger"></small>
                      			<!-- <input id="config_bloque_cant" type="text" class="form-control border-rock"> -->
                      		</div>
                      		<!-- <div class="col-md-4 col-xs-12">
                      			<div class="form-label">Registros por Bloque:</div>
                      			<input type="text" class="form-control border-rock">
                      		</div> -->
                      	</div>
         	 		</div>
	      		</div>
	      	</div>
	      	<div class="modal-footer">
	        	<button type="button" class="btn btn-secondary" data-dismiss="modal">
	        		<i class="fe fe-x-circle"></i> 
	        		Cerrar
	        	</button>
	      	</div>
	    </div>
  	</div>
</div>

<script src="<?= base_url() ?>js_modules/prenda/prenda.index_v1.js"></script>

