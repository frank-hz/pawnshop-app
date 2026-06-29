(function(d,w){
    const EXCEL_TYPE = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;charset=UTF-8';
    const EXCEL_EXTENSION = '.xlsx';
    let app;
    let mdl_notacredito;
    let StateLabel = {
        'E': 'Activo',
        'A': 'Remate',
        'R': 'Recuperado',
        'CC': 'Congelado'
    };
    let DataList = [];
    let DataColumns = [
        "#", "Codigo", "Creacion", "Cliente", "Monto", 
        "Cuotas Venc.", "Prenda", "Estado", "Opcion"
    ];    
    let DataExport = [];
    let loader = {
        open: () => {
            d.getElementById('tablespace_loader').style.visibility = 'visible';
        },
        close: () => {
            d.getElementById('tablespace_loader').style.visibility = 'hidden';
        }
    };
    let grid_table;
    let  grid_config = {
		paginacion: {
			activo: true,
			cantidad: 15
		}
	}
    let FilterSettings = {
        order: [
            {
                target: "CODIGO",
                type: "DESC",
                limit: 0
            }
        ],
        search_default: {
            target: "CODIGO",
            value: ""
        },        
        search_custom: [
            {
                id: 1,
                target: 'ESTADO',
                value: 'E',
                label: 'Estado: Activo'
            }
        ],
        search_prenda: '',
        search_categoria: 0
    }
    let local = {
        idSelected: 0,
        serieSelected: '',
		notas_limit_view: 4
	}

    d.addEventListener('DOMContentLoaded', async()=>{
        app = new App();
        mdl_notacredito = new NotasCredito();
        renderFilterCustom();
        await renderCategorias();
		await renderCreditosData();
		
		let inpx = d.getElementById('filter_search_value');
		if (inpx){
		    inpx.focus();
		}
    })
    d.addEventListener('change', (event)=>{
        let ev = event.target;
        if (ev.matches('#filter_search_target')) {
            FilterSettings.search_default.target = ev.value;
            setTimeout(() => {
                renderCreditosData(1);  
            }, 150);
        }
        if (ev.matches('#filter_order_target')) {
            FilterSettings.order[0].target = ev.value;
            setTimeout(() => {
                renderCreditosData(1);  
            }, 150);
        }
        if (ev.matches('#filter_order_type')) {
            FilterSettings.order[0].type = ev.value;
            setTimeout(() => {
                renderCreditosData(1);  
            }, 150);
        }
        if (ev.matches('#filter_limit')) {
            FilterSettings.order[0].limit = ev.value;
            setTimeout(() => {
                renderCreditosData(1);  
            }, 150);
        }
        if (ev.matches('#filter_estado')) {
            let input_search_prenda = d.getElementById('group_search_prenda');
            input_search_prenda.classList.toggle('d-none',1);

            if (ev.value === 'E'){
                d.getElementById('filter_limit').value = 0;
                FilterSettings.order[0].limit = 0;
            }

            let label = (ev.value) ? ev.options[ev.selectedIndex].text : '---';
            FilterSettings.search_custom[0].value = ev.value;
            FilterSettings.search_custom[0].label = `Estado: ${label}`; 
            if (['A'].includes(ev.value)){
                input_search_prenda.classList.toggle('d-none',0);
            }       
            renderFilterCustom();
            setTimeout(() => {
                renderCreditosData(1);  
            }, 150);
        }
        if (ev.matches('[name=custom_filter_type]')) {
            if (!ev.dataset.ref) {
                not.Aviso('Error al procesar', '', 2000);
                return;
            }
            renderCustomValueInput(ev);
        }
        if (ev.matches('#filter_select_categoria')) {
            FilterSettings.search_categoria = ev.value;
            setTimeout(() => {
                renderCreditosData(1);  
            }, 150);
        }
        if (ev.matches('[name=custom_filter_value]')) {
            // if (!ev.dataset.target || ev.value.length <= 0){
            //     not.Aviso('Valor no permitido o erroneo','',1500);
            //     return;                
            // }
            // let label = ev.value;
            // let target = ev.dataset.target;
            // let value = ev.value;
            // if (ev.tagName === 'SELECT'){
            //     label = ev.options[ev.selectedIndex].text;
            // }
            // let verifyDuplicity = FilterSettings.search_custom.filter(item => {
            //     return item.target === target && item.value === value
            // })
            // if (verifyDuplicity.length){
            //     not.Aviso('Filtro no permitido o duplicado','',2000);
            //     return;
            // }
            // FilterSettings.search_custom.push({
            //     id: (FilterSettings.search_custom.length + 1),
            //     target: ev.dataset.target,
            //     value: ev.value,
            //     label: `${ev.dataset.target}: ${label}`,
            //     removable: true
            // });
            // renderFilterCustom();
            // renderCreditosData(1)
        }
        
    })
    d.addEventListener('input', (event)=>{
        let ev = event.target;
        if (ev.matches('#filter_search_value')) {
            FilterSettings.search_default.value = ev.value;
            setTimeout(() => {
                renderCreditosData(1);
            }, 150);
        }
        if (ev.matches('#filter_search_prenda')) {
            FilterSettings.search_prenda = ev.value;
            setTimeout(() => {
                renderCreditosData(1);
            }, 150);
        }
    })
    d.addEventListener('click', async event => {
        let ev = event.target;
        if (ev.matches('#btnResetFilters')){
            FilterSettings.order = [
                {
                    target: "CODIGO",
                    type: "DESC",
                    limit: 50
                }
            ];
            FilterSettings.search_default = {
                target: "CODIGO",
                value: ""
            };
            FilterSettings.search_custom = [
                {
                    id: 1,
                    target: 'ESTADO',
                    value: 'E',
                    label: 'Estado: Activo'
                }
            ];
            FilterSettings.search_prenda = '';
            FilterSettings.search_categoria = 0;
            renderCreditosData(true);
            d.getElementById('filter_order_target').value = 'CODIGO';
            d.getElementById('filter_order_type').value = 'DESC';
            d.getElementById('filter_limit').value = 50;
            d.getElementById('filter_estado').value = 'E';
            d.getElementById('filter_select_categoria').value = '';
            d.getElementById('filter_search_value').value = '';
            d.getElementById('group_search_prenda').classList.toggle('d-none', 1);

        }
        if (ev.matches('#modal_btn_new_nota')){
            if (local.idSelected){
                loadNotaForm();
            }
        }
        if (ev.matches('#modal_btn_back_nota')){
            if (local.idSelected){
                $('#modal_nota_new').modal('hide');
                loadNotaList();
            }
        }
        if (ev.matches('[name=credito_notas_detalle]')){
            if (!ev.dataset.id || !ev.dataset.serie) {
                not.Aviso('Credito no identificado','',2000);
                return;
            }
            local.idSelected = ev.dataset.id;
            local.serieSelected = ev.dataset.serie;            
            loadNotaList();
        }
        if (ev.matches('[name=nota_delete]')){
            if (ev.dataset.id && local.idSelected){
                let confirmation = confirm('Eliminar nota?');
                if (!confirmation) return;
                $('#modal_nota_new').modal('hide');
                loadNotaList();
                let deletd = await deleteNota(ev.dataset.id);                
                if (!deletd) {                    
                    not.Aviso('El proceso no se completo', '', 2200);	
                    return;		
                }
                not.Ok('Nota eliminada', '', 2200);	
                loadNotaList();
                renderCreditosData(true);
            }
        }
        if (ev.matches('#btn_export_excel')) {
			var worksheet = XLSX.utils.json_to_sheet(DataExport);
			var workbook = {
				Sheets: {
					'data': worksheet
				},
				SheetNames: ['data']
			};
			var excelBuffer = XLSX.write(workbook, {bookType: 'xlsx', type: 'array'})
			excel_export_file(excelBuffer, 'Electrocash - Lista de Creditos')
			
		}
        if (ev.matches('#btn_open_modal_filter')) {
            $("#collapseStart").collapse('show');
            $("#collapseState").collapse('hide');
            $("#collapseDate").collapse('hide');
            $("#collapseDateRange").collapse('hide');

            d.getElementById('FormFilterCustom').reset();
            let cells = d.querySelectorAll('[name=custom_filter_value_cell]');
            if (cells.length){
                cells.forEach(e => {
                    e.innerHTML = '';
                })
            }
            		
			$("#modal_add_filter").modal('show');	
		}
        if (ev.matches('[name=filter_remove_item]')){
            if (!ev.dataset.id) return;
            if(confirm(`¿Eliminar (${ev.dataset.label})?`)){
                if (!RemoveCustomFilter(ev.dataset.id)){
                    not.Aviso('No se pudo eliminar el filtro')
                    return;
                }
                renderFilterCustom();
                renderCreditosData(1);
            }
        }
    })
    d.addEventListener('submit', async event => {
		event.preventDefault()
		let ev = event.target;
		if (ev.matches('#form_nota_new')) {			
			let data = {
				credito: local.idSelected,
				descripcion: ev.descripcion.value,
				tipo: ev.tipo.value
			}
            if (!validateSaveNota(data)) {
                not.Aviso('Error al procesar', '', 2200);
                return;
            }
			let save = await saveNota(data); 
            if (!save) {
                not.Aviso('El proceso no se completo', '', 2200);
                return;
            }
           d.getElementById('form_nota_new').reset();
           not.Ok('Datos Guardados', '', 2200);
           $('#modal_nota_new').modal('hide');
           loadNotaList();
           renderCreditosData(true);
		}
        if (ev.matches('#FormFilterCustom')){
            ProcessCustomFilterForm();
        }
	})




    // F U N C T I O N S

    // obtiene y almacena el listado de creditos
    async function getCreditosData_bk(){
        try {
            loader.open();
            DataList = [];
            DataExport = [];
            DataColumns = [
                "#", "Codigo", "Creacion", "Cliente", "Monto", 
                "Cuotas Venc.", "Prenda", "Estado", "Opcion"
            ];

            let request = await fetch(srv.url()+'api/v1/credito/get-all',{
                method: 'POST',
                body: JSON.stringify(FilterSettings),
                headers: {
                    'Content-Type': 'application/json'
                }
            });
            let response = await request.json();
            if (response.ok && response.result) {
                // response.result.forEach(e => {
                //     console.log(e.prenda_descripcion);
                // })
                // console.table(response.result);
                let ColumnLiquidacion = false;
                let ColumnCuotasPagadas = false;
                if (FilterSettings.search_custom.length) {
                    FilterSettings.search_custom.forEach(e => {
                        // if (['A','R'].includes(e.value)) ColumnLiquidacion = true;
                        if (e.value === 'A') {
                            ColumnLiquidacion = true;
                        }
                        if (e.value === 'R') {
                            ColumnCuotasPagadas = true;
                        }
                    });    
                }                
                DataList = response.result.map((credito,ind) => {
                    let prelist = [
                        ind+1,
                        gridjs.html(`
                            <a href="${srv.url()}Credito/detalle/${credito.id}" class="btn-link">
                                ${credito.serie}
                            </a>
                        `),
                        credito.fecha_creacion,
                        gridjs.html(`
                            <a href="${srv.url()}Cliente/detalle/${credito.cliente_id}" class="btn-link">
                                ${credito.cliente_nombre}
                            </a>
                        `),
                        'S/ '+credito.monto,
                        ()=>{
                            if (ColumnLiquidacion){
                                return credito.cuotas_vencidas
                            }else if (ColumnCuotasPagadas){
                                return credito.cuotas_pagadas
                            }else {
                               return ''
                            }
                        },
                        gridjs.html(`
                            <a href="${srv.url()}Prenda/detalle/${credito.prenda_id}" class="btn-link">
                                ${credito.prenda_descripcion}
                            </a>
                        `),
                        gridjs.html(credito.estado_tag),
                        gridjs.html(`
                            <div class="d-flex">
                                <a href="${srv.url()}Credito/detalle/${credito.id}" class="tablespace-button">
                                    <i class="fe fe-layers"></i
                                    >
                                </a>
                                <button 
                                    name="credito_notas_detalle" 
                                    class="tablespace-button" 
                                    data-id="${credito.id}" 
                                    data-serie="${credito.serie}"> 
                                    Notas (${credito.notas_cantidad})
                                </button>
                            </div>
                        `)
                    ];
                    if (ColumnLiquidacion) {
                        prelist.splice(3,0,credito.fecha_liquidacion);
                    }
                    if (ColumnCuotasPagadas) {
                        prelist.splice(3,0,credito.fecha_liquidacion);
                    }
                    return prelist;
                });
                DataExport = response.result.map((credito,ind) => {
                    let explist = {
						"Credito": credito.serie,
						"Fecha Registro": credito.fecha_creacion,
                        "Fecha Liquidacion": credito.fecha_liquidacion,
						"Cliente": credito.cliente_nombre,
						"Monto": credito.monto,
						"Cuotas Vencidas": credito.cuotas_vencidas,
                        "Cuotas Pagadas": credito.cuotas_pagadas,
						"Estado": credito.estado,
						"Prenda": credito.prenda_descripcion,
                        "Marca": credito.prenda_marca,
                        "Modelo": credito.prenda_modelo,
                        "Serial": credito.prenda_serial,
                        "Peso": credito.prenda_peso,
                        "Año": credito.prenda_anio,
                        "Valoracion Estado": credito.prenda_valoracion_estado,
                        "Observaciones": credito.prenda_observacion,
                        "Comentarios": credito.prenda_comentario,
                        "Estado": credito.prenda_estado
                    };
                    if (ColumnLiquidacion) {
                        explist["Liquidacion"] = credito.fecha_liquidacion
                        
                    }
                    return explist;
                })                
                if (ColumnLiquidacion) {
                    // DataColumns.splice(3,0,'Liquidacion');
                    DataColumns.splice(3,0,{
                        id: 'Liquidacion',
                        name: gridjs.html(`<span style="color: #467fcf;font-weight:bold">Liquidacion</span>`)
                    });
                }
                if (ColumnCuotasPagadas) {
                    DataColumns[6] = "Cuotas Pagadas"
                }
            }
            if (response.error) {
                not.Aviso(response.error,'', 2000);
            }
            setTimeout(() => {
                loader.close();
            }, 300);
        } catch (error) {
            DataList = [];
            console.log(error)
        }
    }

    async function getCreditosData(){
        try {
            loader.open();
            DataList = [];
            DataExport = [];
            DataColumns = [
                "#", "Codigo", "Creacion", "Cliente", "Monto", 
                "Cuotas Venc.", "Prenda", "Estado", "Opcion"
            ];

            let request = await fetch(srv.url()+'api/v1/credito/get-all',{
                method: 'POST',
                body: JSON.stringify(FilterSettings),
                headers: {
                    'Content-Type': 'application/json'
                }
            });
            let response = await request.json();
            if (!response.ok || !response.result) {
                not.Aviso('Error al procesar','', 2000);
                return;
            }
            let ColumnLiquidacion = false;
            let stateRemate = false;
            let stateRecuperado = false;
            if (FilterSettings.search_custom.length) {
                FilterSettings.search_custom.forEach(e => {
                    if (['A','R'].includes(e.value)) ColumnLiquidacion = true;
                    if (e.value === 'A') {
                        stateRemate = true;
                    }
                    if (e.value === 'R') {
                        stateRecuperado = true;
                        DataColumns[5] = "Cuotas Pagadas";
                    }
                });    
            }
            if (ColumnLiquidacion) {
                DataColumns.splice(3,0,{
                    id: 'Liquidacion',
                    name: gridjs.html(`<span style="color: #467fcf;font-weight:bold">Liquidacion</span>`)
                });
            }
            
            DataList = await response.result.map((credito,ind) => {
                let prendaLink = credito.prenda_id 
                    ? `
                        <a href="${srv.url()}Prenda/detalle/${credito.prenda_id}" class="btn-link">
                            ${credito.prenda_descripcion}
                        </a>
                    `
                    : `Sin Garantia` 
                let prelist = [
                    ind+1,
                    gridjs.html(`
                        <a href="${srv.url()}Credito/detalle/${credito.id}" class="btn-link">
                            ${credito.serie}
                        </a>
                    `),
                    credito.fecha_creacion,
                    gridjs.html(`
                        <a href="${srv.url()}Cliente/detalle/${credito.cliente_id}" class="btn-link">
                            ${credito.cliente_nombre}
                        </a>
                    `),
                    'S/ '+credito.monto,
                    (stateRecuperado) ? credito.cuotas_pagadas : credito.cuotas_vencidas,
                    gridjs.html(prendaLink),
                    gridjs.html(credito.estado_tag),
                    gridjs.html(`
                        <div class="d-flex">
                            <a href="${srv.url()}Credito/detalle/${credito.id}" class="tablespace-button">
                                <i class="fe fe-layers"></i
                                >
                            </a>
                            <button 
                                name="credito_notas_detalle" 
                                class="tablespace-button" 
                                data-id="${credito.id}" 
                                data-serie="${credito.serie}"> 
                                Notas (${credito.notas_cantidad})
                            </button>
                        </div>
                    `)
                ];
                if (ColumnLiquidacion) {
                    prelist.splice(3,0,credito.fecha_liquidacion);
                }
                return prelist;
            })

            // E X P O R T 
            DataExport = response.result.map((credito,ind) => {
                let explist = {
                    "Credito": credito.serie,
                    "Fecha Registro": credito.fecha_creacion,
                    "Fecha Liquidacion": credito.fecha_liquidacion,
                    "Cliente": credito.cliente_nombre,
                    "Monto": credito.monto,
                    "Cuotas Vencidas": credito.cuotas_vencidas,
                    "Cuotas Pagadas": credito.cuotas_pagadas,
                    "Estado": credito.estado,
                    "Prenda": credito.prenda_descripcion,
                    "Marca": credito.prenda_marca,
                    "Modelo": credito.prenda_modelo,
                    "Serial": credito.prenda_serial,
                    "Peso": credito.prenda_peso,
                    "Año": credito.prenda_anio,
                    "Valoracion Estado": credito.prenda_valoracion_estado,
                    "Observaciones": credito.prenda_observacion,
                    "Comentarios": credito.prenda_comentario,
                    "Estado": credito.prenda_estado
                };
                if (ColumnLiquidacion) {
                    explist["Liquidacion"] = credito.fecha_liquidacion
                    
                }
                return explist;
            })
            setTimeout(() => {
                loader.close();
            }, 300);            
        } catch (error) {
            DataList = [];
            console.log(error)
        }
    }

    // renderiza en tabla la lista de creditos
    async function renderCreditosData(update = false){
        try {
            loader.open();
            await getCreditosData();      
            if (update) {
                grid_table.updateConfig({
                    columns: DataColumns,
                    data: DataList
                }).forceRender();
                return;
            }
            grid_table = new gridjs.Grid({
				columns: DataColumns,
                data: DataList,
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
				}
			}).render(d.getElementById("tablespace_creditos"));
        } catch (error) {
            console.log(error)
        }
    }



    // obtiene y renderiza las notas del creditos seleccionado 
    async function renderNotasCredito(id){
		try {
			// local.idSelected = id;
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
									<button class="modal-table-button" name="nota_delete" data-id="${e.id}" data-credito="${e.credito_id}" title="Eliminar nota">
                                        
                                        <i class="fe fe-trash"></i>
                                    </button>
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

			d.getElementById('notas_dataset').innerHTML = html_resp;			
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
    // inicializa la carga y renderizado de datos
    async function loadNotaList(){
        d.getElementById('modal_nota_list_title').innerHTML = `
            ${local.serieSelected} - Lista de Notas
        `;
        $('#modal_nota_list').modal('show');
        renderNotasCredito(local.idSelected);
    }
    // inicializa el formulario de registro de notas
    async function loadNotaForm(){
        d.getElementById('modal_nota_new_title').innerHTML = `
            ${local.serieSelected} - Nueva Nota
        `;
        $('#modal_nota_list').modal('hide');
        $('#modal_nota_new').modal('show');
    }
    // valida los datos a registrarse
    function validateSaveNota(data){
		let estado = true;
		d.getElementById('nota-tipo-feed').innerHTML = '';
		d.getElementById('nota-descripcion-feed').innerHTML = '';
		if (!local.idSelected) {
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
    // almacena los datos de la nota
    async function saveNota(data){
		const req = await fetch(srv.url()+'NotaCredito/service_registro', {
			method: 'POST',
			body: JSON.stringify(data)
		})
		const res = await req.json();
        return (res.code === 'OK');
	} 
    // elimina los datos de una nota
    async function deleteNota(id){
		let data = new FormData();
		data.append('id', id);
		const req = await fetch(srv.url()+'NotaCredito/eliminar', {
			method: 'POST',
			body: data 
		})
		const res = await req.json();
        return (res.status === 'OK');
	} 


    async function renderFilterCustom(){
        let space = d.getElementById('filter_stash');
        space.innerHTML = '';
        let data = FilterSettings.search_custom;
        if (data.length > 0) {
            data.forEach(item => {
                space.innerHTML += `
                    <span class="tablespace-filter-tag">
                        <div class="tablespace-filter-tag-message">
                            ${item.label}
                        </div>
                        ${
                            item.removable 
                            ? `
                            <a href="javascript:void(0)" class="tablespace-filter-tag-close" data-id="${item.id}" 
                            name="filter_remove_item" data-label="${item.value}">
                                &#10006;
                            </a>
                            ` 
                            : `<div></div>`
                        }                        
                    </span>
                `;
            });
            return;
        }
        space.innerHTML = `
            <div class="px-2">Sin Filtros</div>
        `;
    }


    // elimina un elemento del arreglo de filtros personalizados
    function RemoveCustomFilter(id){
        let index = FilterSettings.search_custom.findIndex(item => item === id);
        return FilterSettings.search_custom.splice(index,1);
    }
    function ProcessCustomFilterForm(){
        try {
            let values = d.querySelectorAll('[name=custom_filter_value]');
            if (!values.length) {
                not.Dark({
                    title: 'No hay filtros que aplicar',
                    message: '',
                    time: 2000
                });
                $("#modal_add_filter").modal('hide');
                return;
            }          

            let stash = [];
            values.forEach(item => {
                let target = item.dataset.target;
                let value = item.value;

                let value_date = (!isNaN(new Date(item.value))) 
                    ? value.split('-').reverse().join('-') 
                    : '';
                let label_target = item.dataset.label;
                let label_value = (item.tagName === 'SELECT') 
                ? StateLabel[item.value] 
                : value_date;                


                let verifyDuplicity = FilterSettings.search_custom.filter(item => {
                    return item.target === target && item.value === value
                });
                if (verifyDuplicity.length){
                    not.Aviso('Filtro duplicado', '', 2000);
                    return;
                }
                stash.push({
                    id: (FilterSettings.search_custom.length + 1),
                    target: target.toUpperCase(),
                    value: value,
                    label: `
                        ${label_target}: ${label_value}
                    `,
                    removable: true
                });
            });
            if (stash.length){
                FilterSettings.search_custom = [].concat(
                    FilterSettings.search_custom,
                    stash
                );
                renderFilterCustom();
                renderCreditosData(1);
            }      
            $("#modal_add_filter").modal('hide');
            // console.log(FilterSettings.search_custom)          
        } catch (error) {
            not.Aviso('Error al procesar', '', 2000);
            console.log(error)
        }
    }
    function renderCustomValueInput(data){
        try {
            let ref = data.dataset.ref;
            let cell_container = d.getElementById('custom_filter_value_cell_'+ref);
            let html_stash = {
                'ESTADO': `
                    <div class="tablespace-filter-search tablespace-filter-search-right mr-1">
                        <select id="custom_filter_value_${ref}" name="custom_filter_value" data-target="${data.value}"  class="tablespace-search-input w-100" data-label="Estado">
                            <option value="">-TODO-</option>
                            <option value="E" data-label="Activo">Activo</option>
                            <option value="A" data-label="Remate">Remate</option>
                            <option value="R" data-label="Recuperado">Recuperado</option>
                            <option value="CC" data-label="Congelado">Congelado</option>
                        </select>
                        <div class="filter-search-icon">
                            <i class="fe fe-chevron-down"></i>
                        </div>
                    </div>
                `,
                'FECHA': `
                    <div class="tablespace-filter-search mr-1">
                        <input id="custom_filter_value_${ref}" type="date" data-target="${data.value}" data-label="Fecha" name="custom_filter_value"  class="tablespace-search-input w-100">
                        <div class="filter-search-icon">
                            <i class="fe fe-calendar-down"></i>
                        </div>
                    </div>
                `
            }
            cell_container.innerHTML = html_stash[data.value];
        } catch (error) {
            not.Aviso('Error al procesar', '', 2000);
        }
    }




    // export un archivo excel con los datos listados 
    function excel_export_file(buffer, filename) {
		var data = new Blob([buffer], {type: EXCEL_TYPE});
		// saveAs(data,filename+'_export_'+new Date().getTime()+EXCEL_EXTENSION);
		saveAs(data,filename+'_'+EXCEL_EXTENSION);
	}


    // obtener y mostrar categorias
    async function renderCategorias(){
        try {
            let req = await fetch(app.URL('api/Categoria/get_all'));
            let resp = await req.json();
            if (!resp){
                throw "Problema al obtener categorias";
            }
            let select = document.getElementById('filter_select_categoria');
            select.innerHTML = '<option value="">-TODO-</option>';
            resp.forEach(categoria => {
                let option = document.createElement('option');
                option.value = categoria.id;
                option.textContent = categoria.nombre;
                select.appendChild(option);
            })
        } catch (error) {
            console.log(error);
            return;
        }
    }


})(document,window);