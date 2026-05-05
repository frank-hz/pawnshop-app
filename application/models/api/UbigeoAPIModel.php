<?php 
class UbigeoAPIModel extends CI_Model {
    
    function getAllSearch($filter){        
        $this->db->select('ubi_codigo as id');
        $this->db->select('ubi_departamento as departamento');
        $this->db->select('ubi_provincia as provincia');
        $this->db->select('ubi_distrito as distrito');
        $this->db->select('ubi_distrito as ciudad');
        $this->db->order_by('ubi_distrito', 'ASC');
        if (!empty($filter['ciudad'])){
            $this->db->like('ubi_distrito', $filter['ciudad']);
        }
        if (!empty($filter['limite'])){
            $this->db->limit($filter['limite']);
        }
        $res = $this->db->get('ubigeo');
        return $res->result();
    }


    function verifyExists($id) {
        $this->db->where('ubi_codigo', intval($id));
        $query = $this->db->get('ubigeo');
        return $query->num_rows();
    }
}
