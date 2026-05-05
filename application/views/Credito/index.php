<script src="<?= base_url() ?>assets/plugins/sheetjs/dist/FileSaver.js"></script>
<link rel="stylesheet" href="<?= base_url() ?>assets/plugins/gridjs/mermaid.min.css">
<script src="<?= base_url() ?>assets/plugins/gridjs/gridjs.production.min.js"></script>
<script src="<?= base_url() ?>assets/plugins/sheetjs/dist/xlsx.full.min.js"></script>
<link href="<?= base_url() ?>assets/css/tables.css?v=4" rel="stylesheet">

<div class="tablespace">
    <div class="tablespace-header">
        <div class="tablespace-title">
            <i class="fe fe-file text-indigo"></i> 
            Lista de Creditos
        </div>
        <div class="tablespace-options">
            <a href="<?= base_url() ?>Credito/nuevo" class="btn tablespace-option">
                <i class="fe fe-file-plus"></i> 
                Nuevo
            </a>
            <button id="btn_export_excel" class="tablespace-option" title="Exportar Excel">
                <i class="fe fe-download mr-1"></i> 
                Exportar
            </button>
        </div>
    </div>
    <div class="tablespace-filters">
        <div class="tablespace-filters-header">
            <div class="tablespace-filter-row">
                <div class="form-group">
                    <div class="form-label">Tipo Busqueda:</div>
                    <div class="tablespace-filter-search tablespace-filter-search-right mr-1">
                        <select id="filter_search_target" class="tablespace-search-input">
                            <option value="CODIGO">Codigo</option>
                            <option value="CLIENTE">Cliente</option>
                        </select>
                        <div class="filter-search-icon">
                            <i class="fe fe-chevron-down"></i>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="form-label">Buscar:</div>
                    <div id="input_search_v1" class="tablespace-filter-search  mr-1">
                        <input id="filter_search_value" type="text" class="tablespace-search-input" placeholder="Busqueda Rapida">
                        <div class="filter-search-icon">
                            <i class="fe fe-search"></i>
                        </div>
                    </div>
                    <!-- <div id="input_search_v2" class="input-group mr-1">
                        <span class="input-group-prepend " id="basic-addon3">
                            <span class="input-group-text tablespace-border-input font-weight-bold">
                                <?= $this->session->userdata('sucursal_serie') ?> - 
                            </span>
                        </span>
                        <input type="text" class="form-control tablespace-border-input" id="filter_search_value" autocomplete="off">
                    </div> -->
                </div>
                <div class="form-group">
                    <div class="form-label">Ordenar Por:</div>
                    <div class="tablespace-filter-search tablespace-filter-search-right mr-1">
                        <select id="filter_order_target" class="tablespace-search-input">
                            <option value="CODIGO" selected>Codigo</option>
                            <option value="MONTO">Monto</option>
                            <option value="CREACION">Creacion</option>
                            <option value="ATRASO">Atraso</option>
                            <option value="LIQUIDACION">Liquidacion</option>
                        </select>
                        <div class="filter-search-icon">
                            <i class="fe fe-chevron-down"></i>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="form-label">Tipo Orden:</div>
                    <div class="tablespace-filter-search tablespace-filter-search-right mr-1">
                        <select id="filter_order_type" class="tablespace-search-input">
                            <option value="ASC">Ascendente</option>
                            <option value="DESC" selected>Descendente</option>
                        </select>
                        <div class="filter-search-icon">
                            <i class="fe fe-chevron-down"></i>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="form-label">Cantidad:</div>
                    <div class="tablespace-filter-search tablespace-filter-search-right mr-1">
                        <select id="filter_limit" class="tablespace-search-input">
                            <option value="30">30</option>
                            <option value="50">50</option>
                            <option value="100">100</option>
                            <option value="200">200</option>
                            <option value="0" selected>TODO</option>
                        </select>
                        <div class="filter-search-icon">
                            <i class="fe fe-chevron-down"></i>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="form-label">Estado:</div>
                    <div class="tablespace-filter-search tablespace-filter-search-right mr-1">
                        <select id="filter_estado" class="tablespace-search-input">
                            <option value="">-TODO-</option>
                            <option value="E" data-label="Activo" selected>Activo</option>
                            <option value="A" data-label="Remate">Remate</option>
                            <option value="R" data-label="Recuperado">Recuperado</option>
                            <option value="CC" data-label="Congelado">Congelado</option>
                        </select>
                        <div class="filter-search-icon">
                            <i class="fe fe-chevron-down"></i>
                        </div>
                    </div>
                </div>                
                <div class="form-group">
                    <div class="form-label">Categoria:</div>
                    <div class="tablespace-filter-search tablespace-filter-search-right mr-1">
                        <select id="filter_select_categoria" class="tablespace-search-input">
                            <option value="">-TODO-</option>
                        </select>
                        <div class="filter-search-icon">
                            <i class="fe fe-chevron-down"></i>
                        </div>
                    </div>
                </div>
                <div id="group_search_prenda" class="form-group d-none">
                    <div class="form-label">Prenda:</div>
                    <div class="tablespace-filter-search  mr-1">
                        <input id="filter_search_prenda" type="text" class="tablespace-search-input" placeholder="Busqueda prenda">
                        <div class="filter-search-icon">
                            <i class="fe fe-search"></i>
                        </div>
                    </div>
                </div>  
            </div>
            <div class="tablespace-filter-buttons mt-2">
                <!-- <button id="btn_open_modal_filter" class="tablespace-button">
                    <i class="fe fe-plus"></i>
                    Agregar Filtro
                </button> -->
                <button id="btnResetFilters" class="tablespace-button">
                    Reiniciar Filtros
                </button>

            </div>
        </div>
        <div class="tablespace-filters-stash">            
            <div class="tablespace-filters-title">Filtros Actuales</div>
            <div class="tablespace-filter-row">
                <div class="tablespace-filters-list" id="filter_stash">
                    <!-- <span class="tablespace-filter-tag">
                        <div class="tablespace-filter-tag-message">
                            Fecha: 05/10/2023
                        </div>
                        <a href="" class="tablespace-filter-tag-close">
                            &#10006;
                        </a>
                    </span>
                    <span class="tablespace-filter-tag">
                        <div class="tablespace-filter-tag-message">
                            Estado: Activo
                        </div>
                        <a href="" class="tablespace-filter-tag-close">
                            &#10006;
                        </a>
                    </span> -->
                    _NO DATA_
                </div>
                <div id="tablespace_loader" class="d-flex align-items-center text-primary font-weight-bold" style="visibility: hidden;">
					<i class="fas fa-spinner fa-pulse"></i>&nbsp;
					Procesando...
				</div>

            </div>
        </div>
    </div>
    <div class="tablespace-data">
        <!-- <table>
            <thead>
                <tr>
                    <th>Nª</th>
                    <th>Codigo</th>
                    <th>Fecha</th>
                    <th>Cliente</th>
                    <th>Monto</th>
                    <th>Cuotas Venc.</th>
                    <th>Prenda</th>
                    <th>Estado</th>
                    <th>Accion</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>01</td>
                    <td>CH01-2970</td>
                    <td>12/10/2023</td>
                    <td>DIANA VALENTINA TORREALVA LECCA</td>
                    <td>S/ 300.00</td>
                    <td>5</td>
                    <td>LAPTOP ACER CORE I3 8VA</td>
                    <td>
                        <div class="stag stag-cred-E">En Espera</div>
                    </td>
                    <td>
                        <div class="d-flex">
                            <a href="http://localhost/sistema-dev/Credito/detalle/2551" class="tablespace-button">
                                <i class="fe fe-layers"></i
                                >
                            </a>
                            <button name="credito-notas-item" class="tablespace-button" data-credito="2551" data-serie="CH01-2970"> Notas</button>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>02</td>
                    <td>CH01-2970</td>
                    <td>12/10/2023</td>
                    <td>DIANA VALENTINA TORREALVA LECCA</td>
                    <td>S/ 300.00</td>
                    <td>5</td>
                    <td>LAPTOP ACER CORE I3 8VA</td>
                    <td>
                        <div class="stag stag-cred-E">En Espera</div>
                    </td>
                    <td>
                        <div class="d-flex"><a href="http://localhost/sistema-dev/Credito/detalle/2551" class="tablespace-button"><i class="fe fe-layers"></i></a><button name="credito-notas-item" class="tablespace-button" data-credito="2551" data-serie="CH01-2970"> Notas</button></div>
                    </td>
                </tr>
                <tr>
                    <td>03</td>
                    <td>CH01-2970</td>
                    <td>12/10/2023</td>
                    <td>DIANA VALENTINA TORREALVA LECCA</td>
                    <td>S/ 300.00</td>
                    <td>5</td>
                    <td>LAPTOP ACER CORE I3 8VA</td>
                    <td>
                        <div class="stag stag-cred-E">En Espera</div>
                    </td>
                    <td>
                        <div class="d-flex"><a href="http://localhost/sistema-dev/Credito/detalle/2551" class="tablespace-button"><i class="fe fe-layers"></i></a><button name="credito-notas-item" class="tablespace-button" data-credito="2551" data-serie="CH01-2970"> Notas</button></div>
                    </td>
                </tr>
            </tbody>
        </table> -->
        <div id="tablespace_creditos"></div>
    </div>
