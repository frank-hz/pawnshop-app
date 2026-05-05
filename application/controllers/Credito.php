<?php

	class Credito extends CI_Controller
	{

		function __construct()
		{
			parent::__construct();
			if (!$this->session->userdata('pres_ses_sesion')) {
				redirect(base_url().'Acceso','refresh');
			}
			$this->load->model('CreditoModel');
			$this->load->model('CuotaModel');
			$this->load->model('PrendaModel');
			$this->load->model('MoraModel');
			$this->load->model('BancoModel');
			$this->load->model('PermisoModel');
			$this->load->model('NotaCreditoModel');
			$this->load->model('SucursalModel');
			$this->load->model('AdelantoModel');
			$this->load->model('DebitacionModel');
			$this->load->model('PagoModel');
			$this->load->model('AlertaClienteModel');

			$this->load->model('Credito_bzModel');
			$this->load->model('ClienteAPIModel');
			$this->load->model('Prenda_apiModel');
			// Helpers
			$this->load->helper('prenda_util');
		}

		function index(){
			$this->load->view('Layout/header.php');
			$this->load->view('Credito/index.php');
			$this->load->view('Layout/footer.php');
		}

		function nuevo(){
			if (!permission_validate('credito_registrar')) {
				error_render_view('Permisos Insuficientes','El usuario actual no tiene los privilegios suficientes para registrar creditos.');
				return;
			}
			$data = [];
			if (!empty($_GET['cliente'])) {
				$cliente = $this->ClienteAPIModel->getById($_GET['cliente']);
				if (!empty($cliente)) {
					$data['cliente'] = $cliente;
				}
			}
			$this->load->view("Layout/header.php");
			$this->load->view("Credito/insert.php", $data);
			$this->load->view("Layout/footer.php");
		}

		function detalle_v1($id = null){
			ini_set('display_errors', 0);
			if ($id != null) {
				// $credito = $this->CreditoModel->Buscar_detalle($id);
				$credito = $this->CreditoModel->get_data($id);
				if (!empty($credito)) {
					$fecha = date("Y-m-d");
					$data['elimina_opcion'] = ($fecha == $credito->fecha) ? 'SI' : 'NO';
					$prox_fecha = '---';
					// $data['cliente_clas'] = $this->ClasificacionModel->Buscar($credito->cliente_id);
					if ($credito->estado === 'E') {
						$ultima_cuota = $this->CuotaModel->Buscar_libre('cuo_vencimiento as vence, cuo_estado as estado', array(
							"cuo_credito" => $id), 'file', 1, 'DESC', 'RESET');
						if ($ultima_cuota->estado === 'E') {
							$prox_fecha = $ultima_cuota->vence;
						}else{
							$ciclo = $credito->ciclo_pago;
							if ($ciclo === 'MENSUAL' || intval($ciclo) > 7) {
								$prox_fecha = date("Y-m-d", strtotime($ultima_cuota->vence. " + 30 days"));
							}else {
								$prox_fecha = date("Y-m-d", strtotime($ultima_cuota->vence. " + 7 days"));
							}
						}
					}
					$atraso = $this->CuotaModel->get_cantidad_atraso($id)->cantidad;
					$data['atraso'] = ($atraso) ? $atraso : 0;
					$data['credito'] = $credito;
					$data['proximo_vencimiento'] = date("d-m-Y", strtotime($prox_fecha));
					if ($credito->prenda_id && $credito->prenda_id > 0) {
						mysqli_next_result( $this->db->conn_id );
						$data['prenda'] = $this->PrendaModel->Buscar_detalle($credito->prenda_id);
					}
					$this->load->view("Layout/header.php");
					$this->load->view("Credito/detalle_trash.php", $data);
					$this->load->view("Layout/footer.php");
				}else{
					$data['titulo'] = 'Credito desconocido';
					$data['mensaje'] = 'No se encontraron datos para el seguimiento del credito';
					$this->load->view("Layout/header.php");
					$this->load->view("Error/no_found.php", $data);
					$this->load->view("Layout/footer.php");
				}
			}else{
				$data['titulo'] = 'Credito desconocido';
				$data['mensaje'] = 'No se encontraron datos para el seguimiento del credito';
				$this->load->view("Layout/header.php");
				$this->load->view("Error/no_found.php", $data);
				$this->load->view("Layout/footer.php");
			}
		}

		function detalle($id = null){
			ini_set('display_errors', 0);
			$credito = $this->Credito_bzModel->getById($id);
			if (empty($credito)) {
				error_render_view('Credito Desconocido', '');
			}
			if (!$credito->pago_proximo) {
				$fbase = $credito->cuota_last;
				$ciclo = $credito->ciclo_pago;
				$prox_calculo = date("Y-m-d", strtotime($fbase. " + $ciclo days"));
				$credito->pago_proximo = date("d-m-Y", strtotime($prox_calculo));
			}

			$ciclos = ['30' => 'MENSUAL','7' => 'SEMANAL'];
			$ttasas = ['P' => 'PORCENTUAL', 'F' => 'FIJO'];
			$estado_tags = [
				'E' => "<span class='etag etag-cred-E'>Activo</span>",
				'R' => "<span class='etag etag-cred-R'>Recuperado</span>",
				'A' => "<span class='etag etag-cred-A'>Remate</span>",
				'CC' => "<span class='etag etag-cred-CC'>Congelado</span>"
			];
			$credito->ciclo_pago_display = $ciclos[$credito->ciclo_pago];
			$credito->tasa_display = ($credito->tipo_tasa=='P') ? $credito->tasa.'%' : 'S/ '.$credito->tasa;
			$credito->tipo_tasa_display = $ttasas[$credito->tipo_tasa];
			$credito->estado_tag = $estado_tags[$credito->estado];
			$credito->url = base_url().'detalle/'.$credito->id;
			$credito->empleado_url = base_url().'empleado/detalle/'.$credito->empleado_id;
			$credito->cliente_url = base_url().'cliente/detalle/'.$credito->cliente_id;
			$data['credito'] = $credito;
			$cliente = $this->ClienteAPIModel->getById($credito->cliente_id);
			$cliente->url = base_url().'cliente/detalle/'.$cliente->id;
			$data['cliente'] = $cliente;
			//$data['delete_action'] = (date("Y-m-d") == $credito->fecha_creacion_raw);
			$data['delete_action'] = $this->session->userdata('pres_ses_rol') === 'Administrador';

			$prenda = $this->Prenda_apiModel->getById($credito->prenda_id);
			if ($prenda) {
				$prenda->url = base_url().'prenda/detalle/'.$prenda->id;
				$data['prenda'] = $prenda;
				$data['prenda_data_string'] = prendau_data_to_string($prenda);
			}

			$this->load->view("Layout/header.php");
			$this->load->view("Credito/detalle.php", $data);
			$this->load->view("Layout/footer.php");
		}

		function editar($id){
			$permiso = $this->PermisoModel->verificar_permiso(
				$this->session->userdata('pres_ses_usuario'),
				'credito_editar'
			);
			if ( $permiso && $permiso->codigo == 'P' ) {
				$detalle = $this->CreditoModel->Buscar_detalle($id);
				if ($detalle != null) {
					$data['credito'] = $detalle;
					$this->load->view("Layout/header.php");
					$this->load->view("Credito/editar.php", $data);
					$this->load->view("Layout/footer.php");
				}else{
					$data['titulo'] = 'Credito desconocido';
					$data['mensaje'] = 'Datos del credito no encontrados.';
					$this->load->view("Layout/header.php");
					$this->load->view("Error/no_access.php", $data);
					$this->load->view("Layout/footer.php");
				}
			}else{
				$data['titulo'] = 'Permisos insuficientes';
				$data['mensaje'] = 'El usuario actual no tiene los privilegios suficientes para registrar creditos.';
				$this->load->view("Layout/header.php");
				$this->load->view("Error/no_access.php", $data);
				$this->load->view("Layout/footer.php");
			}
		}

		function contrato($id = null){
			if ($id != null) {
				$id_suc  = $this->session->userdata('pres_ses_sucursal');
				$data['credito'] = $this->CreditoModel->contrato_detalle($id);
				$data['prenda'] = $this->CreditoModel->contrato_prenda($id);
				if (!empty($data['credito'])) {
					$this->load->view('Credito/contrato.php', $data);
				}else{
					$data['titulo'] = 'Credito Desconocido';
					$data['mensaje'] = 'No se encontraron los datos del credito';
					$this->load->view("Layout/header.php");
					$this->load->view("Error/no_access.php", $data);
					$this->load->view("Layout/footer.php");
				}
			}else{
				$data['titulo'] = 'Credito Desconocido';
				$data['mensaje'] = 'No se encontraron los datos del credito';
				$this->load->view("Layout/header.php");
				$this->load->view("Error/no_access.php", $data);
				$this->load->view("Layout/footer.php");
			}
		}

		function movimientos($id){
			if ($id != null) {
				$credito = $this->CreditoModel->Buscar_libre(
					'cred_codigo as id, cred_serie as codigo, cred_estado as estado',
					array('cred_codigo' => $id)
				);
				if (!empty($credito)) {
					$data['credito'] = $credito;
					// $data['entrada'] = $this->AdelantoModel->Buscar_entrada($id);
					// $data['salida'] = $this->AdelantoModel->Buscar_salida($id);
					$data['debitacion'] = $this->DebitacionModel->Buscar($id);
					$data['amortizacion'] = $this->PagoModel->amortizacion_entradas($id);
					$this->load->view("Layout/header.php");
					$this->load->view("Credito/movimiento.php", $data);
					$this->load->view("Layout/footer.php");
				}else{
					$data['titulo'] = 'Credito Desconocido';
					$data['mensaje'] = 'No se encontraron los datos del credito';
					$this->load->view("Layout/header.php");
					$this->load->view("Error/no_access.php", $data);
					$this->load->view("Layout/footer.php");
				}
			}else{
				$data['titulo'] = 'Credito Desconocido';
				$data['mensaje'] = 'No se encontraron los datos del credito';
				$this->load->view("Layout/header.php");
				$this->load->view("Error/no_access.php", $data);
				$this->load->view("Layout/footer.php");
			}
		}

		function moras($id){
			if ($id != null) {
				$credito = $this->CreditoModel->Buscar_libre(
					'cred_codigo as id, cred_serie as codigo, cred_estado as estado',
					array('cred_codigo' => $id)
				);
				if (!empty($credito)) {
					$data['credito'] = $credito;
					// $data['moras'] = $this->CreditoModel->Buscar_moras($id);
					$this->load->view("Layout/header.php");
					$this->load->view("Credito/mora.php", $data);
					$this->load->view("Layout/footer.php");
				}else{
					$data['titulo'] = 'Credito Desconocido';
					$data['mensaje'] = 'No se encontraron los datos del credito';
					$this->load->view("Layout/header.php");
					$this->load->view("Error/no_access.php", $data);
					$this->load->view("Layout/footer.php");
				}
			}else{
				$data['titulo'] = 'Credito Desconocido';
				$data['mensaje'] = 'No se encontraron los datos del credito';
				$this->load->view("Layout/header.php");
				$this->load->view("Error/no_access.php", $data);
				$this->load->view("Layout/footer.php");
			}
		}

		function regularizar($id = null){
			if ($id != null) {
				/*$credito = $this->CreditoModel->Buscar_libre(
					'cred_codigo as id, cred_serie as codigo, cred_estado as estado',
					array('cred_codigo' => $id)
				);*/
				$credito = $this->Credito_bzModel->getById($id);
				$permiso = $this->PermisoModel->verificar_permiso(
					$this->session->userdata('pres_ses_usuario'),
					'pago_registrar'
				);
				
				$data['prenda'] = (!empty($credito) && !empty($credito->prenda_id)) 
					? $this->Prenda_apiModel->getById($credito->prenda_id) 
					: null;

				if (!empty($credito) AND $credito->estado === 'E' AND $permiso->codigo == 'P') {
					$data['credito'] = $credito;
					$data['cuotas'] = $this->CuotaModel->Lista_regularizacion($id, date("Y-m-d"));
					$data['mora'] = $this->MoraModel->Buscar_pendiente($id);
					$data['pospuesto'] = $this->MoraModel->Buscar_pospuesto($id);

					if (!empty($data['cuotas']) || !empty($data['mora']) || !empty($data['pospuesto'])) {
						$data['banco'] = $this->BancoModel->activos();
						$this->load->view("Layout/header.php");
						$this->load->view("PPago/regularizar.php", $data);
						$this->load->view("Layout/footer.php");
					}else{
						$data['titulo'] = 'Credito al dia';
						$data['mensaje'] = 'El credito no tiene deudas pendientes por regularizar';
						$this->load->view("Layout/header.php");
						$this->load->view("Error/informa.php", $data);
						$this->load->view("Layout/footer.php");
					}
				}else{
					if (empty($credito) || $credito->estado != 'E') {
						$data['titulo'] = 'Estado de Credito';
						$data['mensaje'] = 'El credito no esta disponible para REGULARIZACION';
					}elseif($permiso->codigo != 'P'){
						$data['titulo'] = 'Permisos insuficientes.';
						$data['mensaje'] = 'El usuario actual no tiene los privilegios suficientes para registrar un pago.';
					}
					else{
						$data['titulo'] = 'Credito desconocido';
						$data['mensaje'] = 'No se encontraron datos para el seguimiento del credito';
					}
					$this->load->view("Layout/header.php");
					$this->load->view("Error/no_found.php", $data);
					$this->load->view("Layout/footer.php");
				}
			}else{
				$data['titulo'] = 'Credito desconocido';
				$data['mensaje'] = 'No se encontraron datos para el seguimiento del credito';
				$this->load->view("Layout/header.php");
				$this->load->view("Error/no_found.php", $data);
				$this->load->view("Layout/footer.php");
			}
		}

		function recuperar($id = null){
			if ($id != null) {
				$credito = $this->Credito_bzModel->getById($id);
				// $credito = $this->CreditoModel->Buscar_libre(
				// 	'cred_codigo as id, cred_serie as codigo, cred_estado as estado, cred_prenda as prenda',
				// 	array('cred_codigo' => $id)
				// );
				$permiso = $this->PermisoModel->verificar_permiso(
					$this->session->userdata('pres_ses_usuario'),
					'credito_liquidar'
				);
				$data['prenda'] = (!empty($credito) && !empty($credito->prenda_id)) 
					? $this->Prenda_apiModel->getById($credito->prenda_id) 
					: null;
					
				if (!empty($credito) AND ($credito->estado === 'E' || $credito->estado === 'CC') AND $permiso->codigo == 'P') {
					$data['credito'] = $credito;
					$data['banco'] = $this->BancoModel->activos();
					$data['cuotas'] = $this->CuotaModel->Lista_recuperacion($id, date("Y-m-d"));
					$data['moras'] = $this->MoraModel->Buscar_total($id);
					$data['capital'] = $this->CreditoModel->Buscar_libre('(cred_monto - cred_amortizacion) as monto, cred_codigo as id',array('cred_codigo'=>$id));
					if(!empty($data['cuota']) || !empty($data['mora']) || !empty($data['capital'])){
						$this->load->view("Layout/header.php");
						$this->load->view("PPago/recuperar.php", $data);
						$this->load->view("Layout/footer.php");
					}else{

					}
				}else{
					if (empty($credito) || $credito->estado != 'E') {
						$data['titulo'] = 'Estado de Credito';
						$data['mensaje'] = 'El credito no esta disponible para una RECUPERACION';
					}elseif($permiso->codigo != 'P'){
						$data['titulo'] = 'Permisos insuficientes.';
						$data['mensaje'] = 'El usuario actual no pude liquidar creditos.';
					}
					else{
						$data['titulo'] = 'Credito desconocido';
						$data['mensaje'] = 'No se encontraron datos para el seguimiento del credito';
					}
					$this->load->view("Layout/header.php");
					$this->load->view("Error/no_found.php", $data);
					$this->load->view("Layout/footer.php");
				}
			}else{
				$data['titulo'] = 'Credito desconocido';
				$data['mensaje'] = 'No se encontraron datos para el seguimiento del credito';
				$this->load->view("Layout/header.php");
				$this->load->view("Error/no_found.php", $data);
				$this->load->view("Layout/footer.php");
			}
		}

		function amortizar($id = null){
			if ($id != null) {
				$credito = $this->CreditoModel->Buscar_libre(
					'cred_codigo as id, cred_serie as codigo, cred_estado as estado,
					(cred_monto - cred_amortizacion) as restante, cred_monto as monto, cred_amortizacion as amortizacion',
					array('cred_codigo' => $id)
				);
				$permiso = $this->PermisoModel->verificar_permiso(
					$this->session->userdata('pres_ses_usuario'),
					'pago_registrar'
				);
				if (!empty($credito) AND ($credito->estado === 'E' || $credito->estado === 'CC') AND $permiso->codigo == 'P') {
					$pend_cuota = $this->CuotaModel->Buscar_libre('count(*) as cantidad', array(
						'cuo_credito' => $id, 'cuo_estado' => 'V'
					));
					$data['credito'] = $credito;
					$data['banco'] = $this->BancoModel->activos();
					$this->load->view("Layout/header.php");
					$this->load->view("Credito/amortizar.php", $data);
					$this->load->view("Layout/footer.php");
				}else{
					if (empty($credito) || $credito->estado != 'E') {
						$data['titulo'] = 'Estado de Credito';
						$data['mensaje'] = 'El credito no esta disponible para AMORTIZACION';
					}elseif($permiso->codigo != 'P'){
						$data['titulo'] = 'Permisos insuficientes.';
						$data['mensaje'] = 'El usuario actual no tiene los privilegios suficientes para registrar un pago.';
					}
					else{
						$data['titulo'] = 'Credito desconocido';
						$data['mensaje'] = 'No se encontraron datos para el seguimiento del credito';
					}
					$this->load->view("Layout/header.php");
					$this->load->view("Error/no_found.php", $data);
					$this->load->view("Layout/footer.php");
				}
			}else{
				$data['titulo'] = 'Credito desconocido';
				$data['mensaje'] = 'No se encontraron datos para el seguimiento del credito';
				$this->load->view("Layout/header.php");
				$this->load->view("Error/no_found.php", $data);
				$this->load->view("Layout/footer.php");
			}
		}

		function adelanto($id = null){
			if ($id != null) {
				$credito = $this->CreditoModel->Buscar_libre(
					'cred_codigo as id, cred_serie as codigo, cred_estado as estado, cred_pago_semanal as pago,
					(cred_monto - cred_amortizacion) as restante, cred_monto as monto, cred_amortizacion as amortizacion',
					array('cred_codigo' => $id)
				);
				$permiso = $this->PermisoModel->verificar_permiso(
					$this->session->userdata('pres_ses_usuario'),
					'pago_registrar'
				);
				if (!empty($credito) AND $credito->estado === 'E' AND $permiso->codigo == 'P') {
					$pend_cuota = $this->CuotaModel->Buscar_libre('count(*) as cantidad', array(
						'cuo_credito' => $id, 'cuo_estado' => 'V'
					));
					$pend_mora = $this->MoraModel->Buscar_libre('count(*) as cantidad', array(
						'mor_credito' => $id, 'mor_estado' => 'PEN'
					));
					if ($pend_cuota->cantidad == 0 && $pend_mora->cantidad == 0) {
						$data['credito'] = $credito;
						$data['banco'] = $this->BancoModel->activos();
						$this->load->view("Layout/header.php");
						$this->load->view("Credito/adelanto.php", $data);
						$this->load->view("Layout/footer.php");
					}else{
						$data['titulo'] = 'Credito Pendiente';
						$data['mensaje'] = "El credito tiene cuotas y/o mora por regularizar";
						$this->load->view("Layout/header.php");
						$this->load->view("Error/informa.php", $data);
						$this->load->view("Layout/footer.php");
					}
				}else{
					if (empty($credito) || $credito->estado != 'E') {
						$data['titulo'] = 'Estado de Credito';
						$data['mensaje'] = 'El credito no esta disponible para AMORTIZACION';
					}elseif($permiso->codigo != 'P'){
						$data['titulo'] = 'Permisos insuficientes.';
						$data['mensaje'] = 'El usuario actual no tiene los privilegios suficientes para registrar un pago.';
					}
					else{
						$data['titulo'] = 'Credito desconocido';
						$data['mensaje'] = 'No se encontraron datos para el seguimiento del credito';
					}
					$this->load->view("Layout/header.php");
					$this->load->view("Error/no_found.php", $data);
					$this->load->view("Layout/footer.php");
				}
			}else{
				$data['titulo'] = 'Credito desconocido';
				$data['mensaje'] = 'No se encontraron datos para el seguimiento del credito';
				$this->load->view("Layout/header.php");
				$this->load->view("Error/no_found.php", $data);
				$this->load->view("Layout/footer.php");
			}
		}

		function congelamiento($id = null){
			// $data['id'] = $id;

			try {
				$credito = $this->CreditoModel->get_data($id);
				if (!$credito) throw new Exception('Credito no identificado.');
				if ($credito->estado != 'E') throw new Exception('El credito debe estar activo para poder congelarse.');


				$data = [
					'credito' => $credito,
					'cuotas' => $this->CuotaModel->get_all_to_congelacion($id)
				];
				$this->load->view("Layout/header.php");
				$this->load->view("Credito/congelamiento.php", $data);
				$this->load->view("Layout/footer.php");
			} catch (Exception $e) {
				$data['titulo'] = 'Error';
				$data['mensaje'] = $e->getMessage();
				$this->load->view("Layout/header.php");
				$this->load->view("Error/no_found.php", $data);
				$this->load->view("Layout/footer.php");
			}

		}







		// Services
		function lista(){
			try {
				$suc = $this->session->userdata('pres_ses_sucursal');
				$data = [];
				if (!$_POST) throw new Exception('Parametros no recibidos');
				switch ($_POST['tipo']) {
					case 'codigo':
						$data = $this->CreditoModel->Lista_por_codigo($_POST['buscar'], $suc, intval($_POST['limite']));
						break;

					case 'cliente':
						$data = $this->CreditoModel->Lista_por_cliente($_POST['buscar'], $suc, intval($_POST['limite']));
						break;

					case 'estado':
						$data = $this->CreditoModel->Lista_por_estado($_POST['buscar'], $suc, intval($_POST['limite']));
						break;

					case 'atraso':
						$parms = explode(" ", $_POST['buscar']);
						$math_symbol = ["=", "<=", ">=", ">", "<"];
						// die(json_encode(count($parms)));
						if (count($parms) != 2) throw new Exception('Formula no valida');
						if (!in_array($parms[0], $math_symbol)) throw new Exception('Formula incompleta');
						// if (!is_int($parms[1])) throw new Exception('Formula no validax');
						$data = $this->CreditoModel->Lista_por_atraso($parms[0], intval($parms[1]), $suc, intval($_POST['limite']));
						break;

					default:
						$data = $this->CreditoModel->Lista_basica($suc, intval($_POST['limite']));
						break;
				}
				echo json_encode($data);
			} catch (Exception $e) {
				echo json_encode([]);
			}
		}

		function consulta_cuotas(){
			$res = $this->CreditoModel->Buscar_cuotas($_POST['id']);
			echo json_encode($res);
		}

		function consulta_pagos(){
			$res = $this->CreditoModel->Buscar_pagos($_POST['id']);
			echo json_encode($res);
		}

		function consulta_moras(){
			$res = $this->CreditoModel->Buscar_moras($_POST['id']);
			echo json_encode($res);
		}

		function consulta_prorrogas(){
			$res = $this->CreditoModel->Buscar_prorrogas($_POST['id']);
			echo json_encode($res);
		}

		function consulta_notas(){
			$res = $this->NotaCreditoModel->Listar($_POST['id']);
			echo json_encode($res);
		}

		function registrar(){
			$resp = [];
			$permiso = $this->PermisoModel->verificar_permiso(
				$this->session->userdata('pres_ses_usuario'),
				'credito_registrar'
			);
			if ($permiso && $permiso->codigo == 'P') {
				$valida = $this->validate_registro($_POST);
				if (count($valida) === 0) {
					try {
						$this->db->trans_begin();
						$sucursal = $this->session->userdata('pres_ses_sucursal');
						$data_sucursal = $this->SucursalModel->Buscar_libre(
							'suc_serie as serie', array('suc_codigo' => $sucursal)
						);
						if (empty($data_sucursal) || $data_sucursal->serie == null) {
							die(json_encode(array(
								'status' => 'ERROR',
								'message' => 'Error al procesar: Serie de sucursal no definida'
							)));
						}
						$busca_orden = $this->CreditoModel->Buscar_libre(
							'MAX(cred_orden) as orden', array('cred_sucursal' => $sucursal)
						);
						// $ciclo_bnk = ['SEM' => 7, 'MEN' => 30];
						// $ciclo_act = $ciclo_bnk[$_POST['ciclo']];
						// if ($_POST['ciclo'] === 'MENSUAL') $cant = "+ 30 days";
						$ciclo_cant = 7;
						// $ciclo_cant = ($_POST['ciclo'] === 'MENSUAL') ? 30 : 7;
						$ciclo_cant = $_POST['ciclo'];
						$ciclo_string = "+ $ciclo_cant days";
						$orden = $busca_orden->orden + 1;
						$credito_serie = $data_sucursal->serie."-".$orden;
						$data = array(
							'cred_serie' => $credito_serie,
							'cred_orden' => $orden,
							'cred_creacion' => date("Y-m-d "),
							'cred_empleado' => $this->session->userdata('pres_ses_empleado'),
							'cred_cliente' => $_POST['idcliente'],
							'cred_prenda' => $_POST['idprenda'],
							'cred_monto' => $_POST['monto'],
							'cred_prorroga' => $_POST['prorroga'],
							'cred_cargo' => $_POST['cargo'],
							'cred_tasa' => $_POST['interes'],
							'cred_tipo_tasa' => $_POST['tipo'],
							'cred_pago_semanal' => $_POST['pago'],
							'cred_penalidad' => $_POST['mora'],
							'cred_sucursal' => $this->session->userdata('pres_ses_sucursal'),
							'cred_estado' => 'E',
							'cred_ciclo_pago' => $ciclo_cant,
							'cred_ciclo_ref' => $_POST['ciclo'],
							'cred_create_at' => date("Y-m-d H:i:s"),
							'cred_update_at' => date("Y-m-d H:i:s")
						);
						// die(json_encode([
						// 	'data' => $data
						// ]));
						$this->CreditoModel->Insertar($data);
						$id = $this->db->insert_id();
						$vence = date("Y-m-d", strtotime(date("Y-m-d"). $ciclo_string));
						if (date('w', strtotime($vence)) === 0) {
							while( date('w', strtotime($vence)) === 0) {
								$vence = date("Y-m-d", strtotime($vence. ' + 1 days'));
							}
						}
						$ini_mora = date('Y-m-d', strtotime($vence. " + ".$_POST['prorroga']." days"));
						if (date('w', strtotime($ini_mora)) === 0) {
							while( date('w', strtotime($ini_mora)) === 0) {
								$ini_mora = date("Y-m-d", strtotime($ini_mora. ' + 1 days'));
							}
						}
						$data_cuota = array(
							'cuo_credito' => $id,
							'cuo_orden' => 1,
							'cuo_monto' => $_POST['pago'],
							'cuo_penalidad' => $_POST['mora'],
							'cuo_vencimiento' => $vence,
							'cuo_inicio_mora' => $ini_mora,
							'cuo_estado' => 'E'
						);
						$this->CuotaModel->insertar($data_cuota);

						if (intval($_POST['prenda']) > 0) {
							$this->PrendaModel->modificar($_POST['prenda'], array('pren_estado' => 'O'));
						}
						$resp = array(
							'status' => 'OK',
							'message' => 'Credito '.$credito_serie.' registrado',
							'id' => $id
						);
						// die("no registrado");
						$this->db->trans_commit();
					} catch (PDOException $e) {
						$this->db->trans_rollback();
						die(json_encode(array(
							'status' => 'ERROR',
							'message' => 'Error al procesar. '.$e->getMessage()
						)));
					}
				}else{
					$resp = array(
						'status' => 'WARN',
						'message' => 'Errores encontrados',
						'error' => $valida
					);
				}
			}else{
				$resp = array(
					'status' => 'WARN',
					'message' => 'Permisos insuficientes'
				);
			}
			echo json_encode($resp);
		}

		function modificar(){
			$resp = [];
			$permiso = $this->PermisoModel->verificar_permiso(
				$this->session->userdata('pres_ses_usuario'),
				'credito_editar'
			);
			if ($permiso && $permiso->codigo == 'P') {
				$valida = $this->validate_modifica($_POST);
			}else{

			}
		}

		function eliminar(){
			$resp = [];
			$permiso = $this->PermisoModel->verificar_permiso(
				$this->session->userdata('pres_ses_usuario'),
				'credito_eliminar'
			);
			if ($permiso && $permiso->codigo == 'P') {
				$data_credito = $this->CreditoModel->Buscar_libre(
					'cred_estado as estado, cred_creacion as fecha, cred_prenda as prenda',
					array('cred_codigo' => $_POST['id'])
				);
				if ($data_credito != null) {
					$fecha = date("Y-m-d");
					if ($fecha == $data_credito->fecha) {
						if ($data_credito->prenda > 0) {
							$this->PrendaModel->Modificar($data_credito->prenda, array('pren_estado'=>'D'));
						}
						$this->CreditoModel->Eliminar($_POST['id']);
						if ($this->db->affected_rows() > 0) {
							$resp = array('status' => 'OK', 'message' => 'Credito eliminado');
						}else{
							$resp = array('status' => 'ERROR', 'message' => 'Proceso no completado');
						}
					}else{
						$resp = array('status' => 'WARN', 'message' => 'El credito ha sobrepasado el tiempo limite disponible para la eliminacion');
					}
				}else{
					$resp = array('status' => 'WARN', 'message' => 'Credito no encontrado');
				}
			}else{
				$resp = array('status' => 'WARN', 'message' => 'Permisos insuficientes');
			}
			echo json_encode($resp);
		}

		function modificar_penalidad(){
			$resp = [];
			$post = $this->input->post();
			$permiso = $this->PermisoModel->verificar_permiso(
				$this->session->userdata('pres_ses_usuario'),
				'credito_editar'
			);
			if ($permiso && $permiso->codigo == 'P') {
				try {
					$this->db->trans_begin();
					$this->CreditoModel->Modificar(
						$post['id'],
						array('cred_aplica_penalidad' => $_POST['estado'])
					);
					$rows = $this->db->affected_rows();
					if ($rows > 0) {
						$this->db->trans_commit();
						$resp = array('status' => 'OK', 'message' => 'Credito actualizado');
					}else{
						$this->db->trans_rollback();
						$resp = array('status' => 'ERROR', 'message' => 'No se pudo aplicar la solicitud');
						die(json_encode($resp));
					}
				} catch (PDOException $e) {
					$this->db->trans_rollback();
					$resp = array('status' => 'ERROR', 'message' => $r->getMessage());
					die(json_encode($resp));
				}
			}else{
				$resp = array('status' => 'WARN', 'message' => 'Permisos insuficientes');
			}
			echo json_encode($resp);
		}

		function validate_registro($data){
			$error = [];
			if (empty($data['cliente'])) {
				$error['cliente'] = 'El cliente es requerido.';
			}
			if (empty($data['monto'])) {
				$error['monto'] = 'Monto no procesado';
			}
			if (empty($data['prorroga']) || $data['prorroga'] == 0 ) {
				$error['prorroga'] = 'Especifique los dias de prorroga.';
			}
			if (empty($data['pago'])) {
				$error['pago'] = 'Pago semanal no procesado.';
			}
			// if (empty($data['tarifa'])) {
			// 	$error['tarifa'] = 'Tarifa no definida.';
			// }else{
			// 	if ($data['tarifa'] == 0) {
			// 		$error['tarifa'] = 'Tarifa incorrecta.';
			// 	}
			// }
			if (empty($data['mora'])) {
				$error['mora'] = 'Mora no procesada.';
			}
			return $error;
		}

		function validate_modifica($data){
			$error = [];
			if (empty($data['cliente'])) {
				$error['cliente'] = 'El cliente es requerido.';
			}
			if (empty($data['monto'])) {
				$error['monto'] = 'Monto no procesado';
			}
			if (empty($data['prorroga']) || $data['prorroga'] == 0 ) {
				$error['prorroga'] = 'Especifique los dias de prorroga.';
			}
			if (empty($data['pago'])) {
				$error['pago'] = 'Pago semanal no procesado.';
			}
			if (empty($data['tarifa'])) {
				$error['tarifa'] = 'Tarifa no definida.';
			}else{
				if ($data['tarifa'] == 0) {
					$error['tarifa'] = 'Tarifa incorrecta.';
				}
			}
			if (empty($data['mora'])) {
				$error['mora'] = 'Mora no procesada.';
			}
			return $error;
		}

		function procesar_congelacion(){
			$resp = [];
			$post = $this->input->post();
			$permiso = $this->PermisoModel->verificar_permiso(
				$this->session->userdata('pres_ses_usuario'),
				'credito_liquidar'
			);
			if ($permiso && $permiso->codigo == 'P') {
				$comp = $this->CreditoModel->Buscar_libre('cred_estado as estado', array('cred_codigo' => $_POST['id']));
				if ($comp->estado === 'E') {
					try {
						$this->db->trans_begin();
						$this->CreditoModel->Modificar($post['id'], array('cred_estado' => 'CC'));
						$this->CuotaModel->Modificar_libre(
							array('cuo_estado' => 'CC'),
							"cuo_credito = ".$_POST['id']." AND (cuo_estado = 'E' OR cuo_estado = 'V')"
						);
						$resp = array('status' => 'OK', 'message' => 'Credito congelado con exito');
						$this->db->trans_commit();
					} catch (PDOException $e) {
						$this->db->trans_rollback();
						$resp = array('status' => 'ERROR', 'message' => $r->getMessage());
						die(json_encode($resp));
					}
				}else{
					$resp = array('status' => 'WARN', 'message' => 'Credito no apto para congelacion');
				}
			}else{
				$resp = array('status' => 'WARN', 'message' => 'Permisos insuficientes');
			}
			echo json_encode($resp);
		}

		function procesar_remate_old(){
			$resp = [];
			$post = $this->input->post();
			$permiso = $this->PermisoModel->verificar_permiso(
				$this->session->userdata('pres_ses_usuario'),
				'credito_liquidar'
			);
			if ($permiso && $permiso->codigo == 'P') {
				$comp = $this->CreditoModel->Buscar_libre('cred_estado as estado, cred_prenda as prenda, cred_monto as monto, cred_liquidacion as fecha', array('cred_codigo' => $_POST['id']));
				if (($comp->estado === 'E' || $comp->estado === 'CC') && $comp->prenda > 0) {
					try {
						$this->db->trans_begin();
						$this->CreditoModel->Modificar(
							$post['id'],
							array('cred_estado' => 'A', 'cred_liquidacion' => date('Y-m-d'))
						);
						$this->CuotaModel->Modificar_libre(
							array('cuo_estado' => 'CC'),
							"cuo_credito = ".$_POST['id']." AND (cuo_estado = 'E' OR cuo_estado = 'V')"
						);
						$this->PrendaModel->Modificar($comp->prenda, array('pren_estado' => 'A'));

						$credata = $this->CreditoModel->ObtenerCliente(intval($_POST['id']));
						$this->AlertaClienteModel->Insertar([
							'acli_cliente' => intval($credata->cliente),
							'acli_fecha' => date('Y-m-d'),
							'acli_contenido' => 'El cliente tiene una prenda en remate',
							'acli_estado' => 'P'
						]);

						// Insertar en el modulo de Remates
						$this->load->model('ArticuloModel');
						$ins = $this->ArticuloModel->SetInsert(array(
							$comp->prenda,
							$post['id'],
							'D',
							$comp->monto,
							$comp->monto,
							0,
							date('Y-m-d'),
							date('Y-m-d')
						));

						$resp = array('status' => 'OK', 'message' => 'Credito pasado a remate');
						$this->db->trans_commit();
					} catch (PDOException $e) {
						$this->db->trans_rollback();
						$resp = array('status' => 'ERROR', 'message' => $e->getMessage());
						die(json_encode($resp));
					}
				}else{
					$msg = 'Credito no apto para remate';
					if ($comp->prenda == 0) {
						$msg = 'El credito no tiene una prenda asiganda como garantia';
					}
					$resp = array('status' => 'WARN', 'message' => $msg);
				}
			}else{
				$resp = array('status' => 'WARN', 'message' => 'Permisos insuficientes');
			}
			echo json_encode($resp);
		}

		function procesar_remate(){
			$resp = [];
			$post = $this->input->post();
			$permiso = $this->PermisoModel->verificar_permiso(
				$this->session->userdata('pres_ses_usuario'),
				'credito_liquidar'
			);
			if ($permiso && $permiso->codigo == 'P') {
				$comp = $this->CreditoModel->Buscar_libre('cred_estado as estado, cred_prenda as prenda, cred_monto as monto, cred_liquidacion as fecha', array('cred_codigo' => $_POST['id']));
				if (($comp->estado === 'E' || $comp->estado === 'CC') && $comp->prenda > 0) {
					try {
						$this->db->trans_begin();
						$this->CreditoModel->Modificar(
							$post['id'],
							array('cred_estado' => 'A', 'cred_liquidacion' => date('Y-m-d'))
						);
						$this->CuotaModel->Modificar_libre(
							array('cuo_estado' => 'CC'),
							"cuo_credito = ".$_POST['id']." AND (cuo_estado = 'E' OR cuo_estado = 'V')"
						);
						$this->PrendaModel->Modificar($comp->prenda, array(
							'pren_estado' => 'A'
						));

						$credata = $this->CreditoModel->ObtenerCliente(intval($_POST['id']));
						$this->AlertaClienteModel->Insertar([
							'acli_cliente' => intval($credata->cliente),
							'acli_fecha' => date('Y-m-d'),
							'acli_contenido' => 'El cliente tiene una prenda en remate',
							'acli_estado' => 'P'
						]);

						// Insertar en el modulo de Remates
						// $this->load->model('ArticuloModel');
						// $ins = $this->ArticuloModel->SetInsert(array(
						// 	$comp->prenda,
						// 	$post['id'],
						// 	'D',
						// 	$comp->monto,
						// 	$comp->monto,
						// 	0,
						// 	date('Y-m-d'),
						// 	date('Y-m-d')
						// ));

						$this->load->model('RemadevModel');
						$this->RemadevModel->update_prenda([
							$comp->monto,
							$comp->monto,
							0,
							date('Y-m-d'),
							$post['id']
						]);

						$resp = array('status' => 'OK', 'message' => 'Credito pasado a remate');
						$this->db->trans_commit();
					} catch (PDOException $e) {
						$this->db->trans_rollback();
						$resp = array('status' => 'ERROR', 'message' => $e->getMessage());
						die(json_encode($resp));
					}
				}else{
					$msg = 'Credito no apto para remate';
					if ($comp->prenda == 0) {
						$msg = 'El credito no tiene una prenda asiganda como garantia';
					}
					$resp = array('status' => 'WARN', 'message' => $msg);
				}
			}else{
				$resp = array('status' => 'WARN', 'message' => 'Permisos insuficientes');
			}
			echo json_encode($resp);
		}

		// Nuevo [04-06-2020]
		function serv_rest(){
			try {
				if(!$_POST || !$_POST['tipo']) throw new Exception('Parametros no recibidos');
				$data = [];
				switch ($_POST['tipo']) {
					case 'atraso':
						$send = $this->CuotaModel->ContadorAtraso(intval($_POST['buscar']));
						$data = ['status' => 'OK', 'atraso' => ($send) ? $send->valor : 'No Data'];
						break;

					default:
						# code...
						break;
				}
				echo json_encode($data);
			} catch (Exception $e) {
				echo json_encode([
					'status' => 'ERROR',
					'message' => $e->getMessage()
				]);
			}
		}

		// Code: 02-11-2020
		function ServiceConsulta(){
			$file = file_get_contents("php://input");
			$json = json_decode($file);
			$suc = $this->session->userdata('pres_ses_sucursal');
			$data_res = [];
			switch ($json->filtro) {
				case 'codigo':
					$data_res = $this->CreditoModel->SelectByCodigo($json->buscar_a, $suc, $json->cantidad);
					break;
				case 'cliente':
					$data_res = $this->CreditoModel->SelectByCliente($json->buscar_a, $suc, $json->cantidad);
					break;
				case 'estado':
					$data_res = $this->CreditoModel->SelectByEstado($json->buscar_a, $suc, $json->cantidad);
					break;
				case 'ciclo_pago':
					$data_res = $this->CreditoModel->SelectByCiclo($json->buscar_a, $suc, $json->cantidad);
					break;
				case 'atraso':
					$scode = "";
					switch ($json->buscar_b) {
						case 'mayor':
							$scode = ">";
							break;
						case 'menor':
							$scode = "<";
							break;
						case 'mayor_igual':
							$scode = ">=";
							break;
						case 'menor_igual':
							$scode = "<=";
							break;
						default:
							$scode = "=";
							break;
					}
					$data_res = $this->CreditoModel->SelectByAtraso($scode, intval($json->buscar_a), $suc, $json->cantidad);
					break;
				default:
					$data_res = $this->CreditoModel->SelectAll($suc, $json->cantidad);
					break;
			}

			echo json_encode($data_res);
		}

		function nuevo_debug(){
			$permiso = $this->PermisoModel->verificar_permiso(
				$this->session->userdata('pres_ses_usuario'),
				'credito_registrar'
			);
			if ($permiso && $permiso->codigo == 'P') {
				$this->load->view("Layout/header.php");
				$this->load->view("Credito/registro3.php");
				$this->load->view("Layout/footer.php");
			}else{
				$data['titulo'] = 'Permisos insuficientes';
				$data['mensaje'] = 'El usuario actual no tiene los privilegios suficientes para registrar creditos.';
				$this->load->view("Layout/header.php");
				$this->load->view("Error/no_access.php", $data);
				$this->load->view("Layout/footer.php");
			}
		}

		// Services Apis [10-10-2022]
		function service_get_all(){
			$file = file_get_contents("php://input");
			$data_in = json_decode($file);
			$data_out = $this->CreditoModel->get_data_all($data_in);
			echo json_encode($data_out);
		}


		// encode: 04-2023
		function service_congela_prep_data(){
			$d_inp = json_decode(file_get_contents('php://input'));
			$data = [
				'credito' => $this->CreditoModel->get_data($di->id),
				'cuotas' => $this->CuotaModel->get_all_to_congelamiento($di->id)
			];
			echo json_encode($data);
		}


	}

?>
