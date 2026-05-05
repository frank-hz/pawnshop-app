<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ElectroCash Remates</title>
    <link rel="icon" type="image/svg" href="<?= base_url() ?>assets/images/box.svg">
    <link rel="stylesheet" href="<?= base_url() ?>assets/fonts/Be Vietnam/be_vietnam.css">
	<link rel="stylesheet" href="<?= base_url() ?>assets/plugins/gridjs/mermaid.min.css">
	<link rel="stylesheet" href="<?= base_url() ?>assets/css/remate.indice.css">
    <link rel="stylesheet" href="<?= base_url() ?>assets/css/remate.v1.css">
</head>
<body>
	<div id="popup" class="popup">
		<div class="popup-icon">
		<svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
	 	viewBox="0 0 489.068 489.068" style="enable-background:new 0 0 489.068 489.068;" xml:space="preserve">
			<g>
				<g>
					<path d="M486.011,456.66l-222.6-445.2c-7.8-14.5-29.9-15.9-37.5,0l-223.6,448.4c-6.4,9.8,0.7,29.1,18.7,29.1h447.3
						C484.511,490.16,494.411,470.86,486.011,456.66z M53.211,448.36l191.5-382.8l190.4,382.8H53.211z"/>
					<path d="M223.911,177.86v125.9c0,11.4,9.4,20.8,20.8,20.8c11.4,0,19.8-9.4,20.8-20.8v-125.9c0-11.4-9.4-20.8-20.8-20.8
						S223.911,166.46,223.911,177.86z"/>
					<path d="M244.711,347.46c-11.4,0-20.8,9.4-20.8,20.8v13.5c0,11.4,9.4,20.8,20.8,20.8c11.4,0,19.8-9.4,20.8-20.8v-13.5
						C265.511,356.86,256.111,347.46,244.711,347.46z"/>
				</g>
			</g>
		</svg>
		</div>
		<div class="popup-message" data-obj="popup">
			Datos No Encontrados
		</div>
		<div class="popup-close">✖</div>
	</div>
    <div class="wrapper">
		<div class="header">			
			<div class="header-titles">
				<a href="#" class="logo">
					<svg version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
					viewBox="0 0 482 482" style="enable-background:new 0 0 482 482;" xml:space="preserve">
						<g><g>
						<polygon points="279.8,244.8 258.2,257.3 258.2,482 452.7,369.7 452.7,145 		"/>
						<polygon points="315,43.3 240.2,0 40.3,115.4 115.2,158.7 		"/>
						<polygon points="440,115.4 353.8,66.3 154,181.7 165.4,187.6 240.2,230.8 314.6,187.9 		"/>
						<polygon points="138.9,264.3 103.1,245.9 103.1,188.7 29.3,146.2 29.3,369.3 222.4,480.8 222.4,257.7 138.9,209.6 		"/>
						</g></g>
					</svg>
					<div class="logo-label">
						<div class="logo-label-main">ElectroStore</div>
						<div class="logo-subtitle">Modulo de Remates</div>
					</div>
				</a>
			</div>
			<div class="sesion-controls">				
				<a href="<?= base_url() ?>" class="btn btn-blue">
					<svg xmlns="http://www.w3.org/2000/svg" class="ionicon" viewBox="0 0 512 512"><title>Arrow Back</title><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="48" d="M244 400L100 256l144-144M120 256h292"/></svg>
					Sistema Principal
				</a>
				<button class="btn btn-blue">
					<svg xmlns="http://www.w3.org/2000/svg" class="ionicon" viewBox="0 0 512 512"><title>Warning</title><path d="M85.57 446.25h340.86a32 32 0 0028.17-47.17L284.18 82.58c-12.09-22.44-44.27-22.44-56.36 0L57.4 399.08a32 32 0 0028.17 47.17z" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="32"/><path d="M250.26 195.39l5.74 122 5.73-121.95a5.74 5.74 0 00-5.79-6h0a5.74 5.74 0 00-5.68 5.95z" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="32"/><path d="M256 397.25a20 20 0 1120-20 20 20 0 01-20 20z"/></svg>
					Verificacion
				</button>
			</div>
		</div>
		<div class="content">			
			<div class="tabler-widget">
				<!-- <div class="tabler-title"></div> -->
				<div class="tabler-item">
					<div class="tabler-item-value" id="tabler-prendas-totales">--</div>
					<div class="tabler-item-label">Prendas Totales</div>
				</div>
				<div class="tabler-item">
					<div class="tabler-item-value" id="tabler-prendas-disponibles">--</div>
					<div class="tabler-item-label">Prendas Disponibles</div>
				</div>
				<div class="tabler-item">
					<div class="tabler-item-value" id="tabler-prendas-vendidas">--</div>
					<div class="tabler-item-label">Prendas Vendidas</div>
				</div>
			</div>
			<div class="main">
				<div id="datatable" class="datatable">
					<div class="datatable-params">
						<div class="params-group-a">						
							<div class="medal-item">
								<div class="medal-label">
									<svg xmlns="http://www.w3.org/2000/svg" class="ionicon" viewBox="0 0 512 512"><title>Search</title><path d="M221.09 64a157.09 157.09 0 10157.09 157.09A157.1 157.1 0 00221.09 64z" fill="none" stroke="currentColor" stroke-miterlimit="10" stroke-width="32"/><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-miterlimit="10" stroke-width="32" d="M338.29 338.29L448 448"/></svg>
									Buscar por
								</div>
								<div class="medal-input">
									<select name="" id="params-select-tipo">
										<option value="ALL">Todo</option>
										<option value="TIPO">Tipo</option>
										<option value="DESC">Descripcion</option>
										<option value="MODELO">Modelo</option>
										<option value="SERIE">Serie</option>
										<option value="ESTADO">Estado</option>
									</select>
								</div>
							</div>						
							<div id="params-section-input" class="medal-item d-none">
								<div class="medal-label">
									<svg xmlns="http://www.w3.org/2000/svg" class="ionicon" viewBox="0 0 512 512"><title>Create</title><path d="M384 224v184a40 40 0 01-40 40H104a40 40 0 01-40-40V168a40 40 0 0140-40h167.48" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="32"/><path d="M459.94 53.25a16.06 16.06 0 00-23.22-.56L424.35 65a8 8 0 000 11.31l11.34 11.32a8 8 0 0011.34 0l12.06-12c6.1-6.09 6.67-16.01.85-22.38zM399.34 90L218.82 270.2a9 9 0 00-2.31 3.93L208.16 299a3.91 3.91 0 004.86 4.86l24.85-8.35a9 9 0 003.93-2.31L422 112.66a9 9 0 000-12.66l-9.95-10a9 9 0 00-12.71 0z"/></svg>
									Ingresar 
								</div>
								<div class="medal-input">
									<input type="text" id="params-input-search" class="text-uppercase">
								</div>
							</div>
							<form id="params-section-radio" class="medal-radio-items d-none">
								<div class="no-data-tag">SIN DATA</div>
							</form>
						</div>
						<div class="params-group-b">
							<div class="medal-item">
								<div class="medal-label">
									<svg xmlns="http://www.w3.org/2000/svg" class="ionicon" viewBox="0 0 512 512"><title>Location</title><path d="M256 48c-79.5 0-144 61.39-144 137 0 87 96 224.87 131.25 272.49a15.77 15.77 0 0025.5 0C304 409.89 400 272.07 400 185c0-75.61-64.5-137-144-137z" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="32"/><circle cx="256" cy="192" r="48" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="32"/></svg>
									Sucursal
								</div>
								<div class="medal-input">
									<select name="" id="params-select-sucursal">
										<option value="">SELECCIONAR</option>
									</select>
								</div>
							</div>	
						</div>					
					</div>
					<div id="datatable-content" class="datatable-content">
						<div id="data-area" class="datatable-area"></div>
						<div id="table-loading-screen" class="datatable-loading d-none">
							<div class="loading-message">
								<div class="loading-sprite"><span></span></div>
								Cargando...
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div id="modal-prenda-detalle" class="modal-wrapper">
		<div class="modal">
			<div class="modal-header">
				<div class="modal-title">
					<div class="modal-title-icon">
						<svg xmlns="http://www.w3.org/2000/svg" class="ionicon" viewBox="0 0 512 512"><title>Layers</title><path d="M434.8 137.65l-149.36-68.1c-16.19-7.4-42.69-7.4-58.88 0L77.3 137.65c-17.6 8-17.6 21.09 0 29.09l148 67.5c16.89 7.7 44.69 7.7 61.58 0l148-67.5c17.52-8 17.52-21.1-.08-29.09zM160 308.52l-82.7 37.11c-17.6 8-17.6 21.1 0 29.1l148 67.5c16.89 7.69 44.69 7.69 61.58 0l148-67.5c17.6-8 17.6-21.1 0-29.1l-79.94-38.47" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="32"/><path d="M160 204.48l-82.8 37.16c-17.6 8-17.6 21.1 0 29.1l148 67.49c16.89 7.7 44.69 7.7 61.58 0l148-67.49c17.7-8 17.7-21.1.1-29.1L352 204.48" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="32"/></svg>
					</div>
					Detalle de la Prenda
				</div>
				<div class="modal-options">
					<div class="modal-close" name="modal-close" data-obj="modal-prenda-detalle">✖</div>
				</div>
			</div>
			<div id="modal-message-general" class="modal-message-section d-nonex">
				<div class="modal-message">
					<div class="modal-message-icon">
						<svg width="512px" height="512px" viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg"><title>ionicons-v5-a</title><path d="M256,48C141.31,48,48,141.31,48,256s93.31,208,208,208,208-93.31,208-208S370.69,48,256,48Zm0,319.91a20,20,0,1,1,20-20A20,20,0,0,1,256,367.91Zm21.72-201.15-5.74,122a16,16,0,0,1-32,0l-5.74-121.94v-.05a21.74,21.74,0,1,1,43.44,0Z"/></svg>
					</div>
					<div class="modal-message-content">
						Sin Datos
					</div>
				</div>
			</div>
			<div class="modal-body">
				<form id="prenda-detalle-form" class="form-section">
					<div class="form-group w-50">
						<div class="form-label">Cliente/Propietario</div>
						<a id="prenda_cliente_value" href="" class="form-link" target="_blank">JOSE PEREZ MENDOZA</a>
					</div>
					<div class="form-group w-50">
						<div class="form-label">Credito</div>
						<a id="prenda_credito_value" href="" class="form-link" target="_blank">JOSE PEREZ MENDOZA</a>
					</div>
					<div class="form-group w-50">
						<div class="form-label">Descripcion</div>
						<div id="prenda_descrip_value" class="form-text">LAPTOP</div>
					</div>
					<div class="form-group w-50">
						<div class="form-label">Estado</div>
						<span id="prenda_estado_tag" class="tag tag-activo">No Data</span>
					</div>
					<div class="form-group w-50">
						<div class="form-label">Tipo</div>
						<div id="prenda_tipo_value" class="form-text">ELECTRO</div>
					</div>
					<div class="form-group w-50">
						<div class="form-label">Categoria</div>
						<div id="prenda_categoria_value" class="form-text">LAPTOP</div>
					</div>
					<div class="form-group w-50">
						<div class="form-label">Serial</div>
						<div id="prenda_serial_value" class="form-text"></div>
					</div>
					<div class="form-group w-50">
						<div class="form-label">Modelo</div>
						<div id="prenda_modelo_value" class="form-text">XS-2541</div>
					</div>
					<div class="form-group w-50">
						<div class="form-label">Comentarios</div>
						<div id="prenda_comentario_value" class="form-text">Lorem ipsum dolor sit amet consectetur, adipisicing elit. Nam perferendis optio libero.</div>
					</div>
					<div class="form-group w-50">
						<div class="form-label">Observaciones</div>
						<div id="prenda_observacion_value" class="form-text">Lorem ipsum dolor sit amet consectetur, adipisicing elit. Nam perferendis optio libero.</div>
					</div>

					<div class="w-100 mb-1">
						<div class="divider-x"></div>
					</div>
					<div class="form-group w-50">
						<div class="form-label">Precio Inicial</div>
						<input id="prenda_precio_inicial" type="number" step="any" value="0" data-basevalue="0" readonly>
						<span id="feed_precio_inicial" class="feed-text text-danger"></span>
					</div>
					<div class="form-group w-50">
						<div class="form-label">Costo Total</div>
						<input id="prenda_costo_total" type="number" step="any" value="0" data-basevalue="0" readonly>
						<span id="feed_costo_total" class="feed-text text-danger"></span>
					</div>
					<div class="form-group w-50">
						<div class="form-label">Reparacion/Mantenimiento</div>
						<input id="prenda_reparacion" type="number" step="any" value="0" data-basevalue="0" readonly>
						<span id="feed_reparacion" class="feed-text text-danger"></span>
					</div>
					<div class="form-group w-50">
						<div class="form-label">Precio Venta</div>
						<input id="prenda_precio_venta" type="number" step="any" value="0" data-basevalue="0" readonly>
						<span id="feed_precio_venta" class="feed-text text-danger"></span>
					</div>
				</form>
			</div>
			<div class="modal-footer justify-between">
				<div class="d-flex">
					<button id="btn_edit_enable" class="btn mr-1 font-weight-600 text-blue">Editar</button>
					<button id="btn_venta_add" class="btn mr-1 font-weight-600 text-blue">Agregar a la Venta</button>
				</div>
				<div class="d-flex">
					<button id="btn_edit_cancel" class="btn mr-1 btn-danger d-none">Cancelar</button>
					<button id="btn_edit_save" class="btn mr-1 btn-blue d-none">Guardar Cambios</button>
				</div>
			</div>
		</div>
	</div>
	
	<script src="<?= base_url() ?>js_modules/app.js"></script>
	<script src="<?= base_url() ?>js_modules/remate/remate.v1.js" type="module"></script>
	<script src="<?= base_url() ?>assets/plugins/gridjs/gridjs.production.min.js"></script>
	
</body>
</html>
