<?php 
    class NotTemplateModel extends CI_Model {
        
        function getAll(){
            // $this->db->select("*");
            $res = $this->db->get("notificacion_template");
            return $res->result();
        }
    
    }