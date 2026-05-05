(function(d, w){
	var app;
	var soda;
	var global_data = [];
	w.onload = () => {
		app = new App();
		soda = new Soda();
		get_general_data()
		get_reporte_data()
		d.getElementById('btn-side-reload-data').addEventListener('click', get_general_data)
		d.getElementById('btn-rep-reload-data').addEventListener('click', () => {
			let radio = document.getElementsByName('repo-radio-option');
			radio.forEach((element, index) => {
				element.checked = false;
				if (index === 0) element.checked = true;
			})
			get_reporte_data('general');
		})
		d.addEventListener('change', function(event){
			if (event.target.value) data_decrease(event.target.value);
		})
	}

	async function get_general_data(){
		try {
			soda.loader('open', '#not-general');
			const req = await fetch( srv.url() + 'Absolver/service_dashboard_info' );
			const res = await req.json();
			soda.loader('close', '#not-general');

			app.render('cnt-activos', res.cantidad_activos);
			app.render('cnt-vencidos', res.cantidad_vencidos);
			app.render('cnt-cumplidos', res.cantidad_cumplidos);
			app.render('mv-registros', res.movimiento_registros);
			app.render('mv-prestado', res.movimiento_prestado);
			app.render('mv-recuperado', res.movimiento_recuperado);
			app.render('mv-diferencia', res.movimiento_diferencia);
		} catch(e) {
			soda.loader('close', '#not-general');
			console.log(e);
		}
	}

	async function get_reporte_data(){
		try {
			soda.loader('open', '#not-reporte');
			const req = await fetch( srv.url() + 'Absolver/service_reporte_procesado' );
			const res = await req.json();
			global_data = res;

			soda.loader('close', '#not-reporte');
			var redata = await data_decrease('general');			
		} catch(e) {
			// soda.loader('close', '#not-reporte');
			console.log(e);
		}
	}

	function init_table(){
    	const grid = new gridjs.Grid({
  			from: d.getElementById('repo-table'),
	    }).render(d.getElementById('test'));
	}


	function data_decrease(concept){
		var html = '<tr><td colspan="11" class="text-center">Sin Resultados</td></tr>';
		if (global_data.length > 0) {
			html = '';
			global_data.forEach(element => {
				switch (concept) {
					case 'general':
						html += `
							<tr class="${ element.class }">
								<td>${ element.estado }</td>
								<td>
									<a href="${element.url}">#${ element.serie }</a>
								</td>
								<td>${ element.orden }</td>
								<td>${ element.cliente }</td>
								<td>S/ ${ element.prestamo }</td>
								<td>S/ ${ element.interes }</td>
								<td>S/ ${ element.penalidad }</td>
								<td>${ element.vencimiento }</td>
								<td>S/ ${ element.mora }</td>
								<td>${ element.dias }</td>
								<td>S/ ${ element.prorrateo }</td>
							</tr>
						`;
						break;
					case 'cumplido':
						if (element.sub_estado === 'CPD') {
							html += `
								<tr class="${ element.class }">
									<td>${ element.estado }</td>
									<td>
										<a href="${element.url}">#${ element.serie }</a>
									</td>
									<td>${ element.orden }</td>
									<td>${ element.cliente }</td>
									<td>S/ ${ element.prestamo }</td>
									<td>S/ ${ element.interes }</td>
									<td>S/ ${ element.penalidad }</td>
									<td>${ element.vencimiento }</td>
									<td>S/ ${ element.mora }</td>
									<td>${ element.dias }</td>
									<td>S/ ${ element.prorrateo }</td>
								</tr>
							`;
						}
						break;
					case 'prox-remate':
						if (element.sub_estado === 'RPX') {
							html += `
								<tr class="${ element.class }">
									<td>${ element.estado }</td>
									<td>
										<a href="${element.url}">#${ element.serie }</a>
									</td>
									<td>${ element.orden }</td>
									<td>${ element.cliente }</td>
									<td>S/ ${ element.prestamo }</td>
									<td>S/ ${ element.interes }</td>
									<td>S/ ${ element.penalidad }</td>
									<td>${ element.vencimiento }</td>
									<td>S/ ${ element.mora }</td>
									<td>${ element.dias }</td>
									<td>S/ ${ element.prorrateo }</td>
								</tr>
							`;
						}
						break;
				}
				
			})			
		}
		setTimeout(function(){
			app.render('repo-data', html);
		}, 300);
	}

})(document, window);