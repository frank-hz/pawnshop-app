<link href="<?= base_url("assets/css/inicio.rework.css?v=05022025"); ?>" rel="stylesheet">
<script src="<?= base_url() ?>assets/plugins/sheetjs/dist/FileSaver.js"></script>
<script src="<?= base_url() ?>assets/plugins/sheetjs/dist/xlsx.full.min.js"></script>
<script src="<?= base_url() ?>assets/plugins/html2canvas.min.js"></script>

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
            <button id="btn_resumen_open" class="btn btn-dark">
                <i class="fe fe-sunset"></i> Resumen MOV
            </button>
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




<div id="VIEW_RESUMEN_DIA" class="resumen-wrapper">
  <div id="RESUMEN_VIEW" class="resumen-grid">
    <div class="resumen-title">
      <div class="">
        <h1 id="RESUMEN_TITLE">RESUMEN [00/00/0000]</h1>
        <small id="SEDE_NAME">SEDE STORE 01</small>
      </div>
      <div class="">
        <button id="btn_resumen_download">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
            class="feather feather-camera">
            <path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"></path>
            <circle cx="12" cy="13" r="4"></circle>
          </svg>
        </button>
        <button id="btn_resumen_close">
          <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
            class="feather feather-x">
            <line x1="18" y1="6" x2="6" y2="18"></line>
            <line x1="6" y1="6" x2="18" y2="18"></line>
          </svg>
        </button>
      </div>
    </div>
    <div class="list-widget" style="flex-">
      <div class="wsublist">
        <div class="resumen-widget">
          <h1 id="CREDITOS_CREADOS">00</h1>
          <p>Creditos Creados</p>
        </div>
        <div class="resumen-widget">
          <h1 id="PAGOS_RECIBIDOS">00</h1>
          <p>Pagos Recibidos</p>
        </div>
        <div class="resumen-widget">
          <h1 id="RECUPERACIONES">00</h1>
          <p>Recuperaciones</p>
        </div>
      </div>
      <div class="wsublist">
        <div class="resumen-widget">
          <h1 id="TOTAL_INGRESO">S/ 0.00</h1>
          <p>Total Ingreso</p>
        </div>
        <div class="resumen-widget">
          <h1 id="TOTAL_EGRESO">S/ 0.00</h1>
          <p>Total Egreso</p>
        </div>
        <div class="resumen-widget">
          <h1 id="TOTAL_BALANCE">S/ 0.00</h1>
          <p>Balance</p>
        </div>
      </div>
    </div>

    <div class="resumen-card">
      <div class="resumen-card-header">
        PAGOS
      </div>
      <table>
        <thead>
          <tr>
            <th>Credito</th>
            <th>Cliente</th>
            <th>Prenda</th>
            <th>Forma Pago</th>
            <th>Concepto</th>
            <th>Monto</th>
          </tr>
        </thead>
        <tbody id="TABLE_PAGOS">
          <tr>
            <td>C0-4547</td>
            <td>ELMER PEÑA CORDOVA</td>
            <td>LAPTOP GAMER ACER COREI5</td>
            <td>S/ 125.60</td>
            <td>VIRTUAL</td>
            <td>RECUPERACION</td>
          </tr>
          <tr>
            <td>C0-4547</td>
            <td>ELMER PEÑA CORDOVA</td>
            <td>LAPTOP GAMER ACER COREI5</td>
            <td>S/ 125.60</td>
            <td>VIRTUAL</td>
            <td>RECUPERACION</td>
          </tr>
          <tr>
            <td>C0-4547</td>
            <td>ELMER PEÑA CORDOVA</td>
            <td>LAPTOP GAMER ACER COREI5</td>
            <td>S/ 125.60</td>
            <td>VIRTUAL</td>
            <td>RECUPERACION</td>
          </tr>
        </tbody>
      </table>
    </div>

    <div class="resumen-card">
      <div class="resumen-card-header">
        CREDITOS
      </div>
      <table>
        <thead>
          <tr>
            <th>Credito</th>
            <th>Cliente</th>
            <th>Prenda</th>
            <th>Forma Pago</th>
            <th>Monto</th>
          </tr>
        </thead>
        <tbody id="TABLE_CREDITOS">
          <tr>
            <td>C0-6957</td>
            <td>ELMER PEÑA CORDOVA</td>
            <td>LAPTOP GAMER ACER COREI5</td>
            <td>S/ 125.60</td>
            <td>VIRTUAL</td>
          </tr>
        </tbody>
      </table>
    </div>

  </div>
