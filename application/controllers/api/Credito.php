<?php
class Credito extends CI_Controller {

    protected $clase_api = true;
    function __construct(){
        parent::__construct();

        $this->load->model('CreditoModel');
        $this->load->model('SucursalModel');
    }

    function get_by_id($id = null){
        try {
            if (!$this->session->userdata('logged_in')) {
				throw new Exception('Sesion no encontrada', 404);
			}
            // if (!verificar_permiso('CREDITO_VER')) {
			// 	response_json('Acceso Denegado!.');
			// 	return;
			// }
            $id = (int) $id;
            if ($id <= 0){
                throw new Exception("Credito no identificado.");
            }
            $data = $this->CreditoModel->getById($id);
            if (!$data){
                response_json("Credito no encontrado");
                return;
            }

            echo json_encode([
                'status' => 'OK',
                'message' => 'Consulta completada.',
                'result' => $data
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'status' => 'ERR',
                'message' => $e->getMessage()
            ], JSON_UNESCAPED_UNICODE);
        }
    }     
    function get_all(){
        try {
            if (!$this->session->userdata('pres_ses_sesion')) {
				throw new Exception('Sesion no encontrada', 404);
			}
            if (!verificar_permiso('CREDITO_LISTAR')) {
				response_json('Acceso Denegado!.');
				return;
			}
            if ($this->input->method() !== 'post'){
                throw new Exception("Metodo no permitido"); 
            }
            $filters = [
                'serie' => $this->input->post("serie", TRUE),
                'cliente_id' => (int) $this->input->post("cliente_id", TRUE),
                'prenda_id' => (int) $this->input->post("prenda_id", TRUE),
                'creacion' => $this->input->post("creacion", TRUE),
                'liquidacion' => $this->input->post("liquidacion", TRUE),
                'rango_monto' => $this->input->post("rango_monto", TRUE),
                'orden_tipo' => $this->input->post("orden_tipo", TRUE),
                'orden_campo' => $this->input->post("orden_campo", TRUE),
                'limite' => $this->input->post("limite", TRUE),
                'categoria' => $this->input->post("categoria", TRUE),
                'prenda_categoria' => (int) $this->input->post("prenda_categoria", TRUE),
                'prenda_descripcion' => $this->input->post("prenda_descripcion", TRUE),
            ];

            $estado = $this->input->post("estado", TRUE);
            if (!empty($estado) && in_array($estado, ["A","R","E","CC","AN"]) ){
                $filters['estado'] = $estado;
            }

            // Limpiar elementos nulos o vacios
            $filters = array_filter($filters, function($val){
                return $val !== null && $val !== '';
            });
            
            $result = $this->CreditoModel->getAll($filters);
            echo json_encode([
                'status' => 'OK',
                'message' => 'Consulta completada.',
                'result' => $result
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'status' => 'ERR',
                'message' => $e->getMessage()
            ], JSON_UNESCAPED_UNICODE);
        }
    }
    function get_by_cliente(){
        try {
            if (!$this->session->userdata('pres_ses_sesion')) {
				throw new Exception('Sesion no encontrada', 404);
			}
            if (!verificar_permiso('CREDITO_LISTAR')) {
				response_json('Acceso Denegado!.');
				return;
			}
            if ($this->input->method() !== 'post'){
                throw new Exception("Metodo no permitido"); 
            }
            $cliente_id = $this->input->post('cliente_id');
            if (!$cliente_id){
                throw new Exception("Cliente Desconocido."); 
            }
            $filters = [
                'serie' => $this->input->post("serie", TRUE),
                'descripcion' => $this->input->post("descripcion", TRUE),
                'creacion' => $this->input->post("creacion", TRUE),
                'liquidacion' => $this->input->post("liquidacion", TRUE),
                'rango_monto' => $this->input->post("rango_monto", TRUE),
                'limite' => $this->input->post("limite", TRUE),
                'cliente_id' => $cliente_id
            ];

            $estado = $this->input->post("estado", TRUE);
            if (!empty($estado) && in_array($estado, ["A","R","E","CC","AN"]) ){
                $filters['estado'] = $estado;
            }

            // Limpiar elementos nulos o vacios
            $filters = array_filter($filters, function($val){
                return $val !== null && $val !== '';
            });
            
            $result = $this->CreditoModel->getAll($filters);
            echo json_encode([
                'status' => 'OK',
                'message' => 'Consulta completada.',
                'result' => $result
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'status' => 'ERR',
                'message' => $e->getMessage()
            ], JSON_UNESCAPED_UNICODE);
        }
    }
    function get_by_prenda(){
        try {
            if (!$this->session->userdata('pres_ses_sesion')) {
				throw new Exception('Sesion no encontrada', 404);
			}
            if (!verificar_permiso('CREDITO_LISTAR')) {
				response_json('Acceso Denegado!.');
				return;
			}
            if ($this->input->method() !== 'post'){
                throw new Exception("Metodo no permitido"); 
            }
            $prenda_id = $this->input->post('prenda_id');
            if (!$prenda_id){
                throw new Exception("Prenda Desconocida."); 
            }
            $filters = [
                'prenda_id' => $prenda_id,
                'serie' => $this->input->post("serie", TRUE),
                'descripcion' => $this->input->post("descripcion", TRUE),
                'creacion' => $this->input->post("creacion", TRUE),
                'liquidacion' => $this->input->post("liquidacion", TRUE),
                'rango_monto' => $this->input->post("rango_monto", TRUE),
                'limite' => $this->input->post("limite", TRUE)
            ];

            $estado = $this->input->post("estado", TRUE);
            if (!empty($estado) && in_array($estado, ["A","R","E","CC","AN"]) ){
                $filters['estado'] = $estado;
            }

            // Limpiar elementos nulos o vacios
            $filters = array_filter($filters, function($val){
                return $val !== null && $val !== '';
            });
            
            $result = $this->CreditoModel->getAll($filters);
            echo json_encode([
                'status' => 'OK',
                'message' => 'Consulta completada.',
                'result' => $result
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'status' => 'ERR',
                'message' => $e->getMessage()
            ], JSON_UNESCAPED_UNICODE);
        }
    }
    

