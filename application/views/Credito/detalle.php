<?php ini_set('display_errors', 0); ?>
<input id="ident" type="hidden" value="<?= $credito->id; ?>">
<script>
	window.credito_id = <?php echo json_encode($credito->id); ?>;
</script>
<div class="px-5 py-3">
	<div class="row">
		<div class="col-xs-12 col-md-3">
			<h4 class="page-title mb-1 text-primary">
				<span class="font-weight-bold"><?= $credito->serie; ?></span>
			</h4>
			<p class="px-1 py-0 text-danger">
				Cuotas Atrasadas: <b><?php echo $credito->cuotas_atraso;  ?></b>
			</p>  
			<div class="mt-2 mb-4">
				<?php if ($credito->estado == 'A' OR $credito->estado == 'R'): ?>
					<div class="d-flex p-1">
						<span class='text-dark font-weight-bold'>
							Liquidado: 
							<?php 
								echo $credito->liquidacion_fecha_raw. " " .$credito->liquidacion_hora_raw;
							?> 
						</span>
					</div>
				<?php endif ?>
				<?php echo $credito->estado_tag; ?>
			</div>
			<div>
				<div class="list-group list-group-transparent mb-0">
					<div class="list-group-item list-group-item-action d-flex align-items-center active">
						Opciones
					</div>
					<!-- <a href="javascript:void(0)" id="btn_revision" class="list-group-item list-group-item-action d-flex align-items-center text-primary" data-id="<?= $credito->id; ?>">
						<span class="icon mr-3 "><i class="fe fe-search text-primary"></i></span>
						Aplicar Revision
					</a> -->
					<!-- <a id="btn_revision" href="<?= base_url() ?>Credito/movimientos/<?= $credito->id; ?>" href="javascript:void(0)" class="list-group-item list-group-item-action d-flex align-items-center text-primary">
						<span class="icon mr-3"><i class="fe fe-shuffle text-primary"></i></span>
						Ver movimientos
					</a> -->
					<a href="<?= base_url() ?>credito/anexo/<?= $credito->id; ?>" class="list-group-item list-group-item-action d-flex align-items-center text-primary" target="_blank">
						<span class="icon mr-3"><i class="fe fe-alert-circle text-primary"></i></span>
						Contrato
					</a>					
					
					<?php if ($credito->estado === 'E'): ?>
					<a href="<?= base_url() ?>Credito/regularizar/<?= $credito->id; ?>" class="list-group-item list-group-item-action d-flex align-items-center text-primary font-weight-bold">
						<span class="icon mr-3"><i class="fe fe-sliders text-primary font-weight-bold"></i></span>
						Regularizar
					</a>
					<?php endif ?>
	
					<?php if ($credito->estado === 'E' OR $credito->estado === 'CC'): ?>
					<a href="<?= base_url() ?>Credito/recuperar/<?= $credito->id; ?>" class="list-group-item list-group-item-action d-flex align-items-center text-primary font-weight-bold">
						<span class="icon mr-3"><i class="fe fe-send text-primary font-weight-bold"></i></span>
						Recuperar
					</a>
					<?php endif ?>
	
					<?php if ($credito->estado === 'E'): ?>
					<a href="<?= base_url() ?>Credito/adelanto/<?= $credito->id; ?>" class="list-group-item list-group-item-action d-flex align-items-center text-primary">
						<span class="icon mr-3"><i class="fe fe-briefcase text-primary"></i></span>
						Adelanto
					</a>
					<?php endif ?>
	
					<?php if ($credito->estado === 'E' OR $credito->estado === 'CC'): ?>
					<a href="<?= base_url() ?>Credito/amortizar/<?= $credito->id; ?>" class="list-group-item list-group-item-action d-flex align-items-center text-primary">
						<span class="icon mr-3"><i class="fe fe-plus-square text-primary"></i></span>
						Amortizar
					</a>
					<a id="btn_remate" v-on:click="rematar_credito" href="javascript:void(0)" data-id="<?= $credito->id; ?>" class="list-group-item list-group-item-action d-flex align-items-center text-primary">
						<span class="icon mr-3"><i class="fe fe-briefcase text-primary"></i></span>
						Rematar
					</a>
					<?php endif ?>
	
					<?php if ($credito->estado === 'E'): ?>
					<a id="btn_ccongela" href="javascript:void(0)" :data-id="<?= $credito->id; ?>" class="list-group-item list-group-item-action d-flex align-items-center text-primary">
						<span class="icon mr-3"><i class="fe fe-stop-circle text-primary"></i></span>
						Congelar
					</a>
					<!-- <a href="<?= base_url() ?>Credito/congelamiento/<?= $credito->id ?>" class="list-group-item list-group-item-action d-flex align-items-center text-primary">
						<span class="icon mr-3"><i class="fe fe-stop-circle text-primary"></i></span>
						Congelar
					</a> -->
						<?php if ($credito->aplica_penalidad == 'I'): ?>
							<a id="btn_penalidad" href="javascript:void(0)" v-on:click="modificar_cobro_mora" data-id="<?= $credito->id; ?>" data-estado="A" class="list-group-item list-group-item-action d-flex align-items-center text-primary">
								<span class="icon mr-3"><i class="fe fe-check text-primary"></i></span>
								Activar cobro de mora
							</a>
						<?php elseif ($credito->aplica_penalidad == 'A'): ?>
							<a id="btn_penalidad" href="javascript:void(0)" v-on:click="modificar_cobro_mora" data-id="<?= $credito->id; ?>" data-estado="I" class="list-group-item list-group-item-action d-flex align-items-center text-danger">
								<span class="icon mr-3"><i class="fe fe-x text-danger"></i></span>
								Desactivar cobro de mora
							</a>
						<?php endif ?>            	
					<?php endif ?>
					
	
					<?php if ($delete_action): ?>
						<a id="btn_delete" href="javascript:void(0)" data-id="<?= $credito->id; ?>" class="list-group-item list-group-item-action bg-scarlet text-light py-2">
							<span class="icon mr-3"><i class="fe fe-trash text-light"></i></span>
							Eliminar
						</a>
					<?php endif ?>
	
					<?php if ($credito->estado === 'E'): ?>
						<!-- <a id="btn_revision" href="<?= base_url() ?>Credito/moras/<?= $credito->id; ?>" class="list-group-item list-group-item-action d-flex align-items-center text-primary">
							<span class="icon mr-3"><i class="fe fe-info text-primary"></i></span>
							Administrar mora
						</a>
						<a id="btn_revision" href="<?= base_url() ?>Credito/editar/<?= $credito->id; ?>" class="list-group-item list-group-item-action d-flex align-items-center text-primary">
							<span class="icon mr-3"><i class="fe fe-code text-primary"></i></span>
							Editar
						</a> -->
						<a href="javascript:void(0)" id="btn_correo_open" class="list-group-item list-group-item-action d-flex align-items-center text-primary" data-id="<?= $credito->id; ?>">
							<span class="icon mr-3 "><i class="fe fe-mail text-primary"></i></span>
							Notificacion por Correo
						</a>
					<?php endif ?>
					<?php if ($this->session->userdata('dev')): ?>
					<a id="btn_restringir" href="javascript:void(0)" :data-id="<?= $credito->id; ?>" class="list-group-item list-group-item-action d-flex align-items-center text-primary">
						<span class="icon mr-3"><i class="fe fe-slash text-primary"></i></span>
						Restrigir
					</a>
					<?php endif; ?>
					<div class="mt-2">
						<div class="form-label mb-1">Empleado Registrador</div>
						<span><?= $credito->empleado_nombre; ?></span>
						<!-- <a href="<?= $credito->empleado_url; ?>" class="list-group-item list-group-item-action d-flex align-items-center text-primary px-0 pt-0">
							<?= $credito->empleado_nombre; ?>
						</a> -->
					</div>
					
				</div>	          	
			</div>
		</div>
		<div class="col-xs-12 col-md-9" style="max-height: 700px; overflow-y: auto;">
			<div class="card mb-2 border-fx-1">
				<div class="card-header" style="border-bottom:none;">
				<div class="card-title text-sky font-weight-bold">
					<i class="fe fe-clipboard"></i> Notas 
					<div id="notas-loader" class="spinner-v1-mini d-none"></div>
				</div>
				<div class="card-options">
					<button id="btn-nota-new" class="enc2454-btn-option enc2454-btn-option-blue" data-idcliente="<?= $cliente->id; ?>">
						<i class="fe fe-plus"></i>
						Nueva Nota 
					</button>
				</div>
				</div>
				<div class="p-0" style="max-height: 350px; overflow: auto;">
				<table class="card-table table table-leverage-space">
					<thead>
						<th>Descripcion</th>
						<th>Fecha</th>
						<th>Empleado</th>
						<th class="text-center w-10">Accion</th>
					</thead>
					<tbody id="dataset_notas">
						<tr class="text-center">
							<td colspan="4">Sin resultados</td>
						</tr>
					</tbody>
				</table>
				</div> 
			</div> 
			<div class="card mb-1 border-fx-1">
				<div class="card-header">
					<div class="card-title text-sky font-weight-bold">
						<i class="fe fe-layers text-sky"></i>
						Datos Generales
					</div>
					<div class="card-options">
						<small class="font-weight-bold text-indigo">Detalle de Credito</small>
					</div>
				</div>
				<div class="card-body">
					<div class="row">
						<!-- <div class="col-xs-12 col-lg-12 form-group">
							<div class="form-label">Empleado Registrador</div>
							<a href="<?= $credito->empleado_url; ?>" class="btn-link">
								<?= $credito->empleado_nombre; ?>
							</a>
						</div> -->
						<div class="col-xs-12 col-lg-4 form-group">
							<div class="form-label">Fecha de Registro</div>
							<span><?= $credito->fecha_creacion; ?></span>
						</div>
						<div class="col-xs-12 col-lg-4 form-group">
							<div class="form-label">Tipo de Tasa</div>
							<span><?= $credito->tipo_tasa_display; ?></span>
						</div>
						<div class="col-xs-12 col-lg-4 form-group">
							<div class="form-label">Tasa</div>
							<span><?= $credito->tasa_display; ?></span>
						</div>            
						<div class="col-xs-12 col-lg-4 form-group">
							<div class="form-label">Monto Prestado</div>
							<span>S/ <?= $credito->monto; ?></span>
						</div>            
						<div class="col-xs-12 col-lg-4 form-group">
							<div class="form-label">
								Pago <span><?php echo $credito->ciclo_pago_display; ?></span>
							</div>
							<span>S/ <?= $credito->pago; ?></span>
						</div>
						<div class="col-xs-12 col-lg-4 form-group">
							<div class="form-label">Penalidad por Mora</div>
							<span>S/ <?= $credito->penalidad; ?></span>
						</div>
						<div class="col-xs-12 col-lg-4 form-group">
							<div class="form-label">Prorroga</div>
							<span><?= $credito->prorroga; ?> dias</span>
						</div>
						<div class="col-xs-12 col-lg-4 form-group">
							<div class="form-label">Amortizacion Actual</div>
							<span>S/ <?= $credito->amortizacion; ?></span>
						</div>
						<!-- <div class="col-xs-12 col-lg-4 form-group">
							<div class="form-label">Adelanto Actual</div>
							<span><?= $credito->adelanto; ?></span>
						</div> -->
						<div class="col-xs-12 col-lg-4 form-group">
							<div class="form-label">Aplicacion de Mora</div>
							<?php 
								if ($credito->aplica_penalidad) {
									echo "<span class='text-blue'>Activo</span>";
								}else {
									echo "<span class='text-red'>Desactivado</span>";
								}
							?>
						</div>
						<div class="col-xs-12 col-lg-4 form-group">
							<div class="form-label">Ciclo de Pago</div>
							<span><?= $credito->ciclo_pago_display; ?></span>
						</div>
						<div class="col-xs-12 col-lg-4 form-group">
							<div class="form-label">Proximo Pago</div>
							<span><?= $credito->pago_proximo; ?></span>
						</div>
						<?php if (in_array($credito->estado,['R','A','CC'])): ?>
							<div class="col-xs-12 col-md-4 form-group">
								<label for="" class="font-weight-bold form-label">Fecha de Liquidacion</label>
								<span class="text-success font-weight-bold">
									<?php echo $credito->fecha_liquidacion; ?>
								</span>
							</div>
						<?php endif ?>


					</div>
				</div>
				<div class="card-body">
					<div class="row">
						<div class="col-xs-12 col-md-4 form-group">
							<div class="form-label">Cliente</div>
							<a href="<?= $cliente->url; ?>"><?= $cliente->nombre_full; ?></a>
						</div>						
						<div class="col-xs-12 col-md-4 form-group">
							<div class="form-label">Documento</div>
							<span>
								<?= $cliente->documento." <b class='text-indigo'>[$cliente->tipo_documento]</b>"; ?>
							</span>
						</div>
						<div class="col-xs-12 col-md-4 form-group">
							<div class="form-label">Direccion</div>
							<span>
								<?= $cliente->direccion.", ".$cliente->ciudad; ?>
							</span>
						</div>
						<div class="col-xs-12 col-md-4 form-group">
							<div class="form-label">Telefono</div>
							<span>
								<?= $cliente->telefono; ?>
							</span>
						</div>
						<div class="col-xs-12 col-md-4 form-group">
							<div class="form-label">Email</div>
							<span>
								<?= !empty($cliente->email) ? $cliente->email : '[Sin Datos]'; ?>
							</span>
						</div>
						<div class="col-xs-12 col-md-4 form-group">
							<div class="form-label">Puntuacion</div>
							<div class="enc1217-conducta-sm-tag enc1212-conducta-tag-<?= $cliente->puntuacion; ?>">
								<?php echo ($cliente->puntuacion) ? $cliente->puntuacion : "-"; ?>
							</div>
						</div>
						<?php if ($cliente->bloqueo): ?> 
						<div class="col-xs-12 col-md-8 form-group">
							<div class="form-label">Lista Negra</div>
							<span class="font-weight-bold encode-text-danger">
								<?= $cliente->bloqueo_motivo; ?>
							</span>
						</div>
						<?php endif; ?>

					</div>
				</div>
				<div class="card-body">
				<?php if ($prenda): ?>
					<?php echo $prenda_data_string; ?>
				<?php else: ?>
					<div class="text-center">Datos de prendas no encontrados.</div>
				<?php endif; ?> 
				</div>
			</div>
			<div class="card mb-2 border-fx-1">
				<div class="card-header" style="border-bottom:none;">
					<div class="card-title text-sky font-weight-bold">
						<i class="fe fe-calendar"></i> Cuotas 
						<div id="cuotas-loader" class="spinner-v1-mini d-none"></div>
					</div>
					<div class="card-options">
						<button id="btn-cuota-reload" class="enc2454-btn-option enc2454-btn-option-blue">
							Recarga
						</button>
					</div>
				</div>
				<div class="p-0" style="max-height: 300px; overflow-y: auto;">
					<table class="card-table table text-nowrap">
						<thead>
							<tr>
								<th>Nº Orden</th>
								<th>Vencimiento</th>
								<th>Monto</th>
								<!-- <th>Dias de Vencimiento</th>
								<th>Mora</th> -->
								<th>Estado</th>
							</tr>
						</thead>
						<tbody id="dataset_cuotas">
							<tr>
								<td colspan="6" class="text-center">Sin Datos</td>
							</tr>
						</tbody>
					</table>
				</div> 
			</div>
			<div class="card mb-2 border-fx-1">
				<div class="card-header" style="border-bottom:none;">
					<div class="card-title text-sky font-weight-bold">
						<i class="fe fe-credit-card"></i> Pagos 
						<div id="pagos-loader" class="spinner-v1-mini d-none"></div>
					</div>
					<div class="card-options">
						<button id="btn-pago-reload" class="enc2454-btn-option enc2454-btn-option-blue">
							Recarga
						</button>
					</div>
				</div>
				<div class="p-0" style="max-height: 300px; overflow-y: auto;">
					<table class="card-table table text-nowrap">
						<thead>
							<tr>
								<th>Serie</th>
								<th>Fecha</th>
								<th>Forma</th>
								<th>Concepto</th>
								<th>Total</th>
								<th>Enlace</th>
							</tr>
						</thead>
						<tbody id="dataset_pagos">
							<tr>
								<td colspan="6" class="text-center">Sin Datos</td>
							</tr>
						</tbody>
					</table>
				</div> 
			</div>
		</div>
	</div>

