<?php
    class Credito_bzModel extends CI_Model
    {
		function getById($id){
			$curdate = date("Y-m-d");
			$sql = "
				SELECT  
					c.cred_codigo as 'id',
					c.cred_serie as 'codigo',
					c.cred_serie as 'serie',
					DATE_FORMAT(c.cred_create_at, '%d/%m/%Y - %h:%i %p') as 'fecha_creacion',
					DATE_FORMAT(c.cred_create_at, '%h:%i %p') as 'hora_creacion',
					c.cred_creacion as 'fecha_creacion_raw',
					IF (
						DATE(c.cred_liquidacion),
						DATE_FORMAT(c.cred_liquidacion, '%d/%m/%Y'),
						''
					) as liquidacion_fecha_raw,
					IF (
						TIME(c.cred_liquidacion),
						TIME_FORMAT(c.cred_liquidacion, '%h:%i %p'),
						''
					) as liquidacion_hora_raw,
					DATE_FORMAT(c.cred_liquidacion, '%d/%m/%Y %h:%i %p') as liquidacion_fecha,
					DATE_FORMAT(c.cred_liquidacion, '%d/%m/%Y %h:%i %p') as fecha_liquidacion,
					c.cred_empleado as 'empleado_id',
					CONCAT(
						emp_nombres,' ',emp_paterno,' ',emp_materno
					) as 'empleado_nombre',
					c.cred_cliente as 'cliente_id',
					CONCAT(
						cli_nombres,' ',cli_paterno,' ',cli_materno
					) as 'cliente_nombre',
					cli_email as cliente_email,
					c.cred_prenda as 'prenda_id',
					c.cred_monto as 'monto',
					c.cred_prorroga as 'prorroga',
					c.cred_cargo as 'cargo',
					c.cred_tasa as 'tasa',
					c.cred_tipo_tasa as 'tipo_tasa',
					c.cred_pago_semanal as 'pago',
					c.cred_penalidad as 'penalidad',
					c.cred_amortizacion as 'amortizacion',
					c.cred_adelanto as 'adelanto',
					c.cred_aplica_penalidad as 'aplica_penalidad',
					c.cred_ciclo_pago as 'ciclo_pago',
					c.cred_ciclo_ref as 'ciclo_ref',
					(SELECT 
							DATE_FORMAT(cuo_vencimiento, '%d/%m/%Y') 
						FROM cuota 
						WHERE 
							cuo_credito = c.cred_codigo and cuo_estado = 'E'
					ORDER BY cuo_orden DESC LIMIT 1) as 'pago_proximo',
					(SELECT 
							cuo_vencimiento
						FROM cuota 
						WHERE 
							cuo_credito = c.cred_codigo
					ORDER BY cuo_orden DESC LIMIT 1) as 'cuota_last',
					(
						SELECT 
							COUNT(*)
						FROM cuota 
						WHERE 
							(cuo_estado='E' OR cuo_estado='V' OR cuo_estado='CC') AND 
							cuo_credito = cred_codigo AND 
							cuo_vencimiento <= ?
					) as cuotas_atraso,
					c.cred_estado as 'estado',
					c.cred_sucursal as sucursal_id		
				FROM credito c 
				INNER JOIN cliente ON cli_codigo = cred_cliente 
				INNER JOIN empleado ON emp_codigo = cred_empleado
				WHERE
					c.cred_codigo = ?
			";
			$res = $this->db->query($sql,[$curdate, $id]);
			return $res->row();
		}
		function getBySerieQuick($serie){
			$sql = "
				SELECT  
					c.cred_codigo as 'id',
					c.cred_serie as 'serie',
					DATE_FORMAT(c.cred_creacion, '%d/%m/%Y') as 'fecha_creacion',
					c.cred_empleado as 'empleado_id',
					c.cred_prenda as 'prenda_id',
					c.cred_monto as 'monto',
					c.cred_ciclo_pago as 'ciclo_pago',
					c.cred_estado as 'estado'		
				FROM credito c 
				WHERE
					c.cred_serie = ?
			";
			$res = $this->db->query($sql,[$serie]);
			return $res->row();
		}
		function getByCliente($idcliente){
			$sql = "
				SELECT 
					cred_codigo as 'id',
					cred_serie as 'serie',
					DATE_FORMAT(cred_creacion, '%d/%m/%Y') as 'fecha_creacion',
					cred_creacion as 'fecha_creacion_raw',
					DATE_FORMAT(cred_liquidacion, '%d/%m/%Y')as 'fecha_liquidacion',
					cred_empleado as 'empleado_id',
					CONCAT(
						emp_nombres,' ',emp_paterno,' ',emp_materno
					) as empleado_nombre,
					cred_cliente as 'cliente_id',
					CONCAT(
						cli_nombres,' ',cli_paterno,' ',cli_materno
					) as 'cliente_nombre',
					cred_monto as 'monto',
					cred_ciclo_pago as 'ciclo_pago',
					cred_estado as 'estado'
				FROM credito 
				INNER JOIN cliente ON cli_codigo = cred_cliente 
				INNER JOIN empleado ON emp_codigo = cred_empleado
				WHERE
					cli_codigo = ? 
				ORDER BY cred_codigo DESC
			";
			$res = $this->db->query($sql,[$idcliente]);
			return $res->result();
		}
		function getLastOrdenBySucursal($idsucursal){
			$sql = "
				SELECT 
					MAX(cred_orden) as value
				FROM credito 
				WHERE cred_sucursal = ?
			";
			$res = $this->db->query($sql, [$idsucursal]);
			return $res->row();
		}

		function insert($data){
			return $this->db->insert('credito', $data);
		}
		function delete($id){
			return $this->db->delete('credito', ['cred_codigo' => $id]); 
		}


		// verifica si existe un credito siguiente al 
		// especificado en el id y segun la sucursal
		function check_next_credits($id, $sede){
			return $this->db->where('cred_codigo >', $id)
				->where('cred_sucursal', $sede)
				->count_all_results('credito');
		}

		// function count_next_credits($id, $sede){
		// 	$sql = "
		// 		SELECT COUNT(*) as cantidad FROM credito
		// 		WHERE 
		// 			cred_codigo > ? AND
		// 			cred_sucursal = ? 
		// 	";
		// 	$res = $this->db->query($sql, [$id, $sede]);
		// 	return $res->row();
		// }

    }
?>