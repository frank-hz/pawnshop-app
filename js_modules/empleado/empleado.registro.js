(function(){
    let app;
    const frm_registro = document.getElementById('frm_registro');
    
    document.addEventListener('DOMContentLoaded', (e)=>{
        app = new App();
        tipos_doc();
    });

    if (document.getElementById('btn_sucursal')) {
        document.getElementById('btn_sucursal').addEventListener('click', async(event) => {
            event.preventDefault();
            buscar_sucursal();
            $('#md_sucursal').modal('show');
        })
    }
    if (document.getElementById('btn_ciudad')) {
        document.getElementById('btn_ciudad').addEventListener('click', (evt)=>{
            evt.preventDefault();
            buscar_ciudad();
            $('#md_ciudad').modal('show');
        })
    }
    if (document.getElementById('input_ciudad_buscar')) {
        document.getElementById('input_ciudad_buscar').addEventListener('keyup', (evt)=>{
            evt.preventDefault();
            let search = evt.target.value.trim();
            if (search){
                buscar_ciudad('BUSCAR', search);
            }
            // $('#md_ciudad').modal('show');
        })
    }
    if (document.getElementById('input_cliente_buscar')) {
        document.getElementById('input_cliente_buscar').addEventListener('keyup', (evt)=>{
            buscar_ciudad('buscar', evt.target.value);
        })
    }   

    if (frm_registro) {
        frm_registro.addEventListener('submit', async(event)=>{
            event.preventDefault();
            try {
                app.preload('open', 'Guardando..');
                let data = new FormData(event.target);
                let empleado = new Empleado();
                let registro = await empleado.Registrar(data);
                feedback_reset();
                app.preload('close');

                if (registro.status === 'OK') {
                    event.target.reset();
                    not.Ok('Completado', 'Empleado registrado', 2500);
                    setTimeout(() => {
                        window.location.href = `${srv.url()}Empleado/detalle/${registro.id}`
                    }, 1000);
                    // app.renderAlert('ok', `
                    //     <b>${registro.mssg}</b>
                    //     <div class="d-block pt-2">
                    //         <a class="btn btn-primary btn-sm" href="${srv.url()}Empleado/detalle/${registro.id}">Ver Empleado</a>
                    //     </div>
                    // `);
                } else {
                    let tipo = (registro.status === 'WARN') ? 'aviso' : 'error';
                    // app.renderAlert(tipo, registro.mssg, 3000);                    
                    if (registro.error) feedback(registro.error);
                }
            } catch (error) {
                console.log(error);
                app.renderAlert('error', 'Error al registrar', 3000);
            }
        })
    }
    
    const tipos_doc = async function(){
        try {
            document.getElementById('tdoc_container').innerHTML = `<h4 class="text-center">Cargando&nbsp;<i class="fas fa-circle-notch fa-spin"></i> `;
            const require = await fetch(srv.url()+'TipoDocumento/rest');
            const res = await require.json();
            let html = '';
            if (res.length > 0) {
                res.forEach((element,index) => {
                    let chk = (index === 0) ? 'checked' : '';
                    html += `
                        <label class="custom-control custom-radio custom-control-inline">
                            <input type="radio" class="custom-control-input" name="tipo_documento" value="${element.nombre}" ${chk} required>
                            <span class="custom-control-label">${element.nombre}</span>
                        </label>
                    `;
                });
            }else{
                html = `
                    <b>No se encontraron tipos de documentos.</b>
                `;
            }
            app.render('tdoc_container', html);
        } catch (error) {
            app.render('tdoc_container', 'Error al cargar');
        }
    }

    const buscar_ciudad = async function(tipo = 'muestra', buscar = ''){
        try {
            app.render('datatable_ciudad','<tr class="text-center"><td colspan="5"><div class="loader"></div>Cargando..</td></tr>');
            let ubigeo = new Ubigeo();
            let ciudad = await ubigeo.ListarCiudad(tipo, buscar);
            let html = '';
            if (ciudad.status === 'OK') {
                if (ciudad.result.length > 0) {
                    ciudad.result.forEach(element => {
                        let badge = '';
                        if (element.preferencia == 'A') badge = '<i class="fas fa-bookmark text-dark" title="Favorito"></i>';
                        html += `
                            <tr>

                                <td>${element.departamento}</td>
                                <td>
                                    ${badge}
                                    ${element.distrito} 
                                </td>  
                                <td>
                                    <button name="ciudad_item" class="btn btn-outline-success" data-id="${element.id}" data-ciudad="${element.distrito}">
                                        Elegir  
                                    </button>
                                </td>
                            </tr>
                        `;
                    });
                }else{
                    html = `
                        <tr class="text-center"><td colspan="5">Sin ciudades.</td></tr>
                    `;
                }
            }else{
                html = `
                    <tr class="text-center"><td colspan="5">${ciudad.mssg}.</td></tr>
                `;
            }
            app.render('datatable_ciudad',html);
            let c_item = document.getElementsByName('ciudad_item');
            c_item.forEach(element => {
                element.addEventListener('click', event => {
                    $('#md_ciudad').modal('hide');
                    frm_registro.ciudad.value = event.target.dataset.ciudad;
                    frm_registro.ubigeo.value = event.target.dataset.id;
                })
            });
        } catch (error) {
            console.log(error);
            app.render('datatable_ciudad','<tr class="text-center"><td colspan="5">Error al consultar</td></tr>');
        }
    }

    const buscar_sucursal = async function(tipo = 'muestra'){
        try {
            app.render('datatable_sucursal','<tr class="text-center"><td colspan="3"><div class="loader"></div>Cargando..</td></tr>');
            let sucursal = new Sucursal();
            let consulta = await sucursal.Listar(tipo);
            let html = '';
            if (consulta.status === 'OK') {
                if (consulta.result.length > 0) {
                    consulta.result.forEach(element => {
                        html += `
                            <tr>
                                <td>${element.nombre}</td>
                                <td>${element.ciudad}</td>  
                                <td>
                                    <button name="sucursal_item" class="btn btn-outline-success" data-id="${element.id}" data-nombre="${element.nombre}">
                                        Elegir  
                                    </button>
                                </td>
                            </tr>
                        `;
                    });
                }else{
                    html = `<tr class="text-center"><td colspan="3">Sin ciudades.</td></tr>`;
                }
            }else{
                html = `<tr class="text-center"><td colspan="3">${consulta.mssg}.</td></tr>`;
            }
            app.render('datatable_sucursal',html);
            let s_item = document.getElementsByName('sucursal_item');
            s_item.forEach(element => {
                element.addEventListener('click', event => {
                    $('#md_sucursal').modal('hide');
                    frm_registro.sede.value = event.target.dataset.nombre;
                    frm_registro.sucursal.value = event.target.dataset.id;
                })
            });
        } catch (error) {
            console.log(error);
            app.render('datatable_sucursal','<tr class="text-center"><td colspan="3">Error al consultar</td></tr>');
        }
    }

    const feedback_reset = function(){
        app.render('feed_documento', '');
        app.render('feed_tipodoc', '');
        app.render('feed_nombre', '');
        app.render('feed_paterno', '');
        app.render('feed_materno', '');
        app.render('feed_ubigeo', '');
        app.render('feed_direccion', '');
        app.render('feed_sucursal', '');
    }

    const feedback = function(error){
        if(error.documento) app.render('feed_documento', error.documento);
        if(error.tipo_documento) app.render('feed_tipodoc', error.tipo_documento);
        if(error.nombre) app.render('feed_nombre', error.nombre);
        if(error.paterno) app.render('feed_paterno', error.paterno);
        if(error.materno) app.render('feed_materno', error.materno);
        if(error.ubigeo) app.render('feed_ubigeo', error.ubigeo);
        if(error.direccion) app.render('feed_direccion', error.direccion);
        if(error.sucursal) app.render('feed_sucursal', error.sucursal);
    }

})();

