(function(d){
    let app;
    let id_base;
    let mdl_prenda, mdl_tprenda, mdl_categoria;
    let loader = d.getElementById('loader_data');
    let local = {
        id_categoria_edit: 0
    }

    d.addEventListener('DOMContentLoaded', () => {
        app = new App();
        mdl_prenda = new Prenda();
        mdl_tprenda = new TipoPrenda();
        mdl_categoria = new Categoria();
        id_base = d.getElementById('idbase').value;
    })

    // C L I C K
    d.addEventListener('click', async event => {
        let ev = event.target;
        if (ev.id === 'btn_open_edit'){
            if (app.empty(id_base)) return;
            prenda_prepare_edit_form(id_base);
        }
        if (ev.id === 'btn_delete'){
            if (app.empty(id_base) || app.empty(ev.dataset.cliente)) {
                not.Aviso('Prenda no identificada','',2000);
                return;
            }
            const aswer = Swal.fire({
                 type: 'question',
                 title: '¿Eliminar Prenda?',
                 showCancelButton: true,
                 confirmButtonColor: '#E92C4C',
                 confirmButtonText: 'Si, eliminar',
                 cancelButtonColor: '#283845',
                 cancelButtonText: 'Cancelar'
             }).then(async(result) => {
                if (result.value) {
                    prenda_delete(ev.dataset.cliente);                        
                }
            });   
        }
    });

    // C H A N G E
    d.addEventListener('change', async event => {
        let ev = event.target;
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
                if (local.id_categoria_edit > 0){
                    cat_params.categoria_default = local.id_categoria_edit
                }
                await prenda_render_categorias(cat_params);

                prenda_render_form({
                    id: id_base,
                    mode: 'EDIT',
                    tipo: ev.dataset.nombre
                });
            }
        }
    });

    // S U B M I T
    d.addEventListener('submit', async event => {
        event.preventDefault();
        let ev = event.target;
        if (ev.id === 'form-prenda') {
            let data = new FormData(ev);
            data.append('id', id_base);
            prenda_update(data);
        }
    });

    // I N P U T
    d.addEventListener('input', event => {
        let ev = event.target;
        if (ev.matches('[name=valoracion]')) {
            console.log(ev.value)
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
    });


    async function prenda_render_data(){
        try {
            loader.classList.remove('d-none');
            let data = await mdl_prenda.get_data(id_base);
            if (!data) {
                not.Aviso('Datos no encontrados','',2500);
                return;
            }
            app.render('d_descripcion',data.descripcion);
            app.render('d_estado_tag', data.estado_tag);
            app.render('data_general_content', data.prenda_data_string);
            loader.classList.add('d-none');
        } catch (error) {
            loader.classList.add('d-none');
        }
    } 
      
    

    async function prenda_prepare_edit_form(){
        try {
            app.preload('open', 'Cargando datos..');            
            let data = await mdl_prenda.get_data(id_base);
            if (app.empty(data)) {
                not.Aviso('[prenda] Datos no encontrados','',2000);
                $('#modal-prenda').modal('hide');
                return;
            }

            let form = d.getElementById('form-prenda');
            form.reset();
            await prenda_render_tipos({id_default: data.tipo_id});
            local.id_categoria_edit = data.categoria_id;
            await prenda_render_categorias({
                tipo_id: data.tipo_id,
                categoria_default: data.categoria_id
            });

            form.descripcion.value = data.descripcion;
            await prenda_render_form({
                mode: 'EDIT',
                tipo: data.tipo_nombre,
                id: data.id
            });
            d.getElementById('btn_prenda_save').removeAttribute('disabled'); 
            
            app.preload('close');
            $('#modal-prenda').modal('show');
        } catch (error) {
            console.log('[prenda] '+error.message)
            app.preload('close');
            not.Aviso('[prenda] Error al procesar','',2000);
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
            loader.classList.remove('d-none');            
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
            loader.classList.add('d-none');

        } catch (error) {
            loader.classList.add('d-none');
            console.log(error)
        }
    }
    async function prenda_render_form(config = {}){
        try {
            console.log(config)
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
                prenda_render_data();
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
            console.log(error)
            let mssg = (error.name) ? '[Editar] Error de ejecucion' : error;
            d.getElementById('btn_prenda_save').disabled = false;
            not.Aviso(mssg, '', 2500); 
        }
    }
    async function prenda_delete(idcliente){
        try {
            app.preload('open', 'eliminando prenda..');
            let del = await mdl_prenda.delete(id_base,idcliente);
            app.preload('close'); 

            if (del.success) {
                not.Ok(del.success, '', 2200);
                setTimeout(() => {
                    location.href = del.url;
                }, 1000);
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
            throw 'El proceso no se completo';  
        } catch (error) {
            app.preload('close');
            not.Error('Error al procesar', '', 2000);
        }
    }

})(document);