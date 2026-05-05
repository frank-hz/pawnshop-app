<?php 
    class AnalisisNeonModel extends CI_Model {
        
        protected $idsucursal = null;

        function __construct(){
            $this->idsucursal = $this->session->userdata('pres_ses_sucursal');
        }

        public function get_pagos_dia(){
            $sql = "
                SELECT 
                    pag_codigo as id_pago,
                    pag_credito as id_credito,
                    pag_serie as serie_pago,
                    cred_serie as serie_credito,
                    pdet_tipo as tipo,
                    pdet_referencia as referencia,
                    pdet_monto as monto
                FROM pago_detalle
                INNER JOIN pago ON pag_codigo = pdet_pago
                INNER JOIN credito ON cred_codigo = pag_credito
                WHERE 
                    DATE(pag_fecha) = ? AND
                    cred_sucursal = ? 
                ORDER BY pag_codigo ASC 
            ";
            $res = $this->db->query($sql, ["2021-09-24", $this->idsucursal]);
            return $res->result();
        }

        public function get_descuentos_dia(){
            $sql = "
                SELECT 
                    pag_codigo as id_pago,
                    pag_credito as id_credito,
                    pag_serie as serie_pago,
                    cred_serie as serie_credito,
                    'DESC' as tipo,
                    'Descuento de Pago' as referencia,
                    pag_descuento as monto,
                    'Calculo de Descuentos' as uso
                FROM pago
                INNER JOIN credito ON cred_codigo = pag_credito
                WHERE 
                    YEAR(pag_fecha) != 0 AND
                    DATE(pag_fecha) = ? AND
                    cred_sucursal = ? AND 
                    pag_descuento > 0
                ORDER BY pag_codigo ASC 
            ";
            $res = $this->db->query($sql, ["2021-09-24", $this->idsucursal]);
            return $res->result();
        }

    }