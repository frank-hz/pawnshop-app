<div class="container">
	<div class="row">
		<div class="col-xs-12 col-lg-12">
			<div class="card">
				<div class="card-header">
					<h4 class="card-title mt-0 mb-4">
						<i class="fe fe-settings text-blue"></i>
						Configuracion
					</h4>
					<div class="card-options">
						<a href="<?= base_url() ?>App/revision" class="btn">
							Aplicar Revision
							<i class="fe fe-circle"></i>
						</a>
					</div>
				</div>
              	<div class="card-body">
					<div class="row">
						<div class="col-xs-12 col-md-6">
							<div class="card">
								<div class="card-header">
									<h3 class="card-title">
										Fondo de Pantalla - Login 
									</h3>
									<div class="card-options">
										<button class="btn btn-secondary">
											Cambiar 
											<i class="fe fe-camera"></i>
										</button>
									</div>
								</div>
								<div class="card-body">
									<img src="<?= base_url().$config->valor; ?>" alt="">
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>


<script src="<?= base_url() ?>js_modules/configuracion/configuracion.js"></script>
