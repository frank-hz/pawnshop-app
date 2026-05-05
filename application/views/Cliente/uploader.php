<div class="container">
	<div class="card">
		<div class="card-header">
			Uploader
		</div>
		<div class="card-body">
			<form id="frmCliente" action="<?= base_url() ?>Cliente/subir" method="POST" class="row" autocomplete="off" enctype="multipart/form-data">
				<div class="row">
					<div class="col-md-8">
						<label for="">Archivo</label>
						<input type="file" class="form-control" name="foto">
					</div>
				</div>
				<button type="submit" class="btn btn-blue">Enviar</button>
			</form>
		</div>
	</div>
</div>