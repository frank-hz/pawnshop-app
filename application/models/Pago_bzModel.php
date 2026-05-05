<?php
    class Pago_bzModel extends CI_Model
    {
        function getByCredito($idcredito, $parm = []) {
            $ord_target_raw = (!empty($parm['orden_target'])) ? strtolower($parm['orden_target']) : '';			
			$orden_target = (in_array($ord_target_raw,['codigo','fecha','serie','monto'])) ? 'pag_'.$ord_target_raw : 'pag_codigo';
			$orden_type = (!empty($parm['orden_type'])) ? $parm['orden_type'] : 'ASC';

            $sql = "
				SELECT  
					pag_codigo as 'id',
					pag_serie as 'serie',
					pag_orden as 'orden',
					pag_nomen as 'nomen',
					pag_credito as 'credito_id',
					cred_serie as credito_serie,
					DATE_FORMAT(pag_fecha, '%d/%m/%Y %H:%i:%s') as 'fecha_hora',
					DATE_FORMAT(pag_fecha, '%d/%m/%Y') as 'fecha',
					pag_forma_pago as 'forma_pago_raw',
					pag_concepto as 'concepto_raw',
					pag_banco as 'banco_id',
					pag_total as 'total',
					pag_empleado as 'empleado_id',
					CONCAT(emp_nombres,' ',emp_paterno,' ',emp_materno) as 'empleado_nombre_full'				
				FROM pago 
				INNER JOIN credito ON cred_codigo = pag_credito 
				INNER JOIN empleado ON emp_codigo = pag_empleado
				WHERE pag_credito = ? 
				ORDER BY $orden_target $orden_type 
			";
			$res = $this->db->query($sql, [$idcredito]);
			return $res->result();
        }
                
    }
    
?>