(function(){
	let app;
	let hash_id = 0;
	document.addEventListener('DOMContentLoaded', event => {
		app = new App();
		hash_id = document.getElementById('hash_id').value;
		Moras();
	})

	if (document.getElementById('form_mora_editar')) {
		document.getElementById('form_mora_editar').addEventListener('submit', async event => {
			try {
				event.preventDefault();
				let data = new FormData(event.target);
				let mora = new Mora();
				let modifica = await mora.Modificar(data);
				$('#md_mora_editar').modal('hide');
				if (modifica.status === 'OK') {
					not.Ok(modifica.mssg, '', 3000);
					Moras();
				}else{
					if(modifica.status === 'WARN') not.Aviso(modifica.mssg, '', 3000);
					else not.Error(modifica.mssg, '', 3000);
				}
			} catch(e) {
				// statements
				console.log(e);
			}
		})
	}

	const Moras = async function(){
		try {
			let credito = new Credito();
			let consulta = await credito.ObtenerMoras(hash_id);
			let html = '';
			if (consulta.status === 'OK') {
				if (consulta.result.length > 0) {
					consulta.result.forEach( function(element, index) {
						let opt = '';
						let estado = '';
						if (element.estado === 'PEN' || element.estado === 'POS') {
							opt = `
								<a href="#" name="mora_edita" class="btn btn-sm btn-outline-primary" data-id="${element.id}" data-dias="${element.dias}" 
								data-monto="${element.monto}">
									Editar 
									<i class="fe fe-edit-2"></i>
								</a> |
								<a href="#" name="mora_elimina" class="btn btn-outline-danger btn-sm" data-id="${element.id}">
									Elimina 
									<i class="fe fe-trash-2"></i>
								</a>
							`;
						}
						switch (element.estado) {
							case 'PEN':
								estado = '<span class="stag stag-mor-pen">Pendiente</span>';
								break;
							case 'POS':
								estado = '<span class="stag stag-mor-pos">Pospuesto</span>';
								break;
							case 'PAG':
								estado = '<span class="stag stag-mor-pag">Pagado</span>';
								break;
						}
						html += `
							<tr>
								<td>
									Cuota ${(element.orden > 9) ? element.orden : '0'+element.orden}
								</td>
								<td>${element.vence}</td>
								<td>${element.dias}</td>
								<td>S/ ${element.monto}</td>
								<td>${estado}</td>
								<td>
									${opt}
								</td>
							</tr>
						`;
					});
				}else{
					html = '<tr class="text-center"><td colspan="6">El credito no tiene registros de mora</td></tr>';
				}
			}else{
				html = '<tr class="text-center"><td colspan="6">'+consulta.mssg+'</td></tr>';
			}
			app.render('tb_pendiente', html);

			let edits = document.getElementsByName('mora_edita');
			let dels = document.getElementsByName('mora_elimina');
			edits.forEach( function(element2, index) {
				element2.addEventListener('click', async event => {
					try {
						if (event.target.dataset.id && event.target.dataset.dias && event.target.dataset.monto) {
							let form = document.getElementById('form_mora_editar');
							form.id.value = event.target.dataset.id;
							form.dias.value = event.target.dataset.dias;
							form.monto.value = event.target.dataset.monto;
							$('#md_mora_editar').modal('show');
						}else{
							not.Aviso('Error al obtener datos', '', 3000);
						}
					} catch(e) {
						not.Error('Error al procesar', '', 3000);
						console.log(e);
					}
				})
			});
			dels.forEach( function(element3, index) {
				element3.addEventListener('click', async event => {
					try {
						if (event.target.dataset.id) {
							if (confirm('Eliminar mora?')) {
								let mora = new Mora();
								let elimina = await mora.Eliminar(event.target.dataset.id);
								if (elimina.status === 'OK') {
									not.Ok(elimina.mssg, '', 3000);
									Moras();
								}else{
									if (elimina.status === 'WARN') not.Aviso('Error al procesar', '', 3000);
									else not.Error('Error al procesar', '', 3000);
								}
							}
						}else{
							not.Aviso('Mora no reconocida', '', 3000);
						}
					} catch(e) {
						not.Error('Error al procesar', '', 3000);
						console.log(e);
					}
				})
			});
		} catch(e) {
			console.log(e);
			app.render('tb_pendiente', '<tr class="text-center"><td colspan="6">Error al procesar</td></tr>');
		}
	}
})();