</div>

<!-- Modals -->
<div id="modal-nota" class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h4 id="modal-nota-title" class="modal-title">
					<i class="fe fe-file-plus text-blue"></i>
					<span>Nueva Nota</span>
				</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true" class="text-primary"></span>
				</button>
			</div>
			<form id="nota-form" autocomplete="off" autocorrect="off" data-type="NEW">
				<input type="hidden" name="idcode">
				<div class="modal-body">
					<div class="row">
						<div class="col-xs-12 col-md-12">
							<div class="form-group">
								<label for="" class="font-weight-bold">Descripcion</label>
								<textarea name="descripcion" cols="10" rows="5" class="form-control"></textarea>
							</div>
						</div>
						<div class="col-xs-12 col-md-4">
							<div class="form-group">
								<div class="form-label">Tipo</div>
								<select name="anclado" class="form-control border-rock">
									<option value="NO">NOTA NORMAL</option>
									<option value="SI">NOTA ANCLADA</option>
								</select>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer text-right">
					<button type="submit" class="btn btn-blue">
						<i class="fe fe-save"></i> Guardar
					</button>
					<button class="btn btn-secondary" data-dismiss="modal">
						Salir
					</button>
				</div>
			</form>
		</div>
	</div>
</div>

<div id="MODAL_CORREO" class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h4 id="modal-nota-title" class="modal-title">
					<i class="fe fe-mail text-blue"></i>
					<span>Enviar Correo Informativo</span>
				</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true" class="text-primary"></span>
				</button>
			</div>
			
			<form id="FORM_ENVIO_CORREO" autocomplete="off" autocorrect="off" class="card-body mt-2 pt-2">
				<div class="form-group">
					<label class="form-label">Tipo:</label>
					<div class="selectgroup w-100" id="TIPO_EMAIL">
						<!-- <label class="selectgroup-item">
							<input type="radio" name="tipo_notificacion" value="" class="selectgroup-input" checked="">
                            <span class="selectgroup-button">48Horas</span>
						</label>
						<label class="selectgroup-item">
                            <input type="radio" name="tipo_notificacion" value="" class="selectgroup-input">
                            <span class="selectgroup-button">Recordatorio</span>
						</label>
						<label class="selectgroup-item">
                            <input type="radio" name="tipo_notificacion" value="" class="selectgroup-input">
                            <span class="selectgroup-button">Recordatorio</span>
						</label> -->
						<div class="d-flex">
							<p class="text-center">SIN DATOS</p>
						</div>
					</div>
				</div>
				<div class="form-group">
					<div class="row align-items-center">
						<label class="col-sm-2">Destinatario:</label>
						<div class="col-sm-10">
							<input type="text" class="form-control" value="<?= $credito->cliente_email; ?>" name="correo" disabled>
							<!-- <input type="text" class="form-control" value="blackrap13@gmail.com" name="correo"> -->
						</div>
					</div>
				</div>
				<div class="form-group">
					<div class="row align-items-center">
						<label class="col-sm-2">Asunto:</label>
						<div class="col-sm-10">
                            <input type="text" class="form-control" name="asunto">
						</div>
					</div>
				</div>
				<div class="form-group">
					<label for="">Cuerpo:</label>
					<textarea rows="6" class="form-control" name="cuerpo"></textarea>
				</div>
				<textarea name="pie" class="form-control" rows="4" hidden></textarea>
				<div class="btn-list mt-4 text-right">
					<button type="button" class="btn btn-secondary btn-space" data-dismiss="modal">
						Cancelar
					</button>
					<button name="buttonEmailSubmit" id="buttonEmailSubmit" type="submit" class="btn btn-primary btn-space px-4">
						<i class="fe fe-send"></i> Enviar
					</button>
				</div>
			</form>
		</div>
	</div>
</div>


<script src="<?= base_url() ?>js_modules/credito/credito.detalle.js?v=99744"></script>