    // por revisar
    function create(){
        try {
            if (!$this->session->userdata('pres_ses_sesion')) {
				throw new Exception('Sesion no encontrada', 404);
			}
            if (!verificar_permiso('CREDITO_CREAR')) {
				response_json('Acceso Denegado!.');
				return;
			}

            $rules_validation = credito_create_validation();
            $this->form_validation->set_rules($rules_validation);
            if (!$this->form_validation->run()){
                response_json($e->getMessage(), ["errors" => validation_errors()]);
                exit;
            }

            // Definir variables de datos
            $cliente_id = (int) $this->input->post("cliente_id", TRUE);
            $monto = $this->input->post("monto", TRUE);
            $cargo = $this->input->post("cargo", TRUE);
            $tasa_interes = $this->input->post("tasa_interes", TRUE);
            $tasa_tipo = $this->input->post("tasa_tipo", TRUE);
            $pago_cuota = $this->input->post("pago_cuota", TRUE);
            $pago_mora = $this->input->post("pago_mora", TRUE);
            $forma_entrega = $this->input->post("forma_entrega", TRUE);
            $detalles_entrega = $this->input->post("detalles_entrega", TRUE);
            $dias_prorroga = (int) $this->input->post("prorroga", TRUE);
            $prenda_id = (int) $this->input->post("prenda_id", TRUE);
            $ciclo_pago = $this->input->post("ciclo_pago", TRUE);
            $tieneGarantia = $this->input->post("garantia", TRUE);

            if (!in_array($ciclo_pago, ["SEMANAL","MENSUAL"])){
                throw new Exception("Ciclo de pago invalido.");
            }
            $ciclo_dias = ($ciclo_pago === "MENSUAL") ? 30 : 7;
            

            // Verificar prenda y su disponibilidad
            if ($tieneGarantia){
                $prenda_data = $this->PrendaModel->getById($prenda_id);
                if (empty($prenda_data) || !in_array($prenda_id->estado, ['R','D'])){
                    response_json(
                        "Prenda no valida.", 
                        ["errors" => ['prenda' => 'Prenda no identificada.']]
                    );
                    exit;
                }
            }

            // Generar serie de credito
            $sede_id = $this->session->userdata('pres_ses_sucursal');
            $sede_data = $this->SucursalModel->get_data($sede_id);
            if (empty($sede_data) || $sede_data->serie === null) {
                throw new Exception("Sucursal no identificada");
            }

            $orden_serie = $this->CreditoModel->get_ultimo_orden_serie($sede_id);
            if (empty($orden_serie) || $orden_serie->value === null){
                throw new Exception("Error al generar serie.");
            }
            $orden_nuevo = (int) $orden_serie->value + 1;
            $serie_nuevo = $sede_data->serie. "-" .$orden_nuevo;
            if ($this->CreditoModel->checkSerieExists($serie_nuevo)){
                throw new Exception("Error de serie - En uso");
            }

            
            $this->db->trans_begin();
            $credito_data = [
                'cred_serie' => $serie_nuevo,
                'cred_orden' => $orden_nuevo,
                'cred_creacion' => date("Y-m-d "),
                'cred_create_at' => date("Y-m-d H:i:s"),
                'cred_update_at' => date("Y-m-d H:i:s"),
                'cred_empleado' => $this->session->userdata('pres_ses_empleado'),
                'cred_cliente' => $cliente_id,
                'cred_prenda' => $prenda_id,
                'cred_monto' => $monto,
                'cred_capital' => $monto,
                'cred_prorroga' => $dias_prorroga,
                'cred_cargo' => $cargo,
                'cred_tasa' => $tasa_interes,
                'cred_tipo_tasa' => $tasa_tipo,
                'cred_pago_semanal' => $pago_cuota,
                'cred_pago_cuota' => $pago_cuota,
                'cred_penalidad' => $pago_mora,
                'cred_sucursal' => $sede_id,
                'cred_estado' => 'E',
                'cred_ciclo_pago' => $ciclo_dias,
                'cred_pago_periodo' => $ciclo_pago,
                'cred_forma_entrega' => $forma_entrega,
                'cred_detalles_entrega' => json_encode($detalles_entrega)
            ];

            $this->CreditoModel->insert($credito_data);
            $credito_id = $this->db->insert_id();
            if (!$credito_id){
                throw new Exception("Error al crear credito.");
            }

            // Registrar cuota
            $vencimiento = generar_fecha(date("Y-m-d"), $ciclo_dias);
            $inicio_mora = generar_fecha($vencimiento, $dias_prorroga);

            $cuota_data = [
                'cuo_credito' => $credito_id,
                'cuo_orden' => 1,
                'cuo_monto' => $pago_cuota,
                'cuo_penalidad' => $pago_mora,
                'cuo_vencimiento' => $vencimiento,
                'cuo_inicio_mora' => $inicio_mora,
                'cuo_estado' => 'E'
            ];
            $this->CuotaModel->insertar($cuota_data);
            $cuota_id = $this->db->insert_id();

            // Actualizar estado de la prenda
            // $garantia = $this->input->post("garantia", TRUE);
            if ($garantia && !empty($prenda_id)){
                $prenda_estado = ['pren_estado' => 'O'];
                $this->PrendaModel->update($prenda_id, ['pren_estado' => 'O']);
                if ($this->db->affected_rows() === 0){
                    throw new Exception("Prenda no actualizada.");
                }
            }
            
            $this->db->trans_commit();
            echo json_encode([
                'status' => 'OK',
                'message' => 'Credito '.$serie_nuevo.' creado con exito.',
                'id' => $credito_id
            ], JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            $this->db->trans_rollback();
            http_response_code(500);
            echo json_encode([
                'status' => 'ERR',
                'message' => $e->getMessage()
            ], JSON_UNESCAPED_UNICODE);
        }
    }
    function delete(){
        try {
            if (!$this->session->userdata('pres_ses_sesion')) {
				throw new Exception('Sesion no encontrada', 404);
			}
            if (!verificar_permiso('CREDITO_ELIMINAR')) {
				response_json('Acceso Denegado!.');
				return;
			}
            if ($this->input->method() !== 'post'){
                throw new Exception("Metodo no permitido"); 
            }

            $cliente_id = (int) $this->input->post('cliente_id', TRUE);
            if (empty($cliente_id)){
                throw new Exception("Cliente no identificado.");
            }

            if (!$this->ClienteModel->delete($cliente_id)){
                throw new Exception("Error al eliminar el cliente.");
            }
            $cliente = $this->ClienteModel->getById($cliente_id);
            if (!empty($cliente)){
                throw new Exception("Cliente no eliminado.");
            }

            echo json_encode([
                'status' => 'OK',
                'message' => 'Cliente eliminado con exito.'
            ], JSON_UNESCAPED_UNICODE);

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'status' => 'ERR',
                'message' => $e->getMessage()
            ], JSON_UNESCAPED_UNICODE);
        }
    }
    
}