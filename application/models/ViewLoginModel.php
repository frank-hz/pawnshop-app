<?php
    class ViewLoginModel extends CI_Model
    {
        function getAll(){
            $sql = "
                SELECT 
                    v.id,
                    v.file_title,
                    v.file_preview,
                    v.file_name,
                    IF(sy.id, 'IN_USE', 'NO_USE') as status
                FROM view_login v 
                LEFT JOIN system_config sy ON v.id = sy.view_login
            ";
            $query = $this->db->query($sql);
            return $query->result();
            // IF(v.id = sy.login, 'A', 'I') as status
        }
    }
    