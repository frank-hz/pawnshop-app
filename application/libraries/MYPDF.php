<?php 
    defined('BASEPATH') OR EXIT('No direct script access allowed');
    require_once('TCPDF/tcpdf.php');
    
    class MYPDF extends TCPDF {
        public function __construct(){
            parent::__construct();
        }
        public function Header() {
	       	$image_file = K_PATH_IMAGES.'electrocash_logo.png';
	        $this->Image($image_file, 7, 2, 28, '', 'PNG', '', 'T', false, 300, '', false, false, 0, false, false, false);
	        $this->SetFont('helvetica', 'N', 6);
	        $this->Cell(173, 56, date("d/m/Y"), 0, false, 'R', 0, '', 0, true, 'C', 'M');
	    }
        public function Footer(){
	        $this->SetY(-10);
	        $this->SetFont('helvetica', 'I', 7);
	        $this->Cell(0, 10, 'Pag. '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');	        
	    }
    }
?>