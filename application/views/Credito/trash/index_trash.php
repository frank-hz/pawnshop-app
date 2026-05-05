<script src="<?= base_url() ?>assets/plugins/sheetjs/dist/FileSaver.js"></script>
<link rel="stylesheet" href="<?= base_url() ?>assets/plugins/gridjs/mermaid.min.css">
<script src="<?= base_url() ?>assets/plugins/gridjs/gridjs.production.min.js"></script>
<script src="<?= base_url() ?>assets/plugins/sheetjs/dist/xlsx.full.min.js"></script>

<link rel="stylesheet" href="<?= base_url() ?>assets/plugins/vanillajs-datepicker/dist/css/datepicker.min.css">
<script src="<?= base_url() ?>assets/plugins/vanillajs-datepicker/dist/js/datepicker-full.min.js"></script>


<div class="modulo">
    <div class="modulo-header">
        <div class="modulo-head-title">
            <i class="fe fe-file"></i> 
            Creditos
        </div>
        <div class="modulo-options">
            <div class="">
                <a href="<?= base_url() ?>credito/nuevo" class="btn btn-indigo">
                    Nuevo 
                    <i class="fe fe-plus"></i>
                </a>
                <button id="btn-export-excel" class="btn btn-excel" title="Exportar Excel">
                    Excel 
                    <i class="fe fe-download"></i> 
                </button>
                <button id="btn-config" class="btn btn-secondary">
                    Config 
                    <i class="fe fe-settings"></i> 
                </button>
            </div>
        </div>
    </div>
    <div id="credito_modulo_loader" class="modulo-loader-content">
        <div class="modulo-loader-area">
            <div class="modulo-loading-label">Cargando </div>
            <div class="modulo-loading-icon">
                <span></span>
                <span></span>
                <span></span>
            </div>
        </div>        
    </div>
    <div class="modulo-filter">
        <div class="modulo-filter-item">
            <div class="modulo-filter-label">Tipo de Busqueda</div>
            <div class="modulo-select">
                <select id="select-tipo-busqueda">
                    <option value="">-- ELEGIR --</option>
                    <option value="CODIGO" selected>Codigo</option>
                    <option value="CLIENTE">Cliente</option>
                </select>
                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"></path></svg>
            </div>
        </div>
        <div class="modulo-filter-item">
            <div class="modulo-filter-label">Buscar</div>
            <input id="input-buscar" type="text" class="modulo-filter-input text-uppercase">
        </div>
        <div class="modulo-filter-item">
            <div class="modulo-filter-label">Cuotas Atrasadas</div>
            <div class="d-flex">
                <div class="modulo-select mr-1">
                    <select id="select-atraso-simbolo">
                        <option value="">-- ELEGIR --</option>
                        <option value=">"> Mayor (>) </option>
                        <option value=">=" selected> Mayor Igual (>=) </option>
                        <option value="="> Igual (=) </option>
                        <option value="<"> Menor (<) </option>
                        <option value="<="> Menor Igual (<=) </option>
                    </select>
                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"></path></svg>
                </div>
                <input id="input-atraso-valor" type="number" class="modulo-filter-input">
            </div>
        </div>
        <div class="modulo-filter-item">
            <div class="modulo-filter-label">Estado</div>
            <div class="modulo-select">
                <select id="select-estado">
                    <option value="">TODOS</option>
                    <option value="E" selected>Activo</option>
                    <option value="A">Remate</option>
                    <option value="R">Recuperado</option>
                    <option value="CC">Congelado</option>
                </select>
                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"></path></svg>
            </div>
        </div>
        <div class="modulo-filter-item">
            <div class="modulo-filter-label">Fecha Registro</div>
            <input id="datepicker-fecha" type="text" class="modulo-filter-input" readonly>
        </div>        
        <div class="modulo-filter-item">
            <div class="modulo-filter-label">Ordenar por</div>
            <div class="d-flex">
                <div class="modulo-select mr-1">
                    <select id="select-orden-por">
                        <option value="CODIGO">Codigo</option>
                        <option value="MONTO">Monto</option>
                        <option value="CREACION">Fecha</option>
                        <option value="ATRASO">Atraso</option>
                    </select>
                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"></path></svg>
                </div>
                <div class="modulo-select">
                    <select id="select-orden-tipo">
                        <option value="">-- ELEGIR --</option>
                        <option value="ASC">Ascendente</option>
                        <option value="DESC" selected>Descendente</option>
                    </select>
                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"></path></svg>
                </div>                
            </div>
        </div>
        <div class="modulo-filter-item">
            <div class="modulo-filter-label">&nbsp;</div>
            <button id="btn-data-filter-reset" class="btn btn-secondary text-blue">Reiniciar Filtros</button>
       </div>

    </div>
    <div class="modulo-table">
        <div id="credito_data_table"></div>
        <!-- <div class="modulo-table-loader">
            <div class="modulo-loader-message">     
                <div class="spinner-icon">
                    <span></span>
                </div>
                Cargando...
            </div>           
        </div> -->
    </div>
