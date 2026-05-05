<?php

    class Credito_api extends CI_Controller
    {
        function __construct(){
            parent::__construct();
            if (!$this->session->userdata('pres_ses_sesion')) {
				redirect(base_url().'Acceso','refresh');
			}
            $this->load->model('PermisoModel');
            $this->load->model('Credito_bzModel');
            $this->load->model('CreditoModel');
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


		// C R U D
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
				$ciclo_dias = ($_POST['ciclo_pago'] == 30) ? $_POST['ciclo_pago'] : 7;
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

				// echo json_encode([
				// 	'code' => 'OK',
				// 	'log' => $ins,
				// 	'id' => $this->db->insert_id()
				// ]);

				// Generacion de cuota
				// $periodo = 'SEMANAL';
				// $ciclo = 7;
				// if ($_POST['ciclo_pago'] == 'MENSUAL') {
				// 	$periodo = 'MENSUAL';
				// 	$ciclo = 30;
				// }
				// $cuota_vence = creditou_generate_vencimiento(date("Y-m-d"),'SEMANAL');
				// die(json_encode([
				// 	'new' => $serie_new,
				// 	'last' => $ciclo_string,
				// 	'data' => $data
				// ]));



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


		//
    function apply_congelacion(){
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
        function apply_remate(){
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
		function get_by_cliente(){
            try {
				$creditos = $this->Credito_bzModel->getByCliente($_POST['idcliente']);
				if (!empty($creditos)) {
					foreach ($creditos as $credito) {
						$credito->url = base_url().'credito/detalle/'.$credito->id;
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


		// Get Data
		function get_data_list(){
			try {
				$data = json_decode(file_get_contents("php://input"));
				$resp = [];
				if (empty($data)){
					throw new Exception("Datos de configuracion no recibidos",99);
				}
        // if ($data->search_custom) {
        //   $wstash = $this->get_search_custom_string($data->search_custom);
        //   die(json_encode($wstash));
        // }
        $settings = [
          'order' => (!empty($data->order) ? $this->get_order_string($data->order[0]) : null),
          'search_default' => (!empty($data->search_default) ? $this->get_search_default_string($data->search_default) : 'null'),
          'search_custom' => (!empty($data->search_custom) ? $this->get_search_custom_string($data->search_custom) : 'null')
        ];
        $credito_list = $this->Credito_ApiModel->GetListData(1, $settings);
        if (!empty($credito_list)) {
          foreach ($credito_list as $credito) {
            $credito->estado_tag = creditou_tag_estado($credito->estado);
            $credito->cuotas_vencidas = (in_array($credito->estado,['E','CC']))
            ? $credito->cuotas_vencidas : '-';
          }
        }
				echo json_encode([
					'ok' => 'proceso completado',
					'result' => $credito_list
				]);
			} catch (Exception $e) {
				$code = $e->getCode();
				$mssg = $e->getMessage();
				echo json_encode([
					'error' => ($code==99) ? $mssg : 'Error al procesar',
					'log' => $e->getMessage()
				]);
			}
		}

    // procesa los datos y devuelve un query de ordenamiento
    function get_order_string($data){
      if (empty($data)) return;
      $string = "";
      $types = ["ASC","DESC"];
      $columns = [
        "CODIGO" => "cred_codigo",
        "MONTO" => "cred_monto",
        "CREACION" => "cred_creacion",
        "ATRASO" => "cred_atraso",
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
      $columns = [
        "CODIGO" => "cred_codigo",
        "CLIENTE" => ""
      ];
      $string = "";
      $parameters = [];
      switch ($data->target) {
        case 'CODIGO':
          $string = "(cred_serie LIKE CONCAT(?,'%'))";
          $parameters = [$data->value];
          break;
        case 'CLIENTE':
          $string = "
            cli_paterno LIKE CONCAT(?, '%') OR
            cli_materno LIKE CONCAT(?, '%') OR
            cli_nombres LIKE CONCAT('%', ?, '%')
          ";
          $parameters = [$data->value,$data->value,$data->value];
          break;
      }

      return [
        'query' => $string,
        'parameters' => $parameters
      ];
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
        'parameters' => array_merge($ParametersEstado,$ParametersFecha)
      ];
    }


  }

?>
