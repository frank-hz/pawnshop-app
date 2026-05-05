<?php ini_set('display_errors', 0); ?>

<input type="hidden" id="idbase" value="<?= $prenda->id; ?>">
<div class="container">
  <div class="row">
    <div class="col-xs-12 col-lg-3">
      <div class="card mt-2 border-rock">
        <div class="card-header">
          <h3 class="card-title text-blue form-label">Opciones</h3>
        </div>
        <div class="list-group" style="background: #e0e1e1;">
          <a id="btn_open_edit" href="javascript:void(0)" class="list-group-item list-group-item-action text-blue py-2 font-weight-bold" 
          data-cliente="<?= $prenda->cliente_id; ?>">
            <span class="icon mr-3"><i class="fe fe-edit-2 text-blue"></i></span>
            Editar
          </a>
          <a id="btn_delete" href="javascript:void(0)" class="list-group-item list-group-item-action text-red font-weight-bold" data-cliente="<?= $prenda->cliente_id; ?>">
            <span class="icon mr-3"><i class="fe fe-trash text-red"></i></span>
            Eliminar
          </a>
          <?php if(in_array($prenda->estado, ["D","R"])): ?>
          <a href="<?= base_url() ?>Prenda/Transferir/<?= $prenda->id; ?>" class="list-group-item list-group-item-action text-blue py-2 font-weight-bold" data-cliente="<?= $prenda->cliente_id; ?>" >
            <span class="icon mr-3"><i class="fas fa-exchange-alt text-blue"></i></span>
            Transferir
          </a>
          <?php endif; ?>
        </div>
      </div>
    </div>
    <div class="col-xs-12 col-lg-9">
      <div class="card border-fx-1 my-2">
        <div class="card-header">
					<div class="card-title font-weight-bold text-indigo">
						<i class="fe fe-layers text-indigo font-weight-bold"></i>
            <span id="d_descripcion">
              <?php echo "{$prenda->descripcion}"; ?> 
            </span>
          </div>
          <div class="card-options">
            <div id="d_estado_tag" class="px-1"><?= $prenda->estado_tag; ?></div>
            <small class="font-weight-bold text-indigo">
              Detalle de Prenda
            </small>
            <div id="loader_data" class="spinner-v1-mini ml-1 d-none"></div>
          </div>
				</div>
        <div id="data_general_content" class="card-body">          
          <?php echo $prenda_data_string; ?>
        </div>
      </div>
      <div class="card border-fx-1 my-2">
        <div class="card-header" style="border-bottom:none;">
					<h5 class="card-title text-indigo">
            <i class="fe fe-file"></i>
            Creditos Asociados
          </h5>
        </div>
        <div class="p-0" style="max-height: 300px; overflow-y: auto;">
          <table class="card-table table">
            <thead>
              <th>Serie</th>
              <th>Fecha</th>
              <th>Monto Prestado</th>
              <th>Estado</th>
              <th></th>
            </thead>
            <tbody>
            <?php if (!empty($creditos)): ?>
              <?php foreach ($creditos as $credito): ?>
                <tr>
                  <td>
                    <a href="<?= $credito->url; ?>" class="btn-link">
                      <?= $credito->serie; ?>
                    </a>
                  </td>
                  <td><?php echo $credito->creacion; ?></td>
                  <td>S/ <?php echo $credito->monto; ?></td>
                  <td><?php echo $credito->estado_tag; ?></td>
                  <td>
                    <div class="d-flex justify-center">
                      <a href="<?= $credito->url; ?>" class="btn btn-outline btn-outline-primary btn-sm">
                        <i class="fe fe-layers"></i>
                        Ver Detalle
                      </a>
                    </div>
                  </td>
                </tr>
              <?php endforeach ?>
            <?php else: ?>
              <tr>
                  <td colspan="5" class="text-center">Datos no encontrados</td>
              </tr>
            <?php endif ?>
            </tbody>
          </table>
        </div>
      </div>
      <div class="card border-fx-1 my-2">
        <div class="card-header" style="border-bottom:none;">
					<h5 class="card-title text-indigo">
            <i class="fe fe-anchor"></i>
            Propietarios
          </h5>
        </div>
        <div class="p-0" style="max-height: 300px; overflow-y: auto;">
          <table class="card-table table">
            <thead>
              <th>Cliente</th>
              <th>Fecha Incicio</th>
              <th>Fecha Termino</th>
            </thead>
            <tbody>
            <?php if (!empty($propiedades)): ?>
              <?php foreach ($propiedades as $propiedad): ?>
                <tr>
                  <td>
                    <a href="<?= base_url() ?>cliente/detalle/<?= $propiedad->cliente_id; ?>">
                      <?php echo $propiedad->cliente_nombre_full; ?>
                    </a>
                  </td>
                  <td><?php echo $propiedad->inicio; ?></td>
                  <td><?php echo $propiedad->termino; ?></td>
                </tr>
              <?php endforeach ?>
            <?php else: ?>
              <tr>
                  <td colspan="3" class="text-center">Datos no encontrados</td>
              </tr>
            <?php endif ?>
            </tbody>
          </table>
        </div>
      </div>

    </div>
  </div>
</div>


<div id="modal-prenda" class="modal modal-blur fade" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
    <form id="form-prenda" class="modal-content" autocomplete="off">
      <div class="modal-header">
          <h5 class="modal-title">
              <i class="fe fe-edit text-blue"></i>
              Editar Datos 
          </h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>
      </div>
      <div id="modal_prenda_rendalert"></div>
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


<script src="<?= base_url() ?>js_modules/prenda/prenda.detalle.js?v=1"></script>