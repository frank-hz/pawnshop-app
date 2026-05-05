<!-- Code: enc1212 -->
<?php ini_set('display_errors', 0); ?>

<input id="id_cliente" type="hidden" value="<?= $cliente->id; ?>">
<div class="containers px-3 py-1">
  <div class="row">
    <div class="col-xs-12 col-lg-3 p-0">
      <div class="card p-0 mb-3">
        <div id="cond-puntuacion" class="py-2 d-flex justify-center">
          <!-- <div class="spinner-v1"></div> -->
          [NO DATA]
        </div>
        <div class="enc1212-cond-sub">Puntuacion</div>
        <div id="cond-block-banner" class="enc1212-cond-blocked d-none">
        </div>
      </div>
      <div class="card">
        <div class="card-header">
          <h3 class="card-title text-blue form-label">Opciones</h3>
        </div>
        <div class="list-group">
          <a id="btn-cliente-edit" href="javascript:void(0)" class="list-group-item list-group-item-action text-blue py-2" data-idcliente="<?= $cliente->id; ?>">
            <span class="icon mr-3"><i class="fe fe-edit-2 text-blue"></i></span>
            Editar Datos
          </a>
          <a id="btn-conducta-edit" href="javascript:void(0)" class="list-group-item list-group-item-action text-blue py-2" data-idcliente="<?= $cliente->id; ?>">
            <span class="icon mr-3"><i class="fe fe-edit text-blue"></i></span>
            Editar Conducta
          </a>
        </div>
      </div>
    </div>
    <div class="col-xs-12 col-lg-9">
      <div class="card mb-1 border-fx-1">
        <div class="card-header">
          <div class="card-title text-blue font-weight-bold">
            <i class="fe fe-layers text-blue"></i>
            <span id="dg-nombre">
              <?php echo $cliente->nombres.' '.$cliente->paterno.' '.$cliente->materno; ?>
            </span>
          </div>
          <div class="card-options">
            <div id="loader_data" class="spinner-v1-mini ml-1 d-none"></div>
            <!-- <small class="font-weight-bold text-indigo">Detalle de cliente</small> -->
          </div>
        </div>
        <div class="card-body">
          <h5 class="mb-3">Datos Generales</h5>
          <div class="row">
            <div class="col-xs-12 col-lg-4 form-group">
              <div class="form-label">Documento</div>
              <span id="dg-documento"><?= $cliente->documento; ?></span>
            </div>
            <div class="col-xs-12 col-lg-4 form-group">
              <div class="form-label">Tipo de Documento</div>
              <span id="dg-tipodoc"><?= $cliente->tipo_documento; ?></span>
            </div>
            <div class="col-xs-12 col-lg-4 form-group">
              <div class="form-label">Distrito</div>
              <span id="dg-distrito"><?= $cliente->ciudad; ?></span>
            </div>            
            <div class="col-xs-12 col-lg-4 form-group">
              <div class="form-label">Telefono</div>
              <span id="dg-telefono"><?= $cliente->telefono; ?></span>
            </div>            
            <div class="col-xs-12 col-lg-8 form-group">
              <div class="form-label">Direccion</div>
              <span id="dg-direccion"><?= $cliente->direccion; ?></span>
            </div>
            <div class="col-xs-12 col-lg-4 form-group">
              <div class="form-label">Email</div>
              <span id="dg-email"><?= $cliente->email; ?></span>
            </div>
          </div>
        </div>
      </div>
      <div class="card mb-2 border-fx-1">
        <div class="card-header px-3" style="border-bottom:none;">
          <div class="card-title text-primary font-weight-bold">
            <i class="fe fe-clipboard"></i> Notas 
            <div id="loader_nota" class="spinner-v1-mini ml-1 d-none"></div>
          </div>
          <div class="card-options">
            <button id="btn-nota-nuevo" class="enc2454-btn-option enc2454-btn-option-blue" data-idcliente="<?= $cliente->id; ?>">
              <i class="fe fe-plus"></i>
              Nueva Nota 
            </button>
          </div>
        </div>
        <div class="p-0" style="max-height: 300px; overflow-y: auto;">
          <table class="card-table table table-leverage-space text-nowrap">
            <thead>
              <th>Anclar</th>
              <th>Descripcion</th>
              <th>Empleado</th>
              <th>Fecha Creacion</th>
              <th class="text-center w-10">Accion</th>
            </thead>
            <tbody id="_table_notas">
              <tr class="text-center">
                <td colspan="5">Sin resultados</td>
              </tr>
            </tbody>
          </table>
        </div> 
      </div>   
        
      <div class="card mb-2 border-fx-1">
        <div class="card-header px-3" style="border-bottom:none;">
          <div class="card-title text-primary font-weight-bold">
            <i class="fe fe-monitor"></i> Prendas registradas 
            <div id="loader_prenda" class="spinner-v1-mini ml-1 d-none"></div>
          </div>
          <div class="card-options">
            <button id="btn-prenda-nuevo" class="enc2454-btn-option enc2454-btn-option-blue" data-idcliente="<?= $cliente->id; ?>">
              <i class="fe fe-plus"></i>
              Nueva Prenda 
            </button>
          </div>
        </div>
        <div class="p-0" style="max-height: 320px; overflow-y: auto;">
          <table class="card-table table table-leverage-space table-capitalize text-nowrap">
            <thead>
              <th>Descripcion</th>
              <th>Categoria</th>
              <th>Valoracion</th>
              <th>Fecha Creacion</th>
              <th>Estado</th>
              <th class="text-center w-10">Accion</th>
            </thead>
            <tbody id="_table_prendas">
              <tr class="text-center">
                <td colspan="6">Datos no encontrados</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
      <div class="card mb-1 border-fx-1">
        <div class="card-header px-3" style="border-bottom:none;">
          <div class="card-title text-primary font-weight-bold">
            <i class="fe fe-file"></i> Creditos relacionados 
            <div id="loader_credito" class="spinner-v1-mini ml-1 d-none"></div>
          </div>
          <div class="card-options">
            <a href="<?= base_url() ?>Credito/nuevo?cliente=<?= $cliente->id; ?>" class="enc2454-btn-option enc2454-btn-option-blue" data-idcliente="<?= $cliente->id; ?>">
              Nuevo Credito 
              <i class="fe fe-arrow-right"></i>
            </a>
          </div>
        </div>
        <div class="p-0" style="max-height: 300px; overflow-y: auto;">
          <table class="card-table table table-leverage-space table-capitalize text-nowrap">
            <thead class="thead-dasrk">
              <th>Codigo</th>
              <th>Creacion</th>
              <th>Monto</th>
              <th>Estado</th>
            </thead>
            <tbody id="_table_creditos">
              <tr class="text-center">
                <td colspan="4">Datos no encontrados</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div> 

  </div>
