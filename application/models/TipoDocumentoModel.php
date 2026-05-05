<?php  
	
	class TipoDocumentoModel extends CI_Model
	{
		
		function consulta($tipo = 'rest', $p1 = '', $p2 = '')
		{
			switch ($tipo) {
				case 'rest':
					// $sql = "CALL proc_tipo_documento_lista()";
					$sql = "
						SELECT 
							td.tdoc_codigo as 'id',
							td.tdoc_nombre as 'nombre',
							td.tdoc_descripcion as 'descripcion',
							td.tdoc_max_caracteres as 'car_minimo',
							td.tdoc_max_caracteres as 'car_maximo',
							td.tdoc_estado as 'estado'
						FROM tipo_documento td
					";
					$cons = $this->db->query($sql);
					return $cons->result();
					break;
				
				default:
					# code...
					break;
			}
		}

	}
?>	