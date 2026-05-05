<link rel="stylesheet" href="<?= base_url() ?>/assets/fonts/Roboto/roboto.css">
<link rel="stylesheet" href="<?= base_url() ?>/assets/fonts/Inter/inter.css">
<script src="<?= base_url() ?>assets/plugins/html2canvas.min.js"></script>

<div class="container p-0">
    <div class="card">
        <div class="card-header px-2 flex-column-mobile">
            <div class="card-title py1-mob mb2-mob">
                <span class="text-blue font-weight-bold">
                #<?php echo $pago->serie;  ?>
                </span>
                | Detalle de Pago
            </div>
            <div class="card-options">
                <div class="">
                <button id="btn_anulacion" data-credito="<?= $pago->credito; ?>" data-pago="<?= $pago->id; ?>" class="btn btn-danger">
                    <i class="fe fe-slash"></i>
                    Anulacion
                </button>
                <a class="btn btn-primary" href="http://localhost/ElectroPlus/index.php?c=pago&a=ver_detalle&p=<?= $pago->id; ?>" target="_blank">
                    <i class="fe fe-printer"></i>
                    Procesar voucher
                </a>
                <button id="controlSendMail" data-credito="<?= $pago->credito; ?>" data-pago="<?= $pago->id; ?>" data-email="<?= $pago->email; ?>" class="btn btn-secondary">
                    <i class="fe fe-mail"></i>
                    Enviar Email
                </button>
                <button id="btn-preview" data-toggle="modal" data-target="#modalPreview" class="btn btn-secondary">
                    <i class="fe fe-file-text"></i>
                    Voucher Virtual
                </button>         
                <!-- <a href="<?= base_url() ?>Renderizer/voucher_pago/<?= $pago->id; ?>" target="_blank" class="btn btn-dark">Voucher Virtual</a> -->
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="d-flex">
                <div class="col-xs-12 col-md-6 form-group py-0">
                    <label class="form-label">Estilo de tabla</label>
                    <label class="custom-switch">
                        <input type="checkbox" id="check_darkmode" class="custom-switch-input">
                        <span class="custom-switch-indicator"></span>
                        <span class="custom-switch-description">
                        Modo oscuro
                        </span>
                    </label>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-12 text-left">
                    <p class="h3">
                    <?php  
                        switch($pago->concepto){
                            case 'REG':
                            echo 'Pago por Regularizacion';
                            break;
                            case 'REC':
                            echo 'Liquidacion de credito';
                            break;
                            case 'AMOR':
                            echo 'Amortizacion de credito';
                            break;
                            case 'ADEL':
                            echo 'Pago por Adelanto';
                            break;
                        }
                    ?>
                    </p>
                    <ul class="pdet-list">
                        <li>
                        <label for="">Credito:</label>
                        <span>
                            <a class="btn-link" href="<?= base_url(); ?>Credito/detalle/<?= $pago->credito; ?>">
                            #<?php echo $pago->codigo_credito; ?>
                            </a>
                        </span>
                        </li>
                        <li>
                        <label for="">USUARIO:</label>
                        <span><?php echo $pago->cliente; ?></span>
                        </li>
                        <li>
                        <label for="">Fecha:</label>
                        <span><?php echo $pago->fecha; ?></span>
                        </li>
                        <li>
                        <label for="">Empleado:</label>
                        <span><?php echo $pago->empleado; ?></span>
                        </li>
                        <?php  
                        if ($pago->forma === 'DEP') {
                            $bnk = ($pago->banco_nombre) ? $pago->banco_nombre : 'No especificado';
                            echo "<li>";
                            echo "<label>Forma de Pago:</label>&nbsp;";
                            echo "<span>DEPOSITO</span>";
                            echo "</li>";
                            echo "<li>";
                            echo "<label>Entidad:</label>&nbsp;";
                            echo "<span>$pago->banco_nombre</span>";
                            echo "</li>";
                            echo "<li>";
                            echo "<label>Comprobante:</label>&nbsp;";
                            echo "<span>$pago->comprobante</span>";
                            echo "</li>";
                        }elseif ($pago->forma === 'TRANF') {
                            $bnk = ($pago->banco_nombre) ? $pago->banco_nombre : 'No especificado';
                            echo "<li>";
                            echo "<label>Forma de Pago:</label>&nbsp;";
                            echo "<span>TRANSFERENCIA</span>";
                            echo "</li>";
                            echo "<li>";
                            echo "<label>Entidad:</label>&nbsp;";
                            echo "<span>$pago->banco_nombre</span>";
                            echo "</li>";
                            echo "<li>";
                            echo "<label>Comprobante:</label>&nbsp;";
                            echo "<span>$pago->comprobante</span>";
                            echo "</li>";
                        }elseif ($pago->forma === 'AGEN') {
                            echo "<li>";
                            echo "<label>Forma de Pago:</label>&nbsp;";
                            echo "<span>AGENTE</span>";
                            echo "</li>";
                            echo "<li>";
                            echo "<label>Entidad:</label>";
                            echo "<span>$pago->banco_nombre</span>";
                            echo "</li>";
                            echo "<li>";
                            echo "<label>Comprobante:</label>&nbsp;";
                            echo "<span>$pago->comprobante</span>";
                            echo "</li>";
                        }elseif ($pago->forma === 'TIEN') {
                            echo "<li>";
                            echo "<label>Forma de Pago:</label>&nbsp;";
                            echo "<span>TIENDA</span>";
                            echo "</li>";
                        }elseif ($pago->forma === 'REV') {
                            echo "<li>";
                            echo "<label>Forma de Pago:</label>&nbsp;";
                            echo "<span>Revision</span>";
                            echo "</li>";
                        }
                        ?>
                        <?php if (!empty($pago->comentario)): ?>
                        <li>
                        <label for="">Comentarios:</label>
                        <span class="text-danger"><?php echo $pago->comentario; ?></span>
                        </li>
                        <?php endif; ?>
                    </ul>
                </div>        
            </div>
            <div class="table-responsive push">
                <table id="table_detalle" class="table table-bordered table-hover table-sm">
                <tr>
                    <th class="text-center" style="width: 1%"></th>
                    <th>Descripcion</th>
                    <th class="text-right" style="width: 13%">Monto</th>
                </tr>
                <?php if (!empty($detalle)): ?>
                    <?php foreach ($detalle as $ind => $d): ?>
                    <tr>
                        <td class="text-center"><?= $ind+1; ?></td>
                        <td>
                        <p class="font-w600 mb-1">
                            <?php echo $d->referencia." ".$d->subref; ?>
                        </p>
                        </td>
                        <td class="text-right">S/ <?php echo $d->monto; ?></td>
                    </tr>
                    <?php endforeach ?>
                <?php else: ?>
                    <tr class="text-center">
                    <td colspan="3">Datos no encontrados</td>
                    </tr>
                <?php endif ?>
                <tr>
                    <td colspan="2" class="font-w600 text-right">Subtotal</td>
                    <td class="text-right">S/ <?= $pago->subtotal; ?></td>
                </tr>
                            <?php if ($pago->adicional && $pago->adicional > 0) {?>
                        <td colspan="2" class="font-w600 text-right">
                                        <div class="d-flex" style="justify-content: space-between;">
                                            <div class="d-block text-blue"><?php echo $pago->comentario; ?></div>
                                            <div class="d-block">Cargos Adicionales</div>
                                        </div>
                                    </td>
                        <td class="text-right">S/ <?= $pago->adicional; ?></td>
                    </tr>
                            <?php } ?>
                <tr>
                    <td colspan="2" class="font-w600 text-right text-danger">Descuento</td>
                    <td class="text-right text-danger">- S/ <?= $pago->descuento; ?></td>
                </tr>					
                <tr>
                    <td colspan="2" class="font-weight-bold text-uppercase text-right">Total</td>
                    <td class="font-weight-bold text-right">S/ <?= $pago->total; ?></td>
                </tr>
                <tr>
                    <td colspan="2" class="text-uppercase text-right">Recibido</td>
                    <td class="text-blue text-right">S/ <?= $pago->recibido; ?></td>
                </tr>
                <tr>
                    <td colspan="2" class="text-uppercase text-right">Vuelto</td>
                    <td class="text-red text-right">S/ <?= $pago->devuelto; ?></td>
                </tr>
                </table>
            </div>

        </div>

    </div>
