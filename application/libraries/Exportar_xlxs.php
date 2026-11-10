<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require_once APPPATH . 'libraries/Exportador_interface.php';

use OpenSpout\Writer\Common\Creator\WriterEntityFactory;   // OpenSpout 3.x (compatible con PHP 7.4)

/** Un libro .xlsx con una hoja por tabla. Escritura en streaming (poca memoria). */
class Exportador_xlsx implements Exportador_interface {
    private $writer;
    private $primera = TRUE;
    private $columnas = [];
    private $numericas = [];

    public function iniciar($nombre)
    {
        while (ob_get_level()) { ob_end_clean(); }
        $this->writer = WriterEntityFactory::createXLSXWriter();
        $this->writer->openToBrowser($nombre . '.xlsx');
    }

    public function iniciar_tabla($tabla, array $columnas, array $numericas)
    {
        if (!$this->primera) { $this->writer->addNewSheetAndMakeItCurrent(); }
        $this->primera = FALSE;

        $nombreHoja = substr(preg_replace('/[\[\]\:\*\?\/\\\\]/', '_', $tabla), 0, 31);
        $this->writer->getCurrentSheet()->setName($nombreHoja);

        $this->columnas  = $columnas;
        $this->numericas = $numericas;
        $this->writer->addRow(WriterEntityFactory::createRowFromArray($columnas));
    }

    public function escribir_fila(array $fila)
    {
        $out = [];
        foreach ($this->columnas as $c) {
            $v = $fila[$c];
            // MySQL entrega todo como texto: convertir a número para poder analizar
            if ($v !== NULL && $v !== '' && in_array($c, $this->numericas, TRUE) && is_numeric($v)) {
                $v = $v + 0;
            }
            $out[] = $v;
        }
        $this->writer->addRow(WriterEntityFactory::createRowFromArray($out));
    }

    public function finalizar_tabla() { /* nada: la hoja ya quedó escrita */ }

    public function finalizar() { $this->writer->close(); }
}
