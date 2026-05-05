(function(d){
    let app;
    let mdl_credito, mdl_nota_credito, mdl_cuota, mdl_pago;
    let id_base;    
    let local = {
        notas_stash: [],
        notas_ancladas: [],
        notas_limit: 4,
        notas_data: [],
        cuotas_limit: 4,
        cuotas_data: [],
        pagos_limit: 4,
        pagos_data: []
    }
    let loaders = {
        nota: d.getElementById('notas-loader'),
        cuota: d.getElementById('cuotas-loader'),
        pago: d.getElementById('pagos-loader')
    }
    let CREDITO_ID = 0;
    let tiposData = [];

    d.addEventListener('DOMContentLoaded', async() => {
        app = new App();
        mdl_credito = new Credito();
        mdl_nota_credito = new NotasCredito();
        mdl_cuota = new Cuota();
        mdl_pago = new Pago();

        id_base = document.getElementById('ident').value;
        if (id_base && id_base != undefined){
            nota_render_all(true);
            cuota_render_all();
            pago_render_all();
            getEmailTemplate();
        }

        CREDITO_ID = parseInt(window.credito_id) || 0;        
    });
    d.addEventListener('click', async event => {
        let ev = event.target;
        if (ev.id === 'notas_vtodo') {
            local.notas_limit = 0;
            nota_render_all(false);
        }
        if (ev.id === 'cuotas_vtodo') {
            console.log('cuotas vtodo')
            local.cuotas_limit = 0;
            cuota_render_all(false);
        }
        if (ev.id === 'pagos_vtodo') {
            local.pagos_limit = 0;
            pago_render_all(false);
        }
        if (ev.matches('[name=nota-edit]')) {
            if (ev.dataset.id) {
                let nota = await mdl_nota_credito.get_data(ev.dataset.id); 
                let form = d.getElementById('nota-form');
                form.dataset.type = 'UPD';
                form.idcode.value = nota.id;
                form.descripcion.value = nota.descripcion;
                form.anclado.value = nota.anclado;    
                app.render('modal-nota-title', `
                    <i class="fe fe-edit text-blue"></i>
                    <span>Editar Nota</span>
                `);     
                $('#modal-nota').modal('show');
            }
        }
        if (ev.id === 'btn-nota-new') {
            let form = d.getElementById('nota-form');
            form.reset();
            form.dataset.type = 'NEW';
            app.render('modal-nota-title', `
                <i class="fe fe-file-plus text-blue"></i>
                <span>Nueva Nota</span>
            `);
            $('#modal-nota').modal('show');
        }
        if (ev.matches('[name=nota-delete]')) {
            if (!ev.dataset.id) not.Aviso('Nota no identificada','',1000);
            if (confirm('Eliminar Nota?')){
                let del = await mdl_nota_credito.delete(ev.dataset.id);
                if (del.code === 'OK'){
                    not.Ok('',del.message,2000);
                    nota_render_all(true);
                }else {
                    not.Aviso('',del.message, 2000);
                }
            }
        }
        if (ev.id === 'btn_ccongela') {
            if (id_base){
                const aswer = Swal.fire({
                    imageUrl: srv.url()+'assets/images/app_icon/pause.svg',
                    imageWidth: 200,
                    imageHeight: 100,
                    html: '���<b>Congelar</b> credito?',
                    showCancelButton: true,
                    confirmButtonColor: '#3fd0d4',
                    confirmButtonText: `Si, congelar` ,
                    cancelButtonColor: '#283845',
                    cancelButtonText: 'Cancelar'
                }).then(async(result) => {
                    if (result.value) {
                        try {
                            app.preload('open', 'Procesando..');
                            let mdl_credito = new Credito();
                            let congela = await mdl_credito.apply_congelacion(id_base);
                            app.preload('close');
                            if (congela.status === 'OK') {
                                not.Ok(congela.mssg, '', 2000);
                                setTimeout(() => {
                                    window.location.reload();
                                }, 350);
                            }else{
                                if (congela.status === 'WARN') 
                                    not.Aviso(congela.mssg, '', 2200);
                                else
                                    not.Error(congela.mssg, '', 2200);
                            }
                        } catch (error) {
                            console.log(error)
                            app.preload('close');
                            not.Error('Error al procesar', '', 2200);
                        }
                    }
                });
            }
        }
        if (ev.id === 'btn_remate') {
            if (id_base) {
                const aswer = Swal.fire({
                    imageUrl: srv.url()+'assets/images/app_icon/shop.svg',
                    imageWidth: 200,
                    imageHeight: 100,
                    html: '\u00BFRematar</b> credito?',
                    showCancelButton: true,
                    confirmButtonColor: '#0070E8',
                    confirmButtonText: `Si, rematar` ,
                    cancelButtonColor: '#283845',
                    cancelButtonText: 'Cancelar'
                }).then(async(result) => {
                    if (result.value) {
                        try {
                            app.preload('open', 'Procesando..');
                            let mdl_credito = new Credito();
                            let remata = await mdl_credito.apply_remate(id_base);
                            app.preload('close');
                            if (remata.status === 'OK') {
                                not.Ok(remata.mssg, '', 3000);
                                setTimeout(() => {
                                    window.location.reload();
                                }, 350);
                            }else{
                                if (remata.status === 'WARN') 
                                    not.Aviso(remata.mssg, '', 3000);
                                else
                                    not.Error(remata.mssg, '', 3000);
                            }
                        } catch (error) {
                            not.Error('Error al procesar', '', 3000);
                        }
                    }
                });
            }
        }
        if (ev.id === 'btn_delete') {
            console.log(id_base)
            if (!id_base) not.Aviso('Credito no identificado','',1000);
            const aswer = await Swal.fire({
                type: 'question',
                title: 'Eliminar',
                text: 'Eliminar los datos del credito?',
                showCancelButton: true,
                confirmButtonColor: '#E92C4C ',
                confirmButtonText: 'Si, eliminar',
                cancelButtonColor: '#283845',
                cancelButtonText: 'Cancelar',
                allowOutsideClick: false
            }).then(async(result) => {
                if (result.value) {
                    try {
                        let del = await mdl_credito.delete(id_base);
                        if (del.code === 'OK'){
                            not.Ok('Credito eliminado','',2000);
                            setTimeout(() => {
                                window.location.href = srv.url()+'credito';
                            }, 350);
                        }else {
                            not.Aviso('',del.message, 2000);
                        }
                    } catch (error) {
                        console.log(error)
                        not.Error('Error al procesar', '', 3000);
                    }
                }
            });
        }
    });
    d.addEventListener('submit', async event => {
        event.preventDefault();
        let ev = event.target;
        if (ev.id === 'nota-form') {
            try {
                let data = new FormData(ev);
                let process;
                if (ev.dataset.type === 'NEW') {
                    data.append('credito', id_base);
                    process = await mdl_nota_credito.save(data); 


                }else if(ev.dataset.type === 'UPD'){
                    data.append('id', ev.idcode.value);
                    process = await mdl_nota_credito.update(data);
                }    

                if (process && process.code === 'OK') {
                    not.Ok('', process.message, 2500);
                    $('#modal-nota').modal('hide');
                    d.getElementById('nota-form').reset();
                    nota_render_all(true);
                }else {
                    let msg = 'Error al procesar';
                    not.Aviso('', msg, 2500);
                }
            } catch (error) {
                console.log(error)
            }
        }
    });

    d.getElementById('btn_correo_open')?.addEventListener('click', async evt => {
        try {
            if (!CREDITO_ID || CREDITO_ID <= 0){
                not.Aviso('Credito no identificado', '', 3000);
                return;
            }
            let data = await getDataCredito();    
            if (!EmailValido(data.cliente_email)){
                not.Aviso('Correo no valido', '', 3000);
                return;
            }
            d.getElementById('buttonEmailSubmit')?.classList.remove("btn-loading");
            d.getElementById('FORM_ENVIO_CORREO').reset();
            $("#MODAL_CORREO").modal("show");
        } catch (error) {
            console.log('[error] '+error.message);
        }
    });

    d.getElementById('FORM_ENVIO_CORREO')?.addEventListener('submit', async evt => {
        try {
            evt.target.buttonEmailSubmit.classList.add("btn-loading");
            let correo = evt.target.correo.value;
            let asunto = evt.target.asunto.value;
            let cuerpo = evt.target.cuerpo.value;
            // let pie = evt.target.pie.value ?? "";

            if (!correo || !asunto || !cuerpo){
                not.Aviso("Datos incompletos y/o invalidos.","",2000);
                return;
            }
            const envio = await procesarEnvioCorreo(correo, asunto, cuerpo);
            if (envio){
                $("#MODAL_CORREO").modal("hide");
                return;
            }
            evt.target.buttonEmailSubmit.classList.remove("btn-loading");
        } catch (error) {
            evt.target.buttonEmailSubmit.classList.remove("btn-loading");
            not.Aviso("","Ocurrio un error al procesar.");
            console.log(error);
        }
    })

    // const items = d.getElementsByName('tipo_notificacion');
    // if (items && items.length){
    //     items.forEach(element => {
    //         element.addEventListener('change', evt => {
    //             console.log('ey!');
    //         })
    //     });
    // }
    

    async function nota_process_all_data(){
        try {   
            let res = await mdl_nota_credito.get_all(id_base);      
            if (res) {
                html = "";
                local.notas_ancladas = res.filter((e,ind) => {
                    if (e.anclado == 'SI') return e;
                })
                
                local.notas_stash = res.filter((e,ind) => {
                    if (e.anclado == 'NO') return e;
                });
            }
            
        } catch (error) {
            console.log(error)
        }
    }
    async function nota_render_all(recall = true){
        try {
            loaders.nota.classList.remove('d-none');
            if (recall) await nota_process_all_data();
            let html = `
                <tr>
                    <td class="text-center" colspan="4">Sin Resultados</td>
                </tr>
            `;
            data = local.notas_ancladas.concat(local.notas_stash);
            if (data.length) {
                html = "";
                let limite = (local.notas_limit > 0) ? local.notas_limit : data.length;
                data.forEach((nota, ind) => {
                    let style = (nota.anclado === 'SI') ? 'text-anclado font-weight-bold' : '';
                    let icon = (nota.anclado === 'SI') ? `<i class="fa fa-thumbtack"></i>&nbsp;` : '';
                    if ((ind+1) <= limite) {
                        html += `
                        <tr>
                            <td class="${style}">
                                ${icon}
                                ${nota.descripcion.toLowerCase()}
                            </td>
                            <td>${nota.fecha}</td>
                            <td>
                                ${
                                    nota.empleado_nombre_full.slice(0, 15) + (nota.empleado_nombre_full.length > 15 ? "..." : "")
                                }
                            </td>
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
                });
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
            }
            loaders.nota.classList.add('d-none');
            app.render('dataset_notas', html);
            
        } catch (error) {
            loaders.nota.classList.add('d-none');
            app.render('dataset_notas', `
                <tr>
                    <td class="text-center" colspan="4">Error al procesar</td>
                </tr>
            `);
        }
    }
    async function cuota_render_all(request = true){
        try {
            loaders.cuota.classList.remove('d-none');
            if (request) {
                let req = await mdl_cuota.get_by_credito(id_base, {orden_tipo: 'DESC'});
                local.cuotas_data = req.result;
            }
            let data = local.cuotas_data;
            let html = `
                <tr>
                    <td class="text-center" colspan="6">Sin Resultados</td>
                </tr>
            `;
            if (data.length) {
                html = '';
                let limite = (local.cuotas_limit > 0) ? local.cuotas_limit : data.length;
                data.forEach((cuota,ind) => {
                    if ((ind+1) <= limite) {
                        // html += `
                        // <tr>
                        //     <td>${cuota.orden}</td>
                        //     <td>${cuota.vencimiento}</td>
                        //     <td>S/ ${cuota.monto}</td>
                        //     <td>
                        //         ${(['E','V'].includes(cuota.estado)) ? cuota.dias_atraso : 0}
                        //     </td>
                        //     <td>${cuota.mora}</td>
                        //     <td>${cuota.estado_tag}</td>                        
                        // </tr>
                        // `;
                        html += `
                        <tr>
                            <td>${cuota.orden}</td>
                            <td>${cuota.vencimiento}</td>
                            <td>S/ ${cuota.monto}</td>
                            <td>${cuota.estado_tag}</td>                        
                        </tr>
                        `;
                    }
                });
                if (limite < data.length) {
                    html += `
                    <tr>
                        <td class="text-center" colspan="4">
                            <a id="cuotas_vtodo" href="javascript:void(0)" class="btn btn-sm btn-outline-primary">
                                Ver Todo
                            </a>
                        </td>
                    </tr>
                    `;
                }
            }

            loaders.cuota.classList.add('d-none');
            app.render('dataset_cuotas', html);

        } catch (error) {
            app.render('dataset_cuotas', `
                <tr>
                    <td class="text-center" colspan="6">Error al procesar</td>
                </tr>
            `);
            let mssg = (error.name) ? 'error de ejecucion' : error;
            console.log(error)
            not.Aviso('[Cuotas] '+mssg, '', 2500); 
            loaders.cuota.classList.add('d-none');
        }
    }
    async function pago_render_all(request = true){
        try {
            loaders.pago.classList.remove('d-none');
            if (request) {
                let req = await mdl_pago.get_by_credito(id_base, {orden_tipo: 'DESC'});
                local.pagos_data = req.result;
            }
            let data = local.pagos_data;
            let html = `
                <tr>
                    <td class="text-center" colspan="6">Sin Resultados</td>
                </tr>
            `;
            if (data.length) {
                html = '';
                let limite = (local.pagos_limit > 0) ? local.pagos_limit : data.length;
                data.forEach((pago,ind) => {
                    if ((ind+1) <= limite) {
                        html += `
                        <tr>
                            <td>
                                <a href="${pago.url}" class="btn-link">
                                    ${pago.serie}
                                </a> 
                            </td>
                            <td>${pago.fecha}</td>
                            <td>${pago.forma_pago}</td>
                            <td>${pago.concepto}</td>
                            <td>S/ ${pago.total}</td>
                            <td>
                                <a href="${pago.url}" class="btn-cell">
                                    <span class="fe fe-layers"></span> 
                                </a>
                            </td>                       
                        </tr>
                        `;
                    }
                });
                if (limite < data.length) {
                    html += `
                    <tr>
                        <td class="text-center" colspan="6">
                            <a id="pagos_vtodo" href="javascript:void(0)" class="btn btn-sm btn-outline-primary">
                                Ver Todo
                            </a>
                        </td>
                    </tr>
                    `;
                }
            }
            loaders.pago.classList.add('d-none');
            app.render('dataset_pagos', html);

        } catch (error) {
            app.render('dataset_pagos', `
                <tr>
                    <td class="text-center" colspan="6">Error al procesar</td>
                </tr>
            `);
            loaders.pago.classList.add('d-none');
        }
    }
    
    async function getDataCredito(){
        try {
            let data = new FormData();
            data.append("id_credito", CREDITO_ID);

            let request = await fetch(app.URL("creditoAPI/getData"), {
                method: 'POST',
                body: data
            })
            let resp = await request.json();
            return resp ?? false;
        } catch (error) {
            return;
        }
    }
    function EmailValido(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }
    async function procesarEnvioCorreo(correo_, asunto_, cuerpo_){
        try {
            const data = new FormData();
            data.append("correo_destino", correo_);
            data.append("asunto", asunto_);
            data.append("cuerpo", cuerpo_);
            // data.append("pie", pie_);
            const request = await fetch(app.URL("api/v1/cliente/sendEmail"), {
                method: 'POST',
                body: data
            });
            const resp = await request.json();
            if (resp.ok) {
                not.Ok(resp.ok, "", 3000);
                $("#MODAL_CORREO").modal("hide");
                return true;
            }else {
                not.Aviso((resp.error ?? "Proceso no completado"), "", 2500);
                return;
            }
        } catch (error) {
            not.Error("Error de procesamiento", "", 2500);
            return;
        }
    }
    
    async function getEmailTemplate(){
        try {
            const req = await fetch(app.URL('NotTemplate/get_all'), {
                method: 'POST',
                body: JSON.stringify({credito_id: id_base})
            });
            const resp = await req.json();

            const tipoContainer = d.getElementById('TIPO_EMAIL');
            tipoContainer.innerHTML = '';
            if (resp && Array.isArray(resp)){
                tipoContainer.innerHTML = resp.map(item => `
                    <label class="selectgroup-item">
                        <input type="radio" name="tipo_notificacion" value="${item.id}" class="selectgroup-input">
                        <span class="selectgroup-button">${item.nombre}</span>
                    </label>
                `).join('');
                tiposData = resp;
                d.getElementsByName('tipo_notificacion').forEach(element => {
                    element.addEventListener('change', evt => {
                        if (!evt.target.value){
                            not.Aviso("",'<p class="text-center">Tipo no encontrado.</p>',2500);
                            return;
                        }
                        let tipo = tiposData.find(item => item.id === evt.target.value);
                        if (!tipo){
                            not.Aviso("",'<p class="text-center">Plantilla no encontrada.</p>',2500);
                            return;
                        }
                        const form_email = d.getElementById('FORM_ENVIO_CORREO');
                        form_email.asunto.value = tipo.asunto;
                        form_email.cuerpo.value = tipo.cuerpo_repl;
                        // form_email.pie.value = tipo.footer;
                    })
                });
            }else {
                not.Aviso("",'<p class="text-center">Plantilla no encontrada.</p>',2500);
            }
        } catch (error) {
            console.log(`[Email] ${error.message}`);
            not.Aviso("","Error al cargar plantillas.",2500);
        }
    }

    // function EliminarCredito(event){
    //     try {
    //         if (event.target.dataset.id) {
    //             if (confirm('Eliminar credito?')) {
    //                 app.preload('open', 'Procesando..');
    //                 let credito = new Credito();
    //                 let elimina = await credito.Eliminar(event.target.dataset.id);

    //                 app.preload('close');
    //                 if (elimina.status === 'OK') {
    //                     not.Ok(elimina.mssg, '', 3000);
    //                     setTimeout(() => {
    //                         window.location.href = srv.url()+'Credito';
    //                     }, 500);
    //                 }else{
    //                     if (elimina.status === 'WARN') 
    //                         not.Aviso(elimina.mssg, '', 3000);
    //                     else
    //                         not.Error(elimina.mssg, '', 3000);
    //                 }
    //             }
    //         }else {
    //             console.log('no code');
    //         }
    //     } catch (error) {
    //         not.Error('Error al procesar', '', 3000);
    //     }
    // }
})(document);