</div>


<!-- Modals -->
<div id="modal-conducta" class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h4 id="modal-nota-title" class="modal-title text-blue d-flex align-items-center">
          <i class="fe fe-star text-blue mr-1"></i>
          Editar Conducta 
          <i id="loader_modal_conducta" class="ml-1 spinner-v1-mini d-none"></i>
        </h4>
        <div class="card-options">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true"></span>
          </button>
        </div>
      </div>
      <div id="modal_conducta_rendalert"></div>
      <form id="form-conducta" autocomplete="off" autocorrect="off" data-mode="new">
        <input id="base_puntuacion" type="hidden">
        <input id="base_bloqueo_atencion" type="hidden">
        <input id="base_bloqueo_motivo" type="hidden">
        <div class="modal-body">
          <div class="row">
            <div class="col-md-12">
              <!-- <div class="d-flex justify-center">
                <a id="btn_puntuacion_reset" href="javascript:void(0)" class="btn-link">Reset</a>
              </div> -->
              <div class="enc1212-califica-subtitle">Calificacion</div>
              <div class="enc1212-califica-list enc1212-califica-item-A">
                <div class="enc1212-califica-item enc1212-tag-center">
                  <input id="cal_a" type="radio" name="puntuacion" value="A">
                  <label for="cal_a">A</label>
                  <div class="d-block">
                    <small>Buen Cliente</small>
                  </div>
                </div>
                <div class="enc1212-califica-item enc1212-califica-item-B enc1212-tag-center">
                  <input id="cal_b" type="radio" name="puntuacion" value="N">
                  <label for="cal_b">N</label>
                  <div class="d-block">
                    <small>Cliente Normal</small>
                  </div>
                </div>
                <div class="enc1212-califica-item enc1212-califica-item-C enc1212-tag-center">
                  <input id="cal_c" type="radio" name="puntuacion" value="C">
                  <label for="cal_c">C</label>
                  <div class="d-block">
                    <small>Mal Cliente</small>
                  </div>
                </div>
                <!-- <div class="enc1212-califica-item">
                  <input id="cal_" type="radio" name="puntuacion" value="-">
                  <label for="cal_">-</label>
                </div> -->
              </div>
              

            </div>
          </div>
          <div class="modal-body px-0">
            <div class="row">
              <div class="col-md-12">
                <div class="form-group">
                  <div class="form-label">Lista Negra</div>
                  <label class="custom-switch">
                    <input type="checkbox" name="bloqueo_atencion" class="custom-switch-input" value="SI">
                    <span class="custom-switch-indicator"></span>
                    <span class="custom-switch-description">Aplicar</span>
                  </label>
                </div>
              </div>
              <div class="col-md-12">
                <div class="form-group">
                  <label for="" class="form-label">Motivo Lista Negra</label>
                  <textarea id="input_bloqueo_motivo" name="bloqueo_motivo" cols="7" rows="5" class="form-control border-fx-1" required disabled></textarea>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer text-right">
          <button class="btn btn-secondary" data-dismiss="modal">
            Cancelar
          </button>
          <button id="btn_conducta_save" name="btnsubmit" type="submit" class="btn btn-blue">
            <i class="fe fe-check-circle"></i> 
            Guardar
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<div id="modal-nota" class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h4 id="modal-nota-title" class="modal-title text-blue">
          <i class="fe fe-file-plus text-blue"></i>
          Nueva Nota
        </h4>
        <div class="card-options">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true"></span>
          </button>
        </div>
      </div>
      <div id="modal_nota_rendalert"></div>
      <form id="form-nota" autocomplete="off" autocorrect="off" data-mode="new">
        <input name="idnota" type="hidden">
        <div class="modal-body">
          <div class="row">
            <div class="col-xs-12 col-md-12">
              <div class="form-group">
                <label for="" class="font-weight-bold">Contenido de la nota</label>
                <textarea name="descripcion" cols="7" rows="5" class="form-control border-fx-1" required></textarea>
              </div>
            </div>
            <div class="col-xs-12 col-md-12">
              <div class="form-group">
                <label class="form-label">Tipo de Nota</label>
                <div class="selectgroup">
                  <label class="selectgroup-item">
                    <input type="radio" name="fixed" value="0" class="selectgroup-input" checked="">
                    <span class="selectgroup-button">NORMAL</span>
                  </label>
                  <label class="selectgroup-item">
                    <input type="radio" name="fixed" value="1" class="selectgroup-input">
                    <span class="selectgroup-button">ANCLADO</span>
                  </label>
                </div>
              </div>

            </div>
          </div>
        </div>
        <div class="modal-footer text-right">
          <button class="btn btn-secondary" data-dismiss="modal">
            Cancelar
          </button>
          <button id="btn_nota_save" type="submit" class="btn btn-blue">
            <i class="fe fe-check-circle"></i> 
            Guardar
          </button>
        </div>
      </form>
    </div>
  </div>
