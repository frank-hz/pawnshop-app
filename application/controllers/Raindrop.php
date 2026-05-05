<?php  
	/**
	 * 
	 */
	class Raindrop extends CI_Controller
	{
		
		function __construct()
		{
			parent::__construct();
			$this->load->model('CreditoModel');
			$this->load->library('pdf');
		}

		function contrato($id = null){
			if ($id != null) {
				$credito = $this->CreditoModel->contrato_detalle($id);
				$prenda = $this->CreditoModel->contrato_prenda($id);
				$estilo = base_url()."assets/css/contrato.v2.css";
				$font = base_url()."assets/fonts/fontawesome/css/all.css";
				$imagen = base_url()."assets/images/electrocash_logo.png"; 

				$html = "<!DOCTYPE html>
				<html lang='es'>
				<head>
					<meta name='viewport' content='width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0'>
					<meta charset='UTF-8'>
					<title>Contrato </title>
					<link rel='stylesheet' href=$font>
					<link rel='stylesheet' href=$estilo>
				</head>
				<body>
					<table class='contrato'>
						<tr class='header'>
							<td class='logo'>
								<img src=$imagen alt=''>
							</td>
							<td class='wsp'>
								<h5>
									Whatsapp: 969751203&nbsp;
									<i class='fab fa-whatsapp'></i>
								</h5>
							</td>
							<td class='codigo'>
								<p>Nº de Contrato: #$credito->codigo</p>
							</td>
						</tr>
						<tr class='title'>
							<td colspan='3'>
								<h4>ANEXO 01: DETALLE DE CREDITO</h4>
							</td>
						</tr>
						<tr class='sub'>
							<td class='liquida' >
								<h5>LIQUIDACION:</h5> 
							</td>
							<td></td>
							<td class='fecha'>
								FECHA: 
							</td>
						</tr>
					</table>
				</body></html>";
				$this->pdf->loadHtml($html);
				$this->pdf->setPaper('A4', 'potrait');
				$this->pdf->render();
				$this->pdf->stream("Contrato.pdf",array("Attachment" => false));


			}
		}
	}
?>