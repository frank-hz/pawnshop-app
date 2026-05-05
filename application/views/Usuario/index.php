<div class="container">
    <div class="modulo">
        <div class="modulo-header" style="border-bottom: none;">
            <h3 class="modulo-head-title">
                <i class="fe fe-user"></i> 
                Usuarios
            </h3>
            <div class="modulo-options d-flex">
                <div>
                    <a href="<?= base_url() ?>Empleado/nuevo" class="btn btn-blue">
                        <i class="fe fe-plus-circle"></i> 
                        Nuevo Empleado
                    </a>
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
                        <th>Nombres y Apellidos</th>
                        <th>Usuario</th>
                        <th>Rol</th>
                        <th>Estado</th>
                        <th class="text-center">Accion</th>
                    </tr>
                </thead>
                <tbody id="datatable-usuario"></tbody>
            </table>
        </div>
    </div>
</div>

<script src="<?= base_url() ?>js_modules/usuario/usuario.index.js"></script>