</div>


<div id="modal_nota_list" class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h4 id="modal-nota-title" class="modal-title">
					<i class="fe fe-file text-blue"></i>
					<span id="modal_nota_list_title">_CODE_ - Lista de Notas</span>
				</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true" class="text-primary"></span>
				</button>
			</div>
            <div class="py-3 px-2 d-flex align-items-center flex-row-reverses">
                <button id="modal_btn_new_nota" class="tablespace-button">
                    <i class="fe fe-plus"></i> 
                    Nueva Nota
                </button>
            </div>
			<div class="modal-table">
                <table class="table-wrap">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Descripcion</th>
                            <th>Tipo</th>
                            <th>Fecha</th>
                            <th class="text-center">Eliminar</th>
                        </tr>
                    </thead>
                    <tbody id="notas_dataset">
                        <tr>
                            <td colspan="5" class="text-center">Sin Datos</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="modal-footer">                
                <button class="btn btn-danger" data-dismiss="modal">Cerrar</button>
            </div>
		</div>
	</div>
</div>

<div id="modal_nota_new" class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h4 id="modal-nota-title" class="modal-title">
					<i class="fe fe-file-plus text-blue"></i>
					<span id="modal_nota_new_title">_CODE_ - Nueva Nota</span>
				</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true" class="text-primary"></span>
				</button>
			</div>
			<form id="form_nota_new">
				<!-- <input type="hidden" name="idcode"> -->
				<div class="modal-body">
					<div class="row">
						<div class="col-xs-12 col-md-12">
							<div class="form-group">
								<label for="" class="font-weight-bold">Descripcion</label>
								<textarea name="descripcion" cols="10" rows="5" class="form-control"></textarea>
                                <div class="text-danger" id="nota-descripcion-feed"></div>
							</div>
						</div>
						<div class="col-xs-12 col-md-4">
							<div class="form-group">
								<div class="form-label">Tipo</div>
								<select name="tipo" class="form-control border-rock">
									<option value="NO">NOTA NORMAL</option>
									<option value="SI">NOTA ANCLADA</option>
								</select>
                                <div class="text-danger" id="nota-tipo-feed"></div>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer text-right">
					<button type="submit" class="btn btn-blue">
						<i class="fe fe-save"></i> Guardar
					</button>
                    <button id="modal_btn_back_nota" class="btn btn-secondary">
						Ver Notas
					</button>
					<button class="btn btn-secondary" data-dismiss="modal">
						Salir
					</button>
				</div>
			</form>
		</div>
	</div>
