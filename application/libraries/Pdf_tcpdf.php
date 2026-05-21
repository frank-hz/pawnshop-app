<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pdf_tcpdf extends TCPDF {

    public function __construct() {
        require_once 'TCPDF/tcpdf.php';
        parent::__construct();
    }
}