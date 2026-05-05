(function(d,w){
	const EXCEL_TYPE = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;charset=UTF-8';
	const EXCEL_EXTENSION = '.xlsx';
	let mdl_notacredito;
	let module;
	let grid_table;
	let  grid_config = {
		paginacion: {
			activo: true,
			cantidad: 15
		}
	}
	let data_export = [];
	let data_raw = [];
	let data_req = {
		tipo_busqueda: 'CODIGO',
		buscar: '',
		orden: {
			por: 'CODIGO',
			tipo: 'DESC'
		},
		estado: 'E',
		limite: 0,
		atraso: {
			valor: 0,
			simbolo: '>='
		},
		fechas: {
			inicio: '',
			final: ''
		}
	}
	let local = {
		notas_limit_view: 4
	}
	let modal_notas = {
		credito_current: 0
	}


    d.addEventListener('DOMContentLoaded', async () => { 
		module = new Module(); 
		mdl_notacredito = new NotasCredito();
		await render_data_creditos()		
	})
	d.addEventListener('submit', (event) => {
		event.preventDefault()
		let ev = event.target;

		if (ev && ev.id === 'nota-registro-form') {			
			let data = {
				credito: ev.credito.value,
				descripcion: ev.descripcion.value,
				tipo: ev.nota_tipo.value
			}
			if (nota__validate_insert(data)) {
				nota__save(data)
			}else {
				not.Aviso('Error al procesar', '', 2200);
			}
		}
		if (ev && ev.id === 'fechas-selector-form') {
			if (ev.rango_1.value && ev.rango_2.value) {
				data_req.fechas.inicio = ev.rango_1.value;
				data_req.fechas.final = ev.rango_2.value;
				d.getElementById('datepicker-fecha').value = `${data_req.fechas.inicio} - ${data_req.fechas.final}`
				$('#modal-fecha-selector').modal('hide');
				console.log(data_req)
				ev.reset();
				render_data_creditos(true)

			}else {
				not.Aviso('Datos incompletos', '', 2000);
			}
		}
	})
	d.addEventListener('click', event => {
		let ev = event.target;
		if (ev && ev.id === 'btn-data-filter-reset') {
			data_req.tipo_busqueda = '';
			data_req.buscar = '';
			data_req.orden.por = 'SERIE';
			data_req.orden.tipo = 'DESC';
			data_req.limite = 50;
			data_req.atraso.valor = 0;
			data_req.atraso.simbolo = '';
			data_req.fecha_registro = '';
			d.getElementById('select-tipo-busqueda').value = '';
			d.getElementById('datepicker-fecha').value = '';
			d.getElementById('select-atraso-simbolo').value = '';
			d.getElementById('input-atraso-valor').value = '';
			d.getElementById('select-estado').value = '';
			d.getElementById('select-orden').value = '';
			d.getElementById('input-buscar').value = '';

			render_data_creditos(true)
		}
		if (ev && event.target.id === 'btn-export-excel') {
			var worksheet = XLSX.utils.json_to_sheet(data_export);
			var workbook = {
				Sheets: {
					'data': worksheet
				},
				SheetNames: ['data']
			};
			var excelBuffer = XLSX.write(workbook, {bookType: 'xlsx', type: 'array'})
			excel_export_file(excelBuffer, 'Electrocash - Lista de Creditos')
			
		}
		if (ev && ev.matches('[name=tabb-item]')) {
			tabb_content(ev)
		}
		if (ev && ev.matches('[name=credito-notas-item]')) {
			if (ev.dataset.credito && ev.dataset.serie) {
				
				nota__modal_reset();
				let title_link = d.getElementById('nota-title-credito-link'); 
				title_link.innerHTML = `[${ev.dataset.serie}]`;
				title_link.href = srv.url()+'Credito/detalle/'+ev.dataset.credito;
				d.getElementById('nota-credito-id').value = ev.dataset.credito;

				$('#modal-notas').modal('show');
				tabb_content('tabb-item-lista');
				nota__get_all(ev.dataset.credito)
			}
		}
		if (ev && ev.matches('[name=nota-delete-item]')) {
			if (ev.dataset.id && ev.dataset.credito) {
				if (confirm('¿Eliminar Nota?')) {
					nota__delete(ev.dataset.id, ev.dataset.credito)
				}
			}
		}
		if (ev && ev.id === 'datepicker-fecha') {
			$('#modal-fecha-selector').modal('show');
		}
		if (ev && ev.id === 'btn-config') {
			$('#modal-config').modal('show');
		}
		if (ev && ev.id === 'modal_nota_recargar') {
			local.notas_limit_view = 4;
			nota__get_all(modal_notas.credito_current);
			console.log(modal_notas)
		}
		if (ev && ev.id === 'modal_nota_salir') {
			local.notas_limit_view = 4;
			modal_notas.credito_current = 0;
			console.log(modal_notas)
		}
		if (ev && ev.id === 'notas_vtodo') {
			local.notas_limit_view = 0;
			nota__get_all(modal_notas.credito_current);
		}
		
	})
	d.addEventListener('change', event => {
		let ev = event.target;
		if (ev && ev.id === 'select-tipo-busqueda') {
			data_req.tipo_busqueda = ev.value;
			if (data_req.buscar) {
				render_data_creditos(true);
			}
		}
		if (ev && ev.id === 'select-atraso-simbolo') {
			data_req.atraso.simbolo = ev.value;
			if (data_req.atraso.valor) {
				render_data_creditos(true);
			}
		}
		if (ev && ev.id === 'select-estado') {
			data_req.estado = ev.value;
			// if (['R', 'CC'].includes(ev.value)) {
			// 	grid_table.updateConfig({
			// 		columns: [
			// 			"#", "Codigo", "Fecha", "Cliente", "Monto", 
			// 			"Cuotas Venc.", "Prenda", "Estado", "Opcion"
			// 		]
			// 	}).forceRender();
			// }
			render_data_creditos(true);
		}
		if (ev && ev.id === 'select-orden-por') {
			data_req.orden.por = ev.value;
			render_data_creditos(true);
		}
		if (ev && ev.id === 'select-orden-tipo') {
			data_req.orden.tipo = ev.value;
			render_data_creditos(true);
		}
		if (ev && ev.name === 'option-pagination-item') {
			if (ev.value === 'SI') {
				grid_table.updateConfig({
					pagination: {
						limit: grid_config.paginacion.cantidad
					}
				}).forceRender();
				d.getElementById('option_cantidad_area').style.display = 'block';
			}else if(ev.value === 'NO') {
				grid_table.updateConfig({
					pagination: false
				}).forceRender();
				d.getElementById('option_cantidad_area').style.display = 'none';
			}
		}

	})
	d.addEventListener('keyup', event => {
		let ev = event.target;
		if (ev && ev.id === 'input-buscar') {
			data_req.buscar = ev.value;
			render_data_creditos(true);
		}
		if (ev && ev.id === 'input-atraso-valor') {
			data_req.atraso.valor = ev.value;
			if (data_req.atraso.simbolo) {
				render_data_creditos(true);
			}			
		}
		if (ev && ev.id === 'option-cantidad') {
			let val = parseInt(ev.value)
			if (typeof val === 'number' && val > 0) {
				grid_config.paginacion.cantidad = ev.value;
				grid_table.updateConfig({
					pagination: {
						limit: grid_config.paginacion.cantidad
					}
				}).forceRender();
			}
		}
		
	})

	async function get_data_creditos(){
		try {     
            let req = await fetch(srv.url()+'Credito/service_get_all', {
                method: 'POST',
                body: JSON.stringify(data_req),
                headers: {
                    'Content-Type': 'application/json'
                }
            });
            let res = await req.json();
            data_raw = [];
			data_export = [];
			res = res.data;
			if (res.length > 0) {
				res.forEach((e, ind) => {
					let estado_raw = e.estado;
					// if (e.estado === 'E' && e.cuota_cumplida > 0) {
					// 	estado_raw = 'V';
					// }
					let estadoTag = define_tag_estado(estado_raw);
					// let estadoTag = define_tag_estado(e.estado);
					let action_controls = eval("gridjs.html(`<div class='d-flex'><a href='${srv.url()}Credito/detalle/${e.id}' class='enc2454-btn-option'><i class='fe fe-layers'></i></a><button name='credito-notas-item' class='enc2454-btn-option' data-credito='${e.id}' data-serie='${e.serie}'><i class='fe fe-file-plus'></i> Notas</button></div>`)");
					let prenda_link = (parseInt(e.prenda_id) > 0) 
						? eval("gridjs.html(`<a class='form-link' href='"+srv.url()+"Prenda/detalle/"+e.prenda_id+"'>"+e.prenda_descripcion+"</a>`)")
                     	: 'Sin Datos';

					data_raw.push([
						ind+1,
						eval("gridjs.html(`<a class='form-link' href='${srv.url()}Credito/detalle/${e.id}'>${e.serie}</a>`)"),
						e.creacion,
						eval("gridjs.html(`<a class='form-link' href='${srv.url()}Cliente/detalle/${e.cliente_id}'>${e.cliente_nombre}</a>`)"),
						'S/ '+e.monto,
						e.cuotas_vencidas,
						prenda_link,
						estadoTag,
						action_controls
					]);	

					data_export.push({
						"Credito": e.serie,
						"Registro": e.creacion,
						"Cliente": e.cliente_nombre,
						"Prenda": e.prenda_descripcion,
						"Monto": e.monto,
						"Cuotas": e.cuotas_vencidas,
						"Estado": e.estado,
						"Liquidacion": e.liquidacion
					});
				});
			}
        } catch (error) {
			console.log(error)
            return [];
        }
	}
	function define_tag_estado(estado_){
		let tag = '';
		switch (estado_) {
			case 'E':
				tag = "gridjs.html(`<div class='stag stag-cred-E'>En Espera</div>`)";
				break;
			case 'R':
				tag = "gridjs.html(`<div class='stag stag-cred-R'>Recuperado</div>`)";
				break;
			case 'AN':
				tag = "gridjs.html(`<div class='stag stag-cred-AN'>Anulado</div>`)";
				break;
			case 'A':
				tag = "gridjs.html(`<div class='stag stag-cred-A'>Remate</div>`)";
				break;
			case 'CC':
				tag = "gridjs.html(`<div class='stag stag-cred-CC'>Congelado</div>`)";
				break;
			case 'V':
				tag = "gridjs.html(`<div class='stag stag-cred-V'>Vencido</div>`)";
				break;
		}
		return eval(tag);
	}    
    async function render_data_creditos(upd = false){
		module.loader('credito_modulo_loader','open')
		await get_data_creditos();
		let table_area = d.getElementById("credito_data_table");
		if (upd) {
			grid_table.updateConfig({
				data: data_raw
			}).forceRender();
		}else {
			grid_table = new gridjs.Grid({
				columns: [
					"#", "Codigo", "Fecha", "Cliente", "Monto", 
					"Cuotas Venc.", "Prenda", "Estado", "Opcion"
				],
				sort: false,
				autoWidth: true,
				pagination: {
					limit: grid_config.paginacion.cantidad
				},
				fixedHeader: true,
				language: {
					'search': {
					'placeholder': 'Buscar...'
					},
					'pagination': {
					'previous': 'Ant.',
					'next': 'Sig.',
					'showing': 'mostrando',
					'results': () => 'registros',
					'of': 'de',
					'to': 'a',
					},
					'loading': 'Cargando...',
					'noRecordsFound': 'No se encontraron registros',
				},
				style: {
					table: {
						'box-shadow': 'none',
						'border-radius': 'none',
						'width': '100%',
						'border': 'none'
					},
					tbody: {
						'background': 'blue'
					},
					th: {
						'max-width': '100%',
						'font-size': '.92rem',
						'font-weight': '600',
						'padding': '6px 10px',
						'word-wrap': 'break-all',
						'color': '#0f1f31'
					},
					td: {
						'box-shadow': 'none',
						'font-size': '.94rem',
						'padding': '6px 8px',
						'color': '#0e121a'
					}
				},
				data: data_raw
			}).render(table_area);
		}
		module.loader('credito_modulo_loader','close')
	}

	// Notas
	async function nota__get_all(id){
		try {
			// const req = await fetch(srv.url()+'NotaCredito/ServiceConsulta', {
			// 	method: 'POST',
			// 	body: JSON.stringify({id: id}),
			// 	headers: {
			// 		'Content-Type': 'application/json'
			// 	}
			// })
			// const res = await req.json();

			modal_notas.credito_current = id;
			let req = await mdl_notacredito.get_by_credito(id, {
				limit: 0,
				order_type: 'DESC'
			});
			let data = req.result;
			
			let html_resp = '';
			let limit_view = (local.notas_limit_view > 0) ? local.notas_limit_view : data.length;
			if (data.length > 0) {
				data.forEach((e, index) => {
					if ((index+1) <= limit_view) {
						html_resp += `
							<tr>
								<td>${index+1}</td>
								<td>${e.descripcion}</td>
								<td>${ e.tipo === 'SI' ? 'Anclado' : 'Normal'}</td>
								<td>${e.fecha}</td>
								<td class="text-center">
									<button class="btn btn-sm btn-danger" name="nota-delete-item" data-id="${e.id}" data-credito="${e.credito_id}">Eliminar</button>
								</td>
							</tr>
						`;
					}	
				})
				if (limit_view < data.length) {
					html_resp += `
					<tr>
						<td class="text-center" colspan="5">
							<a id="notas_vtodo" href="javascript:void(0)" class="btn btn-sm btn-outline-primary">
								Ver Todo
							</a>
						</td>
					</tr>
					`;
				}
			}else{
				html_resp = `
					<tr>
						<td colspan='5' class='text-center'>
							<i class="fe fe-bar-chart-2"></i>
							Sin Datos
						</td>
					</tr>
				`;
			}

			d.getElementById('notas-dataset').innerHTML = html_resp;			
		} catch (error) {
			console.log(error)
			d.getElementById('notas-dataset').innerHTML = `
				<tr>
					<td colspan='5' class='text-center'>
						<i class="fe fe-bar-chart-2"></i>
						Error al Procesar
					</td>
				</tr>
			`;
		}
	}
	function tabb_content(el){
		let elem = (typeof el === 'object') ? el : d.getElementById(el);
		let items = d.getElementsByName('tabb-item');
		let contents = d.getElementsByName('tabb-pane');

		items.forEach(e => {
			e.classList.remove('tabb-active')
		})
		contents.forEach(e => {
			e.style.display = 'none';
		})
		elem.classList.add('tabb-active');
		let target = d.getElementById(elem.dataset.target);
		target.style.display = 'block';

	}
	function nota__modal_reset(){
		let title_link = d.getElementById('nota-title-credito-link'); 
		let form = d.getElementById('nota-registro-form');
		title_link.innerHTML = `[]`;
		title_link.href = 'javascript:void(0)';
		form.reset();
		tabb_content('tabb-item-lista')
	}
	
	function nota__validate_insert(data){
		let estado = true;
		d.getElementById('nota-tipo-feed').innerHTML = '';
		d.getElementById('nota-descripcion-feed').innerHTML = '';
		if (!data.credito) {
			not.Aviso('Credito no identificado', '', 2200);
			estado = false;
		}
		if (!data.tipo) {
			d.getElementById('nota-tipo-feed').innerHTML = 'Campo requerido';
			estado = false;
		}
		if (!data.descripcion) {
			d.getElementById('nota-descripcion-feed').innerHTML = 'Campo requerido';
			estado = false;
		}
		return estado;
	}
	async function nota__save(data){
		const req = await fetch(srv.url()+'NotaCredito/service_registro', {
			method: 'POST',
			body: JSON.stringify(data)
		})
		const res = await req.json();
		if (Object.keys(res)) {
			if (res.code === 'OK') {
				not.Ok(res.message, '', 2200);
				let form = d.getElementById('nota-registro-form');
				form.reset();
				nota__get_all(data.credito)
				tabb_content('tabb-item-lista')
			}else if(res.code === 'WARN'){
				not.Aviso(res.message, '', 2200);
			}
		}
	} 
	async function nota__delete(id, idcredito){
		let data = new FormData();
		data.append('id', id);
		const req = await fetch(srv.url()+'NotaCredito/eliminar', {
			method: 'POST',
			body: data 
		})
		const res = await req.json();
		if (Object.keys(res)) {
			if (res.status === 'OK') {
				not.Ok(res.message, '', 2200);				
			}else if(res.status === 'WARN'){
				not.Aviso(res.message, '', 2200);
			}
			nota__get_all(idcredito)
		}
	} 

	// Util

	function excel_export_file(buffer, filename) {
		var data = new Blob([buffer], {type: EXCEL_TYPE});
		// saveAs(data,filename+'_export_'+new Date().getTime()+EXCEL_EXTENSION);
		saveAs(data,filename+'_'+EXCEL_EXTENSION);
	}
	function date_format_send(date) {
		var d = new Date(date),
			month = '' + (d.getMonth() + 1),
			day = '' + d.getDate(),
			year = d.getFullYear();
	
		if (month.length < 2) 
			month = '0' + month;
		if (day.length < 2) 
			day = '0' + day;
	
		return [month, day, year].join('-');
	}
})(document, window);