</div>

<div id="modal-prenda" class="modal modal-blur fade" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
    <form id="form-prenda" class="modal-content" autocomplete="off" data-mode="">
      <input type="hidden" id="idprenda">
      <div class="modal-header">
          <h5 id="modal-prenda-title" class="modal-title">
              <i class="fe fe-edit text-blue"></i> 
              Nueva Prenda
          </h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>
      </div>
      <div id="modal_prenda_rendalert" class="p-1"></div>
      <div class="modal-body">              
        <div class="row">
          <div class="col-md-12 form-group">
            <div class="form-label">Tipo de Prenda</div>
            <div id="tprenda_render_area" class="custom-controls-stacked py-2">
              <div class="text-center">Sin Datos</div>                            
            </div>
            <span id="nprenda-feed-tprenda" class="feedtag feedtag-danger"></span>
          </div>
        </div>
        <div class="row">
          <div class="col-md-6 col-xs-12 form-group">
            <div class="form-label">Descripcion</div>
            <input type="text" class="form-control text-uppercase border-fx-1" name="descripcion" maxlength="50" required>
            <span id="nprenda-feed-descripcion" class="feedtag feedtag-danger"></span>
          </div>
          <div class="col-md-6 col-xs-12 form-group">
            <div class="form-label">
                Categoria <div class="" id="categoria-loader"></div>
            </div>
            <select id="categoria_list" name="categoria" class="form-control border-fx-1" required>
            <option value="">-SELECCIONAR-</option>
            </select>
            <span id="nprenda-feed-categoria" class="feedtag feedtag-danger"></span>
          </div>
        </div>
        <div id="prenda_form_content"></div>
      </div>
      <div class="modal-body text-right">
          <button id="btn_prenda_save" class="btn btn-indigo btn-lg">
            <i class="fe fe-check-circle"></i>  
            Guardar
          </button>
      </div>
    </form>
  </div>
