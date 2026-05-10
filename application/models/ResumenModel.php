<?php
class ResumenModel extends CI_Model {
    
    function getCreditosGenerados($settings = []){
        if (!empty($settings['fecha_creacion'])){
            $this->db->where("c.cred_creacion", $settings['fecha_creacion']);
        }
        $this->db->select("c.cred_codigo as id");
        $this->db->select("c.cred_serie as serie");
        $this->db->select("
            DATE_FORMAT(c.cred_create_at, '%d/%m/%Y')
            as fecha_creacion
        ");
        $this->db->select("
            DATE_FORMAT(c.cred_create_at, '%d/%m/%Y - %I:%i %p')
            as fecha_hora_creacion
        ");
        $this->db->select("c.cred_monto as monto");
        $this->db->select("c.cred_prenda as prenda_id");
        $this->db->select("c.cred_cliente as cliente_id");
        $this->db->select("IFNULL(
            p.pren_descripcion, 'SIN GARANTIA'
        ) as prenda_descripcion");
        $this->db->select("CONCAT_WS(
            ' ', cl.cli_nombres, cl.cli_paterno, cl.cli_materno
        ) as cliente_nombre_full");

        $this->db->from("credito c");
        $this->db->join("prenda p", "p.pren_codigo = c.cred_prenda", "left");
        $this->db->join("cliente cl", "cl.cli_codigo = c.cred_cliente");
        $resp = $this->db->get();
        return $resp->result();
    }
    function getPagosRecibidos($settings = []){
        if (!empty($settings['fecha_creacion'])){
            $this->db->where("DATE(p.pag_fecha)", $settings['fecha_creacion']);
        }

        $this->db->select("p.pag_codigo as id");
        $this->db->select("p.pag_serie as serie");
        $this->db->select("p.pag_total as total");
        $this->db->select("p.pag_concepto as concepto");
        $this->db->select("p.pag_forma_pago as forma_pago");
        $this->db->select("
            DATE_FORMAT(p.pag_fecha, '%d/%m/%Y')
            as fecha_creacion
        ");
        $this->db->select("
            DATE_FORMAT(p.pag_fecha, '%d/%m/%Y - %I:%i %p')
            as fecha_hora_creacion
        ");
        
        $this->db->select("c.cred_cliente as cliente_id");
        $this->db->select("IFNULL(
            pr.pren_descripcion, 'SIN GARANTIA'
        ) as prenda_descripcion");
        $this->db->select("CONCAT_WS(
            ' ', cl.cli_nombres, cl.cli_paterno, cl.cli_materno
        ) as cliente_nombre_full");
        $this->db->from("pago p");
        $this->db->join("credito c", "c.cred_codigo = p.pag_credito");
        $this->db->join("cliente cl", "cl.cli_codigo = c.cred_cliente");
        $this->db->join("prenda pr", "pr.pren_codigo = c.cred_prenda", "left");
        $resp = $this->db->get();
        return $resp->result();
    }
}
