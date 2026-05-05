<div class="d-flex">
	<div class="col-md-8">
		<div id="creditoreg_sysmssg" style="display: none;"></div>
		<form id="credito_nuevo_form" data-actual="1" data-sig="2" class="card" autocomplete="off">
			<div class="card-header bg-primary text-light">
				<h5 class="card-title">
					<i class="fe fe-file"></i>
					Registro de Credito
				</h5>
				<!-- <div class="card-options">
					<a href="javascript:void(0)" id="btn_test" class="btn btn-secondary">Start Test</a>
				</div> -->
			</div>		
			<div class="card-body">
				<h5 id="view_titulo">Formulario</h5>
				<div id="view_cliente" class="row">
					<div class="table-responsive col-lg-8">
						<div class="row">
							<div class="col-xs-12 col-lg-4 form-group">
								<div class="form-label">Filtro</div>
								<select id="filtro_cliente" class="form-control border-rock">
									<option value="nombre">Nombres</option>
									<option value="documento">Documento</option>
								</select>
							</div>
							<div class="col-xs-12 col-lg-8 form-group">
								<div class="form-label">Buscar</div>
								<input id="cliente_input_buscar" type="text" class="form-control text-uppercase border-rock">
							</div>
						</div>
						<table class="table table-bordered text-nowrap">
							<caption id="caption_cliente"></caption>
							<thead>
								<tr>
									<th>Nombres y Apellidos</th>
									<th>Ciudad</th>
									<th>Documento</th>
									<th class="text-center">Opcion</th>
								</tr>
							</thead>
							<tbody id="view_cliente_table">
								<tr class="text-center">
									<td colspan="4" class="p-4 font-weight-bold text-primary">
										Inicia la busqueda
									</td>
								</tr>
							</tbody>
						</table>
					</div>
					<div class="col-lg-4">
						<div class="card">
							<div class="card-header">
								<div class="card-title text-primary">
									<i class="fe fe-user"></i> Cliente
								</div>
							</div>
							<div id="pcliente_calificacion" class="card-body p-1" style="display: none">
					          	<div class="text-center text-primary" style="font-size: 30px; font-weight: bold;">--</div>
					          	<p class="text-secondary text-center">Calificacion</p>
					        </div>
							<div id="pcliente_blacklist" class="text-light py-2 px-3" style="background: #E92C4C;display: none"></div>
							<div id="pcliente_data" class="card-body">
								<div class="text-center">
									<i class="fe fe-info"></i>
									No Seleccionado
								</div>							
							</div>
						</div>
					</div>
				</div>
				
				<div id="view_prenda" style="display: none;">
					<div class="py-2">
						<a id="btn_prenda_nueva" href="javascript:void(0)" class="btn btn-secondary text-primary font-weight-bold">
							Nueva Prenda <i class="fe fe-plus"></i>
						</a>
						<a id="btn_prenda_recarga" href="javascript:void(0)" class="btn btn-secondary text-primary font-weight-bold">
							Recargar <i class="fe fe-rotate-cw"></i>
						</a>
					</div>
					<div class="table-responsive">
						<table class="table table-bordered table-hover table-outsline ">
	                      	<thead>
		                        <tr>
		                          	<th class="text-nowrap">Descripcion</th>
		                          	<th>Categoria/Tipo</th>
		                          	<th>Datos</th>
		                          	<th>Estado</th>
		                          	<th>Enlace</th>
		                          	<th class="text-center">Seleccion</th>
		                        </tr>
	                      	</thead>
	                      	<tbody id="tdata_prenda">
	                      	</tbody>
	                    </table>
					</div>
				</div>
				
				<div id="view_prestamo" style="display: none;">
					<div class="row">
						<div class="form-group col-xs-12 col-lg-4">
	                        <label class="form-label">Monto a Prestar:</label>
	                        <div class="input-group">
	                          	<span class="input-group-prepend">
	                            	<span class="input-group-text border-rock">S/</span>
	                          	</span>
	                          	<input id="monto" name="monto" type="number" step="any" class="form-control border-rock" placeholder="0.00">
	                        </div>
	                  	</div>
						<!-- <div class="form-group col-xs-12 col-lg-4">
	                        <label class="form-label">Ciclo de Pago:</label>
	                        <div class="input-group">
	                          	<span class="input-group-prepend">
	                            	<span class="input-group-text border-rock">
										<i class="fe fe-calendar"></i>
									</span>
	                          	</span>
								<select name="ciclo" class="form-control border-rock" required>
									<option value="SEM">SEMANAL</option>
									<option value="MEN">MENSUAL</option>
								</select>
	                        </div>
	                  	</div> -->
						<div class="form-group col-xs-12 col-lg-3">
							<label class="form-label">Prorroga</label>
							<div class="input-group">
								<span class="input-group-prepend">
	                            	<span class="input-group-text border-rock">
										<i class="fe fe-clock"></i>
									</span>
	                          	</span>
								<input type="number" class="form-control border-rock" value="7" name="prorroga" id="prorroga">
							</div>
						</div>	                  							
					</div>
					<div class="row">
						<div class="form-group col-xs-12 col-lg-4">
							<label class="form-label">Cargo por almacenamiento</label>
							<label class="custom-switch py-2">
	                          	<input type="checkbox" id="check_cargo" class="custom-switch-input">
	                          	<span class="custom-switch-indicator"></span>
	                          	<span class="custom-switch-description">
	                          		Aplicar
	                          	</span>
	                        </label>
						</div>
						<div class="form-group col-xs-12 col-lg-4">
							<label class="form-label">Cambiar a Pago Mensual</label>
							<label class="custom-switch py-2">
	                          	<input type="checkbox" id="check_ciclo_pago" class="custom-switch-input">
	                          	<span class="custom-switch-indicator"></span>
	                          	<span class="custom-switch-description">
	                          		Aplicar
	                          	</span>
	                        </label>
						</div>
					</div>
				</div>
				<div id="view_registro" style="display: none;">
					<div class="d-flex justify-content-center align-items-center p-4">
						<h4 class="d-block text-primary">Datos Completos.</h4>
					</div>
				</div>
			</div>
			<div id="view_controls" class="card-footer text-right">
				<!-- <a href="javascript:void(0)" class="btn btn-lg btn-secondary">
					<i class="fe fe-arrow-left"></i> Atras
				</a>
				<button type="submit" class="btn btn-lg btn-primary">
					Siguiente 
					<i class="fe fe-arrow-right"></i>
				</button> -->
			</div>
		</form>
	</div>
	<div class="col-md-4">
		<div class="card">
			<div class="card-header">
				<div class="card-title text-primary">Resumen</div>
			</div>
			<div class="card-body p-0">
				<ul id="__resumen_view" class="rs-list">
					<li class="text-center">Sin Data</li>
					<!-- <li>
						<div class="rs-title">Cliente</div>
						<p>
							<b>Nombres y Apellidos:</b>
							<span>Jose Luis Perez</span>
						</p>
						<p>
							<b>Documento:</b>
							<span>70609372</span>
						</p>
						<p>
							<b>Distrito:</b>
							<span>Casma, Ancash</span>
						</p>
					</li> -->
				</ul>
			</div>
			<div id="__resumen_foot" class="card-footer" style="display: none;">
				<a id="link__edit_adv" href="javascript:void(0)" class="btn-link font-weight-bold">
					<i class="fe fe-edit"></i>
					Edicion Avanzada de Credito 
				</a>
			</div>
		</div>
	</div>
