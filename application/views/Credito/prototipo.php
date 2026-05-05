<div class="custom-form-1709">
	<div class="data-form">
		<div class="card">
			<div class="card-header">
				<div class="data-viewer-title">
					<i class="fe fe-file"></i>&nbsp;Nuevo Credito
				</div>
			</div>
			<div class="card-body py-1 px-2">
				<div class="row">
					<div class="col-md-3 form-group">
						<div class="form-label">Tipo de Busqueda</div>
						<select class="form-control border-rock" id="">
							<option value="">Nombres</option>
							<option value="">DNI</option>
						</select>
					</div>
					<div class="col-md-9 form-group">
						<div class="form-label">Buscar Cliente</div>
						<input type="text" class="form-control border-rock">
					</div>
				</div>				
			</div>
			<div class="card-body py-2 px-1">
				<div class="d-flex">
					<div class="w-70">
						<table class="table ">
							<thead>
								<tr>
									<th>Nombres y Apellidos</th>
									<th>Ciudad</th>
									<th>Documento</th>
									<th class="text-center">Opcion</th>
								</tr>
							</thead>
							<tbody id="view_cliente_table">
								<tr class="text-center">
									<td colspan="4" class="p-4 font-500 text-primary">
										Inicia la busqueda
									</td>
								</tr>
							</tbody>
						</table>
					</div>
					<div class="w-30">
						<div class="preview-blank">
							<span>Sin Datos</span>
						</div>
						<!-- <div class="preview-card">
							<div class="preview-icon">
								<svg xmlns="http://www.w3.org/2000/svg" class="icon" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><line x1="12" y1="8" x2="12.01" y2="8" /><rect x="4" y="4" width="16" height="16" rx="2" /><polyline points="11 12 12 12 12 16 13 16" /></svg>
							</div>							
							<div class="preview-main-text">JOSE PEREZ LUJAN</div>
							<div class="d-flex justify-between px-2 py-3">
								<div class="mid-text">
									<span>DNI: </span>
									70654840
								</div>
								<div class="mid-text">
									<span>Ciudad: </span>
									Chimbote, Ancash
								</div>
							</div>							
							<div class="preview-danger-area">
								<span>CLIENTE EN LISTA NEGRA</span>
								<span>Deudas Pendientes</span>
							</div>
						</div> -->
					</div>
				</div>
			</div>

		</div>
	</div>
	<div class="data-viewer">
		<div class="card">
			<div class="card-header">
				<div class="data-viewer-title">
					<i class="fe fe-list"></i>&nbsp;Resumen
				</div>
			</div>
			<div class="card-body p-2 px-5">
				<div class="data-item-label">Datos del Cliente</div>
				<div class="d-flex">
					<div class="view-group-info w-100">
						<label for="">Nombres y Apellidos:</label>
						<span>JOSE ALVARES ROJAS</span>
					</div>			
				</div>
				<div class="d-flex">
					<div class="view-group-info w-50">
						<label for="">Dni/Documento:</label>
						<span>70609345</span>
					</div>
					<div class="view-group-info w-50">
						<label for="">Distrito:</label>
						<span>CHIMBOTE, ANCASH</span>
					</div>
				</div>	
			</div>
			<div class="card-body p-2 px-5">
				<div class="data-item-label">Datos de la Prenda</div>
				<div class="d-flex">
					<div class="view-group-info w-100">
						<label for="">Nombres y Apellidos:</label>
						<span>JOSE ALVARES ROJAS</span>
					</div>			
				</div>
				<div class="d-flex">
					<div class="view-group-info w-50">
						<label for="">Dni/Documento:</label>
						<span>70609345</span>
					</div>
					<div class="view-group-info w-50">
						<label for="">Distrito:</label>
						<span>CHIMBOTE, ANCASH</span>
					</div>
				</div>	
			</div>
			<div class="card-body p-2 px-5">
				<div class="data-item-label">Datos del Prestamo</div>
				<div class="d-flex">
					<div class="view-group-info w-100">
						<label for="">Nombres y Apellidos:</label>
						<span>JOSE ALVARES ROJAS</span>
					</div>			
				</div>
				<div class="d-flex">
					<div class="view-group-info w-50">
						<label for="">Dni/Documento:</label>
						<span>70609345</span>
					</div>
					<div class="view-group-info w-50">
						<label for="">Distrito:</label>
						<span>CHIMBOTE, ANCASH</span>
					</div>
				</div>	
			</div>
		</div>
		<!-- <div class="data-viewer-title">Nuevo Credito</div>
		<div class="data-view-item">
			<div class="data-item-label">Datos del Cliente</div>
			<div class="d-flex">
				<div class="view-group-info w-100">
					<label for="">Nombres y Apellidos:</label>
					<span>JOSE ALVARES ROJAS</span>
				</div>			
			</div>
			<div class="d-flex">
				<div class="view-group-info w-50">
					<label for="">Dni/Documento:</label>
					<span>70609345</span>
				</div>
				<div class="view-group-info w-50">
					<label for="">Distrito:</label>
					<span>CHIMBOTE, ANCASH</span>
				</div>
			</div>			
		</div> -->
		
	</div>	
</div>


