<?php 

	class Venta extends CI_Controller 
	{	
		function __construct()
		{
			parent::__construct();
			$this->load->model('ArticuloModel');
			// $this->load->model('PanelModel');
			$this->load->model('VentaModel');
		}

		public function service_registro(){
			$errLog = [];
			try {
				$codelog = '';
				$this->db->trans_begin();
				$file = file_get_contents("php://input");
				$data_receive = json_decode($file);
				$errLog = $this->validate_registro($data_receive);
				if (count($errLog) > 0) {
					throw new exception("Errores encontrados");
				}

				$data = array(
					$this->session->userdata('pres_ses_empleado'),					
					$data_receive->dni,
					strtoupper($data_receive->nombre),
					$data_receive->celular,
					strtoupper($data_receive->metodo),
					$data_receive->subtotal,
					$data_receive->descuento,
					$data_receive->total,
					strtoupper($data_receive->comentario),
					date("Y-m-d H:i:s"),
					date("Y-m-d H:i:s")
				);
				$this->VentaModel->SetInsert($data);
				if ($this->db->affected_rows() <= 0) throw new exception('Registro Incompleto [venta]'.$this->db->insert_id());


				$id_venta = $this->db->insert_id();
				$codelog .= $id_venta."_";
				$articulos = $data_receive->detalle;

				foreach ($articulos as $art) {
					
					$verState = $this->ArticuloModel->GetStateById($art->id);
					if (!$verState || $verState->estado != 'D') throw new exception("Articulo no disponible");

					$data_detalle = array(
						$id_venta,
						$art->id,
						$art->precio,
						1,
						$art->total
					);
					if (!$this->VentaModel->SetInsertVentaDetalle($data_detalle)) throw new exception("Registro Incompleto [detalle]");

					$this->ArticuloModel->SetUpdateIDK($art->id, ['estado'], ['V']);
				}
				if (!$this->db->affected_rows()) throw new exception("Registro Incompleto [articulo][upd]=".$this->db->affected_rows());

				echo json_encode(array(
					'code' => 'OK',
					'message' => 'Registro Completo',
					'log' => $codelog
				));
				$this->db->trans_commit();
			} catch (PDOException $e) {
				$this->db->trans_rollback();
				die(json_encode(array(
					'code' => 'WARN',
					'error_log' => $errLog,
					'message' => $e->getMessage()
				)));
			}

		}

		public function validate_registro($data){
			$err_ = [];
			if (!$data->subtotal) array_push($err, ['subtotal' => 'Campo requerido']);
			if (!$data->total) array_push($err, ['total' => 'Campo requerido']); 
			if (!$data->detalle || empty($data->detalle)) array_push($err, ['articulo' => 'Articulo no encontrado']);
			
			// if ($data->detalle && !empty($data->detalle)) {

				// $state = $this->ArticuloModel->GetStateById($data->detalle);
				// if (!$data->detalle[0]->id) array_push($err, ['articulo' => 'No encontrado']);
				// $data_search = $this->ArticuloModel->GetSearchFree(
				// 	['estado as estado'], 
				// 	"art_id = ".$data->detalle[0]->id, 
				// 	"row"
				// );
				// if ($data_search->estado != 'D') array_push($err, ['articulo' => 'Articulo no disponible']); 
			// }

			return $err_;
		}

		public function test(){
			$data = $this->ArticuloModel->GetSearchFree(['id', 'estado'], "art_id = 213", "row");
			echo json_encode($data);
		}

		public function service_anulacion(){
			try {
				$this->db->trans_begin();
				$file = file_get_contents("php://input");
				$data_receive = json_decode($file);

				if (!$data_receive->venta || !$data_receive->articulo) throw new exception('Venta no identificada');

				$this->VentaModel->SetDeleteVenta($data_receive->venta);
				if ($this->db->affected_rows() <= 0) throw new exception('Anulacion Incompleta [venta]');

				$this->ArticuloModel->SetUpdateIDK($data_receive->articulo, ['estado'], ['D']);
				if ($this->db->affected_rows() <= 0) throw new exception("Anulacion Incompleta [articulo]");
				echo json_encode([
					'code' => 'OK',
					'message' => 'Anulacion Realizada'
				]);
				$this->db->trans_commit();
			} catch (PDOException $e) {
				$this->db->trans_rollback();
				die(json_encode(array(
					'code' => 'WARN',
					'message' => $e->getMessage()
				)));
			}
		}

		public function service_recibo(){
			$file = file_get_contents("php://input");
			$data_receive = json_decode($file);
			
			$venta = $this->VentaModel->GetVenta($data_receive->id);
			$detalle = $this->VentaModel->GetVentaDetalle($data_receive->id);

			if ($venta) {
				$venta->pre_creacion = $this->get_date_pre($venta->creacion);
			}
			echo json_encode([
				'venta' => $venta,
				'detalle' => $detalle
			]);
		}

		public function get_date_pre($datex){
			try {
				date_default_timezone_set('America/Lima');
				$days = array(
					"Monday" => "Lunes",
					"Tuesday" => "Martes",
					"Wednesday" => "Miércoles",
					"Thursday" => "Jueves",
					"Friday" => "Viernes",
					"Saturday" => "Sabado",
					"Sunday" => "Domingo",
				);
				$months = array(
					"January" => "Enero",
					"February" => "Febrero",
					"March" => "Marzo",
					"April" => "Abril",
					"May" => "Mayo",
					"June" => "Junio",
					"July" => "Julio",
					"August" => "Agosto",
					"September" => "Septiembre",
					"October" => "Octubre",
					"November" => "Noviembre",
					"December" => "Diciembre",
				);
				// $fecha_base = date('2021-09-15');
				$fecha_base = date($datex);
				$mes_nombre = date('F', strtotime($fecha_base));
				$mes_nombre = $months[$mes_nombre];
				$dia_nombre = date('l', strtotime($fecha_base));
				$dia_nombre = $days[$dia_nombre];
				$dia_numero = date('d', strtotime($fecha_base));
				$anio = date('Y', strtotime($fecha_base));
	
				return "$dia_nombre, $dia_numero de $mes_nombre de $anio";
			} catch (Exception $e) {
				return "";
			}
		}

	}
	