<?php 
class CreditoAPI extends CI_Controller
{
	private $sede_id;
	function __construct(){
		parent::__construct();
		if (!$this->session->userdata('pres_ses_sesion')) {
			redirect(base_url().'Acceso','refresh');
		}
		$this->sede_id = $this->session->userdata('sucursal_id');
		$this->load->model('PermisoModel');
		$this->load->model('Credito_bzModel');
		$this->load->model('CreditoModel');
		// $this->load->model('CreditoAPIModel');
		$this->load->model('CuotaModel');
		$this->load->model('PrendaModel');
		$this->load->model('AlertaClienteModel');
		$this->load->model('SucursalModel');
		$this->load->model('ClienteAPIModel');
		$this->load->model('Prenda_apiModel');
		$this->load->model('Credito_ApiModel');
		$this->load->helper('credito_util_helper');

		// Helpers
		$this->load->helper('credito_util');
	}


	// B A S I C
	function getData(){
		$id_credito = $this->input->post("id_credito");
		// if ($id_credito === null) {
		// 	$valueJson = json_decode($this->input->raw_input_stream, true);
		// 	if ($valueJson['id_credito'] === null){
		// 		die("NO_DATA");
		// 	}
		// 	$id_credito = !empty($valueJson['id_credito']) ?? null;
		// }
		$data = $this->Credito_ApiModel->getData(intval($id_credito));
		echo json_encode($data);
	}

