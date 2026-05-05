<?php 
    class Exporter extends CI_Controller {
        function __construct(){
            parent::__construct();
            $this->load->library("MYPDF_ANEXO");
        }


        function exportAnexoPDF(){
            ob_start();
            // error_reporting(E_ALL & ~E_NOTICE);
            // ini_set("display_errors", 0);
            // ini_set("log_errors", 1);

            // echo "exportando...";

            $pdf_library = new MYPDF_ANEXO();
            $pdf = new MYPDF_ANEXO(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

            $pdf->SetCreator(PDF_CREATOR);
            $pdf->SetAuthor('ElectroCASH');
            $pdf->SetTitle('Anexo 01: Detalle del Crédito');
            $pdf->SetMargins(10, 20, 10);
            $pdf->SetHeaderMargin(5);
            $pdf->SetFooterMargin(10);
            $pdf->SetAutoPageBreak(TRUE, 10);
            $pdf->SetFont('helvetica', '', 9);
            $pdf->AddPage();

            // Título
            $pdf->SetFont('helvetica', 'B', 12);
            $pdf->Cell(0, 10, 'ANEXO 01: DETALLE DEL CREDITO', 0, 1, 'C');
            $pdf->Ln(5);

            // Tabla de datos del solicitante
            $pdf->SetFont('helvetica', '', 9);
            $html = '
            <table border="1" cellspacing="3" cellpadding="4">
                <tr>
                    <th colspan="4"><b>DATOS DEL SOLICITANTE</b></th>
                </tr>
                <tr>
                    <td><b>NOMBRES</b></td>
                    <td>LUCERO ANGELA</td>
                    <td><b>APELLIDOS</b></td>
                    <td>VEGA GONZALES</td>
                </tr>
                <tr>
                    <td><b>DNI/CE</b></td>
                    <td>61109961</td>
                    <td><b>DIRECCION</b></td>
                    <td>AV PIURA C.M.P CAMBIO PUENTE M22 LT3</td>
                </tr>
                <tr>
                    <td><b>DISTRITO</b></td>
                    <td>Chimbote</td>
                    <td><b>TELEFONOS</b></td>
                    <td>971520416 / 912674993</td>
                </tr>
            </table>
            ';

            $pdf->writeHTML($html, true, false, false, false, '');

            $html = '
            <table border="1" cellspacing="3" cellpadding="4">
                <tr>
                    <th colspan="4"><b>DATOS DEL PRÉSTAMO</b></th>
                </tr>
                <tr>
                    <td><b>IMPORTE DEL PRÉSTAMO</b></td>
                    <td>S/ 350.00</td>
                    <td><b>Nº DE CUOTAS SEMANALES</b></td>
                    <td>INDEFINIDO</td>
                </tr>
                <tr>
                    <td><b>FECHA DE INICIO</b></td>
                    <td>09/10/2024</td>
                    <td><b>CUOTA SEMANAL</b></td>
                    <td>S/ 24.50</td>
                </tr>
                <tr>
                    <td><b>PENALIDAD POR DÍA DE ATRASO</b></td>
                    <td>S/ 2.00</td>
                    <td><b>FECHA DE VENCIMIENTO</b></td>
                    <td>INDEFINIDO</td>
                </tr>
            </table>
            ';

            $pdf->writeHTML($html, true, false, false, false, '');
            // Salida del archivo
            $pdf->Output('detalle_credito.pdf', 'I');
        }
        
        function export2(){
            $this->load->library('Tcpdf');
            $html = $this->load->view("credito/anexo", [], TRUE);
            $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, "a4", true, 'UTF-8', false);

            // Configurar propiedades del documento
            $pdf->SetCreator(PDF_CREATOR);
            $pdf->SetAuthor('ElectroCASH');
            $pdf->SetTitle('Anexo 01: Detalle del Crédito');
            $pdf->SetMargins(10, 20, 10);
            // $pdf->SetHeaderMargin(5);
            // $pdf->SetFooterMargin(10);
            $pdf->SetAutoPageBreak(TRUE, 10);
            $pdf->SetFont('helvetica', '', 10);

            // Agregar página
            $pdf->AddPage();

            // Escribir contenido HTML en el PDF
            $pdf->writeHTML($html, true, false, true, false, '');

            // Salida del PDF
            $pdf->Output('detalle_credito.pdf', 'I');
        }

        function export3(){
            $this->load->library('Dompdf_lib');

            // Cargar contenido HTML desde una vista
            $html = $this->load->view('credito/anexo', [], true);

            // Generar PDF
            $this->dompdf_lib->generate($html, 'reporte.pdf', true);
        }
    }