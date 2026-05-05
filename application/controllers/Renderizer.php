<?php 
    class Renderizer extends CI_Controller
    {
        function __construct(){
            parent::__construct();
            if (!$this->session->userdata("pres_ses_sesion")) {
				redirect(base_url()."Acceso","refresh");
			}
            $this->load->library("TCPDF");
            $this->load->model("PagoModel");
        }

        function voucher_tcpdf($id){
            ob_start();
            error_reporting(E_ALL & ~E_NOTICE);
            ini_set("display_errors", 0);
            ini_set("log_errors", 1);
            // $id = "2985";
            $dpago = $this->PagoModel->getPagoDetail($id);
            $ddetalle = $this->PagoModel->obtener_detalle($id);
            
            // print_r($ddetalle);
            $pdf = new TCPDF("P", "mm", "A4", true, "UTF-8", false);
            $pdf->SetCreator("Electrocash");

            $year = date("Y");
            $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, "ELECTROCASH", "Electrocashperu.com - $year");

            $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, "", PDF_FONT_SIZE_MAIN));
            $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, "", PDF_FONT_SIZE_DATA));
            $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
            $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
            $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
            $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
            $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
            $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

            // set some language-dependent strings (optional)
            if (@file_exists(dirname(__FILE__)."/lang/eng.php")) {
                require_once(dirname(__FILE__)."/lang/eng.php");
                $pdf->setLanguageArray($l);
            }

            $pdf->SetFont("helvetica", "R", 14);
            $pdf->AddPage();
            $pdf->Write(0, "Pago de Credito", "", 0, "C", true, 0, false, false, 0);
            $pdf->Ln();
            $pdf->SetFont("helvetica", "", 9);

            $concepto = "NO DEFINIDO";
            if ($dpago && strlen($dpago->concepto) > 0) {
                switch($dpago->concepto) {
                    case "REC":
                        $concepto = "RECUPERACION";
                        break;
                    case "REG":
                        $concepto = "REGULARIZACION";
                        break;
                    case "AMOR":
                        $concepto = "AMORTIZACION";
                        break;
                    case "ADEL":
                        $concepto = "ADELANTO";
                        break;
                    case "ADEL_AUTO":
                        $concepto = "ADELANTO AUTOMATICO";
                        break;
                    case "DEB_AUTO":
                        $concepto = "DEBITACION AUTOMATICA";
                        break;
                }
            }
            $voucher_header = '            
                <table class="table-info">
                    <tr>
                        <td colspan="2">
                            CREDITO: '.$dpago->codigo_credito.'
                        </td>
                        <td colspan="2" style="text-align: right">
                            PAGO: '.$dpago->serie.'
                        </td>
                    </tr>
                    <tr>
                        <td style="width:140px;">
                            CONCEPTO:
                        </td>
                        <td colspan="3">'.$concepto.'</td>
                    </tr>
                    <tr>
                        <td style="width:140px;">
                            CLIENTE: 
                        </td>
                        <td colspan="3">'.$dpago->cliente.'</td>
                    </tr>
                    <tr>
                        <td style="width:140px;">
                            FECHA: 
                        </td>
                        <td colspan="3">'.$dpago->fecha.'</td>
                    </tr>
                    <tr>
                        <td style="width:140px;">
                            EMPLEADO ATEND.: 
                        </td>
                        <td colspan="3">'.$dpago->empleado.'</td>
                    </tr>
                </table>
                <br/>
                <style>   
                    .table-info {
                        width: 100%;
                    }         
                    .table-info td {
                        text-align: left;
                        padding: 4px;
                    }
                    .table-info tr td:first-child {
                        max-width: 100px;
                        color: red;
                    } 
                    .label-block {
                        display: inline-block;
                    }
                    .line-dashed {
                        width: 100%;
                        padding: 6px;
                        display: block;
                        border-top: 1px dashed black;
                    }
                    .text-left {
                        text-align: left !important;
                    }
                    .text-right {
                        text-align: right !important;
                    }
                    .text-center {
                        text-align: center !important;
                    }
                    tr.file-dashed td {
                        border-top: 1px dashed #343a40;
                    }
                </style>
            ';
            $pdf->writeHTML($voucher_header, true, false, false, false, "");

            $data = "";
            foreach ($ddetalle as $ind => $d) {
                $index = $ind+1;
                $data .= '
                    <tr>
                        <td>
                            <span style="padding: 0 6px;">'.$index.'</span>&nbsp;
                            '.$d->referencia.' '.$d->subref.'
                        </td>
                        <td colspan="2" style="text-align: right;">S/ '.$d->monto.'</td>
                    </tr>
                ';
            }

            $voucher_data = '
                <div class="data_container">
                    <table class="table_data" style="width:100%" cellspacing="0" cellpadding="1" border="0">
                        '.$data.'
                    </table>
                </div>
                <style>
                    .table_data {
                        min-width: 100%;
                        display: block;
                    }
                    .table_data td {
                        padding: 4px;
                    }
                    .index-cell {
                        border: 1px solid blue;
                    }
                </style>
            ';
            $pdf->writeHTML($voucher_data, true, false, false, false, "");

            $voucher_body = '            
                <table cellspacing="0" cellpadding="1" border="0">
                    <tr class="file-dashed">
                        <td colspan="2" class="font-w600 text-right">Subtotal</td>
                        <td class="text-right">S/ '.$dpago->subtotal.'</td>
                    </tr>          
                    <tr>
                        <td colspan="2" class="font-w600 text-right">Descuento</td>
                        <td class="text-right text-red">- S/ '.$dpago->descuento.'</td>
                    </tr>
                    <tr>
                        <td colspan="2" class="font-bold text-uppercase text-right">Total</td>
                        <td class="font-bold text-right">S/ '.$dpago->total.'</td>
                    </tr>
                    <tr>
                        <td colspan="2" class="text-uppercase text-right">Recibido</td>
                        <td class="text-blue text-right">S/ '.$dpago->recibido.'</td>
                    </tr>
                    <tr>
                        <td colspan="2" class="text-uppercase text-right">Vuelto</td>
                        <td class="text-right">S/ '.$dpago->devuelto.'</td>
                    </tr>
                </table>
                <br/>
                <style>
                    tr.file-dashed td {
                        border-top: 1px dashed #343a40;
                    }
                    .text-red {
                        color: #e01e37;
                    }
                    .text-green {
                        color: #2eb265;
                    }
                    .text-blue {
                        color: #195eff;
                    }
                    .text-left {
                        text-align: left !important;
                    }
                    .text-right {
                        text-align: right !important;
                    }
                    .text-center {
                        text-align: center !important;
                    }
                    .font-bold {
                        font-weight: bold;
                    }
                    .text-uppercase {
                        text-tranform: uppercase;
                    }
                </style>
            ';

            $pdf->writeHTML($voucher_body, true, false, false, false, "");
            $pdf->Output("electrocash_voucher_pago_$dpago->serie.pdf", "I");

        }

        
    }
    
?>