</div>
<div id="modal-invoice" class="vmodal-content">
    <div class="vmodal-bar">
        <button class="vmodal-btn" name="modal-button" title="Cerrar" data-action="modal-close">
            <svg xmlns="http://www.w3.org/2000/svg" class="ionicon" viewBox="0 0 512 512"><title></title><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="32" d="M368 368L144 144M368 144L144 368"/></svg>
        </button>
        <button class="vmodal-btn" name="modal-button" title="Descargar" data-code="<?= $pago->serie; ?>" data-action="modal-download">
            <svg xmlns="http://www.w3.org/2000/svg" class="ionicon" viewBox="0 0 512 512"><title></title><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="48" d="M112 268l144 144 144-144M256 392V100"/></svg>
        </button>
    </div>
    <div id="invoice" class="invoice-card">
        <div class="invoice-logo">
            <img src="<?= base_url() ?>assets/images/electrocash_logo_nuevo.png" alt="">
        </div>
        <!-- <div class="invoice-logo">
            <div class="invoice-logo-main">Electro</div>
            <div class="invoice-logo-label">CA$H</div>
        </div> -->
        <div class="invoice-title">
            <div id="main-title">
                <h4>VOUCHER DE PAGO</h4>
                <span>#<?= $pago->serie; ?></span>
            </div>
        </div>
        <div class="invoice-info-area">
            <div class="invoice-info-item">
                <div class="invoice-info-label">FECHA: </div>
                <div class="invoice-info-value"><?= $pago->fecha; ?></div>
            </div>
            <div class="invoice-info-item">
                <div class="invoice-info-label">CREDITO: </div>
                <div class="invoice-info-value"><?= $pago->codigo_credito; ?></div>
            </div>
            <div class="invoice-info-item">
                <div class="invoice-info-label">CONCEPTO: </div>
                <div class="invoice-info-value">
                <?php  
                    switch($pago->concepto){
                        case 'REG':
                        echo 'REGULARIZACION';
                        break;
                        case 'REC':
                        echo 'LIQUIDACION';
                        break;
                        case 'AMOR':
                        echo 'AMORTIZACION';
                        break;
                        case 'ADEL':
                        echo 'ADELANTO';
                        break;
                    }
                ?>
                </div>
            </div>
            <div class="invoice-info-item">
                <div class="invoice-info-label">USUARIO: </div>
                <div class="invoice-info-value"><?= $pago->cliente; ?></div>
            </div>
            <div class="invoice-info-item">
                <div class="invoice-info-label">FORMA DE PAGO: </div>
                <div class="invoice-info-value">
                <?php  
                    switch($pago->forma){
                        case 'TIEN':
                            echo 'TIENDA';
                        break;
                        case 'TRANF':
                            echo 'TRANSFERENCIA';
                        break;
                        case 'AGEN':
                            echo 'AGENTE';
                        break;
                        case 'DEP':
                            echo 'DEPOSITO';
                        break;
                        case 'REV':
                            echo 'DÉBITO AUT.';
                        break;
                    }
                ?>
                </div>
            </div>
            <?php if(in_array($pago->forma, ['AGEN','TRANF','DEP'])): ?>
            <div class="invoice-info-item">
                <div class="invoice-info-label">BANCO: </div>
                <div class="invoice-info-value"><?= $pago->banco_nombre; ?></div>
            </div>
            <div class="invoice-info-item">
                <div class="invoice-info-label">NRO. OPERACION: </div>
                <div class="invoice-info-value"><?= $pago->comprobante; ?></div>
            </div>
            <?php endif ?>
        </div>
        <div class="divider-dotted"></div>
        
        <div class="invoice-details">
            <table class="invoice-table">
                <thead>
                    <tr>
                        <td class="px-1">#</td>
                        <td>REFERENCIA</td>
                        <td>MONTO</td>
                    </tr>
                </thead>
                <tbody>
                <?php if (!empty($detalle)): ?>
                    <?php foreach ($detalle as $ind => $d): ?>
                    <tr class="row-data">
                        <td class="text-center px-1"><?= $ind+1; ?></td>
                        <td>
                        <p class="font-w600 mb-1">
                            <?php echo $d->referencia." ".str_replace("Vencimiento","Venc.",$d->subref); ?>
                        </p>
                        </td>
                        <td class="text-right">S/ <?php echo $d->monto; ?></td>
                    </tr>
                    <?php endforeach ?>
                <?php else: ?>
                    <tr class="row-data text-center">
                        <td colspan="3">Datos no encontrados</td>
                    </tr>
                <?php endif ?>
                </tbody>
                <div class="divider-dotted"></div>
                <tfoot>
                <tr class="row-row">
                    <td colspan="2" class="font-w600 text-right">Subtotal</td>
                    <td class="text-right">S/ <?= $pago->subtotal; ?></td>
                </tr> 
                <?php if ($pago->adicional && $pago->adicional > 0): ?>
                    <tr class="calc-row">
                        <td colspan="2" class="font-w600 text-right">Cargos Adicionales</td>
                        <td class="text-right">S/ <?= $pago->adicional; ?></td>
                    </tr>
                    <?php endif ?>
                    <tr class="calc-row">
                        <td colspan="2" class="font-w600 text-right text-s">Descuento</td>
                        <td class="text-right text-dangers">- S/ <?= $pago->descuento; ?></td>
                    </tr>
                    <tr class="calc-row">
                        <td colspan="2" class="font-weight-bold text-uppercase text-right">Total</td>
                        <td class="font-weight-bold text-right">S/ <?= $pago->total; ?></td>
                    </tr>
                    <tr class="calc-row">
                        <td colspan="2" class="text-uppercase text-right">Recibido</td>
                        <td class="text-blues text-right">S/ <?= $pago->recibido; ?></td>
                    </tr>
                    <tr class="calc-row">
                        <td colspan="2" class="text-uppercase text-right">Vuelto</td>
                        <td class="text-reds text-right">S/ <?= $pago->devuelto; ?></td>
                    </tr>
                </tfoot>           
            </table>            
        </div>
    </div>
</div>

<script src="<?= base_url() ?>js_modules/pago/pago.detalle.js"></script>
<style>
  .deck-card {
    padding: .5em;
    font-family: 'Maven Pro', sans-serif;
    border-radius: 2px;
  }
  .deck-card .deck-title {
    padding: 4px 2px;
  }
  .deck-title span:last-child {
    display: block;
    text-align: center;
    font-weight: 600;
  }
  .dflex-betw {
    display: flex;
    justify-content: space-between;
    padding: 2px 0;
  }
  .dblock {
    display: block;
    padding: 2px 0;
  }
  .divider-dash {
    border-bottom: 1px dashed rgb(173, 181, 189);
    margin: 2px 0;
  }
  .deck-table {
    width: 100%;
  }
  .deck-table td {
    padding: 4px 8px;
  }

  .pdet-list {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    list-style: none;
    display: flex;
    flex-direction: column;

  }
  .pdet-list li {
    margin: 0;
    padding: 0;
    line-height: 1em;
  }
  .pdet-list li label {
    font-weight: 600;
    display: inline-block;
    margin-right: 2px;
  }







  
</style>
