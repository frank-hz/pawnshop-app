<?php  
	
	class Tarifa extends CI_Controller
	{
		
		private $sucursal;
		private $sede_id;
		function __construct(){			
			parent::__construct();
			if (!$this->session->userdata('pres_ses_sesion')) {
				redirect(base_url().'Acceso','refresh');
			}
			$this->sucursal = $this->session->userdata('pres_ses_sucursal');
			$this->sede_id = $this->session->userdata('sucursal_id');
			$this->load->model('TarifaModel');
			$this->load->model('PermisoModel');
			$this->load->model('CreditoModel');
		}

		function index(){
			$data['tarifas'] = $this->TarifaModel->Lista($this->session->userdata('pres_ses_sucursal'));
			$this->load->view('Layout/header.php');
			$this->load->view('Tarifa/lista.php', $data);
			$this->load->view('Layout/footer.php');
		}

		function consulta(){
			$sucursal = $this->session->userdata('pres_ses_sucursal');
			$resp = $this->TarifaModel->Lista($sucursal);
			echo json_encode($resp);
		}

		function consulta_detalle(){
			$resp = $this->TarifaModel->Buscar($_POST['id']);
			echo json_encode($resp);
		}

		function editar($id = null){
			if ($id != null) {
				$data['tarifa'] = $this->TarifaModel->consulta('detalle', $id);
				$this->load->view('Layout/header.php');
				$this->load->view('Tarifa/editar.php', $data);
				$this->load->view('Layout/footer.php');
			}else{
				$data['titulo'] = 'Tarifa desconocida';
				$data['mensaje'] = 'No se encontraron datos para el seguimiento de la tarifa';
				$this->load->view("Layout/header.php");
				$this->load->view("Error/no_found.php", $data);
				$this->load->view("Layout/footer.php");
			}
		}

		function registrar(){
			$resp = [];
			if ($this->session->userdata('pres_ses_rol') === 'Administrador') {
				$valida = $this->validate_registro($_POST);
				if (empty($valida)) {
					$this->TarifaModel->Insertar(array(
						'tar_minimo' => $_POST['minimo'],
						'tar_maximo' => $_POST['maximo'],
						'tar_tipo' => $_POST['tipo'],
						'tar_interes' => $_POST['interes'],
						'tar_mora' => $_POST['mora'],
						'tar_sucursal' => $this->session->userdata('pres_ses_sucursal')
					));
					$id = $this->db->insert_id();
					$resp = array(
						'status'=>'OK',
						'message'=>'Tarifa registrada',
						'id' => $id
					);
					// die("Error");
				}else{
					$resp = array(
						'status'=>'WARN',
						'message'=>'Errores encontrados',
						'error' => $valida
					);
				}
			}else{
				$resp = array('status'=>'WARN','message'=>'Permisos insuficientes');
			}
			echo json_encode($resp);
		}

		function modificar(){
			$resp = [];
			if ($this->session->userdata('pres_ses_rol') === 'Administrador') {
				$validar = $this->validate_modifica($_POST);
				if (empty($validar)) {
					$this->TarifaModel->Modificar(
						$_POST['id'],
						array(
							'tar_tipo' => $_POST['tipo'],
							'tar_interes' => $_POST['interes'],
							'tar_mora' => $_POST['mora'],
							'tar_mora' => $_POST['mora'],
							'tar_minimo' => $_POST['minimo'],
							'tar_maximo' => $_POST['maximo']
						)
					);
					$msg = ($this->db->affected_rows() > 0) ? 'Cambios aplicados' : 'Sin cambios';
					$resp = array(
						'status' => 'OK',
						'message' => $msg
					);

					// if ($upd->codigo === 'Ok') {
					// 	$creditos = $this->TarifaModel->consulta('credito_relacion', $post['codigo']);
					// 	if (!empty($creditos)) {
					// 		foreach ($creditos as $cred) {
					// 			$rec = $this->TarifaModel->recalibrarCreditos(
					// 				array(
					// 					$cred->id,
					// 					$cred->monto,
					// 					$cred->tasa,
					// 					$cred->cargo,
					// 					$cred->interes,
					// 					$cred->mora,
					// 					$cred->tipo
					// 				)
					// 			);
					// 		}
							
					// 	}

					// 	$resp['code'] = $upd->codigo;
					// 	$resp['message'] = $upd->mensaje;
					// }else{
					// 	$resp['code'] = 'Error';
					// 	$resp['message'] = 'No se pudo completar el proceso.';
					// }
				}else{
					$resp['status'] = 'WARN';
					$resp['message'] = 'Errores encontrados';
					$resp['error'] = $validar;
				}
			}else{
				$resp['status'] = 'WARN';
				$resp['message'] = 'Permisos insuficientes';
			}
			echo json_encode($resp);
		}

		function eliminar(){
			$resp = [];
			if ($this->session->userdata('pres_ses_rol') === 'Administrador') {
				$verifica = $this->CreditoModel->Buscar_libre(
					'count(*) as cantidad', 
					array('cred_tarifa' => $_POST['id'])
				);
				if ($verifica->cantidad == 0) {
					$this->TarifaModel->Eliminar($_POST['id']);
					if ($this->db->affected_rows() > 0) {
						$resp = array('status'=>'OK','message'=>'Tarifa eliminada');
					}else{
						$resp = array('status'=>'ERROR','message'=>'Proceso incompleto');
					}
				}else{
					$resp = array('status'=>'WARN','message'=>'La eliminacion de esta tarifa no esta permitida');
				}
			}else{
				$resp = array('status'=>'WARN','message'=>'Permisos insuficientes');
			}
			echo json_encode($resp);
		}

		function validate_modifica($data){
			$error = [];
			if(empty($data['id'])) $error['id'] = 'Tarifa desconocida';
			if(empty($data['tipo'])) $error['tipo'] = 'Tipo de tarifa requerida';
			// if(empty($data['mora'])) $error['mora'] = 'Mora no definida';
			// if(empty($data['interes'])) $error['interes'] = 'Interes no definido';

			if (!empty($data['minimo']) && !empty($data['maximo'])) {
				$repetido = $this->TarifaModel->Buscar_libre(
					'count(*) as cantidad', 
					"tar_maximo = '".$data['maximo']."' AND tar_minimo = '".$data['minimo']."' AND 
					tar_sucursal = '".$this->session->userdata('pres_ses_sucursal')."' AND tar_codigo != '".$data['id']."'"
				);
				if ($repetido->cantidad > 0) {
					$error['repetido'] = 'Ya existe una tarifa con las limitantes minima y maxima';
				}
			}else{
				if (empty($data['minimo'])) $error['minimo'] = 'Limitante minima no definida';
				if (empty($data['maximo'])) $error['maximo'] = 'Limitante maximo no definida';
			}
			return $error;
		}

		function validate_registro($data){
			$error = [];
			if(empty($data['tipo'])) $error['tipo'] = 'Tipo de tarifa requerida';
			// if(empty($data['mora'])) $error['mora'] = 'Mora no definida';
			// if(empty($data['interes'])) $error['interes'] = 'Interes no definido';

			if (!empty($data['minimo']) && !empty($data['maximo'])) {
				$repetido = $this->TarifaModel->Buscar_libre('count(*) as cantidad', array(
					'tar_maximo' => $data['maximo'],
					'tar_minimo' => $data['minimo'],
					'tar_sucursal' => $this->session->userdata('pres_ses_sucursal')
				));
				if ($repetido->cantidad > 0) {
					$error['repetido'] = 'Ya existe una tarifa con las limitantes minima y maxima';
				}
			}else{
				if (empty($data['minimo'])) $error['minimo'] = 'Limitante minima no definida';
				if (empty($data['maximo'])) $error['maximo'] = 'Limitante maximo no definida';
			}
			return $error;
		}

		// NEWCODE
		function service_calcular_tarifa(){
			$suc = $this->session->userdata('pres_ses_sucursal');
			$file = file_get_contents("php://input");
			$body = json_decode($file);
			$data = [];
			if (isset($body->monto)) {
				$monto = round($body->monto, 2);
				$tar = $this->TarifaModel->get_tarifa($monto, $this->sucursal);
				if ($tar) {
					$data['tipo'] = $tar->tipo;
					$data['interes'] = floatval($tar->interes) + floatval($body->cargo);
					
					$data['penalidad'] = round($tar->mora, 2);

					$data['cuota'] = ($tar->tipo === 'P') ? $this->calc_porcentual($monto, $data['interes']) : floatval($tar->interes);
					$data['mora'] = round($tar->mora, 2);
					$data['result_raw'] = json_encode($tar);
				}
			}
			echo json_encode($data);
		}
		function service_calcular_tarifa_v2(){
			$suc = $this->session->userdata('pres_ses_sucursal');
			$file = file_get_contents("php://input");
			$form = json_decode($file);
			$data = [];
			die(json_encode($_POST['monto']));
			if (isset($form->monto)) {
				$monto = round($form->monto, 2);
				$cargo = floatval($form->cargo);
				$tar = $this->TarifaModel->get_tarifa($monto, $this->sucursal);

				if ($tar) {
					$tipo = $tar->tipo;
					$interes = floatval($tar->interes) + $cargo;					
					$penalidad = round($tar->mora, 2);
					$cuota = $interes;
					if ($tipo === 'P') {
						$cuota = ($monto * $interes) / 100;
					}
					$mora = round($tar->mora, 2);
					$data = [
						'monto' => $monto,
						'tipo' => $tipo,
						'interes' => $interes,
						'penalidad' => $penalidad,
						'cuota' => $cuota,
						'mora' => $mora
					];
				}
			}
			echo json_encode($data);
		}
		
		// function service_recalculo(){
		// 	$data = json_decode(file_get_contents('php://input'));
		// 	if (!$data || !$data->monto, $data->tipo_tasa, $data->interes)
		// }
		
		function calc_porcentual($monto, $p){
			$cal = ($monto * $p) / 100;
			// $cal = (($monto * $p) / 100);
			return $cal;
		}


		// A P I 
		function api_tarifa_evaluation(){
			try {
				if (!isset($_POST['monto']) || !isset($_POST['cargo'])){
					throw new Exception('Datos requeridos incompletos', 99);
				}
				
				$monto = $_POST['monto'];
				$cargo = $_POST['cargo'];
				if ($monto <= 0) {
					throw new Exception('Monto invalido', 99);
				}
				$tarifas = $this->TarifaModel->getAllBySede($this->sede_id);
				if (empty($tarifas)){
					throw new Exception('Ocurrio un problema al solicitar tarifas', 99);
				}

				$tarifa_found = null;
				foreach ($tarifas as $tarifa) {
					if ($monto >= $tarifa->minimo && $monto <= $tarifa->maximo){
						$tarifa_found = $tarifa;
					}
				}
				if (empty($tarifa_found)){
					throw new Exception('Tarifas aplicables no encontradas', 99);
				}

				$tipo = $tarifa_found->tipo;
				$interes = floatval($tarifa_found->interes) + $cargo;					
				$penalidad = round($tarifa_found->mora, 2);
				$cuota = $interes;
				if ($tipo === 'P') {
					$cuota = ($monto * $interes) / 100;
				}
				$mora = round($tarifa_found->mora, 2);
				echo json_encode([
					'ok' => 'PROCESS_OK',
					'monto' => $monto,
					'tipo' => $tipo,
					'interes' => $interes,
					'penalidad' => $penalidad,
					'cuota' => $cuota,
					'mora' => $mora
				]);
				
			} catch (Exception $e) {
				$code = $e->getCode();
				$mssg = $code === 99 ? $e->getMessage() : 'ERROR_PROCESS';
				die(json_encode([
					'error' => $mssg,
					'log' => $e->getMessage()
				]));
			}
		}

	}
?>
