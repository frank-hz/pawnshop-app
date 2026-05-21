<div class="row p-2 px-0">
    <div class="col-xs-12 col-md-3 p-1">
        <div class="modulo">
            <div class="modulo-header py-3">
                <div class="modulo-head-title">
                    <i class="fe fe-server text-indigo"></i>&nbsp; 
                    Datos Generales
                </div>
            </div>
            <div class="modulo-body py-3">
                <div class="">
                    <a href="<?= base_url() ?>Credito/nuevo" class="btn btn-block btn-indigo py-3">
                        <i class="fe fe-file-plus"></i>
                        Nuevo Credito
                    </a>
                    <a href="<?= base_url() ?>Cliente/nuevo" class="btn btn-block btn-indigo py-3">
                        <i class="fe fe-user"></i>
                        Nuevo Cliente
                    </a> 
                </div>
            </div>
            <div class="modulo-table-list py-2">
                <table class="text-nowrap">
                    <tr>
                        <td>
                            <div class="celltag">Creditos Activos</div>
                        </td>
                        <td>
                            <div id="cnt-activos" class="celltag number-font" style="font-size:1rem">0</div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="celltag">Creditos Vencidos</div>
                        </td>
                        <td>
                            <div id="cnt-vencidos" class="celltag number-font" style="font-size:1rem">0</div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="celltag">Creditos Cumplen Hoy</div>
                        </td>
                        <td>
                            <div id="cnt-cumplidos" class="celltag number-font" style="font-size:1rem">0</div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="celltag">Creditos Registrado</div>
                        </td>
                        <td>
                            <div id="mv-registros" class="celltag number-font" style="font-size:1rem">0</div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="celltag celltag-green">Prestado</div>
                        </td>
                        <td>
                            <div id="mv-prestado" class="celltag celltag-green number-font" style="font-size:1rem">0</div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="celltag celltag-red">Recuperados</div>
                        </td>
                        <td>
                            <div id="mv-recuperado" class="celltag celltag-red number-font" style="font-size:1rem">0</div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="celltag celltag-black">Diferencia</div>
                        </td>
                        <td>
                            <div id="mv-diferencia" class="celltag celltag-black number-font" style="font-size:1rem">0</div>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    <div class="col-xs-12 col-md-9 p-1">
        <div class="modulo">
            <div class="modulo-header py-3" style="flex-wrap: wrap;">
                <div class="modulo-head-title">
                    <i class="fe fe-sidebar text-indigo"></i> 
                    Reporte Diario
                </div>
                
            </div>
            <div class="card-header px-3 justify-between">
                <div class="modulo-options">
                    <div class="modulo-radio-list">
                        <label for="repoption-general" class="modulo-radio-item">
                            <input id="repoption-general" type="radio" name="repo-radio-option" value="GENERAL" checked="">
                            <span>Data General</span>
                        </label>
                        <label for="repoption-vencehoy" class="modulo-radio-item">
                            <input id="repoption-vencehoy" type="radio" name="repo-radio-option" value="VENCEHOY">
                            <span>Vence Hoy</span>
                        </label>
                        <label for="repoption-remate" class="modulo-radio-item">
                            <input id="repoption-remate" type="radio" name="repo-radio-option" value="PROXREMATE">
                            <span>Entrando a Remate</span>
                        </label>
                    </div>
                </div>
                <a href="<?= base_url() ?>Absolver/generate_pdf" class="btn-cell" title="Descargar Reporte" target="_blank">
                    <i class="fe fe-download"></i>
                </a>
            </div>
            <div class="modulo-report-table" style="overflow-x: auto;">
                <table>
                    <thead>
                        <tr>
                            <th class="w-1">Status</th>
                            <th>Credito</th>
                            <th>Nro Cuota</th>
                            <th>Cliente</th>
                            <th>Prestamo</th>
                            <th>Interes</th>
                            <th>Mora</th>
                            <th>Vencimiento</th>
                            <th>Mora Total</th>
                            <th>Dias</th>
                            <th>Prorrateo</th>
                        </tr>
                    </thead>
                    <tbody id="repo_data"></tbody>
                </table>
            </div>
        </div>

    </div>
</div>

<script src="<?= base_url() ?>js_modules/inicio/inicio.homepage.js?v=2"></script>