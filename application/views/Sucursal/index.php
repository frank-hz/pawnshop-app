<script src="<?= base_url() ?>assets/plugins/sheetjs/dist/FileSaver.js"></script>
<script src="<?= base_url() ?>assets/plugins/sheetjs/dist/xlsx.full.min.js"></script>

<div class="container">
    <div class="modulo">
        <div class="modulo-header" style="border-bottom: none;">
            <h3 class="modulo-head-title">
                <i class="fe fe-compass"></i> 
                Sucursales
            </h3>
            <div class="modulo-options d-flex">
                <div>
                    <a id="btn-sucursal-nuevo" href="<?= base_url() ?>Sucursal/nuevo" class="btn btn-blue" title="Agregar Sucursal">
                        <i class="fe fe-plus-circle"></i> 
                        Nueva Sucursal
                    </a>
                    <button id="btn-export-excel" class="btn btn-excel" title="Exportar Excel">
                        <i class="fe fe-download"></i> 
                        Excel
                    </button>
                </div>
            </div>
        </div>
        <div id="data-loader" class="modulo-loader-content">
            <div class="modulo-loader-area">
                <div class="modulo-loading-label">Cargando </div>
                <div class="modulo-loading-icon">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
            </div>        
        </div>
        <div class="modulo-datatable">
            <table>
                <thead>
                    <tr>
                        <th class="px-2 text-center">#</th>
                        <th>Nombre</th>
                        <th>Ciudad</th>
                        <th>Direccion</th>
                        <th>Estado</th>
                        <th class="text-center">Accion</th>
                    </tr>
                </thead>
                <tbody id="datatable-sucursal"></tbody>
            </table>
        </div>
    </div>
</div>



<script src="<?= base_url() ?>js_modules/sucursal/sucursal.index.js"></script>