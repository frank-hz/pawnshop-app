<!-- STL CODE: enc2454 -->

<div class="enc2454-content">
    <div class="enc2454-section-a">
        <div class="modulo">
            <div class="modulo-header">
                <div class="modulo-title">
                    <svg xmlns="http://www.w3.org/2000/svg" class="modulo-title-icon" viewBox="0 0 512 512"><title>Add Circle</title><path d="M448 256c0-106-86-192-192-192S64 150 64 256s86 192 192 192 192-86 192-192z" fill="none" stroke="currentColor" stroke-miterlimit="10" stroke-width="32"/><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="32" d="M256 176v160M336 256H176"/></svg>
                    Nuevo Credito
                </div>
                <div class="modulo-options">
                    <button class="enc2454-btn-preview" title="Previsualizar">
                        <svg xmlns="http://www.w3.org/2000/svg" class="ionicon" viewBox="0 0 512 512"><title>Document</title><path d="M416 221.25V416a48 48 0 01-48 48H144a48 48 0 01-48-48V96a48 48 0 0148-48h98.75a32 32 0 0122.62 9.37l141.26 141.26a32 32 0 019.37 22.62z" fill="none" stroke="currentColor" stroke-linejoin="round" stroke-width="32"/><path d="M256 56v120a32 32 0 0032 32h120" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="32"/></svg>
                    </button>
                </div>
            </div>
            <div id="wiz-cliente" class="modulo-body enc2454-trans-1 d-none">
                <div class="enc2454-header">
                    <div class="enc2454-subtitle">1. Seleccionar Cliente</div>
                    <button id="btn-cliente-nuevo" class="enc2454-btn-option" data-bs-toggle="modal" data-bs-target="#modal-cliente-nuevo">Nuevo Cliente</button>
                </div>                
                <div class="enc2454-search-form mt-1">
                    <label for="" class="enc2454-form-label">Buscar:</label>
                    <div class="enc2454-search-input">                        
                        <input id="input-cliente-search" type="text" spellcheck="false" placeholder="ingrese nombres,apellidos o dni" autocomplete="off">
                        <svg xmlns="http://www.w3.org/2000/svg" class="ionicon" viewBox="0 0 512 512"><title>Search</title><path d="M464 428L339.92 303.9a160.48 160.48 0 0030.72-94.58C370.64 120.37 298.27 48 209.32 48S48 120.37 48 209.32s72.37 161.32 161.32 161.32a160.48 160.48 0 0094.58-30.72L428 464zM209.32 319.69a110.38 110.38 0 11110.37-110.37 110.5 110.5 0 01-110.37 110.37z"/></svg>
                    </div>
                </div>
                <div id="cliente-dataset" class="enc2454-table">
                    <div class="enc2454-dataset-blank">
                        [ Sin Datos ]
                    </div>
                </div>
                
        
            </div>        
            <div id="wiz-prenda" class="modulo-body enc2454-trans-1 d-none">
                <div class="enc2454-header">
                    <div class="enc2454-subtitle">2. Seleccionar Prenda</div>
                    <button id="btn-prenda-nuevo" class="enc2454-btn-option">Nueva Prenda</button>
                </div>
                <div id="prenda-dataset" class="enc2454-checklist">
                    
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
                            <input id="dp_monto" type="number" step="any" class="form-control form-control-lg border-fx-1 text-center">
                            <span id="monto_feed" class="text-danger"></span>
                        </div>
                    </div>
                    <div class="col-md-4 col-xs-12">
                        <div class="form-group">
                            <div class="form-label">Prorroga</div>
                            <input id="dp_prorroga" type="number" step="1" class="form-control form-control-lg border-fx-1 text-center" value="7">
                            <span id="prorroga_feed" class="text-danger"></span>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4 col-xs-12">
                        <div class="form-group">
                            <div class="form-label">Cargo por Almacenamiento</div>
                            <label class="custom-switch py-2">
	                          	<input type="checkbox" id="dp_cargo_almacena" class="custom-switch-input">
	                          	<span class="custom-switch-indicator"></span>
	                          	<span class="custom-switch-description">
	                          		Aplicar
	                          	</span>
	                        </label>
                        </div>
                    </div>
                    <div class="col-md-4 col-xs-12">
                        <div class="form-group">
                            <div class="form-label">Cambiar a Pago Mensual</div>
                            <label class="custom-switch py-2">
	                          	<input type="checkbox" id="dp_ciclo_pago" class="custom-switch-input">
	                          	<span class="custom-switch-indicator"></span>
	                          	<span class="custom-switch-description">
	                          		Aplicar
	                          	</span>
	                        </label>
                        </div>
                    </div>
                </div>
                
            </div>
            <div id="wiz-complete" class="modulo-body enc2454-trans-1 d-none">
                <div class="enc2454-subtitle">4. Finalizar Registro</div>
                <p class="text-center">Se han completado todos los datos.</p>
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
    <div class="enc2454-section-b">
        <div class="enc2454-preview-card">
            <div class="enc2454-preview-header">Previsualizacion</div>
            <div class="enc2454-preview-subtitle">Cliente</div>
            <div class="enc2454-preview-row">
                <div class="enc2454-preview-label">
                    Nombres: 
                </div>
                <div id="pw1_cliente_nombre" class="enc2454-preview-text">
                    Sin Datos
                </div>
            </div>
            <div class="enc2454-preview-row">
                <div class="enc2454-preview-label">
                    Documento: 
                </div>
                <div id="pw1_cliente_doc" class="enc2454-preview-text">
                    000000000
                </div>
            </div>
            <div class="enc2454-preview-row">
                <div class="enc2454-preview-label">
                    Direccion: 
                </div>
                <div id="pw1_cliente_dir" class="enc2454-preview-text">
                    Sin Datos
                </div>
            </div>

            <div class="enc2454-preview-subtitle mt-3">Prenda</div>
            <div class="enc2454-preview-row">
                <div class="enc2454-preview-label">
                    Descripcion: 
                </div>
                <div id="pw1_prenda_descripcion" class="enc2454-preview-text">
                    Sin Datos
                </div>
            </div>
            <div class="enc2454-preview-row">
                <div class="enc2454-preview-label">Subdata</div>
                <div id="pw1_prenda_subdata" class="enc2454-preview-text">
                    Sin Datos
                </div>
            </div>

            <div class="enc2454-preview-subtitle mt-2">Prestamo</div>
            <div class="enc2454-preview-row">
                <div class="enc2454-preview-label">
                    Monto: 
                </div>
                <div id="pw1_monto" class="enc2454-preview-text enc2454-font-money">
                    S/ 0.00
                </div>
            </div>
            <div class="enc2454-preview-row">
                <div class="enc2454-preview-label">
                    Interes: 
                </div>
                <div id="pw1_interes" class="enc2454-preview-text">
                    0% [---]
                </div>
            </div>
            <div class="enc2454-preview-row">
                <div class="enc2454-preview-label">
                    Tipo: 
                </div>
                <div id="pw1_interes_tipo" class="enc2454-preview-text">
                    
                </div>
            </div>
            <div class="enc2454-preview-row">
                <div class="enc2454-preview-label">
                    Pago <span id="pw1_pago_tipo_label"></span>: 
                </div>
                <div id="pw1_cuota" class="enc2454-preview-text enc2454-font-money">
                    S/ 0.00
                </div>
            </div>
            <div class="enc2454-preview-row">
                <div class="enc2454-preview-label">
                    Mora: 
                </div>
                <div id="pw1_mora" class="enc2454-preview-text enc2454-font-money">
                    S/ 0.00
                </div>
            </div>
            <a id="link__edit_avanzado" href="javascript:void(0)" class="btn-link font-weight-bold">
                <i class="fe fe-edit"></i>
                Edicion Avanzada de Credito 
            </a>

        </div>

    </div>
