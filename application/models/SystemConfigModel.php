<?php
    class SystemConfigModel extends CI_Model
    {
        // BASIC 
        function get(){
            $sql = "
                SELECT 
                    sy.id,
                    v.file_name as view_login,
                    v.file_title as view_login_title
                FROM system_config sy
                LEFT JOIN view_login v ON v.id = sy.view_login
                ORDER BY id ASC LIMIT 1
            ";
            $res = $this->db->query($sql);
            return $res->row();
        }

        function get_all(){
            
        }

        function insert($data){
			return $this->db->insert('system_config', $data);
		}

        function update($id, $data){
			$this->db->where('id', $id);
			$this->db->update('system_config', $data);
		}

        function delete($id){

        }

        // CUSTOM

        
    }
    // formulario de trámite, 
    // pago por derecho de certificado, 
    // constancia de no adeudo y 
    // kardex de notas de egresado
    