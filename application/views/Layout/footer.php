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

    <div id="accesoRapido" class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="mdPrendaAgregarLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title" id="mdCiudadLabel">
              <i class="fe fe-grid text-blue"></i>
              Acceso Rapido
            </h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true"></span>
            </button>
          </div>
          <div class="modal-body">
              <div class="row">
                <div class="col-md-4 text-center">
                  <div class="card">
                    <i class="d-block text-center fe fe-file text-blue"></i>
                  <a href="">Registrar Credito</a>
                  </div>
                </div>
                <div class="col-md-4">
                  <a href="">Registrar Credito</a>
                </div>
                <div class="col-md-4">
                  <a href="">Registrar Credito</a>
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

      /*.tablax tr:nth-child(even) {
        background-color: #dddddd;
      }*/
    </style>
    <!-- <script src="<?= base_url() ?>node_modules/vue/dist/vue.min.js"></script> -->
    
	  <script src="<?= base_url() ?>js_modules/app.js?v=19012025"></script>
    <script src="<?= base_url() ?>js_modules/dashboard.js?v=19012025"></script>
  </body>
</html>