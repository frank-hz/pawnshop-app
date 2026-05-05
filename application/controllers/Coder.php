<?php 

	class Code extends CI_Controller {

		// Agregar campo 'cred_ciclo_cobro'
		// para especificar el tiempo de espera entre cuotas
		function __construct()
		{
			parent::__construct();
			$this->load->model('CoderModel');
			$this->load->model('PrendaModel');
		}

		public function new_register(){
			try {
				$this->db->trans_begin();


			} catch (PDOException $e) {
				//throw $th;
			}
			
		}

		public function validations_prev_register(){
			if ($this->session->userdata('pres_ses_sesion')) { 
				$rol = $this->session->userdata('pres_ses_rol');
				$permss = $rol === 'Administrador' ? 1 : $this->CoderModel->validate_user_permission('credito_registrar');
				if (!$permss) throw new exception('Permisos Insuficientes.');
			} else {
				throw new exception('Sesion Caducada.');
			}


		}

		function validate_registro($data){
			$error = [];
			if (empty($data['cliente'])) {
				$error['cliente'] = 'El cliente es requerido.';
			}
			if (empty($data['monto'])) {
				$error['monto'] = 'Monto no procesado';
			}
			if (empty($data['prorroga']) || $data['prorroga'] == 0 ) {
				$error['prorroga'] = 'Especifique los dias de prorroga.';
			}
			if (empty($data['pago'])) {
				$error['pago'] = 'Pago semanal no procesado.';
			}
			// if (empty($data['tarifa'])) {
			// 	$error['tarifa'] = 'Tarifa no definida.';
			// }else{
			// 	if ($data['tarifa'] == 0) {
			// 		$error['tarifa'] = 'Tarifa incorrecta.';	
			// 	}
			// }
			if (empty($data['mora'])) {
				$error['mora'] = 'Mora no procesada.';
			}
			return $error;
		}


	}

