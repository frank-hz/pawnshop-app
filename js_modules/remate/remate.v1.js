// "use strict"
// import app from "./app_core.js";
(function(d){
	let app;
	var grid_table;
	var data_raw = [];
	var request_config = {
		type: 'ALL',
		value: '',
		sucursal: 0
	}
	var prenda_object = {
		id: 0,
		edition: false
	}
	d.addEventListener('DOMContentLoaded', async () => { 
		app = new App();
		await render_sucursal()
		await render_tabler_values()
		await init_data()
		// resize_datatable_content()

		// modal('modal-prenda-detalle', 'open')

		// if (ResizeObserver) {
		// 	const obs = new ResizeObserver(ent => {
		// 		var el = document.getElementById('datatable-content');
		// 		var maxh = parseInt(el.clientHeight);
		// 		var maxw = parseInt(el.clientWidth);
		// 		var new_maxh = maxh - 54;
		// 		var new_maxw = maxw - 10;
		// 		d.getElementById('datatable-content').style.maxHeight = new_maxh+'px';
		// 	})
		// 	obs.observe(document.getElementById('datatable-content'))
		// }
	})

	// Resize Observer
    
	d.addEventListener('click', async (event) => {
		if (event.target && event.target.name === 'prenda-open-detalle') {
			if (event.target.dataset.id) {
				prenda_object.edition = false;
				edition_mode()
				await get_prenda_detail(event.target.dataset.id)
			}
		}
		if (event.target && event.target.matches('[name=modal-close]')) {
			if (event.target.dataset.obj) {
				modal(event.target.dataset.obj, 'close');				
			}
		}
		if (event.target && event.target.matches('.popup-close')) {
			popup_alert(false, 'close');
		}
		if (event.target && event.target.id === 'btn_edit_enable') {
			prenda_object.edition = true;
			edition_mode()
		}
		if (event.target && event.target.id === 'btn_edit_cancel') {
			prenda_object.edition = false;
			edition_mode()
		}
		if (event.target && event.target.id === 'btn_edit_save') {
			var val = edition_validate();
			if (val.code) {
				var form = document.getElementById('prenda-detalle-form');
				const send = await fetch(srv.url() + 'Remadev/service_prenda_update', {
					method: 'POST',
					body: JSON.stringify(val.data),
					headers: {
						'Content-Type': 'application/json'
					}
				})
				const res = await send.json();
				prenda_object.edition = false;
				edition_mode()
				if (res.code === 'OK') {
					popup_alert(true, res.message, 3000);
				}else {
					popup_alert(true, 'No se completo la actualizacion.', 3000);
				}

			}
		}
	});
	d.addEventListener('change', async event => {
		if (event.target && event.target.matches('#params-select-tipo')) {
			if (event.target.value.length > 0) {
				request_config.type = event.target.value;
				var section_input = d.getElementById('params-section-input')
				var section_radio = d.getElementById('params-section-radio')
				
				section_radio.reset();
				d.getElementById('params-input-search').value = '';

				if (event.target.value === 'TIPO') {
					section_input.classList.add('d-none')
					section_radio.classList.remove('d-none')
					render_tipo_items()
				} else if(event.target.value === 'ESTADO') {
					section_input.classList.add('d-none')
					section_radio.classList.remove('d-none')
					render_estado_items()
				} else if(event.target.value === 'ALL'){
					section_input.classList.add('d-none')
					section_radio.classList.add('d-none')
				} else {
					section_input.classList.remove('d-none')
					section_radio.classList.add('d-none')
				}
			}
		}
		if (event.target && event.target.matches('#params-select-sucursal')) {
			if (event.target.value.length > 0) {
				request_config.sucursal = event.target.value;
				await get_data();
				table_loading_screen('close')
				grid_table.updateConfig({
					data: data_raw
				}).forceRender();
			}
		}
		if (event.target && event.target.name === 'rad-group-tipo') {
			if (event.target.value.length > 0) {
				request_config.value = event.target.value
				await get_data();
				table_loading_screen('close')
				grid_table.updateConfig({
					data: data_raw
				}).forceRender();
			}
		}
		if (event.target && event.target.name === 'rad-group-estado') {
			if (event.target.value.length > 0) {
				request_config.value = event.target.value
				await get_data();
				table_loading_screen('close')
				grid_table.updateConfig({
					data: data_raw
				}).forceRender();
			}
		}
	});
	d.addEventListener('keyup', async event => {
		if (event.target && event.target.matches('#params-input-search')) {
			if (event.target.value.length > 0) {
				table_loading_screen('open')
				request_config.value = event.target.value;
				await get_data();
				table_loading_screen('close')
				grid_table.updateConfig({
					data: data_raw
				}).forceRender();
			}			
		}
		if (event.target && event.target.id === 'prenda_reparacion') {
			var in_value = event.target.value;
			var inp_costo_total = d.getElementById('prenda_costo_total');
			var inp_precio_inicial = d.getElementById('prenda_precio_inicial');
			var ntotal = 0;
			if (in_value.length > 0) {
				ntotal = parseFloat(inp_precio_inicial.value) + parseFloat(in_value);					
			}else {
				ntotal = parseFloat(inp_precio_inicial.value) + parseFloat(event.target.dataset.basevalue);
			}
			inp_costo_total.value = parseFloat(ntotal);
		}
	});
	

	async function get_prenda_detail(id){
		var req = await fetch(srv.url() + 'Remadev/service_prenda_detalle', {
			method: 'POST',
			body: JSON.stringify({id: id}),
			headers: {
				'Content-Type': 'application/json'
			}
		})
		var res = await req.json();
		if (res){
			prenda_object.id = res.id;
			d.getElementById('prenda_cliente_value').innerHTML = res.cliente;
			d.getElementById('prenda_cliente_value').href = srv.url()+'cliente/detalle/'+res.cliente_id;
			d.getElementById('prenda_credito_value').innerHTML = res.credito_serie;
			d.getElementById('prenda_credito_value').href = srv.url()+'cliente/detalle/'+res.credito_id;
			d.getElementById('prenda_descrip_value').innerHTML = res.descripcion;
			d.getElementById('prenda_tipo_value').innerHTML = res.tipo;
			d.getElementById('prenda_categoria_value').innerHTML = res.categoria;
			d.getElementById('prenda_serial_value').innerHTML = res.serial;
			d.getElementById('prenda_modelo_value').innerHTML = res.modelo;
			d.getElementById('prenda_comentario_value').innerHTML = res.comentario;
			d.getElementById('prenda_observacion_value').innerHTML = res.observacion;
			
			var etag = d.getElementById('prenda_estado_tag'); 
			if (res.estado === 'V') {
				etag.innerHTML = 'Vendido';
				etag.className = 'tag tag-vendido';
			} else if(res.estado === 'A'){
				etag.innerHTML = 'Disponible';
				etag.className = 'tag tag-disponible';
			}
			var form = d.getElementById('prenda-detalle-form');
			form.prenda_precio_inicial.value = (res.precio_inicial);
			form.prenda_precio_inicial.dataset.basevalue = parseFloat(res.precio_inicial);
			
			form.prenda_costo_total.value = parseFloat(res.costo_total);
			form.prenda_costo_total.dataset.basevalue = parseFloat(res.costo_total);

			form.prenda_reparacion.value = parseFloat(res.costo_reparacion);
			form.prenda_reparacion.dataset.basevalue = parseFloat(res.costo_reparacion);

			form.prenda_precio_venta.value = parseFloat(res.precio_venta);
			form.prenda_precio_venta.dataset.basevalue = parseFloat(res.precio_venta);

			
			// d.getElementById('prenda_costo_total_value').value = res.costo_total;
			// d.getElementById('prenda_reparacion').value = res.costo_reparacion;
			// d.getElementById('prenda_precio_venta_value').value = res.precio_venta;
		}

		modal('modal-prenda-detalle', 'open')
	}
	async function render_tipo_items(){
		var section_radio = d.getElementById('params-section-radio')
		const req = await fetch(srv.url()+'Remadev/service_tipos_prendas')
		const res = await req.json();
		let html = '<div class="no-data-tag">SIN DATA</div>';
		if (res.length > 0) {
			html = '<div class="radio-list">';
			res.forEach(e => {
				html += `
					<div class="radio-item">
						<input id="rad-btn-${e.value}" name="rad-group-tipo" type="radio" value="${e.value}">
						<label for="rad-btn-${e.value}">
							<span class="radio-icon"></span>
							${e.value}
						</label>								
					</div>
				`;
			})
			html += '</div>';
		}
		section_radio.innerHTML = html;
	}
	function render_estado_items(){
		var section_radio = d.getElementById('params-section-radio')
		section_radio.innerHTML = `
			<div class="radio-list">
				<div class="radio-item">
					<input id="rad-btn-disponible" name="rad-group-estado" type="radio" value="A">
					<label for="rad-btn-disponible">
						<span class="radio-icon"></span>
						DISPONIBLE
					</label>
					
				</div>
				<div class="radio-item">
					<input id="rad-btn-vendido" name="rad-group-estado" type="radio" value="V">
					<label for="rad-btn-vendido">
						<span class="radio-icon"></span>
						VENDIDO
					</label>
				</div>
			</div>
		`;
	}
	async function render_tabler_values(){
		try {
			const req = await fetch(srv.url()+'Remadev/tabler_data', {
				method: 'POST',
				body: JSON.stringify({'sucursal': request_config.sucursal}),
				headers: {
					'Content-Type': 'application/json'
				}
			})
			const res = await req.json();
			d.getElementById('tabler-prendas-totales').innerHTML = res.cantidad_total;
			d.getElementById('tabler-prendas-disponibles').innerHTML = res.cantidad_disponible;
			d.getElementById('tabler-prendas-vendidas').innerHTML = res.cantidad_vendido;
		} catch (error) {
			console.log('ERROR[render_tabler]')
		}

	}
	async function render_sucursal(){
		let data = new FormData();
		data.append('tipo', 'breve');
		const req = await fetch(srv.url()+'Sucursal/consulta', {
			method: 'POST',
			body: data
		});
		const res = await req.json();
		if (res) {
			let html = '';
			res.forEach(e => {
				if (e.estado === 'A') {
					request_config.sucursal = e.id;
					// d.getElementById('filter-sucursal').value = e.id;
				}
				html += `
					<option value="${e.id}">
						${e.descripcion + ', '+e.ciudad}
					</option>
				`;
			})
			d.getElementById('params-select-sucursal').innerHTML = html;
		}
	}
	async function get_data(){
		try {     
            let req = await fetch(srv.url()+'Remadev/service_get_all', {
                method: 'POST',
                body: JSON.stringify(request_config),
                headers: {
                    'Content-Type': 'application/json'
                }
            });
            let res = await req.json();
            data_raw = [];
			if (res && res.length > 0) {
				res.forEach(e => {  
					let estadoTag = '';
					let action_controls = '';
					if (e.estado === 'V') {
						estadoTag = eval("gridjs.html(`<div class='tag tag-vendido'>Vendido</div>`)");
					} else if(e.estado === 'A'){
						estadoTag = eval("gridjs.html(`<div class='tag tag-disponible'>Disponible</div>`)");

						// action_controls = eval("gridjs.html(`<div class='table-options'><a href='javascript:void(0)' class='table-link' name='prenda-open-detalle' data-id='"+e.id+"'>Ver Detalle</a></div>`)")

						// action_controls = eval("gridjs.html(`<div class='table-options'><button class='btn-detail'>"+icons_stash.layers+"</button></div>`)")

						action_controls = eval("gridjs.html(`<div class='table-options'><button name='prenda-open-detalle' data-id='"+e.id+"' class='btn-detail'><i class='fi fi-br-layers'></i></button></div>`)")
					}
					data_raw.push([
						eval("gridjs.html(`<a class='form-link' href='"+srv.url()+"Credito/detalle"+e.credito_id+"'>"+e.credito_serie+"</a>`)"),
						e.descripcion,
						e.tipo,
						e.modelo,
						e.serial,
						e.liquidacion,
						'S/ '+e.costo_total,
						'S/ '+e.precio_venta,
						estadoTag,
						action_controls
					]);	
				});
			}
        } catch (error) {
            return [];
        }
	}
	async function init_data(){
		await get_data();
		grid_table = new gridjs.Grid({
			columns: [
				"Credito", "Descripcion", "Tipo", "Modelo", "Serie", 
				"Liquidacion", "Costo Total", "Precio Venta", "Estado",
				"Accion"
			],
			// search: true,
			sort: true,
            autoWidth: true,
			pagination: {
                limit: 13
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
					'font-size': '.94rem',
					'font-weight': '600',
                    'padding': '6px 10px',
                    'word-wrap': 'break-all',
					'color': '#0F1F31'
                },
                td: {
                    'box-shadow': 'none',
					'font-size': '.96rem',
					'padding': '6px 8px',
					'color': '#0F1F31'
                }
            },
			data: data_raw
		}).render(document.getElementById("data-area"));
	}
	function table_loading_screen(action = 'open'){
		var screen = d.getElementById('table-loading-screen')
		if (action === 'open') {
			screen.classList.remove('d-none');
		} else if(action === 'close'){
			screen.classList.add('d-none');
		}
	}
	function resize_datatable_content(){
		var el = document.getElementById('datatable-content');
		var maxh = parseInt(el.clientHeight);
		var maxw = parseInt(el.clientWidth);
		var new_maxh = maxh - 54;
		var new_maxw = maxw - 10;
		d.getElementById('datatable').style.maxHeight = new_maxh+'px';
	}
	function modal(id, action){
		var modal = (typeof id === 'object') ? id : document.getElementById(id);
		if (action === 'open') {
			// document.body.style.overflow = 'hidden';
			modal.classList.add('modal-show')
		} else if(action === 'close') {
			// document.body.style.overflow = 'auto';
			modal.classList.remove('modal-show')
		}
	}

	// 
	function edition_mode(){
		var state = prenda_object.edition; 
		var btn_cancel = d.getElementById('btn_edit_cancel');
		var btn_save = d.getElementById('btn_edit_save');
		var btn_edit_enable = d.getElementById('btn_edit_enable');
		var btn_venta_add = d.getElementById('btn_venta_add');
		var input_reparacion = d.getElementById('prenda_reparacion');
		var input_precio_venta = d.getElementById('prenda_precio_venta');

		if (state) {
			btn_cancel.classList.remove('d-none');
			btn_save.classList.remove('d-none');
			input_reparacion.removeAttribute('readonly');
			input_precio_venta.removeAttribute('readonly');
			btn_edit_enable.setAttribute('disabled','disabled');
			btn_venta_add.setAttribute('disabled','disabled');
		}else {
			reset_prenda_detail()
			btn_cancel.classList.add('d-none');
			btn_save.classList.add('d-none');
			input_reparacion.setAttribute('readonly', 'readonly');
			input_precio_venta.setAttribute('readonly', 'readonly');
			btn_edit_enable.removeAttribute('disabled');
			btn_venta_add.removeAttribute('disabled');
		}		
	}
	function edition_validate(){
		var response = {
			code: true,
			data: {}
		};
		var form = d.getElementById('prenda-detalle-form');
		if (form.prenda_costo_total.value.length <= 0) {
			d.getElementById('feed_costo_total').innerHTML = 'dato requerido';
			code = false;
		}else {
			response.data.costo_total = form.prenda_costo_total.value;
		}

		if (form.prenda_precio_venta.value.length <= 0) {
			d.getElementById('feed_precio_venta').innerHTML = 'dato requerido';
			code = false;
		}else {
			response.data.precio_venta = form.prenda_precio_venta.value;
		}

		if (form.prenda_reparacion.value.length <= 0) {
			d.getElementById('feed_reparacion').innerHTML = 'dato requerido';
			code = false;
		}else {
			response.data.reparacion = form.prenda_reparacion.value;
		}

		if (!prenda_object.id || prenda_object.id <= 0) {
			code = false;
			modal_message_feed('warn', 'Prenda no Identificada')		
		}else {
			response.data.id = prenda_object.id;
		}
		
		return response;
	}
	function reset_prenda_detail(){
		var form = d.getElementById('prenda-detalle-form');
		form.prenda_precio_inicial.value = form.prenda_precio_inicial.dataset.basevalue;
		form.prenda_costo_total.value = form.prenda_costo_total.dataset.basevalue;
		form.prenda_reparacion.value = form.prenda_reparacion.dataset.basevalue;
		form.prenda_precio_venta.value = form.prenda_precio_venta.dataset.basevalue; 
	}
	function clear_prenda_detail(){
		prenda_object.id = 0;
		d.getElementById('prenda_cliente_value').innerHTML = '';
		d.getElementById('prenda_cliente_value').href = '';
		d.getElementById('prenda_credito_value').innerHTML = '';
		d.getElementById('prenda_credito_value').href = '';
		d.getElementById('prenda_descrip_value').innerHTML = '';
		d.getElementById('prenda_tipo_value').innerHTML = '';
		d.getElementById('prenda_categoria_value').innerHTML = '';
		d.getElementById('prenda_serial_value').innerHTML = '';
		d.getElementById('prenda_modelo_value').innerHTML = '';
		d.getElementById('prenda_comentario_value').innerHTML = '';
		d.getElementById('prenda_observacion_value').innerHTML = '';

		var form = d.getElementById('prenda-detalle-form');
		form.prenda_precio_inicial.value = 0;
		form.prenda_precio_inicial.dataset.basevalue = 0;
		
		form.prenda_costo_total.value = 0;
		form.prenda_costo_total.dataset.basevalue = 0;

		form.prenda_reparacion.value = 0;
		form.prenda_reparacion.dataset.basevalue = 0;

		form.prenda_precio_venta.value = 0;
		form.prenda_precio_venta.dataset.basevalue = 0;

	}
	function modal_message_alert(mode, val, t = 0){
		var el = d.getElementById('modal-message-general');
		var mssg_css = '';
		switch (mode) {
			case 'error':
				mssg_css = 'message-danger';
				break;
			case 'warn': 
				mssg_css = 'message-warning';
				break;
			case 'success': 
				mssg_css = 'message-success';
				break;
			default:

				break;
		}
		el.innerHTML = `
			<div class="modal-message ${mssg_css}">
				<div class="modal-message-icon">
					<svg width="512px" height="512px" viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg"><title>ionicons-v5-a</title><path d="M256,48C141.31,48,48,141.31,48,256s93.31,208,208,208,208-93.31,208-208S370.69,48,256,48Zm0,319.91a20,20,0,1,1,20-20A20,20,0,0,1,256,367.91Zm21.72-201.15-5.74,122a16,16,0,0,1-32,0l-5.74-121.94v-.05a21.74,21.74,0,1,1,43.44,0Z"/></svg>
				</div>
				<div class="modal-message-content">
					${val}
				</div>
			</div>
		`;
		el.classList.remove('d-none')
		if (t > 0) {
			setTimeout(() => {
				el.innerHTML = '';
				el.classList.add('d-none')
			}, t)
		}
	}
	function popup_alert(action, mssg, t = 0){
		var popup = document.getElementById('popup');
		var mssg_content = popup.querySelector('.popup-message');
		if (action) {
			mssg_content.innerHTML = mssg;
			popup.style.top = '30px';
		}else {
			mssg_content.innerHTML = '';
			popup.style.top = '-250px';
		}

		if (t > 0) {
			setTimeout(() => {
				mssg_content.innerHTML = '';
				popup.style.top = '-250px';
			}, t);
		}

	}
})(document);
