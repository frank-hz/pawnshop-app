(function(d){
    let app;
    const cotizacion_url = 'app/cuota_simulation';
    d.addEventListener('DOMContentLoaded', async event => {
        app = new App();
        set_enviroment_style();
        menu_dashboard_active();
    })
    d.addEventListener('click', async event => {
        let ev = event.target;
        if (ev.matches('[name=sede-item]')) {
            console.log('cambiando..')
            if (!ev.dataset.id) return;
            const aswer = Swal.fire({
                type: 'question',
                title: '¿Cambiar de Sucursal?',
                showCancelButton: true,
                confirmButtonColor: '#2e45dd',
                confirmButtonText: 'Si, cambiar',
                cancelButtonColor: '#283845',
                cancelButtonText: 'Cancelar'
            }).then(async(result) => {
                if (result.value) {
                    // establecer_sucursal(ev.dataset.id, function(){
                    //     d.getElementsByName('sede-item').forEach(e => e.classList.remove('sede-active'));
                    //     ev.classList.add('sede-active')
                    // });
                    establecer_sucursal(ev.dataset.id)
                }
            });
        }
        if (ev.id === 'dash-btn-define-sede') {
            const req = await fetch(srv.url()+'Sucursal/service_get_all');
            const res = await req.json();
            let html = '';
            if (res.result.length > 0) {
                res.result.forEach(e => {
                    let sede_actual = res.idc;
                    let style = (sede_actual === e.id) ? 'sede-active' : '';
                    html += `
                        <div class="sede-item ${style}" name="sede-item" data-id="${e.id}">                            
                            <div class="sede-item-code">${e.serie}</div>
                            <div class="sede-item-title">${e.nombre}</div>
                            <div class="sede-item-subtitle">${e.ciudad}</div>
                        </div>
                    `;
                });
            }else {
                html = 'Datos no encontrados';
            }
            app.render('tb_sucursal_definir', html);
            $('#md_definir_sucursal').modal('show');
        }
    })
    if (document.getElementById('sesionLink')) {
        document.getElementById('sesionLink').addEventListener('click', (e)=>{
            e.preventDefault();
            const conf = Swal.fire({
                text: '¿Seguro que quieres cerrar sesion?',    
                showCancelButton: true,
                confirmButtonColor: '#42B883',
                cancelButtonColor: '#35495E',
                confirmButtonText: 'Si, salir',
                cancelButtonText: 'No, quedarme'
            }).then(async(result) => {
                if (result.value) {
                    const response = await fetch(srv.url()+'api/v1/user/logout',{
                        method: 'POST'
                    });
                    const res = await response.json();  

                    let t = (res.code === 'OK') ? 'success' : 'error';
                    Swal.fire({
                        type: t,
                        title: res.message
                    });
                    if (res.code === 'OK') {
                        setTimeout(() => {
                            window.location.reload();
                        }, 300);
                    }
                }
            });
        })
    }  
    if (d.getElementById('toggle-btn')) {
        d.getElementById('toggle-btn').addEventListener('click', event => {
            console.log(event.target)
            event.target.classList.toggle('toggle-active')
        })
    }


    function cotizador_set_values(reset = false, values = {}){
        const cuota     = d.getElementById('cotiz_cuota_value');
        const penalidad = d.getElementById('cotiz_penalidad_value');
        const tasa      = d.getElementById('cotiz_tasa_value');

        if (reset) {
            cuota.textContent = 'S/ 0.00';
            penalidad.textContent = 'S/ 0.00';
            tasa.textContent = '--';
            return;
        }
        cuota.textContent = values.cuota;
        penalidad.textContent = values.penalidad;
        tasa.textContent = values.tasa === 'P' ? 'Porcentual' : 'Fijo';
    }
    d.getElementById('layout_btn_cotizador')?.addEventListener('click', evt => {
        const modal = d.getElementById('layout_modal_cotizador');
        const form  = d.getElementById('layout_form_cotizador');
        
        if (!modal) {
            console.warning('Modal cotizador no encontrado.');
            return;
        }
        if (form && form instanceof HTMLFormElement) form.reset();
        cotizador_set_values(true);
        modal.classList.add('is-visible');
    });
    d.getElementById('layout_modal_close')?.addEventListener('click', evt => {
        const modal = d.getElementById('layout_modal_cotizador');
        if (!modal) {
            console.warning('Modal cotizador no encontrado.');
            return;
        }
        modal.classList.add('closing');
        
        setTimeout(() => {
            modal.classList.remove('is-visible');
            modal.classList.remove('closing');
        }, 300);
    });
    d.getElementById('layout_form_cotizador')?.addEventListener('submit', async evt => {
        evt.preventDefault();
        try {
            const log = d.getElementById('cotiza_log');
            log.textContent = '';
            log.className = 'text-center';
            const form_data = new FormData(evt.target); 
            const request = await fetch(app.URL(cotizacion_url), {
                method: 'POST',
                body: form_data
            });
            const resp = await request.json();
            console.log(resp)
            if (!resp.success) {
                log.classList.add('text-danger');
                log.textContent = 'Ocurrio un error inesperado.';
                return;
            }
            if (!resp.result) {
                log.classList.add('text-danger');
                log.textContent = 'Ocurrio un error al obtener datos.';
                return;
            }
            const data = resp.result;
            cotizador_set_values(false, {
                cuota: `S/ ${data.cuota}`,
                penalidad: `S/ ${data.penalidad}`,
                tasa: data.tipo_tasa
            });

        } catch (error) {
            
        }
    });
    

    async function establecer_sucursal(id){
        try {
            app.preload('open','Cambiando..');
            const req = await fetch(srv.url()+'api/v1/session/sede/change', {
                method: 'POST',
                body: JSON.stringify({id: id}),
                headers: {
                    'Content-Type': 'application/json;charset=UTF-8'
                }
            });
            const res = await req.json();
            app.preload('close');
            if (res.status === 'OK') {
                not.Ok(res.message, '', 1500);
                // callback();
                window.location.href = srv.url();
                window.location.reload();
                
            }else {
                not.Aviso(res.message, '', 2000);
            }
        } catch (error) {
            app.preload('close');
            console.log(error);
        }
    }

    function set_enviroment_style(){
        let dash = document.getElementById('dashboard-app');
        let tag = document.getElementById('testing-tag');
        if (!dash || !tag) return;

        if (APP_CONFIG.ENV === 'TESTING') {
            dash.classList.add('dash-testing');
            tag.classList.remove('d-none');
        }
        if (APP_CONFIG.ENV === 'DEV') {
            dash.classList.add('dash-testing');
            tag.classList.remove('d-none');
        }

    }

    function menu_dashboard_active(){
        try {
            let url = window.location.href.split('/');
            let url_module = url[4].replace("#", "");
            url_module = url_module.toUpperCase();
            url_module = (url_module) ? url_module : 'INICIO';

            let items = d.getElementsByName('menu-header-item');
            // d.querySelector('.menu-spring-item-active').classList.remove('menu-spring-item-active');
            if (items.length) {
                items.forEach(e => {                    
                    if (e.dataset.ref) {
                        let name_module = e.dataset.ref.toUpperCase();
                        if (name_module === url_module) {
                            e.classList.add('menu-spring-item-active');
                        } else{
                            e.classList.remove('menu-spring-item-active');
                        }
                    }
                })
            }
        } catch (error) {
            console.warn('[Dashboard Menu] '+error.message)
            console.log(error)
        }
    }

})(document);
