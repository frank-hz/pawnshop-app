<!-- <script src="https://cdn.tailwindcss.com"></script> -->

<!-- <script src="https://unpkg.com/react@18/umd/react.development.js" crossorigin></script>
<script src="https://unpkg.com/react-dom@18/umd/react-dom.development.js" crossorigin></script> -->

<!-- <script src="https://unpkg.com/react/umd/react.development.js" crossorigin></script>
<script src="https://unpkg.com/react-dom/umd/react-dom.development.js" crossorigin></script>
<script src="https://unpkg.com/babel-standalone@6/babel.min.js"></script> -->


<div class="p-1">
    <div class="card">
        <!-- <input id="_inputID" type="text"> -->
        <div class="card-header">
            <h4 class="card-title font-weight-bold px-1">
				<a href="<?= base_url()?>Credito/detalle/<?= $credito->id; ?>">
					<span class="text-blue">#<?php echo $credito->serie; ?></span>
				</a>
				| REGULARIZACION
			</h4>
        </div>
        <script>
			window.IDE = <?= $credito->id; ?>;
            window.cuotas = <?php echo json_encode($cuotas); ?>;
        </script>
        <div class="card-body p-3">
            <div class="row">
                <div class="col-xs-12 col-md-12 col-lg-7">
                    <div class="card">
                        <div class="card-header">
                            <div class="label-red-600">
                                Cuotas: <span id="pendingLabelTotal">0</span>
                            </div>
                        </div>
                        <div class="row px-3 pt-2">
							<div class="col-xs-12 col-md-4 form-group">
								<div class="form-label">Estilo de tabla</div>
		                        <label class="custom-switch">
		                          	<input type="checkbox" id="check_darkmode" class="custom-switch-input">
		                          	<span class="custom-switch-indicator"></span>
		                          	<span class="custom-switch-description">
		                          		Modo Oscuro
		                          	</span>
		                        </label>
							</div>
							<?php if (!empty($cuotas)): ?>
							<div class="col-xs-12 col-md-4 form-group">
		                        <div class="form-label">Seleccionar todo</div>
		                        <div>
		                          	<label class="custom-control custom-checkbox custom-control-inline">
		                            	<input type="checkbox" class="custom-control-input" id="check_seleccion_total">
		                            	<span class="custom-control-label">Marcar</span>
		                          	</label>
		                        </div>
	                      	</div>							
							<?php endif ?>
						</div>
                        <div class="table-responsive" style="max-height: 600px">
							<table id="tabla_pendiente" class="table table-sm table-bordered">
								<thead>
									<tr class="text-center">
										<th class="text-center">#</th>
										<th>Cuota</th>
										<th>Vencimiento</th>
										<th>Monto</th>
										<th>Estado</th>
										<th class="font-weight-bold text-center text-indigo">PAGAR</th>
									</tr>
								</thead>
								<tbody id="TABLE_PENDING"></tbody>
							</table>
						</div>

                        
                    </div>
                </div>
                <div class="col-xs-12 col-md-12 col-lg-5">
					<div class="card mb-1">
						<div class="card-header">
							<ul class="nav nav-pills px-0">
								<li class="nav-item">
									<a class="nav-link p-2 active" href="#" data-toggle="tab" data-target="#CARD_INFO">
										<i class="fe fe-credit-card"></i> &nbsp;
										Informacion del Pago
									</a>
								</li>
								<li class="nav-item">
									<a class="nav-link p-2" href="#" data-toggle="tab" data-target="#CARD_ADVANCED">
										<i class="fe fe-chevrons-right"></i> 
										Avanzado
									</a>
								</li>
							</ul>
						</div>
						<div class="total_card card-border-bottom">
							<h3 class="total_value">S/ 0.00</h3>
							<div class="total_subtitle text-blue2">Pago Total</div>
							<div class="">
							</div>	
						</div>
						<div class="py-2 px-3 d-flex justify-center">
							<a href="">
								LAURA PEÑA CORDOVA
							</a>
							&nbsp; - &nbsp;
							<a href="" class="btn-link">LAPTOP ACER IDEAPAD CORE I3</a>
						</div>

					</div>
					<div class="tab-content">
						<div id="CARD_INFO" class="card mb-1 tab-pane fade show active" role="tabpanel">
							<table class="table card-table table-sm card-border-bottom">
								<tbody>
									<tr>
										<td width="1">
											<i class="fa fa-plus-circle text-success"></i>
										</td>
										<td colspan="1">Cuotas</td>
										<td id="total_cuotas_detalle">
											<?php 
												$cant = count($cuotas);
												$str = ($cant <= 9) ? '0'.$cant : $cant; 
												echo $str." cuotas";
											?>
										</td>
										<td class="text-center">
											<span id="recibo_cuota" class="text-dark">S/ 0.00</span>
										</td>
									</tr>
									<tr>
										<td width="1">
											<i class="fa fa-plus-circle text-success"></i>
										</td>
										<td colspan="2">Mora</td>
										<td class="text-center">
											<span id="recibo_mora" class="text-dark">S/ 0.00</span>
										</td>
									</tr>
									<tr>
										<td width="1">
											<i class="fa fa-plus-circle text-primary"></i>
										</td>
										<td colspan="2">Cargos Adicionales</td>
										<td class="text-center">
											<span id="recibo_adicional" class="text-primary" style="font-weight: 600;">S/ 0.00</span>
										</td>
									</tr>
									<tr>
										<td width="1">
											<i class="fa fa-minus-circle text-danger"></i>
										</td>
										<td colspan="2">Descuento</td>
										<td class="text-center">
											<span id="recibo_descuento" class="text-danger" style="font-weight: 600;">S/ 0.00</span>
										</td>
									</tr>
									<tr>
										<td width="1">
											<i class="fa fa-plus-circle text-success"></i>
										</td>
										<td colspan="1">Mora Pospuesta</td>
										<td>
										<label class="custom-switch">
											<input type="checkbox" name="custom-switch-checkbox" class="custom-switch-input" checked>
											<span class="custom-switch-indicator"></span>
											<!-- <span class="custom-switch-description">Cobrar</span> -->
										</label>
										</td>
										<td class="text-center">
											<span id="recibo_descuento" class="text-danger" style="font-weight: 600;">S/ 0.00</span>
										</td>
									</tr>

								</tbody>
							</table>
						</div>
						<div id="CARD_ADVANCED" class="card mb-1 tab-pane fade" role="tabpanel">
							<div class="card-body">
								<button class="btn btn-blue btn-sm">
									<i class="fe fe-plus"></i> 
									Agregar 
								</button>
							</div>
							<div class="card-table">
								<table class="table table-sm">
									<thead>
										<th>Canal</th>
										<th>Nro Operacion</th>
										<th>Monto</th>
										<th>Accion</th>
									</thead>
									<tbody>
										<tr>
											<td>Efectivo</td>
											<td></td>
											<td>S/ 50.00</td>
											<td>
												<button class="btn btn-sm">Remove</button>
											</td>
										</tr>
										<tr>
											<td>YAPE</td>
											<td>OPE00454774</td>
											<td>S/ 23.50</td>
											<td>
												<button class="btn btn-sm">Remove</button>
											</td>
										</tr>
									</tbody>
								</table>
							</div>
						</div>	
					</div>
					
					<div class="card">
						<div class="card-body">
							<div class="row">
								<div class="col-xs-12 col-md-6 form-group">
									<label class="form-label">Descuento/Penalidad</label>
									<label class="custom-switch">
										<input type="checkbox" id="check_custom" class="custom-switch-input">
										<span class="custom-switch-indicator"></span>
										<span class="custom-switch-description">
											Aplicar
										</span>
									</label>
								</div>
								<div class="col-xs-12 col-md-6 form-group">
									<label class="form-label">Forma de Pago</label>
									<div class="custom-controls-stacked">
										<label class="custom-control custom-radio custom-control-inline">
											<input type="radio" class="custom-control-input" name="forma_pago" value="TIEN" required checked>
											<span class="custom-control-label">Tienda</span>
										</label>
										<label class="custom-control custom-radio custom-control-inline">
											<input type="radio" class="custom-control-input" name="forma_pago" value="DEP" required>
											<span class="custom-control-label">Deposito</span>
										</label>
										<label class="custom-control custom-radio custom-control-inline">
											<input type="radio" class="custom-control-input" name="forma_pago" value="TRANF" required>
											<span class="custom-control-label">Transferencia</span>
										</label>
									</div>
								</div>

							</div>
							<div class="row">
								<div id="section_descuento" class="col-xs-12 col-md-6 col-lg-6 form-group" style="display: none;">
									<label class="form-label">Descuento</label>
									<div class="input-group">
										<span class="input-group-prepend icon-red">
											<span class="input-group-text">- S/</span>
										</span>
										<input type="number" class="form-control border-rock" step="any" id="input_descuento" name="descuento">
									</div>
									<small id="descuento_feed" class="text-red"></small>
								</div>
								<div id="section_adicional" class="col-xs-12 col-md-6 col-lg-6 form-group" style="display: none;">
									<label class="form-label">Penalidad/Mantenimiento</label>
									<div class="input-group">
										<span class="input-group-prepend icon-green">
											<span class="input-group-text" style="font-weight: 600;">+ S/</span>
										</span>
										<input type="number" class="form-control border-rock" step="any" id="input_adicional" name="adicional">
									</div>
									<small id="adicional_feed" class="text-red"></small>
								</div>
							</div>
							<div class="row" id="forma_operacion" style="display: none;">
								<div class="col-xs-12 col-md-6 form-group">
									<label class="form-label">Nro de Operacion</label>
									<input type="text" class="form-control border-rock" name="comprobante">
									<small id="_noperacion_feed" class="text-danger"></small>
								</div>
								<div class="col-xs-12 col-md-6">
									<div class="form-group">
										<label class="form-label">Banco (Seleccionar o ingresar)</label>
										<select id="banco_list" name="banco" class="form-control border-rock">
											<?php if (!empty($banco)): ?>
												<option value="">- Seleccionar -</option>
												<?php foreach ($banco as $b): ?>
													<option value="<?= $b->ban_codigo; ?>">
														<?= $b->ban_nombre; ?>
													</option>
												<?php endforeach ?>
											<?php endif ?>
										</select>
										<small id="_banco_feed" class="text-danger"></small>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-xs-12 col-md-6 form-group">
									<label class="form-label">Monto Recibido</label>
									<div class="input-group">
										<span class="input-group-prepend" id="basic-addon1">
											<span class="input-group-text">S/</span>
										</span>
										<input type="number" class="form-control border-rock" step="any" id="input_recibido" aria-label="Username" aria-describedby="basic-addon1">
									</div>
									<small id="monto_feed" class="text-red"></small>
								</div>
								<div class="col-xs-12 col-md-6 form-group">
									<label class="form-label">Vuelto</label>
									<input id="input_devuelto" name="vuelto" type="number" step="any" class="form-control border-rock" disabled>
								</div>
								<div id="section_comentario" class="col-xs-12 col-md-12 form-group" style="display: none;">
									<div class="form-label">Comentarios</div>
									<textarea id="pago_comentario" cols="10" rows="2" class="form-control border-rock" maxlength="300"></textarea>
								</div>
							</div>
							<div class="card-footer text-left">
								<?php if (!empty($cuotas)): ?>
									<button name="btn_envio" class="btn btn-dark btn-lg">
										Procesar Pago 
										<i class="fe fe-send"></i> 
									</button>
								<?php else: ?>
									<b>No se encontro nada que pagar</b>
								<?php endif ?>
							</div>
						</div>


					</div>

                </div>
            </div>
        </div>
    </div>
