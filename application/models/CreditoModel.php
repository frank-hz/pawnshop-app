<?php

	class CreditoModel extends CI_Model
	{
        protected $id_suc;
        function __construct(){
            $this->id_suc = $this->session->userdata('pres_ses_sucursal');
        }
		// function Lista($tipo, $bus, $lim, $suc){
		// 	$sql = "CALL proc_credito_lista(?,?,?,?)";
		// 	$res = $this->db->query($sql, array($tipo, $bus, $lim, $suc));
		// 	return $res->result();
		// }

		// function Lista_basica($suc, $limite){
		// 	$sql = "
		// 		SELECT
		// 			c.cred_codigo as 'id',
		// 			c.cred_serie as 'codigo',
		// 			DATE_FORMAT(c.cred_creacion, '%d/%m/%Y') as 'creacion',
		// 			c.cred_monto as 'monto',
		// 			CONCAT(cl.cli_nombres,' ',cl.cli_paterno,' ',cl.cli_materno) as 'cliente',
		// 			c.cred_estado as 'estado',
		// 			IF(
		// 				c.cred_estado='E',
		// 				(SELECT COUNT(*) FROM cuota WHERE cuo_estado='V' AND cuo_credito=c.cred_codigo),
		// 				'---'
		// 			) as 'cuotas_vencidas'
		// 		FROM credito c
		// 		INNER JOIN cliente cl ON cl.cli_codigo = c.cred_cliente
		// 		WHERE
		// 			c.cred_sucursal = ?
		// 		ORDER BY
		// 			CASE
		// 				WHEN c.cred_estado = 'E' then 1
		// 				WHEN c.cred_estado = 'CC' then 2
		// 				ELSE 3
		// 			END,
		// 			c.cred_codigo DESC
		// 		LIMIT ?;
		// 	";
		// 	$res = $this->db->query($sql, array($suc, $limite));
		// 	return $res->result();
		// }

		function Lista_por_codigo($buscar, $suc, $limite){
			$sql = "
				SELECT
					c.cred_codigo as 'id',
					c.cred_serie as 'codigo',
					DATE_FORMAT(c.cred_creacion, '%d/%m/%Y') as 'creacion',
					c.cred_monto as 'monto',
					CONCAT(cl.cli_nombres,' ',cl.cli_paterno,' ',cl.cli_materno) as 'cliente',
					c.cred_estado as 'estado',
					IF(
						c.cred_estado='E',
						(SELECT COUNT(*) FROM cuota WHERE cuo_estado='V' AND cuo_credito=c.cred_codigo),
						'---'
					) as 'cuotas_vencidas'
				FROM credito c
				INNER JOIN cliente cl ON cl.cli_codigo = c.cred_cliente
				WHERE
					c.cred_serie LIKE CONCAT('%', ?, '%') AND
					c.cred_sucursal = ?
				ORDER BY
					CASE
						WHEN c.cred_estado = 'E' then 1
						WHEN c.cred_estado = 'CC' then 2
						ELSE 3
					END,
					c.cred_codigo DESC
				LIMIT ?;
			";
			$res = $this->db->query($sql, array($buscar, $suc, $limite));
			return $res->result();
		}

		function Lista_por_cliente($buscar, $suc, $limite){
			$sql = "
				SELECT
					c.cred_codigo as 'id',
					c.cred_serie as 'codigo',
					DATE_FORMAT(c.cred_creacion, '%d/%m/%Y') as 'creacion',
					c.cred_monto as 'monto',
					CONCAT(cl.cli_nombres,' ',cl.cli_paterno,' ',cl.cli_materno) as 'cliente',
					c.cred_estado as 'estado',
					IF(
						c.cred_estado='E',
						(SELECT COUNT(*) FROM cuota WHERE cuo_estado='V' AND cuo_credito=c.cred_codigo),
						'---'
					) as 'cuotas_vencidas'
				FROM credito c
				INNER JOIN cliente cl ON cl.cli_codigo = c.cred_cliente
				WHERE
					(
						cl.cli_paterno LIKE CONCAT(?, '%') OR
						cl.cli_documento LIKE CONCAT(?, '%') OR
						cl.cli_nombres LIKE CONCAT(?, '%')
					) AND c.cred_sucursal = ?
				ORDER BY
					CASE
						WHEN c.cred_estado = 'E' then 1
						WHEN c.cred_estado = 'CC' then 2
						ELSE 3
					END,
					c.cred_codigo DESC
				LIMIT ?;
			";
			$res = $this->db->query($sql, array($buscar, $buscar, $buscar, $suc, $limite));
			return $res->result();
		}

		function Lista_por_estado($buscar, $suc, $limite){
			$sql = "
				SELECT
					c.cred_codigo as 'id',
					c.cred_serie as 'codigo',
					DATE_FORMAT(c.cred_creacion, '%d/%m/%Y') as 'creacion',
					c.cred_monto as 'monto',
					CONCAT(cl.cli_nombres,' ',cl.cli_paterno,' ',cl.cli_materno) as 'cliente',
					c.cred_estado as 'estado',
					IF(
						c.cred_estado='E',
						(SELECT COUNT(*) FROM cuota WHERE cuo_estado='V' AND cuo_credito=c.cred_codigo),
						'---'
					) as 'cuotas_vencidas'
				FROM credito c
				INNER JOIN cliente cl ON cl.cli_codigo = c.cred_cliente
				WHERE
					c.cred_estado = ? AND
					c.cred_sucursal = ?
				ORDER BY
					CASE
						WHEN c.cred_estado = 'E' then 1
						WHEN c.cred_estado = 'CC' then 2
						ELSE 3
					END,
					c.cred_codigo DESC
				LIMIT ?;
			";
			$res = $this->db->query($sql, array($buscar, $suc, $limite));
			return $res->result();
		}

		function Lista_por_atraso($simbolo, $buscar, $suc, $limite){
			$sql = "
				SELECT
					c.cred_codigo as 'id',
					c.cred_serie as 'codigo',
					DATE_FORMAT(c.cred_creacion, '%d/%m/%Y') as 'creacion',
					c.cred_monto as 'monto',
					CONCAT(cl.cli_nombres,' ',cl.cli_paterno,' ',cl.cli_materno) as 'cliente',
					c.cred_estado as 'estado',
					IF(
						c.cred_estado='E',
						(SELECT COUNT(*) FROM cuota WHERE cuo_estado='V' AND cuo_credito=c.cred_codigo),
						'---'
					) as 'cuotas_vencidas'
				FROM credito c
				INNER JOIN cliente cl ON cl.cli_codigo = c.cred_cliente
				WHERE
					(SELECT COUNT(*) FROM cuota WHERE cuo_estado='V' AND cuo_credito=c.cred_codigo)
					$simbolo ?
					AND c.cred_sucursal = ?
					AND c.cred_estado != 'A'
				ORDER BY
					CASE
						WHEN c.cred_estado = 'E' then 1
						WHEN c.cred_estado = 'CC' then 2
						ELSE 3
					END,
					c.cred_codigo DESC
				LIMIT ?;
			";
			$res = $this->db->query($sql, array($buscar, $suc, $limite));
			return $res->result();
		}

		//
		function Insertar(array $data){
			return $this->db->insert('credito', $data);
		}
		//
		function Buscar_detalle($id){
			$sql = "
				SELECT
				c.cred_codigo as 'id',
				c.cred_serie as 'codigo',
				DATE_FORMAT(c.cred_creacion, '%d/%m/%Y') as 'creacion',
				c.cred_creacion as 'fecha',
				DATE_FORMAT(c.cred_liquidacion, '%d/%m/%Y')as 'liquidacion',
				c.cred_empleado as 'empleado_id',
				c.cred_cliente as 'cliente_id',
				CONCAT(e.emp_nombres, ' ', e.emp_paterno, ' ', e.emp_materno) as 'empleado',
				CONCAT(cl.cli_nombres, ' ', cl.cli_paterno, ' ', cl.cli_materno) as 'cliente',
				IF(
					c.cred_prenda > 0,
					(SELECT pren_descripcion FROM prenda WHERE pren_codigo = c.cred_prenda),
					'SIN GARANTIA'
				) as 'prenda',
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
				c.cred_ciclo_pago as ciclo_pago,
				IF(c.cred_estado = 'E',
					(SELECT
						DATE_FORMAT(cuo_vencimiento, '%d/%m/%Y')
					FROM cuota
					WHERE
						cuo_credito = c.cred_codigo
					ORDER BY cuo_orden DESC LIMIT 1),
					'No Definido'
				) as 'cuota_proxima',
				c.cred_estado as 'estado'
			FROM credito c
			INNER JOIN cliente cl ON cl.cli_codigo = c.cred_cliente
			INNER JOIN empleado e ON e.emp_codigo = c.cred_empleado
			WHERE
				c.cred_codigo = ?
			";
			$res = $this->db->query($sql, array($id));
			return $res->row();
		}
		//
		function Buscar_cuotas($id){
			$sql = "CALL proc_credito_cuotas(?)";
			$res = $this->db->query($sql, array($id));
			return $res->result();
		}
		//
		function Buscar_pagos($id){
			$sql = "CALL proc_credito_pagos(?)";
			$res = $this->db->query($sql, array($id));
			return $res->result();
		}
		//
		function Buscar_prorrogas($id){
			$sql = "CALL proc_credito_prorrogas(?)";
			$res = $this->db->query($sql, array($id));
			return $res->result();
		}
		//
		function Buscar_moras($id){
			$sql = "CALL proc_credito_moras(?)";
			$res = $this->db->query($sql, array($id));
			return $res->result();
		}
		//
		function Buscar_libre($data, $cond){
			$this->db->where($cond);
			$this->db->select($data);
			$res = $this->db->get('credito');
			return $res->row();
		}
		//
		function Modificar($id, $data){
			$this->db->where('cred_codigo', $id);
			$this->db->update('credito', $data);
		}
		//
		function Modificar_libre($data, $cond){
			$this->db->where($cond);
			$this->db->update('credito', $data);
		}
		//
		function contrato_detalle($id){
			$sql = "
				SELECT
					c.cred_codigo as 'id',
					c.cred_serie as 'codigo',
					DATE_FORMAT(c.cred_creacion, '%d/%m/%Y') as 'creacion',
					c.cred_creacion as 'fecha',
					c.cred_liquidacion as 'liquidacion',
					CONCAT(e.emp_nombres, ' ', e.emp_paterno, ' ', e.emp_materno) as 'empleado',
					cl.cli_nombres as 'cliente_nombre',
					CONCAT(cl.cli_paterno, ' ', cl.cli_materno) as 'cliente_apellido',
					cl.cli_documento as 'cliente_documento',
					cl.cli_direccion as 'cliente_direccion',
					u.ubi_distrito as 'cliente_ciudad',
					u.ubi_departamento as 'cliente_departamento',
					cl.cli_telefono as 'cliente_telefono',
					IF(
						c.cred_prenda > 0,
						(SELECT pren_descripcion FROM prenda WHERE pren_codigo = c.cred_prenda),
						'SIN GARANTIA'
					) as 'prenda',
					c.cred_monto as 'monto',
					c.cred_prorroga as 'prorroga',
					c.cred_cargo as 'cargo',
					c.cred_tipo_tasa as 'tipo_tasa',
					c.cred_tasa as 'tasa',
					c.cred_tipo_tasa as 'tipo_tasa',
					c.cred_pago_semanal as 'pago',
					c.cred_penalidad as 'penalidad',
					c.cred_amortizacion as 'amortizacion',
					CONCAT(suc_banco, ' ',s.suc_cuenta_tipo) as 'banco_tipo',
					s.suc_cuenta_numero as 'cuenta_numero',
					s.suc_direccion as 'sucursal_direccion',
					s.suc_telefono as 'sucursal_telefono',
					c.cred_estado as 'estado',
					c.cred_ciclo_pago as ciclo_pago,
					IF(c.cred_ciclo_pago = 30, 'Mensual', 'Semanal') as ciclo_pago_string
				FROM credito c
				INNER JOIN cliente cl ON cl.cli_codigo = c.cred_cliente
				INNER JOIN empleado e ON e.emp_codigo = c.cred_empleado
				INNER JOIN ubigeo u ON u.ubi_codigo = cl.cli_ubigeo
				INNER JOIN sucursal s ON s.suc_codigo = c.cred_sucursal
				WHERE
					c.cred_codigo = ?
			";
			$res = $this->db->query($sql, [$id]);
			return $res->row();
		}
		//
		function contrato_prenda($id){
			// $sql = "CALL proc_contrato_prenda(?)";
			$sql = "
				SELECT
					p.pren_codigo as 'id',
					p.pren_descripcion as 'descripcion',
					ct.cat_nombre as 'categoria',
					tp.tpren_nombre as 'tipo',
					p.pren_serial as 'serial',
					p.pren_marca as 'marca',
					p.pren_modelo as 'modelo',
					p.pren_quilate as 'quilate',
					p.pren_peso as 'peso',
					p.pren_placa as 'placa',
					p.pren_anio as 'anio',
					p.pren_kilometraje as 'kilometraje',
					p.pren_observacion as 'observacion',
					p.pren_comentario as 'comentario',
					p.pren_valoracion as 'valoracion',
					p.pren_color as color,
					p.pren_transmision as transmision,
					p.pren_carroceria as carroceria,
					p.pren_estado as 'estado'
				FROM prenda p
				INNER JOIN credito cd ON cd.cred_prenda = p.pren_codigo
				INNER JOIN categoria ct ON ct.cat_codigo = p.pren_categoria
				INNER JOIN tipo_prenda tp ON tp.tpren_codigo = ct.cat_tipo
				WHERE cd.cred_codigo = ?
			";
			$res = $this->db->query($sql, array($id));
			return $res->row();
		}
		//
		function Buscar_capital_pendiente($id){
			$this->db->where('cred_codigo', $id);
			$this->db->select('(cred_monto - cred_amortizacion) as monto');
			$res = $this->db->get('credito');
			return $res->row();
		}
		//
		function Modificar_amortizacion($id, $monto){
			$sql = "UPDATE credito
				SET cred_amortizacion = cred_amortizacion + ".$monto." WHERE cred_codigo = ".$id;
			$this->db->query($sql);
		}

		function Reducir_amortizacion($id, $monto){
			$sql = "UPDATE credito
				SET cred_amortizacion = cred_amortizacion - ".$monto." WHERE cred_codigo = ".$id;
			$this->db->query($sql);
		}
		//
		function Eliminar($id){
			$this->db->delete('credito', array('cred_codigo' => $id));
		}
		//
		function Modificar_adelanto($id, $monto, $tipo){
			try {
				if ($tipo === 'SUMA') {
					$sql = "
					UPDATE credito
					SET cred_adelanto = cred_adelanto + ".$monto."
					WHERE cred_codigo = ".$id;
				}elseif ($tipo === 'RESTA') {
					$sql = "
					UPDATE credito
					SET cred_adelanto = cred_adelanto - ".$monto."
					WHERE cred_codigo = ".$id;
				}
				$this->db->query($sql);
				return true;
			} catch (PDOException $e) {
				return false;
			}
		}

		function ObtenerCliente($id){
			$sql = "SELECT cred_cliente as cliente FROM credito WHERE cred_codigo = ?";
			$res = $this->db->query($sql, $id);
			return $res->row();
		}

		function code_free($script){
			$res = $this->db->query($script);
			return $res;
		}

		// NEW CODE: 13-11-2020
		function SelectByCliente($buscar, $sucursal, $limite){
			$sql = "
				SELECT
					c.cred_codigo as 'id',
					c.cred_serie as 'codigo',
					DATE_FORMAT(c.cred_creacion, '%d/%m/%Y') as 'creacion',
					DATE_FORMAT(c.cred_creacion, '%H:%i:%s') as 'hora',
					c.cred_monto as 'monto',
					CONCAT(cl.cli_nombres,' ',cl.cli_paterno,' ',cl.cli_materno) as 'cliente',
					c.cred_estado as 'estado',
					IF(
						c.cred_estado='E',
						(SELECT COUNT(*) FROM cuota WHERE cuo_estado='V' AND cuo_credito=c.cred_codigo),
						'---'
					) as 'cuotas_vencidas',
					cred_prenda as prenda_id,
					IF (
						c.cred_prenda > 0,
						(SELECT pren_descripcion FROM prenda WHERE pren_codigo = c.cred_prenda),
						'Sin Datos'
					) as prenda_descripcion
				FROM credito c
				INNER JOIN cliente cl ON cl.cli_codigo = c.cred_cliente
				WHERE
					(
						cl.cli_paterno LIKE CONCAT(?, '%') OR
						cl.cli_materno LIKE CONCAT(?, '%') OR
						cl.cli_nombres LIKE CONCAT('%', ?, '%')
					) AND c.cred_sucursal = ?
				ORDER BY
					CASE
						WHEN c.cred_estado = 'E' then 1
						WHEN c.cred_estado = 'CC' then 2
						ELSE 3
					END,
					c.cred_codigo DESC
				LIMIT ?
			";
			$res = $this->db->query($sql, array($buscar, $buscar, $buscar, $sucursal, $limite));
			return $res->result();
		}

		function SelectByEstado($buscar, $suc, $limite){
			$sql = "
				SELECT
					c.cred_codigo as 'id',
					c.cred_serie as 'codigo',
					DATE_FORMAT(c.cred_creacion, '%d/%m/%Y') as 'creacion',
					DATE_FORMAT(c.cred_creacion, '%H:%i:%s') as 'hora',
					c.cred_monto as 'monto',
					CONCAT(cl.cli_nombres,' ',cl.cli_paterno,' ',cl.cli_materno) as 'cliente',
					c.cred_estado as 'estado',
					IF(
						c.cred_estado='E',
						(SELECT COUNT(*) FROM cuota WHERE cuo_estado='V' AND cuo_credito=c.cred_codigo),
						'---'
					) as 'cuotas_vencidas',
					cred_prenda as prenda_id,
					IF (
						c.cred_prenda > 0,
						(SELECT pren_descripcion FROM prenda WHERE pren_codigo = c.cred_prenda),
						'Sin Datos'
					) as prenda_descripcion
				FROM credito c
				INNER JOIN cliente cl ON cl.cli_codigo = c.cred_cliente
				WHERE
					c.cred_estado = ? AND
					c.cred_sucursal = ?
				ORDER BY
					CASE
						WHEN c.cred_estado = 'E' then 1
						WHEN c.cred_estado = 'CC' then 2
						ELSE 3
					END,
					c.cred_codigo DESC
				LIMIT ?;
			";
			$res = $this->db->query($sql, array($buscar, $suc, $limite));
			return $res->result();
		}

		function SelectByCiclo($buscar, $sucursal, $limite){
			$sql = "
				SELECT
					c.cred_codigo as 'id',
					c.cred_serie as 'codigo',
					DATE_FORMAT(c.cred_creacion, '%d/%m/%Y') as 'creacion',
					DATE_FORMAT(c.cred_creacion, '%H:%i:%s') as 'hora',
					c.cred_monto as 'monto',
					CONCAT(cl.cli_nombres,' ',cl.cli_paterno,' ',cl.cli_materno) as 'cliente',
					c.cred_estado as 'estado',
					IF(
						c.cred_estado='E',
						(SELECT COUNT(*) FROM cuota WHERE cuo_estado='V' AND cuo_credito=c.cred_codigo),
						'---'
					) as 'cuotas_vencidas',
					cred_prenda as prenda_id,
					IF (
						c.cred_prenda > 0,
						(SELECT pren_descripcion FROM prenda WHERE pren_codigo = c.cred_prenda),
						'Sin Datos'
					) as prenda_descripcion
				FROM credito c
				INNER JOIN cliente cl ON cl.cli_codigo = c.cred_cliente
				WHERE
					c.cred_ciclo_pago = ? AND
					c.cred_sucursal = ?
				ORDER BY
					CASE
						WHEN c.cred_estado = 'E' then 1
						WHEN c.cred_estado = 'CC' then 2
						ELSE 3
					END,
					c.cred_codigo DESC
				LIMIT ?
			";
			$res = $this->db->query($sql, array($buscar, $sucursal, $limite));
			return $res->result();
		}

		function SelectByAtraso($simbolo, $valor, $suc, $limite){
			$sql = "
				SELECT
					c.cred_codigo as 'id',
					c.cred_serie as 'codigo',
					DATE_FORMAT(c.cred_creacion, '%d/%m/%Y') as 'creacion',
					DATE_FORMAT(c.cred_creacion, '%H:%i:%s') as 'hora',
					c.cred_monto as 'monto',
					CONCAT(cl.cli_nombres,' ',cl.cli_paterno,' ',cl.cli_materno) as 'cliente',
					c.cred_estado as 'estado',
					IF(
						c.cred_estado='E',
						(SELECT COUNT(*) FROM cuota WHERE cuo_estado='V' AND cuo_credito=c.cred_codigo),
						'0'
					) as 'cuotas_vencidas',
					cred_prenda as prenda_id,
					IF (
						c.cred_prenda > 0,
						(SELECT pren_descripcion FROM prenda WHERE pren_codigo = c.cred_prenda),
						'Sin Datos'
					) as prenda_descripcion
				FROM credito c
				INNER JOIN cliente cl ON cl.cli_codigo = c.cred_cliente
				WHERE
					(SELECT COUNT(*) FROM cuota WHERE cuo_estado='V' AND cuo_credito=c.cred_codigo)
					$simbolo ?
					AND c.cred_sucursal = ?
					AND c.cred_estado != 'A'
				ORDER BY
					CASE
						WHEN c.cred_estado = 'E' then 1
						WHEN c.cred_estado = 'CC' then 2
						ELSE 3
					END,
					c.cred_codigo DESC
				LIMIT ?;
			";
			// die($sql);
			// print_r($res);
			$res = $this->db->query($sql, array($valor, $suc, $limite));
			return $res->result();
		}

		function SelectByCodigo($buscar, $suc, $limite){
			$sql = "
				SELECT
					c.cred_codigo as 'id',
					c.cred_serie as 'codigo',
					DATE_FORMAT(c.cred_creacion, '%d/%m/%Y') as 'creacion',
					DATE_FORMAT(c.cred_creacion, '%H:%i:%s') as 'hora',
					c.cred_monto as 'monto',
					CONCAT(cl.cli_nombres,' ',cl.cli_paterno,' ',cl.cli_materno) as 'cliente',
					c.cred_estado as 'estado',
					IF(
						c.cred_estado='E',
						(SELECT COUNT(*) FROM cuota WHERE cuo_estado='V' AND cuo_credito=c.cred_codigo),
						'---'
					) as 'cuotas_vencidas',
					cred_prenda as prenda_id,
					IF (
						c.cred_prenda > 0,
						(SELECT pren_descripcion FROM prenda WHERE pren_codigo = c.cred_prenda),
						'Sin Datos'
					) as prenda_descripcion
				FROM credito c
				INNER JOIN cliente cl ON cl.cli_codigo = c.cred_cliente
				WHERE
					c.cred_serie LIKE CONCAT('%', ?, '%') AND
					c.cred_sucursal = ?
				ORDER BY
					CASE
						WHEN c.cred_estado = 'E' then 1
						WHEN c.cred_estado = 'CC' then 2
						ELSE 3
					END,
					c.cred_codigo DESC
				LIMIT ?;
			";
			$res = $this->db->query($sql, array($buscar, $suc, $limite));
			return $res->result();
		}

		function SelectAll($suc, $limite){
			$sql = "
				SELECT
					c.cred_codigo as 'id',
					c.cred_serie as 'codigo',
					DATE_FORMAT(c.cred_creacion, '%d/%m/%Y') as 'creacion',
					DATE_FORMAT(c.cred_creacion, '%H:%i:%s') as 'hora',
					c.cred_monto as 'monto',
					CONCAT(cl.cli_nombres,' ',cl.cli_paterno,' ',cl.cli_materno) as 'cliente',
					c.cred_estado as 'estado',
					IF(
						c.cred_estado='E',
						(SELECT COUNT(*) FROM cuota WHERE cuo_estado='V' AND cuo_credito=c.cred_codigo),
						'---'
					) as 'cuotas_vencidas',
					cred_prenda as prenda_id,
					IF (
						c.cred_prenda > 0,
						(SELECT pren_descripcion FROM prenda WHERE pren_codigo = c.cred_prenda),
						'Sin Datos'
					) as prenda_descripcion
				FROM credito c
				INNER JOIN cliente cl ON cl.cli_codigo = c.cred_cliente
				WHERE
					c.cred_sucursal = ?
				ORDER BY
					CASE
						WHEN c.cred_estado = 'E' then 1
						WHEN c.cred_estado = 'CC' then 2
						ELSE 3
					END,
					c.cred_codigo DESC
				LIMIT ?;
			";
			$res = $this->db->query($sql, array($suc, $limite));
			return $res->result();
		}

        // [10-10-2022]
		function get_data_all($pars_in){
			$sql = "
				SELECT
					cred_codigo as 'id',
					cred_serie as 'serie',
					DATE_FORMAT(cred_creacion, '%d/%m/%Y') as 'creacion',
					DATE_FORMAT(cred_creacion, '%H:%i:%s') as 'hora',
					cred_monto as 'monto',
					cli_codigo as cliente_id,
					CONCAT(cli_nombres,' ',cli_paterno,' ',cli_materno) as 'cliente_nombre',
					cred_estado as 'estado',
					IF(
						cred_estado='E',
						(SELECT COUNT(*) FROM cuota WHERE (cuo_estado='V' OR cuo_estado='E') AND cuo_credito=cred_codigo and cuo_vencimiento <= now()),
						''
					) as 'cuotas_vencidas',
					(
						SELECT COUNT(*) FROM cuota WHERE (cuo_estado='V' OR cuo_estado='E') AND cuo_credito=cred_codigo and cuo_vencimiento <= now()
					) as 'cred_atraso',
					cred_prenda as prenda_id,
					IF (
						cred_prenda > 0,
						(SELECT pren_descripcion FROM prenda WHERE pren_codigo = cred_prenda),
						'Sin Datos'
					) as prenda_descripcion,
					cred_sucursal as sucursal,
					(
						SELECT COUNT(*) FROM cuota WHERE cuo_vencimiento = CURDATE() and cuo_credito = cred_codigo
					) as 'cuota_cumplida',
					cred_liquidacion as liquidacion
				FROM credito
				INNER JOIN cliente ON cli_codigo = cred_cliente
				WHERE
					cred_sucursal = ?
			";
			$pars_db = [$this->id_suc];
			if (!empty($pars_in->buscar)) {
				switch ($pars_in->tipo_busqueda) {
					case 'CODIGO':
						$sql .= " AND cred_serie LIKE CONCAT('%', ?, '%')";
						array_push($pars_db, strtoupper($pars_in->buscar));
						break;

					case 'CLIENTE':
						$sql .= " AND (
								cli_paterno LIKE CONCAT(?, '%') OR
								cli_materno LIKE CONCAT(?, '%') OR
								cli_nombres LIKE CONCAT('%', ?, '%')
							)";
						array_push(
							$pars_db,
							$pars_in->buscar,
							$pars_in->buscar,
							$pars_in->buscar
						);
						break;
				}
			}

			if (!empty($pars_in->estado) && in_array($pars_in->estado,['E','R','CC', 'A'])) {
				$sql .= " AND cred_estado = ?";
				array_push($pars_db, $pars_in->estado);
			}

			$simbol = (in_array($pars_in->atraso->simbolo, ['>','>=','=','<=','<'])) ? $pars_in->atraso->simbolo : 'NO_DATA';
			if (!empty($pars_in->atraso->valor) && $simbol != 'NO_DATA') {
				$sql .= "
					AND
						(SELECT COUNT(*) FROM cuota WHERE cuo_estado='V' AND cuo_credito=cred_codigo) $simbol ?
				";
				array_push($pars_db, intval($pars_in->atraso->valor));
			}

			$more_html = "";
			if (!empty($pars_in->fechas->inicio) && !empty($pars_in->fechas->final)) {
				$sql .= " AND cred_creacion BETWEEN ? AND ?";
				array_push($pars_db, $pars_in->fechas->inicio);
				array_push($pars_db, $pars_in->fechas->final);
			}

			$order_str = "";
			if (in_array($pars_in->orden->tipo, ['ASC', 'DESC']) && in_array($pars_in->orden->por, ['CODIGO', 'CREACION', 'MONTO', 'ATRASO'])) {
				$por = 'cred_' . strtolower($pars_in->orden->por);
				$tipo = $pars_in->orden->tipo;
				$order_str = " ORDER BY $por $tipo";
			}
			$limite = (!empty($pars_in->limite)) ? ' limit '.$pars_in->limite : '';
			$sql .= $order_str;

			$res = $this->db->query($sql, $pars_db);
			return [
				'data' => $res->result(),
				'string' => $sql,
				'params' => $pars_db
			];
		}

		// encode 04-2023
		function get_data($id){
			$sql = "
				SELECT
					c.cred_codigo as 'id',
					c.cred_serie as 'codigo',
					DATE_FORMAT(c.cred_creacion, '%d/%m/%Y') as 'creacion',
					c.cred_creacion as 'fecha',
					DATE_FORMAT(c.cred_liquidacion, '%d/%m/%Y')as 'liquidacion',
					c.cred_empleado as 'empleado_id',
					c.cred_cliente as 'cliente_id',
					CONCAT(e.emp_nombres, ' ', e.emp_paterno, ' ', e.emp_materno) as 'empleado',
					CONCAT(cl.cli_nombres, ' ', cl.cli_paterno, ' ', cl.cli_materno) as 'cliente',
					IF(
						c.cred_prenda > 0,
						(SELECT pren_descripcion FROM prenda WHERE pren_codigo = c.cred_prenda),
						'SIN GARANTIA'
					) as 'prenda',
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
					c.cred_ciclo_pago as ciclo_pago,
					IF(c.cred_estado = 'E',
						(SELECT
							DATE_FORMAT(cuo_vencimiento, '%d/%m/%Y')
						FROM cuota
						WHERE
							cuo_credito = c.cred_codigo
						ORDER BY cuo_orden DESC LIMIT 1),
						'No Definido'
					) as 'cuota_proxima',
					c.cred_estado as 'estado',
					cond_bloqueo_atencion as cliente_bloqueo,
                    cond_bloqueo_motivo as cliente_bloqueo_motivo,
                    cond_puntuacion as cliente_puntuacion
				FROM credito c
				INNER JOIN cliente cl ON cl.cli_codigo = c.cred_cliente
				INNER JOIN empleado e ON e.emp_codigo = c.cred_empleado
				LEFT JOIN conducta on cond_cliente = cli_codigo
				WHERE
					c.cred_codigo = ?
			";
			$res = $this->db->query($sql, array($id));
			return $res->row();
		}


	}
?>
