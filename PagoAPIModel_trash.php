<?php

    class PagoAPIModel extends CI_Model
    {
        public function getPago($idpago){
            $sql = "
                SELECT
                    p.pag_codigo as 'id',
                    c.cred_codigo as 'credito',
                    c.cred_serie as 'codigo_credito',
                    p.pag_serie as 'serie',
                    DATE_FORMAT(p.pag_fecha, '%d/%m/%Y %H:%i:%s') as 'fecha',
                    p.pag_forma_pago as 'forma',
                    p.pag_comprobante as 'comprobante',
                    IF(
                        (p.pag_forma_pago = 'DEP' OR p.pag_forma_pago = 'TRAN') AND p.pag_banco > 0,
                        (select ban_nombre from banco WHERE ban_codigo = p.pag_banco),
                        ''
                    ) as 'banco',
                    p.pag_subtotal as 'subtotal',
                    p.pag_descuento as 'descuento',
                    p.pag_total as 'total',
                    p.pag_recibido as 'recibido',
                    p.pag_devuelto as 'devuelto',
                    p.pag_concepto as 'concepto',
                    CONCAT(e.emp_nombres,' ',e.emp_paterno,' ',e.emp_materno) as 'empleado',
                    CONCAT(cl.cli_nombres,' ',cl.cli_paterno,' ',cl.cli_materno) as 'cliente'
                FROM pago p 
                INNER JOIN credito c ON c.cred_codigo = p.pag_credito
                INNER JOIN empleado e ON e.emp_codigo = p.pag_empleado
                INNER JOIN cliente cl ON cl.cli_codigo = c.cred_cliente
                WHERE 
                    p.pag_codigo = ?
            ";
            $res = $this->db->query($sql, [$idpago]);
            return $res->row();
        }

        public function getPagoDetalle($idpago){
            $sql = "
                SELECT
                    pd.pdet_codigo as 'id',
                    pd.pdet_tipo as 'tipo',
                    IF(
                        pd.pdet_tipo = 'CU', 
                        (select concat('- Vencimiento: ',DATE_FORMAT(cuo_vencimiento, '%d/%m/%Y')) from cuota where cuo_codigo = pd.pdet_id_ref), 
                        ''
                    ) as 'subref',
                    pd.pdet_referencia as 'referencia',
                    pd.pdet_id_ref as 'id_referencia',
                    pd.pdet_monto as 'monto'
                FROM pago_detalle pd 
                WHERE pd.pdet_pago = ?
            ";
            $res = $this->db->query($sql, [$idpago]);
            return $res->result();
        }

        public function getPagoVoucher($idpago){
            $sql = "
                SELECT
                    p.pag_codigo as 'id',
                    p.pag_serie as 'codigo',
                    c.cred_serie as 'credito',
                    CONCAT(cl.cli_nombres,' ',cl.cli_paterno,' ',cl.cli_materno) as 'cliente',
                    DATE_FORMAT(p.pag_fecha, '%d/%m/%Y %H:%i') as 'fecha',
                    p.pag_recibido as 'recibido',
                    p.pag_devuelto as 'devuelto',
                    p.pag_descuento as 'descuento',
                    p.pag_descuento as 'total',
                    p.pag_concepto as 'concepto'		
                FROM pago p 
                INNER JOIN credito c ON c.cred_codigo = p.pag_credito
                INNER JOIN cliente cl ON cl.cli_codigo = c.cred_cliente
                WHERE 
                    p.pag_codigo = ?
            ";
            $res = $this->db->query($sql, [$idpago]);
            return $res->row();
        }
        public function getPagoDetalleVoucher($idpago){
            $sql = "
                SELECT
                    pd.pdet_codigo as 'id',
                    pd.pdet_tipo as 'tipo',
                    pd.pdet_referencia as 'referencia',
                    (CASE 
                    WHEN pd.pdet_tipo = 'MO' THEN 
                        (SELECT 
                            CONCAT('S/ ',c.cuo_penalidad,' x ',m.mor_dias,' dias')
                            FROM mora m INNER JOIN cuota c ON c.cuo_codigo = m.mor_cuota 
                            WHERE m.mor_codigo = pd.pdet_id_ref
                        )
                    WHEN pd.pdet_tipo = 'CU' THEN 
                        (SELECT c.cuo_vencimiento FROM cuota c WHERE c.cuo_codigo = pd.pdet_id_ref)
                    WHEN pd.pdet_tipo = 'ADEL' THEN ''
                    WHEN pd.pdet_tipo = 'AMOR' THEN ''
                    ELSE ''
                    END) as 'subinfo',
                    pd.pdet_monto as 'monto'
                FROM pago_detalle pd 
                WHERE pd.pdet_pago = ?
            ";
            $res = $this->db->query($sql, [$idpago]);
            return $res->result();
        }
    }
    