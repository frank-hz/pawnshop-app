(function(){

    document.addEventListener('DOMContentLoaded', event => {

    	let links = document.getElementsByName('reporte_link');
    	if (links.length > 0) {
    		links.forEach(element => {
    			element.addEventListener('change', event => {
    				switch (event.target.value) {
    					case 'general':
    						$('#dset_reportegen').slideDown('fast');
    						$('#dset_vencedia').slideUp('fast');
    						break;
    					case 'vence_dia':
    						$('#dset_vencedia').slideDown('fast');
    						$('#dset_reportegen').slideUp('fast');
    						ObtenerReporte();
    						break;
    				}
    			})
    		})
    	}

    })


    async function ObtenerReporte(){
    	try {
    		let app = new App();
    		app.preload('open', 'Cargando..');
    		let request = await fetch(srv.url()+'Inicio/ServiceReporte');
    		let response = await request.json();
    		
    		let html = '';
    		if (response && response.length > 0) {
    			response.forEach(element => {
    				html += `
    				<tr style="background: #FCDF8E">
    					<td>${element.estado}</td>
    					<td>
    						<a href="${srv.url()}Credito/detalle/${element.credito_id}" target="_blank">
    							#${element.credito}
    						</a>
    					</td>
    					<td>${element.orden}</td>
    					<td>${element.cliente}</td>
    					<td>S/ ${element.prestamo}</td>
    					<td>S/ ${element.interes}</td>
    					<td>S/ ${element.penalidad}</td>
    					<td>${element.vencimiento}</td>
    					<td>S/ ${element.mora}</td>
    					<td>${element.dias}</td>
    					<td>S/ ${element.prorrateo}</td>
    				</tr>`
    			})
    		}else{
    			html = `
    			    <tr>
        			    <td colspan="11" class="text-center">Sin Resultados</td>
        			</tr>
    			`;
    		}
    		
    		app.render('dset_vencedia', html);
    		app.preload('close');
    	} catch(e) {
    		let app = new App();
    		app.preload('close');
    		console.log(e);
    	}
    }
    

})();