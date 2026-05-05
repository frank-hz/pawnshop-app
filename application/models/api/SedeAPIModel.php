<?php 
class SedeAPIModel extends CI_Model {
    


    function verifyExists($id) {
        // $this->db->where(array('suc_codigo' => $id));
        // $query = $this->db->get('sucursal');
        // return $query->num_rows() > 0;
        $this->db->where('suc_codigo', intval($id));
        $query = $this->db->get('sucursal');
        return $query->num_rows();
    }

    function getAll($filter = []){
        if (!empty($filter['nombre'])){
            $this->db->like('suc_nombre',$filter['nombre']);
        }
        if (!empty($filter['limite'])){
            $this->db->limit($filter['limite']);
        }
        $this->db->select('suc_codigo as id');
        $this->db->select('suc_nombre as nombre');
        $this->db->select('suc_orden as orden');
        $this->db->select('suc_sigla as sigla');
        $this->db->select('suc_serie as serie');
        $this->db->select('suc_pago_serie as pago_serie');
        $this->db->select('suc_banco as banco');
        $this->db->select('suc_cuenta_tipo as cuenta_tipo');
        $this->db->select('suc_cuenta_numero as cuenta_numero');
        $this->db->select('suc_direccion as direccion');
        $this->db->select('suc_telefono as telefono');
        $this->db->select('suc_creacion as creacion');
        $this->db->select('suc_estado as estado');
        $this->db->select('suc_ubigeo as ubigeo_id');
        $this->db->select('ubi_departamento as departamento');
        $this->db->select('ubi_provincia as provincia');
        $this->db->select('ubi_distrito as distrito');
        $this->db->join('ubigeo u','u.ubi_codigo = sucursal.suc_ubigeo');
        $res = $this->db->get('sucursal');
        return $res->result();
    }

    function basic_getAll($filter = []){
        if (!empty($filter['nombre'])){
            $this->db->like('suc_nombre',$filter['nombre']);
        }
        if (!empty($filter['limite'])){
            $this->db->limit($filter['limite']);
        }
        $this->db->select('suc_codigo as id');
        $this->db->select('suc_nombre as nombre');
        $this->db->select('suc_serie as serie');
        $this->db->select('suc_cuenta_tipo as cuenta_tipo');
        $this->db->select('suc_cuenta_numero as cuenta_numero');
        $this->db->select('suc_direccion as direccion');
        $this->db->select('suc_telefono as telefono');
        $this->db->select('ubi_distrito as distrito');
        $this->db->join('ubigeo u','u.ubi_codigo = sucursal.suc_ubigeo');
        $res = $this->db->get('sucursal');
        return $res->result();
    }
}
