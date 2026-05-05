<?php 
	class Remate extends CI_Controller {
		function __construct()
		{
			parent::__construct();
			if (!$this->session->userdata('pres_ses_sesion')) {
				redirect(base_url().'Acceso', 'refresh');
			}
			$this->load->model('RemateModel');
			$this->load->model('ArticuloModel');
		}

		function index(){
			$this->load->view('Remate/remate.v2.php');
		}

		function recode(){
			$data = $this->ArticuloModel->getCreditosRemate();
			$this->load->view('Remate/recode.php', ['redata' => $data]);
			// foreach ($data as $d) {
			// 	echo 
			// }
		}

		function service_registro(){
			try {
				$this->db->trans_begin();
				$vald = $this->validate_registro($_POST);
				if (!$_POST) throw new exception("Datos no encontrados");
				$data = array(
					$_POST['dni'],
					$_POST['nombre'],
					$this->session->userdata('pres_ses_empleado'),					
					$_POST['precio'],
					$_POST['descuento'],
					$_POST['total'],
					date("Y-m-d H:i:s"),
					date("Y-m-d H:i:s")
				);
				if (!$this->RemateModel->SetInsertVenta($data)) throw new exception("Venta no registrada");
				$id_venta = $this->db->insert_id();
				$data_detalle = array(
					$id_venta,
					$_POST['articulo'],
					$_POST['precio'],
					1,
					$_POST['total']
				);
				if (!$this->RemateModel->SetInsertVentaDetalle($data_detalle)) throw new exception("Registro Incompleto");
				
				$this->ArticuloModel->SetUpdateIDK($_POST['articulo'], ['estado'], ['V']);
				if ($this->db->affected_rows() <= 0) throw new exception("Actualizacion Incompleta");
				echo json_encode(array(
					'code' => 'OK',
					'message' => 'Registro Completo'
				));
				$this->db->trans_commit();
			} catch (PDOException $e) {
				$this->db->trans_rollback();
				die(json_encode(array(
					'code' => 'WARN',
					'message' => $e->getMessage()
				)));
			}
		}

		function validate_registro(){
			$errors = [];
			return $errors;
		}
		
		function service_venta_recibo(){
			$file = file_get_contents("php://input");
			$data_receive = json_decode($file);
			
			$res = $this->RemateModel->GetVenta($data_receive->id);
			$data_resp = [];
			if ($res) {
				$res->pre_creacion = $this->get_date_pre($res->creacion);
				$data_resp = $res;
			}
			echo json_encode($data_resp);
		}

		function get_date_pre($datex){
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

?>
