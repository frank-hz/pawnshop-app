<?php  
	
	class ClienteModel extends CI_Model
	{

		// Lista de registros
		// function Lista($tipo, $buscar, $limite){
		// 	$sql = "CALL proc_cliente_lista(?,?,?)";
		// 	$res = $this->db->query($sql, array($tipo, $buscar, $limite));
		// 	return $res->result();
		// }
		function Buscar_detalle($id){
			$sql = "CALL proc_cliente_detalle(?)";
			$res = $this->db->query($sql, array($id));
			mysqli_next_result( $this->db->conn_id );
			return $res->row();
		}
		// Consulta de prendas asociadas
		function Buscar_prendas($id, $tipo){
			$sql = "call proc_cliente_prendas(?,?)";
			$res = $this->db->query($sql, array($id, $tipo));
			return $res->result();
		}
		// Consulta del historial de creditos
		function Buscar_creditos($id, $orden, $limite = 0){
			$this->db->where('cred_cliente', $id);
			$this->db->select('cred_codigo as id');
			$this->db->select('cred_serie as serie');
			$this->db->select('DATE_FORMAT(cred_creacion, "%d/%m/%Y") as creacion');
			$this->db->select('cred_monto as monto');
			$this->db->select('cred_estado as estado');
			$this->db->order_by('cred_codigo', $orden);
			if ($limite > 0) $this->db->limit($limite);
			$res = $this->db->get('credito');
			return $res->result();
		}
		// insertar registro
		function Insertar(array $data){
			return $this->db->insert('cliente', $data);
		}
		// actualizacion de datos
		function Modificar($id, $data){
			$this->db->where('cli_codigo', $id);
			$this->db->update('cliente', $data);
			// $aff = $this->db->affected_rows();
			// return $aff;
		}		
		// Eliminando registros
		function delete(){
			$this->db->where('cli_codigo', $id);
			$this->db->delete('cliente');
		}


		// obtener registros segun el documento 
		function Buscar_dni_v1($doc, $tipo){
			$sql = "SELECT cli_codigo as 'id' FROM cliente WHERE cli_documento = ? AND cli_tipo_documento = ?";
			$ins = $this->db->query($sql, array($doc, $tipo));
			return $ins->result();
		}

		function Buscar_dni_v2($doc, $tipo, $id){
			$sql = "SELECT cli_codigo as 'id' FROM cliente WHERE cli_documento = ? AND cli_tipo_documento = ? AND cli_codigo != ?";
			$ins = $this->db->query($sql, array($doc, $tipo, $id));
			return $ins->result();
		}
		
		function buscar_cliente($tipo, $valor, $limite){
			switch ($tipo) {
				case 'nombre':
					$sql = "
						SELECT
							c.cli_codigo as 'id', 
							c.cli_documento as 'documento',
							c.cli_tipo_documento as 'tipo_documento', 
							c.cli_nombres as 'nombres',
							c.cli_paterno as 'paterno',
							c.cli_materno as 'materno',
							u.ubi_distrito as 'ciudad',
							c.cli_direccion as 'direccion',
							c.cli_telefono as 'telefono',
							(
								select count(cr.cred_codigo) from credito cr 
								where cr.cred_estado = 'E' and cr.cred_cliente = c.cli_codigo
							) as creditos_activos
						FROM cliente c
						INNER JOIN ubigeo u ON u.ubi_codigo = c.cli_ubigeo 
						WHERE 
							c.cli_nombres LIKE CONCAT(?, '%') OR 
							c.cli_paterno LIKE CONCAT(?, '%') OR 
							c.cli_materno LIKE CONCAT(?, '%') OR 
							SUBSTRING_INDEX(c.cli_nombres, ' ', -1) LIKE CONCAT(?, '%') 

						ORDER BY cli_nombres ASC LIMIT ?
					";
					$res = $this->db->query($sql, array($valor,$valor,$valor,$valor, $limite));
					return $res->result();
					break;

				case 'documento':
					$sql = "
						SELECT
							c.cli_codigo as 'id', 
							c.cli_documento as 'documento',
							c.cli_tipo_documento as 'tipo_documento', 
							c.cli_nombres as 'nombres',
							c.cli_paterno as 'paterno',
							c.cli_materno as 'materno',
							u.ubi_distrito as 'ciudad',
							c.cli_direccion as 'direccion',
							c.cli_telefono as 'telefono',
							c.cli_imagen as 'imagen'
						FROM cliente c
						INNER JOIN ubigeo u ON u.ubi_codigo = c.cli_ubigeo 
						WHERE c.cli_documento LIKE CONCAT(?, '%')
						ORDER BY cli_nombres ASC LIMIT ?
					";
					$res = $this->db->query($sql, array($valor, $limite));
					return $res->result();
					break;
				
				default:
					$sql = "
						SELECT
							c.cli_codigo as 'id', 
							c.cli_documento as 'documento',
							c.cli_tipo_documento as 'tipo_documento', 
							c.cli_nombres as 'nombres',
							c.cli_paterno as 'paterno',
							c.cli_materno as 'materno',
							u.ubi_distrito as 'ciudad',
							c.cli_direccion as 'direccion',
							c.cli_telefono as 'telefono',
							c.cli_imagen as 'imagen'
						FROM cliente c
						INNER JOIN ubigeo u ON u.ubi_codigo = c.cli_ubigeo 
						ORDER BY cli_nombres ASC LIMIT ?
					";
					$res = $this->db->query($sql, array($limite));
					return $res->result();
					break;
			}
		}

		// NEWCODE: 18-11-2020
		function SelectAll($limite = 10){
			$sql = "
				SELECT
					c.cli_codigo as 'id', 
					c.cli_documento as 'documento',
					c.cli_tipo_documento as 'tipo_documento', 
					c.cli_nombres as 'nombres',
					c.cli_paterno as 'paterno',
					c.cli_materno as 'materno',
					u.ubi_distrito as 'ciudad',
					u.ubi_distrito as 'distrito',
					c.cli_direccion as 'direccion',
					c.cli_telefono as 'telefono',
					c.cli_imagen as 'imagen'
				FROM cliente c
				INNER JOIN ubigeo u ON u.ubi_codigo = c.cli_ubigeo 
				ORDER BY cli_nombres ASC LIMIT ?
			";
			$res = $this->db->query($sql, array($limite));
			return $res->result();
		}

		function SelectByNombreApellido($buscar, $limite = 10){
			$sql = "
				SELECT
					c.cli_codigo as 'id', 
					c.cli_documento as 'documento',
					c.cli_tipo_documento as 'tipo_documento', 
					c.cli_nombres as 'nombres',
					c.cli_paterno as 'paterno',
					c.cli_materno as 'materno',
					u.ubi_distrito as 'ciudad',
					u.ubi_distrito as 'distrito',
					u.ubi_departamento as 'departamento',
					c.cli_direccion as 'direccion',
					c.cli_telefono as 'telefono',
					c.cli_imagen as 'imagen'
				FROM cliente c
				INNER JOIN ubigeo u ON u.ubi_codigo = c.cli_ubigeo 
				WHERE 
					c.cli_paterno LIKE CONCAT(?, '%') OR
					c.cli_materno LIKE CONCAT(?, '%') OR 
					c.cli_nombres LIKE CONCAT('%', ?, '%')
				ORDER BY cli_nombres ASC LIMIT ?
			";
			$res = $this->db->query($sql, array($buscar, $buscar, $buscar, $limite));
			return $res->result();
		}

		function SelectByDocumento($buscar, $limite = 10){
			$sql = "
				SELECT
					c.cli_codigo as 'id', 
					c.cli_documento as 'documento',
					c.cli_tipo_documento as 'tipo_documento', 
					c.cli_nombres as 'nombres',
					c.cli_paterno as 'paterno',
					c.cli_materno as 'materno',
					u.ubi_distrito as 'ciudad',
					u.ubi_distrito as 'distrito',
					u.ubi_departamento as 'departamento',
					c.cli_direccion as 'direccion',
					c.cli_telefono as 'telefono',
					c.cli_imagen as 'imagen'
				FROM cliente c
				INNER JOIN ubigeo u ON u.ubi_codigo = c.cli_ubigeo 
				WHERE 
					c.cli_documento LIKE CONCAT(?, '%')
				ORDER BY cli_nombres ASC LIMIT ?
			";
			$res = $this->db->query($sql, array($buscar, $limite));
			return $res->result();
		}

		function SelectByDistrito($buscar, $limite = 10){
			$sql = "
				SELECT
					c.cli_codigo as 'id', 
					c.cli_documento as 'documento',
					c.cli_tipo_documento as 'tipo_documento', 
					c.cli_nombres as 'nombres',
					c.cli_paterno as 'paterno',
					c.cli_materno as 'materno',
					u.ubi_distrito as 'ciudad',
					u.ubi_distrito as 'distrito',
					u.ubi_departamento as 'departamento',
					c.cli_direccion as 'direccion',
					c.cli_telefono as 'telefono',
					c.cli_imagen as 'imagen'
				FROM cliente c
				INNER JOIN ubigeo u ON u.ubi_codigo = c.cli_ubigeo 
				WHERE 
					u.ubi_distrito LIKE CONCAT('%', ?, '%')
				ORDER BY cli_nombres ASC LIMIT ?
			";
			$res = $this->db->query($sql, array($buscar, $limite));
			return $res->result();
		}

		function GetDetalle($id){
			$sql = "
				SELECT
					c.cli_codigo as 'id', 
					c.cli_documento as 'documento',
					c.cli_tipo_documento as 'tipo_documento', 
					c.cli_nombres as 'nombres',
					c.cli_paterno as 'paterno',
					c.cli_materno as 'materno',
					u.ubi_distrito as 'ciudad',
					u.ubi_distrito as 'distrito',
					c.cli_direccion as 'direccion',
					c.cli_telefono as 'telefono',
					c.cli_imagen as 'imagen'				
				FROM cliente c
				INNER JOIN ubigeo u ON u.ubi_codigo = c.cli_ubigeo
				WHERE c.cli_codigo = ?
			";
			$res = $this->db->query($sql, array($id));
			return $res->row();
		}

		function GetAlerta($id){
			$this->db->where('acli_cliente', $id);
			$this->db->where('acli_estado', 'P');
			$this->db->select('acli_contenido as contenido');
			$this->db->select('DATE_FORMAT(acli_fecha, "%d/%m/%Y") as fecha');
			$this->db->select('acli_estado as estado');
			$this->db->order_by('acli_codigo', 'DESC');
			// $this->db->limit($limite);
			$res = $this->db->get('alerta_cliente');
			return $res->result();
		}

		// NEWCODE: 15-01-2023
		function fast_get_data($id) {
			$sql = "
				SELECT
					c.cli_codigo as 'id', 
					c.cli_documento as 'documento',
					c.cli_tipo_documento as 'tipo_documento', 
					c.cli_nombres as 'nombres',
					c.cli_paterno as 'paterno',
					c.cli_materno as 'materno',
					concat(
						cli_nombres,' ',
						cli_paterno,' ',
						cli_materno
					) as nombre,
					u.ubi_distrito as 'ciudad',
					u.ubi_distrito as 'distrito',
					c.cli_direccion as 'direccion',
					c.cli_telefono as 'telefono'			
				FROM cliente c
				INNER JOIN ubigeo u ON u.ubi_codigo = c.cli_ubigeo
				WHERE c.cli_codigo = ?
			";
			$res = $this->db->query($sql, array($id));
			return $res->result();
		}
		function get_data($id){
			$sql = "
				SELECT
					cli_codigo as 'id', 
					cli_documento as 'documento',
					cli_tipo_documento as 'tipo_documento', 
					cli_nombres as 'nombres',
					cli_paterno as 'paterno',
					cli_materno as 'materno',
					concat(
						cli_nombres,' ',
						cli_paterno,' ',
						cli_materno
					) as nombre_completo,
					ubi_distrito as 'ciudad',
					ubi_distrito as 'distrito',
					ubi_departamento as 'departamento',
					ubi_codigo as 'ubigeo',
					cli_direccion as 'direccion',
					cli_telefono as 'telefono',
					cli_email as 'email',
                    cond_bloqueo_atencion as bloqueo,
                    cond_bloqueo_motivo as bloqueo_motivo,
                    cond_puntuacion as puntuacion			
				FROM cliente 
				INNER JOIN ubigeo ON ubi_codigo = cli_ubigeo 
				LEFT JOIN conducta on cond_cliente = cli_codigo 
				WHERE cli_codigo = ?
			";
			$res = $this->db->query($sql, array($id));
			return $res->row();
		}
		function get_data_all_search($val){
			$sql = "
				SELECT
					cli_codigo as 'id', 
					cli_documento as 'documento',
					cli_tipo_documento as 'tipo_documento', 
					cli_nombres as 'nombres',
					cli_paterno as 'paterno',
					cli_materno as 'materno',
					concat(
						cli_nombres,' ',
						cli_paterno,' ',
						cli_materno
					) as nombre_completo,
					ubi_distrito as 'ciudad',
					ubi_distrito as 'distrito',
					ubi_departamento as 'departamento',
					ubi_codigo as 'ubigeo',
					cli_direccion as 'direccion',
					cli_telefono as 'telefono',
					cli_email as 'email',
                    cond_bloqueo_atencion as bloqueo,
                    cond_bloqueo_motivo as bloqueo_motivo,
                    cond_puntuacion as puntuacion			
				FROM cliente 
				INNER JOIN ubigeo ON ubi_codigo = cli_ubigeo 
				LEFT JOIN conducta on cond_cliente = cli_codigo 
				WHERE 
					cli_nombres LIKE CONCAT(?,'%') OR 
					cli_paterno LIKE CONCAT(?,'%') OR 
					cli_materno LIKE CONCAT(?,'%') OR 
					cli_documento LIKE CONCAT(?,'%') 
				ORDER BY cli_codigo DESC LIMIT 4
			";
			$res = $this->db->query($sql, [$val,$val,$val,$val]);
			return $res->result();
		}
		function update_data($id, $data){
			$this->db->where('cli_codigo', $id);
			$this->db->update('cliente', $data);
		}

	}
?>	