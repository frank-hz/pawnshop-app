(function(d,w){
    const EXCEL_TYPE = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;charset=UTF-8';
    const EXCEL_EXTENSION = '.xlsx';
    let app;
    let gridTable;
    let  grid_config = {
		paginacion: {
			activo: true,
			cantidad: 15
		}
	}
    const INPUT = { };
    const FILTER = { };
    const TIPO_ACTIONS = { };
    const searchParams = {
        tipo: '',
        categoria: '',
        marca: '',
        modelo: '',
        serie: '',
        quilate: '',
        placa: '',
        color: '',
        descripcion: '',
        limite: 50
    }
    let DataList = [];
    let DataExport = [];
    let DataColumns = [
        "#", "Descripcion", "Propietario", "Categoria", 
        "Comentario", "Estado"
    ];

    d.addEventListener('DOMContentLoaded', ()=>{
        app = new App();

        INPUT.tipo = d.getElementById('input_tipo');
        INPUT.categoria = d.getElementById('input_categoria');
        INPUT.marca = d.getElementById('input_marca');
        INPUT.quilate = d.getElementById('input_quilate');
        INPUT.muestra = d.getElementById('input_muestra');

        
        FILTER.categoria = d.getElementById('FILTER_CATEGORIA'),
        FILTER.marca = d.getElementById('FILTER_MARCA'),
        FILTER.modelo = d.getElementById('FILTER_MODELO'),
        FILTER.serie = d.getElementById('FILTER_SERIE'),
        FILTER.quilate = d.getElementById('FILTER_QUILATE'),
        FILTER.placa = d.getElementById('FILTER_PLACA'),
        FILTER.color = d.getElementById('FILTER_COLOR')
        
        TIPO_ACTIONS.ELECTRO = ()=>{
            FILTER.marca.classList.remove('d-none');
            FILTER.modelo.classList.remove('d-none');
            FILTER.serie.classList.remove('d-none');
            renderMarcas();
        };
        TIPO_ACTIONS.JOYA = ()=>{
            FILTER.quilate.classList.remove('d-none');
            renderQuilates();
        };
        TIPO_ACTIONS.VEHICULO = ()=>{
            FILTER.marca.classList.remove('d-none');
            FILTER.modelo.classList.remove('d-none');
            FILTER.placa.classList.remove('d-none');
            renderMarcas();
        };

        renderTipoPrendaData();
        hideFilters();

        renderPrendasData();        
    })

    d.getElementById('btnResetFilters')?.addEventListener('click', evt => {
        resetFilters();
    })

    d.getElementById('input_tipo')?.addEventListener('change', evt => {
        hideFilters();
        if (evt.target?.value){
            searchParams.tipo = evt.target.value;
            FILTER.categoria.classList.remove('d-none');
            TIPO_ACTIONS[evt.target?.value]();
            
            renderCategoriaData(evt.target.value);
            setTimeout(() => {
                renderPrendasData(1);  
            }, 150);
        }
    })
    d.getElementById('input_categoria')?.addEventListener('change', evt => {
        if (evt.target?.value){
            searchParams.categoria = evt.target.value;
            setTimeout(() => {
                renderPrendasData(1);  
            }, 150);
        }
    })
    d.getElementById('input_marca')?.addEventListener('change', evt => {
        if (evt.target?.value){
            searchParams.marca = evt.target.value;
            console.log('marca..');
            setTimeout(() => {
                renderPrendasData(1);  
            }, 150);
        }
    })
    d.getElementById('input_modelo')?.addEventListener('input', evt => {
        if (evt.target?.value){
            searchParams.modelo = evt.target.value;
            setTimeout(() => {
                renderPrendasData(1);  
            }, 150);
        }
    })
    d.getElementById('input_serie')?.addEventListener('input', evt => {
        if (evt.target?.value){
            searchParams.serie = evt.target.value;
            setTimeout(() => {
                renderPrendasData(1);  
            }, 150);
        }
    })
    d.getElementById('input_quilate')?.addEventListener('change', evt => {
        if (evt.target?.value){
            searchParams.quilate = evt.target.value;
            setTimeout(() => {
                renderPrendasData(1);  
            }, 150);
        }
    })
    d.getElementById('input_placa')?.addEventListener('input', evt => {
        if (evt.target?.value){
            searchParams.placa = evt.target.value;
            setTimeout(() => {
                renderPrendasData(1);  
            }, 150);
        }
    })
    d.getElementById('input_color')?.addEventListener('change', evt => {
        if (evt.target?.value){
            searchParams.color = evt.target.value;
            setTimeout(() => {
                renderPrendasData(1);  
            }, 150);
        }
    })
    d.getElementById('input_descripcion')?.addEventListener('input', evt => {
        if (evt.target){
            searchParams.descripcion = evt.target.value;
            setTimeout(() => {
                renderPrendasData(1);  
            }, 150);
        }
    })
    d.getElementById('input_muestra')?.addEventListener('change', evt => {
        if (evt.target?.value){
            searchParams.limite = parseInt(evt.target.value);
            setTimeout(() => {
                renderPrendasData(1);  
            }, 150);
        }
    })

    d.getElementById('btn_export')?.addEventListener('click', evt => {
        evt.target.disabled = true;
        var worksheet = XLSX.utils.json_to_sheet(DataExport);
        var workbook = {
            Sheets: {
                'data': worksheet
            },
            SheetNames: ['data']
        };
        var excelBuffer = XLSX.write(workbook, {bookType: 'xlsx', type: 'array'})
        excel_export_file(excelBuffer, 'Electrocash - Prendas');

        evt.target.disabled = false;

    })


    async function renderCategoriaData(tipo_){
        try {
            const data = new FormData();
            data.append("tipo", tipo_);
            const req = await fetch(app.URL("api/v1/categoria/getbytipo"), {
                method: 'POST',
                body: data
            });
            const resp = await req.json();
            if (Array.isArray(resp) && resp?.length > 0){
                INPUT.categoria.innerHTML = `
                    <option value="">-SELECCIONAR-</option>
                `; 
                INPUT.categoria.innerHTML += resp.map(categoria => `
                    <option value="${categoria.id}">${categoria.nombre}</option>
                `).join('');
            }
        } catch (error) {
            console.log(`[Categorias] ${error.message}`);
        }
    }

    async function renderTipoPrendaData(){
        try {
            const req = await fetch(app.URL("api/v1/tipoprenda/getall"), {
                method: 'POST'
            });
            const resp = await req.json();
            if (Array.isArray(resp) && resp?.length > 0){
                INPUT.tipo.innerHTML = '<option value="">-SELECCIONAR-</option>'; 
                INPUT.tipo.innerHTML += resp.map(tipo => `
                    <option value="${tipo.nombre}">${tipo.nombre}</option>
                `).join('');
                return;
            }
            
            not.Aviso("","",2500);
            return;

        } catch (error) {
            console.log(`[Tipos] ${error.message}`);
        }
    }

    function hideFilters(){
        FILTER.categoria.classList.add('d-none');
        FILTER.marca.classList.add('d-none');
        FILTER.modelo.classList.add('d-none');
        FILTER.serie.classList.add('d-none');
        FILTER.quilate.classList.add('d-none');
        FILTER.placa.classList.add('d-none');
        FILTER.color.classList.add('d-none');
    }

    function resetFilters(){
        searchParams.tipo = '';
        searchParams.categoria = '';
        searchParams.marca = '';
        searchParams.modelo = '';
        searchParams.serie = '';
        searchParams.quilate = '';
        searchParams.placa = '';
        searchParams.color = '';
        searchParams.descripcion = '';
        searchParams.limite = 50;

        INPUT.descripcion = '';
        INPUT.marca = '';
        INPUT.categoria = '';
        INPUT.quilate = '';
        INPUT.modelo = '';
        INPUT.serie = '';
        INPUT.muestra = '';
    }

    async function getPrendasData(){
        try {
            const paramsData = app.jsonToFormData(searchParams);
            const request = await fetch(app.URL('api/v2/prenda/getAll'), {
                method: 'POST',
                body: paramsData
            });
            const resp = await request.json();
            if (!Array.isArray(resp)){
                return;
            }
            
            DataExport = resp.map(prenda => {
                return {
                    Descripcion: prenda.descripcion,
                    Propietario: prenda.cliente_nombre,
                    Tipo: prenda.tipo_nombre,
                    Categoria: prenda.categoria_nombre,
                    Marca: prenda.marca,
                    Modelo: prenda.modelo,
                    Serie: prenda.serial,
                    Quilate: prenda.quilate,
                    Peso: prenda.peso,
                    Kilometraje: prenda.kilometraje,
                    Placa: prenda.placa,
                    Valoracion: prenda.valoracion,
                    Comentario: prenda.comentario,
                    Observacion: prenda.observacion,
                    Estado: prenda.estado
                };
            });
            DataList = resp.map((prenda, index) => {
                let propietarioLink = gridjs.html(`
                    <a href="${app.URL("cliente/detalle/"+prenda.cliente_id)}" class="btn-link">
                        ${prenda.cliente_nombre}
                    </a> 
                `);
                let detalleLink = gridjs.html(`
                    <a href="${app.URL("prenda/detalle/"+prenda.id)}" class="btn-link">
                        ${prenda.descripcion}
                    </a> 
                `);
                let estadoTag = {
                    'A': '<span class="tagger-prenda-A">Remate</span>',
                    'R': '<span class="tagger-prenda-R">Recuperado</span>',
                    'O': '<span class="tagger-prenda-O">Ocupado</span>',
                    'D': '<span class="tagger-prenda-D">Disponible</span>',
                }

                let data_base = [
                    index+1,
                    prenda.descripcion,
                    propietarioLink,
                    prenda.categoria_nombre,
                    prenda.comentario,
                    gridjs.html(estadoTag[prenda.estado])
                ];

                switch (searchParams.tipo) {
                    case 'ELECTRO':
                        DataColumns = [
                            "#", "Descripcion", "Propietario", "Categoria", 
                            "Marca", "Modelo", "Serie", "Comentario", "Estado"
                        ];
                       
                        data_base = [
                            index+1,
                            detalleLink,
                            propietarioLink,
                            prenda.categoria_nombre,
                            prenda.marca,
                            prenda.modelo,
                            prenda.serial,
                            prenda.comentario,
                            gridjs.html(estadoTag[prenda.estado])
                        ];
                        break;
                    case 'JOYA':
                        DataColumns = [
                            "#", "Descripcion", "Propietario", "Categoria", 
                            "Quilate", "Peso","Comentario", "Estado"
                        ];

                        data_base = [
                            index+1,
                            prenda.descripcion,
                            propietarioLink,
                            prenda.categoria_nombre,
                            prenda.quilate,
                            prenda.peso,
                            prenda.comentario,
                            gridjs.html(estadoTag[prenda.estado])
                        ];
                        break;
                    case 'VEHICULO':
                        DataColumns = [
                            "#", "Descripcion", "Propietario", "Categoria", 
                            "Marca", "Modelo", "Placa", "Kilometraje",
                            "Comentario", "Estado"
                        ];

                        data_base = [
                            index+1,
                            prenda.descripcion,
                            propietarioLink,
                            prenda.categoria_nombre,
                            prenda.marca,
                            prenda.modelo,
                            prenda.placa,
                            prenda.kilometraje,
                            prenda.comentario,
                            gridjs.html(estadoTag[prenda.estado])
                        ];
                        break;
                    default:
                        break;
                }
                return data_base;
            })

        } catch (error) {
            console.log("[PrendaList] "+error.message);
        }
    }

    async function renderPrendasData(update = false){
        try {
            // loader.open();
            await getPrendasData();      
            if (update) {
                gridTable.updateConfig({
                    columns: DataColumns,
                    data: DataList
                }).forceRender();
                return;
            }
            gridTable = new gridjs.Grid({
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
						'font-weight': '800',
						'padding': '6px 10px',
						'word-wrap': 'break-all',
						'color': '#0f1f31'
					},
					td: {
						'box-shadow': 'none',
						'font-size': '.94rem',
						'padding': '6px 8px',
						'color': '#212a49'
					}
				}
			}).render(d.getElementById("TABLE_PRENDAS"));
        } catch (error) {
            console.log(error)
        }
    }

    async function renderMarcas(){
        try {
            const data = new FormData();
            data.append("tipo", searchParams.tipo);
            const request = await fetch(app.URL("api/v1/prenda/getAllMarcas"), {
                method: 'POST',
                body: data
            });
            const resp = await request.json();
            console.log(resp.length);

            INPUT.marca.innerHTML = `<option value="">-SELECCIONAR-</option>`
            if (!Array.isArray(resp)){
                return;
            }

            if (resp.length > 0){
                INPUT.marca.innerHTML += resp.map((item,ind) => `
                    <option value="${item.marca}">${item.marca }</option>    
                `)
            }

        } catch (error) {
            console.log(`[Marcas] ${error.message}`);
        }
    }

    async function renderQuilates(){
        try {
            const data = new FormData();
            data.append("tipo", searchParams.tipo);
            const request = await fetch(app.URL("api/v1/prenda/getAllQuilates"), {
                method: 'POST',
                body: data
            });
            const resp = await request.json();

            INPUT.quilate.innerHTML = `<option value="">-SELECCIONAR-</option>`
            if (!Array.isArray(resp)){
                return;
            }

            if (resp.length > 0){
                INPUT.quilate.innerHTML += resp.map(item => `
                    <option value="${item.quilate}">${item.quilate}</option>    
                `)
            }

        } catch (error) {
            console.log(`[Quilates] ${error.message}`);
        }
    }
    
    function excel_export_file(buffer, filename) {
		var data = new Blob([buffer], {type: EXCEL_TYPE});
		saveAs(data,filename+'_'+EXCEL_EXTENSION);
	}

})(document, window);