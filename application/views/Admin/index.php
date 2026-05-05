<input id="usercode-10112022" type="hidden" value="<?= $this->session->userdata('pres_ses_usuario'); ?>">
<div class="container">
    <div class="card">
        <div class="card-header">
            <div class="card-title text-indigo">
                <i class="fe fe-gitlab"></i>
                Admin
            </div>
        </div>
        <?php  if($this->session->userdata('pres_ses_rol') === 'Administrador'): ?>
        <div class="card-body">
            <h4 class="mt-0 mb-4">Usuarios</h4>
            <div class="d-flex">
                <a href="<?= base_url() ?>administrador/usuarios" class="btn btn-primary">Administrar</a>
            </div>
        </div>
        <div class="card-body">
            <h4 class="mt-0 mb-4">Cuota: Calculo de Vencimiento</h4>
            <form id="form_util_proxima_cuota" class="px-1 row">
                <div class="col-md-4">
                    <div class="form-label">Fecha</div>
                    <input type="date" class="form-control" name="fecha">
                </div>
                <div class="col-md-4">
                    <div class="form-label">Ciclo</div>
                    <select name="ciclo" class="form-control">
                        <option value="SEMANAL">Semanal</option>
                        <option value="MENSUAL">Mensual</option>
                    </select>
                </div>
                <div class="col-md-12 py-2">
                    <button type="submit" class="btn btn-primary">Calcular</button>
                </div>
                <pre id="calculo_cuota_proximo_area"></pre>
            </form>
        </div>

        <?php endif; ?>
        <!-- <div class="card-body">
            <h4 class="mt-0 mb-4">Remate</h4>
            <div class="d-flex">
                <button id="btn-remate-orden" class="btn btn-primary">Aplicar Ordenamiento</button>
            </div>
            <div class="console-log d-block">
                <pre id="remate-log-area"></pre>
            </div>
        </div>
        <div class="card-body">
            <h4 class="mt-0 mb-4">Usuarios</h4>
            <div class="d-flex">
                <button id="btn-usuario-encript" class="btn btn-primary">Encriptar Contraseñas</button>
            </div>
            <div class="console-log d-blocks" style="max-height: 250px;overflow: auto;">
                <pre id="usuario-log-area"></pre>
            </div>
        </div>
        <div class="card-body">
            <h4 class="mt-0 mb-4">Encriptacion</h4>
            <form id="decrypt-form" autocomplete="off">
                <div class="form-group">
                    <div class="form-label">Texto</div>
                    <input name="value" type="text" class="form-control border-rock">
                </div>
                <div class="form-group">
                    <div class="form-label">Accion</div>
                    <div class="custom-controls-stacked">
                        <label class="custom-control custom-radio custom-control-inline">
                            <input type="radio" class="custom-control-input" name="action" value="ENCRPT" checked="">
                            <span class="custom-control-label">Encriptar</span>
                        </label>
                        <label class="custom-control custom-radio custom-control-inline">
                            <input type="radio" class="custom-control-input" name="action" value="DCRPT">
                            <span class="custom-control-label">Desencriptar</span>
                        </label>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary d-inline-block">Procesar</button>
            </form>
            <div class="console-log d-blocks" style="max-height: 250px;overflow: auto;">
                <pre id="decrypt-log-area"></pre>
            </div>
        </div> -->
    </div>
</div>

<script src="<?= base_url() ?>js_modules/admin/admin.index.js"></script>