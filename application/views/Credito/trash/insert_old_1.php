<!-- STL CODE: enc2454 -->
<?php ini_set('display_errors', 0); ?>
<?php if(!empty($cliente)): ?>
    <input type="hidden" id="idcliente_base" value="<?= $cliente->id; ?>">
<?php endif;?> 

<!-- <button id="btn_test" class="btn btn-lg btn-dark m-2">Test</button> -->
<div class="container">
    <div class="enc2454-content">
        <div class="enc2454-section-a w-100">
            <div class="modulo">
                <div class="modulo-header">
                    <div class="modulo-title">
                        <svg xmlns="http://www.w3.org/2000/svg" class="modulo-title-icon" viewBox="0 0 512 512"><title>Add Circle</title><path d="M448 256c0-106-86-192-192-192S64 150 64 256s86 192 192 192 192-86 192-192z" fill="none" stroke="currentColor" stroke-miterlimit="10" stroke-width="32"/><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="32" d="M256 176v160M336 256H176"/></svg>
                        Nuevo Credito
                    </div>
                    <div class="modulo-options"></div>
                </div>
                <div id="modulo-preview" class="modulo-preview">
                </div>
                <div id="wiz-cliente" class="modulo-body enc2454-trans-1 d-none">
                    <div class="enc2454-header">
                        <div class="enc2454-subtitle">1. Seleccionar Cliente</div>
                        <button id="btn_cliente_nuevo" class="enc2454-btn-option" data-bs-toggle="modal" data-bs-target="#modal_cliente">Nuevo Cliente</button>
                    </div>                
                    <div class="enc2454-search-form mt-1">
                        <label for="" class="enc2454-form-label">Buscar:</label>
                        <div class="enc2454-search-input">                        
                            <input id="input-cliente-search" type="text" spellcheck="false" placeholder="Buscar por nombres, apellidos o documento" autocomplete="off">
                            <i class="fe fe-search"></i>
                        </div>
                    </div>
                    <div id="cliente_dataset" class="enc2454-table">
                        <div class="enc2454-dataset-blank">
                            [ Sin Datos ] 
                        </div>
                    </div>
                    
            
                </div>        
                <div id="wiz-prenda" class="modulo-body enc2454-trans-1 d-none">
                    <div class="enc2454-header">
                        <div class="enc2454-subtitle">2. Seleccionar Prenda</div>
                        <button id="btn_prenda_nuevo" class="enc2454-btn-option">Nueva Prenda</button>
                    </div>
                    <div id="prenda_dataset" class="enc2454-checklist">
                        
                        <!-- <div class="enc2454-checklist-item">
                            <input id="prenda_3" type="radio" name="prenda-item">
                            <label for="prenda_3">
                                <span class="enc2454-checklist-icon"></span>
                                <div class="enc2454-checklist-data">
                                    <div class="enc2454-checklist-title">
                                        LAPTOP HP I5 10MA
                                    </div>
                                    <div class="enc2454-checklist-subtitle">
                                        MODELO: I5-DW106OLA,
                                        SERIE: 	SNCID11F22D
                                    </div>
                                </div>
                            </label>
                        </div>-->

                    </div>
                </div>
                <div id="wiz-prestamo" class="modulo-body enc2454-trans-1 d-none">
                    <div class="enc2454-header">
                        <div class="enc2454-subtitle">3. Definir Prestamo</div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 col-xs-12">
                            <div class="form-group">
                                <div class="form-label">Monto a Prestar</div>
                                <input id="dp_monto" type="text" step="any" class="form-control form-control-lg border-fx-1 text-center" autocomplete="off">
                                <span id="monto_feed" class="text-danger"></span>
                            </div>
                        </div>
                        <div class="col-md-4 col-xs-12">
                            <div class="form-group">
                                <div class="form-label">Prorroga (dias)</div>
                                <input id="dp_prorroga" type="number" step="1" class="form-control form-control-lg border-fx-1 text-center" value="35">
                                <span id="prorroga_feed" class="text-danger"></span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 col-xs-12">
                            <div class="form-group">
                                <div class="form-label">Cargo por Almacenamiento</div>
                                <!-- <label class="custom-switch py-2">
                                    <input type="checkbox" id="dp_cargo_almacena" class="custom-switch-input">
                                    <span class="custom-switch-indicator"></span>
                                    <span class="custom-switch-description">
                                        Aplicar
                                    </span>
                                </label> -->
                                <div class="custom-switches-stacked">
                                    <label class="custom-switch">
                                        <input type="radio" name="cargo-item" value="A" class="custom-switch-input">
                                        <span class="custom-switch-indicator"></span>
                                        <span class="custom-switch-description">Activo</span>
                                    </label>
                                    <label class="custom-switch">
                                        <input type="radio" name="cargo-item" value="I" class="custom-switch-input" checked="">
                                        <span class="custom-switch-indicator"></span>
                                        <span class="custom-switch-description">Inactivo</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 col-xs-12">
                            <div class="form-group">
                                <div class="form-label">Ciclo de Pago</div>
                                <!-- <label class="custom-switch py-2">
                                    <input type="checkbox" id="dp_ciclo_pago" class="custom-switch-input">
                                    <span class="custom-switch-indicator"></span>
                                    <span class="custom-switch-description">
                                        Aplicar
                                    </span>
                                </label> -->
                                <div class="custom-switches-stacked">
                                    <label class="custom-switch">
                                        <input type="radio" name="ciclo-item" value="7" class="custom-switch-input" checked="">
                                        <span class="custom-switch-indicator"></span>
                                        <span class="custom-switch-description">SEMANAL</span>
                                    </label>
                                    <label class="custom-switch">
                                        <input type="radio" name="ciclo-item" value="30" class="custom-switch-input">
                                        <span class="custom-switch-indicator"></span>
                                        <span class="custom-switch-description">MENSUAL</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                </div>
                <div id="wiz-complete" class="modulo-body enc2454-trans-1 d-none">
                    <div class="enc2454-subtitle">4. Finalizar Registro</div>
                    <!-- <p class="text-center">Se han completado todos los datos.</p> -->
                    <div id="minipreview" class="rs-list"></div>
                    <a id="link__edit_adv" href="javascript:void(0)" class="btn-link font-weight-bold d-nones">
                        <i class="fe fe-edit"></i>
                        Edicion Avanzada de Credito 
                    </a>
                </div>
                <div class="modulo-footer p-3">
                    <div class="d-flexs">
                        <button id="wiz-btn-prev" data-value="-1" class="btn btn-indigo btn-lg d-none">
                            <i class="fe fe-chevron-left"></i>
                            Previo
                        </button>
                        <button id="wiz-btn-next" class="btn btn-indigo btn-lg d-none">
                            Siguiente 
                            <i class="fe fe-chevron-right"></i>
                        </button>
                        <button id="wiz-btn-save" class="btn btn-sodagreen btn-lg d-none">
                            <i class="fe fe-save"></i>
                            Guardar
                        </button>
                    </div>
                </div>
                <div id="modulo-loader" class="enc2454-modulo-loader">
                    <div class="enc2454-modulo-loader-message">
                        <div class="spinner-v1"></div> Cargando
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>



