<?php 

    class CreditoAPIModel extends CI_Model {
        
        function getById($id){
            $sql = "
                SELECT 
                    c.cred_codigo as id,
                    c.cred_serie as serie,
                    c.cred_monto as monto,
                    DATE_FORMAT(c.cred_creacion, '%d/%m/%Y') as fecha_creacion,
                    DATE_FORMAT(c.cred_creacion, '%H:%i:%s') as hora,
                    DATE_FORMAT(c.cred_liquidacion, '%d/%m/%Y') as fecha_liquidacion,
                    c.cred_estado as estado,
                    e.emp_codigo as empleado_id,
                    CONCAT(
                        e.emp_nombres,' ',
                        e.emp_paterno,' ',
                        e.emp_materno
                    ) as empleado_nombre_full,
                    cl.cli_codigo as cliente_id,
                    CONCAT(
                        cl.cli_nombres,' ',
                        cl.cli_paterno,' ',
                        cl.cli_materno
                    ) as cliente_nombre_full
                FROM credito c 
                INNER JOIN cliente cl ON cl.cli_codigo = c.cred_cliente
                INNER JOIN empleado e ON e.emp_codigo = c.cred_empleado
                WHERE c.cred_codigo = ?
            ";
            $res = $this->db->query($sql, Array($id));
            return $res->row();
        }
    }
    