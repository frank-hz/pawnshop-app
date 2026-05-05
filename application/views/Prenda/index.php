<script src="<?= base_url() ?>assets/plugins/sheetjs/dist/FileSaver.js"></script>
<link rel="stylesheet" href="<?= base_url() ?>assets/plugins/gridjs/mermaid.min.css">
<script src="<?= base_url() ?>assets/plugins/gridjs/gridjs.production.min.js"></script>
<script src="<?= base_url() ?>assets/plugins/sheetjs/dist/xlsx.full.min.js"></script>
<link href="<?= base_url() ?>assets/css/tables.css?v=4" rel="stylesheet">

<div class="tablespace">
    <div class="tablespace-header">
        <div class="tablespace-title">
            <i class="fe fe-tv text-indigo"></i> 
            Prendas
        </div>
        <div class="tablespace-options">
            <button id="btn_export" class="tablespace-option" title="Exportar Excel">
                <i class="fe fe-download mr-1"></i> 
                Exportar
            </button>
        </div>
    </div>
    <div class="tablespace-filters">
        <div class="tablespace-filters-header">
            <div class="tablespace-filter-row py-3">
                <div class="form-group">
                    <div class="form-label">Tipo de Prenda:</div>
                    <div class="tablespace-filter-search tablespace-filter-search-right mr-1">
                        <select id="input_tipo" class="tablespace-search-input">
                            <option value="">-SELECCIONAR-</option>
                            <!-- <option value="1" selected>ELECTRO</option>
                            <option value="2">JOYA</option>
                            <option value="3">VEHICULO</option>
                            <option value="4">PREDIO</option> -->
                        </select>
                        <div class="filter-search-icon">
                            <i class="fe fe-chevron-down"></i>
                        </div>
                    </div>
                </div>
                <div id="FILTER_CATEGORIA" class="form-group">
                    <div class="form-label">Categoria:</div>
                    <div class="tablespace-filter-search tablespace-filter-search-right mr-1">
                        <select id="input_categoria" class="tablespace-search-input">
                            <option value="" selected>-SELECCIONAR-</option>
                        </select>
                        <div class="filter-search-icon">
                            <i class="fe fe-chevron-down"></i>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="form-label">Descripcion:</div>
                    <div class="tablespace-filter-search  mr-1">
                        <input id="input_descripcion" type="text" class="tablespace-search-input" placeholder="Ingrese descripcion..">
                        <div class="filter-search-icon">
                            <i class="fe fe-search"></i>
                        </div>
                    </div>
                </div>
                <div id="FILTER_MARCA" class="form-group d-none">
                    <div class="form-label">Marca:</div>
                    <div class="tablespace-filter-search tablespace-filter-search-right mr-1">
                        <select id="input_marca" class="tablespace-search-input">
                            <option value="">-SELECCIONAR-</option>
                        </select>
                        <div class="filter-search-icon">
                            <i class="fe fe-chevron-down"></i>
                        </div>
                    </div>
                </div>  
                <div id="FILTER_MODELO" class="form-group d-none">
                    <div class="form-label">Modelo:</div>
                    <div class="tablespace-filter-search  mr-1">
                        <input id="input_modelo" type="text" class="tablespace-search-input" placeholder="Busqueda Rapida">
                        <div class="filter-search-icon">
                            <i class="fe fe-search"></i>
                        </div>
                    </div>
                </div>
                <div id="FILTER_SERIE" class="form-group d-none">
                    <div class="form-label">Serie:</div>
                    <div class="tablespace-filter-search  mr-1">
                        <input id="input_serie" type="text" class="tablespace-search-input" placeholder="Busqueda Rapida">
                        <div class="filter-search-icon">
                            <i class="fe fe-search"></i>
                        </div>
                    </div>
                </div>
                <div id="FILTER_QUILATE" class="form-group d-none">
                    <div class="form-label">Quilate:</div>
                    <div class="tablespace-filter-search tablespace-filter-search-right mr-1">
                        <select id="input_quilate" class="tablespace-search-input">
                          <option value="">-SELECCIONAR-</option>
                        </select>
                        <div class="filter-search-icon">
                            <i class="fe fe-chevron-down"></i>
                        </div>
                    </div>
                </div>                
                <div id="FILTER_PLACA" class="form-group d-none">
                    <div class="form-label">Placa:</div>
                    <div class="tablespace-filter-search  mr-1">
                        <input id="input_placa" type="text" class="tablespace-search-input" placeholder="Busqueda Rapida">
                        <div class="filter-search-icon">
                            <i class="fe fe-search"></i>
                        </div>
                    </div>
                </div>
                <div id="FILTER_COLOR" class="form-group d-none">
                    <div class="form-label">Color:</div>
                    <div class="tablespace-filter-search tablespace-filter-search-right mr-1">
                        <select id="input_color" class="tablespace-search-input">
                            <option value="">-SELECCIONAR-</option>
                        </select>
                        <div class="filter-search-icon">
                            <i class="fe fe-chevron-down"></i>
                        </div>
                    </div>
                </div>
                <div id="FILTER_ORDEN" class="form-group">
                    <div class="form-label">Tamaño Muestra:</div>
                    <div class="tablespace-filter-search tablespace-filter-search-right mr-1">
                        <select id="input_muestra" class="tablespace-search-input">
                            <option value="0">TODO</option>
                            <option value="50" selected>50</option>
                            <option value="100">100</option>
                            <option value="200">200</option>
                        </select>
                        <div class="filter-search-icon">
                            <i class="fe fe-chevron-down"></i>
                        </div>
                    </div>
                </div>                
            </div>
            <div class="tablespace-filter-buttonsx mt-2">                
                <!-- <button id="btn_open_modalFILTER" class="tablespace-button">
                    <i class="fe fe-plus"></i>
                    Agregar Filtro
                </button> -->
                <!-- <button id="btnResetFilters" class="tablespace-button">
                    <i class="fe fe-refresh-cw"></i>&nbsp; Reiniciar Filtros
                </button> -->

            </div>
        </div>
    </div>
    <div class="tablespace-data border-top-0">
        <div id="TABLE_PRENDAS"></div>
    </div>
</div>




<script src="<?= base_url() ?>js_modules/prenda/index.js?v=300325"></script>
