<?php  
	
	class RevisionModel extends CI_Model
	{	
		function getCuotasActivas($idcredito){
			$sql = "
				SELECT 
					cuo_codigo as id,
					cuo_credito as credito_id,
					cred_monto as credito_monto,
					cred_prorroga as credito_prorroga,
					cred_cargo as credito_cargo,
					
					cuo_orden as orden,
					cuo_vencimiento as vencimiento,
					cuo_inicio_mora as inicio_mora,
					cuo_monto as monto,
					cred_penalidad as penalidad,
					cuo_estado as estado
					
				FROM cuota 
				WHERE 
					cuo_codigo = ?
				ORDER by cuo_codigo ASC
			";
		}


	}
?>
