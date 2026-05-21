
<script src="<?= base_url('assets/plugins/html2pdf.min.js') ?>"></script>
<?php 
	ini_set('display_errors', 0); 
	$ciclo_name = ($credito->ciclo_pago === '30') ? 'MENSUAL' : 'SEMANAL';	
	$ciclo_value = intval($credito->ciclo_pago);
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Contrato de Credito</title>
	<link rel="shortcut icon" type="image/png" href="<?= base_url() ?>assets/images/electrocash_favicon.png">
	<script src="<?= base_url('assets/plugins/html2canvas.min.js') ?>"></script>
	<script src="<?= base_url('assets/plugins/jspdf.umd.min.js') ?>"></script>
	<link rel="stylesheet" href="<?= base_url() ?>assets/fonts/Poppins/poppins.css">
	<link rel="stylesheet" href="<?= base_url() ?>assets/css/contrato.css">
	<link href="<?= base_url() ?>assets/css/feather.css?v=050625" rel="stylesheet">
</head>
<body>
<div class="flex">
	<div class="my-file">
		<div class="header">
			<p class="header-title">Contrato de Credito</p>
			<div class="controls">
				<button id="btn_print" class="btnz">Imprimir</button>	
				<button id="btn_export" class="btnz">Exportar PDF</button>
			</div>
		</div>
		<div id="anexo_file" class="overlay">
			<div class="page-a4 contrato">
				<div class="page-content">
					<div class="contrato-header">
						<div class="logo">
							<img src="<?= base_url()?>assets/images/electrocash_logo.png" alt="">
						</div>
						<div class="info">
							<b class="text-dark" style="font-size: 17px">
								Whatsapp: <?php echo $credito->sucursal_telefono; ?>&nbsp;
								<i class="fab fa-whatsapp"></i>
							</b>
						</div>
						<div class="serie">
							<b class="text-dark" style="font-size: 16px">Nº de Contrato : </b>
							<?php echo $credito->codigo; ?>
						</div>
					</div>
					<div class="contrato-titulo">
						<h3 class="titulo-principal">ANEXO 01:  DETALLE DEL CREDITO</h3>
					</div>
					<div class="contrato-subtitulo">
						<div class="liquidacion">
							<p>LIQUIDACION: 
								<span class="liquidacionBarra">
									<!-- <b>/</b>
									<b>/</b> -->
								</span>
							</p>
						</div>
						<div class="creacion">
							<p>FECHA:&nbsp;&nbsp;
								<span><?php echo $credito->creacion; ?></span>
							</p>
						</div>
					</div>
					<div class="contrato-cliente">
						<table class="tablaCon1">
							<thead>
								<tr>
									<th colspan="2">DATOS DEL SOLICITANTE</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td class="font-bold text-right" style="width: 20%;padding-right: 10px">
										NOMBRES
									</td>
									<td style="padding-left: 8px">
										<?php echo $credito->cliente_nombre; ?>
									</td>
								</tr>
								<tr>
									<td class="font-bold text-right" style="width: 20%;padding-right: 10px">
										APELLIDOS
									</td>
									<td style="padding-left: 8px">
										<?php echo $credito->cliente_apellido; ?>
									</td>
								</tr>
								<tr>
									<td class="font-bold text-right" style="width: 20%;padding-right: 10px">
										DNI/CE
									</td>
									<td style="padding-left: 8px">
										<?php echo $credito->cliente_documento; ?>
									</td>
								</tr>
								<tr>
									<td class="font-bold text-right" style="width: 20%;padding-right: 10px">
										DIRECCION
									</td>
									<td style="padding-left: 8px">
										<?php echo $credito->cliente_direccion; ?>
									</td>
								</tr>
								<tr>
									<td class="font-bold text-right" style="width: 20%;padding-right: 10px">
										DISTRITO
									</td>
									<td style="padding-left: 8px">
										<?php echo $credito->cliente_ciudad; ?>
									</td>
								</tr>
								<tr>
									<td class="font-bold text-right" style="width: 20%;padding-right: 10px">
										TELEFONOS
									</td>
									<td style="padding-left: 8px">
										<?php echo $credito->cliente_telefono; ?>
									</td>
								</tr>
							</tbody>
						</table>
					</div>
					<div class="contrato-principal">
						<table class="tablaCon1">
							<thead>
								<tr>
									<th colspan="2">DATOS DEL PRESTAMO</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td class="font-bold text-right" style="width: 60%;padding-right: 10px">
										IMPORTE DEL PRESTAMO
									</td>
									<td name="money_item" data-value="<?php echo $credito->monto; ?>" style="padding-left: 8px">
										S/ <?php echo $credito->monto; ?>
									</td>
								</tr>
								<tr>
									<td class="font-bold text-right" style="width: 60%;padding-right: 10px">
										Nº DE CUOTAS 
										<?php echo $ciclo_name; ?>ES
									</td>
									<td style="padding-left: 8px">
										INDEFINIDO
									</td>
								</tr>
								<tr>
									<td class="font-bold text-right" style="width: 60%;padding-right: 10px">
										FECHA DE INICIO
									</td>
									<td style="padding-left: 8px">
										<?php echo $credito->creacion; ?>
									</td>
								</tr>
								<tr>
									<td class="font-bold text-right" style="width: 60%;padding-right: 10px">
										FECHA DE VENCIMIENTO
									</td>
									<td style="padding-left: 8px">
										INDEFINIDO
									</td>
								</tr>
								<tr>
									<td class="font-bold text-right" style="width: 60%;padding-right: 10px">
										CUOTA 
										<?php echo $ciclo_name; ?>
									</td>
									<td name="money_item" data-value="<?php echo $credito->pago; ?>" style="padding-left: 8px">
										S/ <?php echo $credito->pago; ?>
									</td>
								</tr>
								<tr>
									<td class="font-bold text-right" style="width: 60%;padding-right: 10px">
										PENALIDAD POR DIA DE ATRASO
									</td>
									<td style="padding-left: 8px">
										S/ <?php echo $credito->penalidad; ?>
									</td>
								</tr>
							</tbody>
						</table>
					</div>
					<div class="contrato-lateral">
						<table class="tablaCon1">
							<thead>
								<tr>
									<th colspan="2">COMPOSICION DE LA CUOTA</th>
								</tr>
							</thead>
							<tbody>
								<!-- <tr>
									<td class="text-right font-bold" style="width: 40%;padding-right:10px">
										TASA
									</td>
									<td class="text-center" style="padding-left: 8px">
										<?php if ($credito->tipo_tasa === 'P'): ?>
											<?php echo $credito->tasa; ?> %
										<?php else: ?>
											<?php echo $credito->tasa; ?>
										<?php endif ?>
									</td>
								</tr> -->
								<tr>
									<td class="text-right font-bold" style="width: 40%;padding-right:10px">
										VALOR
									</td>
									<td name="money_item" data-value="<?php echo $credito->pago; ?>" class="text-center" style="padding-left: 8px">
										S/ <?php echo $credito->pago; ?>
									</td>
								</tr>
							</tbody>
						</table>
						<br>
						<table class="contrato-tabla2">
							<thead>
								<tr>
									<th>CUOTA</th>
									<th>FECHA</th>
								</tr>
							</thead>
							<tbody>
								<?php
									// $FechaInicio = date_create($credito->fecha); 
									// $ciclo = $credito->ciclo_pago === 'MENSUAL' ? 30 : 7;
									// $ciclo = intval($credito->ciclo_pago);
									
									for ($i=1; $i<=15; $i++){
										$dias = $ciclo_value*$i;
										// $fecha_string = $ciclo === 30 ? "+ $i month" : "+ $i week";
										$fecha_string = " + $dias days";
										$date_current = date('d-m-Y', strtotime($credito->fecha. $fecha_string));
											$ord = $i>9 ? $i : '0'.$i;
										echo "
											<tr>
												<td class='text-center font-bold'>$ord</td>
												<td class='text-center'>$date_current</td>
											</tr>
										";
									}
								?>
							</tbody>
						</table>
					</div>
					<div class="contrato-prenda">
						<table class="tablaCon1" >
							<thead>
								<tr>
									<th colspan="4">DATOS DE LA PRENDA</th>
								</tr>
							</thead>
							<?php if (empty($prenda)): ?>
								<tr style="height: 100px">
									<td colspan="4" class="text-center">SIN GARANTIA</td>
								</tr>
							<?php else: ?>
							<tbody>
								<tr>
									<td class="font-bold text-right" style="width: 30%;padding-right: 10px" colspan="2">
										CATEGORIA
									</td>
									<td style="padding-left: 8px" colspan="2">
										<?php if (!empty($prenda->categoria)): ?>
											<?php echo $prenda->categoria; ?>
										<?php endif ?>
									</td>
								</tr>
								<?php if ($prenda->tipo == 'JOYA'): ?>
									<tr>
										<td class="font-bold text-right" style="width: 30%;padding-right: 10px" colspan="2">
											QUILATE
										</td>
										<td style="padding-left: 8px" colspan="2">
											<?php if (!empty($prenda->quilate)): ?>
												<?php echo $prenda->quilate; ?>
											<?php endif ?>
										</td>
									</tr>
									<tr>
										<td class="font-bold text-right" style="width: 30%;padding-right: 10px" colspan="2">
											PESO 
										</td>
										<td style="padding-left: 8px" colspan="2">
											<?php if (!empty($prenda->peso)): ?>
												<?php echo $prenda->peso; ?>
											<?php endif ?>
										</td>
									</tr>
								<?php else: ?>
									<?php if ($prenda->tipo == 'VEHICULO'): ?>
									<tr>
										<td class="font-bold text-right" style="width: 30%;padding-right: 10px" colspan="2">
											MARCA 
										</td>
										<td style="padding-left: 8px" colspan="2">
											<?php if (!empty($prenda->marca)): ?>
												<?php echo $prenda->marca; ?>
											<?php endif ?>
										</td>
									</tr>
									<tr>
										<td class="font-bold text-right" style="width: 30%;padding-right: 10px" colspan="2">
											MODELO
										</td>
										<td style="padding-left: 8px" colspan="2">
											<?php if (!empty($prenda->modelo)): ?>
												<?php echo $prenda->modelo; ?>
											<?php endif ?>
										</td>
									</tr>
									<tr>
										<td class="font-bold text-right" style="width: 30%;padding-right: 10px" colspan="2">
											AÑO
										</td>
										<td style="padding-left: 8px" colspan="2">
											<?php if (!empty($prenda->anio)): ?>
												<?php echo $prenda->anio; ?>
											<?php endif ?>
										</td>
									</tr>
									<tr>
										<td class="font-bold text-right" style="width: 30%;padding-right: 10px" colspan="2">
											PLACA
										</td>
										<td style="padding-left: 8px" colspan="2">
											<?php if (!empty($prenda->placa)): ?>
												<?php echo $prenda->placa; ?>
											<?php endif ?>
										</td>
									</tr>
									<tr>
										<td class="font-bold text-right" style="width: 30%;padding-right: 10px" colspan="2">
											KILOMETRAJE
										</td>
										<td style="padding-left: 8px" colspan="2">
											<?php if (!empty($prenda->kilometraje)): ?>
												<?php echo $prenda->kilometraje; ?>
											<?php endif ?>
										</td>
									</tr>
									<?php else: ?>
									<tr>
										<td class="font-bold text-right" style="width: 30%;padding-right: 10px" colspan="2">
											MARCA 
										</td>
										<td style="padding-left: 8px" colspan="2">
											<?php if (!empty($prenda->marca)): ?>
												<?php echo $prenda->marca; ?>
											<?php endif ?>
										</td>
									</tr>
									<tr>
										<td class="font-bold text-right" style="width: 30%;padding-right: 10px" colspan="2">
											MODELO
										</td>
										<td style="padding-left: 8px" colspan="2">
											<?php if (!empty($prenda->modelo)): ?>
												<?php echo $prenda->modelo; ?>
											<?php endif ?>
										</td>
									</tr>
									<tr>
										<td class="font-bold text-right" style="width: 30%;padding-right: 10px" colspan="2">
											SERIE
										</td>
										<td style="padding-left: 8px" colspan="2">
											<?php if (!empty($prenda->serial)): ?>
												<?php echo $prenda->serial; ?>
											<?php endif ?>
										</td>
									</tr>
									<?php endif ?>
								<?php endif ?>
								
								<tr>
									<td class="font-bold text-right" style="width: 30%;padding-right: 10px" colspan="2">
										ESTADO
									</td>
									<td style="padding-left: 8px" colspan="2">
										<?php if (!empty($prenda->valoracion)): ?>
											<?php echo $prenda->valoracion; ?>
										<?php endif ?>
									</td>
								</tr>
								<tr>
									<td class="font-bold text-right" style="width: 30%;padding-right: 10px;height: 100%" colspan="2">
										OBSERVACIONES 
									</td>
									<td colspan="2">
										<textarea class="fake__rowspan text-uppercase" cols="10" rows=""><?php echo str_replace("-","",$prenda->observacion); ?></textarea>
									</td>
								</tr>
								<?php if($prenda->tipo == 'VEHICULO'): ?>
									<tr>
										<td class="font-bold text-right" style="width: 30%;padding-right: 10px">
											COLOR
										</td>
										<td style="padding-left: 8px"><?php echo $prenda->color; ?></td>
										<td class="font-bold text-right" style="width: 30%;padding-right: 10px">
											CARROCERIA
										</td>
										<td style="padding-left: 8px">
											<?php echo $prenda->carroceria; ?>
										</td>
									</tr>
								<?php else: ?>
									<tr>
										<td class="font-bold text-right" style="width: 30%;padding-right: 10px;height: 100%" colspan="2">
											COMENTARIOS 
										</td>
										<td rowspan="2" style="padding-left: 8px;font-size: 11px" colspan="2">
											<textarea class="fake__rowspan text-uppercase" cols="10" rows=""><?php echo str_replace("-","",$prenda->comentario); ?></textarea>
										</td>
									</tr>
								<?php endif; ?>
							</tbody>
							<?php endif ?>
						</table>
					</div>
					<div class="contrato-metodo">
						<table class="tablaMetodo" style="font-size: 11px;">
							<thead>
								<tr>
									<th colspan="2" class="font-bold">
										METODOS DE PAGO
									</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td class="text-center">
										Pago Directamente en Tienda	
									</td>
									<td class="text-center">
										<?php echo $credito->sucursal_direccion; ?>
									</td>
								</tr>
								<tr>
									<td class="text-center">
										Banco y Tipo de Cuenta
									</td>
									<td class="text-center">
										<?php echo $credito->banco_tipo; ?>
									</td>
								</tr>
								<tr>
									<td class="text-center">
										Nro de Cuenta 
									</td>
									<td class="text-center">
										<?php echo $credito->cuenta_numero; ?>
									</td>
								</tr>
								
							</tbody>
						</table>
						<!-- <p class="contrato-letras">Se deja constancia de que la prenda ha sido entregada a ELECTROCASH, en el estado descrito en el <br> presente documento, con el fin de avalar el prestamo otorgado. El solicitante, declara conocer <br> plenamente todos los terminos y condiciones que se detallan en el <b>Documento de Contrato</b>.</p> -->
						
						<p class="contrato-letras">
							La prenda ha sido entregada a ELECTROCASH en el estado descrito en el presente documento,<br> con el fin de avalar el préstamo otorgado. El solicitante declara bajo juramento ser el único<br> y legítimo propietario del bien entregado en garantía, asimismo, reconoce haber leído y<br> comprendido los términos y condiciones del <b>contrato suscrito</b>.
						</p>
	
					</div>
					<div class="contrato-footer">
						<div class="firma_empresa">
							<img src="<?= base_url()?>assets/images/electrocash_firma.jpg" alt="" width="100px">
							<p>ELECTROCASH</p>
						</div>
						<div class="firma_cliente">
							<p class="fc_titulo">Solicitante</p>
							<p class="fc_nombre">
								Nombre: 
							</p>
							<p class="fc_dni">
								DNI: 
							</p>
						</div>
					</div>



				</div>
			</div>
			<div class="page-a4 declaracion">
				DECLARACION
			</div>

		</div>
	</div>
</div>

<script src="<?= base_url() ?>assets/js/vendors/jquery-3.2.1.min.js"></script>
<script src="<?= base_url() ?>js_modules/app.js"></script>
<script src="<?= base_url() ?>assets/js/jQuery.print.js"></script>
<script src="<?= base_url()?>js_modules/credito/contrato.js?v=32221"></script> 
</body>
</html>
