<link href="<?= base_url("assets/css/inicio.rework.css?v=05022025"); ?>" rel="stylesheet">
<script src="<?= base_url() ?>assets/plugins/sheetjs/dist/FileSaver.js"></script>
<script src="<?= base_url() ?>assets/plugins/sheetjs/dist/xlsx.full.min.js"></script>

<script>
    window.creditos = <?php echo json_encode($creditos_activos); ?>;
    window.balance_semanal = <?php echo json_encode($balance_semanal); ?>;
</script>
<div class="inicioLayout">
    <div class="inicioHeader">
        <div class="inicioTitle">
            <h2>ELECTROCASH - <?= $this->session->userdata('sucursal_nombre'); ?></h2>
            <p><?php echo current_date_display(); ?></p>
        </div>
        <div class="inicioTitleButtons">
            <a class="btn btn-indigo text-light" href="<?= base_url('credito/nuevo') ?>">
                <i class="fe fe-file"></i> Nuevo Credito
            </a>
            <a class="btn btn-indigo text-light" href="<?= base_url('cliente/nuevo') ?>">
                <i class="fe fe-user"></i> Nuevo Cliente
            </a>
        </div>
    </div>
    <div class="inicioContent">
        <div class="widgets md">
            <div class="card widget-information mb-1">
                <div class="card-header p-3">
                    <h3 class="card-title">
                        <i class="fe fe-package text-indigo"></i> 
                        Datos Generales
                    </h3>
                </div>
                <div class="py-2 pb-0 px-1">
                    <div class="label-info">
                        <span class="text-indigo font-600">
                            <i class="fe fe-chevrons-right text-indigo"></i>&nbsp;Balance Total
                        </span>
                        <h2 id="_balance_total">
                            <?= $balance_total ?? 0; ?>
                        </h2>
                    </div>
                    <div class="label-info">
                        <span>
                            <i class="fe fe-chevrons-right text-indigo"></i>&nbsp;Recuperado
                        </span>
                        <h2 id="_balance_recuperado">
                            <?= $monto_recuperado ?? 0; ?>
                        </h2>
                    </div>
                    <div class="label-info">
                        <span>
                            <i class="fe fe-chevrons-right text-indigo"></i>&nbsp;Generado
                        </span>
                        <h2 id="_balance_prestado">
                            <?= $monto_prestado ?? 0; ?>
                        </h2>
                    </div>
                </div>
            </div>
            <div class="card mb-1">
                <div class="card-body p-1 py-3">
                    <div class="label-info">
                        <p>
                            <i class="fe fe-chevrons-right text-indigo"></i> Creditos Nuevos
                        </p>
                        <h2 id="_creditos_nuevos">
                            <?= $cantidad_nuevos ?? 0; ?>
                        </h2>
                    </div>
                    <div class="label-info">
                        <p>
                            <i class="fe fe-chevrons-right text-indigo"></i> Creditos Activos
                        </p>
                        <h2 id="_creditos_activos">
                            <?= $cantidad_activos ?? 0; ?>
                        </h2>
                    </div>
                    <div class="label-info">
                        <p>
                            <i class="fe fe-chevrons-right text-indigo"></i> Creditos Vencidos
                        </p>
                        <h2 id="_creditos_vencidos">
                            <?= $cantidad_vencidos ?? 0; ?>
                        </h2>
                    </div>
                    <div class="label-info">
                        <p>
                            <i class="fe fe-chevrons-right text-indigo"></i>
                            Vencen Hoy
                        </p>
                        <h2 id="_creditos_cumplidos">
                            <?= $cantidad_cumplidos ?? 0; ?>
                        </h2>
                    </div>
                </div>
            </div>
            <div class="card p-0 mb-1" style="overflow-x:auto;">
                <div class="graphBalance">
                    <canvas id="chartBalance"></canvas>
                </div>
            </div>   

        </div>
        <div class="records card">
            <div class="card-header py-1 px-2">
                <div class="card-title p-3">
                    <i class="fe fe-file text-indigo"></i> Reporte Diario
                </div>
                <div class="card-options px-2">
                    <button id="btn_exportar" class="btn btn-excel">
                        <i class="fe fe-download"></i> Exportar
                    </button>
                </div>
            </div>
            <div class="card-body p-1 records-table">
                <table class="table text-nowrap">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Credito</th>
                            <th>Cliente</th>
                            <th>Calificacion</th>
                            <th>Prestamo</th>
                            <th class="text-center">Cuotas Venc.</th>
                            <th>Prenda</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if(!empty($creditos_activos)): ?>
                        <?php foreach($creditos_activos as $index => $credito): ?>
                        <tr>
                            <td><?= $index+1; ?></td>
                            <td>
                                <a href="<?= base_url("credito/detalle/".$credito->id) ?>">
                                    <?= $credito->serie; ?>
                                </a>
                            </td>
                            <td>
                                <a href="<?= base_url("cliente/detalle/".$credito->cliente_id); ?>">
                                    <?= $credito->cliente_nombre; ?>
                                </a>
                            </td>
                            <td class="text-center">
                                <span class="enc1217-conducta-sm-tag enc1212-conducta-tag-<?= $credito->cliente_puntuacion; ?>">
                                    <?= $credito->cliente_puntuacion; ?>
                                </span>
                            </td>
                            <td>S/ <?= $credito->monto; ?></td>
                            <td class="text-center"><?= $credito->cuotas_vencidas; ?></td>
                            <td><?= $credito->prenda_descripcion; ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center">Sin Datos</td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>


        </div>
        
        
    </div>
</div>


<!-- <script src="<?= base_url("assets/js/ag-charts-community.js"); ?>"></script> -->
<!-- <script src="<?= base_url("assets/plugins/canvasjs-chart/canvasjs.min.js"); ?>"></script> -->

<script src="<?= base_url("assets/plugins/chartjs/chart.js"); ?>"></script>
<script src="<?= base_url("js_modules/inicio/inicio.rework.js?v=24012025"); ?>"></script>