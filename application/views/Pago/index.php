<script src="<?= base_url() ?>assets/plugins/sheetjs/dist/FileSaver.js"></script>
<link rel="stylesheet" href="<?= base_url() ?>assets/plugins/gridjs/mermaid.min.css">
<script src="<?= base_url() ?>assets/plugins/gridjs/gridjs.production.min.js"></script>
<script src="<?= base_url() ?>assets/plugins/sheetjs/dist/xlsx.full.min.js"></script>

<div class="modulo">
    <div class="modulo-header">
        <h3 class="modulo-head-title">
            <!-- <svg xmlns="http://www.w3.org/2000/svg" class="modulo-title-icon" viewBox="0 0 512 512"><title>Logo Usd</title><path d="M240 480v-36.42C160.53 439 112.25 398.06 112 336h72c1.77 26.34 23.86 46.45 56 50v-98l-26.77-7c-61-14.18-93.64-49.39-93.64-102.08C119.59 116.81 164.08 76.08 240 70V32h32v38c77.39 6.3 119 47.74 120 106h-72c-.76-24.06-15.83-43.39-48-46v92l30.82 7.28C367.61 243.46 400 277 400 332c0 64.34-43.74 105.88-128 111.32V480zm0-264v-86c-27.59 1.52-47.27 18.47-47.27 42.53 0 22.3 16.39 36.88 47.27 43.47zm32 78v92c38.15-1.54 56.38-18.92 56.38-45.77 0-24.58-18.23-41.13-56.38-46.23z"/></svg> -->
            <i class="fe fe-dollar-sign"></i>
            Pagos
        </h3>
        <div class="modulo-options">            
            <button id="btn-export-excel" class="btn btn-excel">
                Exportar Excel
            </button>
            <button id="btn-config" class="btn btn-secondary mx-1">
                Config
            </button>
        </div>
    </div>
    <div id="pago_modulo_loader" class="modulo-loader-content">
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
                    <option value="CREDITO">Credito</option>
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
            <div class="modulo-filter-label">Fecha de Registro</div>
            <div class="d-flex">
                <div class="modulo-select mr-1">
                    <select id="select-date-filter-type">
                        <option value="DATE">Fecha</option>
                        <option value="RANGE">Rango de Fechas</option>
                    </select>
                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z"></path></svg>
                </div>
                <div id="date-select-area" class="d-flex">
                    <input id="date-selector-unico" type="date" class="form-control border-rock">
                    <input id="date-selector-a" type="date" class="form-control border-rock" style="display:none">
                    <input id="date-selector-b" type="date" class="form-control border-rock mx-1" style="display:none">
                </div>               
            </div>
        </div>
        <div class="modulo-filter-item">
            <div class="modulo-filter-label">Ordenar por</div>
            <div class="d-flex">
                <div class="modulo-select mr-1">
                    <select id="select-orden-por">
                        <option value="SERIE">Codigo</option>
                        <option value="TOTAL">Monto</option>
                        <option value="FECHA">Fecha</option>
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
            <button id="btn-data-filter-reset" class="btn btn-blue">Reiniciar Filtros</button>
            <button id="btn-data-pagos-dia" class="btn btn-blue">Pagos del Dia</button>
       </div>

    </div>
    <div class="modulo-filter" style="justify-content: space-between">
        <!-- Forma de Pago -->
        <div class="modulo-filter-item">
            <div class="modulo-filter-label">Forma de Pago</div>
            <div>
                <label class="custom-control custom-checkbox custom-control-inline">
                    <input type="checkbox" class="custom-control-input" name="check-forma-pago" value="EFEC">
                    <span class="custom-control-label">Efectivo</span>
                </label>
                <label class="custom-control custom-checkbox custom-control-inline">
                    <input type="checkbox" class="custom-control-input" name="check-forma-pago" value="VIRT">
                    <span class="custom-control-label">Virtual</span>
                </label>
                <label class="custom-control custom-checkbox custom-control-inline">
                    <input type="checkbox" class="custom-control-input" name="check-forma-pago" value="DEB_AUTO">
                    <span class="custom-control-label">Debito Auto.</span>
                </label>
            </div>
        </div>
        <!-- Concepto de Pago -->
        <div class="modulo-filter-item">
            <div class="modulo-filter-label">Concepto</div>
            <div>
                <label class="custom-control custom-checkbox custom-control-inline">
                    <input type="checkbox" class="custom-control-input" name="check-concepto" value="REG">
                    <span class="custom-control-label">Regulariza.</span>
                </label>
                <label class="custom-control custom-checkbox custom-control-inline">
                    <input type="checkbox" class="custom-control-input" name="check-concepto" value="REC">
                    <span class="custom-control-label">Recupera.</span>
                </label>
                <label class="custom-control custom-checkbox custom-control-inline">
                    <input type="checkbox" class="custom-control-input" name="check-concepto" value="AMOR">
                    <span class="custom-control-label">Amortiza.</span>
                </label>
                <label class="custom-control custom-checkbox custom-control-inline">
                    <input type="checkbox" class="custom-control-input" name="check-concepto" value="ADEL">
                    <span class="custom-control-label">Adelanto</span>
                </label>
                <label class="custom-control custom-checkbox custom-control-inline">
                    <input type="checkbox" class="custom-control-input" name="check-concepto" value="DEB_AUTO">
                    <span class="custom-control-label">Debito Auto.</span>
                </label>
                <!-- <label class="custom-control custom-checkbox custom-control-inline">
                    <input type="checkbox" class="custom-control-input" name="check-concepto" value="ADEL_AUTO">
                    <span class="custom-control-label">Adelanto Auto.</span>
                </label> -->
            </div>
        </div>
    </div>
    <div class="modulo-table">
        <div id="pago_data_table"></div>
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
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
	    </div>
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

<script src="<?= base_url() ?>js_modules/pago/pago.index.js?v=19012025"></script>