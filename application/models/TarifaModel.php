<?php  
	
	class TarifaModel extends CI_Model
	{
		
		// Rework 2026
		function get_by_id($id){
			$this->db->select("
				tar_codigo as id,
				tar_tipo as tipo, 
				tar_interes as interes, 
				tar_mora as mora,
				tar_sucursal as sucursal_id
			");
			$this->db->from("tarifa");
			$this->db->where("tar_codigo", $id);
			$res = $this->db->get();
			return $res->row();
		}
		function search_by_monto($sede, $monto){
			$this->db->select("
				tar_codigo as id,
				tar_tipo as tipo, 
				tar_interes as interes, 
				tar_mora as mora,
				tar_sucursal as sucursal_id
			");
			$this->db->from("tarifa");
			$this->db->where("tar_sucursal", $sede);
			$this->db->where("tar_minimo <=", $monto);
			$this->db->where("tar_maximo >=", $monto);
			$res = $this->db->get();
			return $res->row();
		}
		function insert(array $data){
			$this->db->insert('tarifa', $data);
			return ($this->db->affected_rows() > 0);
		}
		function update($id, $data){
			$this->db->where('tar_codigo', $id);
			return $this->db->update('tarifa', $data);
		}
		function delete(int $id){
			$this->db->where('tar_codigo', $id);
			$this->db->delete('tarifa');
			return ($this->db->affected_rows() > 0);
		}

		function Buscar_tarifa_aplicable($monto, $sucursal){
			$sql = "SELECT 
				tar_codigo as 'id',
				tar_tipo as 'tipo', 
				tar_interes as 'interes', 
				tar_mora as 'mora' 
				FROM tarifa WHERE ? >= tar_minimo AND ? <= tar_maximo AND tar_sucursal = ? 
				LIMIT 1";
			$res = $this->db->query($sql, array($monto, $monto, $sucursal));
			return $res->row();
		}

		// 
		function get_tarifa($monto, $sucursal){
			$sql = "SELECT 
				tar_codigo as 'id',
				tar_tipo as 'tipo', 
				tar_interes as 'interes', 
				tar_mora as 'mora' 
				FROM tarifa WHERE ? >= tar_minimo AND ? <= tar_maximo AND tar_sucursal = ? 
				LIMIT 1";
			$res = $this->db->query($sql, array($monto, $monto, $sucursal));
			return $res->row();
		}

		function consulta($tipo = 'rest', $p1 = '', $p2 = ''){
			switch ($tipo) {
				case 'lista':
					$sql = "CALL proc_tarifa_lista()";
					$res = $this->db->query($sql);
					return $res->result();
					break;
				case 'credito_relacion':
					mysqli_next_result( $this->db->conn_id );
					$sql = "CALL proc_tarifa_credito_relacion(?)";
					$res = $this->db->query($sql, array($p1));
					return $res->result();
					break;
				case 'detalle':
					$sql = "SELECT * FROM tarifa WHERE tar_codigo = ?";
					$res = $this->db->query($sql, array($p1));
					return $res->row();
					break;
				case 'calculo':
					$sql = "SELECT 
						tar_codigo as 'id',
						tar_tipo as 'tipo', 
						tar_interes as 'interes', 
						tar_mora as 'mora' 
						FROM tarifa WHERE ? >= tar_minimo AND ? <= tar_maximo LIMIT 1";
					$res = $this->db->query($sql, array($p1, $p1));
					return $res->row();
					break;
			}
		}

		function actualizacion($cod, $tip, $int, $mor){
			$sql = "CALL proc_tarifa_actualizar(?,?,?,?)";
			$res = $this->db->query($sql, array($cod, $tip, $int, $mor));
			return $res->row();
		}

		function recalibrarCreditos($data){
			mysqli_next_result( $this->db->conn_id );
			$sql = "CALL proc_credito_recalibrar(?,?,?,?,?,?,?)";
			$res = $this->db->query($sql, $data);
			return $res->row();
		}

		// NEW CODE
		function Lista($suc){
			$this->db->where('tar_sucursal', $suc);
			$this->db->select('tar_codigo as id');
			$this->db->select('tar_minimo as minimo');
			$this->db->select('tar_maximo as maximo');
			$this->db->select('tar_tipo as tipo');
			$this->db->select('tar_interes as interes');
			$this->db->select('tar_mora as mora');
			$res = $this->db->get('tarifa');
			return $res->result();
		}

		function Buscar($id){
			$this->db->where('tar_codigo', $id);
			$this->db->select('tar_codigo as id');
			$this->db->select('tar_minimo as minimo');
			$this->db->select('tar_maximo as maximo');
			$this->db->select('tar_tipo as tipo');
			$this->db->select('tar_interes as interes');
			$this->db->select('tar_mora as mora');
			$res = $this->db->get('tarifa');
			return $res->row();
		}

		function Buscar_libre($data, $cond, $mode = 'fila', $break_free = 'NO'){
			$this->db->where($cond);
			$this->db->select($data);
			if ($break_free === 'SI')  mysqli_next_result( $this->db->conn_id ); 
			$res = $this->db->get('tarifa');
			return ($mode === 'lista') ? $res->result() : $res->row();
		}

		function Insertar($data){
			return $this->db->insert('tarifa', $data);
		}

		function Eliminar($id){
			return $this->db->delete('tarifa', array('tar_codigo' => $id));
		}

		function Modificar($id, $data){
			$this->db->where('tar_codigo', $id);
			$this->db->update('tarifa', $data);
		}	
		


		// C U S T O M 
		function getAllBySede($sucursal){
			$sql = "
				SELECT 
					tar_codigo as id,
					tar_tipo as tipo, 
					tar_interes as interes, 
					tar_mora as mora,
					tar_minimo as minimo,
					tar_maximo as maximo 
				FROM tarifa 
				WHERE tar_sucursal = ?";
			$res = $this->db->query($sql, array($sucursal));
			return $res->result();
		}

	}
?>