<div id="modal_cliente" class="modal modal-blur fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <form id="form_cliente" class="modal-content" autocomplete="off">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fe fe-plus text-blue"></i>
                    Nuevo Cliente <div id="modal_cliente_loader" class="spinner-v1-mini d-none"></div>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>
            </div>
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
                            <input id="input_ubigeo" type="hidden" name="ubigeo">
                            <input id="input_distrito" type="text" class="form-control text-uppercase border-fx-1" name="distrito" data-ubigeo="0" placeholder="" required>
                            <ul id="distrito-search-list" class="tooltip-form-list">
                                <!-- <li class="tooltip-item" name="distrito-item">
                                    <div class="tooltip-item-title">Casma</div> 
                                    <div class="tooltip-item-subtitle">Ancash</div>    
                                </li>
                                <li class="tooltip-item" name="distrito-item">
                                    <div class="tooltip-item-title">Comas</div> 
                                    <div class="tooltip-item-subtitle">Lima</div>    
                                </li>
                                <li class="tooltip-item" name="distrito-item">
                                    <div class="tooltip-item-title">Huaraz</div> 
                                    <div class="tooltip-item-subtitle">Ancash</div>    
                                </li> -->
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-6 col-xs-12 mb-3">
                        <label class="form-label">Telefono</label>
                        <input type="number" class="form-control text-uppercase border-fx-1" name="telefono" placeholder="" required>
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
            <div class="modal-body text-right">
                <button id="btn_cliente_save" class="btn btn-indigo btn-lg">
                    Guardar
                    <i class="fe fe-check-circle"></i>
                </button>
            </div>
        </form>
    </div>

</div>

<div id="modal_prenda" class="modal modal-blur fade" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
    <form id="form_prenda" class="modal-content" autocomplete="off">
      <input type="hidden" id="idprenda">
      <div class="modal-header">
          <h5 class="modal-title">
              <i class="fe fe-edit text-blue"></i>
              Editar Datos 
          </h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">              
        <div class="row">
          <div class="col-md-12 form-group">
            <div class="form-label">Tipo de Prenda</div>
            <div id="tprenda-render-area" class="custom-controls-stacked py-2">
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
            <select id="categoria-list" name="categoria" class="form-control border-fx-1" required>
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


<!-- Css Adds -->
<style>
	.rs-list {
		list-style: none;
		padding: 0;
		margin: 0;
	}
	.rs-list li {
		padding: .8em .7em;
		display: block;
		border-left: 3px solid #3E54D3;
		margin: 4px 0;
	}
	.rs-list li p {
		margin: 0;
		margin-top: 2px;
		margin-bottom: 4px;
	}
	.rs-title {
		color: #3E54D3;
		font-size: .9em;
		font-weight: 600;
		letter-spacing: .4px;
		text-transform: uppercase;
		margin: 0 3px 0 1px;
	}
	.rs-table {
		width: 100%;
		border-collapse: collapse;
	}
	.rs-table td,.rs-table th {
		padding: 6px 8px;
		border: 1px solid #e9ecef;
	}
</style>

<script src="<?= base_url() ?>js_modules/credito/credito.insert_old_1.js?v=5"></script>