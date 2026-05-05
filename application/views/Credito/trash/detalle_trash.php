<!-- code: enc1217 -->

<script src="<?= base_url() ?>assets/js/vue.js"></script>
<?php 
	$ciclo_name = ($credito->ciclo_pago === '30') ? 'MENSUAL' : 'SEMANAL';
?>
<div id="credito_detalle" class="container">
	<input type="hidden" id="pv_cliente" value="<?= $credito->cliente_id; ?>">
	<div class="row">
      	<div class="col-xs-12 col-md-3">
    		<h4 class="page-title mb-1 text-primary">
        		<span class="font-weight-bold"><?= $credito->codigo; ?></span>
        	</h4>
    		<p class="px-1 py-0 text-danger">
    			Cuotas Atrasadas: <b><?php echo $atraso;  ?></b>
    		</p>  
        	<div class="mt-2 mb-4">
        		<?php if ($credito->estado == 'A' OR $credito->estado == 'R'): ?>
					<div class="d-flex p-1">
						<span class='text-dark font-weight-bold'>Liquidado: <?php echo $credito->liquidacion; ?></span>
					</div>
				<?php endif ?>
				<?php  
					switch ($credito->estado) {
						case 'E':
							echo "<span class='etag etag-cred-E'>Activo</span>";
							break;
						
						case 'R':
							echo "<span class='etag etag-cred-R'>Recuperado</span>";
							break;

						case 'A':
							echo "<span class='etag etag-cred-A'>Remate</span>";
							break;

						case 'CC':
							echo "<span class='etag etag-cred-CC'>Congelado</span>";
							break;
					}
				?>
          	</div>
        	<div>
	          	<div class="list-group list-group-transparent mb-0">
	          		<div class="list-group-item list-group-item-action d-flex align-items-center active">
	          			Opciones
	          		</div>
		            <a href="#" id="btn_revision" class="list-group-item list-group-item-action d-flex align-items-center text-primary" data-id="<?= $credito->id; ?>">
		              <span class="icon mr-3 "><i class="fe fe-search text-primary"></i></span>
		              Aplicar Revision
		            </a>
		            <a id="btn_revision" href="<?= base_url() ?>Credito/movimientos/<?= $credito->id; ?>" href="#" class="list-group-item list-group-item-action d-flex align-items-center text-primary">
		              <span class="icon mr-3"><i class="fe fe-shuffle text-primary"></i></span>
		              Ver movimientos
		            </a>
		            <a href="<?= base_url() ?>Credito/contrato/<?= $credito->id; ?>" class="list-group-item list-group-item-action d-flex align-items-center text-primary">
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
	            	<a v-on:click="rematar_credito" href="#" data-id="<?= $credito->id; ?>" class="list-group-item list-group-item-action d-flex align-items-center text-primary">
	            		<span class="icon mr-3"><i class="fe fe-briefcase text-primary"></i></span>
	            		Rematar
	            	</a>
	            	<?php endif ?>

	            	<?php if ($credito->estado === 'E'): ?>
	        		<!-- <a v-on:click="congelar_credito" href="#" :data-id="<?= $credito->id; ?>" class="list-group-item list-group-item-action d-flex align-items-center text-primary">
	            		<span class="icon mr-3"><i class="fe fe-stop-circle text-primary"></i></span>
	            		Congelar
	            	</a> -->
					<a href="<?= base_url() ?>Credito/congelamiento/<?= $credito->id ?>" class="list-group-item list-group-item-action d-flex align-items-center text-primary">
						<span class="icon mr-3"><i class="fe fe-stop-circle text-primary"></i></span>
						Congelar
					</a>
						<?php if ($credito->aplica_penalidad == 'I'): ?>
			            	<a href="#" v-on:click="modificar_cobro_mora" :data-id="<?= $credito->id; ?>" data-estado="A" class="list-group-item list-group-item-action d-flex align-items-center text-primary">
			            		<span class="icon mr-3"><i class="fe fe-check text-primary"></i></span>
			            		Activar cobro de mora
			            	</a>
		            	<?php else: ?>
							<a href="#" v-on:click="modificar_cobro_mora" :data-id="<?= $credito->id; ?>" data-estado="I" class="list-group-item list-group-item-action d-flex align-items-center text-danger">
			            		<span class="icon mr-3"><i class="fe fe-x text-danger"></i></span>
			            		Desactivar cobro de mora
			            	</a>
		            	<?php endif ?>            	
	            	<?php endif ?>

	            	<?php if ($elimina_opcion == 'SI'): ?>
	            		<a href="#" v-on:click="EliminarCredito" :data-id="<?= $credito->id; ?>" class="list-group-item list-group-item-action bg-scarlet text-light py-2">
		            		<span class="icon mr-3"><i class="fe fe-trash text-light"></i></span>
		            		Eliminar
		            	</a>
	            	<?php endif ?>

	            	<?php if ($credito->estado === 'E'): ?>
	            		<a id="btn_revision" href="<?= base_url() ?>Credito/moras/<?= $credito->id; ?>" class="list-group-item list-group-item-action d-flex align-items-center text-primary">
							<span class="icon mr-3"><i class="fe fe-info text-primary"></i></span>
		            		Administrar mora
						</a>
	            		<a id="btn_revision" href="<?= base_url() ?>Credito/editar/<?= $credito->id; ?>" class="list-group-item list-group-item-action d-flex align-items-center text-primary">
							<span class="icon mr-3"><i class="fe fe-code text-primary"></i></span>
		            		Editar
						</a>
	            	<?php endif ?>

	          	</div>	          	
        	</div>
      	</div>
      	<div class="col-xs-12 col-md-9" style="max-height: 650px; overflow-y: auto;">
      		<div v-if="alerta_dataset.length > 0" v-for="alerta in alerta_dataset" style="width: 100%">
      			<div class="abox">
	      			<div class="abox-content">
	      				{{ alerta.contenido }}
	      			</div>
	      			<div class="abox-footer">
	      				<a href="javascript:void(0)" v-on:click="AlertaFinalizar" class="abox-link" :data-id="alerta.id" :data-estado="alerta.estado">
	      					<i class="fe fe-check-circle"></i> Finalizar
	      				</a>
	      			</div>
	      		</div>
      		</div>
      		<div class="card" style="height: auto;">
          		<div class="card-header">
          			<div class="card-title text-primary">Notas</div>
          			<div class="card-options">
          				<button class="btn btn-primary" v-on:click="NotaNuevo">
          					Nuevo <i class="fe fe-plus"></i>
          				</button>
          			</div>
          		</div>
          		<div class="w-100 px-2 py-1" id="nota_log">&nbsp;</div>
          		<div class="card-table">
          			<table class="table table-striped">
          				<thead>
          					<tr>
          						<th style="width: 50%"><b>Descripcion</b></th>
          						<th style="width: 20%"><b>Fecha</b></th>
          						<th style="width: 30%; text-align: center;"><b>Opcion</b></th>
          					</tr>
          				</thead>
          				<tbody>
          					<tr v-if="!nota_dataset || nota_dataset.length === 0">
			                  	<td colspan="4" class="text-center">
			                  		Sin resultados
			                  	</td>
			                </tr>
			                <tr v-if="nota_dataset.length > 0" v-for="nota of nota_dataset">
			                	<td :class="nota.row_css">{{ nota.descripcion }}</td>
			                	<td :class="nota.row_css">{{ nota.fecha }}</td>
			                	<td class="text-center">
			                		<a href="javascript:void(0)" class="btn-link text-primary font-weight-bold" 
                                        :data-id="nota.id" v-on:click="NotaEditar">
                                        <i class="fe fe-edit-2 text-primary"></i> 
                                        Editar
                                    </a>&nbsp;|&nbsp;
                                    <a href="#" class="btn-link text-danger font-weight-bold" 
                                        :data-id="nota.id" v-on:click="NotaEliminar">
                                        <i class="fe fe-delete text-danger"></i> 
                                        Eliminar
                                    </a>
			                	</td>
			                </tr>
          				</tbody>
          			</table>
          		</div>
            </div>
	        <div class="card">
	          	<div class="card-header">
	            	<h3 class="card-title text-primary">
	            		<i class="fe fe-book"></i>
	            		Datos Generales
	            	</h3>	            	
	          	</div>
	          	<div class="card-body">
					<div class="row">						
			            <div class="col-xs-12 col-md-8 form-group">
			              	<div class="form-label">Cliente</div>
			              	<a href="<?= base_url() ?>Cliente/detalle/<?= $credito->cliente_id; ?>" class="btn-link font-weight-bold">
								<?php echo $credito->cliente; ?>
							</a>
			            </div>
						<div class="col-xs-12 col-md-4 form-group">
							<div class="form-label">Conducta</div>
							<div class="">
								<?php if($credito->cliente_puntuacion != ''): ?>
								<div class="enc1217-conducta-sm-tag enc1212-conducta-tag-<?= $credito->cliente_puntuacion; ?>">
									<?php echo $credito->cliente_puntuacion; ?>
								</div>
								<?php endif; ?>

								<?php if($credito->cliente_bloqueo === '1'): ?>
								<div id="cond-block-banner" class="enc1217-cond-blocked">
									<?php echo $credito->cliente_bloqueo_motivo; ?>
								</div>
								<?php endif; ?>
							</div>
						</div>
					</div>
					<div class="row">
			            <div class="col-xs-12 col-md-6 col-lg-4 form-group">
			              	<label for="" class="font-weight-bold form-label">Fecha de Creacion</label>
							<span><?php echo $credito->creacion; ?></span>
			            </div>
			            <div class="col-xs-12 col-md-6 col-lg-4 form-group">
			              	<label for="" class="font-weight-bold form-label">Tipo de Tasa</label>
							<?php if ($credito->tipo_tasa === 'P'): ?>
								<span>Porcentual</span>
							<?php else: ?>
								<span>Fijo</span>
							<?php endif ?>
			            </div>
			            <div class="col-xs-12 col-md-6 col-lg-4 form-group">
			              	<label for="" class="font-weight-bold form-label">Tasa</label>
							<?php if ($credito->tipo_tasa === 'P'): ?>
								<span><?php echo $credito->tasa; ?></span> %
							<?php else: ?>
								<span><?php echo $credito->tasa; ?></span>
							<?php endif ?>
			            </div>
			            <div class="col-xs-12 col-md-6 col-lg-4 form-group">
			              	<label for="" class="font-weight-bold form-label">Monto Prestado</label>
							<span name="money_item" data-value="<?php echo $credito->monto; ?>">S/ <?php echo $credito->monto; ?></span>
			            </div>
			            <div class="col-xs-12 col-md-6 col-lg-4 form-group">
			              	<label for="" class="font-weight-bold form-label">
								Pago <span><?php echo $ciclo_name; ?></span>
							</label>
							<span name="money_item" data-value="<?php echo $credito->pago; ?>">S/ <?php echo $credito->pago; ?></span>
			            </div>
			            <div class="col-xs-12 col-md-6 col-lg-4 form-group">
			              	<label for="" class="font-weight-bold form-label">Penalidad por mora</label>
							<span>S/ <?php echo $credito->penalidad; ?></span>
			            </div>
			            <div class="col-xs-12 col-md-6 col-lg-4 form-group">
			              	<label for="" class="font-weight-bold form-label">Prorroga</label>
							<span><?php echo $credito->prorroga; ?> dias</span>
			            </div>
			            <div class="col-xs-12 col-md-6 col-lg-4 form-group">
			              	<label for="" class="font-weight-bold form-label">Amortizacion Actual</label>
							<span class="text-blue font-weight-bold">S/ <?php echo $credito->amortizacion; ?></span>
			            </div>
		             	<div class="col-xs-12 col-md-6 col-lg-4 form-group">
			              	<label for="" class="font-weight-bold form-label">Adelanto Actual</label>
							<span class="text-blue font-weight-bold">S/ <?php echo $credito->adelanto; ?></span>
			            </div>
			            <div class="col-xs-12 col-md-6 col-lg-6 form-group">
			              	<label class="font-weight-bold form-label">Aplicacion de Mora</label>
							<?php  
								switch ($credito->aplica_penalidad) {
									case 'A':
										echo "<span class='text-blue'>Activo</span>";
										break;									
									case 'I':
										echo "<span class='text-red'>Desactivado</span>";
										break;
								}
							?>
			            </div>
						<div class="col-xs-12 col-md-6 col-lg-4 form-group">
			              	<label for="" class="font-weight-bold form-label">Ciclo de Pago</label>
							<span><?php echo $ciclo_name; ?></span>
			            </div>
						<!-- ciclo_pago -->
			            <div class="col-xs-12 col-md-6 col-lg-6 form-group">
							<label for="" class="font-weight-bold form-label">Proximo Pago</label>
							<span class="text-red"><?php echo $proximo_vencimiento; ?></span>
						</div>
						<?php if ($credito->estado === 'R' OR $credito->estado === 'A'): ?>
							<div class="col-xs-12 col-md-6 form-group">
								<label for="" class="font-weight-bold form-label">Fecha de Liquidacion</label>
								<span class="text-success font-weight-bold">
									<?php echo $credito->liquidacion; ?>
								</span>
							</div>
						<?php endif ?>

		          	</div>
				</div>
				<?php if (!empty($prenda)): ?>
	            <div class="card-header border">
		          	<div class="card-title text-primary">
		            	<i class="fe fe-tv"></i> Prenda
		          	</div>
	        	</div>
	        	<div class="card-body">
	        		<div class="row">
	        			<div class="col-xs-12 col-md-6 col-lg-4 form-group">
        					<div class="form-label">Descripcion</div>
        					<span><?php echo $prenda->descripcion; ?></span>
        				</div>
        				<div class="col-xs-12 col-md-6 col-lg-4 form-group">
        					<div class="form-label">Tipo</div>
        					<span><?php echo $prenda->tipo; ?></span>
        				</div>
        				<div class="col-xs-12 col-md-6 col-lg-4 form-group">
        					<div class="form-label">Categoria</div>
        					<span><?php echo $prenda->categoria; ?></span>
        				</div>
        				<div class="col-xs-12 col-md-6 col-lg-4 form-group">
        					<div class="form-label">Estado</div>
        					<span><?php echo $prenda->valoracion; ?></span>
        				</div>
        				<div class="col-xs-12 col-md-6 col-lg-4 form-group">
        					<div class="form-label">Enlace</div>
        					<a href="<?= base_url() ?>Prenda/detalle/<?= $prenda->id; ?>" class="btn-link">
        						Ver Detalle
        					</a>
        				</div>
	        		</div>
	        	</div>
		         <?php endif ?>				
				<div class="card-header border">
		          	<div class="card-title text-primary">
		            	<i class="fe fe-calendar"></i> Cuotas 
		            	<div class="d-inline" v-html="cuota_log"></div>
		          	</div>
		          	<div class="card-options">
						<div class="">
							<button v-on:click="buscar_cuotas" class="btn btn-primary fe fe-rotate-ccw" title="Recargar"></button>
						</div>
					</div>
	        	</div>
	        	<div class="p-0" style="max-height: 300px; overflow-y: auto;">
		            <table id="nota_table" class="card-table table text-nowrap">
		              	<thead>
		              		<tr>
								<th>Nº Orden</th>
								<th>Vencimiento</th>
								<th>Monto</th>
								<th>Dias de Vencimiento</th>
								<th>Mora</th>
								<th>Estado</th>
							</tr>
		              	</thead>
		              	<tbody>
		              		<tr v-if="cuotas.length > 0" v-for="(cuota, index) of cuotas">
		              			<td>{{ cuota.orden }}</td>
		              			<td>{{ cuota.vencimiento }}</td>
		              			<td>S/ {{ cuota.monto }}</td>
		              			<td>{{ cuota.atraso }}</td>
								<td>S/ {{ cuota.mora }}</td>
		              			<td>
		              				<span v-if="cuota.estado === 'E'" class="stag stag-cu-E">En espera</span>
		              				<span v-if="cuota.estado === 'V'" class="stag stag-cu-V">Vencido</span>
		              				<span v-if="cuota.estado === 'P'" class="stag stag-cu-P">Pagado</span>
		              				<span v-if="cuota.estado === 'CC'" class="stag stag-cu-CC">Congelado</span>
		              				<span v-if="cuota.estado === 'AN'" class="stag stag-cu-AN">Anulado</span>
		              			</td>
		              		</tr>
			                <tr v-if="!cuotas || cuotas.length === 0">
			                  	<td colspan="6" class="text-center">
			                  		<img src="<?= base_url() ?>assets/images/app_icon/file-blank.svg" style="display: inline-block;" width="40" alt="">
			                  		<p class="text-center" style="color: #718096;">Sin resultados</p>
			                  	</td>
			                </tr>
		              	</tbody>
		            </table>
	        	</div>
				<div class="card-header border">
		          	<div class="card-title text-primary">
		            	<i class="fe fe-credit-card"></i> Pagos 
		          	</div>
		          	<div class="card-options">
						<div class="">
							<button v-on:click="buscar_pagos" class="btn btn-primary fe fe-rotate-ccw" title="Recargar"></button>
						</div>
					</div>
	        	</div>
	        	<div class="w-100 px-2 py-1" id="nota_log">&nbsp;</div>
	            <table id="nota_table" class="card-table table text-nowrap">
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
	              	<tbody>
	              		<tr v-if="pagos.length > 0" v-for="(pago, index) of pagos">
	              			<td>{{ pago.serie }}</td>
	              			<td>{{ pago.fecha }}</td>
	              			<td>{{ pago.forma }}</td>
	              			<td>{{ pago.concepto }}</td>
	              			<td>S/ {{ pago.total }}</td>
	              			<td>
	              				<a v-bind:href="pago.url" class="btn-link">
	              					Detalle
	              				</a>
	              			</td>
	              		</tr>
		                <tr v-if="!pagos || pagos.length === 0">
		                  	<td colspan="6" class="text-center">
		                  		<img src="<?= base_url() ?>assets/images/app_icon/file-blank.svg" style="display: inline-block;" width="40" alt="">
		                  		<p class="text-center" style="color: #718096;">Sin resultados</p>
		                  	</td>
		                </tr>
	              	</tbody>
	            </table>
	        </div>

	        <!-- Modals -->
			<div id="MdNotaNuevo" class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-hidden="true">
			  	<div class="modal-dialog modal-lg" role="document">
				    <div class="modal-content">
				      	<div class="modal-header">
					        <h4 class="modal-title">
					        	Nueva Nota
					        	<i class="fe fe-file-plus text-blue"></i>
					        </h4>
					        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
					        	<span aria-hidden="true" class="text-primary"></span>
					        </button>
				      	</div>
				  		<form autocomplete="off" autocorrect="off" v-on:submit.prevent="NotaRegistrar">
				      		<div class="modal-body">
								<div class="row">
									<div class="col-xs-12 col-md-12">
										<div class="form-group">
											<label for="" class="font-weight-bold">Descripcion</label>
											<textarea v-model="nota_detail.descripcion" cols="10" rows="5" class="form-control text-uppercase"></textarea>
										</div>
									</div>
									<div class="col-xs-12 col-md-4">
										<div class="form-group">
					                        <div class="form-label">Tipo</div>
					                        <select v-model="nota_detail.anclado" class="form-control border-rock">
					                        	<option value="NO">NOTA NORMAL</option>
					                        	<option value="SI">NOTA ANCLADA</option>
					                        </select>
				                      	</div>
									</div>
								</div>
				      		</div>
				      		<div class="modal-footer text-right">
				  				<button type="submit" class="btn btn-blue">
				  					<i class="fe fe-save"></i> Registrar
				  				</button>
				  				<button class="btn btn-secondary" data-dismiss="modal">
				  					<i class="fe fe-corner-left-down"></i> Cancelar
				  				</button>
				      		</div>
						</form>
				    </div>
			  	</div>
			</div>

			<div id="MdNotaEditar" class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-hidden="true">
			  	<div class="modal-dialog modal-lg" role="document">
				    <div class="modal-content">
				      	<div class="modal-header">
					        <h4 class="modal-title">
					        	Editar Nota
					        	<i class="fe fe-file-plus text-blue"></i>
					        </h4>
					        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
					        	<span aria-hidden="true" class="text-primary"></span>
					        </button>
				      	</div>
				  		<form autocomplete="off" autocorrect="off" v-on:submit.prevent="NotaActualizar">
				      		<div class="modal-body">
								<div class="row">
									<div class="col-xs-12 col-md-12">
										<div class="form-group">
											<label for="" class="font-weight-bold">Descripcion</label>
											<textarea v-model="nota_detail.descripcion" cols="10" rows="5" class="form-control text-uppercase"></textarea>
										</div>
									</div>
									<div class="col-xs-12 col-md-4">
										<div class="form-group">
					                        <div class="form-label">Tipo</div>
					                        <select v-model="nota_detail.anclado" class="form-control border-rock">
					                        	<option value="NO">NOTA NORMAL</option>
					                        	<option value="SI">NOTA ANCLADA</option>
					                        </select>
				                      	</div>
									</div>
								</div>
				      		</div>
				      		<div class="modal-footer text-right">
				  				<button type="submit" class="btn btn-blue">
				  					<i class="fe fe-save"></i> Actualizar
				  				</button>
				  				<button class="btn btn-secondary" data-dismiss="modal">
				  					<i class="fe fe-corner-left-down"></i> Cancelar
				  				</button>
				      		</div>
						</form>
				    </div>
			  	</div>
			</div>

			<div id="MdNotaNuevo" class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-hidden="true">
			  	<div class="modal-dialog modal-lg" role="document">
				    <div class="modal-content">
				      	<div class="modal-header">
					        <h4 class="modal-title">
					        	Nueva Nota
					        	<i class="fe fe-file-plus text-blue"></i>
					        </h4>
					        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
					        	<span aria-hidden="true" class="text-primary"></span>
					        </button>
				      	</div>
				  		<form autocomplete="off" autocorrect="off" v-on:submit.prevent="NotaActualiza">
				      		<div class="modal-body">
								<div class="row">
									<div class="col-xs-12 col-md-12">
										<div class="form-group">
											<label for="" class="font-weight-bold">Descripcion</label>
											<textarea name="descripcion" cols="10" rows="5" class="form-control text-uppercase"></textarea>
										</div>
									</div>
									<div class="col">
										<div class="form-group">
					                        <div class="form-label">Tipo</div>
					                        <label class="custom-switch">
					                          	<input type="checkbox" name="anclado" class="custom-switch-input">
					                          	<span class="custom-switch-indicator"></span>
					                          	<span class="custom-switch-description">Anclar Nota</span>
					                        </label>
				                      </div>
									</div>
								</div>
				      		</div>
				      		<div class="modal-footer text-right">
				  				<button type="submit" class="btn btn-warning">
				  					<i class="fe fe-save"></i> Aplicar
				  				</button>
				  				<button class="btn btn-secondary" data-dismiss="modal">
				  					<i class="fe fe-corner-left-down"></i> Cancelar
				  				</button>
				      		</div>
						</form>
				    </div>
			  	</div>
			</div>

	        <!-- end modals -->

      	</div>
    </div>