</div>



<style>
	.nav-link {
		display: inline-flex;
		align-items: center;
	}
	.button-collapse {
		outline: none;
		border: none;
		text-decoration: none;
		background: transparent;
		padding: .4rem .6rem;
		font-size: .9rem;
		font-weight: 600;
		/* letter-spacing: .5px; */
		vertical-align: center;
		color: #0a1440;
		cursor: pointer;
		display: inline-flex;
		align-items: center;
		transition: all .2s;
	}
	.button-collapse:hover {
		opacity: .9;
	}
	.button-collapse:focus {
		outline: none;
		border-left: 1px solid  #0a1440;
	}
	.button-collapse .fe {
		margin-right: 5px;
	}
	.total_card {
		display: flex;
		flex-direction: column;
		justify-content: center;
		align-items: center;
		gap: 0;
		padding: .5rem 0;
	}
	.total_value{
		font-size: 1.6rem;
		line-height: 2rem;
		font-weight: 800;
		color: #0a1440;
		letter-spacing: .5px;
		display: block; 
		padding: 0;
		margin: 0;
		padding: .2rem 0;
	}
	.total_subtitle {
		display: block;
		font-weight: 800;
		font-size: 1rem;
	}
    .label-red-600 {
        display: flex;
        align-items:center;
        gap: 0 5px;
        font-size: 1.2rem;
        font-weight: 600;
        color: #101521;
    }
    .label-red-600 span {
        color: red;
        font-size: 1.3rem;

    }
	.card-border-bottom {
		border-bottom: 1px solid rgba(0, 40, 100, 0.16);
	}
</style>
<script src="<?= base_url() ?>js_modules/pago/regularizacion.js?v=1"></script>