<?php
    class ProcesadorModel extends CI_Model
    {
        function Get_PrestamoRegistroDiaCantidad($suc)
		{
            $fecha = date("y-m-d");
			$sql = "
				SELECT COUNT(*) as cantidad FROM credito 
                WHERE 
                    DATE(cred_creacion) = ? AND 
                    cred_sucursal = ? 
			";
			$res = $this->db->query($sql, array($fecha, $suc));
			return $res->row();
        }
        
        function Get_PrestamoActivosCantidad($suc)
		{
            $fecha = date("y-m-d");
			$sql = "
				SELECT COUNT(*) as cantidad FROM credito 
                WHERE
                    cred_sucursal = ? AND
                    cred_estado = 'E'
			";
			$res = $this->db->query($sql, array($suc));
			return $res->row();
        }

        function Get_ClienteRegistroDiaCantidad()
		{
            $fecha = date("y-m-d");
			$sql = "
				SELECT COUNT(*) as cantidad FROM cliente 
                WHERE 
                    DATE(cli_fecha) = ? 
			";
			$res = $this->db->query($sql, array($fecha));
			return $res->row();
        }

        function Get_PrestamoFinalizaCantidad($suc)
		{
            $fecha = date("y-m-d");
			$sql = "
				SELECT COUNT(*) as cantidad FROM credito 
                WHERE 
                    DATE(cred_liquidacion) = ? AND 
                    cred_sucursal = ? 
			";
			$res = $this->db->query($sql, array($fecha, $suc));
			return $res->row();
        }

        function Get_CreditoEstadoCantidad($suc, $estado){
            if ($estado === 'en_curso') {
                $sql = "
                    SELECT COUNT(*) as cantidad FROM credito 
                    WHERE 
                        cred_sucursal = ? AND
                        (cred_estado = 'E' OR cred_estado = 'A' OR cred_estado = 'CC')  
                ";
                $res = $this->db->query($sql, array($suc));
            }else{
                $sql = "
                    SELECT COUNT(*) as cantidad FROM credito 
                    WHERE 
                        cred_sucursal = ? AND
                        cred_estado = ?  
                ";
                $res = $this->db->query($sql, array($suc, $estado));
            }
			return $res->row();
        }

    }
    
?>