</div>


<div id="modal_prenda_registro" class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      	<form id="form_prenda_registro" autocomplete="off" class="modal-content">
          	<div class="modal-header">
            	<h4 class="modal-title">
              		<i class="fe fe-file-plus text-blue"></i>
              		Nueva Prenda
            	</h4>
            	<button type="button" class="close" data-dismiss="modal" aria-label="Close">
              		<span aria-hidden="true"></span>
            	</button>
          	</div>
        	<div  class="modal-body py-2">
          		<div class="row">
            		<div class="col-xs-12 col-md-12">
              			<div class="form-group">
                            <div class="form-label">Tipo de prenda</div>
                            <div id="tprenda_area" class="custom-controls-stacked">
                              Datos no encontrados
                            </div>
                        </div>
            		</div>
          		</div>
          		<div class="row">
            		<div class="col-xs-12 col-md-6">
              			<div class="form-group">
                            <div class="form-label">Categoria</div>
                           	<select id="prenda_categoria" name="categoria" class="form-control text-uppercase" required>
                  				<option value="">- Seleccionar -</option>
                			</select>
                        </div>
	           		</div>
            		<div class="col-xs-12 col">
              			<div class="form-group">
                            <div class="form-label">Opciones</div>
                         	<a href="<?= base_url() ?>Categoria" class="btn btn-outline-primary" target="_blank">
                              	Nueva Categoria
                              	<i class="fe fe-flag"></i>
                            </a>
                        </div>
            		</div>
		            <div class="col-xs-12 col-md-12">
		              	<div class="form-group">
		                	<div class="form-label">Descripcion:</div>
		                	<input type="text" class="form-control border-rock text-uppercase" name="descripcion" required>
		                	<span id="descripcion_feed" class="text-danger"></span>
		              	</div>
		            </div>
          		</div>
	          	<div id="FormArea" class="row" style="display: none;">
	          		
	          	</div>
        	</div>
            <div class="modal-footer text-right">
            	<button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            	<button type="submit" class="btn btn-primary">
            		<i class="fe fe-save"></i>
            		Guardar
            	</button>
            </div>
      	</form>
    </div>
