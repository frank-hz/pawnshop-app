<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Electro Analisis</title>
	<link rel="icon" type="image/png" href="<?= base_url() ?>assets/images/chart2.png">
	<link rel="stylesheet" href="<?= base_url() ?>assets/fonts/Roboto/roboto.css">
	<link rel="stylesheet" href="<?= base_url() ?>assets/fonts/Poppins/poppins.css">
	<link rel="stylesheet" href="<?= base_url() ?>assets/fonts/Be Vietnam/be_vietnam.css">
	<link rel="stylesheet" href="<?= base_url() ?>assets/fonts/boxicons 2.1.4/css/boxicons.min.css">
	<script src="<?= base_url() ?>assets/plugins/sheetjs/dist/FileSaver.js"></script>
	<script src="<?= base_url() ?>assets/plugins/sheetjs/dist/xlsx.full.min.js"></script>
	<link rel="stylesheet" href="<?= base_url('global-code/css/vaporwave.css'); ?>">
	<link rel="stylesheet" href="<?= base_url('assets/css/analisis.index.css?v=19012025'); ?>">
</head>
<body>	
	<div class="wrapper">
		<div class="header">
			<div class="logo">
				<a href="#">
					<img src="<?= base_url() ?>assets/images/chart2.png" alt="">
					Electro<span>Analisis</span>
				</a>
			</div>
			<div class="menu">
				<button id="btn-sucursal" class="header-option">
					<i class="bx bx-map"></i>No Data
				</button>
				<a href="<?= base_url() ?>" class="header-option">
					<i class='bx bx-layout'></i>
					Regresar al Sistema
				</a>
			</div>
		</div>
		<div class="front-header">
            <div class="front-header-cell front-title">
                <div class="color-indigo">
					<?php echo $this->session->userdata('pres_ses_sede'); ?>
				</div>
            </div>
            <div class="front-header-menu">
				<div class="front-header-cell">
					<div id="filter-current-viewer" class="front-value">
						<div id="typefilter-loader" class="spinner-v1-small text-center d-none"></div>
						<div id="typefilter" style="font-size: .97rem">&nbsp;</div>
					</div>
					<div class="front-subtitle">Filtro Actual</div>
				</div>
                <div class="front-header-cell text-left px-3">
                    <div class="front-value" id="general_credito_activo">0</div>
                    <div class="front-subtitle">Creditos Activos</div>
                </div>
                <div class="front-header-cell text-left px-3">
                    <div class="front-value" id="general_dinero_activo">S/ 0.00</div>                
                    <div class="front-subtitle">Dinero Prestado por Creditos Activos</div>
                </div>
                <div class="front-header-cell">
                    <div class="front-inline-text">
                        <div class="front-subtitle text-indigo">Desde: </div>
                        <div class="front-inline-value" id="general_rango_inicio">--/--/-----</div>
                    </div>
                    <div class="front-inline-text">
                        <div class="front-subtitle text-indigo">Hasta: </div>
                        <div class="front-inline-value" id="general_rango_final">--/--/-----</div>
                    </div>
                </div>
            </div>
        </div>
		<div class="main">
			<div id="collapse-filter" class="collapse">
				<div class="collapse-header">
					<button id="collapse-dropwdown" class="btn-collapse btn-collapse-open text-indigo">
						<i class='bx bx-filter-alt'></i> 
						Establecer Filtros
					</button>
				</div>
				<form id="filtro-form" class="collapse-content">
					<div class="collapse-section">
						<div class="label-title">Tipo de Busqueda:</div>
						<div class="radio-tag-list px-4">
							<label for="type-filter-actual" class="sodv-radio mr-3">
								<input id="type-filter-actual" type="radio" name="type-filter-item" value="MES_ACTUAL">
								<span class="sodv-radio-icon"></span>
								<div class="sodv-label">Mes Actual</div>
							</label>
							<label for="type-filter-anio" class="sodv-radio mr-3">
								<input id="type-filter-anio" type="radio" name="type-filter-item" value="ANIO">
								<span class="sodv-radio-icon"></span>
								<div class="sodv-label">Año</div>
							</label>
							<label for="type-filter-mes" class="sodv-radio mr-3">
								<input id="type-filter-mes" type="radio" name="type-filter-item" value="MES">
								<span class="sodv-radio-icon"></span>
								<div class="sodv-label">Mes</div>
							</label>
							<label for="type-filter-fecha" class="sodv-radio mr-3">
								<input id="type-filter-fecha" type="radio" name="type-filter-item" value="FECHA">
								<span class="sodv-radio-icon"></span>
								<div class="sodv-label">Fecha</div>
							</label>
							<label for="type-filter-rango" class="sodv-radio mr-3">
								<input id="type-filter-rango" type="radio" name="type-filter-item" value="RANGO">
								<span class="sodv-radio-icon"></span>
								<div class="sodv-label">Rango de Fechas</div>
							</label>
						</div>
					</div>
					<div class="collapse-section">
						<div id="section_filter_rango" class="pt-1 d-hide">
							<div class="d-flex">
								<div class="form-group mx-2">            
									<label for="">Rango Inicial:</label>
									<input type="date" class="form-control" name="rango_inicial">
								</div>
								<div class="form-group mx-2">                        
									<label for="">Rango Final:</label>
									<input type="date" class="form-control" name="rango_final">
								</div>
							</div>
						</div>
						<div id="section_filter_fecha" class="d-hide">
							<div class="d-flex">
								<div class="form-group">            
									<label for="">Fecha de Busqueda:</label>
									<input type="date" class="form-control" name="fecha">
								</div>
							</div>
						</div>
						<div id="section_filter_anio" class="mb-2 d-hide">
							<div class="d-flex">
								<div class="form-group w-auto py-3">
									<label for="">Seleccionar Año:</label>
									<ul id="year_area_render" class="radio-group">
										<!-- <li>
											<input id="rd_2019" type="radio" name="anio_radio_item" value="2019">
											<label for="rd_2019">2019</label>
										</li>
										<li>
											<input id="rd_2020" type="radio" name="anio_radio_item" value="2020">
											<label for="rd_2020">2020</label>
										</li>
										<li>
											<input id="rd_2021" type="radio" name="anio_radio_item" value="2021">
											<label for="rd_2021">2021</label>
										</li> -->
									</ul>
								</div>
							</div>                    	
						</div>
						<div id="section_filter_mes" class="mb-2 d-hide">
							<div class="form-group w-auto">
								<label for="">Seleccionar Mes:</label>
								<ul class="radio-group">
									<li>
										<input id="rd_enero" type="radio" name="mes_radio_item" value="01">
										<label for="rd_enero">Enero</label>
									</li>
									<li>
										<input id="rd_febrero" type="radio" name="mes_radio_item" value="02">
										<label for="rd_febrero">Febrero</label>
									</li>
									<li>
										<input id="rd_marzo" type="radio" name="mes_radio_item" value="03">
										<label for="rd_marzo">Marzo</label>
									</li>
									<li>
										<input id="rd_abril" type="radio" name="mes_radio_item" value="04">
										<label for="rd_abril">Abril</label>
									</li>
									<li>
										<input id="rd_mayo" type="radio" name="mes_radio_item" value="05">
										<label for="rd_mayo">Mayo</label>
									</li>
									<li>
										<input id="rd_junio" type="radio" name="mes_radio_item" value="06">
										<label for="rd_junio">Junio</label>
									</li>
									<li>
										<input id="rd_julio" type="radio" name="mes_radio_item" value="07">
										<label for="rd_julio">Julio</label>
									</li>
									<li>
										<input id="rd_agosto" type="radio" name="mes_radio_item" value="08">
										<label for="rd_agosto">Agosto</label>
									</li>
									<li>
										<input id="rd_septiembre" type="radio" name="mes_radio_item" value="09">
										<label for="rd_septiembre">Septiembre</label>
									</li>
									<li>
										<input id="rd_octubre" type="radio" name="mes_radio_item" value="10">
										<label for="rd_octubre">Octubre</label>
									</li>
									<li>
										<input id="rd_noviembre" type="radio" name="mes_radio_item" value="11">
										<label for="rd_noviembre">Noviembre</label>
									</li>
									<li>
										<input id="rd_diciembre" type="radio" name="mes_radio_item" value="12">
										<label for="rd_diciembre">Diciembre</label>
									</li>
								</ul>
							</div>
						</div>
						<div id="section_filter_actual" class="d-hide">
							<div class="d-flex">
								<h2>Mes Actual</h2>
							</div>
						</div>
					</div>
					
					<div class="collapse-footer px-2">
						<button type="submit" class="btn btn-block btn-primary">
							<i class='bx bx-check'></i> 
							Aceptar
						</button>
						<button id="btn_export_data" type="button" class="btn btn-block">
							<i class='bx bx-download'></i> 
							Descargar
						</button>
					</div>
				</form>
			</div>
			<div id="section_data" class="section-area autoflex">
				<!-- Generacion vs Recuperacion -->
				<div class="grid-item">
					<div class="panel">
						<div class="panel-header">
							<div class="panel-title">Generacion vs Recuperacion</div>
							<div class="panel-options">
								<a name="panel-control-option" data-target="GENVREC" href="javascript:void(0)" title="Ver Log de Datos">
									<i class="bx bx-terminal"></i>
									Log
								</a>
								<a id="EXPORT_GENREC" class="panel-control-option" href="javascript:void(0)" title="Exportar Datos">
									<i class="bx bxs-download"></i> 
									Exportar
								</a>
							</div>
						</div>
						<div class="panel-datatable">
							<table>
								<tr>
									<td>
										<div class="datalabel-item">
											<div class="datalabel-title">Creditos Generados</div>
											<div id="generacion_cantidad" class="datalabel-subtitle">
												-- Registros
											</div>
										</div>
									</td>
									<td>
										<div id="generacion_monto" class="datalabel-value">
											S/ 0.00
										</div>
									</td>
								</tr>
								<tr>
									<td>
										<div class="datalabel-item">
											<div class="datalabel-title">Creditos Recuperado</div>
											<div id="recuperado_cantidad" class="datalabel-subtitle">
												-- Recuperaciones
											</div>
										</div>
									</td>
									<td>
										<div id="recuperado_monto" class="datalabel-value">
											S/ 0.00
										</div>
									</td>
								</tr>
								<tr>
									<td>
										<div class="datalabel-item">
											<div class="datalabel-title">Diferencia</div>
											<div id="generacion_cantidad" class="datalabel-subtitle">
												Generado - Recuperado
											</div>
										</div>
									</td>
									<td>
										<div id="diferencia_monto" class="datalabel-value">
											S/ 0.00
										</div>
									</td>
								</tr>
								<tr>
									<td>
										<div class="datalabel-item">
											<div class="datalabel-title">Creditos en Remate</div>
											<div class="datalabel-subtitle" id="remate_cantidad">&nbsp;</div>
										</div>
									</td>
									<td>
										<div id="remate_monto" class="datalabel-value">
											S/ 0.00
										</div>
									</td>
								</tr>
							</table>

						</div>
					</div>
				</div>
				<!-- Utilidad -->
				<div class="grid-item">
					<div class="panel">
						<div class="panel-header">
							<div class="panel-title">Utilidad</div>
							<div class="panel-options">
								<div id="icon-status-utilidad" class="panel-icon-status status-error" title="Status: ERR">
									
								</div>
								<a name="panel-control-option" data-target="UTILIDAD" href="javascript:void(0)" title="Ver Log de Datos">
									<i class="bx bx-terminal"></i>
									Log
								</a>
								<a id="EXPORT_UTILIDAD" class="panel-control-option" href="javascript:void(0)" title="Exportar Datos">
									<i class="bx bxs-download"></i> 
									Exportar
								</a>
							</div>
						</div>
						<div class="panel-table">
							<table>
								<thead>
									<tr>
										<th>Descripcion</th>
										<th class="text-center">Cant. Pagos</th>
										<th class="text-right">Total</th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td class="font-600">Cuotas</td>
										<td class="text-center font-number" id="utilidad_cuota_cant">--</td>
										<td class="text-right parr-value" id="utilidad_cuota_monto">S/ 0.00</td>
									</tr>
									<tr>
										<td class="font-600">Mora</td>
										<td class="text-center font-number" id="utilidad_mora_cant">--</td>
										<td class="text-right parr-value" id="utilidad_mora_monto">S/ 0.00</td>
									</tr>
									<tr>
										<td class="font-600">Cargos Adicionales</td>
										<td class="text-center font-number" id="utilidad_adicional_cant">--</td>
										<td class="text-right parr-value" id="utilidad_adicional_monto">S/ 0.00</td>
									</tr>
									<tr>
										<td class="font-600">Descuentos</td>
										<td class="text-center font-number" id="utilidad_descuento_cant">--</td>
										<td class="text-right parr-value text-danger" id="utilidad_descuento_monto">S/ 0.00</td>
									</tr>
									<tr class="text-indigo font-600">
										<td colspan="2" class="text-right">Utilidad Neta</td>
										<td class="text-right parr-value" id="utilidad_total_neto">S/ 0.00</td>
									</tr>
								</tbody>
							</table>
							
						</div>
					</div>
				</div>
				<!-- Total de Ingresos -->
				<div class="grid-item">
					<div class="panel">
						<div class="panel-header">
							<div class="panel-title">Total de Ingresos</div>
						</div>
						<div class="panel-table">
							<table>
								<thead>
									<tr>
										<th>Tipo Movimiento</th>
										<th class="text-center">Cant. Procesado</th>
										<th class="text-right">Monto Total</th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td class="font-600">Suma Total de Pagos</td>
										<td class="text-center font-number" >
											<span id="entrada_cantidad">--</span>
										</td>
										<td class="text-right parr-value">
											<span id="entrada_monto">S/ 0.00</span>	
										</td>
									</tr>
									<tr>
										<td class="font-600">Suma Total de Descuentos</td>
										<td class="text-center font-number">
											<span id="salida_cantidad">--</span>
										</td>
										<td class="text-right parr-value">
											<span id="salida_monto">S/ 0.00</span>	
										</td>
									</tr>
									<tr>
										<td class="font-600" colspan="2">Total</td>
										<td class="text-right parr-value text-indigo">
											<span id="total_monto_diferencia">S/ 0.00</span>	
										</td>
									</tr>
								</tbody>
							</table>
							
						</div>
					</div>
				</div>
				<!-- Generacion vs Recuperacion Dia -->
				<div class="grid-item">
					<div class="panel">
						<div class="panel-header">
							<div class="panel-title">Generacion vs Recuperacion del Dia</div>
							<div class="panel-options">
								<a name="panel-control-option" data-target="GENVREC_DIA" href="javascript:void(0)" title="Ver Log de Datos">
									<i class="bx bx-terminal"></i>
									Log
								</a>
								<a id="EXPORT_GENREC_DIA" class="panel-control-option" href="javascript:void(0)" title="Exportar Datos">
									<i class="bx bxs-download"></i> 
									Exportar
								</a>
							</div>
						</div>
						<div class="panel-datatable">
							<table>
								<tr>
									<td>
										<div class="datalabel-item">
											<span class="datalabel-title">
												Creditos Generados
											</span>
											<div class="datalabel-subtitle" id="dia_generacion_cantidad">
												-- Registros
											</div>
										</div>
									</td>
									<td>
										<div class="datalabel-value" id="dia_generacion_monto">
											S/ -0.00
										</div>
									</td>
								</tr>
								<tr>
									<td>
										<div class="datalabel-item">
											<span class="datalabel-title">
												Creditos Recuperados
											</span>
											<div class="datalabel-subtitle" id="dia_recuperado_cantidad">
												-- Recuperaciones
											</div>
										</div>
									</td>
									<td>
										<div class="datalabel-value" id="dia_recuperado_monto">
										S/ -0.00
										</div>
									</td>
								</tr>							
								<tr>
									<td>
										<div class="datalabel-item">
											<span class="datalabel-title">
												Diferencia
											</span>
											<div class="datalabel-subtitle">
												Generado - Recuperado
											</div>
										</div>
									</td>
									<td>
										<div class="datalabel-value" id="dia_diferencia_monto">
											S/ -0.00
										</div>
									</td>
								</tr>
								<tr>
									<td>
										<div class="datalabel-item">
											<span class="datalabel-title">
												Creditos en Remate
											</span>
											<div class="datalabel-subtitle" id="dia_remate_cantidad">
												--
											</div>
										</div>
									</td>
									<td>
										<div class="datalabel-value" id="dia_remate_monto">
											S/ -0.00
										</div>
									</td>
								</tr>
							</table>
						</div>
					</div>
				</div>
				<!-- Balance Dia -->
				<div class="grid-item">
					<div class="panel h-100 border-indigox">
						<div class="panel-header">
							<div class="panel-title">
								Balance del Dia
								<small class="d-block text-black font-600" style="font-size:.8rem;margin-top:8px;">* La busqueda por filtros no afecta este calculo.</small>
							</div>
							<div class="panel-options">
								<a name="panel-control-option" data-target="BALANCE" href="javascript:void(0)" title="Ver Log de Datos">
									<i class="bx bx-terminal"></i>
									Log
								</a>
								<a id="EXPORT_BALANCE" class="panel-control-option" href="javascript:void(0)" title="Exportar Datos">
									<i class="bx bxs-download"></i> 
									Exportar
								</a>
							</div>
						</div>
						<div class="panel-table">
							<table>
								<tr>
									<td>
										<div class="parr-title font-600">
											<span class="parr-icon bg-leaf"></span>
											Utilidad por Cuota
										</div>
										<!-- <div class="parr-sub">Pagos de Cuotas</div> -->
									</td>
									<td class="text-right">
										<span class="parr-value" id="balance_cuota_monto">S/ 0.00</span>
									</td>
								</tr>
								<tr>
									<td>
										<div class="parr-title font-600">
											
										<span class="parr-icon bg-leaf"></span>
											Utilidad por Mora
										</div>
										<!-- <div class="parr-sub">Pagos de Mora</div> -->
									</td>
									<td class="text-right">
										<span class="parr-value" id="balance_mora_monto">S/ 0.00</span>
									</td>
								</tr>
								<tr>
									<td class="d-flex align-center py-3">
										<div class="parr-title font-600">
											<span class="parr-icon bg-danger"></span>
											Cargos Adicionales
										</div>
										<div class="parr-sub">&nbsp;</div>
									</td>
									<td class="text-right">
										<span class="parr-value" id="balance_adicional">S/ 0.00</span>
										
									</td>
								</tr>
								<tr>
									<td class="d-flex align-center py-3">
										<div class="parr-title font-600">
											<span class="parr-icon bg-danger"></span>
											Descuentos
										</div>
										<div class="parr-sub">&nbsp;</div>
									</td>
									<td class="text-right">
										<span class="parr-value" id="balance_descuento">S/ 0.00</span>
										
									</td>
								</tr>
								<tr>
									<td class="d-flex align-center py-3">
										<div class="parr-title font-600">
										<span class="parr-icon"></span>
											Utilidad Neta
										</div>
										<div class="parr-sub">&nbsp;</div>
									</td>
									<td class="text-indigo text-right">
										<span class="parr-value final-value" id="balance_utilidad_neta">S/ 0.00</span>
									</td>
								</tr>
							</table>
						</div>
					</div>
				</div>

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
	<div id="modal_log" class="modal-wrapper">
		<div id="modal_filter" class="modal">
            <div class="modal-header">
                <div class="modal-title">
					<i class="bx bx-terminal"></i>
					<div id="modal-log-title">Log de Datos</div>
				</div>
                <a href="javascript:void(0)" class="modal-close" name="modal-close" data-target="modal_log">
                    &#10006;
                </a>
            </div>
			<div class="modal-table-container" id="modal-log-dataset">
				<table class="modal-table">
					<thead>
						<tr id="LOG_TABLE_HEADER">
							<!-- <th>Credito</th>
							<th>Pago</th>
							<th>Referencia</th>
							<th>Tipo</th>
							<th>Uso</th>
							<th>Monto</th> -->
						</tr>
					</thead>
					<tbody id="DATASET_LOG"></tbody>
				</table>
			</div>
            <div class="modal-footer text-right">
                <button class="btn" name="modal-close" data-target="modal_log">
                    Cerrar
                </button>
            </div>
        </div>
	</div>
	
	<div id="modal-sucursal" class="modal-wrapper">
		<div id="modal_filter" class="modal" style="width: 500px;">
            <div class="modal-header">
                <div class="modal-title">
					<i class="bx bx-map text-indigo"></i>
					<div id="modal-log-title">Sucursal</div>
				</div>
                <a href="javascript:void(0)" class="modal-close" name="modal-close" data-target="modal-sucursal">
                    &#10006;
                </a>
            </div>
			<div class="modal-body">
				<div id="sucursal-media-list" class="media-item-list">
					<!-- <div class="media-item">
						<div class="media-item-icon">
							<i class='bx bx-compass' ></i>
						</div>
						<div class="media-item-data">
							<div class="media-item-title">SUCURSAL CHIMBOTE</div>
							<div class="media-item-subtitle">Chimbote, Ancash</div>
						</div>
					</div>
					<div class="media-item">
						<div class="media-item-icon">
							<i class='bx bx-compass' ></i>
						</div>
						<div class="media-item-data">
							<div class="media-item-title">SUCURSAL CHIMBOTE</div>
							<div class="media-item-subtitle">Chimbote, Ancash</div>
						</div>
					</div>
					<div class="media-item">
						<div class="media-item-icon">
							<i class='bx bx-compass' ></i>
						</div>
						<div class="media-item-data">
							<div class="media-item-title">SUCURSAL CHIMBOTE</div>
							<div class="media-item-subtitle">Chimbote, Ancash</div>
						</div>
					</div> -->
				</div>
			</div>
            <div class="modal-footer text-right">
                <button class="btn" name="modal-close" data-target="modal-sucursal">
                    Cerrar
                </button>
            </div>
        </div>
	</div>
	<script src="<?= base_url() ?>js_modules/app.js?v=46784"></script>
	<script src="<?= base_url() ?>js_modules/analisis/analisis.index.js?v=27012025"></script>
</body>
</html>