</div>


<div id="modal-notas" class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-hidden="true">
  	<div class="modal-dialog modal-lg" role="document">
	    <div class="modal-content">
	      	<div class="modal-header border-palette-purple">
		        <h4 class="modal-title">
		        	<i class="fe fe-file-plus text-blue"></i>
                    Notas
                    <a id="nota-title-credito-link" class="text-blue font-600" href="" target="_blank">[]</a>
		        </h4>
		        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
		        	<span aria-hidden="true" class="text-primary"></span>
		        </button>
	      	</div>
            <!-- <div class="p-2 py-1">
                <button id="modal_nota_recargar" class="btn btn-secondary border-rock">
                    Reset
                </button>
            </div> -->
            <div class="pt-1 pb-2">
                <ul class="menu-tabbs mb-4">
                    <li id="tabb-item-lista" class="tabb-item" name="tabb-item" data-target="tabb-content-lista">Notas Actuales</li>

                    <li id="tabb-item-nuevo" class="tabb-item" name="tabb-item" data-target="tabb-content-nuevo">Nueva Nota</li>
                </ul>
                
                <div class="tabb-content">           
                    <div id="tabb-content-lista" class="px-2 tabb-pane" name="tabb-pane">
                        <table class="modal-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Descripcion</th>
                                    <th>Tipo</th>
                                    <th>Fecha</th>
                                    <th class="text-center">Opcion</th>
                                </tr>
                            </thead>
                            <tbody id="notas-dataset">
                                <tr>
                                    <td colspan="5" class="text-center">Sin Datos</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div id="tabb-content-nuevo" class="tabb-pane" name="tabb-pane">
                        <form id="nota-registro-form" autocomplete="off" autocorrect="off">
                            <input id="nota-credito-id" name="credito" type="hidden">
                            <div class="modal-body">                                
                                <div class="row">
                                    <div class="col-xs-12 col-md-12">
                                        <div class="form-group">
                                            <div class="form-label">Tipo</div>
                                            <div class="custom-controls-stacked">
                                                <label class="custom-control custom-radio custom-control-inline">
                                                    <input type="radio" class="custom-control-input" name="nota_tipo" value="NO" checked="">
                                                    <span class="custom-control-label">Nota Normal</span>
                                                </label>
                                                <label class="custom-control custom-radio custom-control-inline">
                                                    <input type="radio" class="custom-control-input" name="nota_tipo" value="SI">
                                                    <span class="custom-control-label">Nota Anclada</span>
                                                </label>
                                            </div>
                                            <div id="nota-tipo-feed" class="feed-tag-error text-danger"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-xs-12 col-md-12">
                                        <div class="form-group">
                                            <label for="" class="font-weight-bold">Descripcion</label>
                                            <textarea cols="5" rows="4" class="form-control border-rock" name="descripcion"></textarea>
                                            <div id="nota-descripcion-feed" class="feed-tag-error text-danger"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-xs-12 col-md-12">
                                        <div class="d-flex justify-centers">
                                            <button type="submit" class="btn btn-indigo">Guardar Datos</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form> 
                    </div>
                </div>
            </div>
            <div class="modal-footer border-palette-purple">
                <div class="">
                    <button id="modal_nota_recargar" class="btn btn-secondary border-rock">
                        Reset
                    </button>
                    <button id="modal_nota_salir" type="button" class="btn btn-secondary border-rock" data-dismiss="modal">Salir</button>
                </div>
            </div>
	    </div>
  	</div>
</div>

