<?php  

	class PrendaModel extends CI_Model {

		// REWORK 2026
		function insert(array $data){
			$this->db->insert('prenda', $data);
			return ($this->db->affected_rows() > 0);
		}
		function update($id, $data){
			$this->db->where('pren_codigo', $id);
			return $this->db->update('prenda', $data);
		}
		function delete(int $id){
			$this->db->where('pren_codigo', $id);
			$this->db->delete('prenda');
			return ($this->db->affected_rows() > 0);
		}





		function Lista($tipo, $buscar, $limite = 10){
			switch ($_POST['tipo']) {
				case 'cliente':
					$this->db->where("cli_nombres LIKE CONCAT('$buscar','%')");
					break;
				case 'tipo':
					$this->db->where(array('cat_tipo' => $buscar));
					break;
				case 'categoria':
					$this->db->where(array('pren_categoria' => $buscar));
					break;
			}
			$this->db->select('pren_codigo as id');
			$this->db->select('CONCAT(cli_nombres," ",cli_paterno," ", cli_materno) as cliente');
			$this->db->select('pren_descripcion as descripcion');
			$this->db->select('pren_estado as estado');
			$this->db->select('tpren_nombre as tipo');
			$this->db->select('cat_nombre as categoria');
			$this->db->join('categoria', 'cat_codigo = pren_categoria');
			$this->db->join('tipo_prenda', 'tpren_codigo = cat_tipo');
			$this->db->join('cliente', 'cli_codigo = pren_cliente');
			$this->db->from('prenda');
			$this->db->limit($limite);
			$this->db->order_by('cli_nombres', 'ASC');
			$res = $this->db->get();
			return $res->result();	
		}

		// insertar registro
		function Insertar(array $data){
			return $this->db->insert('prenda', $data);
		}

		// historial de propiedad de la prenda
		function Lista_propiedad($id){
			$sql = "
				SELECT
					cli_codigo as cliente_id,
					CONCAT(cl.cli_nombres, ' ', cl.cli_paterno, ' ', cl.cli_materno) as 'cliente',
					CONCAT(e.emp_nombres, ' ', e.emp_paterno, ' ', e.emp_materno) as 'empleado',
					pr.prop_inicio as 'inicio',
					IF(pr.prop_estado = 'A', 'En posesion', pr.prop_termino) as 'termino',
					pr.prop_motivo as 'motivo',
					pr.prop_estado as 'estado'
				FROM propiedad pr 
				INNER JOIN cliente cl ON cl.cli_codigo = pr.prop_cliente 
				INNER JOIN empleado e ON e.emp_codigo = pr.prop_empleado
				WHERE pr.prop_prenda = ?
			";
			$res = $this->db->query($sql, array($id));
			return $res->result();
		}  

		// Modificar los datos de la prenda
		function Modificar($id, $data){
			$this->db->where('pren_codigo', $id);
			return $this->db->update('prenda', $data);
		}

		// 
		function Eliminar($id){
			$this->db->delete('prenda', array('pren_codigo' => $id)); 
		}

		// 
		function Buscar_detalle($id, $task = ''){
			// $sql = "CALL proc_prenda_detalle(?)";
			$sql = "
				SELECT 
					p.pren_codigo as 'id',
					p.pren_descripcion as 'descripcion',
					p.pren_id_almacen as 'almacen_id',
					p.pren_cliente as 'cliente_id',
					CONCAT(c.cli_nombres,' ', c.cli_paterno, ' ',c.cli_materno) as 'cliente',
					ct.cat_nombre as 'categoria',
					tp.tpren_nombre as 'tipo',
					p.pren_categoria as 'categoria_id',
					p.pren_serial as 'serial',
					p.pren_marca as 'marca',
					p.pren_modelo as 'modelo',
					p.pren_quilate as 'quilate',
					p.pren_peso as 'peso',
					p.pren_observacion as 'observacion',
					p.pren_comentario as 'comentario',
					p.pren_valoracion as 'valoracion',
					p.pren_reutilizacion as 'reutilizacion',
					p.pren_fecha_registro as 'fecha_registro',
					p.pren_estado as 'estado'	
				FROM prenda p
				INNER JOIN cliente c ON c.cli_codigo = p.pren_cliente
				INNER JOIN categoria ct ON ct.cat_codigo = p.pren_categoria
				INNER JOIN tipo_prenda tp ON tp.tpren_codigo = ct.cat_tipo
				WHERE p.pren_codigo = ?
			";
			$res = $this->db->query($sql, array($id));
			return $res->row();
		}

		// 
		function Buscar_creditos($id, $task = ''){
			// $sql = "CALL proc_prenda_detalle_creditos(?)";
			$sql = "
				SELECT 
					c.cred_codigo as 'id',
					DATE_FORMAT(c.cred_creacion, '%d/%m/%Y') as 'creacion',
					c.cred_monto as 'monto',
					c.cred_estado as 'estado',
					(SELECT COUNT(*) FROM cuota WHERE cuo_credito = c.cred_codigo) as 'cuotas'
				FROM credito c WHERE cred_prenda = ?
			";
			$res = $this->db->query($sql, array($id));
			// if ($task === 'clear') mysqli_next_result( $this->db->conn_id );
			return $res->result();
		}
		
		// 
		function Buscar_libre($data, $cond){
			$this->db->where($cond);
			$this->db->select($data);
			$res = $this->db->get('prenda');
			return $res->row();
		}


		// NEW CODE : 07-11-2020
		function SelectDetalle($id){
			$this->db->where('pren_codigo', $id);
			$this->db->select('pren_codigo as id');
			$this->db->select('CONCAT(cli_nombres," ",cli_paterno," ", cli_materno) as cliente');
			$this->db->select('pren_cliente as cliente_id');

			$this->db->select('cat_nombre as categoria');
			$this->db->select('cat_codigo as categoria_id');
			$this->db->select('pren_descripcion as descripcion');
			$this->db->select('pren_estado as estado');
			$this->db->select('tpren_nombre as tipo');

			$this->db->select('pren_serial as serial');
			$this->db->select('pren_marca as marca');
			$this->db->select('pren_modelo as modelo');
			$this->db->select('pren_quilate as quilate');
			$this->db->select('pren_peso as peso');
			$this->db->select('pren_observacion as observacion');
			$this->db->select('pren_comentario as comentario');
			$this->db->select('pren_valoracion as valoracion');
			$this->db->select('pren_fecha_registro as fecha_registro');
			$this->db->select('pren_kilometraje as kilometraje');
			$this->db->select('pren_anio as anio');
			$this->db->select('pren_peso as peso');
			$this->db->select('pren_placa as placa');
			$this->db->select('pren_transmision as transmision');

			$this->db->join('categoria', 'cat_codigo = pren_categoria');
			$this->db->join('tipo_prenda', 'tpren_codigo = cat_tipo');
			$this->db->join('cliente', 'cli_codigo = pren_cliente');
			$this->db->from('prenda');
			// $this->db->limit($limite);
			$this->db->order_by('cli_nombres', 'ASC');
			$res = $this->db->get();
			return $res->row();	
		}

		function SelectPropiedad($id){
			$this->db->where('prop_prenda', $id);
			$this->db->select('CONCAT(cli_nombres," ",cli_paterno," ", cli_materno) as cliente');
			$this->db->select('CONCAT(emp_nombres," ",emp_paterno," ", emp_materno) as empleado');
			$this->db->select('cli_codigo as cliente_id');
			$this->db->select('prop_inicio as inicio');

			$this->db->select('pren_transmision as transmision');
			$this->db->join('cliente', 'cli_codigo = prop_cliente');
			$this->db->join('empleado', 'emp_codigo = prop_empleado');
			$this->db->from('propiedad');
			$res = $this->db->get();
			return $res->result();	
		}

		function SelectAll($limite = 20){
			// $this->db->where("");
			$this->db->select('pren_codigo as id');
			$this->db->select('CONCAT(cli_nombres," ",cli_paterno," ", cli_materno) as cliente');
			$this->db->select('pren_descripcion as descripcion');
			$this->db->select('pren_estado as estado');
			$this->db->select('pren_observacion as observacion');
			$this->db->select('pren_comentario as comentario');
			$this->db->select('tpren_nombre as tipo');
			$this->db->select('cat_nombre as categoria');
			$this->db->select('pren_marca as marca');
			$this->db->select('pren_modelo as modelo');
			$this->db->select('pren_quilate as quilate');
			$this->db->select('pren_peso as peso');
			$this->db->select("
				IF(
					pren_estado = 'O', 
					(select cred_monto from credito where cred_prenda = pren_codigo order by cred_codigo desc limit 1),
					0
				) as monto_asignado
			");
			$this->db->select("
				IF(
					pren_estado = 'R', 
					(select DATE_FORMAT(cred_liquidacion, '%d/%m/%Y') from credito where cred_prenda = pren_codigo order by cred_codigo desc limit 1),
					'no definido'
				) as fecha_remate
			");
			$this->db->join('categoria', 'cat_codigo = pren_categoria');
			$this->db->join('tipo_prenda', 'tpren_codigo = cat_tipo');
			$this->db->join('cliente', 'cli_codigo = pren_cliente');
			$this->db->from('prenda');
			// $this->db->limit($limite);
			$this->db->order_by('cli_nombres', 'ASC');
			$res = $this->db->get();
			return $res->result();	
		}

		function SelectByCliente($buscar, $limite = 10){
			$this->db->where("cli_nombres LIKE CONCAT('%', '$buscar','%')");
			$this->db->or_where("cli_paterno LIKE CONCAT('%', '$buscar','%')");
			$this->db->select('pren_codigo as id');
			$this->db->select('CONCAT(cli_nombres," ",cli_paterno," ", cli_materno) as cliente');
			$this->db->select('pren_descripcion as descripcion');
			$this->db->select('pren_estado as estado');
			$this->db->select('pren_observacion as observacion');
			$this->db->select('pren_comentario as comentario');
			$this->db->select('tpren_nombre as tipo');
			$this->db->select('cat_nombre as categoria');
			$this->db->select('pren_marca as marca');
			$this->db->select('pren_modelo as modelo');
			$this->db->select('pren_quilate as quilate');
			$this->db->select('pren_peso as peso');
			$this->db->select("
				IF(
					pren_estado = 'O', 
					(select cred_monto from credito where cred_prenda = pren_codigo order by cred_codigo desc limit 1),
					0
				) as monto_asignado
			");
			$this->db->select("
				IF(
					pren_estado = 'R', 
					(select DATE_FORMAT(cred_liquidacion, '%d/%m/%Y') from credito where cred_prenda = pren_codigo order by cred_codigo desc limit 1),
					'no definido'
				) as fecha_remate
			");
			$this->db->join('categoria', 'cat_codigo = pren_categoria');
			$this->db->join('tipo_prenda', 'tpren_codigo = cat_tipo');
			$this->db->join('cliente', 'cli_codigo = pren_cliente');
			$this->db->from('prenda');
			// $this->db->limit($limite);
			$this->db->order_by('cli_nombres', 'ASC');
			$res = $this->db->get();
			return $res->result();	
		}

		function SelectByTipo($tipo, $limite = 10){
			$this->db->where("tpren_codigo", $tipo);
			$this->db->select('pren_codigo as id');
			$this->db->select('CONCAT(cli_nombres," ",cli_paterno," ", cli_materno) as cliente');
			$this->db->select('pren_descripcion as descripcion');
			$this->db->select('pren_estado as estado');
			$this->db->select('pren_observacion as observacion');
			$this->db->select('pren_comentario as comentario');
			$this->db->select('tpren_nombre as tipo');
			$this->db->select('cat_nombre as categoria');
			$this->db->select('pren_marca as marca');
			$this->db->select('pren_modelo as modelo');
			$this->db->select('pren_quilate as quilate');
			$this->db->select('pren_peso as peso');
			$this->db->select("
				IF(
					pren_estado = 'O', 
					(select cred_monto from credito where cred_prenda = pren_codigo order by cred_codigo desc limit 1),
					0
				) as monto_asignado
			");
			$this->db->select("
				IF(
					pren_estado = 'R', 
					(select DATE_FORMAT(cred_liquidacion, '%d/%m/%Y') from credito where cred_prenda = pren_codigo order by cred_codigo desc limit 1),
					'no definido'
				) as fecha_remate
			");
			$this->db->join('categoria', 'cat_codigo = pren_categoria');
			$this->db->join('tipo_prenda', 'tpren_codigo = cat_tipo');
			$this->db->join('cliente', 'cli_codigo = pren_cliente');
			$this->db->from('prenda');
			// $this->db->limit($limite);
			$this->db->order_by('cli_nombres', 'ASC');
			$res = $this->db->get();
			return $res->result();
		}

		function SelectByCategoria($categoria, $limite = 10){
			$this->db->where("pren_categoria", $categoria);
			$this->db->select('pren_codigo as id');
			$this->db->select('CONCAT(cli_nombres," ",cli_paterno," ", cli_materno) as cliente');
			$this->db->select('pren_descripcion as descripcion');
			$this->db->select('pren_estado as estado');
			$this->db->select('pren_observacion as observacion');
			$this->db->select('pren_comentario as comentario');
			$this->db->select('tpren_nombre as tipo');
			$this->db->select('cat_nombre as categoria');
			$this->db->select('pren_marca as marca');
			$this->db->select('pren_modelo as modelo');
			$this->db->select('pren_quilate as quilate');
			$this->db->select('pren_peso as peso');
			$this->db->select("
				IF(
					pren_estado = 'O', 
					(select cred_monto from credito where cred_prenda = pren_codigo order by cred_codigo desc limit 1),
					0
				) as monto_asignado
			");
			$this->db->select("
				IF(
					pren_estado = 'R', 
					(select DATE_FORMAT(cred_liquidacion, '%d/%m/%Y') from credito where cred_prenda = pren_codigo order by cred_codigo desc limit 1),
					'no definido'
				) as fecha_remate
			");
			$this->db->join('categoria', 'cat_codigo = pren_categoria');
			$this->db->join('tipo_prenda', 'tpren_codigo = cat_tipo');
			$this->db->join('cliente', 'cli_codigo = pren_cliente');
			$this->db->from('prenda');
			// $this->db->limit($limite);
			$this->db->order_by('cli_nombres', 'ASC');
			$res = $this->db->get();
			return $res->result();
		}

		function SelectByEstado($e, $limite = 10){
			$this->db->where("pren_estado", $e);
			$this->db->select('pren_codigo as id');
			$this->db->select('CONCAT(cli_nombres," ",cli_paterno," ", cli_materno) as cliente');
			$this->db->select('pren_descripcion as descripcion');
			$this->db->select('pren_estado as estado');
			$this->db->select('pren_observacion as observacion');
			$this->db->select('pren_comentario as comentario');
			$this->db->select('tpren_nombre as tipo');
			$this->db->select('cat_nombre as categoria');
			$this->db->select('pren_marca as marca');
			$this->db->select('pren_modelo as modelo');
			$this->db->select('pren_quilate as quilate');
			$this->db->select('pren_peso as peso');
			$this->db->select("
				IF(
					pren_estado = 'O', 
					(select cred_monto from credito where cred_prenda = pren_codigo order by cred_codigo desc limit 1),
					0
				) as monto_asignado
			");
			$this->db->select("
				IF(
					pren_estado = 'R', 
					(select DATE_FORMAT(cred_liquidacion, '%d/%m/%Y') from credito where cred_prenda = pren_codigo order by cred_codigo desc limit 1),
					'no definido'
				) as fecha_remate
			");

			$this->db->join('categoria', 'cat_codigo = pren_categoria');
			$this->db->join('tipo_prenda', 'tpren_codigo = cat_tipo');
			$this->db->join('cliente', 'cli_codigo = pren_cliente');
			$this->db->from('prenda');
			// $this->db->limit($limite);
			$this->db->order_by('cli_nombres', 'ASC');
			$res = $this->db->get();
			return $res->result();
		}


		// Encode: 01-03-2023

		// listar datos segun el id del cliente propietario
		function get_data_by_cliente($id){
			$this->db->where("pren_cliente", $id);
			$this->db->select('pren_codigo as id');
			$this->db->select('CONCAT(cli_nombres," ",cli_paterno," ", cli_materno) as cliente');
			$this->db->select('pren_descripcion as descripcion');
			$this->db->select('pren_estado as estado');
			$this->db->select('pren_observacion as observacion');
			$this->db->select('pren_comentario as comentario');
			$this->db->select('tpren_nombre as tipo');
			$this->db->select('cat_nombre as categoria');
			$this->db->select('pren_valoracion as valoracion');

			$this->db->select('pren_marca as marca');
			$this->db->select('pren_modelo as modelo');
			$this->db->select('pren_serial as serial');
			$this->db->select('pren_quilate as quilate');
			$this->db->select('pren_peso as peso');
			$this->db->select('pren_placa as placa');
			$this->db->select('pren_peso as peso');
			$this->db->select('pren_anio as anio');
			$this->db->select('pren_kilometraje as kilometraje');
			
			// $this->db->select('pren_area as area');
			// $this->db->select('pren_direccion as direccion');
			// $this->db->select('pren_ubigeo as ubigeo');
			// $this->db->select('pren_partida_registral as partida_registral');


			$this->db->select("
				IF(
					pren_estado = 'O', 
					(select cred_monto from credito where cred_prenda = pren_codigo order by cred_codigo desc limit 1),
					0
				) as monto_asignado
			");
			$this->db->select("
				IF(
					pren_estado = 'R', 
					(select DATE_FORMAT(cred_liquidacion, '%d/%m/%Y') from credito where cred_prenda = pren_codigo order by cred_codigo desc limit 1),
					'no definido'
				) as fecha_remate
			");
			$this->db->join('categoria', 'cat_codigo = pren_categoria');
			$this->db->join('tipo_prenda', 'tpren_codigo = cat_tipo');
			$this->db->join('cliente', 'cli_codigo = pren_cliente');
			$this->db->from('prenda');
			$this->db->order_by('cli_nombres', 'ASC');
			$res = $this->db->get();
			return $res->result();	
		}

		function get_data($id){
			$sql = "
				SELECT 
					p.pren_codigo as 'id',
					p.pren_descripcion as 'descripcion',
					DATE_FORMAT(p.pren_fecha_registro, '%d/%m/%Y') as 'create_at',
					p.pren_id_almacen as 'almacen_id',
					p.pren_cliente as 'cliente_id',
					CONCAT(c.cli_nombres,' ', c.cli_paterno, ' ',c.cli_materno) as 'cliente',
					ct.cat_nombre as 'categoria',
					p.pren_categoria as 'id_categoria',
					tp.tpren_nombre as 'tipo',
					tp.tpren_codigo as 'id_tipo',
					p.pren_serial as 'serial',
					p.pren_marca as 'marca',
					p.pren_modelo as 'modelo',
					p.pren_quilate as 'quilate',
					p.pren_peso as 'peso',
					p.pren_anio as 'anio',
					p.pren_kilometraje as 'kilometraje',
					p.pren_transmision as 'transmision',
					p.pren_color as 'color',
					p.pren_carroceria as 'carroceria',
					p.pren_placa as 'placa',
					p.pren_observacion as 'observacion',
					p.pren_comentario as 'comentario',
					p.pren_valoracion as 'valoracion',
					p.pren_reutilizacion as 'reutilizacion',
					p.pren_fecha_registro as 'fecha_registro',
					p.pren_estado as 'estado'	
				FROM prenda p
				INNER JOIN cliente c ON c.cli_codigo = p.pren_cliente
				INNER JOIN categoria ct ON ct.cat_codigo = p.pren_categoria
				INNER JOIN tipo_prenda tp ON tp.tpren_codigo = ct.cat_tipo
				WHERE p.pren_codigo = ?
			";
			$res = $this->db->query($sql, array($id));
			return $res->row();
		}

		function update_data($data, $id){
			$this->db->where('pren_codigo', $id);
			$this->db->update('prenda', $data);
		}

	}
?>
