<?php 
    class InicioModel extends CI_Model {
        function __construct(){
            $this->sede_id = $this->session->userdata('sucursal_id');
            $this->curDate = date("Y-m-d");
        }

        function get_utilidad_cuota($setting){
            $idsede = $this->session->userdata('sucursal_id');
            if (!empty($setting['fecha'])){
                $this->db->where("DATE(p.pag_fecha)", $setting['fecha']);
            }
            $this->db->select('SUM(pdet_monto) as total');
            $this->db->select('COUNT(*) as cantidad');
            $this->db->where('(cred_estado = "E" OR cred_estado = "R")');
            $this->db->where("pdet_tipo = 'CU'");
            $this->db->where("pdet_tipo", $idsede);
            $this->db->join("pago p", "p.pag_codigo = pdet_pago");
            $res = $this->db->get('pago_detalle');
            
            $res = $this->db->query($sql, Array($idsede));
            return $res->row();
        }
        function get_utilidad_mora(){
            $idsede = $this->session->userdata('sucursal_id');
            $sql = "
                SELECT 
					SUM(pdet_monto) as total,
					COUNT(*) as cantidad
				FROM pago_detalle
				INNER JOIN pago ON pag_codigo = pdet_pago
				INNER JOIN credito ON cred_codigo = pag_credito
				WHERE 
					(cred_estado = 'E' OR cred_estado = 'R') AND
					pdet_tipo = 'MO' AND
					cred_sucursal = ? 
            ";
            $res = $this->db->query($sql, Array($idsede));
            return $res->row();
        }
        function creditosRecuento(){            
            $sql = "
                SELECT 
                    COUNT(CASE WHEN DATE(c.cred_create_at) = ? THEN 1 END) as nuevos,
                    COUNT(CASE WHEN c.cred_estado = 'E' THEN 1 END) as activos,
                    (
                        select 
                            COUNT(DISTINCT cr.cred_codigo) as v
                        from credito cr 
                        JOIN cuota cu ON cr.cred_codigo = cu.cuo_credito
                        WHERE 
                            DATE(cu.cuo_vencimiento) < ? AND 
                            cu.cuo_estado = 'V' AND cr.cred_sucursal = ? 
                            AND cr.cred_estado = 'E'
                    ) as vencidos,
                    (
                        select 
                            COUNT(DISTINCT cr.cred_codigo) as vs
                        from credito cr 
                        JOIN cuota cu ON cr.cred_codigo = cu.cuo_credito
                        WHERE 
                            DATE(cu.cuo_vencimiento) = ? AND 
                            (cu.cuo_estado = 'V' OR cu.cuo_estado = 'E') AND 
                            cr.cred_sucursal = ?
                    ) as cumplidos 
                FROM credito c 
                WHERE 
                    c.cred_sucursal = ?                
            ";
            $res = $this->db->query($sql, Array(
                $this->curDate,
                $this->curDate,
                $this->sede_id,
                $this->curDate,
                $this->sede_id,
                $this->sede_id
            ));
            return $res->row();
        }
        function creditosMontoPrestado($custonDate = null){
            $fecha = $custonDate ?? $this->curDate;
            $this->db->select("SUM(cred_monto) as monto");
            $this->db->where("DATE(cred_create_at)", $fecha);
            $this->db->where("cred_sucursal", $this->sede_id);
            $res = $this->db->get("credito");
            return $res->row();
        }
        function creditosMontoRecuperado($custonDate = null){
            $fecha = $custonDate ?? $this->curDate;
            $this->db->select("SUM(c.cred_monto) as monto");
            $this->db->where("c.cred_estado", "R");
            $this->db->where("DATE(c.cred_liquidacion)", $fecha);
            $this->db->where("c.cred_sucursal", $this->sede_id);
            // $this->db->join("credito c", "c.cred_codigo = p.pag_credito");
            $res = $this->db->get("credito c");
            return $res->row();
        }
        function creditos_activos_v1(){
            $this->db->where("cred_estado", "E");
            $this->db->where("cred_sucursal", $this->sede_id);
            $this->db->select("cred_codigo as id");
            $this->db->select("cred_serie as serie");
            $this->db->select("cred_monto as monto");
            $this->db->select("(
                SELECT COUNT(*) FROM cuota WHERE (cuo_estado='V' OR cuo_estado='E') AND cuo_credito=cred_codigo and cuo_vencimiento <= CURDATE()
            ) as cuotas_vencidas");
            $this->db->select("cl.cli_codigo as cliente_id");
            $this->db->select("CONCAT(
                cl.cli_nombres,' ',
                cl.cli_paterno,' ',
                cl.cli_materno) as cliente_nombre
            ");
            $this->db->select("cl.cli_puntuacion as cliente_puntuacion");
            $this->db->select("
                IF (
                    cred_prenda > 0,
                    (SELECT pren_descripcion FROM prenda WHERE pren_codigo = cred_prenda),
                    'Sin Garantia'
                ) as prenda_descripcion,
            ");
            $this->db->join("cliente cl", "cl.cli_codigo = cred_cliente");
            $this->db->order_by("cuotas_vencidas", "DESC");
            $res = $this->db->get("credito");
            return $res->result();
        }

        function creditos_activos($settings = []){
            $this->db->select("
                IF(
                    (SELECT cuo_vencimiento from cuota where cuo_vencimiento = CURDATE() and cuo_credito = c.cred_codigo ORDER BY cuo_orden DESC LIMIT 1),
                    'SI','NO'
                ) as vence_hoy
            ");
            $this->db->select("
                (SELECT cuo_vencimiento from cuota where cuo_vencimiento = CURDATE() and cuo_credito = c.cred_codigo ORDER BY cuo_orden DESC LIMIT 1) as proximo_vencimiento
            ");
            $this->db->where("c.cred_estado", "E");
            $this->db->where("c.cred_sucursal", $this->sede_id);
            $this->db->select("c.cred_codigo as id");
            $this->db->select("c.cred_prenda as prenda_id");
            $this->db->select("c.cred_serie as serie");
            $this->db->select("c.cred_monto as monto");
            $this->db->select("c.cred_pago_semanal as pago_monto");
            $this->db->select("(
                SELECT COUNT(*) FROM cuota WHERE (cuo_estado='V' OR cuo_estado='E') AND cuo_credito=c.cred_codigo and cuo_vencimiento <= CURDATE()
            ) as cuotas_vencidas");
            $this->db->select("cl.cli_codigo as cliente_id");
            $this->db->select("CONCAT(
                cl.cli_nombres,' ',
                cl.cli_paterno,' ',
                cl.cli_materno) as cliente_nombre
            ");
            $this->db->select("cl.cli_puntuacion as cliente_puntuacion");
            $this->db->select("
                IF (
                    c.cred_prenda > 0,
                    (SELECT pren_descripcion FROM prenda WHERE pren_codigo = c.cred_prenda),
                    'Sin Garantia'
                ) as prenda_descripcion,
            ");
            $this->db->select("
                (SELECT cuo_vencimiento FROM cuota 
                WHERE cuo_credito = c.cred_codigo
                ORDER BY cuo_orden DESC LIMIT 1) as 'cuota_ultima'
            ");
            $this->db->select("c.cred_ciclo_pago as ciclo_pago");
            $this->db->select("
                (SELECT 
                    DATE_FORMAT(cuo_vencimiento, '%d/%m/%Y') 
                FROM cuota 
                WHERE 
                    cuo_credito = c.cred_codigo and cuo_estado = 'E'
                ORDER BY cuo_orden DESC LIMIT 1) as 'pago_proximo'
            ");
            $this->db->select("
                IFNULL(
                    (SELECT DATE_FORMAT(create_at, '%d/%m/%Y') FROM pago 
                        WHERE  pag_credito = c.cred_codigo
                        ORDER BY pag_codigo DESC LIMIT 1
                    ), '00/00/0000'
                ) as 'ultimo_pago'
            ");
            $this->db->join("cliente cl", "cl.cli_codigo = c.cred_cliente");
            $this->db->order_by("cuotas_vencidas", "DESC");
            // $this->db->limit(10);
            $res = $this->db->get("credito c");
            return $res->result();
        }
        function cuotas_vencidas($id){
            $current_date = date("Y-m-d");
            $this->db->where("cuo_credito", $id);
            $this->db->where("DATE(cuo_vencimiento) <= ", $current_date);
            $this->db->where("(cuo_estado = 'E' OR cuo_estado = 'V')");
            $this->db->select("cuo_codigo as id");            
            $this->db->select("DATE(cuo_vencimiento) as vencimiento");
            $res = $this->db->get("cuota");
            return $res->result();
        }
        function creditos_generados($settings = []){
            if (!empty($settings['fecha_creacion'])){
                $this->db->where("c.cred_creacion", $settings['fecha_creacion']);
            }
            $this->db->select([
                'c.cred_codigo as id',
                'c.cred_serie as serie',
                "DATE_FORMAT(c.cred_create_at, '%d/%m/%Y') as fecha_creacion",
                "DATE_FORMAT(c.cred_create_at, '%d/%m/%Y - %I:%i %p') as fecha_hora_creacion",
                'c.cred_monto as monto',
                'c.cred_prenda as prenda_id',
                'c.cred_cliente as cliente_id',
                "IFNULL(p.pren_descripcion, 'SIN GARANTIA') as prenda_descripcion",
                "CONCAT_WS(' ', cl.cli_nombres, cl.cli_paterno, cl.cli_materno) as cliente_nombre_full"
            ]);
            $this->db->from('credito c');
            $this->db->join('prenda p', 'p.pren_codigo = c.cred_prenda', 'left');
            $this->db->join('cliente cl', 'cl.cli_codigo = c.cred_cliente');

            if (!empty($settings['fecha_creacion'])) {
                $this->db->where('c.cred_creacion', $settings['fecha_creacion']);
            }

            $this->db->order_by('c.cred_create_at', 'DESC');
            $query = $this->db->get();
            return $query->result();
        }
        function pagos_recibidos($settings = []){
            if (!empty($settings['fecha_creacion'])) {
                $this->db->where("DATE(p.pag_fecha)", $settings['fecha_creacion']);
            }

            $this->db->select([
                'p.pag_codigo as id',
                'p.pag_serie as serie',
                'p.pag_total as total',
                'p.pag_concepto as concepto',
                'p.pag_forma_pago as forma_pago',
                "DATE_FORMAT(p.pag_fecha, '%d/%m/%Y') as fecha_creacion",
                "DATE_FORMAT(p.pag_fecha, '%d/%m/%Y - %I:%i %p') as fecha_hora_creacion",
                'c.cred_cliente as cliente_id',
                "IFNULL(pr.pren_descripcion, 'SIN GARANTIA') as prenda_descripcion",
                "CONCAT_WS(' ', cl.cli_nombres, cl.cli_paterno, cl.cli_materno) as cliente_nombre_full"
            ]);

            return $this->db
                ->from('pago p')
                ->join('credito c', 'c.cred_codigo = p.pag_credito')
                ->join('cliente cl', 'cl.cli_codigo = c.cred_cliente')
                ->join('prenda pr', 'pr.pren_codigo = c.cred_prenda', 'left')
                ->order_by('p.pag_fecha', 'DESC')
                ->order_by('p.pag_codigo', 'DESC')
                ->get()
                ->result();
        }

    }