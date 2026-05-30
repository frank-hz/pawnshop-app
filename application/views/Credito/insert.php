<?php ini_set('display_errors', 0); ?>
<link rel="stylesheet" href="<?= base_url('assets/css/views/credito.create.css') ?>">
<input type="hidden" id="idcliente_base" value="<?= $cliente->id; ?>">
<div class="container">
    <div id="modal_view_rendalert" class="p-2">
        
    </div>
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
                        <button id="btn_cliente_nuevo" class="enc2454-btn-option">
                            Nuevo Cliente
                        </button>
                    </div>                
                    <div class="enc2454-search-form mt-1">
                        <label for="" class="enc2454-form-label">Buscar:</label>
                        <div class="enc2454-search-input">                        
                            <input id="input-cliente-search" type="text" spellcheck="false" placeholder="Buscar por nombres o apellidos" autocomplete="off">
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
                    <!-- <div class="text-right px-1 py-3">
                        <small class="text-muted">Tiempo: <span id="prenda_timer">00:00 s</span></small>
                    </div> -->
                    <div id="prenda_dataset" class="prendas-gallery">
                        <!-- <div class="prenda-item">
                            <input id="prenda_1" type="radio" name="prenda-item">
                            <label for="prenda_1">
                                <div class="pheader">
                                    <h4 class="ptitle">LAPTOP GAMER ACER CORE I5</h4>
                                    <span class="tagger tagger-prenda-D">Disponible</span>
                                </div>
                                <div class="pcontent">
                                    Categoria: LAPTOP | Valoracion: 6 | Serie: APPLE | Marca: C1MLC6PXDTY3 | Modelo: A1278
                                </div>
                            </label>
                        </div> -->
                        <!-- <div class="prenda-item">
                            <input id="NG" type="radio" name="prenda-item" value="0" data-type="NO_GARANTIA">
                            <label for="NG" data-valoracion="" data-descripcion="" class="no-garantia-item text-primary">
                                Sin Garantia 
                                <i class="fe fe-maximize"></i>
                            </label>
                        </div> -->
                        
                    </div>

                    <!-- <div id="section_prenda" class="prenda-content">
                        <tr class="skeleton-row">
                            <div class="prenda-skeleton" style="height:20px; width:80%"></div>
                            <div class="prenda-skeleton" style="height:20px; width:80%"></div>
                            <div class="prenda-skeleton" style="height:20px; width:80%"></div>
                            <div class="prenda-skeleton" style="height:20px; width:80%"></div>
                        </tr>
                    </div> -->
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
                                <input id="dp_prorroga" type="number" step="1" class="form-control form-control-lg border-fx-1 text-center" value="30">
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
                                        <input type="radio" name="ciclo-item" value="SEMANAL" class="custom-switch-input" checked="">
                                        <span class="custom-switch-indicator"></span>
                                        <span class="custom-switch-description">SEMANAL</span>
                                    </label>
                                    <label class="custom-switch">
                                        <input type="radio" name="ciclo-item" value="MENSUAL" class="custom-switch-input">
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

<!-- <div id="modal_nota_rendalert"></div> -->

<!-- Modals -->
<div id="modal_cliente" class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <form id="form_cliente" autocomplete="off" autocorrect="off" class="modal-content">
            <div class="modal-header">
                <h4 id="modal-nota-title" class="modal-title text-blue">
                    <i class="fe fe-plus-circle text-blue"></i>
                    Nuevo Cliente <div id="modal_cliente_loader" class="spinner-v1-mini d-none"></div>
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
                        <span id="feed_nombre" class="feed-red"></span>
                    </div>
                    <div class="col-md-6 col-xs-12 mb-3">
                        <label class="form-label">Ape. Paterno</label>
                        <input type="text" class="form-control text-uppercase border-fx-1" name="paterno" placeholder="" required>
                        <span id="feed_paterno" class="feed-red"></span>
                    </div>
                    <div class="col-md-6 col-xs-12 mb-3">
                        <label class="form-label">Ape. Materno</label>
                        <input type="text" class="form-control text-uppercase border-fx-1" name="materno" placeholder="" required>
                        <span id="feed_materno" class="feed-red"></span>
                    </div>
                    <div class="col-md-6 col-xs-12 mb-3">
                        <label class="form-label">Documento</label>
                        <input type="number" class="form-control text-uppercase border-fx-1" name="documento" placeholder="" required>
                        <span id="feed_documento" class="feed-red"></span>
                    </div>
                    <div class="col-md-6 col-xs-12 mb-3">
                        <label class="form-label">Tipo Documento</label>
                        <select name="tipo_documento"  class="form-control text-uppercase">
                            <option value="DNI">DNI</option>
                            <option value="CARNET EXT.">CARNET EXT.</option>
                            <option value="PASAPORTE">PASAPORTE</option>
                        </select>
                        <span id="feed_tipodoc" class="feed-red"></span>
                    </div>
                    <div class="col-md-6 col-xs-12 mb-3">
                        <label class="form-label">Distrito</label>
                        <div class="tooltip-form-group">
                            <input id="input_distrito" type="text" class="form-control text-uppercase border-fx-1" name="distrito" data-ubigeo="0" placeholder="" >
                            <input id="input_ubigeo" type="hidden" name="ubigeo">
                            <ul id="distrito-search-list" class="tooltip-form-list">
                            <!-- <li class="tooltip-item" name="distrito-item">
                                <div class="tooltip-item-title">Casma</div> 
                                <div class="tooltip-item-subtitle">Ancash</div>    
                            </li> -->
                            </ul>
                        </div>
                        <span id="feed_ubigeo" class="feed-red"></span>
                    </div>
                    <div class="col-md-6 col-xs-12 mb-3">
                        <label class="form-label">Telefono</label>
                        <input type="text" class="form-control text-uppercase border-fx-1" name="telefono" placeholder="" required>
                        <span id="feed_telefono" class="feed-red"></span>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Direccion</label>
                        <input type="text" class="form-control text-uppercase border-fx-1" name="direccion" placeholder="" required>
                        <span id="feed_direccion" class="feed-red"></span>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label class="form-label">Email</label>
                        <input type="text" class="form-control border-fx-1" name="email" placeholder="">
                        <span id="feed_email" class="feed-red"></span>
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


<div id="modal_prenda" class="modal modal-blur fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <form id="form_prenda" class="modal-content" autocomplete="off">
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
                    <input type="text" class="form-control text-uppercase border-fx-1" name="descripcion" maxlength="50" >
                    <span id="nprenda-feed-descripcion" class="feedtag feedtag-danger"></span>
                    </div>
                    <div class="col-md-6 col-xs-12 form-group">
                    <div class="form-label">
                        Categoria <div class="" id="categoria_loader"></div>
                    </div>
                    <select id="categoria_list" name="categoria" class="form-control border-fx-1" >
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

<div id="modal_testing" class="modal modal-blur fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Testing</h5>
            </div>
            <div class="modal-body">
                <pre id="testing_area">

                </pre>
            </div>
        </div>
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

<script src="<?= base_url() ?>js_modules/credito/credito.insert.js?v=3435"></script>
