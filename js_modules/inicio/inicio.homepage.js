(function(d){
    let app;
	let data_stash = [];
    d.addEventListener('DOMContentLoaded', async()=>{
        app = new App();
		
		general_get_data();
		await reporte_process_data();
		reporte_render_data('GENERAL');
    })
	d.addEventListener('change', event => {
		let ev = event.target;
		if (ev.matches('[name=repo-radio-option]')){
			reporte_render_data(ev.value);
		}
	})

    async function reporte_get_data(){
		try {
			const req = await fetch(srv.url()+'api-reporte/procesado/v1');
			const res = await req.json();
			return res;			
		} catch(e) {
			return [];
		}
	}
    async function reporte_process_data(){
		var html = '';
        let data_raw = await reporte_get_data();
		let html_general = '', html_vencido_hoy = '', html_proximo_remate = '';

		let styles = {
			'E': '',
			'CC': 'file-congelado',
			'V': 'file-vencido',
			'CU': 'file-cumplido'
		}
		data_raw.forEach(item => {
			let rowstyle = (item.vencido_hoy) ? styles['CU'] : styles[item.estado];
			html_general += `
				<tr class="${ rowstyle }">
					<td>${ item.estado }</td>
					<td>
						<a href="${item.url}">#${ item.serie }</a>
					</td>
					<td>${ item.orden }</td>
					<td>${ item.cliente }</td>
					<td>S/ ${ item.prestamo }</td>
					<td>S/ ${ item.interes }</td>
					<td>S/ ${ item.penalidad }</td>
					<td>${ item.vencimiento }</td>
					<td>S/ ${ item.mora }</td>
					<td>${ item.dias }</td>
					<td>S/ ${ item.prorrateo }</td>
				</tr>
			`;
			if (item.vencido_hoy) {
				html_vencido_hoy += `
				<tr class="${ rowstyle }">
					<td>${ item.estado }</td>
					<td>
						<a href="${item.url}">#${ item.serie }</a>
					</td>
					<td>${ item.orden }</td>
					<td>${ item.cliente }</td>
					<td>S/ ${ item.prestamo }</td>
					<td>S/ ${ item.interes }</td>
					<td>S/ ${ item.penalidad }</td>
					<td>${ item.vencimiento }</td>
					<td>S/ ${ item.mora }</td>
					<td>${ item.dias }</td>
					<td>S/ ${ item.prorrateo }</td>
				</tr>
				`;
			}
			if (item.cuotas_vencidas > 8) {
				html_proximo_remate += `
				<tr class="${ rowstyle }">
					<td>${ item.estado }</td>
					<td>
						<a href="${item.url}">#${ item.serie }</a>
					</td>
					<td>${ item.orden }</td>
					<td>${ item.cliente }</td>
					<td>S/ ${ item.prestamo }</td>
					<td>S/ ${ item.interes }</td>
					<td>S/ ${ item.penalidad }</td>
					<td>${ item.vencimiento }</td>
					<td>S/ ${ item.mora }</td>
					<td>${ item.dias }</td>
					<td>S/ ${ item.prorrateo }</td>
				</tr>
				`;
			}

		});
		data_stash = {
			'GENERAL': html_general,
			'VENCEHOY': html_vencido_hoy,
			'PROXREMATE': html_proximo_remate
		}
		
	}
	function reporte_render_data(option = 'general'){
		let data = data_stash[option];

		app.render('repo_data', `
			<tr>
				<td class="text-center" colspan="11" class="text-center">
					Cargando...
				</td>
			</tr>
		`)
		setTimeout(() => {
			if (data.length) {
				app.render('repo_data', data);
			} else{
				app.render('repo_data', `
					<tr>
						<td colspan="11" class="text-center">Sin Datos</td>
					</tr>
				`);
			}
		}, 100);
	}
	

	async function general_get_data(){
		try {
			const req = await fetch(srv.url()+'Reporte/service_dashboard_info');
			const res = await req.json();
			if (res && Object.keys(res).length > 0) {
				app.render('cnt-activos', res.cantidad_activos);
				app.render('cnt-vencidos', res.cantidad_vencidos);
				app.render('cnt-cumplidos', res.cantidad_cumplidos);
				app.render('mv-registros', res.movimiento_registros);
				app.render('mv-prestado', res.movimiento_prestado);
				app.render('mv-recuperado', res.movimiento_recuperado);
				app.render('mv-diferencia', res.movimiento_diferencia);
			}
		} catch (error) {
			
		}
	}



})(document);