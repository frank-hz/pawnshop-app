<?php  
	
	class TipoPrenda extends CI_Controller
	{
		
		function __construct()
		{
			parent::__construct();
			if (!$this->session->userdata('pres_ses_sesion')) {
				redirect(base_url().'Acceso','refresh');
			}
			$this->load->model('TipoPrendaModel');
			$this->load->model('PermisoModel');
			$this->load->model('CategoriaModel');
		}
		// Vistas
		function index(){
			$data['tipos'] = $this->TipoPrendaModel->Lista();
			$this->load->view("Layout/header.php");
			$this->load->view("TipoPrenda/lista.php", $data);
			$this->load->view("Layout/footer.php");
		}
		function editar($id = null){
			if ($id != null) {
				$data['tipo'] = $this->TipoPrendaModel->Buscar_detalle($id);
				if (!empty($data['tipo'])) {
					$this->load->view("Layout/header.php");
					$this->load->view("TipoPrenda/detalle.php", $data);
					$this->load->view("Layout/footer.php");
				}else{
					$data['titulo'] = 'Cliente desconocido';
					$data['mensaje'] = 'No se encontraron datos del cliente';
					$this->load->view("Layout/header.php");
					$this->load->view("Error/no_found.php", $data);
					$this->load->view("Layout/footer.php");
				}
			}else{
				$data['titulo'] = 'Tipo de prenda desconocido';
				$data['mensaje'] = 'No se encontraron datos para el seguimiento del registro';
				$this->load->view("Layout/header.php");
				$this->load->view("Error/no_found.php", $data);
				$this->load->view("Layout/footer.php");
			}
		}


		// Servicios
		function registrar(){
			$rsp = [];
			if (!empty($_POST['nombre'])) {
				$ver = $this->TipoPrendaModel->Buscar_libre(
					'tpren_codigo as id', 
					array('tpren_nombre' => $_POST['nombre'])
				);
				if (empty($ver)) {
					$this->TipoPrendaModel->Insertar(array(
						'tpren_nombre' => strtoupper($_POST['nombre']), 
						'tpren_descripcion' => $_POST['descripcion'],
						'tpren_estado' => 'A'
					));
					$id = $this->db->insert_id();
					$rsp = array(
						'status' => 'OK',
						'message' => 'Proceso completado con exito',
						'id' => $id
					);
				}else{
					$rsp = array(
						'status' => 'WARN',
						'message' => 'Ya existe un tipo de prenda con el mismo nombre',
						'id' => ''
					);
				}
			}else{
				$rsp = array(
					'status' => 'WARN',
					'message' => 'Datos incompletos',
					'id' => ''
				);
			}
			echo json_encode($rsp);
		}
		function eliminar(){
			$rsp = [];
			if (!empty($_POST['id'])) {
				$this->TipoPrendaModel->Eliminar($_POST['id']);
				if ($this->db->affected_rows() > 0) {
					$rsp = array(
						'status' => 'OK',
						'message' => 'Proceso completado con exito'
					);
				}else{
					$rsp = array(
						'status' => 'WARN',
						'message' => 'No se pudo completar el proceso'
					);
				}
			}else{
				$rsp = array(
					'status' => 'WARN',
					'message' => 'Datos incompletos'
				);
			}
			echo json_encode($rsp);
		}
		function consulta(){
			$res = $this->TipoPrendaModel->Lista();
			echo json_encode($res);
		}
		function consulta_formulario(){
			$res = $this->TipoPrendaModel->Buscar_formulario_verifica($_POST['id']);
			echo json_encode($res);
		}
		function form_pre_procesado(){
			$rsp = [];
			if (!empty($_POST['id'])) {
				$data = $this->TipoPrendaModel->Buscar_formulario($_POST['id']);
				$html = '';
				foreach ($data as $d) {
					switch ($d->nombre) {
						case 'SERIAL':
							$html .= '
								<div class="col-xs-12 col-md-6">
									<div class="form-group">
										<label for="" class="form-label">Serie</label>
										<input name="serial" type="text" class="form-control border-rock text-uppercase">
									</div>
								</div>
							';
							break;
						case 'MARCA':
							$html .= '
								<div class="col-xs-12 col-md-6">
									<div class="form-group">
										<label for="" class="form-label">Marca</label>
										<input name="marca" type="text" class="form-control border-rock text-uppercase">
									</div>
								</div>
							';
							break;
						case 'MODELO':
							$html .= '
								<div class="col-xs-12 col-md-6">
									<div class="form-group">
										<label for="" class="form-label">Modelo</label>
										<input name="modelo" type="text" class="form-control border-rock text-uppercase">
									</div>
								</div>
							';
							break;
						case 'QUILATE':
							$html .= '
								<div class="col-xs-12 col-md-6">
									<div class="form-group">
										<label for="" class="form-label">Quilate</label>
										<input type="text" name="quilate" class="form-control border-rock text-uppercase">
									</div>
								</div>
							';
							break;
						case 'PESO':
							$html .= '
								<div class="col-xs-12 col-md-6">
									<div class="form-group">
										<label for="" class="form-label">Peso</label>
										<input type="text" name="peso" class="form-control border-rock text-uppercase">
									</div>
								</div>
							';
							break;
						case 'AÑO':
							$html .= '
								<div class="col-xs-12 col-md-6">
									<div class="form-group">
										<label for="" class="form-label">Año</label>
										<input type="text" name="anio" class="form-control border-rock text-uppercase">
									</div>
								</div>
							';
							break;
						case 'PLACA':
							$html .= '
								<div class="col-xs-12 col-md-6">
									<div class="form-group">
										<label for="" class="form-label">Placa</label>
										<input type="text" name="placa" class="form-control border-rock text-uppercase">
									</div>
								</div>
							';
							break;
						case 'KILOMETRAJE':
							$html .= '
								<div class="col-xs-12 col-md-6">
									<div class="form-group">
										<label for="" class="form-label">Kilometraje</label>
										<input type="text" name="kilometraje" class="form-control border-rock text-uppercase">
									</div>
								</div>
							';
							break;
						case 'TRANSMISION':
							$html .= '
								<div class="col-xs-12 col-md-6">
									<div class="form-group">
										<label for="" class="form-label">Transmision</label>
										<input type="text" name="transmision" class="form-control border-rock text-uppercase">
									</div>
								</div>
							';
							break;
						case 'VALORACION':
							$html .= '
								<div class="col-xs-12 col-md-6">
									<div class="form-group">
										<label for="" class="form-label">Estado</label>
										<input name="valoracion" type="number" step="any" value="5" max="10" min="1" class="form-control border-rock text-center font-bold">
									</div>
								</div>
							';
							break;
						case 'OBSERVACION':
							$html .= '
								<div class="col-xs-12 col-md-12">
									<div class="form-group">
										<label for="" class="form-label">Observaciones</label>
										<textarea name="observacion" class="form-control border-rock text-uppercase" cols="3" rows="3"></textarea>
									</div>
								</div>
							';
							break;
						case 'COMENTARIO':
							$html .= '
								<div class="col-xs-12 col-md-12">
									<div class="form-group">
										<label for="" class="form-label">Comentarios</label>
										<textarea name="comentario" class="form-control border-rock text-uppercase" cols="5" rows="3"></textarea>
									</div>
								</div>
							';
							break;
					}
				}

				if (!empty($html)) {
					$rsp = array(
						'status' => 'OK',
						'message' => 'Formulario procesado',
						'html' => $html,
						'controls' => '
							<button class="btn btn-primary">
								Guardar 
								<i class="fe fe-save"></i>
							</button>
							<button type="button" class="btn btn-secondary" data-dismiss="modal">
					        	Cancelar
					        	<i class="fe fe-x-circle"></i>
					        </button>
						'
					);	
				}else{
					$rsp = array(
						'status' => 'WARN',
						'message' => 'Formulario no encontrado',
						'html' => $html,
						'controls' => '
							<button type="button" class="btn btn-danger" data-dismiss="modal">
					        	Cancelar
					        	<i class="fe fe-x-circle"></i>
					        </button>
						'
					);
				}
			}else{
				$rsp = array(
					'status' => 'WARN',
					'message' => 'Error al procesar',
					'html' => '',
					'controls' => '
						<button type="button" class="btn btn-danger" data-dismiss="modal">
				        	Cancelar
				        	<i class="fe fe-x-circle"></i>
				        </button>
					'
				);
			}
			echo json_encode($rsp);
		}	

		function modifica_atributo(){
			$rsp = [];
			if (!empty($_POST['atributo']) && !empty($_POST['tipo']) && !empty($_POST['estado'])) {
				if ($_POST['estado'] === 'NO') {
					$this->TipoPrendaModel->Formulario_agregar(array(
						'pform_tipo' => $_POST['tipo'],
						'pform_atributo' => $_POST['atributo']
					));
					$id = $this->db->insert_id();
					$rsp = array(
						'status' => 'OK',
						'message' => 'Atributo agregado',
						'id' => $id
					);
				}elseif ($_POST['estado'] === 'SI') {
					$this->TipoPrendaModel->Formulario_remover($_POST['tipo'],$_POST['atributo']);
					if ($this->db->affected_rows() > 0) {
						$rsp = array(
							'status' => 'OK',
							'message' => 'Atributo removido'
						);
					}else{
						$rsp = array(
							'status' => 'WARN',
							'message' => 'Proceso no completado'
						);
					}
				}else{
					$rsp = array(
						'status' => 'WARN',
						'message' => 'Accion no definida'
					);
				}
			}else{
				$rsp = array(
					'status' => 'WARN',
					'message' => 'Datos incompletos'
				);
			}
			echo json_encode($rsp);
		}        
        function ServiceConsulta(){
			$data = $this->TipoPrendaModel->SelectAll();
			echo json_encode($data);		
		}



		// Recode 05-2023
		function get_all(){
            $data = $this->TipoPrendaModel->get_all();
            echo json_encode($data);
        }     
        function get_data(){
            $data = $this->TipoPrendaModel->getData($_POST['id']);
            echo json_encode($data);
        }
		
    
	}
?>