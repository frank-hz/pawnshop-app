<?php
class ExporterModel extends CI_Model {

    
    function get_clientes($prefs = []){
        $orden = $this->validar_orden_busqueda($prefs['orden_tipo'] ?? null);
        $limite = !empty($prefs['limite']) ? $prefs['limite'] : 20;
        
        $this->db->select("c.cli_codigo as id");
        $this->db->select("c.cli_documento as documento");
        $this->db->select("c.cli_tipo_documento as tipo_documento");
        $this->db->select("c.cli_nombres as nombres");
        $this->db->select("c.cli_paterno as paterno");
        $this->db->select("c.cli_materno as materno");
        $this->db->select("CONCAT_WS(' ',cli_nombres,cli_paterno,cli_materno) as nom_ape");
        $this->db->select("CONCAT_WS(' ',cli_nombres,cli_paterno,cli_materno) as nombre_full");
        $this->db->select("u.ubi_distrito as distrito");
        $this->db->select("u.ubi_distrito as ciudad");
        $this->db->select("u.ubi_departamento as departamento");
        $this->db->select("u.ubi_provincia as provincia");
        $this->db->select("c.cli_direccion as direccion");
        $this->db->select("c.cli_telefono as telefono");
        $this->db->select("c.cli_email as email");
        $this->db->select('u.ubi_codigo as ubigeo_id');
        $this->db->select("cli_codigo as id");
        $this->db->join("ubigeo u", "u.ubi_codigo = c.cli_ubigeo");
        $this->db->limit($limite);
        $this->db->order_by("c.cli_nombres", $orden);

        $this->aplicar_filtros($prefs);
        $res = $this->db->get("cliente c");
        return $res->result_array();
    }
    



    private function validar_orden_busqueda($value = null){
        $tipos = ['ASC','DESC'];
        if (empty($value) || $value === null) {
            return FALSE;
        }
        $value = trim(mb_strtoupper($value));
        return in_array($value, $tipos) ? $value : 'ASC';
    }
    private function aplicar_filtros(array $filtros){
        $fecha_fija  = $filtros['fecha'];
        $desde  = $filtros['fecha_desde'];
        $hasta  = $filtros['fecha_hasta'];

        if ($fecha_fija) {
            $this->db->where('fecha_creacion', $fecha_fija);
        }else {
            if ($desde) {
                $this->db->where('fecha_creacion' . ' >=', $desde . ' 00:00:00');
            }
            if ($hasta) {
                $this->db->where('fecha_creacion' . ' <=', $hasta . ' 23:59:59');
            }
        }
    }
}