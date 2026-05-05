<?php
    class PreferenciasModel extends CI_Model
    {
        
        // B A S I C
        function get(){

        } 

        function getAll(){

        } 

        function insert($data){
            return $this->db->insert('preferencias', $data);
        } 

        function update($id, $data){
            $this->db->where('id', $id);
            return $this->db->update('preferencias', $data);
        } 

        function delete(){

        } 


        // C U S T O M
        function getByUser($id){
            $sql = "
                SELECT 
                    p.id,
                    p.usuario_id,
                    p.sucursal_init as sede_init,
                    p.dashboard_theme,
                    p.dashboard_design,
                    s.suc_nombre as sede_nombre
                FROM preferencias p
                LEFT JOIN sucursal s ON s.suc_codigo = p.sucursal_init 
                WHERE p.usuario_id = ?
            ";
            $res = $this->db->query($sql, [$id]);
            return $res->row();
        }

        

        function updateByUser($iduser, $data){
            $this->db->where('usuario_id', $iduser);
            return $this->db->update('preferencias', $data);
        }
    }
    