		</div>
      </div>
      <div class="footer">
        
      </div>
    </div>
    
    <div id="preload_app" class="load-overlay">
      <div class="box-view">
        <div class="box-load">
          <!-- <img src="<?= base_url() ?>assets/images/loader3.svg" alt=""> -->
          <div class="spinner-icon"></div>
        </div>
        <div class="box-mssg" id="pre_text"></div>
      </div>
    </div>

    <div id="layout_modal_cotizador" class="modalz size-md zoomIn">
      <div class="modalz-content">
        <span id="layout_modal_close" class="mzClose" onclick="closeModal()">&#215;</span>
        <div class="cotizador-grid">
          <div class="cotiz-header">
            <h3>Cotizador</h3>
          </div>
          <div class="cotiz-form">
            <form id="layout_form_cotizador" class="d-flex justify-center">
              <div class="form-group">
                <label class="form-label">Monto (S/):</label>
                <div class="cotiz-input-button">
                  <input type="number" name="monto" step="any" class="form-control form-control-lg cotiz-input">
                  <button class="btn btn-lg btn-indigo">
                    <i class="fe fe-search"></i>
                  </button>
                </div>
              </div>  
            </form>
            <div class="d-block">
              <div id="cotiza_log" class="text-center"></div>
            </div>
          </div>
          <div class="cotiz-content">
            <h5>Resultado: <span id="cotiz_loader" class="spinner-v1-mini d-none"></span> </h5>
            <div class="cotiz-results">
              <div class="cotiz-item">
                <h3 id="cotiz_cuota_value">S/ 0.00</h3>
                <span>Cuota</span>
              </div>
              <div class="cotiz-item">
                <h3 id="cotiz_penalidad_value">S/ 0.00</h3>
                <span>Penalidad</span>
              </div>
              <div class="cotiz-item">
                <h3 id="cotiz_tasa_value">--</h3>
                <span>Tasa</span>
              </div>

            </div>
          </div>
          
        </div>
      </div>
    </div>
    <div id="md_definir_sucursal" class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="mdClienteBuscarLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title" id="md_definir_sucursalLabel">
              <i class="fe fe-map-pin text-blue"></i>
              Cambiar Sucursal
            </h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true"></span>
            </button>
          </div>
          <div class="modal-body p-0 m-0">
            <div id="tb_sucursal_definir" class="sede-gallery">
              <!-- <div class="sede-item">
                <input id="item_1" type="radio" name="sede-item">
                <label for="item_1">
                  <div class="sede-item-title">SEDE CHIMBOTE</div>
                  <div class="sede-item-subtitle">Chimbote</div>
                </label>
              </div>
              <div class="sede-item">
                <input id="item_2" type="radio" name="sede-item">
                <label for="item_2">
                  <div class="sede-item-title">SEDE CASN</div>
                  <div class="sede-item-subtitle">Casma</div>
                </label>
              </div> -->
            </div>

          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-danger" data-dismiss="modal">
              Cancelar
              <i class="fe fe-corner-arrow-down"></i>
            </button>
          </div>
        </div>
      </div>
    </div>

    <style>
      .tablax {
        width: 100%;
        border-collapse: collapse;
      }
      .tablax td,.tablax th  {
        border: 1px solid #dddddd;
        
        text-align: left;
        padding: 4px 8px;
      }
    </style>    
	  <script src="<?= base_url() ?>js_modules/app.js?v=19012025"></script>
    <script src="<?= base_url() ?>js_modules/dashboard.js?v=31052026"></script>
  </body>
</html>