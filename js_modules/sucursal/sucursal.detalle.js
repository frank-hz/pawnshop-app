(function(d){
    let app;
    let idsuc;
    
    d.addEventListener('DOMContentLoaded', () => {
        app = new App();
        idsuc = d.getElementById('idsuc').value;
        empleado_render_data();
    })
    d.addEventListener('click', async event => {
        let ev = event.target;
        if (ev.id === 'btn-data-editar') {
            if (!idsuc) return;
            
            let data = await sucursal_get_data();
            if (!data || Object.keys(data).length <= 0) return;
            let form = d.getElementById('form-edit');
            form.reset();
            form.nombre.value = data.nombre;
            // form.serie.value = data.serie;
            // form.pago_serie.value = data.pago_serie;
            form.direccion.value = data.direccion;
            form.telefono.value = data.telefono;
            form.cuenta_tipo.value = data.cuenta_tipo;
            form.cuenta_numero.value = data.cuenta_numero;
            form.ubigeo.value = data.ubigeo;
            form.distrito.value = `${data.distrito}, ${data.departamento}`;
            form.banco.value = data.banco;
            $('#modal-edit').modal('show');
        }
        if (ev.matches('[name=distrito-item]')) {
            let form = d.getElementById('form-edit');
            let list = d.getElementById('distrito-search-list'); 
            if (ev.dataset.ubigeo) {
                form.ubigeo.value = ev.dataset.ubigeo;
                form.distrito.value = ev.dataset.name;
                list.innerHTML = '';
            } 
        }
    })
    d.addEventListener('keyup', async event => {
        let ev = event.target;
        if (ev.matches('[name=distrito]')) {
            let data = await distrito_search_data(ev.value);
            distrito_render_data(data);
        } 
    })
    d.addEventListener('submit', async event => {
        event.preventDefault();
        let ev = event.target;
        if (ev.id === 'form-edit') {
            let data = new FormData(ev);
            data.append('id', idsuc);
            sucursal_update_data(data);
        }
    })


    async function sucursal_get_data(){
        try {
            let req = await fetch(srv.url()+'Sucursal/service_get_data',{
                method: 'POST',
                body: JSON.stringify({id: idsuc})
            });
            let res = await req.json();
            return res;
        } catch (error) {
            return;
        }
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
    async function sucursal_update_data(_data){
        const req = await fetch(srv.url()+'Sucursal/service_update_data', {
            method: 'POST',
            body: _data
        });
        const res = await req.json();
        if (res.status === 'OK') {
            not.Ok(res.message, '', 2500);
            $('#modal-edit').modal('hide');
            sucursal_render_data();
        }else {
            not.Aviso(res.message, '', 2400);
        }
    }
    async function sucursal_render_data(){
        let data = await sucursal_get_data();
        if (!data && Object.keys(data).length <= 0) {
            not.Aviso('Error al cargar los datos de la sucursal.','',2500);
            return;
        }
        app.render('dg-nombre', `${data.nombre}, ${data.distrito}`);
        app.render('dg-serie', data.serie);
        app.render('dg-seriepago', data.serie_pago);
        app.render('dg-distrito', `${data.distrito}, ${data.departamento}`);
        app.render('dg-telefono', data.telefono);
        app.render('dg-direccion', data.direccion);
        app.render('dg-cuentatipo', data.cuenta_tipo);
        app.render('dg-cuentanumero', data.cuenta_numero);
        app.render('dg-banco', data.banco);

    }

    async function empleado_render_data(){
        try {
            let req = await fetch(srv.url()+'Empleado/service_get_all_by_sucursl',{
                method: 'POST',
                body: JSON.stringify({id: idsuc})
            });
            let res = await req.json();

            let html = '';
            if (res && res.length > 0) {
                let states = {'A': 'Activo','I': 'Inactivo'};
                res.forEach((e,index) => {
                    html += `
                        <tr>
                            <td>${index+1}</td>
                            <td>
                                <a href="${srv.url()}Empleado/detalle/${e.id}" class="btn-link text-indigo">
                                ${e.nombres} ${e.paterno} ${e.materno}
                                </a>
                            </td>
                            <td>${e.documento}</td>
                            <td>${e.telefono}</td>
                            <td>${states[e.estado]}</td>
                            <td></td>
                        </tr>
                    `;
                })
            }else {
                html = `
                <tr class="text-center">
                    <td colspan="6">No se encontraron datos.</td>
                </tr>
                `;
            }
            app.render('datatable-empleados', html);
        } catch (error) {
            console.log(error)
        }
    }

})(document);