</div>

<div id="modal_add_filter" class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<form id="FormFilterCustom" class="modal-content">
			<div class="modal-header">
				<h4 id="modal-nota-title" class="modal-title">
					<i class="fe fe-filter text-blue"></i>
					<span>Agregar Filtro</span>
				</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true" class="text-primary"></span>
				</button>
			</div>
			<div class="modal-body py-2 px-1 mx-0">
                <div class="tablespace-data">
                    <table>
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Tipo/Objetivo</th>
                                <th>Valor</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>01</td>
                                <td class="w-50">
                                    <div class="tablespace-filter-search tablespace-filter-search-right mr-1">
                                        <select name="custom_filter_type" data-ref="1" class="tablespace-search-input w-100">
                                            <option value="">-SELECCION-</option>
                                            <option value="ESTADO">Estado</option>
                                            <option value="FECHA">Fecha</option>
                                        </select>
                                        <div class="filter-search-icon">
                                            <i class="fe fe-chevron-down"></i>
                                        </div>
                                    </div>
                                </td>
                                <td id="custom_filter_value_cell_1" name="custom_filter_value_cell" class="w-50">

                                </td>
                            </tr>
                            <tr>
                                <td>02</td>
                                <td>
                                    <div class="tablespace-filter-search tablespace-filter-search-right mr-1">
                                        <select name="custom_filter_type" class="tablespace-search-input w-100" data-ref="2">
                                            <option value="">-SELECCION-</option>
                                            <option value="ESTADO">Estado</option>
                                            <option value="FECHA">Fecha</option>
                                        </select>
                                        <div class="filter-search-icon">
                                            <i class="fe fe-chevron-down"></i>
                                        </div>
                                    </div>
                                </td>
                                <td id="custom_filter_value_cell_2" name="custom_filter_value_cell">
                                <!-- custom_filter_value_cell -->
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

            </div>
            <div class="modal-footer">                
                <button type="submit" class="btn btn-primary">
                    <i class="fe fe-chevrons-left"></i> Aplicar
                </button>
                <button class="btn btn-secondary" data-dismiss="modal">
                    Cerrar
                </button>
            </div>
		</form>
	</div>
</div>


<script src="<?= base_url() ?>js_modules/credito/index.js?v=17012025"></script>
