<?php 

class Articulo extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
		$this->load->model('ArticuloModel');
		$this->load->model('PanelModel');
	}

	public function service_get_all(){
		$file = file_get_contents('php://input');
		$data = json_decode($file);
		$res = [];
		switch ($data->type) {
			case 'ALL':
				$res = $this->ArticuloModel->GetAll($data->sucursal);
				break;
			
			case 'CREDITO':
				$res = $this->ArticuloModel->GetAllByCredit($data->value, $data->sucursal);
				break;	

			case 'CLIENTE':
				$res = $this->ArticuloModel->GetAllByClient($data->value, $data->sucursal);
				break;

			case 'DESCRIPCION':
				$res = $this->ArticuloModel->GetAllByDesc($data->value, $data->sucursal);
				break;

			case 'TIPO':
				$res = $this->ArticuloModel->GetAllByType($data->value, $data->sucursal);
				break;

			case 'ESTADO':
				$res = $this->ArticuloModel->GetAllByState($data->value, $data->sucursal);
				break;
			
			default:
				$res = $this->ArticuloModel->GetAll($data->sucursal);
				break;
		}
		echo json_encode($res);
	} 

	public function service_search(){
		try {
			$file = file_get_contents("php://input");
			$data_recv = json_decode($file);

			$data = $this->ArticuloModel->GetSearchById($data_recv->id);
			echo json_encode($data);
		} catch (\Throwable $th) {
			echo json_encode([]);
		}
	}

	public function service_update(){
		try {
			// $data_recv = $_POST;
			$file = file_get_contents('php://input');
			$data_recv = json_decode($file);
			$resp = [];
			if (!$data_recv) throw new Exception('Datos no encontrados');			
			$valida = $this->validate_update($data_recv);
			if (!empty($valida)) {
				die(json_encode([
					'code' => 'WARN',
					'message' => 'Errores encontrados',
					'error_log' => $valida
				]));
			}
			$data_upd = array(
				'costo_reparacion' => $data_recv->costo_reparacion,
				'costo_total' => $data_recv->costo_total,
				'precio_venta' => $data_recv->precio_venta
			);
			if ($this->ArticuloModel->SetUpdate($data_recv->id, $data_recv)) {
				$resp = [
					'code' => 'OK',
					'updates' => $this->db->affected_rows(),
					'message' => ($this->db->affected_rows() > 0) 
						? 'Cambios Aplicados' : 'Sin Cambios'
				];
			} else{
				$resp = [
					'code' => 'WARN',
					'message' => 'No se completo la actualizacion'
				];
			}
			echo json_encode($resp);
		} catch (PDOException $e) {
			echo json_encode([
				'code' => 'ERROR',
				'message' => $e->getMessage()
			]);
		}
	}

	public function validate_update($data){
		$warns = [];
		if (!$data->id OR $data->id <= 0) {
			array_push($warns, ['id' => 'Articulo no identificado']);
		}
		if (!$data->costo_inicial) {
			array_push($warns, ['costo_inicial' => 'este dato es requerido']);
		}
		if (!$data->costo_reparacion) {
			array_push($warns, ['costo_reparacion' => 'este dato es requerido']);
		}
		if (!$data->costo_total) {
			array_push($warns, ['costo_total' => 'este dato es requerido']);
		}
		if (!$data->precio_venta) {
			array_push($warns, ['precio_venta' => 'este dato es requerido']);
		}
		return $warns;
	}




	function service_scan_prendas_remate(){
		// El estado 'A' en la tabla prendas representa 'En Remate'
		$prendas = $this->PanelModel->GetPrendasByState('A');		
		$cant_eval = count($prendas);
		$new_in = 0;
		$currents = 0;
		$denied = 0;
		foreach ($prendas as $p) {
			$articulo_data = $this->PanelModel->GetArticuloByPrenda($p->id);
			
			if ($articulo_data) {                    
				$currents++;
			}else {
				$credito_origen = $this->ArticuloModel->GetArticuloCreditoOrigen($p->id);
				if ($credito_origen) {
					$valor_prenda = ($credito_origen != null) ? $credito_origen->monto : 0; 

					$ins = $this->ArticuloModel->SetInsert(array(
						$p->id,
						$credito_origen->id,
						'D',
						$valor_prenda,
						$valor_prenda,
						0,
						$credito_origen->fecha,
						$credito_origen->fecha
					));
					if ($ins) {
						$new_in++;
					}else {
						$denied++;
					}                  
				}	
			}
		}
		echo json_encode([
			'total_evaluado' => $cant_eval,
			'actuales' => $currents,
			'nuevos' => $new_in,
			'denegado' => $denied
		]);
	}

	function service_set_date(){
		$articulos = $this->ArticuloModel->GetAll();
		$res = [
			'error_log' => [],
			'success_log' => [],
			'news' => 0,
			'no_changes' => 0
		];
		foreach ($articulos as $art) {
			$credito_origen = $this->ArticuloModel->GetArticuloCreditoOrigen($art->id_prenda);
			array_push($res['error_log'], $art->id);
			if ($credito_origen) {
				$this->ArticuloModel->SetUpdateIDK(
					$art->id, 
					['credito'], 
					[$credito_origen->id]
				);
				$changes_rows = $this->db->affected_rows();
				if ($changes_rows > 0) $res['news']++;
				if ($changes_rows <= 0) $res['no_changes']++;
				array_push($res['success_log'], $art);
			}else {
				array_push($res['error_log'], $art);
			}
		}

		echo json_encode($res);
	}

	function service_articulo_comproba3(){
		$rsp = [
			'no_work' => [],
			'work' => []
		];
		$prendas = $this->PanelModel->GetPrendasByState('A');
		foreach ($prendas as $p) {
			$credito_origen = $this->ArticuloModel->GetArticuloCreditoOrigen($p->id);
			if ($credito_origen) {
				array_push($rsp['work'], [
					'prenda' => $p->id,
					'credito_found' => $credito_origen
				]);
			} else{
				array_push($rsp['no_work'], [
					'prenda' => $p->id,
					'credito_found' => $credito_origen
				]);
			}
		}
		echo json_encode($rsp);
	}

}
