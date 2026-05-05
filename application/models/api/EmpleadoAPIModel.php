<?php 
class EmpleadoAPIModel extends CI_Model {

    function getAll($filter = []){
        if (!empty($filter['nombre_apellido'])){
            $this->db->like('emp_nombres', $filter['nombre_apellido']);
            $this->db->or_like('emp_paterno', $filter['nombre_apellido']);
            $this->db->or_like('emp_materno', $filter['nombre_apellido']);
        }
        if (!empty($filter['documento'])){
            $this->db->like('emp_documento', $filter['documento']);
        }

        $this->db->select('e.emp_codigo as id');
        $this->db->select('e.emp_documento as documento');
        $this->db->select('e.emp_tipo_documento as tipo_documento');
        $this->db->select('e.emp_nombres as nombres');
        $this->db->select('e.emp_paterno as paterno');
        $this->db->select('e.emp_materno as materno');
        $this->db->select('e.emp_direccion as direccion');
        $this->db->select('e.emp_telefono as telefono');
        $this->db->select('e.emp_email as email');
        $this->db->select('e.emp_estado as estado');
        $this->db->select('e.emp_creacion as creacion');
        $this->db->select('e.create_at');
        $this->db->select('e.update_at');
        $this->db->select('u.ubi_codigo as ubigeo_id');
        $this->db->select('u.ubi_distrito as ciudad');
        $this->db->select('s.suc_codigo as sede_id');
        $this->db->select('s.suc_nombre as sede_nombre');
        $this->db->from('empleado e');
        $this->db->join('ubigeo u', 'u.ubi_codigo = e.emp_ubigeo');
        $this->db->join('sucursal s', 's.suc_codigo = e.emp_sucursal');
        $res = $this->db->get();
        return $res->result();
    }

    function getById($id){        
        $this->db->select('e.emp_codigo as id');
        $this->db->select('e.emp_documento as documento');
        $this->db->select('e.emp_tipo_documento as tipo_documento');
        $this->db->select('e.emp_nombres as nombres');
        $this->db->select('e.emp_paterno as paterno');
        $this->db->select('e.emp_materno as materno');
        $this->db->select('e.emp_direccion as direccion');
        $this->db->select('e.emp_telefono as telefono');
        $this->db->select('e.emp_email as email');
        $this->db->select('e.emp_estado as estado');
        $this->db->select('e.emp_creacion as creacion');
        $this->db->select('e.create_at');
        $this->db->select('e.update_at');
        $this->db->select('u.ubi_codigo as ubigeo_id');
        $this->db->select('u.ubi_distrito as ciudad');
        $this->db->select('s.suc_codigo as sede_id');
        $this->db->select('s.suc_nombre as sede_nombre');
        $this->db->from('empleado e');
        $this->db->join('ubigeo u', 'u.ubi_codigo = e.emp_ubigeo');
        $this->db->join('sucursal s', 's.suc_codigo = e.emp_sucursal');
        $this->db->where('e.emp_codigo', $id);
        $res = $this->db->get();
        return $res->row();
    } 

    function insert($data){
        return $this->db->insert('empleado', $data);
    }

    function update($id, $data){
        $this->db->where('emp_codigo', $id);
        $this->db->update('empleado', $data);
        return ($this->db->affected_rows() > 0);
    }

    function delete($id){
        $this->db->delete('empleado', array('emp_codigo' => $id)); 
        return ($this->db->affected_rows() > 0);
    }

    

    // Verifica si la columna tiene valores repetidos 
    // a excepcion de la especifcada por id
    function verifyForUpdate(array $column, $id){
        $this->db->where($column);
        $this->db->where("emp_codigo !=", $id);
        $res = $this->db->get("empleado");
        return $res->result();
    }


    

}
