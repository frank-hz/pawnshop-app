<?php 
class UsuarioAPIModel extends CI_Model {

    function insert($data){
        return $this->db->insert('usuario', $data);
    }

    function update($id, $data){
        $this->db->where('usu_codigo', $id);
        $this->db->update('usuario', $data);
    }

    function delete($id){
        return $this->db->delete('usuario', array('usu_codigo' => $id)); 
    }


    function getRol($id){        
        $this->db->select('u.usu_codigo as id');
        $this->db->select('r.rol_keyname as keyname');
        $this->db->select('u.usu_dev_work as dev_work');
        $this->db->from('usuario u');
        $this->db->join('roles r', 'r.rol_codigo = u.usu_rol');
        $this->db->where('u.usu_codigo',$id);
        $res = $this->db->get();
        return $res->row();
    }
}
