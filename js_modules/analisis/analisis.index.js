(function(d){
    const EXCEL_TYPE = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;charset=UTF-8';
    const EXCEL_EXTENSION = '.xlsx';
    let app;
    const mes_transl = [
        "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio",
        "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"
    ];
    const local_data = {
        sucursal_id: 0,
        sucursal_nombre: ''
    }
	const logs = {
		gen_vs_rec: [],
		balance: [],
		utilidad: [],
		gen_vs_rec_dia: []
	}
    const filter_data = {
        mode: 'MES_ACTUAL',
        in_rango_inicial: '',
        in_rango_final  : '',
        in_fecha        : '',
        in_mes          : '',
        in_anio         : ''
    }
    const LOG_DATA = {
        GENREC: []
    }
    const TIPO_LOG = {
       'CU': 'Cuota',
       'MO': 'Mora',
       'DESC': 'Descuento',
       'RECU': 'Recuperacion',
       'GEN': 'Generacion'
    }

    let DATA_EXPORT = {
        generacion_recuperacion: [],
        utilidad: [],
        clientes: []
    };
    let EXPORT_WORKBOOK = null;
    let EXPORT_SECTION = null;

    d.addEventListener('DOMContentLoaded', async () => {
        app = new App();
        EXPORT_SECTION = d.getElementById('export_collapse');
        
        RenderLoader('start'); // iniciar loader
		Load_SucursalSelector(); // carga datos de sucursal
        
        await LoadTimeline(); 
        // await InitAllModules();
        
        // cierrar el loader
        setTimeout(() => {
			RenderLoader('close')
		}, 250)
    })
    d.addEventListener('click', event => {
		if (event.target && event.target.id === 'collapse-dropwdown') {
			let collapse = d.getElementById('collapse-filter');
			collapse.classList.toggle('collapse-active')
		}

		if (event.target && event.target.matches('[name=modal-close]')) {
			if (event.target.dataset.target) {
				tool.modal_hide(event.target.dataset.target);
			}
		}

		if (event.target && event.target.name === 'panel-control-option') {
			RenderLog(event.target.dataset.target);
            // RenderLoader('start');
		}
        if (event.target.id === 'btn-sucursal') {
            tool.modal_open('modal-sucursal'); 
        }
        if (event.target.matches('[name=sucursal-item]')) {
            let id = event.target.dataset.id;
            if (!app.stringval(id)) return;
            if (id === local_data.sucursal_id) return;
            if (confirm('¿Cambiar de Sucursal?')) {
                Change_Sucursal(id);
            }
        }

	})
    d.addEventListener('change', event => {
		if (event.target && event.target.id === 'sucursal-selector') {
            // let val = event.target.value;
            // let nombre = event.target.dataset.nombre;
			// if (val.length > 0 && nombre.length > 0) {
            //     if (confirm(`¿Cambiar a la sucursal ${descripcion} ?`)){                
            //         Change_Sucursal(pars[0], pars[1])
            //     }
			// } 
            // tool.modal_open('modal-sucursal'); 
		}

		if (event.target && event.target.name === 'type-filter-item') {
            filter_data.mode = event.target.value;
            ReedixFilter();
        }
        if (event.target && event.target.name === 'rango_inicial') {
            filter_data.in_rango_inicial = event.target.value;
        }
        if (event.target && event.target.name === 'rango_final') {
            filter_data.in_rango_final = event.target.value;
        }
        if (event.target && event.target.name === 'fecha') {
            filter_data.in_fecha = event.target.value;
        }
        if (event.target && event.target.name === 'anio_radio_item') {
            filter_data.in_anio = event.target.value;
        }
        if (event.target && event.target.name === 'mes_radio_item') {
            filter_data.in_mes = event.target.value;
        }
		// console.log(filter_data)
	})
    d.addEventListener('submit', async event => {
		event.preventDefault()
		if (event.target && event.target.id === 'filtro-form') {
			RenderLoader('start')
			await InitAllModules()
			setTimeout(() => {
				RenderLoader('close')
			}, 1000)
		}
	})

    // NEW
    d.getElementById('EXPORT_GENREC')?.addEventListener('click', async evt => {
        evt.target.disabled = true;
        const data_ = logs.gen_vs_rec.map(item => {
            return {
                'Credito': item.serie,
                'Monto': item.monto,
                'Tipo': TIPO_LOG[item.tipo],
                'Cliente': item.cliente_nombre
            };
        });
        await exportData(data_, 'Generacion vs Recuperacion');
        setTimeout(() => {
            evt.target.disabled = false;
        }, 500);
    })
    d.getElementById('EXPORT_UTILIDAD')?.addEventListener('click', async evt => {
        evt.target.disabled = true;
        const data_ = logs.utilidad.map(item => {
            return {
                'Credito': item.credito_serie,
                'Monto': item.monto,
                'Tipo': TIPO_LOG[item.tipo],
                'Cliente': item.cliente_nombre
            }
        })
        await exportData(data_, 'Utilidad');

        setTimeout(() => {
            evt.target.disabled = false;
        }, 500);
    })
    d.getElementById('EXPORT_BALANCE')?.addEventListener('click', async evt => {
        evt.target.disabled = true;
        await exportData(logs.balance, 'Balance');

        setTimeout(() => {
            evt.target.disabled = false;
        }, 500);
    })
    d.getElementById('EXPORT_GENREC_DIA')?.addEventListener('click', async evt => {
        evt.target.disabled = true;
        const data_ = logs.gen_vs_rec_dia.map(item => {
            return {
                'Credito': item.serie,
                'Monto': item.monto,
                'Tipo': TIPO_LOG[item.tipo],
                'Cliente': item.cliente_nombre
            };
        });
        await exportData(data_, 'Generacion vs Recuperacion Dia');
        setTimeout(() => {
            evt.target.disabled = false;
        }, 500);
    })
    d.getElementById('form_exportacion')?.addEventListener('submit', evt => {
        evt.preventDefault();
        let form = evt.target;
        let nombre = form.nombre.value.length > 0 
            ? form.nombre.value : 'electrocash_export';
        let formato = form.formato.value;
        let formatos_validos = ['csv', 'xlsx', 'json'];
        if (!formato || formato.length <= 0 || !formatos_validos.includes(formato)) {
            console.log('Datos invalidos');
            return;
        }

        let nombre_archivo = `${nombre}.${formato}`;

        if (confirm('¿Exportar?')) {
            let work_book = XLSX.utils.book_new();
            let page_1 = XLSX.utils.json_to_sheet(DATA_EXPORT.generacion_recuperacion ?? []);
            let page_2 = XLSX.utils.json_to_sheet(DATA_EXPORT.utilidad ?? []);
            let page_3 = XLSX.utils.json_to_sheet(DATA_EXPORT.clientes ?? []);
            XLSX.utils.book_append_sheet(work_book, page_1, 'Generacion - Recuperacion');
            XLSX.utils.book_append_sheet(work_book, page_2, 'Utilidad');
            XLSX.utils.book_append_sheet(work_book, page_3, 'Clientes Registrados');
            XLSX.writeFile(work_book, nombre_archivo);
        }

    });
    


    // Init
    async function InitAllModules(){
        await recognizeFilter()        
		await Module_DataGeneral()
		await Module_GeneracionRecuperacion()
		await Module_GeneracionRecuperacionDia()
		await Module_DataUtilidad()
		await Module_TotalIngresos()
		await Module_BalanceDiario()


        if (EXPORT_SECTION != null) {
            EXPORT_SECTION.style.display = 'inherit';
        }
	}
    async function Load_SucursalSelector(){
        try {
            const data = new FormData();
            data.append('tipo', 'breve')
            const req = await fetch(srv.url() + 'Sucursal/consulta', {
                method: 'POST',
                body: data
            });
            const res = await req.json();
            let html = '';
            if (res.length > 0) {
                res.forEach(e => {
                    let actStyle = '';
                    if (e.estado === 'A') {
                        local_data.sucursal_id = e.id;
                        local_data.sucursal_nombre = e.nombre;
                        actStyle = 'media-item-active';
                    }
                    html += `
                        <div class="media-item ${actStyle}" data-id="${e.id}" data-nombre="${e.nombre}, ${e.ciudad}" name="sucursal-item">
                            <div class="media-item-icon">
                                <i class='bx bx-compass'></i>
                            </div>
                            <div class="media-item-data">
                                <div class="media-item-title">${e.nombre}</div>
                                <div class="media-item-subtitle">
                                    ${e.nombre}, ${e.ciudad}
                                </div>
                            </div>
                        </div>
                    `;
                })
            } else{
                html = `<p>NO DATA</p>`;
            }
    
            let list = d.getElementById('sucursal-media-list');
            let btn = d.getElementById('btn-sucursal');
            list.innerHTML = html;
            btn.innerHTML = `
                <i class="bx bx-map text-indigo"></i>
                ${local_data.sucursal_nombre}
            `;          
        } catch (error) {
            console.log(error)   
        }
	}
    async function Change_Sucursal(id){		
        try {
            
            let data = new FormData();
            data.append('sucursal', id);
            const req = await fetch(srv.url() + 'api/v1/session/sede/change', {
                method: 'POST',
                body: data
            })
            const res = await req.json();
            if (res && res.status === 'OK') {
                tool.modal_close('modal-sucursal'); 
                not.Ok('Sucursal Actualizada', '', 2000)
                setTimeout(() => {
                    window.location.reload();
                }, 600);
            }else {
                alert(res.message ? 'res.message' : 'Cambio de sucursal incompleto, intente nuevamente.');
            }            
        } catch (error) {
            console.log(error)
        }
	}
    function RenderLog(target){
        try {
            RenderLoader('start');
            let rows_html = `
                <tr>
                    <td colspan="4" class="text-center">Sin Datos</td>
                </tr>
            `;
            let all_html = '';
            var log_title = '';
            switch (target) {
                case 'GENVREC':
                    updateLogTable(["CREDITO","CLIENTE","PRENDA","MONTO","TIPO"]);
                    log_title = 'Generacion vs Recuperacion';
                    if (logs.gen_vs_rec.length > 0) {
                        rows_html = '';
                        logs.gen_vs_rec.forEach(e => {
                            rows_html += `
                                <tr>
                                    <td>
                                        <a class="btn-link" href="${srv.url()}Credito/detalle/${e.id}" target="_blank">
                                        ${e.serie}
                                        </a>
                                    </td>
                                    <td>${e.cliente_nombre}</td>
                                    <td>${e.prenda_descripcion}</td>
                                    <td>S/ ${e.monto}</td>
                                    <td>${e.tipo}</td>
                                </tr>
                            `;
                        })
                    }
                    // all_html = `
                    //     <table class="modal-table">
                    //         <thead>
                    //             <tr>
                    //                 <th>Credito</th>
                    //                 <th>Tipo</th>
                    //                 <th>Monto</th>
                    //             </tr>
                    //         </thead>
                    //         <tbody>
                    //             ${rows_html}
                    //         </tbody>
                    //     </table>
                    // `;
                    break;
                
                case 'BALANCE':	
                    log_title = 'Balance';
                    updateLogTable(["CREDITO","PAGO","CLIENTE","MONTO","TIPO"]);
                    if (logs.balance.length > 0) {
                        rows_html = '';
                        logs.balance.forEach(e => {
                            rows_html += `
                                <tr>
                                    <td>
                                        <a class="btn-link" href="${srv.url()}Credito/detalle/${e.id_credito}" target="_blank">
                                        ${e.credito_serie}
                                        </a>
                                    </td>
                                    <td>
                                        <a class="btn-link" href="${srv.url()}Pago/detalle/${e.id_pago}" target="_blank">
                                        ${e.pago_serie}
                                        </a>
                                    </td>
                                    <td>${e.cliente_nombre}</td>
                                    <td>S/ ${e.monto}</td>
                                    <td>${e.tipo}</td>
                                </tr>
                            `;
                        })
                    }				
                    // all_html = `
                    //     <table class="modal-table">
                    //         <thead>
                    //             <tr>
                    //                 <th>Credito</th>
                    //                 <th>Pago</th>
                    //                 <th>Tipo</th>
                    //                 <th>Monto</th>
                    //             </tr>
                    //         </thead>
                    //         <tbody>
                    //             ${rows_html}
                    //         </tbody>
                    //     </table>
                    // `;					
                    break;
                
                case 'UTILIDAD':
                    log_title = 'Utilidad';
                    updateLogTable(["CREDITO","CLIENTE","PAGO","MONTO","TIPO"]);
                    if (logs.utilidad.length > 0) {
                        rows_html = '';
                        logs.utilidad.forEach(e => {
                            rows_html += `
                                <tr>
                                    <td>
                                        <a class="btn-link" href="${srv.url()}Credito/detalle/${e.credito_id}" target="_blank">
                                        ${e.credito_serie}
                                        </a>
                                    </td>   
                                    <td>${e.cliente_nombre}</td>    
                                    <td>
                                        <a class="btn-link" href="${srv.url()}Pago/detalle/${e.pago_id}" target="_blank">
                                        ${e.pago_serie}
                                        </a>
                                    </td>                                    
                                    <td>S/ ${e.monto}</td>
                                    <td>${e.tipo}</td>
                                </tr>
                            `;
                        })
                    }				
                    // all_html = `
                    //     <table class="modal-table">
                    //         <thead>
                    //             <tr>
                    //                 <th>Credito</th>
                    //                 <th>Pago</th>
                    //                 <th>Tipo</th>
                    //                 <th>Monto</th>
                    //             </tr>
                    //         </thead>
                    //         <tbody>
                    //             ${rows_html}
                    //         </tbody>
                    //     </table>
                    // `;	
                    break;
                
                case 'GENVREC_DIA':
                    updateLogTable(["CREDITO","CLIENTE","PRENDA","MONTO","TIPO"]);
                    log_title = 'Generacion vs Recuperacion';
                    if (logs.gen_vs_rec_dia.length > 0) {
                        rows_html = '';
                        logs.gen_vs_rec_dia.forEach(e => {
                            rows_html += `
                                <tr>
                                    <td>
                                        <a class="btn-link" href="${srv.url()}Credito/detalle/${e.id}" target="_blank">
                                        ${e.serie}
                                        </a>
                                    </td>
                                    <td>${e.cliente_nombre}</td>
                                    <td>${e.prenda_descripcion}</td>
                                    <td>S/ ${e.monto}</td>
                                    <td>${e.tipo}</td>
                                </tr>
                            `;
                        })
                    }
                    // all_html = `
                    //     <table class="modal-table">
                    //         <thead>
                    //             <tr>
                    //                 <th>Credito</th>
                    //                 <th>Tipo</th>
                    //                 <th>Monto</th>
                    //             </tr>
                    //         </thead>
                    //         <tbody>
                    //             ${rows_html}
                    //         </tbody>
                    //     </table>
                    // `;
                    all_html = rows_html;
                    break;
            }

            all_html = rows_html;
            tool.render('modal-log-title', `Log de Datos: [${log_title}]`);
            tool.render('DATASET_LOG', all_html);
            setTimeout(() => {
                RenderLoader('close');
                tool.modal_open('modal_log');                
            }, 250);
        } catch (error) {
            RenderLoader('close');
        }
    }    

    // Data
    async function LoadTimeline(){
        try {
            const request = await fetch(srv.url()+'AnalisisRest/service_get_timeline', {
                method: 'POST',
                body: JSON.stringify(filter_data),
                headers: {
                    'Content-Type': 'application/json'
                }
            });
            const res = await request.json();
            let timeline_html = '';
			if (res && res.length) {
				res.forEach(e => {
					timeline_html += `
					<li>
						<input id="rd_${e.value}" type="radio" name="anio_radio_item" value="${e.value}">
						<label for="rd_${e.value}">${e.value}</label>
					</li>
					`;
				})
			}else {
				timeline_html = `
					<span class="d-block"><b>Sin Datos</b></span>
				`;
			}
            app.render('year_area_render', timeline_html);
        } catch (error) {
            
        }
    }
    async function Module_DataGeneral(){
        try {
            const request = await fetch(srv.url()+'AnalisisRest/service_data_general', {
                method: 'POST',
                body: JSON.stringify(filter_data),
                headers: {
                    'Content-Type': 'application/json'
                }
            });
            const resp = await request.json();
            if (!resp) throw new Exception('Error al procesar');			
            app.render('general_rango_inicio', resp.inicio);
            app.render('general_rango_final', resp.final);
            app.render('general_credito_activo', resp.activos);
            app.render('general_dinero_activo', `S/ ${ app.money_format(resp.dinero) }`);

        } catch (error) {
            console.log(error.message);
        }
    }
    async function Module_GeneracionRecuperacion(){
        try {
            const formatSoles = (monto) => `S/ ${app.money_format(monto)}`;
            const response = await fetch(`${srv.url()}AnalisisRest/service_generacion_recuperacion`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(filter_data)
            });
            if (!response.ok) throw new Error(`HTTP ${response.status}`);
            const resp = await response.json();
            const generacion_log = resp.genera_log ?? [];
            const recuperacion_log = resp.recupera_log ?? [];
            const remate_log = resp.remate_log ?? [];
            const clientes_log = resp.clientes ?? [];

            let log_compiled = [...recuperacion_log, ...generacion_log, ...remate_log];
            logs.gen_vs_rec = log_compiled;

            
            DATA_EXPORT.generacion_recuperacion = log_compiled;
            DATA_EXPORT.clientes = clientes_log;

            const campos = {
                generacion_cantidad: `${resp.genera_cantidad} Registros`,
                generacion_monto:    formatSoles(resp.genera_monto),
                recuperado_cantidad: `${resp.recupera_cantidad} Recuperaciones`,
                recuperado_monto:    formatSoles(resp.recupera_monto),
                remate_cantidad:     `${resp.remate_cantidad} Rematados`,
                remate_monto:        formatSoles(resp.remate_monto),
                diferencia_monto:    formatSoles(resp.diferencia)
            };
            Object.entries(campos).forEach(([id, valor]) => app.render(id, valor));
            app.css_add('diferencia_monto', resp.diferencia > 0 ? 'text-greenbook' : 'text-danger');

        } catch(e) {
            console.error('Error en Module_GeneracionRecuperacion:', e);
        }
    }
	async function Module_DataUtilidad(){
        try {
            const formatSoles = (monto) => `S/ ${app.money_format(monto)}`;
            let url = `${srv.url()}AnalisisRest/service_data_utilidad`;
            const response = await fetch(url, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(filter_data)
            });
            if (!response.ok) throw new Error(`HTTP ${response.status}`);
            const resp = await response.json();
			let status_elem = d.getElementById('icon-status-utilidad');
			
            if (!resp.status || resp.status != 'OK') {
                status_elem.title = 'Status: ERR';
				status_elem.classList.add('status-error');
				logs.utilidad = [];
                return;
            }

            let data = resp.result;
            status_elem.title = 'Status: OK';
            status_elem.classList.remove('status-error');
            logs.utilidad = data.log;

            let tipos = {
                'CU': 'CUOTA',
                'DESC': 'DESCUENTO',
                'MO': 'MORA',
            }
            let estados = {
                'R': 'RECUPERADO',
                'E': 'ACTIVO',
                'A': 'REMATE',
            }
            let batch = [];
            if (data.log && data.log.length > 0) {
                data.log.forEach(item => {
                    const {
                        pago_id, 
                        pago_serie,
                        credito_id,
                        credito_serie,
                        pago_fecha,
                        tipo,
                        pago_monto,
                        credito_estado,
                        cliente_nombre,
                        prenda_descripcion
                    } = item;
                    

                    const nuevo = {
                        pago_id, 
                        pago_serie,
                        credito_id,
                        credito_serie,
                        pago_fecha,
                        'tipo': tipos[tipo],
                        pago_monto,
                        'estado': estados[credito_estado],
                        cliente_nombre,
                        prenda_descripcion
                    }
                    batch.push(nuevo);
                });

            }
            DATA_EXPORT.utilidad = batch;
            
            app.render('utilidad_cuota_cant', data.cuota_cantidad);
            app.render('utilidad_cuota_monto', formatSoles(data.cuota_suma));
            app.render('utilidad_mora_cant', data.mora_cantidad);
            app.render('utilidad_mora_monto', formatSoles(data.mora_suma));
            app.render('utilidad_descuento_cant', data.descuento_cantidad);
            app.render('utilidad_descuento_monto', formatSoles(data.descuento_suma));
            app.render('utilidad_adicional_cant', data.adicional_cantidad);
            app.render('utilidad_adicional_monto', formatSoles(data.adicional_suma));
            app.render('utilidad_total_neto', formatSoles(data.utilidad_neta));
        } catch(e) {
            console.error('Error en Module_Utilidad:', e);
        }
    }
	async function Module_TotalIngresos(){
        try {
            const dt = new Date();
            const request = await fetch(srv.url()+'AnalisisRest/service_total_ingresos', {
                method: 'POST',
                body: JSON.stringify(filter_data),
                headers: {
                    'Content-Type': 'application/json'
                }
            });
            const resp = await request.json();

            app.render('entrada_cantidad', resp.entrada_cantidad);
            app.render('entrada_monto', `
                S/ ${ app.money_format(resp.entrada_monto) }
            `);
            app.render('salida_cantidad', resp.salida_cantidad);
            app.render('salida_monto', `
                S/ ${ app.money_format(resp.salida_monto) }
            `);
            app.render('total_monto_diferencia', `
                S/ ${ app.money_format(resp.total_diferencia) }
            `);

        } catch(e) {
            // statements
            console.log(e);
        }
    }
	async function Module_BalanceDiario(){
        try {
            const dt = new Date();
            const request = await fetch(srv.url()+'AnalisisRest/service_balance_diario', {
                method: 'POST',
                body: JSON.stringify(filter_data),
                headers: {
                    'Content-Type': 'application/json'
                }
            });
            const resp = await request.json();
			logs.balance = [].concat(resp.log);

            app.render('balance_cuota_monto', `S/ ${app.money_format(resp.cuota_suma)}`);
            app.render('balance_mora_monto', `S/ ${ app.money_format(resp.mora_suma) }`);
			app.render('balance_adicional', `S/ ${app.money_format(resp.adicional_suma)}`);
            app.render('balance_descuento', `S/ ${app.money_format(resp.descuento_suma)}`);
            app.render('balance_utilidad_neta', `S/ ${ app.money_format(resp.utilidad_neta) }`);

        } catch(e) {
            // statements
            console.log(e);
        }
    }
	async function Module_GeneracionRecuperacionDia(){
        try {
            const dt = new Date();
            const request = await fetch(srv.url()+'AnalisisRest/service_generacion_recuperacion', {
                method: 'POST',
                body: JSON.stringify({mode: 'FECHA_ACTUAL'}),
                headers: {
                    'Content-Type': 'application/json'
                }
            });
            const resp = await request.json();
			logs.gen_vs_rec_dia = [].concat(resp.recupera_log, resp.genera_log, resp.remate_log);
			

            app.render('dia_generacion_cantidad', resp.genera_cantidad+' Registros');
            app.render('dia_generacion_monto', `S/ ${ app.money_format(resp.genera_monto) }`);
            app.render('dia_recuperado_cantidad', resp.recupera_cantidad+' Recuperaciones');
            app.render('dia_recuperado_monto', `S/ ${ app.money_format(resp.recupera_monto) }`);
            app.render('dia_remate_cantidad', resp.remate_cantidad+' Rematados');
            app.render('dia_remate_monto', `S/ ${ app.money_format(resp.remate_monto) }`);

            var diff_class = resp.diferencia > 0 ? 'text-greenbook' : 'text-danger';
            app.render('dia_diferencia_monto', `S/ ${ app.money_format(resp.diferencia) }`);
            app.css_add('diferencia_monto', diff_class);
            // app.render('filter-current-viewer', ` ${resp.ident_filtro} `)

        } catch(e) {
            // statements
            console.log(e);
        }
    }
    async function recognizeFilter(){
        let loader = d.getElementById('typefilter-loader');
        let label = d.getElementById('typefilter');
        try {
            loader.classList.remove('d-none');
            label.innerHTML = '';

            let req = await fetch(srv.url()+'AnalisisRest/service_regognize_filter',{
                method: 'POST',
                body: JSON.stringify(filter_data)
            });
            let res = await req.json();
            if (!res || res.status!='OK' || Object.keys(res).length <= 0) {
                throw "Err";
            }
            setTimeout(() => {
                loader.classList.add('d-none');
                label.innerHTML = res.response;
            }, 1000);
            
        } catch (error) {
            console.log('error: '+error.message)
            loader.classList.add('d-none');
        }
    }



    // Ut
    function RenderLoader(act, t = 0){
        var loader = d.getElementById('loader-integrated');
        if (act === 'start') {
            loader.classList.add('d-block');
            if (t > 0) {
                setTimeout(() => {
                    loader.classList.remove('d-block');
                }, t);
            }
        } else if(act === 'close') {
            loader.classList.remove('d-block');
        }     
    }
    function ReedixFilter(){
		let main_area = null;
		let inputs = [];
        switch (filter_data.mode) {
            case 'FECHA':
				main_area = d.getElementById('section_filter_fecha');
				inputs = main_area.querySelectorAll('input');
				inputs.forEach(inp => { inp.value = "" })
                d.getElementById('section_filter_fecha').classList.remove('d-hide');
                d.getElementById('section_filter_rango').classList.add('d-hide');
                d.getElementById('section_filter_anio').classList.add('d-hide');
                d.getElementById('section_filter_mes').classList.add('d-hide');
                d.getElementById('section_filter_actual').classList.add('d-hide');
                break;
            case 'RANGO':
				main_area = d.getElementById('section_filter_rango');
				inputs = main_area.querySelectorAll('input');
				inputs.forEach(inp => { inp.value = "" })
                d.getElementById('section_filter_rango').classList.remove('d-hide');
                d.getElementById('section_filter_fecha').classList.add('d-hide');
                d.getElementById('section_filter_anio').classList.add('d-hide');
                d.getElementById('section_filter_mes').classList.add('d-hide');
                d.getElementById('section_filter_actual').classList.add('d-hide');
                break;
            case 'ANIO':
                d.getElementById('section_filter_anio').classList.remove('d-hide');
                d.getElementById('section_filter_fecha').classList.add('d-hide');
                d.getElementById('section_filter_rango').classList.add('d-hide');
                d.getElementById('section_filter_mes').classList.add('d-hide');
                d.getElementById('section_filter_actual').classList.add('d-hide');
                break;
            case 'MES':
                d.getElementById('section_filter_mes').classList.remove('d-hide');
                d.getElementById('section_filter_anio').classList.remove('d-hide');
                d.getElementById('section_filter_fecha').classList.add('d-hide');
                d.getElementById('section_filter_rango').classList.add('d-hide');
                d.getElementById('section_filter_actual').classList.add('d-hide');
                break;
            case 'MES_ACTUAL':
                d.getElementById('section_filter_mes').classList.add('d-hide');
                d.getElementById('section_filter_anio').classList.add('d-hide');
                d.getElementById('section_filter_fecha').classList.add('d-hide');
                d.getElementById('section_filter_rango').classList.add('d-hide');
                d.getElementById('section_filter_actual').classList.remove('d-hide');
                break;
            default:
                d.getElementById('section_filter_mes').classList.add('d-hide');
                d.getElementById('section_filter_anio').classList.add('d-hide');
                d.getElementById('section_filter_fecha').classList.add('d-hide');
                d.getElementById('section_filter_rango').classList.add('d-hide');
                d.getElementById('section_filter_actual').classList.add('d-hide');
                break;
        }
    }
    function FiltersReset(){
        d.getElementById('section_filter_mes').classList.add('d-hide');
        d.getElementById('section_filter_anio').classList.add('d-hide');
        d.getElementById('section_filter_fecha').classList.add('d-hide');
        d.getElementById('section_filter_rango').classList.add('d-hide');
        d.getElementById('section_filter_actual').classList.add('d-hide');
    }
    // 
    function updateLogTable(columns){
        let header = d.getElementById('LOG_TABLE_HEADER');
        header.innerHTML = '';
        header.innerHTML += columns.map(item => {
            return `<th>${item}</th>`
        }).join('');
    } 

    // EXPORTATION

    function processDataExport(data_){
        
    }
    function exportData(data_, name_){
        var worksheet = XLSX.utils.json_to_sheet(data_);
        var workbook = {
            Sheets: {
                'data': worksheet
            },
            SheetNames: ['data']
        };
        var excelBuffer = XLSX.write(workbook, {bookType: 'xlsx', type: 'array'})
        excel_export_file(excelBuffer, `Electrocash - ${name_}`);

    }    
    function excel_export_file(buffer, filename) {
		var data = new Blob([buffer], {type: EXCEL_TYPE});
		saveAs(data,filename+'_'+EXCEL_EXTENSION);
	}



})(document);