<div id="modal-nota" class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-hidden="true">
  	<div class="modal-dialog modal-lg" role="document">
	    <div class="modal-content">
	      	<div class="modal-header">
		        <h4 class="modal-title">
		        	<i class="fe fe-file-plus text-blue"></i>
		        	<span id="mdnota_registro_title">Nueva Nota</span>
		        </h4>
		        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
		        	<span aria-hidden="true" class="text-primary"></span>
		        </button>
	      	</div>
	      	<div id="notargt_sysmssg" class="p-0 m-0" style="display: none;"></div>
	  		<form id="notaform_registro" autocomplete="off" autocorrect="off">
	  			<input id="mdnota_registro_credito" name="credito" type="hidden">
	      		<div class="modal-body">
					<div class="row">
						<div class="col-xs-12 col-md-12">
							<div class="form-group">
								<label for="" class="font-weight-bold">Credito</label>
								<input id="nnota_credito_serie" type="text" class="form-control border-rock" value="" readonly>
							</div>
						</div>
						<div class="col-xs-12 col-md-12">
							<div class="form-group">
								<label for="" class="font-weight-bold">Descripcion</label>
								<textarea cols="10" rows="5" class="form-control border-rock" name="descripcion"></textarea>
							</div>
						</div>
						<div class="col-xs-12 col-md-4">
							<div class="form-group">
		                        <div class="form-label">Tipo</div>
		                        <div class="custom-controls-stacked">
		                          	<label class="custom-control custom-radio custom-control-inline">
		                            	<input type="radio" class="custom-control-input" name="anclado" value="NO" checked="">
		                            	<span class="custom-control-label">Nota Normal</span>
		                          	</label>
		                          	<label class="custom-control custom-radio custom-control-inline">
		                            	<input type="radio" class="custom-control-input" name="anclado" value="SI">
		                            	<span class="custom-control-label">Nota Anclada</span>
		                          	</label>
		                        </div>
	                      	</div>
						</div>
					</div>
	      		</div>
	      		<div class="modal-footer text-right">
	  				<button type="submit" class="btn btn-blue">
	  					<i class="fe fe-save"></i> Registrar
	  				</button>
	  				<button id="mdnota__registro_volver" class="btn btn-secondary" data-dismiss="modal">
	  					<i class="fe fe-corner-left-down"></i> Volver
	  				</button>
	      		</div>
			</form>
	    </div>
  	</div>
</div>

<div id="modal-fecha-selector" class="modal fade bd-example-modal-sm" tabindex="-1" role="dialog" aria-hidden="true">
  	<div class="modal-dialog modal-sm" role="document">
	    <form id="fechas-selector-form" class="modal-content" autocomplete="off" onsubmit="return false">
	      	<div class="modal-header">
		        <h4 class="modal-title">
		        	<i class="fe fe-calendar text-blue"></i>
		        	<span id="mdnota_registro_title">Definir Fechas</span>
		        </h4>
		        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
		        	<span aria-hidden="true" class="text-primary"></span>
		        </button>
	      	</div>
	  		<div class="modal-body p-1">
                <div class="">
                    <div class="col-sm-12 col-md-12">
                        <div class="form-group">
                            <div class="form-label">Fecha Rango 1</div>
                            <input id="rango_1" name="rango_1" type="date" class="modulo-filter-input" required>
                        </div>
                    </div>
                    <div class="col-sm-12 col-md-12">
                        <div class="form-group">
                            <div class="form-label">Fecha Rango 2</div>
                            <input id="rango_2" name="rango_2" type="date" class="modulo-filter-input" required>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <div class="col-md-12">
                    <button type="submit" class="btn btn-indigo">
                        Aceptar
                    </button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                </div>
            </div>
	    </form>
  	</div>
</div>

<div id="modal-config" class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-hidden="true">
  	<div class="modal-dialog modal-lg" role="document">
	    <div class="modal-content">
	      	<div class="modal-header">
		        <h4 class="modal-title">
		        	<i class="fe fe-settings text-blue"></i>
		        	<span id="mdnota_registro_title">Configuracion</span>
		        </h4>
		        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
		        	<span aria-hidden="true" class="text-primary"></span>
		        </button>
	      	</div>
	  		<div class="modal-body">
                <div class="row">
                    <div class="col-md-4 form-group">
                        <div class="form-label">Paginacion</div>
                        <div class="custom-controls-stacked">
                            <label class="custom-control custom-radio custom-control-inline">
                                <input type="radio" class="custom-control-input" name="option-pagination-item" value="SI" checked="">
                                <span class="custom-control-label">Si</span>
                            </label>
                            <label class="custom-control custom-radio custom-control-inline">
                                <input type="radio" class="custom-control-input" name="option-pagination-item" value="NO">
                                <span class="custom-control-label">No</span>
                            </label>
                        </div>
                    </div>
                    <div id="option_cantidad_area" class="col-md-3 form-group">
                        <label class="form-label">Cantidad</label>
                        <input id="option-cantidad" type="number" class="form-control border-rock text-center" value="20">
                    </div>
                </div>
                

            </div>
            <div class="modal-footer text-right">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
	    </div>
  	</div>
</div>

<script src="<?= base_url() ?>js_modules/credito/credito.index_trash.js"></script>