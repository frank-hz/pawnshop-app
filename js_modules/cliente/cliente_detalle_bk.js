(function(d){
    let app;
    const DEFAULT_TIPO = 'ELECTRO';
    let id_base;
    let local = {
        notas_limit_cant: 4
    }
    let mdl_cliente, mdl_notascliente, mdl_prenda, mdl_credito, mdl_tprenda,mdl_categoria, mdl_conducta;
    let loaders = {
        data: d.getElementById('loader_data'),
        nota: d.getElementById('loader_nota'),
        prenda: d.getElementById('loader_prenda'),
        credito: d.getElementById('loader_credito')
    }
    
    d.addEventListener('DOMContentLoaded', () => {
        app = new App();
        mdl_cliente = new Cliente();
        mdl_notascliente = new NotasCliente();
        mdl_prenda = new Prenda();
        mdl_credito = new Credito();
        mdl_tprenda = new TipoPrenda();
        mdl_categoria = new Categoria();
        mdl_conducta = new Conducta();

        id_base = parseInt(d.getElementById('id_cliente').value);
        nota_get_all();
        prenda_get_all();
        credito_get_all();
        conducta_render_data();
    });
    d.addEventListener('click', async event => {
        let ev = event.target;
        // Nota
        if (ev.id === 'notas_vtodo') {
            local.notas_limit_cant = 0;
            nota_get_all();
        }
        if (ev.id === 'btn-nota-nuevo') {
            if (ev.dataset.idcliente) {
                d.getElementById('form-nota').dataset.mode = 'new';
                d.getElementById('form-nota').reset();
                d.getElementById('modal-nota-title').innerHTML = `
                    <i class="fe fe-plus-circle text-blue"></i>
                    Nueva Nota
                `;
                $('#modal-nota').modal('show');                  
            }
        }
        if (ev.matches('[name=nota-delete]')) {
            if (ev.dataset.id) {
                const aswer = Swal.fire({
                    type: 'question',
                    title: '¿Eliminar Nota?',
                    showCancelButton: true,
                    confirmButtonColor: '#E92C4C',
                    confirmButtonText: 'Si, eliminar',
                    cancelButtonColor: '#283845',
                    cancelButtonText: 'Cancelar'
                }).then(async(result) => {
                    if (result.value) {
                        nota_delete(ev.dataset.id);
                    }
                });               
            }
        }
        if (ev.matches('[name=nota-edit]')) {
            if (ev.dataset.id) {
                let data = await nota_get_data(ev.dataset.id);
                if (Object.keys(data).length) {
                    let form = d.getElementById('form-nota');
                    form.dataset.mode = 'edit';
                    form.reset();
                    form.descripcion.value = data.descripcion;
                    form.idnota.value = data.id;
                    d.getElementById('modal-nota-title').innerHTML = `
                        <i class="fe fe-edit text-blue"></i>
                        Editar Nota
                    `;
                    $('#modal-nota').modal('show'); 
                    return;                 
                }
                not.Aviso('Nota no identificada', '', 2300);
            }
        } 
        
        // Prenda        
        if (ev.id === 'btn-prenda-nuevo') {                            
            await prenda_render_tipos({name: DEFAULT_TIPO});

            let form = d.getElementById('form-prenda');
            form.dataset.mode = 'NEW';
            form.idprenda = 0;           
            prenda_render_form(DEFAULT_TIPO);
            d.getElementById('modal-prenda-title').innerHTML = `
                <i class="fe fe-plus text-blue"></i> 
                Nueva Prenda
            `;
            $('#modal-prenda').modal('show');            
        }        
        if (ev.matches('[name=prenda-edit]')){
            try {
                app.render('prenda_form_content', `
                    <div class="d-flex justify-center">
                        <div class="spinner-v1-mini"></div>
                    </div>
                `);
                if (!app.validata('id',ev.dataset.id)) return;    
                let pdata = await mdl_prenda.get_data(ev.dataset.id);
                if (pdata) {
                    await prenda_render_tipos({
                        id: pdata.tipo_id,
                        categoria: pdata.categoria_id
                    });
                    let form = d.getElementById('form-prenda');
                    form.dataset.mode = 'EDIT';
                    form.idprenda.value = pdata.id;
                    form.descripcion.value = pdata.descripcion;
                    form.categoria.value = pdata.categoria_id;
                    app.render('prenda_form_content', pdata.prenda_form_string);
                    
                    d.getElementById('modal-prenda-title').innerHTML = `
                        <i class="fe fe-edit text-blue"></i> 
                        Editar Prenda
                    `;
                    $('#modal-prenda').modal('show');            
                }            
                
            } catch (error) {
                app.render('prenda_form_content', 'Error al procesar');
            }
        }
        if (ev.matches('[name=prenda-delete]')) {
            if (ev.dataset.id) {
                const aswer = Swal.fire({
                    type: 'question',
                    title: '<p>¿Eliminar prenda?</p>',
                    html: `<b style="color:#E92C4C;">${ev.dataset.ref}</b>`,
                    showCancelButton: true,
                    confirmButtonColor: '#E92C4C',
                    confirmButtonText: 'Si, eliminar',
                    cancelButtonColor: '#283845',
                    cancelButtonText: 'Cancelar'
                }).then(async(result) => {
                    if (result.value) {
                        prenda_delete(ev.dataset.id);
                    }
                });               
            }
        }

        // Conducta
        if (ev.id === 'btn-conducta-edit') {      
            let form = d.getElementById('form-conducta');
            form.reset();   
            let response = await mdl_conducta.getbycliente(id_base); 
            if (!response || response.code != 'OK') {
                not.Aviso('[conducta] Error al inicializar','',2000);
                return;
            }
            let data = response.result;
            if (data) {
                form.puntuacion.value = data.puntuacion; 
                form.base_puntuacion.value = data.puntuacion;
                form.base_bloqueo_atencion.value = data.bloqueo_atencion;
                form.base_bloqueo_motivo.value = data.bloqueo_motivo;                
                if (data.bloqueo_atencion == 'SI') {
                    form.bloqueo_atencion.checked = true;
                    form.bloqueo_motivo.value = data.bloqueo_motivo;
                    form.bloqueo_motivo.removeAttribute('disabled');
                }else {
                    form.bloqueo_atencion.checked = false;
                    form.bloqueo_motivo.setAttribute('disabled','disabled');
                }
            } else{
                form.bloqueo_motivo.setAttribute('disabled','disabled');
            }

            $('#modal-conducta').modal('show');
        }
        if (ev.id === 'btn-cliente-edit') {
            try {
                let req = await mdl_cliente.get_data(id_base); 
                let data = req.result;
                if (!data || Object.keys(data).length <= 0) {
                    not.Aviso('Error al cargar los datos.','',2500);
                    return;
                }
                let form = d.getElementById('form-cliente');
                form.reset();
                form.nombre.value = data.nombres;
                form.paterno.value = data.paterno;
                form.materno.value = data.materno;
                form.documento.value = data.documento;
                form.tipo_documento.value = data.tipo_documento;
                form.distrito.value = `${data.distrito}, ${data.departamento}`;
                form.ubigeo.value = data.ubigeo;
                form.telefono.value = data.telefono;
                form.direccion.value = data.direccion;
                form.email.value = data.email;

                d.getElementById('btn_cliente_save').disabled = false;
                $('#modal-cliente').modal('show');
            } catch (error) {
                console.log(error)
            }
        }
        if (ev.matches('[name=distrito-item]')) {
            let form = d.getElementById('form-cliente');
            let list = d.getElementById('distrito-search-list'); 
            if (ev.dataset.ubigeo) {
                form.ubigeo.value = ev.dataset.ubigeo;
                form.distrito.value = ev.dataset.name;
                list.innerHTML = '';
            } 
        }

    });
    d.addEventListener('input', event => {
        let ev = event.target;
        if (ev.matches('[name=valoracion]')) {
            console.log(ev.value)
            d.getElementById('valora-range-label').innerHTML = ev.value;
        }
    });
    d.addEventListener('change', event => {
        let ev = event.target;
        
        // if (ev.matches('[name=valoracion]')) {
        //     d.getElementById('valora-range-label').innerHTML = ev.value;
        // }
        if (ev.matches('[name=tipo_prenda]')) {
            if (ev.value.length) {
                prenda_render_categorias(parseInt(ev.value));
                prenda_render_form(ev.dataset.nombre);
            }
        }
        if (ev.matches('[name=bloqueo_atencion]')) {
            let inp = d.getElementById('inp-conducta-motivo');
            inp.disabled = !ev.checked;
            if (!ev.checked) {
                inp.value = '';
            }
        }
    });
    d.addEventListener('submit', async event => {
        event.preventDefault();
        let ev = event.target;

        if (ev.id === 'form-nota') {
            if (ev.dataset.mode === 'new') {
                let data = new FormData(ev);
                data.append('cliente', id_base);
                nota_save(data);

            }else if (ev.dataset.mode === 'edit'){
                let data = new FormData(ev);
                nota_update(data);

            }
        }
        if (ev.id === 'form-prenda') {
            if (ev.dataset.mode === 'NEW') {
                let data = new FormData(ev);
                data.append('idcliente', id_base);
                prenda_save(data);

            }else if (ev.dataset.mode === 'EDIT') {
                let data = new FormData(ev);
                data.append('id', ev.idprenda.value);
                prenda_update(data);

            }
        }
        if (ev.id === 'form-conducta') {
            let data = new FormData(ev);
            data.append('idcliente', id_base);
            conducta_save(data);
        }
        if (ev.id === 'form-cliente') {
            let data_upd = new FormData(ev);
            data_upd.append('id', id_base);
            cliente_update_data(data_upd);
        }
    })
    d.addEventListener('keyup', async event => {
        let ev = event.target;
        if (ev.matches('[name=distrito]')) {
            let data = await distrito_search_data(ev.value);
            distrito_render_data(data);
        }        
    });




    // Notas
    async function nota_get_all(){
        try {
            loaders.nota.classList.remove('d-none');
            let resp = await mdl_notascliente.get_by_cliente(id_base);
            let notas = resp.result;
            

            let html = '';
            if (notas.length > 0) {
                let limite = (local.notas_limit_cant > 0) ? local.notas_limit_cant : notas.length;
                // let limite_active = (limite) 
                notas.forEach((nota, ind) => {
                    if ((ind+1) <= limite) {
                        html += `
                            <tr>
                                <td>${nota.descripcion}</td>
                                <td>
                                    <a href="${nota.empleado_url}" class="btn-link">
                                        ${nota.empleado_nombre_full}
                                    </a>
                                </td>
                                <td>${nota.fecha}</td>
                                <td>
                                    <div class="d-flex justify-center">
                                    <button name="nota-edit" class="btn-cell" data-id="${nota.id}" title="Editar Nota">
                                        <span class="fe fe-edit"></span> 
                                    </button>
                                    <button name="nota-delete" class="btn-cell btn-cell-danger" data-id="${nota.id}" title="Eliminar Nota">
                                        <span class="fe fe-trash"></span> 
                                    </button>
                                    </div>
                                </td>
                            </tr>  
                        `;
                    }
                })
                if (limite < notas.length) {
                    html += `
                    <tr>
                        <td class="text-center" colspan="4">
                            <a id="notas_vtodo" href="javascript:void(0)" class="btn btn-sm btn-outline-primary">
                                Ver Todo
                            </a>
                        </td>
                    </tr>
                    `;
                }
            }else{
                html = '<tr><td colspan="4" class="text-center">Sin Resultados</td></tr>';
            }
            loaders.nota.classList.add('d-none');
            app.render('datatable-notas', html);
        } catch (error) {
            console.log(error)
            loaders.nota.classList.add('d-none');
            app.render('datatable-notas', `
                <tr><td colspan="4" class="text-center">Error al cargar</td></tr>
            `);
        }
    }
    async function nota_get_data(_id){
        app.preload('open', 'Cargando...');
        let data = new FormData();
        data.append('id', _id);
        const req = await fetch(srv.url()+'NotaCliente/get_data', {
            method : 'POST',
            body : data
        });
        const res = await req.json();
        app.preload('close');
        return res;
    }
    async function nota_delete(_id) {
        try {
            app.preload('open', 'eliminando nota..');
            let data = new FormData();
            data.append('id', _id);
            const request = await fetch(srv.url()+'NotaCliente/eliminar', {
                method : 'POST',
                body : data
            });
            const res = await request.json();
            app.preload('close');
            if (res.status === 'OK') {
                not.Ok(res.message, '', 3000);
                nota_get_all();
            }else{
                if (res.status === 'WARN') 
                    not.Aviso(res.message, '', 3000);
                else
                    not.Error(res.message, '', 3000);
            }
        } catch (error) {
            app.preload('close');
        }
    
    }
    async function nota_save(data){
        try {
            app.preload('open','cargando..');
            const request = await fetch(srv.url()+'NotaCliente/agregar', {
                method : 'POST',
                body : data
            });
            const res = await request.json();

            app.preload('close');
            if (res.status === 'OK') {
                d.getElementById('form-nota').reset();
                not.Ok(res.message, '', 3000);
                $('#modal-nota').modal('hide'); 
                nota_get_all();
            }else{
                if (res.status === 'WARN') 
                    not.Aviso(res.message, '', 3000);
                else
                    not.Error(res.message, '', 3000);
            }
        } catch (error) {
            app.preload('close');
            not.Error('Error al agregar nota');
        }
    }    
    async function nota_update(data){
        try {
            app.preload('open','cargando..');
            const request = await fetch(srv.url()+'NotaCliente/update_data', {
                method : 'POST',
                body : data
            });
            const res = await request.json();

            app.preload('close');
            if (res.status === 'OK') {
                d.getElementById('form-nota').reset();
                not.Ok(res.message, '', 3000);
                $('#modal-nota').modal('hide'); 
                nota_get_all();
            }else{
                if (res.status === 'WARN') 
                    not.Aviso(res.message, '', 3000);
                else
                    not.Error(res.message, '', 3000);
            }
        } catch (error) {
            app.preload('close');
            not.Error('Error al guardar los cambios.');
        }
    }


    // Prenda
    async function prenda_get_all(){
        try {
            loaders.nota.classList.remove('d-none');
            let req = await mdl_prenda.get_by_cliente(id_base);
            let res = req.result; 
            
            let html = "";
            if (res.length) {
                res.forEach(prenda => {                    
                    html += `
                        <tr>
                            <td>
                                <a class="btn-link text-blue" href="${prenda.url}">
                                ${ prenda.descripcion }
                                </a>
                            </td>
                            <td>${ prenda.categoria_nombre }</td>
                            <td>${ prenda.valoracion }</td>
                            <td>${ prenda.estado_tag }</td>
                            <td>
                                <div class="d-flex justify-center">
                                    <button name="prenda-edit" class="btn-cell" data-id="${ prenda.id }" title="Editar Prenda">
                                    <span class="fe fe-edit"></span> 
                                    </button>
                                    <button name="prenda-delete" class="btn-cell btn-cell-danger" data-id="${ prenda.id }" data-ref="${prenda.descripcion}" title="Eliminar Prenda">
                                    <span class="fe fe-trash"></span> 
                                </button>
                                </div>
                            </td>
                        </tr>
                    `;
                });
            }else {
                html = `
                <tr>
                    <td class="text-center" colspan="5">Sin Resultados.</td>
                </tr>
                `;
            }
            loaders.nota.classList.add('d-none');
            app.render('datatable-prendas', html);
        } catch (error) {
            loaders.nota.classList.add('d-none');
            app.render('datatable-prendas', `
                <tr>
                    <td class="text-center" colspan="5">Error al procesar.</td>
                </tr>
            `);
        }
    }
    async function prenda_render_form(tipo){
        try {
            app.render('prenda_form_content', `
            <div class="d-flex justify-center">
                <div class="spinner-v1-mini"></div>
            </div>
            `);
            let settsdata = await mdl_prenda.get_form_by_tipo(tipo);
            let validate = typeof settsdata;
            if (validate === 'undefined' || validate === null) {
                throw ('Error al obtener el formulario');
            }
            app.render('prenda_form_content', settsdata.result);
            if (d.getElementById('prendau_placa')){
                app.inputLicence('prendau_placa', (nval)=>{});
            }
        } catch (error) {
            app.render('prenda_form_content', '');
            let mssg = (error.name) ? 'Error al procesar' : error;
            not.Aviso('',mssg,2500);
        }
    }
    async function prenda_render_tipos(default_data = {}){
        try {
            let area = d.getElementById('tprenda-render-area');
            area.innerHTML = `
                <div class="d-flex justify-center">
                    <div class="spinner-v1"></div>
                </div>
            `;
            let data = await mdl_tprenda.get_all();      
            area.innerHTML = (data) ? '' : '<div class="text-center">Sin Datos</div>';

            let html = '', id_default = 0;
            data.forEach(e => {  
                let chkd = '';              
                if (default_data.id>0 || default_data.name) {
                    let val_a = (default_data.id) ? default_data.id : default_data.name;
                    let val_b = (default_data.id) ? e.id : e.nombre;
                    if (val_a === val_b) {
                        chkd = 'checked';
                        id_default = e.id;
                    }
                }
                area.innerHTML += `
                <label class="custom-control custom-radio custom-control-inline">
                    <input type="radio" data-ref="${e.nombre}" data-nombre="${e.nombre}" class="custom-control-input" name="tipo_prenda" value="${e.id}" ${chkd}>
                    <span class="custom-control-label">${e.nombre}</span>
                </label>
                `;
            });
            prenda_render_categorias(id_default, default_data.categoria);
        } catch (error) {
            console.log(error)
        }
    }
    async function prenda_render_categorias(idtipo, default_checked = 0){
        try {
            let list = d.getElementById('categoria-list');
            let loader = d.getElementById('categoria-loader');
            loader.classList.add('spinner-v1-small');

            let res = await mdl_categoria.get_by_tipo(idtipo);
            list.innerHTML = '<option value="">-SELECCIONAR-</option>';
            res.forEach(e => {
                let item = d.createElement('option');
                item.setAttribute('value', e.id);
                item.textContent = e.nombre;
                if (e.id == default_checked) item.setAttribute('selected', 'selected');
                list.appendChild(item);
            });
            loader.classList.remove('spinner-v1-small');
        } catch (error) {
            app.preload('close');
            let mssg = (error.name) ? 'Error de ejecucion' : error;
            not.Aviso(mssg, '', 2500); 
        }
    } 
    async function prenda_save(data){
        try {
            app.preload('open','procesando..');
            d.getElementById('btn_prenda_save').disabled = true;

            let save = await mdl_prenda.save(data);
            app.preload('close'); 
            if (save.code === 'OK' && save.id) {
                not.Ok('Prenda Registrada', '', 2000);
                d.getElementById('form-prenda').reset();
                $('#modal-prenda').modal('hide');
                prenda_get_all();
                return;
            }else if(save.code === 'ERR') {
                not.Aviso(save.message, '', 2500); 
                d.getElementById('btn_prenda_save').disabled = false;
                return;
            }
            throw 'El proceso no se completo';       
        } catch (error) {
            app.preload('close');
            let mssg = (error.name) ? 'Error de ejecucion' : error;
            not.Aviso(mssg, '', 2500); 
        }
    }
    async function prenda_update(data){
        try {
            app.preload('open','procesando..');
            d.getElementById('btn_prenda_save').disabled = true;

            let update = await mdl_prenda.update(data);
            app.preload('close'); 
            if (update.code === 'OK') {
                not.Ok('Prenda Registrada', '', 2000);
                d.getElementById('form-prenda').reset();
                $('#modal-prenda').modal('hide');
                prenda_get_all();
                return;
            }else if(update.code === 'ERR') {
                not.Aviso(update.message, '', 2500); 
                d.getElementById('btn_prenda_save').disabled = false;
                return;
            }            
            throw 'El proceso no se completo';               
        } catch (error) {
            app.preload('close'); 
            let mssg = (error.name) ? 'Error de ejecucion' : error;
            not.Aviso(mssg, '', 2500); 
        }
    }
    async function prenda_delete(id){
        try {
            app.preload('open', 'eliminando prenda..');
            let del = await mdl_prenda.delete(id);
            app.preload('close'); 

            if (del.code === 'OK') {
                not.Ok('Prenda Eliminada', '', 2500);
                prenda_get_all();
                return;
            }else if(del.code === 'ERR') {
                not.Aviso(del.message, '', 2500);
                return;
            }
            throw 'El proceso no se completo';  
        } catch (error) {
            app.preload('close');
        }
    }


    // Creditos
    async function credito_get_all(){
        try {
            loaders.nota.classList.remove('d-none');
            let req = await mdl_credito.get_by_cliente(id_base);
            let res = req.result;
    
            let html = '';
            if (res.length > 0) {
                res.forEach(credito => {                    
                    html += `
                        <tr>
                            <td>
                                <a class="btn-link" href="${credito.url}">#${credito.serie}</a>
                            </td>  
                            <td>${credito.fecha_creacion}</td>
                            <td>S/ ${credito.monto}</td>
                            <td>${credito.estado_tag}</td>
                        </tr>
                    `;
                });
            }else {
                html = `
                    <tr>
                        <td class="text-center" colspan="4">Sin Resultados.</td>
                    </tr>
                `;
            }
            loaders.nota.classList.add('d-none');
            app.render('datatable-creditos', html);            
        } catch (error) {
            loaders.nota.classList.add('d-none');
            app.render('datatable-creditos', `
                <tr>
                    <td class="text-center" colspan="4">Error al procesar.</td>
                </tr>
            `);
        }

    }

    // Conducta
    async function conducta_render_data(){
        try {
            d.getElementById('cond-block-banner').classList.add('d-none');
            let response = await mdl_conducta.getbycliente(id_base); 
            if (!Object.keys(response).length || response.code != 'OK') {
                not.Aviso('[conducta] Error al inicializar','',2000);
                return;
            }
            let data = response.result;
            if (data && Object.keys(data).length) {
                app.render('cond-puntuacion', `
                    <div class="enc1212-conducta-tag enc1212-conducta-tag-${data.puntuacion}">
                        ${(data.puntuacion) ? data.puntuacion : '-'}
                    </div>
                `);                
                if (data.bloqueo_atencion == 'SI') {
                    d.getElementById('cond-block-banner').classList.remove('d-none');
                    app.render('cond-block-banner', `
                        <div class="font-weight-bold d-block">Lista Negra</div>
                        ${data.bloqueo_motivo}
                    `);
                }
            }else {
                app.render('cond-puntuacion', `
                    <div class="enc1212-conducta-tag">
                        -
                    </div>
                `);
            }            
        } catch (error) {
            console.log(error)
        }
    }
    async function conducta_save(data){
        try {
            let save = await mdl_conducta.save(data);
            if (save.code === 'OK') {
                not.Ok(save.message, '', 2500);
                $('#modal-conducta').modal('hide');
                conducta_render_data();
                return;
            }else if(save.code === 'ERR') {
                not.Aviso(res.message, '', 2400);
            }else {
                throw 'Error al procesar';
            }            
        } catch (error) {
            let mssg = (error.name) ? '[conducta] Error de ejecucion' : error;
            console.log('[conducta] '+error.message)
            not.Error(mssg, '', 2000);
        }
    }

    // Cliente
    async function cliente_render_data(){
        let req = await mdl_cliente.get_data(id_base); 
        let data = req.result;
        if (!data && Object.keys(data).length <= 0) {
            not.Aviso('Error al cargar los datos del cliente.','',2500);
            return;
        }
        app.render('dg-nombre', `${data.nombres} ${data.paterno} ${data.materno}`);
        app.render('dg-documento', data.documento);
        app.render('dg-tipodoc', data.tipo_documento);
        app.render('dg-distrito', data.distrito);
        app.render('dg-telefono', data.telefono);
        app.render('dg-direccion', data.direccion);
        app.render('dg-email', data.email);

    }
    async function distrito_search_data(_value){
        const req = await fetch(srv.url() + 'Ubigeo/service_search',{
            method: 'POST',
            body: JSON.stringify({
                search: _value
            })
        });
        const res = await req.json();
        return res;
    }
    async function distrito_render_data(data){        
        let list = d.getElementById('distrito-search-list');
        list.innerHTML = '';

        data.forEach((e, ind) => {
            if (ind <= 2) {
                let item = d.createElement('li');
                item.setAttribute('class', 'tooltip-item');
                item.setAttribute('name', 'distrito-item');
                item.dataset.ubigeo = e.id;
                item.dataset.name = `${e.distrito}, ${e.departamento}`;
                item.innerHTML = `
                    <div class="tooltip-item-title">${e.distrito}</div> 
                    <div class="tooltip-item-subtitle">${e.departamento}</div>  
                `;
                list.appendChild(item);
            }
        });
    }
    async function cliente_update_data(data){
        try {
            app.preload('open','procesando..');
            d.getElementById('btn_cliente_save').disabled = true;

            let update = await mdl_cliente.update(data);
            app.preload('close'); 
            if (update.code === 'OK') {
                not.Ok('Cambios guardados', '', 2000);
                d.getElementById('form-cliente').reset();
                $('#modal-cliente').modal('hide');
                cliente_render_data();
                return;
            }else if(update.code === 'ERR') {
                not.Aviso(update.message, '', 2500); 
                d.getElementById('btn_prenda_save').disabled = false;
                return;
            }            
            throw 'El proceso no se completo';               
        } catch (error) {
            d.getElementById('btn_cliente_save').disabled = false;
            console.log(error)
            app.preload('close'); 
            let mssg = (error.name) ? 'Error de ejecucion' : error;
            not.Aviso(mssg, '', 2500); 
        }
    }
    async function cliente_delete(){
        try {
            // app.preload('open', 'eliminando prenda..');
            // let del = await mdl_cliente.delete(id_base);
            // app.preload('close');
            // if (del.code === 'OK') {
            //     not.Ok('del.message', '', 2500);
            //     window.location.href = srv.url()+'cliente';
            // }else if (del.code === 'ERR'){
            //     not.Aviso(del.message, '', 2500);
            // }            
            // throw 'El proceso no se completo'; 
        } catch (error) {
            app.preload('close');
            let mssg = (error.name) ? 'Error de ejecucion' : error;
            not.Aviso(mssg, '', 2500); 
        }
    }
    

})(document);