(function(d,w){
    let app;
    const FORM = document.getElementById('_form');
    const archivosPermitidos = ['image/png','image/jpeg','image/jpg'];
    const img_preview = document.getElementById('imagePrev');
    const BtnSubmit = document.getElementById('btnSubmit');

    document.addEventListener('DOMContentLoaded', (e)=>{
        app = new App();
        consultaTipos();
    });

    if (document.getElementById('btn_md_ciudad')) {
        document.getElementById('btn_md_ciudad').addEventListener('click', event => {
            event.preventDefault();
            getCiudad();
            $('#md_ciudad').modal('show');
        })
    }
    if (document.getElementById('input_ciudad_buscar')) {
        document.getElementById('input_ciudad_buscar').addEventListener('keyup', (e)=>{
            getCiudad('buscar', e.target.value);
        })
    }   
    if (document.getElementById('foto-remove')) {
        document.getElementById('foto-remove').addEventListener('click', (event)=>{
            event.preventDefault();
            img_preview.setAttribute('src', srv.url()+'uploads/default/sin_imagen.jpg' );
            event.target.style.display = 'none';
            FORM.foto.value = '';
        })
    }
    if (FORM) {
        FORM.addEventListener('submit', async(event)=>{
            event.preventDefault();
            try {
                app.preload('open', 'Registrando..');
                BtnSubmit.disabled = true;
                let data = new FormData(event.target);
                let request = await fetch(app.URL('api/v1/cliente/save'),{
                    method: 'POST',
                    body: data
                });
                let registro = await request.json();
                app.preload('close');
                if (registro.ok || registro.success) {
                    event.target.reset();
                    await Swal.fire({
                        type: 'success',
                        title: 'Cliente registrado'
                    });
                    setTimeout(() => {
                        location.href = app.URL('Cliente/detalle/'+registro.id);
                    }, 180);
                }else{
                    BtnSubmit.disabled = false;
                    if (registro.errors) feedback(registro.errors);
                    registro.warning 
                    ? not.Aviso(registro.warning, '', 3000) 
                    : not.Error(registro.error, '', 3000);                  
                }
            } catch (error) {
                console.log(error);
                app.preload('close');
                BtnSubmit.disabled = false;
                not.Error('Error al registrar', '', 3000);
            }
        })
    }
    async function consultaTipos(){
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
            document.getElementById('tdoc_container').innerHTML = html;
        } catch (error) {
            if (document.getElementById('tdoc_container')) {
                document.getElementById('tdoc_container').innerHTML = `<b>Error al cargar. ${error.message}</b>`;
            }
        }
    }
    async function getCiudad(tipo = 'muestra', buscar = ''){
        try {
            app.render('tdata__ciudad', '<tr class="text-center"><td colspan="3">Cargando..</td></tr>');
            let ubigeo = new Ubigeo();
            let ciudad = await ubigeo.ListarCiudad(tipo, buscar);
            let html = '';
            if (ciudad.status === 'OK') {
                if (ciudad.result.length > 0) {
                    ciudad.result.forEach(element => {
                        let badge = '';
                        if (element.preferencia == 'A') {
                            badge = '<i class="fas fa-bookmark text-dark" title="Favorito"></i>';
                        }
                        html += `
                            <tr>
                                <td>${element.departamento}</td>
                                <td>${element.distrito} </td>  
                                <td>
                                    <button name="ciudad_item" class="btn btn-sm btn-outline-primary" data-codigo="${element.id}" data-ciudad="${element.distrito}">
                                        Elegir
                                        <i class="fe fe-check-circle"></i>   
                                    </button>
                                
                                </td>
                            </tr>
                        `;
                    });
                }else{
                    html = `
                        <tr class="text-center"><td colspan="3">Sin ciudades.</td></tr>
                    `;
                }
            }else{
                html = `
                    <tr class="text-center"><td colspan="3">${ciudad.mssg}.</td></tr>
                `;
            }
            app.render('tdata__ciudad', html);
            let items = document.getElementsByName('ciudad_item');
            items.forEach(element => {
                element.addEventListener('click', event => {
                    try {
                        $('#md_ciudad').modal('hide');
                        FORM.ciudad.value = event.target.dataset.ciudad;
                        FORM.ubigeo.value = event.target.dataset.codigo;
                    } catch (error) {
                        not.Error('Error al seleccionar la ciudad', '', 3000);
                        console.log(error);
                    }
                })
            });
        } catch (error) {
            not.Error('Error al obtener las ciudades', '', 3000);
            console.log(error);
        }
    }
    function feedback(error){
        if(error.documento) app.render('feed_documento', error.documento); else app.render('feed_documento', '');
        if(error.tipo_documento) app.render('feed_tipodoc', error.tipo_documento); else app.render('feed_tipodoc', '');
        if(error.nombre) app.render('feed_nombre', error.nombre); else app.render('feed_nombre', '');
        if(error.paterno) app.render('feed_paterno', error.paterno); else app.render('feed_paterno', '');
        if(error.materno) app.render('feed_materno', error.materno); else app.render('feed_materno', '');
        if(error.ubigeo) app.render('feed_ubigeo', error.ubigeo); else app.render('feed_ubigeo', '');
        if(error.direccion) app.render('feed_direccion', error.direccion); else app.render('feed_direccion', '');
        if(error.telefono) app.render('feed_telefono', error.telefono); else app.render('feed_telefono', '');
        if(error.email) app.render('feed_email', error.email); else app.render('feed_email', '');
    }



    // if (FORM.foto) {
    //     FORM.foto.addEventListener('change', (e)=>{
    //         let archivo = e.target.files[0];
    //         let reader = new FileReader();
    //         reader.onload = function(){
    //             let peso = (parseFloat(archivo.size)/1024).toFixed(2);  
    //             let dataURL = reader.result;
    //             if (peso <= 5120) {
    //                 if (archivosPermitidos.includes(archivo.type)) {
    //                     img_preview.setAttribute('src', dataURL);
    //                     if (document.getElementById('foto-remove')) {
    //                         document.getElementById('foto-remove').style.display = '';
    //                     }
    //                 }else{
    //                     not.Aviso('Foto', 'Formato invalido, solo se permiten imagenes.');
    //                 }
    //             }else{
    //                 not.Aviso('Foto', 'El peso maximo permitido para archivos es de 5MB.');
    //                 e.target.value = '';
    //             }
    //         };
    //         reader.readAsDataURL(archivo);
    //     })
    // }

})(document,window);
