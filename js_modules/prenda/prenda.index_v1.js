(function(){
    var app;
    var grid_global;
    var req_data = {
        'filtro': 'all',
        'buscar': '',
        'cantidad': 100
    }

    var dataset_setting = {
        'theme': 'vx-table',
        'sub_limit': 8,
        'sub_search': false,
        'header_static': false,
        'models': [
            {
                name: 'descripcion',
                check: true,
                title: 'Descripcion'
            },
            {
                name: 'cliente',
                check: true,
                title: 'Cliente'
            },
            {
                name: 'categoria',
                check: true,
                title: 'Categoria'
            },
            {
                name: 'tipo',
                check: true,
                title: 'Tipo de Prenda'
            },
            {
                name: 'marca',
                check: false,
                title: 'Marca'
            },
            {
                name: 'modelo',
                check: false,
                title: 'Modelo'
            },
            {
                name: 'quilate',
                check: false,
                title: 'Quilate'
            },
            {
                name: 'peso',
                check: false,
                title: 'Peso'
            },
            {
                name: 'observacion',
                check: false,
                title: 'Observacion'
            },
            {
                name: 'comentario',
                check: false,
                title: 'Comentario'
            },
            {
                name: 'monto_asignado',
                check: false,
                title: 'Monto Asignado'
            },
            {
                name: 'fecha_recupera',
                check: false,
                title: 'Fecha Recuperacion'
            }
        ],
        'columns':[
            'Descripcion', 'Cliente', 'Categoria', 'Tipo de Prenda',
            'Estado', 'Acciones'
        ],
        'data_raw': [],
        'data_pop': ['descripcion', 'cliente', 'categoria', 'tipo', 'estado', 'accion']
    }

    document.addEventListener('DOMContentLoaded', async () => {
        window.jsPDF = window.jspdf.jsPDF;
        app = new App();        
        prenda_render_data();

        app.defineEvent('#link_config_tabla', 'click', () => {
            init_config_form();
            $('#md_tabla_config').modal('show');
        })

        app.defineEvent('#__download_data_pdf', 'click', () => {
            var doc = new jsPDF();
            var rows = [];
            dataset_setting.data_raw.forEach((element, index) => {      
                var temp = [
                    parseInt(index + 1),
                    element.descripcion, 
                    element.cliente, 
                    element.tipo,
                    element.marca,
                    element.modelo
                ];
                rows.push(temp);
            });
            doc.autoTable({
                theme: 'grid',                
                styles: { fontSize: 8 },
                head: [[
                    "#", "Desc.","Cliente", "Tipo", "Marca", "Modelo"
                ]],
                body: rows
            })
            doc.save('Electrocash - Prendas.pdf');
        })

        app.defineEvent('#btn_sub_limit', 'click', () => {
            app.render('feed_sub_limit', '');
            let val = document.getElementById('config_sub_limit').value;
            if (Number(val) && parseInt(val) > 0) {
                dataset_setting.sub_limit = parseInt(val);
                grid_global.updateConfig({
                    pagination: {
                        limit: dataset_setting.sub_limit
                    }
                }).forceRender();
            }else{
                app.render('feed_sub_limit', 'Cantidad no valida.');    
            }
        })

        app.defineEvent('#input_search', 'keyup', async (ev) => {
            req_data.buscar = ev.target.value;
            await prenda_data_get();
            grid_global.updateConfig({
                columns: dataset_setting.columns.concat([
                    { 
                        name: 'Acciones',
                        formatter: (_, row) => gridjs.html(`
                            <a href="javascript:void(0)" class="btn btn-sm btn-indigo" 
                                >
                                <i class="fe fe-layers"></i>
                                Ver Detalle
                            </a>
                        `)
                    }
                ]),
                data: dataset_setting.data_raw.map((prenda, index, array) => {
                    return file_research(prenda);
                })
            }).forceRender();
            console.log('forzado 1', dataset_setting.data_raw);
        })
    });

    document.addEventListener('change', async event => {
        if (event.target.matches('[name=filter_item]')) {
            req_data.filtro = event.target.value;
            switch (req_data.filtro) {
                case 'cliente':
                    $('#sectionSearch').slideDown('fast');
                    $('#sectionSelect').slideUp('fast');
                    $('#sectionRadio').slideUp('fast');
                    document.getElementById('input_search').value = '';
                    break;
                
                case 'tipo':
                    $('#sectionSearch').slideUp('fast');
                    $('#sectionSelect').slideDown('fast');
                    $('#sectionRadio').slideUp('fast');
                    tipo_prenda_render();
                    break;

                case 'categoria':
                    $('#sectionSearch').slideUp('fast');
                    $('#sectionSelect').slideDown('fast');
                    $('#sectionRadio').slideUp('fast');
                    categoria_render();
                    break;

                case 'estado':
                    $('#sectionSearch').slideUp('fast');
                    $('#sectionSelect').slideDown('fast');
                    $('#sectionRadio').slideUp('fast');
                    estado_render();
                    break;
            }
        }

        if (event.target.matches('#select_search')) {
            req_data.buscar = event.target.value;
            if (event.target.value.length > 0) {
                await prenda_data_get();
                grid_global.updateConfig({
                    data: dataset_setting.data_raw.map((prenda, index, array) => {
                        return file_research(prenda);
                    })
                }).forceRender();
                console.log('forzado 2', dataset_setting.data_raw)
            }
        }

        if (event.target.matches('[name=radio_item]')) {
            req_data.buscar = event.target.value;
            prenda_render_data();
        }

        if (event.target.matches('[name=config-item]')) {
            dataset_setting.models.forEach(async el => {
                if (event.target.value === el.name) {
                    el.check = event.target.checked;
                    if (event.target.checked) {
                        if(!dataset_setting.columns.includes(el.title)) {
                            dataset_setting.columns.push(el.title);
                        }
                        if (!dataset_setting.data_pop.includes(el.name)) {
                            dataset_setting.data_pop.push(el.name);
                        }
                    }else{
                        dataset_setting.columns = dataset_setting.columns.filter(ex => {
                            return ex != el.title;
                        });
                        dataset_setting.data_pop = dataset_setting.data_pop.filter(ex => {
                            return ex != el.name;
                        });
                    }                  
                    grid_global.updateConfig({
                        data: dataset_setting.data_raw.map((prenda, index, array) => {
                            return file_research(prenda);
                        })
                    }).forceRender();
                    console.log('forzado 2', dataset_setting.data_raw)
                }
            }); 
        }

        if (event.target.matches('#config_head_static')) {
            dataset_setting.header_static = event.target.checked;
            grid_global.updateConfig({
                fixedHeader: dataset_setting.header_static,
                height: dataset_setting.header_static ? '400px' : '',
            }).forceRender();
        }

        if (event.target.matches('#config_sub_search')) {
            dataset_setting.sub_search = event.target.checked;
            grid_global.updateConfig({
                search: dataset_setting.sub_search
            }).forceRender();
        }

        if (event.target.matches('[name=theme-item]')) {
            dataset_setting.theme = 'vx-table '+event.target.value;
            grid_global.updateConfig({
                className: {
                    table: dataset_setting.theme
                }
            }).forceRender();
        }
    });

    async function prenda_data_get(){
        try {
            // $('#search_preload').slideDown('fast');
            document.getElementById('search_preload').style.visibility = 'visible';
            var req = await fetch(srv.url()+'Prenda/prot_service_data', {
                method: 'POST',
                body: JSON.stringify(req_data)
            });
            var res = await req.json();
            var raw = [];
            res.forEach((item) => {
                let tag_class = "";
                switch (item.estado) {
                    case 'R':
                        tag_class = "gridjs.html('<span class=tagger-prenda-R>Recuperado</span>')";
                        break;
                
                    case 'O':
                        tag_class = "gridjs.html('<span class=tagger-prenda-O>Ocupado</span>')";
                        break;

                    case 'D':
                        tag_class = "gridjs.html('<span class=tagger-prenda-D>Disponible</span>')";
                        break;

                    case 'A':
                        tag_class = "gridjs.html('<span class=tagger-prenda-A>Remate</span>')";
                        break;
                }
                var btn_class = `
                    <a class='btn btn-indigo btn-sm' href='${srv.url()}Prenda/detalle/'>
                        <i class='fe fe-layers'></i>
                        Ver Detalle
                    </a>`;
                raw.push({
                    id: item.id, 
                    cliente: item.cliente, 
                    categoria: item.categoria, 
                    tipo: item.tipo,
                    marca: item.marca,
                    modelo: item.modelo,
                    peso: item.peso,
                    quilate: item.quilate,
                    descripcion: item.descripcion, 
                    comentario: item.comentario,
                    monto_asignado: 'S/ '+item.monto_asignado,
                    fecha_recupera: item.fecha_remate, 
                    estado: eval(tag_class),
                    accion: eval("gridjs.html(`<a class='btn btn-indigo btn-sm' href='${srv.url()}Prenda/detalle/${item.id}'><i class='fe fe-layers'></i> Ver Detalle </a>`)")


                });
            });
            dataset_setting.data_raw = raw;
            document.getElementById('search_preload').style.visibility = 'hidden';
            return raw;
        } catch (error) {
            return [];
        }
    }    

    async function prenda_render_data(req_data = true){
        if (req_data) await prenda_data_get();
        grid_global = new gridjs.Grid({
            columns: dataset_setting.columns,
            sort: true,
            autoWidth: true,
            search: dataset_setting.sub_search,
            fixedHeader: dataset_setting.header_static,
            height: dataset_setting.header_static ? '400px' : '',
            pagination: {
                limit: dataset_setting.sub_limit
            },
            data: dataset_setting.data_raw.map((prenda, index, array) => {
                return file_research(prenda);
            }),
            language: {
                'search': {
                  'placeholder': '🔍 Buscar...'
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
                },
                th: {
                    'max-width': '80%',
                    'padding': '6px 12px',
                    'word-wrap': 'break-all'
                },
                td: {
                    'box-shadow': 'none'
                }
            },
            className: {
                table: dataset_setting.theme
            }
        }).render(document.getElementById("dataset_prenda"));
        document.getElementById('dataset_prenda').style.boxShadow = 'none';
    }

    function file_research(data){
        var names = [];
        var vx = dataset_setting.data_pop.forEach(elx => {
            names.push(eval(`data.${elx}`));
        })
        return names;
    }

    async function categoria_get_data(){
        try {
            var req = await fetch(srv.url()+'Categoria/ServiceConsulta');
            var res = await req.json();
            return res;
        } catch (error) {
            return [];
        }
    }

    async function tipo_prenda_get_data(){
        try {
            var req = await fetch(srv.url()+'TipoPrenda/consulta');
            var res = await req.json();
            return res;
        } catch (error) {
            return [];
        }
    }
    
    async function init_config_form(){
        var html_cols = '';
        dataset_setting.models.forEach(el => {
            let chk_state = (el.check) ? 'checked' : '';
            html_cols += `
                <div class="col-md-4">
                    <label class="custom-control custom-checkbox custom-control-inline">
                        <input type="checkbox" class="custom-control-input" name="config-item" 
                        value="${el.name}" ${chk_state}>
                        <span class="custom-control-label">${el.title}</span>
                    </label>
                </div>
            `;
        })
        app.render('config_cols', html_cols);
    }

    async function tipo_prenda_render(){
        try {
            const req = await fetch(srv.url()+'TipoPrenda/ServiceConsulta');
            const res = await req.json();
            if (res) {
                var res_html = '<option value="">- SELECCIONAR -</option>';
                if (res.length > 0) {
                    res.forEach(element => {
                        res_html += `
                            <option value="${element.id}">
                                ${element.nombre}
                            </option>
                        `;
                    })
                    app.render('select_search', res_html);
                }
            }
            req_data.buscar = '';
        } catch (error) {
            console.log(error.message)
        }
    }

    async function categoria_render(){
        try{
            const req = await fetch(srv.url()+'Categoria/ServiceConsulta');
            const res = await req.json();
            if (res) {
                var res_html = '<option value="">- SELECCIONAR -</option>';
                if (res.length > 0) {
                    res.forEach(element => {
                        res_html += `
                            <option value="${element.id}">
                                ${element.nombre}
                            </option>
                        `;
                    })
                    app.render('select_search', res_html);
                }
            }
            req_data.buscar = '';
        }catch(err){

        } 
    }
    
    async function estado_render(){
        document.getElementById('select_search').innerHTML = `
            <option value="">- SELECCIONAR -</option>
            <option value="R">Recuperado</option>
            <option value="D">Disponible</option>
            <option value="O">Ocupado</option>
            <option value="A">Remate</option>
        `;
        req_data.buscar = '';        
    }

    // function 

})();