	function get_all(){
		try {
			$data = json_decode(file_get_contents("php://input"));
			$resp = [];
			if (empty($data)){
				throw new Exception("Error al iniciar busqueda",99);
			}
			$order_sett = $this->get_order_string($data->order[0]);
			$search_sett = $this->get_search_default_string($data->search_default);
			$search_custom = $this->get_search_custom_string($data->search_custom);
			$search_prenda = !empty($data->search_prenda) ? $data->search_prenda : '';
			$search_categoria = !empty($data->search_categoria) ? $data->search_categoria : '';
			$settings = [
				'order' => $order_sett,
				'search_default' => $search_sett,
				'search_custom' => $search_custom,
				'search_prenda' => $search_prenda,
				'search_categoria' => $search_categoria
			];
			$creditoList = $this->Credito_ApiModel->getAllAdvanced(
				$this->sede_id, $settings
			);
			// die(json_encode($creditoList));
			if (!empty($creditoList)) {
				foreach ($creditoList as $credito) {
					$credito->estado_tag = creditou_tag_estado($credito->estado);
				  	$credito->cuotas_vencidas = (in_array($credito->estado,['E','CC','A']))
				  ? $credito->cuotas_vencidas : '-';
				}
			}
			echo json_encode([
				'ok' => 'proceso completado',
				'settings' => $settings,
				'result' => $creditoList,
				'sucursal' => $this->sede_id
			]);
		} catch (Exception $e) {
			$code = $e->getCode();
			$mssg = $e->getMessage();
			echo json_encode([
				'error' => ($code==99) ? $mssg : 'ERROR_PROCESS',
				'log' => $e->getMessage()
			]);
		}

	}
	function save_data(){
		try {
			if (!$this->session->userdata('pres_ses_sesion')) {
				die(json_encode([
					'warning' => 'Sesion no encontrada'
				]));
			}
			if (!permission_validate('credito_registrar')) {
				die(json_encode([
					'warning' => 'Permisos insuficientes'
				]));
			}
			$this->form_validation->set_rules(creditou_insert_validation());
			if (!$this->form_validation->run()) {
				die(json_encode([
					'warning' => 'Datos incompletos o invalidos',
					'validation_errors' => validation_errors()
				]));
			}
			if (empty($this->ClienteAPIModel->getById($_POST['idcliente']))){
				die(json_encode([
					'warning' => 'Cliente desconocido'
				]));
			}
			if ($_POST['idprenda'] > 0) {
				$prenda_data = $this->Prenda_apiModel->getById($_POST['idprenda']);
				if (empty($prenda_data)) {
					die(json_encode([
						'warning' => 'Prenda desconocida'
					]));
				}
				if (!in_array($prenda_data->estado, ['R','D'])) {
					die(json_encode([
						'warning' => 'Prenda no disponible para nuevos creditoss'
					]));
				}
				$verify_use = $this->Prenda_apiModel->verifyPrendaInUse($_POST['idprenda']);
				if (!empty($verify_use)) {
					die(json_encode([
						'warning' => 'Prenda asociada a un credito activo'
					]));
				}
			}


			$this->db->trans_begin();

			// Generacion de Serie
			$sede_id = $this->session->userdata('pres_ses_sucursal');
			$sede_data = $this->SucursalModel->get_data($sede_id);
			if (empty($sede_data) || $sede_data->serie == null) {
				die(json_encode(['warning' => 'Sucursal no identificada']));
			}
			$orden_rastro = $this->Credito_bzModel->getLastOrdenBySucursal($sede_id);
			$orden_rastro = intval($orden_rastro->value);
			$orden_new = $orden_rastro + 1;
			$serie_new = $sede_data->serie.'-'.$orden_new;

			if (empty($orden_rastro) || !intval($orden_rastro)) {
				die(json_encode(['warning' => 'Error al generar serie']));
			}
			if (!empty($this->Credito_bzModel->getBySerieQuick($serie_new))) {
				die(json_encode([
					'warning' => 'Conflicto de generacion de serie'
				]));
			}
			$ciclo_dias = 7;
			if ($_POST['ciclo_pago'] === 'MENSUAL' || $_POST['ciclo_pago'] == '30'){
				$ciclo_dias = 30;
			}
			$ciclo_string = "+ $ciclo_dias days";
			$data = array(
				'cred_serie' => $serie_new,
				'cred_orden' => $orden_new,
				'cred_creacion' => date("Y-m-d "),
				'cred_empleado' => $this->session->userdata('pres_ses_empleado'),
				'cred_cliente' => $_POST['idcliente'],
				'cred_prenda' => $_POST['idprenda'],
				'cred_monto' => $_POST['monto'],
				'cred_prorroga' => $_POST['prorroga'],
				'cred_cargo' => $_POST['cargo'],
				'cred_tasa' => $_POST['interes'],
				'cred_tipo_tasa' => $_POST['tipo_tasa'],
				'cred_pago_semanal' => $_POST['pago_cuota'],
				'cred_penalidad' => $_POST['mora'],
				'cred_sucursal' => $this->session->userdata('pres_ses_sucursal'),
				'cred_estado' => 'E',
				'cred_ciclo_pago' => $ciclo_dias,
				'cred_ciclo_ref' => $_POST['ciclo_pago'],
				'cred_create_at' => date("Y-m-d H:i:s"),
				'cred_update_at' => date("Y-m-d H:i:s"),
				'cred_pago_periodo' => $_POST['ciclo_pago'],
				'cred_pago_cuota' => $_POST['pago_cuota']
			);
			// die(json_encode($data));
			$ins = $this->Credito_bzModel->insert($data);
			$idcredito = $this->db->insert_id();

			if (!$ins || $this->db->insert_id() < 0) {
				die(json_encode([
					'warning' => 'El registro no se completo'
				]));
			}

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
				'cuo_credito' => $idcredito,
				'cuo_orden' => 1,
				'cuo_monto' => $_POST['pago_cuota'],
				'cuo_penalidad' => $_POST['mora'],
				'cuo_vencimiento' => $vence,
				'cuo_inicio_mora' => $ini_mora,
				'cuo_estado' => 'E'
			);
			$ins_cuota = $this->CuotaModel->insertar($data_cuota);
			$cuotas_list = $this->CuotaModel->getByCredito($idcredito);
			if ( !$ins_cuota || empty($cuotas_list)){
				die(json_encode([
					'warning' => 'Error al generar las cuotas'
				]));
			}

			if (!empty($_POST['idprenda'])) {
				$prenda_upd = $this->PrendaModel->modificar(
					$_POST['idprenda'], ['pren_estado' => 'O']
				);
				if (!$prenda_upd || $this->db->affected_rows() == 0) {
					die(json_encode([
						'warning' => 'Error al actualizar la prenda'
					]));
				}
			}
			$this->db->trans_commit();
			echo json_encode([
				'success' => 'Credito '.$serie_new.' registrado',
				'id' => $idcredito
			]);

		} catch (Exception $e) {
			$code = $e->getCode();
			$mssg = $e->getMessage();
			die(json_encode([
				'code' => 'ERR',
				'message' => ($code==99) ? $mssg : 'Error al procesar',
				'log' => $e->getMessage()
			]));
		}
	}

	public function delete_sks(){
		try {
			$id = $this->input->post('id');
			
			if (!permission_validate('credito_eliminar')) {
				throw new Exception('Permisos insuficientes', 99);
			}

			if (!$id) {
				throw new Exception('Crédito no identificado', 99);
			}

			$credito = $this->Credito_bzModel->getById($id);
			if (!$credito) {
				throw new Exception('El crédito no existe', 99);
			}
			$estado = $credito->estado;
			$sucursal_id = $credito->sucursal_id;
			$fecha_actual = new DateTime();
			$fecha_creacion_og = $credito->fecha_creacion;
			$fecha_creacion = DateTime::createFromFormat(
				'd/m/Y - h:i A', $fecha_creacion_og
			);
			$fecha_creacion->setTime(0, 0, 0);
			$fecha_actual->setTime(0, 0, 0);

			if ($estado != 'E') {
				throw new Exception("Credito no activo.", 99);
			}
			if ($fecha_actual > $fecha_creacion) {
				throw new Exception("Tiempo limite expirado.", 99);
			}

			$check_nexts = $this->Credito_bzModel->check_next_credits(
				$id, $sucursal_id
			);
			if ($check_nexts > 0){
				throw new Exception("Eliminacion no permitida.", 99);
			}

			$log_batch = [];
			$this->db->trans_start();

			$this->Credito_bzModel->delete($id);
			if (!empty($credito->prenda_id)) {
				$prenda_id = $credito->prenda_id;
				$upd_data  =  ['pren_estado' => 'D'];
				$this->Prenda_apiModel->update($prenda_id, $upd_data);
				$rows = $this->db->affected_rows();
				if ($rows > 0) {
					$desc = "{$prenda_id}: {$rows} changes";
					$log_batch['prenda'] = $desc;
				}
			}
			
			$this->db->trans_complete();
			if ($this->db->trans_status() === FALSE) {
				throw new Exception("Error interno al procesar.", 99);
			}

			echo json_encode([
				'code' => 'OK',
				'status' => 'OK',
				'message' => 'Eliminacion completada',
				'log' => $log_batch
			]);

		} catch (Exception $e) {
			$code = $e->getCode();
			$mssg = $e->getMessage();
			echo json_encode([
				'code' => 'ERR',
				'message' => ($code==99) ? $mssg : 'Error al procesar',
				'log' => $e->getMessage()
			]);
		}
	}
	function delete_data(){
		try {
			$this->db->trans_begin();
			if (!permission_validate('credito_eliminar')) throw new Exception('Permisos insuficientes',99);
			if (!$_POST['id']) throw new Exception('Credito no identificado');

			$credito = $this->Credito_bzModel->getById($_POST['id']);
			if ($credito->fecha_creacion !== date("d/m/Y")) throw new Exception('El credito ha sobrepasado el tiempo limite disponible para la eliminacion: '.$credito->fecha_creacion."=".date("Y-m-d"),99);


			$del = $this->Credito_bzModel->delete($_POST['id']);
			if (!$del || $this->db->affected_rows() <= 0) {
				throw new Exception('Proceso no completado',99);
			}
			if (intval($credito->prenda_id) > 0) {
				$this->Prenda_apiModel->update($credito->prenda_id, [
					'pren_estado' => 'D'
				]);
			}
			$this->db->trans_commit();
			echo json_encode([
				'code' => 'OK',
				'message' => 'Credito Eliminado',
				'log' => $this->db->affected_rows()
			]);
		} catch (Exception $e) {
			$this->db->trans_rollback();
			$code = $e->getCode();
			$mssg = $e->getMessage();
			echo json_encode([
				'code' => 'ERR',
				'message' => ($code==99) ? $mssg : 'Error al procesar',
				'log' => $e->getMessage()
			]);
		}

	}
	function get_by_cliente(){
		try {
			if (empty($this->input->post('idcliente'))){
				throw new Exception('Cliente no identificado',99);
			}
			$idcliente = $this->input->post('idcliente');
			$rol = $this->session->userdata('rol_nombre');
			$restrict = ($rol === 'Administrador') ? FALSE : TRUE;
			$creditos = $this->CreditoModel->get_all_by_cliente($idcliente, $restrict); 
			if (!empty($creditos)){
				foreach ($creditos as $credito) {
					$credito->url = base_url().'Credito/detalle/'.$credito->id;
					$credito->estado_tag = creditou_tag_estado($credito->estado);
				}
			}
			echo json_encode([
				'code' => 'OK',
				'result' => $creditos,
				'message' => 'proceso completado.'
			]);
		} catch (Exception $th) {
			$code = $e->getCode();
			$mssg = $e->getMessage();
			echo json_encode([
				'code' => 'ERR',
				'message' => ($code==99) ? $mssg : 'Error al procesar',
				'log' => $e->getMessage()
			]);
		}
	}
	



	// C U S T O M

	function setPause(){
		try {
			$resp = [];
			$post = $this->input->post();
			$id = $post['id'];
			$permiso = $this->PermisoModel->verificar_permiso(
				$this->session->userdata('pres_ses_usuario'),
				'credito_liquidar'
			);
			if (!$permiso || $permiso->codigo != 'P'){
				throw new Exception('Permisos insuficientes',99);
			}
			$credito = $this->Credito_ApiModel->getCustom(
				'cred_estado as estado', 
				['cred_codigo' => $id]
			);
			if ($credito->estado != 'E'){
				throw new Exception('Credito no apto para congelacion.',99);
			}
			
			$this->db->trans_begin();
			$this->Credito_ApiModel->Update($id, ['cred_estado' => 'CC']);
			$this->CuotaModel->UpdateCustom(
				['cuo_estado' => 'CC'],
				"cuo_credito = $id AND (cuo_estado = 'E' OR cuo_estado = 'V')"
			);
			$this->db->trans_commit();
			echo json_encode([
				'status' => 'OK', 
				'message' => 'Credito congelado con exito.'
			]);
		} catch (Exception $e) {
			$code = $e->getCode();
			$mssg = $code===99 ? $e->getMessage() : '';
			die(json_encode([
				'error' => $mssg,
				'log' => $e->getMessage()
			]));
		}
		
	}

	function setRemate(){
		try {
			$resp = [];
			$idcredito = $this->input->post('id');
			$idusuario = $this->session->userdata('pres_ses_usuario');	
			if (!permission_validate('credito_liquidar')){
				throw new Exception('Permisos insuficientes',99);
			}
			$credito = $this->Credito_ApiModel->getCustom(
				'cred_estado as estado, cred_prenda as prenda', 
				['cred_codigo' => $idcredito]
			);
			if ($credito->estado != 'E'){
				throw new Exception('Credito no apto para congelacion',99);
			}			
			$this->db->trans_begin();
			// $FechaActual =  date("Y-m-d");
			// $this->Credito_ApiModel->Update($idcredito, [
			// 	'cred_estado' => 'A'
			// 	'cred_liquidacion' => $FechaActual
			// ]);
			$FechaActual =  date("Y-m-d H:i:s");
			// $this->CuotaModel->UpdateCustom(
			// 	['cuo_estado' => 'CC'],
			// 	"cuo_credito = $idcredito AND (cuo_estado = 'E' OR cuo_estado = 'V')"
			// );
			$this->Credito_ApiModel->Update($idcredito, [
				'cred_estado' => 'A',
				'cred_liquidacion' => $FechaActual
			]);
			// if (!empty($credito->prenda)){
			// 	$idprenda = $credito->prenda;
			// 	$this->Prenda_apiModel->update($idprenda, [
			// 		'pren_estado' => 'A',
			// 		'pren_fecha_liquida' => $FechaActual
			// 	]);
			// }			
			$this->db->trans_commit();
			echo json_encode([
				'status' => 'OK', 
				'message' => 'Credito rematado con exito.'
			]);
		} catch (Exception $e) {
			$code = $e->getCode();
			$mssg = $code===99 ? $e->getMessage() : '';
			die(json_encode([
				'error' => $mssg,
				'log' => $e->getMessage()
			]));
		}
		
	}

	function get_order_string($data){
		if (empty($data)) return;
		$string = "";
		$types = ["ASC","DESC"];
		$columns = [
		  "CODIGO" => "cred_codigo",
		  "MONTO" => "cred_monto",
		  "CREACION" => "cred_creacion",
		  "ATRASO" => "cuotas_vencidas",
		  "LIQUIDACION" => "cred_liquidacion"
		];
  
		if (!empty($data->target) &&
		  in_array($data->type, $types) &&
		  array_key_exists($data->target, $columns)) {
		  $target = $columns[$data->target];
		  $string .= "ORDER BY $target $data->type";
		  if (!empty($data->limit)) {
			$string .= " LIMIT $data->limit";
		  }
		}
		return $string;
	}

	// procesa los datos y devuelve un query de busqueda
    function get_search_default_string($data){
		if (empty($data)) return;
		if (empty($data->target) || empty($data->value)) return;

		$global_resp = [];
		$columns = [
		  "CODIGO" => "cred_codigo",
		  "CLIENTE" => ""
		];
		$string = "";
		$parameters = [];
		// switch ($data->target) {
		//   case 'CODIGO':
		// 	$string = "(cred_serie LIKE CONCAT(?,'%'))";
		// 	$parameters = [$data->value];
		// 	break;
		//   case 'CLIENTE':
		// 	$string = "
		// 	  cli_paterno LIKE CONCAT(?, '%') OR
		// 	  cli_materno LIKE CONCAT(?, '%') OR
		// 	  cli_nombres LIKE CONCAT('%', ?, '%')
		// 	";
		// 	$parameters = [$data->value,$data->value,$data->value];
		// 	break;
		// }
		
		if ($data->target === 'CODIGO' && !empty($data->value)){
			$global_resp = [
				'query' => "(cred_serie LIKE CONCAT('%',?,'%'))",
				'values' => [strtoupper($data->value)]
			];

		}elseif ($data->target === 'CLIENTE' && !empty($data->value)){
			$string = "(
				cli_paterno LIKE CONCAT(?, '%') OR
				cli_materno LIKE CONCAT(?, '%') OR
				cli_nombres LIKE CONCAT('%', ?, '%') 
			  )
			";
			$global_resp = [
				'query' => $string,
				'values' => [$data->value,$data->value,$data->value]
			];
		}
		return $global_resp;
		// return [
		//   'query' => $string,
		//   'parameters' => $parameters
		// ];

	}

	// procesa los filtros adicionales y devuelve un query de busqueda
    function get_search_custom_string($data){
		$targetAccepted = ["ESTADO","FECHA","FECHA_RANGO"];
		$stateValuesAccepted = ["E","A","CC","R"];
  
		$StringTotal = "";
		$ItemsEstado = array_filter($data, function($key){
		  if ($key->target === 'ESTADO') {
			return $key;
		  }
		},ARRAY_FILTER_USE_BOTH);
  
		$ItemsFecha = array_filter($data, function($key){
		  if ($key->target === 'FECHA') {
			return $key;
		  }
		},ARRAY_FILTER_USE_BOTH);
  
		$ParametersEstado = [];
		$StashEstado = [];
		$StringEstado = "";
		foreach ($ItemsEstado as $item) {
		  if (!in_array($item->value, $stateValuesAccepted)) continue;
		  array_push($StashEstado, "cred_estado = ?");
		  array_push($ParametersEstado, $item->value);
		}
		if (!empty($StashEstado)) $StringEstado = implode(" || ", $StashEstado);
		if (!empty($StringEstado)) $StringEstado = "($StringEstado)";
  
		$ParametersFecha = [];
		$StashFecha = [];
		$StringFecha = "";
		foreach ($ItemsFecha as $index => $item) {
		  if (empty($item->value)) continue;
		  array_push($StashFecha, "cred_creacion = ?");
		  array_push($ParametersFecha, $item->value);
		}
		if (!empty($StashFecha)) $StringFecha = implode(" || ", $StashFecha);
		if (!empty($StringFecha)) $StringFecha = "($StringFecha)";
  
		// filtramos las cadenas vacias
		$final_string_stash = array_filter([$StringEstado,$StringFecha]);
		$StringTotal = implode(" && ", $final_string_stash);
  
		return [
		  'query' => $StringTotal,
		  'values' => array_merge($ParametersEstado,$ParametersFecha)
		];
	}
}