</div>


<!-- Modals -->
<div class="modal modal-blur fade" id="modal-cliente-nuevo" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <form id="form-cliente-nuevo" class="modal-content" autocomplete="off">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fe fe-plus text-blue"></i>
                    Nuevo Cliente
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
                            <input id="input-cliente-ubigeo" type="text" class="form-control text-uppercase border-fx-1" name="distrito" data-ubigeo="0" placeholder="" required>
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
                <button class="btn btn-indigo btn-lg">
                    Guardar
                    <i class="fe fe-check-circle"></i>
                </button>
            </div>
        </form>
    </div>

</div>

<div class="modal modal-blur fade" id="modal-prenda-nuevo" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <form id="form-prenda-nuevo" class="modal-content" autocomplete="off">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fe fe-plus text-blue"></i>
                    Nueva Prenda
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-2">
                    <div class="col-md-12">
                        <div class="form-label">Propietario</div>
                        <input type="hidden" name="idcliente" id="idcliente" value="">
                        <input type="text" name="nomcliente" id="nomcliente" class="form-control border-fx-1" value="" disabled>
                        <span id="nprenda-feed-cliente" class="feedtag feedtag-danger"></span>
                    </div>
                </div>                
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-label">Tipo de Prenda</div>
                        <div id="tprenda-render-area" class="custom-controls-stacked py-2">
                            <div class="text-center">Sin Datos</div>                            
                        </div>
                        <span id="nprenda-feed-tprenda" class="feedtag feedtag-danger"></span>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 col-xs-12">
                        <div class="form-label">Descripcion</div>
                        <input type="text" class="form-control text-uppercase border-fx-1" name="descripcion" maxlength="50" required>
                        <span id="nprenda-feed-descripcion" class="feedtag feedtag-danger"></span>
                    </div>
                    <div class="col-md-6 col-xs-12">
                        <div class="form-label">
                            Categoria <div class="" id="categoria-loader"></div>
                        </div>
                        <select id="categoria-list" name="categoria" class="form-control border-fx-1" required>
                        <option value="">-SELECCIONAR-</option>
                        </select>
                        <span id="nprenda-feed-categoria" class="feedtag feedtag-danger"></span>
                    </div>
                </div>
                <div id="prenda-form-area" class="row">
                    <div id="np__serial" class="col-xs-12 col-md-6 d-none">
                        <div class="form-label">Serial</div>
                        <input type="text" class="form-control text-uppercase border-fx-1" name="serial" maxlength="50">
                        <span id="nprenda-feed-serial" class="feedtag feedtag-danger"></span>
                    </div>
                    <div id="np__marca" class="col-xs-12 col-md-6 d-none">
                        <div class="form-label">Marca</div>
                        <input type="text" class="form-control text-uppercase border-fx-1" name="marca" maxlength="50">
                        <span id="nprenda-feed-marca" class="feedtag feedtag-danger"></span>
                    </div>
                    <div id="np__modelo" class="col-xs-12 col-md-6 d-none">
                        <div class="form-label">Modelo</div>
                        <input type="text" class="form-control text-uppercase border-fx-1" name="modelo" maxlength="50">
                        <span id="nprenda-feed-modelo" class="feedtag feedtag-danger"></span>
                    </div>
                    <div id="np__quilate" class="col-xs-12 col-md-6 d-none">
                        <div class="form-label">Quilate</div>
                        <input type="text" class="form-control text-uppercase border-fx-1" name="quilate" maxlength="20">
                        <span id="nprenda-feed-quilate" class="feedtag feedtag-danger"></span>
                    </div>
                    <div id="np__peso" class="col-xs-12 col-md-6 d-none">
                        <div class="form-label">Peso</div>
                        <input type="text" class="form-control text-uppercase border-fx-1" name="peso" maxlength="10">
                        <span id="nprenda-feed-peso" class="feedtag feedtag-danger"></span>
                    </div>
                    <div id="np__anio" class="col-xs-12 col-md-6 d-none">
                        <div class="form-label">Año</div>
                        <input type="text" class="form-control text-uppercase border-fx-1" name="anio" maxlength="4">
                    </div>
                    <div id="np__placa" class="col-xs-12 col-md-6 d-none">
                        <div class="form-label">Placa</div>
                        <input type="text" class="form-control text-uppercase border-fx-1" name="placa" maxlength="8">
                    </div>
                    <div id="np__kilometraje" class="col-xs-12 col-md-6 d-none">
                        <div class="form-label">Kilometraje</div>
                        <input type="text" class="form-control text-uppercase border-fx-1" name="kilometraje" maxlength="10">
                    </div>
                    <div id="np__transmision" class="col-xs-12 col-md-6 d-none">
                        <div class="form-label">Transmision</div>
                        <input type="text" class="form-control text-uppercase border-fx-1" name="transmision" maxlength="20">
                    </div>
                    <div id="np__color" class="col-xs-12 col-md-6 d-none">
                        <div class="form-label">Color</div>
                        <input type="text" class="form-control text-uppercase border-fx-1" name="color" maxlength="50">
                    </div>
                    <div id="np__carroceria" class="col-xs-12 col-md-6 d-none">
                        <div class="form-label">Carroceria</div>
                        <input type="text" class="form-control text-uppercase border-fx-1" name="carroceria" maxlength="25">
                    </div>

                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-label">Estado</div>
                        <div class="range-slider">
                            <div class="range-value">
                                <span id="valora-range-label">5</span>
                            </div>
                            <div class="range-picker">
                                <div class="range-value-left">0</div>
                                <input type="range" min="0" max="10" step=".5" value="5" name="valoracion">
                                <div class="range-value-right">10</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="" class="form-label">Observaciones</label>
                            <textarea name="observacion" class="form-control text-uppercase border-rock" cols="3" rows="2" maxlength="120"></textarea>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="" class="form-label">Comentarios</label>
                            <textarea name="comentario" class="form-control text-uppercase border-rock" cols="3" rows="2" maxlength="120"></textarea>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-body text-right">
                <button class="btn btn-indigo btn-lg">
                    Guardar
                    <i class="fe fe-check-circle"></i>
                </button>
            </div>
        </form>
    </div>

</div>




<script src="<?= base_url() ?>js_modules/credito/credito.insert.js"></script>

