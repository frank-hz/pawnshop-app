(function(d, w){
    const EXCEL_TYPE = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;charset=UTF-8';
	const EXCEL_EXTENSION = '.xlsx';
    let data_export = [];
	let data_raw = [];
    let data_req = {
		tipo_busqueda: 'CODIGO',
		buscar: '',
		orden: {
			por: 'SERIE',
			tipo: 'DESC'
		},
		forma_pago: [],
		concepto: [],
		limite: 0,
		fecha: {
            tipo: 'DATE',
			valor_a: '',
            valor_b: '',
            unico: ''
		}
	}
	let concepto_recognize = {
		'REG': 'Regulariza.',
		'REC': 'Recupera.',
		'AMOR': 'Amortiza.',
		'ADEL': 'Adelanto',
		'DEB_AUTO': 'Debito Auto.',
		'ADEL_AUTO': 'Adelanto Auto.'
	}
	let forma_recognize = {
		'TIEN': 'Tienda',
		'AGEN': 'Agente',
		'TRANF': 'Transferencia',
		'REV': 'Revision',
		'DEP': 'Deposito'
	}
	const module = {
		loader: (id, action)=>{
			let area = (typeof id === 'object') ? id : document.getElementById(id);	
		if (area) {
			if (action === 'open') {
				area.classList.add('openx');
			}else if(action === 'close'){
				area.classList.remove('openx');
			}
		}
		}
	}

    d.addEventListener('DOMContentLoaded', function(){
        init_pagos_dia()
		data_req.fecha.unico = '';
		// module.loader('pago_modulo_loader', 'open') 
		
    })
    d.addEventListener('click', event => {
        let ev = event.target;
        if (ev && ev.id === 'btn-export-excel') {
			var worksheet = XLSX.utils.json_to_sheet(data_export);
			var workbook = {
				Sheets: {
					'data': worksheet
				},
				SheetNames: ['data']
			};
			var excelBuffer = XLSX.write(workbook, {bookType: 'xlsx', type: 'array'})
			excel_export_file(excelBuffer, 'Electrocash - Lista de Pagos')
			
		}
		if (ev && ev.id === 'btn-data-filter-reset') {
			console.log('reset')
			data_req.tipo_busqueda = 'CODIGO';
			data_req.buscar = '';
			data_req.orden.por = 'SERIE';
			data_req.orden.tipo = 'DESC';
			data_req.forma_pago = [];
			data_req.concepto = [];
			data_req.limite = 0;
			data_req.fecha.tipo = 'DATE';
			data_req.fecha.valor_a = '';
			data_req.fecha.valor_b = '';
			data_req.fecha.unico = '';
			
			let concepto_items = d.getElementsByName('check-concepto');
			let forma_items = d.getElementsByName('check-forma-pago');
			concepto_items.forEach(e => {
				e.checked = false;
			});
			forma_items.forEach(e => {
				e.checked = false;
			});
			d.getElementById('input-buscar').value = '';
			d.getElementById('date-selector-unico').value = '';
			d.getElementById('date-selector-a').value = '';
			d.getElementById('date-selector-b').value = '';
			d.getElementById('select-tipo-busqueda').value = 'CODIGO';
			d.getElementById('select-date-filter-type').value = 'DATE';
			d.getElementById('select-orden-por').value = 'SERIE';
			d.getElementById('select-orden-tipo').value = 'DESC';

			renderizar_datos(true)
		}
		if (ev && ev.id === 'btn-data-pagos-dia') {
			init_pagos_dia(true)
			data_req.fecha.unico = '';
		}

    })
    d.addEventListener('change', event => {
		let ev = event.target;
		if (ev && ev.id === 'select-tipo-busqueda') {
			data_req.tipo_busqueda = ev.value;
			if (data_req.buscar) {
				renderizar_datos(true);
			}
		}
		if (ev && ev.id === 'select-date-filter-type') {
			data_req.fecha.tipo = ev.value;
            render_date_area(ev.value);
		}
		if (ev && ev.id === 'date-selector-unico') {
			data_req.fecha.unico = ev.value;
			renderizar_datos(true);
		}
        if (ev && ev.id === 'date-selector-a') {
			data_req.fecha.valor_a = ev.value;
            if (data_req.fecha.valor_b.length) {
                renderizar_datos(true);
            }
		}
        if (ev && ev.id === 'date-selector-b') {
			data_req.fecha.valor_b = ev.value;
			if (data_req.fecha.valor_a.length) {
                renderizar_datos(true);
            }
		}
		if (ev && ev.id === 'select-orden-por') {
			data_req.orden.por = ev.value;
			renderizar_datos(true);
		}
		if (ev && ev.id === 'select-orden-tipo') {
			data_req.orden.tipo = ev.value;
			renderizar_datos(true);
		}
		if (ev && ev.name === 'check-forma-pago') {
			if(ev.checked) {
				data_req.forma_pago.push(ev.value);
			}else {
				let indel = null;
				data_req.forma_pago.forEach((e, index) => {
					if (e === ev.value) {
						indel = index;
					}
				})
				if (indel != null) {
					data_req.forma_pago.splice(indel, 1);
				}
			}
			// console.log('formas', data_req.forma_pago)
			renderizar_datos(true);
		}
		if (ev && ev.name === 'check-concepto') {
			if(ev.checked) {
				data_req.concepto.push(ev.value);
			}else {
				let indel = null;
				data_req.concepto.forEach((e, index) => {
					if (e === ev.value) {
						indel = index;
					}
				})
				if (indel != null) {
					data_req.concepto.splice(indel, 1);
				}
			}
			// console.log('formas', data_req.concepto)
			renderizar_datos(true);
		}
		if (ev && ev.id === 'select-concepto') {
			data_req.concepto.push(ev.value);
			renderizar_datos(true);
		}
	})
	d.addEventListener('keyup', event => { 
		let ev = event.target;       
        if (event.target && event.target.id === 'input-buscar') {
            data_req.buscar = ev.value;
			if (ev.value.length > 0) {
				renderizar_datos(true)
			}
        }
    })


    async function solicitar_datos(){
		try { 
            let req = await fetch(srv.url()+'Pago/service_get_data', {
                method: 'POST',
                body: JSON.stringify(data_req),
                headers: {
                    'Content-Type': 'application/json'
                }
            });
            let res = await req.json();
            data_raw = [];
			data_export = [];
			if (res.length > 0) {
				res.forEach((e, ind) => {
					let action_controls = eval("gridjs.html(`<a href='${srv.url()}Pago/detalle/${e.pago_id}' class='btn btn-sm btn-outline-primary mr-1'><i class='fe fe-layers'></i>Ver Detalle</a>`)");
					let concepto = concepto_recognize[e.concepto];
					let forma_pago = forma_recognize[e.forma_pago];
					data_raw.push([
						ind+1,
						eval("gridjs.html(`<a class='form-link' href='${srv.url()}Pago/detalle/${e.pago_id}'>${e.pago_serie}</a>`)"),
                        eval("gridjs.html(`<a class='form-link' href='${srv.url()}Credito/detalle/${e.credito_id}'>${e.credito_serie}</a>`)"),
						eval("gridjs.html(`<a class='form-link' href='${srv.url()}Cliente/detalle/${e.cliente_id}'>${e.cliente_nombre}</a>`)"),
                        e.fecha,
                        forma_pago,
                        concepto,
						'S/ '+e.total,
						action_controls
					]);	

					data_export.push({
						"Indice": ind+1,
                        "Pago": e.pago_serie,
						"Credito": e.credito_serie,
						"Registro": e.fecha,
						"Cliente": e.cliente_nombre,
						"Forma Pago": forma_pago,
						"Concepto": concepto,
						"Total": e.total
					});
				});
			}
        } catch (error) {
			console.log(error)
            return [];
        }
	}  
    async function renderizar_datos(upd = false){
		module.loader('pago_modulo_loader', 'open')        
		await solicitar_datos();
		let table_area = d.getElementById("pago_data_table");
		if (upd) {
			grid_table.updateConfig({
				data: data_raw
			}).forceRender();
		}else {
			grid_table = new gridjs.Grid({
				columns: [
					"#", "Pago", "Credito", "Cliente", "Fecha" 
					,"Forma de Pago","Concepto","Total", "Opcion"
				],
				sort: false,
				autoWidth: true,
				pagination: {
					limit: 20
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
		module.loader('pago_modulo_loader', 'close')
	}
    function render_date_area(t){
        let unico = d.getElementById('date-selector-unico');
        let rango_a = d.getElementById('date-selector-a');
        let rango_b = d.getElementById('date-selector-b');
        switch (t) {
            case 'DATE':
                unico.style.display = 'block';
                rango_a.style.display = 'none';
                rango_b.style.display = 'none';
                if (unico.value.length > 0) {
                    renderizar_datos(true);
                }
                break;
        
            case 'RANGE':
                unico.style.display = 'none';
                rango_a.style.display = 'block';
                rango_b.style.display = 'block';
                if (rango_a.value.length > 0 && rango_b.value.length > 0) {
                    renderizar_datos(true);
                }
                break;
        }
    }
	function init_pagos_dia(updx = false){
		const date = new Date();
		let day = date.getDate();
		day = (parseInt(day) < 10) ? '0'+day : day;
		let month = date.getMonth() + 1;
		let year = date.getFullYear();
		
		let currentDate = `${year}-${month}-${day}`;
		d.getElementById('date-selector-unico').value = currentDate;
		data_req.fecha.unico = currentDate;
		renderizar_datos(updx)
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