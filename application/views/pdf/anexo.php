<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Anexo 01: Detalle del Crédito</title>
    <style>
        @page {
            size: A4;
            margin: 12mm 15mm;
            margin-left: 70px;
        }
        * {
            box-sizing: border-box;
        }
        body {
            font-family: sans-serif,'Calibri','Arial', 'Helvetica', sans-serif;
            color: #000;
            margin: 0;
            padding: 0;
            font-size: 8.5pt; 
            line-height: 1.15; 
        }
        
        
        /* Sección de Encabezado */
        .header-table {
            width: 100%;
            
            border-collapse: collapse;
            margin-bottom: 5px;
        }
        .header-table td {
            vertical-align: middle;
            border: none;
        }
        .logo-container {
            width: 40%;
        }       
        .logo-title {
            font-size: 16pt;
            font-weight: bold;
            color: #034f1e;
            margin: 0;
            display: inline-block;
        }
        .logo-sub {
            font-size: 6.5pt;
            font-style: italic;
            color: #555;
            margin: 0;
            margin-top: -3px;
        }
        .logo {
            width: 30%;
            text-align: left;
            padding: 0 1rem;
        }
        .logo img {
            width: 160px;
        }
        .whatsapp-container {
            width: 34%;
            text-align: center;
            padding: 0 18px;
            font-size: 14px;
            font-weight: bold;
        }
        .serie-container {
            width: 30%;
            text-align: right;
            font-size: 13px;
            font-weight: bold;
            white-space: nowrap;
        }
        .serie-container span {
            font-weight: lighter;
        }

        /* Título del Documento */
        .doc-title {
            text-align: center;
            font-size: 22px;
            font-weight: bold;
            margin: 10px 0 15px 0;
            margin-bottom: 30px;
            /* letter-spacing: 0.5px; */
        }

        /* Liquidación y Fecha */
        .liquidation-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
            font-size: 10pt;
            font-weight: bold;
        }
        .liquidation-table td {
            border: none;
        }
        .underline-text {
            border-bottom: 1px solid #000;
            display: inline-block;
            font-weight: normal;
            /* padding-left: 10px; */
            min-width: 150px;
            width: 100px;
        }
        .date-label {
            display: inline-block;   
        }
        .date-item {
            display: inline-block;
            padding: 1rem 0;
            width: 150px;
            border-bottom: 1px solid #000;
            font-weight: normal; 
        }
        .item-left {
            float: left;
            text-align: left;
        }
        .item-right {
            right: 0;
            float: right;
            text-align: left;
        }
        .dates-header {
            display: table;
            width: 100%;
            margin-bottom: 10px;
        }
        .item-liquidacion {
            display: table-cell;
            text-align: left;
            vertical-align: middle;
            font-size: 13px;
        }
        .item-liquidacion span {
            display: inline-block;            
        }
        .item-liquidacion div {
            display: inline-block;
        }
        .item-creacion {
            display: table-cell;
            text-align: right !important;
            vertical-align: middle;
            font-size: 13px;
            width: 136px;
        }
        .item-creacion span {
            display: inline-block;
            margin-right: 6px;
        }
        .item-creacion div {
            display: inline-block;
        }



        .subline {
            border-bottom: 1px solid #000;
            width: 120px;
        }
        .subline-right {
            display: inline-block;
            border-bottom: 1px solid #000;
            width: 100px;
        }



        /* Estilo General de Tablas de Datos */
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }        
        table.data-table th, table.data-table td {
            border: 1px solid #000;
            padding: 4px 6px;
            text-align: left;
            vertical-align: middle;
        }
        table.data-table td.section-header {
            text-align: center;
            background-color: #fff;
            font-weight: bold;
            font-size: 12px;
            letter-spacing: 0.5px;
        }
        table.data-table td.label {
            font-weight: bold;
            width: 20%;
            font-size: 10px;
            text-align: right;
            padding-right: 6px;
        }
        table.data-table td.value {
            font-weight: normal;
            font-size: 11px;
        }
        table.data-metodos {
            width: 60%;
        }
        table.data-metodos td {
            font-size: 10px;
        }
        table.data-metodos td.label {
            width: 40%;
        }
        table.data-metodos td.value {
            font-size: 9px;
            text-align: center;
        }

        
        /* Contenedor de Dos Columnas compatible con dompdf */
        /* .columns-container {
            width: 100%;
            border-collapse: collapse;
            border: none;
            margin-bottom: 5px;
        }
        .columns-container > tr > td {
            border: none;
            vertical-align: top;
        }
        .left-col {
            width: 62%;
            padding-right: 12px;
        }
        .right-col {
            width: 38%;
        }

        .left-col table.data-table td.label {
            width: 50%;
        }
        .left-col table.data-prenda td.label {
            width: 30%;
        } */
        
        /* ==========================================================================
        NUEVO SISTEMA DE COLUMNAS INDEPENDIENTES (EVITA DESCUADRES)
        ========================================================================== */
        table.columns-container { 
            width: 100%; 
            border-collapse: collapse; 
            border: none; 
            margin-bottom: 5px; 
            table-layout: fixed; /* Fuerza a dompdf a respetar los anchos */
        }

        /* Quitamos bordes y forzamos alineación SOLO a las columnas maestras, NO a las tablas hijas */
        td.left-col, 
        td.right-col { 
            border: none !important; 
            vertical-align: top !important; 
            padding: 0;
        }

        td.left-col { 
            width: 61%; 
            padding-right: 12px; 
        }

        td.right-col { 
            width: 39%; 
        }

        /* Corregimos el alineamiento superior de las tablas internas */
        .left-col table.data-table,
        .right-col table.cuotas-table {
            margin-top: 0 !important;
            vertical-align: top;
        }

        .left-col table.data-table td.label { 
            width: 45%; 
        }


        
        /* Tabla de Composición de Cuotas */
        table.cuotas-table {
            width: 100%;
            border-collapse: collapse;
            text-align: center;
        }
        table.cuotas-table th, table.cuotas-table td {
            border: 1px solid #000;
            padding: 2.5px 4px;
            font-size: 8pt;
        }
        table.cuotas-table th {
            font-weight: bold;
            background-color: #fff;
        }
        table.cuotas-table td {
            text-align: center;
        }

        /* Texto de condiciones */
        .disclaimer-text {
            font-size: 9.5px;
            text-align: justify;
            margin-top: 10px;
            margin-bottom: 35px;
            line-height: 1.4;
            width: 65%;
        }

        /* Bloque de Firmas */
        .signatures-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .signatures-table td {
            border: none;
            vertical-align: bottom;
            width: 50%;
        }
        .signature-line {
            border-top: 1px solid #000;
            width: 80%;
            margin-top: 50px;
            padding-top: 5px;
            font-size: 9pt;
        }
        .signature-placeholder {
            height: 45px;
            /* font-family: 'Courier New', monospace; */
            font-size: 18pt;
            font-style: italic;
            color: #111;
            padding-left: 15px;
            line-height: 1;
        }
        .firma {
            text-align: left;
            padding-left: 15px;
        }
        .firma img {
            width: 85px;
        }
        .firma-text {
            display: block;
            font-size: 11px;
            font-weight: normal;
        }


        /* ==========================================================================
           ESTILOS ESPECÍFICOS DE LA PÁGINA 2
           ========================================================================== */
        .pagina-2 {
            page-break-before: always;
            padding-top: 2rem;
            padding-bottom: 1rem;
            padding-left: 0rem;
            padding-right: 2rem;
            color: #0e141c;
        }
        .p2-mini-header {
            /* font-size: 7.5pt; */
            text-align: center;
            font-size: 13px;
            color: #333;
            margin-bottom: 12px;
        }
        .p2-header-line {
            border-top: 1px solid #000;
            margin-bottom: 12px;
        }
        .p2-main-title {
            text-align: center;
            font-size: 11pt;
            font-weight: bold;
            margin-top: 15px;
            margin-bottom: 20px;
            letter-spacing: 0.3px;
            text-decoration: underline;
            text-decoration-thickness: 3px;
            padding-top: 1rem;
            padding-bottom: .2rem;
        }
        .p2-section-bar {
            background-color: #f2f2f2;
            border-left: 4px solid #18314e;
            padding: 4px 8px;
            font-weight: bold;
            font-size: 12px;
            margin-top: 14px;
            margin-bottom: 10px;
            letter-spacing: 0.2px;
        }
        .p2-statement-text {
            font-size: 13.5px;
            line-height: 1.7;
            text-align: justify;
            margin-bottom: 12px;
        }
        .p2-dot-span {
            display: inline-block;
            border-bottom: 1px dotted #000;
            height: 14px;
        }
        
        /* Tabla del Bien (Página 2) */
        table.p2-goods-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }
        table.p2-goods-table td {
            border: 1px solid rgba(172, 183, 195, .9);
            padding: 5px 6px;
            font-size: 8.5pt;
            vertical-align: middle;
            height: 24px;
        }
        table.p2-goods-table td.label {
            background: #f2f2f2;
            font-weight: bold;
            width: 18%;
            font-size: 13px;
        }
        table.p2-goods-table td.value-field {
            width: 32%;
        }

        /* Recuadro de Declaraciones Cláusulas */
        .p2-clauses-box {
            border: 1px solid #000;
            padding: 8px 10px;
            margin-bottom: 12px;
        }
        .p2-clause-item {
            /* font-size: 7.8pt; */
            font-size: 13px;
            text-align: justify;
            margin-bottom: 8px;
            line-height: 1.3;
        }
        .p2-clause-item:last-child {
            margin-bottom: 0;
        }

        /* Recuadro de Advertencia Legal */
        .p2-warning-box {
            border: 1px solid #000;
            background-color: #f2f2f2;
            padding: 8px 10px;
            font-size: 13px;
            text-align: justify;
            line-height: 1.35;
            margin-bottom: 25px;
        }

        /* Footer y Firma (Página 2) */
        table.p2-footer-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 60px;
        }
        table.p2-footer-table td {
            border: none;
            vertical-align: top;
            font-size: 12px;
        }
        .p2-signature-container {
            width: 250px;
            float: right;
            text-align: left;
            margin-top: 45px;
        }
        .p2-signature-line {
            border-top: 1px solid #000;
            padding-top: 4px;
        }
    </style>