<style>
	.w-70 {
		width: 70%;
	}
	.w-75 {
		width: 75%;
	}
	.w-25 {
		width: 25%;
	}
	.w-30 {
		width: 30%;
	}
	.font-500 {
		font-weight: 500;
		letter-spacing: .3px;
		font-size: .945rem;
	}
	.justify-around {
		justify-content: space-around;
	}
	.justify-between {
		justify-content: space-between;
	}
	.media-item-card {
		padding: 0;
		margin: .5rem .2rem;
		display: block;
		position: relative;
	}
	.media-item-area {
		display: flex;
		width: 100%;
		align-items: center;
		background: #fff;
		border: 1px solid rgba(184, 195, 223, .6);
		padding: .7rem 1rem;
		border-radius: 4px;
		cursor: pointer;
		transition: all .3s ease;
		
	}
	.media-item-checked-icon {
		position: relative;
		width: 18px;
		height: 18px;
		border: 2px solid rgba(113, 141, 165, .8);
		border-radius: 50%;
		margin-right: .6rem;
		display: flex;
		align-items: center;
		justify-content: center;		
	}
	.media-item-checked-icon::after {
		position: absolute;
		display: inline-block;
		text-align: center;
		content: '';
		width: 18px;
		height: 18px;
		border-radius: 50%;
		border: 5px solid transparent;
		transition: all .3s ease;
	}
	.media-item-input {
		position: absolute;
		top: 0;
		left: 0;
		z-index: -1;
		opacity: 0; 
	}
	.media-item-input:checked + .media-item-area {
		background: rgba(87, 104, 199, .1);
		border-color: rgba(87, 104, 199, .8);
		box-shadow: rgba(87, 104, 199, 0.4) 0px 0px 0px 4px; 
	}
	.media-item-input:checked + .media-item-area .media-item-checked-icon::after {
		border-color: #3d50b6;
	}
	.media-item-text {
		padding: .2rem 1rem;
		font-weight: 500;
		color: #000230;
	}
	.media-conduct-preview {
		border-radius: 2px;
		padding: .2rem .6rem;
		background: rgba(246, 70, 104, .9);
		text-align: center;
		display: none;
		transition: all .3s ease;
	}
	.media-conduct-preview span:nth-child(1){
		font-weight: 700;
		font-size: .925rem;
		color: #f64668;
		color: #fff;
		display: block;
	}
	.media-conduct-preview span:nth-child(2){
		display: block;
		color: #fff;
	}
	.media-preview-on:checked + .media-item-area .media-conduct-preview {
		display: block;
	}

	.form-label {
		display: block;
		font-weight: 500;
		font-size: .945rem;
		margin-bottom: 0;
		padding-bottom: 0;
		color: #000230;
		margin-bottom: 2px;
	}
	.custom-form-1709 {
		display: flex;
		border-radius: 4px;
		margin: 0 .4rem;
	}
	.data-viewer-title {
		color: #000230;
		font-weight: 600;
		font-size: 1.4em;		
		display: flex;
		align-items: center;
	}
	.data-view-item {
		border-radius: 4px;
		display: block;
		border-top: 1px solid rgba(208, 219, 241, .4);
		color: #000230;
		background: rgba(185, 199, 219, .2);
		box-shadow: 
			rgba(0, 0, 0, 0.16) 0px 10px 36px 0px, 
			rgba(0, 0, 0, 0.06) 0px 0px 0px 1px;
	}
	.data-item-label {
		display: inline-block;
		font-weight: 500;
		font-size: 1.1rem;
		padding: .1rem 0;
		margin: .2rem 0;
		color: #000230;
	}
	.view-group-info {
		display: block;
		margin-bottom: .3rem;
	}
	.view-group-info label {
		display: block;
		font-weight: 500;
		font-size: .925rem;
		margin-bottom: 0;
		padding-bottom: 0;
		color: #000230;
	}
	.view-group-info span {
		padding: 0;
		margin: 0;
		color: #000230;
		font-size: 1rem;
	}
	.mleft {
		margin-left: 2rem;
	}
	.custom-form-1709 .data-viewer {
		width: 30%;
	}
	.custom-form-1709 .data-form {
		width: 70%;
		padding: 0 1rem;
	}


	.preview-card {
		border-radius: 4px;
		padding: .8rem 0;
		display: block;
		align-items: center;
		color: #000230;
	}
	.preview-blank {
		padding: 2rem;
		display: flex;
		align-items: center;
		justify-content: center;
		color: #6A86A0;
		font-size: 1.1rem;
		font-weight: 500;
		border: 2px dashed #8FA4B7;
		margin: 2px;
		border-radius: 2px;
	}
	.preview-main-text {
		padding: .2rem .2rem;
		font-size: 1.1rem;
		font-weight: 500;
		display: flex;
		justify-content: center;
	}
	.preview-danger-area {
		padding: .6rem .2rem;
		text-align: center;
		border-radius: 2px;
		color: #fff;
		background: rgb(245,41,82);
	}
	.preview-danger-area span:nth-child(1) {
		display: block;
		font-weight: 700;
	}
	.preview-danger-area span:nth-child(2) {
		display: block;
	}
	.mid-text {
		font-size: .950rem;
	}
	.mid-text span {
		font-weight: 500;
		text-transform: uppercase;
	}
	.preview-icon {
		display: flex;
		align-items: center;
		justify-content: center;
		color: #718da5;
		font-size: 1.2rem;
	}
	.preview-icon svg {
		width: 50px;
		height: 50px;
		stroke: 6px;
		padding: .1rem;
		background: #fff;
		border-radius: 50%;	
		stroke: #000230;
	}

</style>
<script>
	document.getElementById('cliente-data-list').addEventListener('click', e => {
		if (e.target && e.target.matches('[name=cliente-item]')) {
			// e.preventDefault()
			document.getElementsByName('cliente-item').forEach(el => {
				el.classList.remove('media-preview-on');
			})
			if (e.target.dataset.conducta === 'N') {
				e.target.classList.add('media-preview-on');
				// e.target.checked = true;
			}
		}
	})

</script>