</div>

<div id="modal-cliente" class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <form id="form-cliente"  autocomplete="off" autocorrect="off" class="modal-content">
      <div class="modal-header">
        <h4 id="modal-nota-title" class="modal-title text-blue">
          <i class="fe fe-edit-2 text-blue"></i>
          Editar Datos
        </h4>
        <div class="card-options">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true"></span>
          </button>
        </div>
      </div>
      <div id="modal_cliente_rendalert"></div>
      <div class="modal-body">
        <div class="row">
          <div class="col-md-12 mb-3">
            <label class="form-label">Nombres</label>
            <input type="text" class="form-control text-uppercase border-fx-1" name="nombre" placeholder="" required>
          </div>
          <div class="col-md-6 col-xs-12 mb-3">
            <label class="form-label">Ape. Paterno</label>
            <input type="text" class="form-control text-uppercase border-fx-1" name="paterno" placeholder="" required>
          </div>
          <div class="col-md-6 col-xs-12 mb-3">
            <label class="form-label">Ape. Materno</label>
            <input type="text" class="form-control text-uppercase border-fx-1" name="materno" placeholder="" required>
          </div>
          <div class="col-md-6 col-xs-12 mb-3">
            <label class="form-label">Documento</label>
            <input type="number" class="form-control text-uppercase border-fx-1" name="documento" placeholder="" required>
          </div>
          <div class="col-md-6 col-xs-12 mb-3">
            <label class="form-label">Tipo Documento</label>
            <select name="tipo_documento"  class="form-control text-uppercase">
              <option value="DNI">DNI</option>
              <option value="CARNET EXT.">CARNET EXT.</option>
              <option value="PASAPORTE">PASAPORTE</option>
            </select>
          </div>
          <div class="col-md-6 col-xs-12 mb-3">
            <label class="form-label">Distrito</label>
            <div class="tooltip-form-group">
              <input id="input_distrito" type="text" class="form-control text-uppercase border-fx-1" name="distrito" data-ubigeo="0" placeholder="" required>
              <input id="input_ubigeo" type="hidden" name="ubigeo">
              <ul id="distrito-search-list" class="tooltip-form-list">
                <!-- <li class="tooltip-item" name="distrito-item">
                  <div class="tooltip-item-title">Casma</div> 
                  <div class="tooltip-item-subtitle">Ancash</div>    
                </li> -->
              </ul>
            </div>
          </div>
          <div class="col-md-6 col-xs-12 mb-3">
            <label class="form-label">Telefono</label>
            <input type="text" class="form-control text-uppercase border-fx-1" name="telefono" placeholder="" required>
          </div>
          <div class="col-md-12 mb-3">
            <label class="form-label">Direccion</label>
            <input type="text" class="form-control text-uppercase border-fx-1" name="direccion" placeholder="" required>
          </div>
          <div class="col-md-12 mb-3">
            <label class="form-label">Email</label>
            <input type="text" class="form-control border-fx-1" name="email" placeholder="">
          </div>
        </div>  

      </div>
      <div class="modal-footer text-right">
        <button class="btn btn-secondary" data-dismiss="modal">
          Cancelar
        </button>
        <button id="btn_cliente_save" type="submit" class="btn btn-blue">
          <i class="fe fe-check-circle"></i>
          Guardar
        </button>
      </div>
    </form>
  </div>
</div>

<script src="<?= base_url() ?>js_modules/cliente/cliente.detalle.js?v=23012025"></script>