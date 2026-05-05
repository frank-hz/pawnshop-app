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
        
        //c.cred_create_at as fecha1,
        //DATE(c.cred_create_at) as fecha2,
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
    

        function creditosActivos(){
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

        function cuotasVencidas($id){
            $this->db->where("cuo_credito", $id);
            $this->db->where("DATE(cuo_vencimiento) <= CURDATE()");
            $this->db->where("(cuo_estado = 'E' OR cuo_estado = 'V')");
            $this->db->select("cuo_codigo as id");            
            $this->db->select("DATE(cuo_vencimiento) as vencimiento");
            $res = $this->db->get("cuota");
            return $res->result();
        }
    }