<?php ini_set('display_errors', 0); ?>
<script src="<?= base_url() ?>assets/js/soda.elements.js"></script>
<link rel="stylesheet" href="<?= base_url() ?>assets/plugins/gridjs/mermaid.min.css">
<script src="<?= base_url() ?>assets/plugins/gridjs/gridjs.production.min.js"></script>

<div class="row p-3"  style="background: #ecf0f6;">
  <div class="col-xs-12 col-md-4">
    <div class="sidebox">
      <div class="sidebox-header">
        <div class="side-title">
          <?php echo $sucursal->descripcion; ?> - 
          <span><?php echo $sucursal->ciudad; ?>, <?php echo $sucursal->departamento; ?> </span>
        </div>
        <div id="not-general" class="side-loader-area">
          <!-- <div class="side-loader">          
            <span>Cargando </span>
            <svg xmlns="https://www.w3.org/2000/svg" viewBox="0 0 16 16">
              <circle r="4" cx="8" cy="8" fill="transparent"/>
            </svg>
          </div> -->    
        </div>
      </div>
      <div class="sidebox-controls">
        <div>
         <a href="<?= base_url() ?>Credito/nuevo" class="sidebtn sidebtn-bluex">
          <i class="fe fe-file-plus"></i>
          Nuevo Credito
        </a>
        <a href="<?= base_url() ?>Cliente/nuevo" class="sidebtn sidebtn-bluex">
          <i class="fe fe-user"></i>
          Nuevo Cliente
        </a>          
        </div>
        <div>
          <button id="btn-side-reload-data" class="sidebtn">
            <i class="fe fe-refresh-cw"></i>
            Recargar Data
          </button>
        </div>
      </div>
      <div class="side-alert">
        Cargando...
      </div>
      <div class="sidebox-content">
        <ul class="sidelist">
          <li>
            <span>Creditos Activos</span>
            <i id="cnt-activos">--</i>
          </li>
          <li>
            <span>Creditos Vencidos</span>
            <i id="cnt-vencidos">--</i>
          </li>
          <li>
            <span>Creditos que cumplen hoy</span>
            <i id="cnt-cumplidos">--</i>
          </li>
        </ul>
      </div>
      <div class="sidebox-content">
        <ul class="sidelist">
          <li>
            <span>Creditos registrados</span>
            <i id="mv-registros">--</i>
          </li>
          <li class="red">
            <span>Prestado</span>
            <i id="mv-prestado">S/ --</i>
          </li>
          <li class="green">
            <span>Recuperado</span>
            <i id="mv-recuperado">S/ --</i>
          </li>
          <li class="black">
            <span>
              Diferencia
              <small class="d-block">(Prestado - Recuperado)</small>
            </span>
            <i id="mv-diferencia">S/ --</i>
          </li>
        </ul>
      </div>
    </div>
  </div>

  <div class="col-xs-12 col-md-8">
    <div class="repo-dataset">
      <div class="repo-header">
        <div class="repo-filters">
          <label for="radio-option-general" class="radio-label">
            <input id="radio-option-general" type="radio" name="repo-radio-option" value="general" checked>
            <span>Data General</span>
          </label>
          <label for="radio-option-cumplido" class="radio-label">
            <input id="radio-option-cumplido" type="radio" name="repo-radio-option" value="cumplido">
            <span>Vencen Hoy</span>
          </label>
          <label for="radio-option-remate-prox" class="radio-label">
            <input id="radio-option-remate-prox" type="radio" name="repo-radio-option" value="prox-remate">
            <span>Por Entrar a Remate</span>
          </label>
        </div>
        <div class="repo-options">
          <a href="<?= base_url() ?>Absolver/generate_pdf" target="_blank">
            <i class="fe fe-download"></i>
          </a>
          <a href="javascript:void(0)" id="btn-rep-reload-data">
            <i class="fe fe-refresh-cw"></i>
          </a>
        </div>
      </div>
      <div id="not-reporte" class="side-loader-area">
        <!-- <div class="side-loader">
          <svg xmlns="https://www.w3.org/2000/svg" viewBox="0 0 16 16">
            <circle r="4" cx="8" cy="8" fill="transparent"/>
          </svg>
          <span>Cargando..</span>
        </div> -->
      </div>
      <div class="content-table table-responsive">
        <table id="repo-table" class="repo-table">
          <thead>
            <tr>
              <th>Status</th>
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
          <tbody id="repo-data">
            <tr>
              <td colspan="11" class="text-center">Sin Resultados</td>
            </tr>
          </tbody>
        </table>        
      </div>
    </div>
    <!-- #Prueba
    <div id="test"></div> -->

  </div>
</div>

<script src="<?= base_url() ?>js_modules/inicio/inicio.view.js"></script>
