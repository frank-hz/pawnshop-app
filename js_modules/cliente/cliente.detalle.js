(function(d){
    const PRENDA_TIPO = 'ELECTRO';
    let app;
    let id_base;
    let local = {
        prendas_limit_show: 4,
        notas_limit_show: 4,
        prenda_edit: false,
        id_prenda_edit: 0,
        id_categoria_edit: 0
    }
    let mdl_cliente, mdl_notascliente, mdl_prenda, mdl_credito, mdl_tprenda,mdl_categoria, mdl_conducta;
    let loaders = {
        data: d.getElementById('loader_data'),
        nota: d.getElementById('loader_nota'),
        prenda: d.getElementById('loader_prenda'),
        credito: d.getElementById('loader_credito'),
        modal_conducta: d.getElementById('loader_modal_conducta'),
        categoria: d.getElementById('categoria-loader')
    }
    let local_conducta = {
        puntuacion: '-',
        bloqueo_atencion: '',
        bloqueo_motivo: ''
    } 

    d.addEventListener('DOMContentLoaded', async () => {
        app = new App();
        mdl_cliente = new Cliente();
        mdl_notascliente = new NotasCliente();
        mdl_prenda = new Prenda();
        mdl_credito = new Credito();
        mdl_tprenda = new TipoPrenda();
        mdl_categoria = new Categoria();
        mdl_conducta = new Conducta();

        id_base = parseInt(d.getElementById('id_cliente').value);
        await conducta_render_data();
        await nota_get_all();
        await prenda_get_all();
        await credito_get_all();
        
    });


    // C L I C K
    d.addEventListener('click', async event => {
        let ev = event.target;
        if (ev.id === 'btn-conducta-edit') {
            conducta_prepare_to_edit();      
        }

        if (ev.matches('[name=nota-edit]')) {
            if (!app.empty(ev.dataset.id)) {
                nota_prepare_to_edit(ev.dataset.id);
            }
        } 

        if (ev.id === 'btn-nota-nuevo') {
            let form = d.getElementById('form-nota');
            form.dataset.mode = 'NEW';
            form.reset();
            d.getElementById('btn_nota_save').disabled = false;
            d.getElementById('modal-nota-title').innerHTML = `
                <i class="fe fe-plus-circle text-blue"></i>
                Nueva Nota
            `;
            $('#modal-nota').modal('show');
        }

        if (ev.matches('[name=nota-delete]')) {
            if (!app.empty(ev.dataset.id)) {
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

        if (ev.id === 'notas_vtodo') {
            local.notas_limit_show = 0;
            nota_get_all();
        }

        if (ev.id === 'btn-prenda-nuevo') {
            prenda_prepare_save_form();
        }

        if (ev.matches('[name=prenda-edit]')) {
            if (app.empty(ev.dataset.id)){
                not.Aviso('[prenda editar] prenda no identificada');
                return;
            }
            prenda_prepare_edit_form(ev.dataset.id);
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

        if (ev.id === 'prendas_vtodo') {
            local.prendas_limit_show = 0;
            prenda_get_all();
        }

        if (ev.id === 'btn-cliente-edit') {
            if (app.empty(id_base)) {
                return;
            }
            cliente_prepare_edit_form();
        }

        if (ev.matches('[name=distrito-item]')) {
            let inp_ubigeo = d.getElementById('input_ubigeo');
            let inp_distrito = d.getElementById('input_distrito');
            let list = d.getElementById('distrito-search-list'); 
            if (ev.dataset.ubigeo) {
                inp_ubigeo.value = ev.dataset.ubigeo;
                inp_distrito.value = ev.dataset.name;
                list.innerHTML = '';
            } 
        }
    });

    // C H A N G E
    d.addEventListener('change', async event => {
        let ev = event.target;
        if (ev.matches('[name=bloqueo_atencion]')) {
            let inp = d.getElementById('input_bloqueo_motivo');
            inp.disabled = (ev.checked) ? false : true;
            inp.value = '';
            local_conducta.bloqueo_atencion = (ev.checked) ? 'SI' : 'NO';
            if (!ev.checked) {
                let items = document.querySelectorAll('[name=puntuacion]');
                items.forEach(e => {
                    e.disabled = false;
                });
            }
        }

        if (ev.matches('[name=tipo_prenda]')) {
            if (ev.value.length) {
                if (app.empty(ev.value)) {
                    not.Aviso('Tipo no identificado','',2000);
                    return;
                }
                let tipo_id = await prenda_render_tipos({
                    id_default: ev.value
                });
                let cat_params = {
                    tipo_id: tipo_id
                }
                if (local.prenda_edit && local.id_categoria_edit > 0){
                    cat_params.categoria_default = local.id_categoria_edit
                }
                await prenda_render_categorias(cat_params);
                let params = {
                    mode: 'NEW',
                    tipo: ev.dataset.nombre
                };
                if (local.prenda_edit && local.id_prenda_edit > 0) {
                    params.mode = 'EDIT';
                    params.id = local.id_prenda_edit;
                }
                prenda_render_form(params);
            }
        }

        if (ev.matches('[name=puntuacion]')) {
            local_conducta.puntuacion = ev.value;
        }
        
        if (ev.matches('[name=nota_fixed_item]')){
            if (ev.value){
                let check_status = ev.checked ? 1 : 0;
                let data = new FormData();
                data.append('id', ev.value);
                data.append('fixed', check_status);
                if (await !nota_update(data)){
                    ev.checked = check_status * 1;
                    // console.log('volver a '+(check_status * 1));
                    return;
                }

            }
        }
    });

    // S U B M I T
    d.addEventListener('submit', event => {
        event.preventDefault();
        let ev = event.target;
        if (ev.id === 'form-conducta') {
            let data = new FormData();
            data.append('idcliente', id_base);
            data.append('puntuacion',local_conducta.puntuacion);
            data.append('bloqueo_atencion',local_conducta.bloqueo_atencion);
            data.append('bloqueo_motivo',local_conducta.bloqueo_motivo);
            conducta_save_data(data);
        }

        if (ev.id === 'form-nota') {
            if (ev.dataset.mode === 'NEW') {
                let data = new FormData(ev);
                data.append('idcliente', id_base);
                nota_save(data);
            }else if (ev.dataset.mode === 'EDIT'){
                let data = new FormData(ev);
                nota_update(data);

            }
        }

        if (ev.id === 'form-prenda') {
            if (ev.dataset.mode === 'NEW') {
                let data = new FormData(ev);
                data.append('idcliente', id_base);
                prenda_save(data);

            }else if (ev.dataset.mode === 'EDIT'){
                let data = new FormData(ev);
                data.append('id',ev.idprenda.value);
                prenda_update(data);

            }
        }

        if (ev.id === 'form-cliente') {
            let data_upd = new FormData(ev);
            data_upd.append('id', id_base);
            cliente_update(data_upd);
        }

    });

    // I N P U T
    d.addEventListener('input', event => {
        let ev = event.target;
        if (ev.matches('[name=valoracion]')) {
            d.getElementById('valora-range-label').innerHTML = ev.value;
        }

        if (ev.id === 'prendau_placa') {
            if (ev.value.length === 3){
                ev.value += "-";
            }
            if (ev.value.length === 7){
                e.preventDefault()
            }
        }

        if (ev.id === 'input_bloqueo_motivo') {
            local_conducta.bloqueo_motivo = ev.value;
        }
    });

    // K E Y U P
    d.addEventListener('keyup', async event => {
        let ev = event.target;
        if (ev.matches('[name=distrito]')) {
            let data = await distrito_search_data(ev.value);
            distrito_render_data(data);
        }  
    });




    // C O N D U C T A
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
                local_conducta.puntuacion = data.puntuacion;
                local_conducta.bloqueo_atencion = data.bloqueo_atencion;
                local_conducta.bloqueo_motivo = data.bloqueo_motivo;
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
    async function conducta_prepare_to_edit(){
        let form = d.getElementById('form-conducta');
        try {
            app.preload('open', 'Cargando datos...');
            form.reset();
            form.bloqueo_motivo.setAttribute('disabled','disabled');
            form.btnsubmit.setAttribute('disabled','disabled');

            // obtener datos de la conducta actual
            let request_data = await mdl_conducta.getbycliente(id_base); 
            if (!request_data || request_data.code != 'OK') {
                not.Aviso('[conducta] Error al obtener datos','',2000);
                $('#modal-conducta').modal('hide');
                return;
            }
            if (app.empty(request_data.result)){
                not.Aviso('[conducta] Datos no encontrados','',2000);
                $('#modal-conducta').modal('hide');
                return;
            }
            let data = request_data.result;
            form.puntuacion.value = data.puntuacion; 
            form.base_puntuacion.value = data.puntuacion;
            form.base_bloqueo_atencion.value = data.bloqueo_atencion;
            form.base_bloqueo_motivo.value = data.bloqueo_motivo;  
            form.puntuacion.disabled = false; 
                 
            let items = document.querySelectorAll('[name=puntuacion]');
            items.forEach(e => {
                e.disabled = false;
            });
            if (data.bloqueo_atencion == 'SI') {
                form.bloqueo_atencion.checked = true;
                form.bloqueo_motivo.value = data.bloqueo_motivo;
                form.bloqueo_motivo.removeAttribute('disabled');

                items.forEach(e => {
                    e.disabled = true;
                });
                form.puntuacion.disabled = true;
            }else {
                form.bloqueo_atencion.checked = false;
                form.bloqueo_motivo.setAttribute('disabled','disabled');
            }
            form.btnsubmit.removeAttribute('disabled'); 
            $('#modal-conducta').modal('show');
            app.preload('close');
        } catch (error) {
            $('#modal-conducta').modal('hide');
            not.Error('Error al procesar','',2000);
            app.preload('close');
        } 
    }
    async function conducta_save_data(data){
        try {
            app.preload('open', 'Guardando datos...');
            d.getElementById('btn_conducta_save').disabled = true;
            app.rendalert('modal_conducta_rendalert', {clean: true});

            let save = await mdl_conducta.save(data);
            app.preload('close');
            if (save.success) {
                not.Ok(save.success, '', 2500);
                $('#modal-conducta').modal('hide');
                conducta_render_data();
                return;
            }
            if(save.warning) {
                app.rendalert('modal_conducta_rendalert',{
                    message: save.warning,
                    type: 'warning'
                });
                d.getElementById('btn_conducta_save').disabled = false;
                return;
            }
            if(save.error) {
                app.rendalert('modal_conducta_rendalert',{
                    message: save.error,
                    type: 'error'
                });
                d.getElementById('btn_conducta_save').disabled = false;
                return;
            }            
        } catch (error) {
            app.preload('close');
            console.log('[conducta] '+error.message)
            let mssg = (error.name) ? '[conducta] Error de ejecucion' : error;
            not.Error('[nota] Error al procesar', '', 2000);
            d.getElementById('btn_conducta_save').disabled = false;
        }
    }
    function conducta_items_reset(){

    }


    // N O T A S 
    async function nota_get_all(){
        try {
            loaders.nota.classList.remove('d-none');
            let request_data = await mdl_notascliente.get_by_cliente(id_base);
           
            if (app.empty(request_data.result)) {
                app.render('_table_notas', `
                    <tr><td colspan="4" class="text-center">Datos no encontrados</td></tr>
                `);
                return;
            }
            let data_raw = request_data.result;
            let html = '';
            if (!app.empty(data_raw)) {
                let fixeds = data_raw.filter(item => item.fixed === 1);
                let rest = data_raw.filter(item => item.fixed === 0);
                let data = [
                    ...fixeds,
                    ...rest
                ]


                let limite = local.notas_limit_show;
                limite = (limite > 0) ? limite : data.length;

                data.forEach((nota, ind) => {
                    if ((ind+1) <= limite) {
                        html += `
                            <tr>
                                <td>
                                    <label class="custom-switch">
                                        <input type="checkbox" name="nota_fixed_item" value="${nota.id}" class="custom-switch-input" 
                                        ${
                                            nota.fixed ? 'checked' : ''
                                        }>
                                        <span class="custom-switch-indicator"></span>
                                    </label>
                                </td>
                                <td class="${nota.fixed ? 'text-anclado font-weight-bold' : ''}">
                                    ${
                                        nota.fixed 
                                        ? '<i class="fa fa-thumbtack"></i>' : ''
                                    }
                                    ${ nota.descripcion }
                                </td>
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
                if (limite < data.length) {
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
            app.render('_table_notas', html);
        } catch (error) {
            console.log(error)
            loaders.nota.classList.add('d-none');
            app.render('_table_notas', `
                <tr><td colspan="4" class="text-center">Error al cargar</td></tr>
            `);
        }
    }
    async function nota_prepare_to_edit(_id){
        try {
            app.preload('open', 'Cargando datos...');
            let request_data = await mdl_notascliente.get_data(_id);
            if (app.empty(request_data) || request_data.code != 'OK') {
                not.Aviso('[nota] Error al obtener datos','',2000);
                $('#modal-nota').modal('hide');
                return;
            }
            if (app.empty(request_data.result)){
                not.Aviso('[nota] Datos no encontrados','',2000);
                $('#modal-nota').modal('hide');
                return;
            }

            let data = request_data.result;
            let form = d.getElementById('form-nota');
            form.dataset.mode = 'EDIT';
            form.reset();
            form.descripcion.value = data.descripcion;
            form.idnota.value = data.id;
            d.getElementById('modal-nota-title').innerHTML = `
                <i class="fe fe-edit text-blue"></i>
                Editar Nota
            `;
            d.getElementById('btn_nota_save').disabled = false;
            $('#modal-nota').modal('show');
            app.preload('close');
        } catch (error) {
            console.log('[nota] '+error.message)
            app.preload('close');
            not.Aviso('[nota] Error al procesar','',2000);
            $('#modal-nota').modal('hide');
        }
    }
    async function nota_save(data){
        try {
            app.preload('open','Guardando datos..');
            app.rendalert('modal_nota_rendalert',{clean: true});
            d.getElementById('btn_nota_save').disabled = true;

            const save = await mdl_notascliente.save(data);
            app.preload('close');
            if (save.success) {
                d.getElementById('form-nota').reset();
                not.Ok(save.success, '', 2500);
                $('#modal-nota').modal('hide'); 
                nota_get_all();
                return;
            }
            if (save.warning) {
                app.rendalert('modal_nota_rendalert',{
                    message: save.warning,
                    type: 'error'
                });
                d.getElementById('btn_prenda_save').disabled = false;
            }
            if (save.error) {
                app.rendalert('modal_nota_rendalert',{
                    message: save.error,
                    type: 'error'
                });
                d.getElementById('btn_nota_save').disabled = false;
            }
        } catch (error) {
            app.preload('close');
            d.getElementById('btn_nota_save').disabled = false;
            console.log('[nota] '+error.message)
            not.Error('[nota] Error al procesar');
        }
    }    
    async function nota_update(data){
        try {
            app.preload('open','cargando..');
            app.rendalert('modal_nota_rendalert',{clean: true});
            d.getElementById('btn_nota_save').disabled = true;

            const update = await mdl_notascliente.update(data);
            app.preload('close');
            if (update.ok) {
                d.getElementById('form-nota').reset();
                not.Ok(update.success, '', 2500);
                $('#modal-nota').modal('hide'); 
                nota_get_all();
                return true;
            }
            if (update.warning) {
                app.rendalert('modal_nota_rendalert',{
                    message: update.warning,
                    type: 'warning'
                });
                d.getElementById('btn_nota_save').disabled = false;
            }
            if (update.error) {
                app.rendalert('modal_nota_rendalert',{
                    message: update.error,
                    type: 'error'
                });
                d.getElementById('btn_nota_save').disabled = false;
            }
            return;
        } catch (error) {
            app.preload('close');
            d.getElementById('btn_nota_save').disabled = false;
            not.Error('[nota] Error al procesar','',2000);
            return;
        }
    }
    async function nota_delete(_id) {
        try {
            app.preload('open', 'eliminando nota..');
            let deletion = await mdl_notascliente.delete(_id);
            app.preload('close');
            if (deletion.success) {
                not.Ok(deletion.success, '', 2200);
                nota_get_all();
            }
            if (deletion.warning) {
                not.Aviso(deletion.warning, '', 2000);
            } 
            if (deletion.error) {
                not.Error(deletion.error, '', 2000);
            }
        } catch (error) {
            app.preload('close');
            not.Error('[nota] Error al procesar', '', 2000);
        }
    
    }


    // P R E N D A S
    async function prenda_get_all(){
        try {
            loaders.prenda.classList.remove('d-none');
            let request_data = await mdl_prenda.get_by_cliente(id_base, {
                order_by: 'creacion',
                order_type: 'desc'
            });
            if (app.empty(request_data.result)) {
                app.render('_table_prendas', `
                    <tr><td colspan="6" class="text-center">Datos no encontrados</td></tr>
                `);
                return;
            }

            let data = request_data.result;             
            let html = "";
            if (data.length) {
                let limite = local.prendas_limit_show;
                limite = (limite > 0) ? limite : data.length;

                data.forEach((prenda,ind) => {    
                    if ((ind+1) <= limite){                        
                        html += `
                            <tr>
                                <td>
                                    <a class="btn-link text-blue" href="${prenda.url}">
                                    ${ prenda.descripcion }
                                    </a>
                                </td>
                                <td>${ prenda.categoria_nombre }</td>
                                <td>${ prenda.valoracion }</td>
                                <td>${ prenda.create_at }</td>
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
                    }                
                });
                if (limite < data.length) {
                    html += `
                    <tr>
                        <td class="text-center" colspan="5">
                            <a id="prendas_vtodo" href="javascript:void(0)" class="btn btn-sm btn-outline-primary">
                                Ver Todo
                            </a>
                        </td>
                    </tr>
                    `;
                }
            }else {
                html = `
                <tr>
                    <td class="text-center" colspan="5">Sin Resultados.</td>
                </tr>
                `;
            }
            loaders.prenda.classList.add('d-none');
            app.render('_table_prendas', html);
        } catch (error) {
            loaders.prenda.classList.add('d-none');
            console.log('[prenda] '+error.message)
            app.render('_table_prendas', `
                <tr>
                    <td class="text-center" colspan="6">Error al procesar.</td>
                </tr>
            `);
        }
    }
    async function prenda_prepare_save_form(){
        try {
            app.preload('open', 'Cargando datos..'); 
            app.rendalert('modal_prenda_rendalert', {clean: true});
            let form = d.getElementById('form-prenda');
            form.dataset.mode = 'NEW';
            form.reset();            
            d.getElementById('btn_prenda_save').removeAttribute('disabled'); 
            d.getElementById('modal-prenda-title').innerHTML = `
                <i class="fe fe-plus text-blue"></i> 
                Nueva Prenda
            `;
            
            let tipo_id = await prenda_render_tipos({
                name_default: PRENDA_TIPO
            });
            await prenda_render_categorias({
                tipo_id: tipo_id
            });
            await prenda_render_form({
                mode: 'NEW',
                tipo: PRENDA_TIPO
            });
            local.prenda_edit = false;
            local.id_prenda_edit = 0;
            local.id_categoria_edit = 0;
            app.preload('close');
            $('#modal-prenda').modal('show');
        } catch (error) {
            console.log('[prenda] '+error.message)
            app.preload('close');
            not.Aviso('[prenda] Error al procesar','',2000);
            $('#modal-prenda').modal('hide');
        } 
    }
    async function prenda_prepare_edit_form(_id){
        try {
            app.preload('open', 'Cargando datos..');    
            app.rendalert('modal_prenda_rendalert', {clean: true});        
            let data = await mdl_prenda.get_data(_id);
            if (app.empty(data)) {
                not.Aviso('[prenda] Datos no encontrados','',2000);
                $('#modal-prenda').modal('hide');
                return;
            }

            let form = d.getElementById('form-prenda');
            form.reset();
            await prenda_render_tipos({id_default: data.tipo_id});
            await prenda_render_categorias({
                tipo_id: data.tipo_id,
                categoria_default: data.categoria_id
            });

            form.dataset.mode = 'EDIT';
            local.prenda_edit = true;
            local.id_prenda_edit = data.id;
            local.id_categoria_edit = data.categoria_id;
            form.idprenda.value = data.id;
            form.descripcion.value = data.descripcion;
            // app.render('prenda_form_content', data.prenda_form_string);
            await prenda_render_form({
                mode: 'EDIT',
                tipo: data.tipo_nombre,
                id: data.id
            });

            d.getElementById('btn_prenda_save').removeAttribute('disabled'); 
            d.getElementById('modal-prenda-title').innerHTML = `
                <i class="fe fe-plus text-blue"></i> 
                Editar Prenda
            `;
            
            app.preload('close');
            $('#modal-prenda').modal('show');
        } catch (error) {
            console.log('[prenda] '+error.message)
            app.preload('close');
            not.Aviso('[prenda] Error al procesar','',2000);
            $('#modal-prenda').modal('hide');
        } 
    }
    async function prenda_render_form(config = {}){
        try {
            let request;
            if (config.mode == 'EDIT'){
                request = await mdl_prenda.get_form_edit(
                    config.id,
                    config.tipo
                );
            }else {
                request = await mdl_prenda.get_form_by_tipo(config.tipo);
            }
            if (app.empty(request) || request.code != 'OK') {
                not.Aviso('[prenda] Error al obtener formulario','',2000);
                $('#modal-prenda').modal('hide');
                return;
            }
            let html = request.result;
            d.getElementById('prenda_form_content').innerHTML = html;
        } catch (error) {
            not.Aviso('[prenda] Error al procesar formulario','',2000);
            $('#modal-prenda').modal('hide');
        }
    }
    async function prenda_render_tipos(config = {}){
        try {
            let html = '';
            app.render('tprenda_render_area',`
                <div class="d-flex justify-center">
                    <div class="spinner-v1"></div>
                </div>
            `);
            let data = await mdl_tprenda.get_all();  
            if (app.empty(data)){
                app.render('tprenda_render_area',`
                    <div class="text-center">Sin Datos</div>
                `);
                return;
            }
            let id_response = 0;
            data.forEach(e => {  
                let chkd = '';              
                if (config.id_default > 0 || config.name_default) {
                    
                    let val_a = (config.id_default) ? config.id_default : config.name_default;
                    let val_b = (config.id_default) ? e.id : e.nombre;
                    if (val_a === val_b) {
                        chkd = 'checked';
                        id_response = e.id;
                    }
                }
                html += `
                <label class="custom-control custom-radio custom-control-inline">
                    <input type="radio" data-ref="${e.nombre}" data-nombre="${e.nombre}" class="custom-control-input" name="tipo_prenda" value="${e.id}" ${chkd}>
                    <span class="custom-control-label">${e.nombre}</span>
                </label>
                `;
            });
            app.render('tprenda_render_area', html);
            return id_response;
        } catch (error) {
            not.Aviso('[prenda tipos] Error al procesar','',2000);
            app.render('tprenda_render_area','[Error]')
            console.log(error)
        }
    }
    async function prenda_render_categorias(config){
        try {
            loaders.categoria.classList.remove('d-none');            
            let request = await mdl_categoria.get_by_tipo(config.tipo_id);
            if (app.empty(request)){
                app.render('categoria_list','<option value="">-Sin Datos-</option>');
                return;
            }
            let data = request;
            let html = '<option value="">-SELECCIONAR-</option>';
            data.forEach(e => {
                let chkd = (e.id == config.categoria_default) ? 'selected' : '';

                html += `
                    <option value="${e.id}" ${chkd}>${e.nombre}</option>
                `;
            });
            app.render('categoria_list',html);
            loaders.categoria.classList.add('d-none');

        } catch (error) {
            loaders.categoria.classList.add('d-none');
            console.log(error)
        }
    }
    async function prenda_save(data){
        try {
            app.preload('open','procesando..');
            app.rendalert('modal_prenda_rendalert', {clean: true});
            d.getElementById('btn_prenda_save').disabled = true;

            let save = await mdl_prenda.save(data);
            app.preload('close'); 
            if (save.success && save.id) {
                not.Ok(save.success, '', 2000);
                d.getElementById('form-prenda').reset();
                d.getElementById('form-prenda').dataset.mode = '';
                $('#modal-prenda').modal('hide');
                prenda_get_all();

                return;
            }
            if(save.error) {
                app.rendalert('modal_prenda_rendalert',{
                    message: save.error,
                    type: 'error'
                });
                d.getElementById('btn_prenda_save').disabled = false;
                return;
            }
            if (save.warning) {
                app.rendalert('modal_prenda_rendalert',{
                    message: save.warning,
                    type: 'warning'
                });
                d.getElementById('btn_prenda_save').disabled = false;
                return;
            }     
        } catch (error) {
            app.preload('close');
            let mssg = (error.name) ? 'Error de ejecucion' : error;
            d.getElementById('btn_prenda_save').disabled = false;
            not.Aviso(mssg, '', 2500); 
        }
    }
    async function prenda_update(data){
        try {
            app.preload('open','procesando..');
            app.rendalert('modal_prenda_rendalert', {clean: true});
            d.getElementById('btn_prenda_save').disabled = true;

            let update = await mdl_prenda.update(data);
            app.preload('close'); 
            if (update.success) {
                not.Ok(update.success, '', 2000);
                d.getElementById('form-prenda').reset();
                d.getElementById('form-prenda').dataset.mode = '';
                $('#modal-prenda').modal('hide');
                prenda_get_all();
                return;
            }
            if(update.error) {
                app.rendalert('modal_prenda_rendalert',{
                    message: update.error,
                    type: 'error'
                });
                d.getElementById('btn_prenda_save').disabled = false;
                return;
            }
            if (update.warning) {
                app.rendalert('modal_prenda_rendalert',{
                    message: update.warning,
                    type: 'warning'
                });
                d.getElementById('btn_prenda_save').disabled = false;
                return;
            }                         
        } catch (error) {
            app.preload('close'); 
            let mssg = (error.name) ? 'Error de ejecucion' : error;
            d.getElementById('btn_prenda_save').disabled = false;
            not.Aviso(mssg, '', 2500); 
        }
    }
    async function prenda_delete(id){
        try {
            app.preload('open', 'eliminando prenda..');
            let idcliente = id_base;
            let del = await mdl_prenda.delete(id,idcliente);
            app.preload('close'); 

            if (del.success) {
                not.Ok(del.success, '', 2200);
                prenda_get_all();
                return;
            }
            if(del.error) {
                not.Error(del.error, '', 2000);
                return;
            }
            if(del.warning) {
                not.Aviso(del.warning, '', 2000);
                return;
            } 
        } catch (error) {
            app.preload('close');
        }
    }


    // C R E D I T O S
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
            app.render('_table_creditos', html);            
        } catch (error) {
            loaders.nota.classList.add('d-none');
            app.render('_table_creditos', `
                <tr>
                    <td class="text-center" colspan="4">Error al procesar.</td>
                </tr>
            `);
        }

    }


    // C L I E N T E
    async function cliente_render_data(){
        let req = await mdl_cliente.get_data(id_base); 
        let data = req.result;
        if (app.empty(data)) {
            not.Aviso('[cliente] Datos no encontrados','',2000);
            return;
        }
        app.render('dg-nombre', `
            ${data.nombres} ${data.paterno} ${data.materno}
        `);
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
    async function cliente_prepare_edit_form(){
        try {
            app.preload('open', 'Cargando datos..');
            let req = await mdl_cliente.get_data(id_base); 
            if (app.empty(req) || app.empty(req.result)) {
                not.Aviso('[cliente] Error al cargar los datos.','',2000);
                $('#modal-cliente').modal('hide');
                return;
            }
            let data = req.result;
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
            app.preload('close');
            $('#modal-cliente').modal('show');
        } catch (error) {
            console.log(error);
            app.preload('close');
            $('#modal-cliente').modal('hide');
        }
    }
    async function cliente_update(data){
        try {
            app.preload('open','procesando..');
            d.getElementById('btn_cliente_save').disabled = true;
            app.rendalert('modal_cliente_rendalert', {clean: true});

            let update = await mdl_cliente.update(data);
            app.preload('close'); 
            if (update.success) {
                not.Ok(update.success, '', 2000);
                d.getElementById('form-cliente').reset();
                $('#modal-cliente').modal('hide');
                cliente_render_data();
                return;
            }
            if(update.warning) {
                app.rendalert('modal_cliente_rendalert',{
                    message: update.warning,
                    type: 'warning'
                });
                d.getElementById('btn_cliente_save').disabled = false;
                return;
            }            
            if(update.error) {
                app.rendalert('modal_cliente_rendalert',{
                    message: update.error,
                    type: 'error'
                });
                d.getElementById('btn_cliente_save').disabled = false;
                return;
            }               
        } catch (error) {
            d.getElementById('btn_cliente_save').disabled = false;
            app.preload('close'); 
            let mssg = (error.name) ? 'Error de ejecucion' : error;
            not.Aviso(mssg, '', 2500); 
        }
    }

})(document);