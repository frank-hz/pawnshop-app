<?php
class ClienteAPI extends MY_Controller {
    function __construct(){
        parent::__construct();
        $this->load->model("ClienteModel");
        $this->load->model("CreditoModel");
    }

    function get_by_id($id = null){
        try {
            if (!$this->session->userdata('logged_in')) {
                throw new Exception('Sesion no encontrada', 404);
            }
            // if (!verificar_permiso('CLIENTE_VER')) {
            //     response_json('Acceso Denegado!.');
            //     return;
            // }
            $id = (int) $id;
            if ($id <= 0) {
                response_json("ID invalido.");
                return;
            }
            $data = $this->ClienteModel->get_by_id($id);
            if (!$data) {
                response_json("Cliente no encontrado.");
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
    function get_all() {
        try {
            if (!$this->session->userdata('logged_in')) {
                throw new Exception('Sesion no encontrada', 404);
            }
            // if (!verificar_permiso('CLIENTE_LISTAR')) {
            //     response_json('Acceso Denegado!');
            //     return;
            // }
            $post_data = get_post_data();
            $filters = $this->validate_search_filters($post_data);
            $settings = $this->validate_search_settings($post_data);
            // $filters['search_tags'] = ['SHORT', 'STATS'];
            $result = $this->ClienteModel->get_all($filters, $settings);
            echo json_encode([
                'success' => true,
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
    function create() {
        try {
            if (!$this->session->userdata('logged_in')) {
                throw new Exception('Sesion no encontrada', 404);
            }
            // configurar cabeceras para respuesta JSON
            header('Content-Type: application/json');
            header('Access-Control-Allow-Origin: *');
            header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
            header('Access-Control-Allow-Headers: Content-Type');

            // Manejar preflight request
            if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
                exit(0);
            }

            // Verificar si la solicitud es POST
            if ($this->input->server('REQUEST_METHOD') !== 'POST') {
                response_api('Metodo no permitido.', ['code' => 405]);
                return;
            }

            // Obtener datos JSON
            $input = json_decode(file_get_contents('php://input'), TRUE);
            // Obtener datos POST normales en caso no existan por JSON
            $input = empty($input) ? $this->input->post() : $input;

            if (empty($input)) {
                response_api('No se recibieron datos.', ['code' => 405]);
                return;
            }

            $this->form_validation->set_data($input);
            $rules = $this->setDefaultValidationRules();
            if ($this->config->load('form_validation', TRUE)) {
                $config = $this->config->item('form_validation');
                if (isset($config['cliente_create'])) {
                $rules = $config['cliente_create'];
                }
            }
            $this->form_validation->set_rules($rules);
            if ($this->form_validation->run() === FALSE) {
                $errors = $this->form_validation->error_array();
                response_json('Error de validacion.', [
                'errors' => $errors
                ]);
            }

            $documento = $this->input->post("documento", TRUE);
            $tipo_documento = $this->input->post("tipo_documento", TRUE);
            $nombre = $this->input->post("nombre", TRUE);
            $paterno = $this->input->post("paterno", TRUE);
            $materno = $this->input->post("materno", TRUE);
            $ubigeo = (int) $this->input->post("ubigeo", TRUE);
            $direccion = $this->input->post("direccion", TRUE);
            $telefono = $this->input->post("telefono", TRUE);
            $email = $this->input->post("email", TRUE);

            $data = [
                'cli_nombres' => mb_strtoupper($nombre),
                'cli_paterno' => mb_strtoupper($paterno),
                'cli_materno' => mb_strtoupper($materno),
                'cli_documento' => $documento,
                'cli_tipo_documento' => $tipo_documento,
                'cli_direccion' => $direccion,
                'cli_email' => mb_strtolower($email),
                'cli_telefono' => $telefono,
                'cli_ubigeo' => $ubigeo
            ];

            if (!$this->ClienteModel->insert($data)) {
                throw new Exception("No se completo el registro.");
            }
            $cliente_id = $this->db->insert_id();
            echo json_encode([
                'status' => 'OK',
                'message' => 'Datos del cliente registrados con exito.',
                'id' => $cliente_id
            ], JSON_UNESCAPED_UNICODE);
            return;

        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode([
                'status' => 'ERR',
                'message' => $e->getMessage()
            ], JSON_UNESCAPED_UNICODE);
        }
    }  
    function update(){
        try {
            if (!$this->session->userdata('logged_in')) {
                throw new Exception('Sesion no encontrada', 404);
            }
            if (!verificar_permiso('CLIENTE_EDITAR')) {
                response_json('Acceso Denegado!.');
                return;
            }

            $cliente_id = (int) $this->input->post("cliente_id", TRUE);
            if (!$cliente_id) {
                response_json("Cliente no identificado.");
                return;
            }

            $documento = $this->input->post("documento", TRUE);
            $tipo_documento = $this->input->post("tipo_documento", TRUE);
            $nombres = $this->input->post("nombres", TRUE);
            $paterno = $this->input->post("paterno", TRUE);
            $materno = $this->input->post("materno", TRUE);
            $ubigeo = (int) $this->input->post("ubigeo", TRUE);
            $direccion = $this->input->post("direccion", TRUE);
            $telefono = $this->input->post("telefono", TRUE);
            $email = $this->input->post("email", TRUE);
            $conducta = $this->input->post("conducta", TRUE);
            $en_blacklist = (int) $this->input->post("en_blacklist", TRUE);
            $blacklist_motivo = $this->input->post("blacklist_motivo", TRUE);

            $cliente_data = [
                'cli_nombres' => mb_strtoupper($nombres),
                'cli_paterno' => mb_strtoupper($paterno),
                'cli_materno' => mb_strtoupper($materno),
                'cli_documento' => $documento,
                'cli_tipo_documento' => $tipo_documento,
                'cli_direccion' => $direccion,
                'cli_email' => mb_strtolower($email),
                'cli_telefono' => $telefono,
                'cli_ubigeo' => $ubigeo,
                'conducta' => $conducta,
                'en_blacklist' => $en_blacklist,
                'blacklist_motivo' => $blacklist_motivo
            ];

            // Limpiar elementos nulos o vacios
            $cliente_data = array_filter($cliente_data, function ($val) {
                return $val !== null && $val !== '' && $val !== 0;
            });

            if (!$this->ClienteModel->update($cliente_id, $cliente_data)) {
                throw new Exception("No se completo el registro.");
            }

            $rows = (int) $this->db->affected_rows();
            echo json_encode([
                'status' => 'OK',
                'message' => ($rows > 0) ? 'Datos guardados.' : 'Sin cambios.'
            ], JSON_UNESCAPED_UNICODE);
            return;
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'status' => 'ERR',
                'message' => $e->getMessage()
            ], JSON_UNESCAPED_UNICODE);
        }
    }
    function delete(){
        try {
            if (!$this->session->userdata('logged_in')) {
                throw new Exception('Sesion no encontrada', 404);
            }
            if (!verificar_permiso('CLIENTE_ELIMINAR')) {
                response_json('Acceso Denegado!.');
                return;
            }

            if ($this->input->method() !== 'post') {
                throw new Exception("Metodo no permitido");
            }

            $cliente_id = (int) $this->input->post('cliente_id', TRUE);
            if (empty($cliente_id)) {
                response_json("Cliente no identificado.");
                return;
            }

            $filters = [
                'cliente_id' => $cliente_id,
                'search_tags' => ['SHORT']
            ];
            $creditos = $this->CreditoModel->get_all($filters);
            if (!empty($creditos)) {
                response_json("Cliente tiene creditos relacionados.");
                return;
            }

            if (!$this->ClienteModel->delete($cliente_id)) {
                throw new Exception("Error al eliminar el cliente.");
            }
            $cliente = $this->ClienteModel->getById($cliente_id);
            if (!empty($cliente)) {
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



    function get_conducta($id = null){
        try {
            if (!$this->session->userdata('pres_ses_sesion')) {
                throw new Exception('Sesion no encontrada', 404);
            }
            if (!verificar_permiso('CLIENTE_VER')) {
                response_json('Acceso Denegado!.');
                return;
            }
            $id = (int) $id;
            if ($id <= 0) {
                response_json("ID invalido.");
                return;
            }
            $data = $this->ClienteModel->getConducta($id);
            if (!$data) {
                response_json("Cliente no encontrado.");
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
    function getResumen($id = null){
        try {
            $data = $this->ClienteModel->get_resumen($id);
            echo json_encode([
                'status' => 'OK',
                'success' => true,
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



    // UTILIDAD
    function calcFrecuencia($creditos) {
        if (empty($creditos)) { return; }
        $total_creditos = count($creditos);
        foreach ($creditos as $credito) {
            $cuotas = $credito->cuotas_total;
            $cuotas = $credito->cuotas_vencidas;
            $cuotas = $credito->total_interes_pagado;
        }
    }
    function send_email() {
        try {
            $email = $this->input->post("correo_destino") ?? null;
            $asunto = $this->input->post("asunto") ?? null;
            $cuerpo = $this->input->post("cuerpo") ?? null;
            // $pie = $this->input->post("pie") ?? "";
            if ($email === null || $asunto === null || $cuerpo === null) {
                throw new Exception("Parametros no recibidos o invalidos.", 99);
            }
            $response = [];
            if (MailmanEnviarCorreo($email, $asunto, $cuerpo)) {
                $response['ok'] = 'Correo enviado correctamente.';
            } else {
                $response['error'] = 'El proceso de envio no se completo.';
            }
            echo json_encode($response);
        } catch (Exception $e) {
            $code = $e->getCode();
            $mssg = $e->getMessage();
            die(json_encode([
                'error' => ($code == 99) ? $mssg : 'ERROR_PROCESS',
                'log' => $e->getMessage()
            ]));
        }
    }
    function validar_documento_create($str) {
        $cliente = $this->ClienteModel->getByDocumento($str);
        if (empty($cliente)) { return TRUE; }
        $url = base_url('cliente/detalle/' . $cliente->id);
        // $nombre = htmlspecialchars($cliente->nombres . ' ' . $cliente->paterno);
        $this->form_validation->set_message(
            'validar_documento_create',
            "Documento en uso: <a href='$url' target='blank_'><b>Ver Cliente</b></a>"
        );
        return FALSE;
    }
    private function setDefaultValidationRules() {
        return $rules = [
        [
            'field' => 'documento',
            'label' => 'Documento',
            'rules' => 'required|min_length[8]|max_length[20]'
        ],
        [
            'field' => 'tipo_documento',
            'label' => 'TipoDocumento',
            'rules' => 'required|max_length[20]'
        ],
        [
            'field' => 'nombre',
            'label' => 'Nombre',
            'rules' => 'required|max_length[20]'
        ],
        [
            'field' => 'paterno',
            'label' => 'Ape. Paterno',
            'rules' => 'required|max_length[20]'
        ],
        [
            'field' => 'materno',
            'label' => 'Ape. Materno',
            'rules' => 'required|max_length[20]'
        ],
        [
            'field' => 'ubigeo',
            'label' => 'Ciudad/Distrito',
            'rules' => 'required|integer'
        ],
        [
            'field' => 'telefono',
            'label' => 'Telefono',
            'rules' => 'required|max_length[100]'
        ]
        ];
    }
    function validate_search_filters($filters) {
        $valids = [];
        if (!is_array($filters) || empty($filters)) {
            return $valids;
        }

        // doc
        if (isset($filters['documento'])) {
            $valids['documento'] = $filters['documento'];
        }

        // nombres y apellidos
        if (isset($filters['nom_ape'])) {
            $valids['nom_ape'] = $filters['nom_ape'];
        }

        // nombres, apellidos y documento
        if (isset($filters['nom_ape_doc'])) {
            $valids['nom_ape_doc'] = $filters['nom_ape_doc'];
        }

        // cantidad prendas
        if (isset($filters['cantidad_prendas'])) {
            $cantidad_prendas = (int) $filters['cantidad_prendas'];
            $valids['cantidad_prendas'] = $cantidad_prendas;
        }
        // cantidad creditos
        if (isset($filters['cantidad_creditos'])) {
            $cantidad_creds = (int) $filters['cantidad_creditos'];
            $valids['cantidad_creditos'] = $cantidad_creds;
        }

        // fecha creacion 
        if (isset($filters['fecha_creacion'])) {
            $fecha = $this->validate_date($filters['fecha_creacion']);
            if ($fecha) {
                $valids['fecha_creacion'] = $fecha;
            }
        }
        return $valids;
    }
    function validate_search_settings($settings) {
        $valids = [];
        $page = isset($settings['pagina']) ? (int) $settings['pagina'] : 1;
        $limit = is_numeric($settings['limite']) ? (int) $settings['limite'] : 0;
        // $limit = isset($settings['limite']) ? min((int) $settings['limite'], 100) : 50;
        $offset = ($page - 1) * $limit;
        $order_by = isset($settings['orden_campo']) ? $settings['orden_campo'] : 'CODIGO';
        $order_type = isset($settings['orden_tipo']) ? $settings['orden_tipo'] : 'ASC';

        $valids['page'] = $page;
        $valids['limit'] = $limit;
        $valids['offset'] = $offset;
        $valids['order_by'] = $order_by;
        $valids['order_type'] = $order_type;
        return $valids;
    }
    function validate_date($fecha) {
        if (empty($fecha)) return false;

        $formats = ['Y-m-d', 'd/m/Y', 'Y-m-d H:i:s'];
        foreach ($formats as $format) {
            $date = DateTime::createFromFormat($format, $fecha);
            if ($date && $date->format($format) === $fecha) {
                return $date->format('Y-m-D');
            }
        }
        return false;
    }

}
