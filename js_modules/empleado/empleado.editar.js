(function(){
    let form_editar = document.getElementById('form_editar');
    let app;

    document.addEventListener('DOMContentLoaded', (e)=>{
        app = new App();
        consultaTipos(form_editar.dni.value);
    });
    if (document.getElementById('btn_md_ciudad')) {
        document.getElementById('btn_md_ciudad').addEventListener('click', (e)=>{
            e.preventDefault();
            buscar_ciudad();
            $('#md_ciudad').modal('show');
        })
    }
    if (document.getElementById('btn_md_sucursal')) {
        document.getElementById('btn_md_sucursal').addEventListener('click', async(event) => {
            event.preventDefault();
            buscar_sucursal();
            $('#md_sucursal').modal('show');          
        })
    }
    if (document.getElementById('input_ciudad_buscar')) {
        document.getElementById('input_ciudad_buscar').addEventListener('keyup', (evt)=>{
            e.preventDefault();
            buscar_ciudad('BUSCAR', evt.target.value);
            $('#md_ciudad').modal('show');
        })
    }
    if (form_editar) {
        form_editar.addEventListener('submit', async(event)=>{
            event.preventDefault();
            try {
                app.preload('open', 'Guardando..');
                let data = new FormData(event.target);
                let empleado = new Empleado();
                let modifica = await empleado.Actualizar(data);
                app.preload('close');
                if (modifica.status === 'OK') {   
                    not.Ok(modifica.mssg, '', 3000);
                }else{
                    if (modifica.error) feedback(modifica.error);
                    if(modifica.status === 'WARN') not.Aviso(modifica.mssg, '', 3000);
                    else not.Error(modifica.mssg, '', 3000);                    
                }
            } catch (error) {
                not.Error('Error al procesar los cambios', '', 3000); 
            }
        })
    }

    const consultaTipos = async function(current){
        try {
            document.getElementById('tdoc_container').innerHTML = `<h4 class="text-center">Cargando&nbsp;<i class="fas fa-circle-notch fa-spin"></i> `;
            const require = await fetch(srv.url()+'TipoDocumento/rest');
            const res = await require.json();
            let html = '';
            if (res.length > 0) {
                res.forEach((element,index) => {
                    let mark = (current === element.nombre) ? 'checked' : '';
                    html += `
                        <label class="custom-control custom-radio custom-control-inline">
                            <input type="radio" class="custom-control-input" name="tipo_documento" value="${element.nombre}" ${mark} required>
                            <span class="custom-control-label">${element.nombre}</span>
                        </label>
                    `;
                });
            }else{
                html = `
                    <b>No se encontraron tipos de documentos.</b>
                `;
            }
            document.getElementById('tdoc_container').innerHTML = html;
        } catch (error) {
            if (document.getElementById('tdoc_container')) {
                document.getElementById('tdoc_container').innerHTML = `<b>Error al cargar. ${error.message}</b>`;
            }
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
                    form_editar.ciudad.value = event.target.dataset.ciudad;
                    form_editar.ubigeo.value = event.target.dataset.id;
                })
            });
        } catch (error) {
            console.log(error);
            app.render('datatable_ciudad','<tr class="text-center"><td colspan="5">Error al consultar</td></tr>');
        }
    }
    const buscar_sucursal = async function(tipo = 'muestra'){
        try {
            app.render('tb_sucursal','<tr class="text-center"><td colspan="3"><div class="loader"></div>Cargando..</td></tr>');
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
            app.render('tb_sucursal',html);
            let s_item = document.getElementsByName('sucursal_item');
            s_item.forEach(element => {
                element.addEventListener('click', event => {
                    $('#md_sucursal').modal('hide');
                    form_editar.sede.value = event.target.dataset.nombre;
                    form_editar.sucursal.value = event.target.dataset.id;
                })
            });
        } catch (error) {
            console.log(error);
            app.render('tb_sucursal','<tr class="text-center"><td colspan="3">Error al consultar</td></tr>');
        }
    }
    const feedback = function(error){
        if (error.codigo) {
            not.Error(error.codigo, '', 3000);
        }
        if(error.documento) {
            app.render('documento_feed', error.documento);
        }else{
            app.render('documento_feed', '');
        }
        if(error.tipoDoc) {
            app.render('tipodoc_feed', error.tipoDoc);
        }else{
            app.render('tipodoc_feed', '');
        }
        if(error.nombre) {
            app.render('nombre_feed', error.nombre);
        }else{
            app.render('nombre_feed', '');
        }
        if(error.paterno) {
            app.render('paterno_feed', error.paterno);
        }else{
            app.render('paterno_feed', '');
        }
        if(error.materno) {
            app.render('paterno_feed', error.materno);
        }else{
            app.render('paterno_feed', '');
        }
        if(error.ubigeo) {
            app.render('ubigeo_feed', error.ubigeo);
        }else{
            app.render('ubigeo_feed', '');
        }
        if(error.direccion) {
            app.render('direccion_feed', error.direccion);
        }else{
            app.render('direccion_feed', '');
        }
        if(error.telefono) {
            app.render('telefono_feed', error.telefono);
        }else{
            app.render('telefono_feed', '');
        }
        if(error.sucursal) {
            app.render('sucursal_feed', error.sucursal);
        }else{
            app.render('sucursal_feed', '');
        }
    }

})();