</div>
<style>
/* VIEW RESUMEN */
.resumen-wrapper {
  width: 100%;
  height: 100%;
  display: flex;
  justify-content: center;
  align-items: center;
  background: rgba(0, 0, 0, .6);
  top: 0;
  left: 0;
  position: fixed;

  opacity: 0;
  visibility: hidden;
  transform: scale(1.1);
  transition: opacity .3s ease, transform .3s ease, visibility .3s;
}
.resumen-wrapper.isActive {
  opacity: 1;
  visibility: visible;
  transform: scale(1);
}
.resumen-grid {
  min-width: 900px;
  height: auto;
  background: #fff;
  border-radius: 6px;
  display: flex;
  flex-direction: column;
  padding: .8rem;
  max-height: 100vh;
  overflow-y: auto;
}
.resumen-card {
  /* border-top: 1px solid rgba(188, 199, 213, .6); */
}

.resumen-card-header {
  font-size: 1rem;
  font-weight: 800;
  padding: .6rem .4rem;
  color: #1d2534;
}

.resumen-card table {
  width: 100%;
  border: none;
  border-collapse: collapse;
}

.resumen-card table td,
.resumen-card table th {
  border: none;
  padding: .4rem .6rem;
  border: 1px solid rgba(188, 199, 213, .7);
  border-left: none;
  border-right: none;
}

.resumen-card table tr:last-child td,
.resumen-card table tr:last-child th {
  border: none;
}

.resumen-title {
  padding: .4rem .5rem;
  display: flex;
  justify-content: space-between;
  margin-bottom: 7px;
}

.resumen-title h1 {
  font-size: 1.2rem;
  font-weight: 800;
  margin: 0;
  color: #1d2534;
}

.resumen-title small {
  line-height: 1.1rem;
  font-weight: 600;
  color: #1d2534;
  display: block;
}

.resumen-title button {
  display: inline-flex;
  min-width: 40px;
  min-height: 40px;
  padding: .1rem;
  border-radius: 4px;
  text-align: center;
  justify-content: center;
  align-items: center;
  outline: none;
  transition: all .2s;
  border: 2px solid #1d2534;
  border: none;
  cursor: pointer;
  opacity: .2;
}

.resumen-title button svg {
  stroke: #1d2534;
  transition: all .2s;
  width: 24px;
  height: 24px;
  pointer-events: none;
}

.resumen-title button:hover {
  background: #1d2534;
  color: #fff;
  opacity: 1;
}

.resumen-title button:hover svg {
  stroke: #fff;
}
.list-widget {
  display: flex;
  padding: .3rem;
  justify-content: space-between;
  flex-basis: auto;
  gap: 4px 2px;
}

.list-widget .wsublist {
  display: flex;
  align-items: center;
  gap: 0 4px;
}

.resumen-widget {
  width: auto;
  display: flex;
  flex-direction: column;
  justify-content: center;
  text-align: center;
  margin: 1px 2px;
  color: #1d2534;
}

.resumen-widget h1 {
  margin: 0;
  font-size: 1.6rem;
  font-weight: 800;
  text-align: center !important;
}

.resumen-widget p {
  display: inline-block;
  font-size: .8rem;
  font-weight: 500;
  padding: 0;
  margin: 0;
  text-align: right;
}
</style>
<!-- <script src="<?= base_url("assets/js/ag-charts-community.js"); ?>"></script> -->
<!-- <script src="<?= base_url("assets/plugins/canvasjs-chart/canvasjs.min.js"); ?>"></script> -->

<script src="<?= base_url("assets/plugins/chartjs/chart.js"); ?>"></script>
<script src="<?= base_url("js_modules/inicio/inicio.rework.js?v=24012025"); ?>"></script>