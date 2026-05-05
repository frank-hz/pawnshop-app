(function(d){
    let app;
	const SDConversor = new Intl.NumberFormat('en-US', {
		minimumFractionDigits: 2
	})
    var search_settings = {type: 'ALL', value: '', sucursal: ''};
    var data_stash = [];
	var articulo_object = {
		id: 0,
		costo_inicial: 0,
		costo_total: 0,
		reparacion: 0,
		precio_venta: 0
	}
	var venta_object = {
		subtotal: 0,
		descuento: 0,
		total: 0,
		detalle: []
	}

	// EVENTOS
	d.addEventListener('DOMContentLoaded', async function(){
        app = new App();
		await LoadSucursal()
		RenderArticulos()
		// app.modal('modal-venta-invoice')
		// alert('hola')
	})
	document.addEventListener('click', event => {
		if (event.target && event.target.id === 'btn-examinar') {
			app.modal('modal-examinar', 'open');
		}

		if (event.target && event.target.id === 'theme_switch'){
			event.target.classList.toggle('active');
        	d.body.classList.toggle('dark');
		}

		if (event.target && event.target.name === 'articulo-detalle'){
			if (event.target.dataset.id) {
                OpenArticuloDetalle(event.target.dataset.id);
            }else {
                app.comandUp('Articulo no identificado', 'open', 2500, 'warning');
            }
		}

		if (event.target && event.target.name === 'articulo-editar') {
            if (event.target.dataset.id) {
                OpenArticuloEdicion(event.target.dataset.id);
            }else {
                app.comandUp('Articulo no identificado', 'open', 2500, 'warning');
            }
        }

		if (event.target && event.target.name === 'articulo-venta') {
            VentaReset();
            if (event.target.dataset.id) {
                if (event.target.dataset.id) {
                    var modal_form = document.getElementById('modal-articulo-venta')
                    modal_form.articulo.value = event.target.dataset.id;
                    modal_form.precio.value = event.target.dataset.precio;
                    modal_form.descuento.value = '';
                    modal_form.descuento.dataset.base = event.target.dataset.precio;
                    modal_form.total.value = event.target.dataset.precio;
                    
                    venta_object.subtotal = event.target.dataset.precio;
                    venta_object.total = event.target.dataset.precio;
                    venta_object.descuento = 0;
                    venta_object.detalle.push({
                        id: event.target.dataset.id,
                        precio: event.target.dataset.precio,
                        total: event.target.dataset.precio
                    });

                    var row_html = `
                        <tr>
                            <td class="w-60">${event.target.dataset.descripcion}</td>
                            <td class="text-center">01</td>
                            <td>S/ ${event.target.dataset.precio}</td>
                        </tr>
                    `;
                    app.render(modal_form.querySelector('tbody'), row_html);
                    app.render('view_descuento', 'S/ 0.00')
                    app.render('view_total', 'S/ '+event.target.dataset.precio)
                    app.modal(modal_form);
                }else {
                    app.comandUp('Articulo no identificado', 'open', 2500, 'warning');
                }
            }else {
                app.comandUp('Articulo no identificado', 'open', 2500, 'warning');
            }
        }

		if (event.target && event.target.name === 'venta-recibo') {
            if (event.target.dataset.id) {
				// app.comandUp('En mantenimiento...', 'open', 2500, 'primary');
                OpenVentaRecibo(event.target.dataset.id);
            }else {
                app.comandUp('Venta no identificada', 'open', 2500, 'warning');
            }
        }

        if (event.target && event.target.name === 'venta-anular') {
            if (event.target.dataset.venta && event.target.dataset.articulo) {
                if (confirm('¿Anular Venta?')) {
                    AnularVenta(event.target.dataset.venta, event.target.dataset.articulo);
                }
            }else {
                app.comandUp('Venta no identificada', 'open', 2500, 'warning');
            }
        }

		if (event.target && event.target.id === 'ctrl-descuento-aplicar') {
			try {
				app.render('descuento-feedback', '');
				let form = document.getElementById('modal-articulo-venta');
				let descuento = form.descuento.value ? form.descuento.value : 0;
				let monto_base = parseFloat(form.descuento.dataset.base);
				if (descuento > 0) {
					if (descuento > monto_base) {
						app.render('descuento-feedback', 'El descuento debe ser menor o igual al total');
						throw 'Error Descuento'
					}
					descuento = (descuento < monto_base) ? descuento : 0;
					let result =  monto_base - descuento;

					venta_object.descuento = descuento;
					venta_object.total = result;
					// form.total.value = result;
					// form.descuento.value = '';
					app.render('view_descuento', 'S/ '+venta_object.descuento) 
					app.render('view_total', 'S/ '+SDConversor.format(result)) 
				}else {
					app.render('descuento-feedback', 'Descuento invalido.');
				}
			} catch (error) {
				console.log(error)
			}
		}

		if (event.target && event.target.id === 'btn-invoice-screenshot') {
			ScreenRecibo()
		}

		if (event.target && event.target.name === 'modal-close-dissmiss') {
            event.preventDefault()
            if (event.target.dataset.objetive) {
                app.modal(event.target.dataset.objetive, 'close');
            }
        }

        if (event.target.matches('.filter-link-item')) {
            var items = document.querySelectorAll('.filter-link-item');
                items.forEach(e => { e.classList.remove('activate') });
                event.target.classList.add('activate');
                search_settings.type = event.target.dataset.value;
                RenderArticulos()
        }

	})
	document.addEventListener('keyup', event => {

		if (event.target && event.target.id === 'filter-input-search') {
			if (event.target.value.length) {
				search_settings.value = event.target.value;
				RenderArticulos()
			}
		}

		if (event.target && event.target.id === 'edicion-reparacion') {
			app.render('reparacion-feedback', '')
			try {
				const modal = document.getElementById('modal-articulo-editar');
				if (isNaN(event.target.value)) throw 'Tipo de dato erroneo';

				let reparacion = parseFloat(event.target.value) ? parseFloat(event.target.value) : 0;
				let monto_base = parseFloat(articulo_object.costo_inicial);
				reparacion = reparacion ? reparacion : 0;
				let result =  monto_base + reparacion;

				articulo_object.costo_total = result;
				articulo_object.costo_reparacion = reparacion;
				app.render('edicion-costo-total', SDConversor.format(articulo_object.costo_total));
			} catch (error) {
				console.log(error)
				app.render('reparacion-feedback', error.message);
			}
		}

	})
	document.addEventListener('keypress', e => {
		if (e.target && e.target.id === 'edicion-reparacion') {
			e = e || window.event;
			var charCode = (typeof e.which == "undefined") ? e.keyCode : e.which;
			var charStr = String.fromCharCode(charCode);
			if (/[a-z]/.test(charStr)) {
				e.preventDefault()
				return false;
			}
		}
		if (e.target && e.target.id === 'edicion-venta') {
			e = e || window.event;
			var charCode = (typeof e.which == "undefined") ? e.keyCode : e.which;
			var charStr = String.fromCharCode(charCode);
			if (/[a-z]/.test(charStr)) {
				e.preventDefault()
				return false;
			}
		}
	})
	document.addEventListener('change', event => {
		if (event.target && event.target.id === 'filter-sucursal') {
			if (event.target.value.length) {
				search_settings.sucursal = event.target.value;
				
			}
		}
		
		if (event.target && event.target.name === 'radio-option-search') {
            search_settings.value = event.target.value;
            RenderArticulos()
        }

		if (event.target && event.target.name === 'radio-filter-selection') {
            if (event.target.value.length) {
                const inputSection 	= d.getElementById('filter-input-section');
                const optionSection = d.getElementById('filter-option-section');
                const inputElement 	= inputSection.querySelector('input');
                const optionRender 	= d.getElementById('option-render-area');

                
                // option-render-area
                search_settings.type = event.target.value;
                switch (event.target.value) {
                    case 'ALL':
                        inputSection.classList.add('d-none');
                        optionSection.classList.add('d-none');
                        inputElement.value = '';
                        RenderArticulos()
                        break;
                
                    case 'CREDITO':
                        inputSection.classList.remove('d-none');
                        optionSection.classList.add('d-none');
                        inputElement.value = '';
                        inputElement.placeholder = 'INGRESE EL CODIGO DEL CREDITO';
                        break;

                    case 'CLIENTE':
                        inputSection.classList.remove('d-none');
                        optionSection.classList.add('d-none');
                        inputElement.value = '';
                        inputElement.placeholder = 'Ingresar dni, nombres o apellidos del cliente';
                        break;
                    
                    case 'ESTADO':
                        inputSection.classList.add('d-none');
                        optionSection.classList.remove('d-none');
                        app.render(optionRender, `
                            <div class="radio-form mx-2">
                                <input type="radio" id="option-disponible" name="radio-option-search" value="D">
                                <label for="option-disponible">
                                    <span class="radio-form-icon"></span>
                                    DISPONIBLE
                                </label>
                            </div>
                            <div class="radio-form mx-2">
                                <input type="radio" id="option-vendido" name="radio-option-search" value="V">
                                <label for="option-vendido">
                                    <span class="radio-form-icon"></span>
                                    VENDIDO
                                </label>
                            </div>
                        `);
                        break;
                    
                    case 'DESCRIPCION':
                        optionSection.classList.add('d-none');
                        inputSection.classList.remove('d-none');
                        console.log(inputElement)
                        inputElement.value = '';
                        inputElement.placeholder = 'Ingresar datos de la prenda';
                        break;
                    
                    case 'TIPO':
                        inputSection.classList.add('d-none');
                        optionSection.classList.remove('d-none');
                        app.render(optionRender, `
                            <div class="radio-form mx-2">
                                <input type="radio" id="option-electro" name="radio-option-search" value="ELECTRO">
                                <label for="option-electro">
                                    <span class="radio-form-icon"></span>
                                    ELECTRO
                                </label>
                            </div>
                            <div class="radio-form mx-2">
                                <input type="radio" id="option-joya" name="radio-option-search" value="JOYA">
                                <label for="option-joya">
                                    <span class="radio-form-icon"></span>
                                    JOYA
                                </label>
                            </div>
                            <div class="radio-form mx-2">
                                <input type="radio" id="option-vehiculo" name="radio-option-search" value="VEHICULO">
                                <label for="option-vehiculo">
                                    <span class="radio-form-icon"></span>
                                    VEHICULO
                                </label>
                            </div>
                            <div class="radio-form mx-2">
                                <input type="radio" id="option-predio" name="radio-option-search" value="PREDIO">
                                <label for="option-predio">
                                    <span class="radio-form-icon"></span>
                                    PREDIO
                                </label>
                            </div>
                        `);
                        break;
                        break;
                }
            }
        }	
	})
	document.addEventListener('submit', async event => {
		event.preventDefault()

		if (event.target && event.target.id === 'modal-articulo-editar') {
			try {
				app.wait_for('open', 'Guardando...');
				
				articulo_object.precio_venta = event.target.precio_venta.value ? event.target.precio_venta.value : 0;				
				if (Articulo_ValidarEdicion()) {
					let req = await fetch(srv.url()+'Articulo/service_update', {
						method: 'POST',
						body: JSON.stringify(articulo_object)
					});
					const res = await req.json();  
					app.wait_for('close');

					if (res) {
						if (res.code === 'OK') {
							app.modal('modal-articulo-editar', 'close');
							app.comandUp(res.message, 'open', 2500, 'success');
							if (res.updates > 0) RenderArticulos();
						}else {
							app.comandUp(res.message, 'open', 2500, 'warning');
						}
					}else {
						app.comandUp('Error al procesar', 'open', 2500, 'danger');
					}
				}else {
					app.comandUp('No se pudo enviar los cambios', 'open', 2500, 'danger');
				}
			} catch (error) {
				app.wait_for('close');
				app.comandUp('Procesamiento erroneo', 'open', 2500, 'danger');
				console.log('Editar', error)
			}
		}

		if (event.target && event.target.id === 'modal-articulo-venta') {
			try {
				if (confirm('¿Registrar Venta?')) {
					if (parseFloat(venta_object.total) > 0){
						app.wait_for('open', 'Guardando...');
						let form = event.target;
						venta_object.dni = form.dni.value;
						venta_object.nombre = form.nombre.value;
						venta_object.celular = form.celular.value;
						venta_object.metodo = form.metodo.value;
						venta_object.comentario = form.comentario.value;
						var req = await fetch(srv.url()+'Venta/service_registro', {
							headers: {
								'Content-Type': 'application/json'
							},
							method: 'POST',
							body: JSON.stringify(venta_object)
						})
						var res = await req.json();
						app.wait_for('close');
	
						if (res.code === 'OK') {
							app.comandUp(res.message, 'open', 2500, 'success');
							VentaReset();                
							app.modal('modal-articulo-venta', 'close');
							RenderArticulos();
						}else {
							app.comandUp(res.message, 'open', 2500, 'warning');
						}
					}else {
						app.comandUp('El total de la venta debe ser mayor a 0(cero)', 'open', 2000, 'danger');
					}
				}
			} catch (error) {
				console.log(error)
				app.wait_for('close');
				app.comandUp('Procesamiento erroneo', 'open', 2500, 'danger');
			}
		}
	})


    
	// FUNCIONES
	function init(){
		RenderArticulos()
	}

	async function LoadSucursal(){
		let data = new FormData();
		data.append('tipo', 'breve');
		const req = await fetch(srv.url()+'Sucursal/consulta', {
			method: 'POST',
			body: data
		});
		const res = await req.json();
		if (res) {
			// let html = '<option value="">SELECCIONAR</option>';
			let html = '';
			res.forEach(e => {
				if (e.estado === 'A') {
					search_settings.sucursal = e.id;
					d.getElementById('filter-sucursal').value = e.id;
				}
				html += `
					<option value="${e.id}">
						${e.descripcion + ', '+e.ciudad}
					</option>
				`;
			})
			d.getElementById('filter-sucursal').innerHTML = html;
		}
	}

    async function RenderArticulos(){
		var table = document.getElementById('datatable-articulo');
        // app.preload(table.querySelector('tbody'), 'in_table', 'Cargando Data..', 7);
        await GetDataArticulos();

        var html = '';		
        if (data_stash.length) {
            data_stash.forEach((item, index) => {
                var tag = "";
                var controls = `                    
                    <a href="javascript:void(0)" class="option-button"  
                        name="articulo-detalle"
                        data-id="${item.id}" title="Ver Datos del Articulo">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-folder" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
						<path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
						<path d="M5 4h4l3 3h7a2 2 0 0 1 2 2v8a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2v-11a2 2 0 0 1 2 -2"></path>
						</svg>
                    </a>
                `;
                if (item.estado === 'D') { 			   
                    controls += `
                        <a href="javascript:void(0)" class="option-button option-button-warning"
                            name="articulo-editar"
                            data-id="${item.id}" title="Editar datos del articulo">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-edit" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
							<path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
							<path d="M9 7h-3a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-3"></path>
							<path d="M9 15h3l8.5 -8.5a1.5 1.5 0 0 0 -3 -3l-8.5 8.5v3"></path>
							<line x1="16" y1="5" x2="19" y2="8"></line>
							</svg>
                        </a>
                        <a href="javascript:void(0)" class="option-button"
                            name="articulo-venta" 
                            data-id="${item.id}" data-precio="${item.precio_venta}"  
                            data-descripcion="${item.descripcion}" 
                            title="Registrar salida del articulo por venta">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-shopping-cart" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
							<path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
							<circle cx="6" cy="19" r="2"></circle>
							<circle cx="17" cy="19" r="2"></circle>
							<path d="M17 17h-11v-14h-2"></path>
							<path d="M6 5l14 1l-1 7h-13"></path>
							</svg>
                        </a>
                    `;
                    tag = '<div class="tag tag-disponible">Disponible</div>';

                }else if(item.estado === 'V') {					
                    controls += `
                        <a href="javascript:void(0)" class="option-button option-button-success"
                            name="venta-recibo"
                            data-id="${item.venta_id}" title="Ver recibo de venta asociada">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
							<path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
							<path d="M14 3v4a1 1 0 0 0 1 1h4"></path>
							<path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z"></path>
							<line x1="9" y1="9" x2="10" y2="9"></line>
							<line x1="9" y1="13" x2="15" y2="13"></line>
							<line x1="9" y1="17" x2="15" y2="17"></line>
							</svg>
                        </a>
						<a href="javascript:void(0)" class="option-button option-button-danger"
                            name="venta-anular" 
                            data-venta="${item.venta_id}" data-articulo="${item.id}" title="Anular Venta">
                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-square-x" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
							<path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
							<rect x="4" y="4" width="16" height="16" rx="2"></rect>
							<path d="M10 10l4 4m0 -4l-4 4"></path>
							</svg>
                        </a>
                    `;
                    tag = '<div class="tag tag-vendido">Vendido</div>';

                }
                html += `
                    <tr>
						<td class="min-w-100">
							<a href="${srv.url()}Credito/detalle/${item.credito_id}" 	class="btn-link">
								${item.credito_serie}
							</a>
						</td>
                        <td class="min-w-250">
                            ${item.descripcion}
                            <span class="sub-info">${item.categoria}</span>
                        </td>
                        <td class="min-w-100">${item.tipo}</td>
                        <td class="min-w-100">${item.create_at}</td>
                        <td>S/ ${item.costo_total}</td>
						<td>S/ ${item.precio_venta}</td>
                        <td class="min-w-100">${tag}</td>
                        <td>
							<div class="cell-options">
								${ controls }
							</div>
						</td>
                    </tr>
                `;
            })
        }else {
            html = `
                <tr>
                    <td colspan="7" class="text-center">No se encontraron datos</td>
                </tr>
            `;
        }

        // app.preload(table.querySelector('tbody'), 'close');
        app.render(table.querySelector('tbody'), html);
		RenderResumen()
    }

    async function GetDataArticulos(){
        try {            
            const req = await fetch(srv.url()+'Articulo/service_get_all', {
                method: 'POST',
                body: JSON.stringify(search_settings),
                headers: {
                    'Content-Type': 'application/json'
                }
            });
            const res = await req.json();
            data_stash = res;
        } catch (error) {
            return [];
        }
    }

    async function OpenArticuloEdicion(id) {
        try {
			articulo_object.id = 0;
			articulo_object.costo_inicial = 0;
			articulo_object.costo_total = 0;
			articulo_object.costo_reparacion = 0;
			articulo_object.precio_venta = 0;

			app.render('reparacion-feedback', '')
			app.wait_for('open', 'Cargando...')
            var modal = document.getElementById('modal-articulo-editar');
            const req = await fetch(srv.url()+'Articulo/service_search', {
               method: 'POST',
               body: JSON.stringify({id: id})
            });
            const res = await req.json();
            if (res) {
                modal.reset();
				articulo_object.id = res.id;
				articulo_object.costo_inicial = res.costo_inicial;
				articulo_object.costo_total = res.costo_total;
				articulo_object.costo_reparacion = res.costo_reparacion;
				articulo_object.precio_venta = res.precio_venta;

				app.render('md-editar-articulo-preview', res.descripcion);
				app.render('edicion-costo-inicial', SDConversor.format(articulo_object.costo_inicial));
				app.render('edicion-costo-total', SDConversor.format(articulo_object.costo_total));
                modal.costo_reparacion.value = articulo_object.costo_reparacion;
				modal.precio_venta.value = articulo_object.precio_venta;

				app.wait_for('close');
                app.modal(modal);
            }else{
				app.wait_for('close');
				app.comandUp('Articulo no identificado', 'open', 2500, 'warning');
            }
        } catch (error) {
			app.wait_for('close');
            console.log(error)
        }
    }
    
    async function OpenArticuloDetalle(id) {
        var modal = document.getElementById('modal-articulo-detalle');
        app.preview_skeleton(modal.querySelector('.modal-body'), 'modal_detalle');
        var req = await fetch(srv.url()+'Articulo/service_search', {
            method: 'POST',
            body: JSON.stringify({ id: id }),
            headders: {
                'Content-Type': 'application/json'
            }
        });
        var data = await req.json();
        var html = "";
        if (data) {
            var data_unique = "";			
            switch (data.tipo) {
                case 'ELECTRO':
                    data_unique = `
                        <div class="d-flex flex-wrap mb-3">
                            <div class="w-50">
                                <div class="info-label">Serial</div>
                                <div class="info-text">${data.serial}</div>
                            </div>
                            <div class="w-50">
                                <div class="info-label">Marca</div>
                                <div class="info-text">${data.marca}</div>
                            </div>                            
                        </div>
                        <div class="d-flex flex-wrap mb-3">
                            <div class="w-50">
                                <div class="info-label">Modelo</div>
                                <div class="info-text">${data.modelo}</div>
                            </div>
                            <div class="w-50">
                                <div class="info-label">Marca</div>
                                <div class="info-text">${data.marca}</div>
                            </div>
                        </div>
                    `;
                    break;
    
                case 'JOYA':
                    data_unique = `
                        <div class="d-flex flex-wrap mb-3">
                            <div class="w-50">
                                <div class="info-label">Quilate</div>
                                <div class="info-text">${data.quilate}</div>
                            </div>
                            <div class="w-50">
                                <div class="info-label">Peso</div>
                                <div class="info-text">${data.peso}</div>
                            </div>                            
                        </div>
                        <div class="d-flex flex-wrap mb-3">
                            <div class="w-50">
                                <div class="info-label">Estado</div>
                                <div class="info-text">${data.valoracion}</div>
                            </div>
                        </div>
                    `;
                    break;
                
                case 'VEHICULO':
                    data_unique = `
                        <div class="d-flex flex-wrap mb-3">
                            <div class="w-50">
                                <div class="info-label">Marca</div>
                                <div class="info-text">${data.marca}</div>
                            </div>
                            <div class="w-50">
                                <div class="info-label">Modelo</div>
                                <div class="info-text">${data.modelo}</div>
                            </div>
                        </div>
                        <div class="d-flex flex-wrap mb-3">
                            <div class="w-50">
                                <div class="info-label">Placa</div>
                                <div class="info-text">${data.placa}</div>
                            </div>
                            <div class="w-50">
                                <div class="info-label">Año</div>
                                <div class="info-text">${data.anio}</div>
                            </div>
                        </div>
                        <div class="d-flex flex-wrap mb-3">
                            <div class="w-50">
                                <div class="info-label">Kilometraje</div>
                                <div class="info-text">${data.kilometraje}</div>
                            </div>
                            <div class="w-50">
                                <div class="info-label">Estado</div>
                                <div class="info-text">${data.valoracion}</div>
                            </div>
                        </div>
                    `;
                    break;
            }

            data.comentario = (data.comentario.length > 0) ? data.comentario : 'SIN DATA';
			var estado_tag = (data.estado === 'V') 
				?  '<div class="tag tag-vendido">Vendido</div>'
				: '<div class="tag tag-disponible">Disponible</div>';
            html = `
                <div class="d-flex flex-wrap mb-3">
                    <div class="w-50">
                        <div class="info-label">Descripcion</div>
                        <div class="info-text">${data.descripcion}</div>
                    </div> 
					<div class="w-50">
						<div class="info-label">Estado</div>
						<div class="info-text">${estado_tag}</div>
					</div>                   
                </div>
                <div class="d-flex flex-wrap mb-3">
                    <div class="w-50">
                        <div class="info-label">Tipo</div>
                        <div class="info-text">${data.tipo}</div>
                    </div>
                    <div class="w-50">
                        <div class="info-label">Categoria</div>
                        <div class="info-text">${data.categoria}</div>
                    </div>
                </div>
                ${data_unique}
                <div class="d-flex flex-wrap mb-3">
                    <div class="w-50">
                        <div class="info-label">Precio Inicial</div>
                        <div class="info-text">S/ ${data.costo_inicial}</div>
                    </div>
                    <div class="w-50">
                        <div class="info-label">Costo Reparacion</div>
                        <div class="info-text">S/ ${data.costo_reparacion}</div>
                    </div>
                </div>
                <div class="d-flex flex-wrap mb-3">
                    <div class="w-50">
                        <div class="info-label">Costo Total</div>
                        <div class="info-text">S/ ${data.costo_total}</div>
                    </div>  
					<div class="w-50">
                        <div class="info-label">Precio de Venta</div>
                        <div class="info-text">S/ ${data.precio_venta}</div>
                    </div>  
                </div>
                <div class="d-flex flex-wrap mb-3">
                    <div class="w-100">
                        <div class="info-label">Observaciones</div>
                        <div class="info-text">${data.observacion}</div>
                    </div>					
                </div>
				<div class="d-flex flex-wrap mb-3">
					<div class="w-100">
                        <div class="info-label">Comentarios</div>
                        <div class="info-text">${data.comentario}</div>
                    </div>
				</div>
				<div class="d-flex flex-wrap mb-3">
					
                    <div class="w-50">
                        <div class="info-label">Seguimiento</div>
                        <a class="btn btn-small btn-indigo" href="${srv.url()}Prenda/detalle/${data.id_prenda}">
							Ver Detalle de Prenda
						</a>
                    </div>
                </div>
                
            `;            
        }else {
			app.comandUp('Datos no encontrados', 'open', 2500, 'warning');
        }
        setTimeout(() => {
            app.render(modal.querySelector('.modal-body'), html);
        }, 300);
        app.modal(modal);
    }

    async function OpenVentaRecibo(id) {
        try {
            var modal = document.getElementById('modal-venta-invoice');
            var req = await fetch(srv.url()+'Venta/service_recibo', {
                method: 'POST',
                body: JSON.stringify({id: id}),
                headers: {
                    'Content-Type': 'application/json'
                }
            })
            var res = await req.json();
            if (res && res.venta && res.detalle) {
                // res.cliente_nombre = (res.cliente_nombre.length > 0) ? res.cliente_nombre : 'No Definido'; 
                // res.cliente_dni = (res.cliente_dni.length > 0) ? res.cliente_dni : 'No Definido';
                app.render('invoice-fecha', res.venta.pre_creacion);
                app.render('invoice-cliente', res.venta.cliente_nombre);
                app.render('invoice-doc', res.venta.cliente_dni);
				app.render('invoice-celular', res.venta.cliente_celular);
				app.render('invoice-metodo', res.venta.metodo_pago);
				app.render('invoice-total', `S/ ${ SDConversor.format(res.venta.total) }`);

				let detail_html = '';
                res.detalle.forEach(e => {
					detail_html += `
						<tr>
							<td>${e.descripcion}</td>
							<td>S/ ${ SDConversor.format(e.total) }</td>
						</tr>
					`;
				})

				detail_html += `
					<tr>
						<td class="text-left text-danger">DESCUENTO</td>
						<td class="text-danger">S/ ${ SDConversor.format(res.venta.descuento) }</td>
					</tr>
				`;

                app.render(d.getElementById('invoice-detalle'), detail_html);
                app.modal(modal);
            }

        } catch (error) {
            console.log(error)
            
        }
    }

	function RenderResumen(){
		var cantidad_total = data_stash.length;
		var cantidad_activo = data_stash.reduce((prev, item, index) => {
			return item.estado === 'D' ? prev + 1 : prev;
		}, 0)
		var cantidad_vendido = data_stash.reduce((prev, item, index) => {
			return item.estado === 'V' ? prev + 1 : prev;
		}, 0)
		var precio_total = data_stash.reduce((prev, item, index) => {
			return prev + parseFloat(item.precio_venta);
		}, 0)
		var precio_activo = data_stash.reduce((prev, item, index) => {
			return item.estado === 'D' ? prev + parseFloat(item.precio_venta) : prev;
		}, 0)
		var precio_vendido = data_stash.reduce((prev, item, index) => {
			return item.estado === 'V' ? prev + parseFloat(item.precio_venta) : prev;
		}, 0)

		app.render('widget-cantidad-total', cantidad_total)
		app.render('widget-cantidad-activos', cantidad_activo)
		app.render('widget-cantidad-vendidos', cantidad_vendido > 9 ? cantidad_vendido : '0'+cantidad_vendido)
		// app.render('widget-costo-total', 'S/'+app.money_format(precio_total))
		// app.render('widget-costo-activos', 'S/'+app.money_format(precio_activo))
		// app.render('widget-costo-vendidos', 'S/'+app.money_format(precio_vendido))
	}

	function ScreenRecibo(){
		html2canvas(document.getElementById('invoice-screen-area'), {
			letterRending: 1,
			allowTaint: true,
			useCORS: true
		})
		.then(function(canvas){
			var a_link = document.createElement('a')
			a_link.href = canvas.toDataURL('image/png', 0.5);
			a_link.download = 'venta.png';
			a_link.click();
		})
		.catch(e => {
			alert('Error: ', e.message)
		})
	}

	async function AnularVenta(venta, articulo){
		try {
			app.wait_for('open', 'Guardando...');
			const send = await fetch(srv.url()+'Venta/service_anulacion', {
				headers: {
					'Content-Type': 'application/json'
				},
				method: 'POST',
				body: JSON.stringify({ venta: venta, articulo: articulo })
			});
			const resp = await send.json();
			if (resp.code === 'OK') {
				RenderArticulos()
				app.wait_for('close');
				app.comandUp(resp.message, 'open', 2500, 'success');
			}
		} catch (error) {
			app.wait_for('close');
			app.comandUp(`Procesamiento erroneo [${error.message}]`, 'open', 2500, 'danger');
		}
	}

	function VentaReset() {
		let form = document.getElementById('modal-articulo-venta');
		form.reset();

		venta_object.subtotal = 0;
		venta_object.descuento = 0;
		venta_object.total = 0;
		venta_object.detalle = [];
	}

	function Articulo_ValidarEdicion(){
		let state = true;
		let log = [];

		if (!articulo_object.id || articulo_object.id.length < 0 || parseInt(articulo_object.id) <= 0) {
			state = false;
			log.push('id [err]');
		}

		if (isNaN(articulo_object.costo_inicial) || parseFloat(articulo_object.costo_inicial) < 0) {
			state = false;
			log.push('costo_inicial [err]');
		}
		if (isNaN(articulo_object.costo_total) || parseFloat(articulo_object.costo_total) < 0) {
			state = false;
			log.push('costo_total [err]');
		}
		if (isNaN(articulo_object.costo_reparacion) || parseFloat(articulo_object.costo_reparacion) < 0) {
			state = false;
			log.push('reparacion [err]');
		}
		if (isNaN(articulo_object.precio_venta) || parseFloat(articulo_object.precio_venta) < 0){
			state = false;
			if (isNaN(articulo_object.precio_venta)) log.push('precio_venta-01 [err]');  
			if (parseFloat(articulo_object.precio_venta) < 0) log.push('precio_venta-02 [err]'); 
			// log.push('precio_venta [err]');
		}

		console.log('EdicionLog', log)
		return state;
	}

	function Venta_ValidarEdicion(){
		let state = true;
		let log = [];

		if (isNaN(articulo_object.costo_inicial) || parseFloat(articulo_object.costo_inicial) < 0) {
			state = false;
			log.push('costo_inicial [err]');
		}
		if (isNaN(articulo_object.costo_total) || parseFloat(articulo_object.costo_total) < 0) {
			state = false;
			log.push('costo_total [err]');
		}
		if (isNaN(articulo_object.costo_reparacion) || parseFloat(articulo_object.costo_reparacion) < 0) {
			state = false;
			log.push('reparacion [err]');
		}
		if (isNaN(articulo_object.precio_venta) || parseFloat(articulo_object.precio_venta) < 0){
			state = false;
			log.push('precio_venta [err]');
		}

		console.log(log)
		return state;
	}
	
})(document);
