<?php
    class AdminModel extends CI_Model{
    
        function get_data_usuarios(){
            $sql = "
                SELECT 
                    usu_codigo as id,
                    usu_usuario as usuario,
                    usu_clave as clave
                FROM usuario
            ";
            $res = $this->db->query($sql);
            return $res->result();
        }
        
        function update_user_pass($id, $pass){
            $sql = "
                UPDATE usuario 
                SET usu_clave = ? 
                WHERE usu_codigo = ?
            ";
            $res = $this->db->query($sql, [$pass, $id]);
            return $res;
        }
     
        

    }
?>