</div>
<script>
	document.addEventListener('DOMContentLoaded', function(){
		var app = new App();
		var mitems = document.getElementsByName('money_item');
		if (mitems.length > 0) {
			mitems.forEach(element => {
				if (element.dataset.value) {
					element.textContent = `S/ ${ app.priceFormat(element.dataset.value) }`;
				}
			})
		}
	})
</script>
<script src="<?= base_url() ?>js_modules/credito/credito.detalle_trash.js"></script>


<style>
	.bl-tag {
		display: block;
		padding: .4rem .6rem;
	}
	.bl-title {
		font-size: 1.1rem;
		letter-spacing: 0px;
		font-weight: 600;
		color: #1F3044; 
	}
	.bl-subvalue {
		font-size: .9rem;
		font-weight: 500;
		/* display: inline-block; */
		color: #F64668;
		text-transform: lowercase;
	}
	.abox {
		width: 100%;
		border: 1px solid #ECC94B;
		border-radius: 3px;
		margin-top: 6px;
		margin-bottom: 10px;
	}
	.abox-content {
		width: 100%;
		padding: 8px 8px 2px 8px;
		line-height: 2em;
		background: #ECC94B;
		color: #4A5568;
		text-align: justify;
		letter-spacing: .8px;
		font-weight: 300;
	}
	.abox-footer {
		width: 100%;
		display: flex;
		justify-content: flex-end;
		background: #FBC531;
	}
	a.abox-link {
		text-decoration: none;
		text-align: center;
		padding: 5px 8px;
		transition: .5s all;
		background: #ECC94B;
		color: #4A5568;
	}
	a.abox-link:hover {
		/*background: #FBC531;
		color: #4A5568;*/
		background: #2D3748;
		color: #fff;
	}
	.etag {
		padding: 3px 4px;
		text-align: center;
		border: 1px solid #4A5568;
		border-radius: 2px;
		background: #EDF2F7;
		color: #4A5568;
		width: 100%;
		display: inline-block;
	}

	.etag-cred-E {
		background: var(--cred_activo);
		border: 1px solid #202C39;
		color: #202C39;
	}
	.etag-cred-R {
		background: var(--cred_recuperado);
		border-color: var(--cred_recuperado);
		color: #fff;
	}
	.etag-cred-CC {
		/*background: var(--cred_congelado);
		border-color: var(--cred_congelado);*/
		background: #06A8CB;
		border-color: #39495E;
		color: #fff;
	}
	.etag-cred-AN {
		background: var(--cred_anulado);
		border-color: var(--cred_anulado);
		color: #fff;
	}
	.etag-cred-A {
		background: var(--cred_remate);
		border-color: var(--cred_remate);
		color: #fff;
	}
	.etag-cred-V {
		background: #FBC531;
		color: #35495E;
	}
</style>
