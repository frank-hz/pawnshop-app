<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ElectroStore</title>
    <link rel="icon" type="image/svg" href="<?= base_url() ?>assets/images/box.svg">
    <link rel="stylesheet" href="<?= base_url() ?>assets/fonts/Be Vietnam/be_vietnam.css">
    <link rel="stylesheet" href="<?= base_url() ?>assets/fonts/Inter/inter.css">
	<link rel="stylesheet" href="<?= base_url() ?>assets/css/remate.indice.css">
    <link rel="stylesheet" href="<?= base_url() ?>assets/css/remate.v2.css">
</head>
<body>
    <div id="theme_switch" class="theme-toggle">
        <i class="lni lni-bolt"></i>
    </div>
    <div class="wrapper">
        <div class="header">
            <a href="#" class="logo">
                <i class="fi-rr-shopping-cart"></i>
                Modulo<span>Remates</span>
            </a>
            <div class="sesion">				
                <a href="<?= base_url() ?>" class="btn btn-success" title="Sistema Principal">
				<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-table-export" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
					<path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
					<path d="M11.5 20h-5.5a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v7.5m-16 -3.5h16m-10 -6v16m4 -1h7m-3 -3l3 3l-3 3"></path>
				</svg>
				</a>
				<a href="javascript:void(0)" class="btn" title="Ver Usuario">
					<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-user" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
						<path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
						<circle cx="12" cy="7" r="4"></circle>
						<path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2"></path>
					</svg>
				</a>
				<button id="btn-examinar" class="btn">
					Examinar Articulos
				</button>
            </div>
        </div>
        <div class="main">
			<div class="main-header">
				<h3 class="main-title">
					Articulos Actuales
				</h3>
			</div>
			<div class="table-filters">
				<div class="w-30">
					<div class="form-group">
						<div class="form-sd-label">Tipo de Filtro:</div>
						<div class="radio-items-list">
							<div class="radio-item">
								<input id="filter-all" type="radio" value="ALL" name="radio-filter-selection" checked>
								<label for="filter-all" class="radio-item">
									<div class="radio-text">Todo</div>
								</label>
							</div>
							<div class="radio-item">
								<input id="filter-tipo" type="radio" value="TIPO" name="radio-filter-selection">
								<label for="filter-tipo" class="radio-item">
									<div class="radio-text">Tipo</div>
								</label>
							</div>
							<div class="radio-item">
								<input id="filter-descripcion" type="radio" value="DESCRIPCION" name="radio-filter-selection">
								<label for="filter-descripcion" class="radio-item">
									<div class="radio-text">Descripcion</div>
								</label>
							</div>
							<div class="radio-item">
								<input id="filter-estado" type="radio" value="ESTADO" name="radio-filter-selection">
								<label for="filter-estado" class="radio-item">
									<div class="radio-text">Estado</div>
								</label>
							</div>
						</div>
					</div>
				</div>
				<div class="w-40">
					<div id="filter-input-section" class="form-group d-none">
						<div class="form-sd-label">Buscar</div>
						<input id="filter-input-search" type="text" class="form-control text-upper" placeholder="Ingresar dni, nombres o apellidos del cliente" autocomplete="off">
					</div>
					<div id="filter-option-section" class="form-group px-2 d-none">
						<div class="form-label">Seleccionar</div>
						<div id="option-render-area" class="d-flex">
							RENDER OPTIONS
							<!-- <div class="radio-form mx-2">
								<input type="radio" id="radio-form-electro" name="filter-type-selection">
								<label for="radio-form-electro">
									<span class="radio-form-icon"></span>
									ELECTRO
								</label>
							</div>
							<div class="radio-form mx-2">
								<input type="radio" id="radio-form-joya" name="filter-type-selection">
								<label for="radio-form-joya">
									<span class="radio-form-icon"></span>
									JOYA
								</label>
							</div>
							<div class="radio-form mx-2">
								<input type="radio" id="radio-form-vehiculo" name="filter-type-selection">
								<label for="radio-form-vehiculo">
									<span class="radio-form-icon"></span>
									VEHICULO
								</label>
							</div> -->
						</div>
					</div>
				</div>
				<div class="w-30">
					<div class="form-group">
						<div class="form-sd-label mb-1">Sucursal</div>
						<select id="filter-sucursal" class="select-css">
							<option value="">TODO</option>
							<option value="">CHIMBOTE</option>
							<option value="">LIMA/HIGUERETA</option>
							<option value="">NEW YORK</option>
						</select>
					</div>
				</div>
			</div>
            <div class="datatable">
                <table id="datatable-articulo">
                    <thead>
                        <tr>
                            <th class="min-w-100">Credito</th>
							<th class="min-w-250">Descripcion</th>
                            <th class="min-w-100">Tipo</th>
                            <th class="min-w-100">Creacion</th>
                            <th>Costo Total</th>
							<th>Precio Vent.</th>
                            <th class="min-w-100">Estado</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="7" class="text-center">Sin Data</td>
                        </tr>
                    </tbody>
                </table>
            </div>

        </div>
        <div id="data-widget" class="sidemain">
            <h3 class="main-title">Resumen de Datos:</h3>
			<div class="task-list">
				<div class="task-item">
					<div class="task-label">
						Articulos Totales
						<span>Suma de Cantidad</span>
					</div>
					<div class="task-value" id="widget-cantidad-total">--</div>
				</div>
				<div class="task-item">
					<div class="task-label">
						Articulos Disponibles
						<span>Suma de Cantidad</span>
					</div>
					<div class="task-value" id="widget-cantidad-activos">--</div>
				</div>
				<div class="task-item">
					<div class="task-label">
						Articulos Vendidos
						<span>Suma de Cantidad</span>
					</div>
					<div class="task-value" id="widget-cantidad-vendidos">--</div>
				</div>
				<!-- <div class="task-item">
					<div class="task-label">
						Precio Total
						<span>Suma de Precios</span>
					</div>
					<div class="task-value" id="widget-costo-total">S/ --.---</div>
				</div>
				
				<div class="task-item">
					<div class="task-label">
						Precio por Articulos Activos
						<span>Suma de Precios</span>
					</div>
					<div class="task-value" id="widget-costo-activos">S/ --.---</div>
				</div>
				
				<div class="task-item">
					<div class="task-label">
						Precio por Articulos Vendidos
						<span>Suma de Precios</span>
					</div>
					<div class="task-value" id="widget-costo-vendidos">S/ --.---</div>
				</div> -->
			</div>
            <!-- <div class="pegatine-map">
                <div class="pegatine">
                    <div class="pegatine-value" id="widget-monto-total">S/ -.--</div>
                    <div class="pegatine-description">Costo Total de Articulos</div>
                </div>
                <div class="pegatine">
                    <div class="pegatine-value" id="widget-cantidad-total">--</div>
                    <div class="pegatine-description">Cantidad de Articulos</div>
                </div>
                <div class="pegatine">
                    <div class="pegatine-value">--</div>
                    <div class="pegatine-description">Articulos Disponibles</div>
                </div>
                <div class="pegatine">
                    <div class="pegatine-value">--</div>
                    <div class="pegatine-description">Articulos Vendidos</div>
                </div>
            </div> -->
        </div>
    </div>

    <form id="modal-articulo-venta" class="modal-wrapper" autocomplete="off">
        <div class="modal">
            <div class="modal-header">
                <div class="modal-title">
                    <i class="lni lni-empty-file modal-icon"></i>
                    Registrar Venta
                </div>
                <a href="javascript:void(0)" class="modal-close" name="modal-close-dissmiss" data-objetive="modal-articulo-venta">&#10006;</a>
            </div>
            <div class="modal-body-raw">
                <input type="hidden" name="precio">
                <input type="hidden" name="articulo">
                <input type="hidden" name="total">
                <!-- <legend class="text-blue font-600">
					(*) Datos Opcionales
				</legend> -->
				<div class="d-flex">
					<div class="form-group w-30">
						<label for="" class="form-label">Dni:</label>
						<input type="number" step="any" name="dni" class="form-control text-upper">
					</div>
					<div class="form-group w-70">
						<label for="" class="form-label">Nombres y Apellidos:</label>
						<input type="text" name="nombre" class="form-control text-upper">
					</div>
				</div>
				<div class="d-flex">
					<div class="form-group w-50">
						<label for="" class="form-label">Celular:</label>
						<input type="text" name="celular" class="form-control text-upper">
					</div>
					<div class="form-group w-50">
						<label for="" class="form-label">Metodo de Pago:</label>
						<label for="metodo" class="control-select">
							<select id="metodo" name="metodo" class="form-control" required>
								<option value="EFEC">Efectivo</option>
								<option value="TARG">Tarjeta</option>
								<option value="TRAN">Transferencia</option>
							</select>
							<svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><polyline points="15 6 9 12 15 18" /></svg>
						</label>
					</div>
				</div>
				<div class="d-flex">
					<div class="w-100 form-group">
						<div class="form-label">Comentarios</div>
						<textarea name="comentario" class="form-control text-upper" cols="10" rows="40"></textarea>
					</div>
				</div>  
				<div class="d-flex">                   
					<div class="form-group">
						<label for="" class="form-label">Aplicar Descuento:</label>
						<input type="text" name="descuento" class="form-control" autocomplete="off" placeholder="0.00">
						<div class="feedback text-danger" id="descuento-feedback"></div>
					</div>
					<div class="form-group">
						<div class="form-label">&nbsp;</div>
						<a href="javascript:void(0)" id="ctrl-descuento-aplicar" class="btn btn-indigo" style="margin-top: 0 !important">Aplicar</a>
					</div>
				</div> 
            </div>
            <div class="modal-table">
                <table>
                    <thead>
                        <tr>
                            <th>Descripcion</th>
                            <th class="text-center">Cantidad</th>
                            <th>Monto</th>
                        </tr>
                    </thead>
                    <tbody id="modal_venta_dataset">
                        <tr>
                            <td>TV LED 28' SAMSUNG</td>
                            <td>01</td>
                            <td>S/ 500.00</td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr class="font-500">
                            <td class="text-right" colspan="2">
                                DESCUENTO
                            </td>
                            <td id="view_descuento"></td>
                        </tr>
                        <tr class="font-500">
                            <td class="text-right" colspan="2">
                                TOTAL
                            </td>
                            <td id="view_total"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <div class="modal-footer">
                <button class="btn btn-success" type="submit">
                    Procesar
                </button>
            </div>
        </div>
    </form>
    <form id="modal-articulo-editar" class="modal-wrapper" autocomplete="off">
        <div class="modal">
            <div class="modal-header">
                <div class="modal-title">
                    <i class="lni lni-pencil-alt modal-icon"></i>
                    Editar Articulo
                </div>
                <a href="javascript:void(0)" class="modal-close" name="modal-close-dissmiss" data-objetive="modal-articulo-editar">&#10006;</a>
            </div>
            <div class="modal-body">
                <input type="hidden" name="id_articulo">
				<div class="w-100 mb-2">
					<div class="form-group">
						<label for="" class="form-label">Articulo</label>
						<span class="d-block" id="md-editar-articulo-preview"></span>
					</div>
				</div>
				<div class="d-flex">
					<div class="form-group w-50">
						<label for="" class="form-label">
							Costo Inicial
						</label>
						<div class="form-value" id="edicion-costo-inicial">0.00</div>
						<!-- <input type="text" step="any" name="costo_inicial" class="form-control form-control-read" readonly> -->
					</div>
					<div class="form-group w-50">
						<label for="" class="form-label">Costo Total</label>
						<div class="form-value" id="edicion-costo-total">0.00</div>
						<!-- <input type="text" step="any" name="costo_total" class="form-control form-control-read" readonly> -->
					</div>
				</div>
				<div class="d-flex">					
					<div class="form-group w-50">
						<label for="" class="form-label">Reparacion/Mantenimiento</label>
						<input type="text" name="costo_reparacion" class="form-control" id="edicion-reparacion">
						<div class="feedback text-danger" id="reparacion-feedback"></div>
					</div>
					<div class="form-group w-50">
						<label for="" class="form-label">
							Precio Venta 
							<span class="text-small-indigo">(Referencial)</span>
						</label>
						<input type="text" step="any" id="edicion-venta" name="precio_venta" class="form-control">
					</div>
				</div>
            </div>
            <div class="modal-footer">
				<button class="btn" name="modal-close-dissmiss" data-objetive="modal-articulo-editar">
					Cancelar
				</button>
                <button class="btn btn-indigo" type="submit">
                    Guardar
                </button>
            </div>
        </div>
    </form>
    <div id="modal-articulo-detalle" class="modal-wrapper">
        <div class="modal modal-wd-2">
            <div class="modal-header">
                <div class="modal-title">
                    <i class="lni lni-layers modal-icon"></i>
                    Detalle de Articulo
                </div>
                <a href="javascript:void(0)" class="modal-close" name="modal-close-dissmiss" data-objetive="modal-articulo-detalle">&#10006;</a>
            </div>
            <div class="modal-body">
                <!-- <div class="d-flex mb-3">
                    <div class="group-info w-100">
                        <label for="" class="skeleton-loading">Descripcion</label>
                        <span class="skeleton-loading">Lorem ipsum dolor </span> 
                    </div>                    
                </div>
                <div class="d-flex mb-3">
                    <div class="group-info w-50">
                        <label for="" class="skeleton-loading">Tipo</label>
                        <span class="skeleton-loading">ELECTRO</span>
                    </div>
                    <div class="group-info w-50">
                        <label for="" class="skeleton-loading">Categoria</label>
                        <span class="skeleton-loading">TELEVISORES</span>
                    </div>
                </div>
                <div class="d-flex mb-3">
                    <div class="group-info w-100">
                        <label for="" class="skeleton-loading">Comentarios</label>
                        <span class="skeleton-loading">Lorem ipsum dolor sit amet consectetur adipisicing elit</span>
                    </div>
                </div> -->
            </div>
            <div class="modal-footer">
                <button class="btn" name="modal-close-dissmiss">
                    Cerrar
                </button>
            </div>
        </div>
    </div>
    <div id="modal-venta-recibo" class="modal-wrapper">
        <div class="modal">
			<div id="recibo-x">
				<div class="modal-header no-border">
					<div class="modal-talon-header">
						<i class="fi-rr-document modal-jb-icon"></i>
						<div class="modal-main-text">
							Detalle de Venta <span id="modal-recibo-serie">#</span>
						</div>
						<div class="modal-date">
							---, -- de --- del ---
						</div>
					</div>
					<a href="javascript:void(0)" class="modal-close" name="modal-close-dissmiss" data-objetive="modal-venta-recibo">&#10006;</a>
				</div>
			
				<div class="modal-body px-4 border-bot-divider">
					<div class="info-inline">
						<span>Cliente: </span>
						<span id="recibo_view_cliente">Sin Data</span>
					</div>
					<div class="info-inline">
						<span>Dni Cliente: </span>
						<span id="recibo_view_dni">Sin Data</span>
					</div>
					<div class="info-inline">
						<span>Empleado Registrador: </span>
						<span id="recibo_view_empleado">Sin Data</span>
					</div>
				</div>
				<div class="modal-invoice">
					<table>
						<thead>
							<tr>
								<th>Descripcion</th>
								<th class="text-center">Cantidad</th>
								<th>Monto</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td>
									TV LED 25' SAMSUNG
									<div class="cell-subtext">
										TELEVISOR - ELECTRO
									</div>
								</td>
								<td class="text-center">01</td>
								<td>S/ 25.00</td>
							</tr>
							<tr>
								<td colspan="2" class="text-right">Descuento</td>
								<td >S/ 0.00</td>
							</tr>
							<tr class="text-blue fsize-15">
								<td colspan="2" class="text-right font-600">Total</td>
								<td class="font-600">S/ 25.00</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
            <div class="modal-footer">
				<button id="btn-recibo-screenshot" class="btn btn-success">
					Descargar Captura
				</button>
                <button class="btn" name="modal-close-dissmiss">
                    Cerrar
                </button>
            </div>
        </div>
    </div>
	<div id="modal-venta-invoice" class="modal-wrapper content-center">
		<div class="modal-invoice">

			<div id="invoice-screen-area" class="modal-invoice-body">
				<div class="invoice-title">
					Venta de Articulo
					<!-- <div class="date-tag">Modulo de Remates</div> -->
					<span class="date-tag" id="invoice-fecha">[---]</span>
				</div>
				<div class="invoice-content">
					<div class="d-flex justify-between">
						<div class="w-30 text-title">Cliente: </div>
						<div class="w-70 text-right" id="invoice-cliente">JOSE LUIS CORDOVA MIRANDA</div>
					</div>
					<div class="d-flex justify-between">
						<div class="w-30 text-title">Doc/Dni: </div>
						<div class="w-70 text-right" id="invoice-doc">70609372</div>
					</div>
					<div class="d-flex justify-between">
						<div class="w-30 text-title">Celular: </div>
						<div class="w-70 text-right" id="invoice-celular">948005468</div>
					</div>
					<div class="d-flex justify-between">
						<div class="w-30 text-title">Metodo Pago: </div>
						<div class="w-70 text-right" id="invoice-metodo">EFECTIVO</div>
					</div>
					<div class="invoice-main-value">
						<div class="invoice-main-title" id="invoice-total">S/ 1500.00</div>
						<div class="invoice-main-desc">Total Pagado</div>
					</div>
				</div>
				<div class="text-separator"></div>
				<table class="invoice-table mb-2" id="invoice-detalle">
					<tr>
						<td>LAPTOP 25' SAMSUNG</td>
						<td>S/ 2500.00</td>
					</tr>
					<tr>
						<td>SMART TV LED LG</td>
						<td>S/ 1400.00</td>
					</tr>
				</table>
			</div>
			<div class="modal-invoice-footer text-right">
				<button id="btn-invoice-screenshot" class="btn btn-block btn-dark mb-2">Descargar Captura</button>
				<button class="btn btn-block" name="modal-close-dissmiss" data-objetive="modal-venta-invoice">Salir</button>
			</div>
		</div>
	</div>

	<div id="modal-examinar" class="modal-wrapper">
        <div class="modal modal-wd-2">
            <div class="modal-header">
                <div class="modal-title">
                    <i class="lni lni-search modal-icon"></i>
                    Examinar Articulos
                </div>
                <a href="javascript:void(0)" class="modal-close" name="modal-close-dissmiss" data-objetive="modal-articulo-detalle">&#10006;</a>
            </div>
            <!-- <div class="modal-body">

            </div> -->
			<div class="modal-table">
				<table>
					<thead>
						<tr>
							th*
						</tr>
					</thead>
				</table>
			</div>
            <div class="modal-footer">
                <button class="btn" name="modal-close-dissmiss">
                    Cerrar
                </button>
            </div>
        </div>
    </div>
	

	<div id="loader-integrated" class="loading-wrapper">
		<div class="loading-card">
			<div class="loading-message">
				<span class="loading-sprite"></span>
				<div class="loading-text">Cargando...</div>
			</div>
		</div>
	</div>
    <div id="popup-alert" class="popup">
        <div class="popup-icon">
            <i class="fi-br-info"></i>
        </div>
        <div class="popup-text">Lorem ipsum dolor sit amet consectetur</div>
        <a href="javascript:void(0)" class="popup-close">&#10006;</a>
    </div>
	<div id="comand-up" class="comand-up">
		<div class="comand-icon">
			<svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><circle cx="12" cy="12" r="9" /><line x1="12" y1="8" x2="12.01" y2="8" /><polyline points="11 12 12 12 12 16 13 16" /></svg>
		</div>
		<div class="comand-text">
			----
		</div>
		<a href="javascript:void(0)" class="comand-up-close">&#10006</a>
	</div>
    
	<script src="<?= base_url() ?>assets/plugins/html2canvas.min.js"></script>
	<script src="<?= base_url() ?>js_modules/app.js?v=2"></script>
    <script src="<?= base_url() ?>js_modules/remate/remate.v2.js?v=3"></script>
	
</body>
</html>