</head>
<body>
    <div class="pagina-1">
        <table class="header-table">
            <tr>
                <td class="logo">
                    <img src="<?= $logo; ?>" alt="logo electrocash">
                </td>
                <td class="whatsapp-container">
                    Whatsapp: <?= $sede->telefono ?? '969 751 203'; ?> &nbsp;
                </td>
                <td class="serie-container">
                    Nº de Contrato: 
                    <span><?= $serie ?? 'no_data'; ?></span>
                </td>
            </tr>
        </table>
    
        <div class="doc-title">ANEXO 01: DETALLE DEL CREDITO</div>
        <div class="dates-header">
            <div class="item-liquidacion">
                <span>LIQUIDACION:</span> <div class="subline">&nbsp;</div>
            </div>
            <div class="item-creacion" style="text-align: right !important;">
               <span>FECHA: &nbsp;&nbsp; <?= $creacion ?? 'no_data'; ?></span> 
            </div>
        </div>
    
        <table class="data-table">
            <tr>
                <td colspan="2" class="section-header">DATOS DEL SOLICITANTE</td>
            </tr>
            <tr>
                <td class="label">NOMBRES</td>
                <td class="value"><?= $nombres ?? 'Sin Datos'; ?></td>
            </tr>
            <tr>
                <td class="label">APELLIDOS</td>
                <td class="value"><?= $apellidos ?? 'Sin Datos'; ?></td>
            </tr>
            <tr>
                <td class="label">DNI/CE</td>
                <td class="value"><?= $documento ?? 'Sin Datos'; ?></td>
            </tr>
            <tr>
                <td class="label">DIRECCION</td>
                <td class="value"><?= $direccion ?? 'Sin Datos'; ?></td>
            </tr>
            <tr>
                <td class="label">DISTRITO</td>
                <td class="value"><?= $distrito ?? 'Sin Datos'; ?></td>
            </tr>
            <tr>
                <td class="label">TELEFONOS</td>
                <td class="value"><?= $telefono ?? 'Sin Datos'; ?></td>
            </tr>
        </table>
    
        <table class="columns-container">
            <tr>
                <td class="left-col">
                    <table class="data-table">
                        <tr>
                            <td class="label">IMPORTE DEL PRESTAMO</td>
                            <td class="value">S/ <?= $monto ?? '-----'; ?></td>
                        </tr>
                        <tr>
                            <td class="label">Nº DE CUOTAS SEMANALES</td>
                            <td class="value">INDEFINIDO</td>
                        </tr>
                        <tr>
                            <td class="label">FECHA DE INICIO</td>
                            <td class="value"><?= $creacion ?? 'no_data'; ?></td>
                        </tr>
                        <tr>
                            <td class="label">FECHA DE VENCIMIENTO</td>
                            <td class="value">INDEFINIDO</td>
                        </tr>
                        <tr>
                            <td class="label">CUOTA SEMANAL</td>
                            <td class="value">S/ <?= $cuota ?? '-----'; ?></td>
                        </tr>
                        <tr>
                            <td class="label">PENALIDAD POR DIA DE ATRASO</td>
                            <td class="value">S/ <?= $penalidad ?? '-----'; ?></td>
                        </tr>
                    </table>
                    <table class="data-table" style="margin-top: 5px;">
                        <tr>
                            <td colspan="2" class="section-header">
                                DATOS DE LA PRENDA
                            </td>
                        </tr>
                        <tr>
                            <td class="label">CATEGORIA</td>
                            <td class="value">
                                <?= $categoria ?? 'Sin datos'; ?>
                            </td>
                        </tr>
                        <?php if ($prenda_tipo === 'JOYA'): ?>
                        <tr>
                            <td class="label">PESO</td>
                            <td class="value">
                                <?= $peso ?? 'Sin datos'; ?>
                            </td>
                        </tr>
                        <tr>
                            <td class="label">QUILATE</td>
                            <td class="value">
                                <?= $quilate ?? 'Sin datos'; ?>
                            </td>
                        </tr>
                        <?php else: ?>
                        <tr>
                            <td class="label">MARCA</td>
                            <td class="value">
                                <?= $marca ?? 'Sin datos'; ?>
                            </td>
                        </tr>
                        <tr>
                            <td class="label">MODELO</td>
                            <td class="value">
                                <?= $modelo ?? 'Sin datos'; ?>
                            </td>
                        </tr>
                        <tr>
                            <td class="label">SERIE</td>
                            <td class="value">
                                <?= $serial ?? 'Sin datos'; ?>
                            </td>
                        </tr>
                        <?php endif; ?>
                        
                        <tr>
                            <td class="label">ESTADO</td>
                            <td class="value">
                                <?= $valoracion ?? 'Sin datos'; ?>
                            </td>
                        </tr>
                        <tr>
                            <td class="label">OBSERVACIONES</td>
                            <td class="value" style="font-size:7.5pt;line-height:1.1;">
                                <?= $observacion ?? 'no_data'; ?>
                            </td>
                        </tr>
                        <tr>
                            <td class="label">COMENTARIOS</td>
                            <td class="value" style="font-size:7.5pt;line-height:1.1;">
                                <?= $comentario ?? 'no_data'; ?>
                            </td>
                        </tr>
                    </table>
                </td>

                <td class="right-col">
                    <div>
                        <table class="data-table" style="margin-bottom: 5px;">
                            <tr>
                                <td colspan="2" class="section-header">COMPOSICION DE LA CUOTA</td>
                            </tr>
                            <tr>
                                <td class="label" style="text-align:center; width:45%;">VALOR</td>
                                <td class="value" style="text-align:center; font-weight:bold;">
                                    S/ <?= $cuota ?? '----'; ?>
                                </td>
                            </tr>
                        </table>
                        
    
                    </div>
                    <div>
                        <table class="cuotas-table">
                            <thead>
                                <tr><th style="width: 45%;">CUOTA</th><th style="width: 55%;">FECHA</th></tr>
                            </thead>
                            <tbody>
                                <?= $week_html ?? 'no_data'; ?>
                                <!-- <tr><td>01</td><td>17-12-2025</td></tr>
                                <tr><td>02</td><td>24-12-2025</td></tr>
                                <tr><td>03</td><td>31-12-2025</td></tr>
                                <tr><td>04</td><td>07-01-2026</td></tr>
                                <tr><td>05</td><td>14-01-2026</td></tr>
                                <tr><td>06</td><td>21-01-2026</td></tr>
                                <tr><td>07</td><td>28-01-2026</td></tr>
                                <tr><td>08</td><td>04-02-2026</td></tr>
                                <tr><td>09</td><td>11-02-2026</td></tr>
                                <tr><td>10</td><td>18-02-2026</td></tr>
                                <tr><td>11</td><td>25-02-2026</td></tr>
                                <tr><td>12</td><td>04-03-2026</td></tr>
                                <tr><td>13</td><td>11-03-2026</td></tr>
                                <tr><td>14</td><td>18-03-2026</td></tr>
                                <tr><td>15</td><td>25-03-2026</td></tr> -->
                            </tbody>
                        </table>
                    </div>
                </td>
            </tr>
        </table>
    
        <table class="data-table data-metodos" style="margin-top: 5px;">
            <tr>
                <td colspan="2" class="section-header">METODOS DE PAGO</td>
            </tr>
            <tr>
                <td class="label" >Pago Directamente en Tienda</td>
                <td class="value">
                    <?= $sede->direccion ?? 'JR. VILLAVICENCIO 265, CASCO URBANO'; ?>
                </td>
            </tr>
            <tr>
                <td class="label">Banco y Tipo de Cuenta</td>
                <td class="value">BCP Ahorros Soles</td>
            </tr>
            <tr>
                <td class="label">Nro de Cuenta</td>
                <td class="value">
                    <?= $sede->cuenta_numero ?? '310-90559963-0-00'; ?>
                </td>
            </tr>
        </table>
    
        <div class="disclaimer-text">
            La prenda ha sido entregada a ELECTROCASH en el estado descrito en el presente documento,<br> con el fin de avalar el préstamo otorgado. El solicitante declara bajo juramento ser el único<br> y legítimo propietario del bien entregado en garantía, asimismo, reconoce haber leído y<br> comprendido los términos y condiciones del <b>contrato suscrito</b>.
        </div>
    
        <table class="signatures-table">
            <tr>
                <td style="text-align: left;">
                    <!-- <div class="signature-placeholder">Esf</div> -->
                    <div class="firma">
                        <img src="<?= $firma; ?>" alt="firma electrocash">
                        <b class="firma-text">ELECTROCASH</b>
                    </div>
                    <!-- <div class="signature-line" style="width: 70%;">
                        <b>ELECTROCASH</b>
                    </div> -->
                </td>
                <td style="text-align: left; padding-left: 40px;">
                    <div class="signature-line" style="width: 90%;">
                        <b>Solicitante</b><br>
                        Nombre:<br>
                        DNI:
                    </div>
                </td>
            </tr>
        </table>
    </div>
    

    <div class="pagina-2">
        
        <div class="p2-mini-header">
            Electrocash - Casa de Empeño - RUC: 10436928781 | Registro SBS N° 2018-00039293
        </div>
        <div class="p2-header-line"></div>

        <div class="p2-main-title">
            DECLARACIÓN JURADA DE PROPIEDAD Y ACTA DE DACIÓN EN PAGO
        </div>

        <div class="p2-section-bar">I. DATOS DEL SOLICITANTE</div>
        
        <div class="p2-statement-text">
            Yo, <?= $nom_ape ?? ''; ?></span>, identificado(a) con DNI/CE N° 
            <span><?= $documento ?? '00000000'; ?></span>, 
            domiciliado(a) en <span><?= $dir_dist ?? 'no_data'; ?></span>, 
            declaro bajo juramento lo siguiente:
        </div>

        <div class="p2-section-bar">II. DESCRIPCIÓN DETALLADA DEL BIEN</div>
        
        <table class="p2-goods-table">
        <?php if ($prenda_tipo === 'JOYA'): ?>
            <tr>
                <td class="label">Descripcion:</td>
                <td class="value-field"><?= $descripcion ?? 'no_data'; ?></td>
                <td class="label">Peso (g.):</td>
                <td class="value-field"><?= $peso ?? 'no_data'; ?></td>
            </tr>
            <tr>
                <td class="label">Quilates:</td>
                <td class="value-field" colspan="3"><?= $quilate ?? 'no_data'; ?></td>
            </tr>
        <?php else: ?>
            <tr>
                <td class="label">Descripcion:</td>
                <td class="value-field"><?= $descripcion ?? 'no_data'; ?></td>
                <td class="label">Marca:</td>
                <td class="value-field"><?= $marca ?? 'no_data'; ?></td>
            </tr>
            <tr>
                <td class="label">Modelo:</td>
                <td class="value-field"><?= $modelo ?? 'no_data'; ?></td>
                <td class="label">N° de Serie:</td>
                <td class="value-field"><?= $serial ?? 'no_data'; ?></td>
            </tr>
        <?php endif; ?>


            <!-- <tr>
                <td class="label">Estado (1-10):</td>
                <td class="value-field"><?= $valoracion ?? 'no_data'; ?></td>
                <td class="label">Observaciones:</td>
                <td class="value-field"><?= $observacion ?? 'no_data'; ?></td>
            </tr>
            <tr>
                <td class="label">Comentarios:</td>
                <td colspan="3"><?= $comentario ?? 'no_data'; ?></td>
            </tr> -->
        </table>

        <div class="p2-section-bar">III. DECLARACIÓN JURADA Y DACIÓN EN PAGO</div>
        
        <div class="p2-clauses-box">
            <div class="p2-clause-item">
                <b>1. PROPIEDAD Y CARGAS:</b> Declaro que el bien descrito es de mi exclusiva y legítima propiedad, y que sobre él no recae ningún tipo de gravamen, embargo, carga, ni medida judicial o administrativa que limite o impida su libre disposición para fines de empeño o transferencia.
            </div>
            <div class="p2-clause-item">
                <b>2. DACIÓN EN PAGO:</b> En caso de incumplimiento de las obligaciones pactadas en el contrato de préstamo, o por voluntad propia expresada en este acto, <b>ENTREGO EL BIEN EN DACIÓN EN PAGO</b> a favor de ELECTROCASH, conforme al Art. 1265 del Código Civil, para extinguir la totalidad de la deuda acumulada. A partir de la firma, ELECTROCASH adquiere la plena propiedad y facultad de disposición del bien.
            </div>
            <div class="p2-clause-item">
                <b>3. INDEMNIDAD:</b> Me comprometo a mantener indemne a ELECTROCASH frente a cualquier reclamo de terceros que aleguen derechos sobre el bien, asumiendo toda responsabilidad civil o penal derivada.
            </div>
        </div>

        <div class="p2-warning-box">
            <b>ADVERTENCIA LEGAL:</b> El firmante declara bajo juramento que toda la información proporcionada es verdadera. El ocultamiento de la procedencia ilícita del bien o falsear la titularidad del mismo constituye delito de Apropiación Ilícita (Art. 190 CP) y Falsedad Genérica (Art. 438 CP), ELECTROCASH se reserva el derecho de tomar acciones civiles y penales, en caso de detectarse irregularidades.
        </div>

        <table class="p2-footer-table">
            <tr>
                <td style="width: 50%; vertical-align: middle; padding-top: 10px;">
                    <?php
                        $fecha_style = $fecha_desc 
                            ? $fecha_desc
                            :  '...... de .........................de 20....';
                    ?>
                    Chimbote, <?= $fecha_style; ?>
                    <!-- Chimbote, ...... de .........................de 20.... -->
                </td>
                <td style="width: 50%;padding-top: 5px;">
                    <div class="p2-signature-container">
                        <div class="p2-signature-line">
                            <div style="text-align: center; font-weight: bold; margin-bottom: 5px;">SOLICITANTE</div>
                            <div style="text-align:center;">
                                <span style="font-size:8pt;"><?= $nom_ape ?? ''; ?></span>
                                <p style="margin-top:0;padding-top:0;">DNI: <?= $documento ?? ''; ?></p>
                            </div>
                        </div>
                    </div>
                </td>
            </tr>
        </table>

    </div>

</body>
</html>