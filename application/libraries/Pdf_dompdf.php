<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pdf_dompdf {

    public function __construct() {
        require_once 'dompdf/autoload.inc.php';
    }

    public function generate($html, $filename = 'documento.pdf', $download = true) {
        $dompdf = new \Dompdf\Dompdf([
            'defaultFont' => 'DejaVu Sans',
            'isRemoteEnabled' => true,
            'isHtml5ParserEnabled' => true,
            'isPhpEnabled' => true,
        ]);

        $dompdf->setPaper('A4', 'portrait');
        $dompdf->loadHtml($html);
        $dompdf->render();

        if ($download) {
            $dompdf->stream($filename, ["Attachment" => true]);
        } else {
            $dompdf->stream($filename, ["Attachment" => false]);
        }
    }

    public function preview($html, $filename = 'documento.pdf', $download = false) {
        $dompdf = new \Dompdf\Dompdf([
            'defaultFont' => 'DejaVu Sans',
            'isRemoteEnabled' => true,
            'isHtml5ParserEnabled' => true,
        ]);

        $dompdf->setPaper('A4', 'portrait');
        $dompdf->loadHtml($html);
        $dompdf->render();

        // $download = true  → fuerza descarga
        // $download = false → muestra en el navegador
        $dompdf->stream($filename, ["Attachment" => $download]);
    }
}