</div>

<!-- <div id="electro_form" class="row" style="display: none;">
	<div class="col-md-6 form-group">
		<div class="form-label">Serie:</div>
		<input type="text" class="form-control border-rock" name="serie">
		<span id="serie_feed" class="text-danger"></span>
	</div>
	<div class="col-md-6 form-group">
		<div class="form-label">Marca:</div>
		<input type="text" class="form-control border-rock text-uppercase" name="marca">
		<span id="marca_feed" class="text-danger"></span>
	</div>
	<div class="col-md-6 form-group">
		<div class="form-label">Modelo:</div>
		<input type="text" class="form-control border-rock text-uppercase" name="modelo">
		<span id="modelo_feed" class="text-danger"></span>
	</div>
	<div class="col-md-6 form-group">
		<div class="form-label">Estado:</div>
		<input type="number" step="any" class="form-control border-rock" name="valoracion" value="5">
	</div>
	<div class="col-md-12 form-group">
		<div class="form-label">Observaciones:</div>
		<textarea name="observacion" class="form-control border-rock text-uppercase" cols="3" rows="3"></textarea>
	</div>
	<div class="col-md-12 form-group">
		<div class="form-label">Comentarios:</div>
		<textarea name="comentario" class="form-control border-rock text-uppercase" cols="3" rows="3"></textarea>
	</div>
</div>
<div id="joya_form" class="row" style="display: none;">
	<div class="col-md-6 form-group">
		<div class="form-label">Quilate:</div>
		<input type="text" class="form-control border-rock text-uppercase" name="quilate">
		<span id="quilate_feed" class="text-danger"></span>
	</div>
	<div class="col-md-6 form-group">
		<div class="form-label">Peso:</div>
		<input type="text" class="form-control border-rock" name="peso">
		<span id="peso_feed" class="text-danger"></span>
	</div>
	<div class="col-md-6 form-group">
		<div class="form-label">Estado:</div>
		<input type="number" step="any" class="form-control border-rock" name="estado" value="5">
	</div>
	<div class="col-md-12 form-group">
		<div class="form-label">Comentarios:</div>
		<textarea name="comentario" class="form-control border-rock text-uppercase" cols="3" rows="3"></textarea>
	</div>
</div>
<div id="vehiculo_form" class="row" style="display: none;">
	<div class="col-md-6 form-group">
		<div class="form-label">Marca:</div>
		<input type="text" class="form-control border-rock text-uppercase" name="marca">
	</div>
	<div class="col-md-6 form-group">
		<div class="form-label">Modelo:</div>
		<input type="text" class="form-control border-rock text-uppercase" name="modelo">
	</div>
	<div class="col-md-6 form-group">
		<div class="form-label">A���o:</div>
		<input type="text" class="form-control border-rock" name="anio">
	</div>
	<div class="col-md-6 form-group">
		<div class="form-label">Placa:</div>
		<input type="text" class="form-control border-rock text-uppercase" name="placa">
		<span id="placa_feed" class="text-danger"></span>
	</div>
	<div class="col-md-6 form-group">
		<div class="form-label">Kilometraje:</div>
		<input type="text" class="form-control border-rock text-uppercase" name="kilometraje">
		<span id="kilometraje_feed" class="text-danger"></span>
	</div>
	<div class="col-md-6 form-group">
		<div class="form-label">Estado:</div>
		<input type="number" step="any" class="form-control border-rock" name="estado" value="5">
	</div>
	<div class="col-md-12 form-group">
		<div class="form-label">Observaciones:</div>
		<textarea name="observacion" class="form-control border-rock text-uppercase" cols="3" rows="3"></textarea>
	</div>
	<div class="col-md-12 form-group">
		<div class="form-label">Comentarios:</div>
		<textarea name="comentario" class="form-control border-rock text-uppercase" cols="3" rows="3"></textarea>
	</div>
</div> -->

<style>
	.rs-list {
		list-style: none;
		padding: 0;
		margin: 0;
	}
	.rs-list li {
		padding: .8em .7em;
		display: block;
		border-left: 3px solid #467fcf;
		margin: 4px 0;
	}
	.rs-list li p {
		margin: 0;
		margin-top: 2px;
		margin-bottom: 4px;
	}
	.rs-title {
		color: #467fcf;
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

<script src="<?= base_url() ?>js_modules/credito/credito.registro5.js"></script>
