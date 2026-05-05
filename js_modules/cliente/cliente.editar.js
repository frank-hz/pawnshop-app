(function(){
    let app;
    let formatos = ['image/png','image/jpeg','image/jpg'];
    let img_default = 'uploads/default/sin_imagen.jpg';
    let thisForm = document.getElementById('frm_editar');
    let data_img = null;
    let tmp = {img: ''};
    
    // Eventos
    document.addEventListener('DOMContentLoaded', event => {
        app = new App();
    })
    if (document.getElementById('btn_md_ciudad')) {
        document.getElementById('btn_md_ciudad').addEventListener('click', event => {
            event.preventDefault();
            buscarCiudad();
            $('#md_ciudad').modal('show');
        })
    }
    if (document.getElementById('input_ciudad_buscar')) {
        document.getElementById('input_ciudad_buscar').addEventListener('keyup', (event)=>{
            buscarCiudad('buscar', event.target.value);
        })
    }
    if (document.getElementById('imagen')) {
        document.getElementById('imagen').addEventListener('change', event => {
            let archivo = event.target.files[0];
            let reader = new FileReader();
            reader.onload = function(){
                data_img = null;
                let peso = (parseFloat(archivo.size)/1024).toFixed(2);  
                let dataURL = reader.result;
                if (peso <= 5120) {
                    if (formatos.includes(archivo.type)) {
                        let img = `<img src="${dataURL}" class="img-thumbnail" width="70%">`;
                        app.render('md_imagen_preview', img);
                        app.render('md_imagen_format', `<b>Formato: </b>${archivo.type}`);
                        app.render('md_imagen_size', `<b>Tamaño: </b>${peso} Kbps`);

                        tmp.img = dataURL;
                        data_img = new FormData();
                        data_img.append('id', event.target.dataset.id);
                        data_img.append('doc', event.target.dataset.doc);
                        data_img.append('imagen',event.target.files[0], 'nuevaimagen.jpg');
                    }else{
                        not.Aviso('Formato invalido, solo se permiten imagenes', '', 2600);
                    }
                }else{
                    not.Error('El peso maximo permitido para archivos es de 5MB', '', 2600);
                    event.target.value = '';
                }
            };
            reader.readAsDataURL(archivo);
            $('#md_imagen').modal('show');
        })
    }
    if (document.getElementById('frm_imagen')) {
        document.getElementById('frm_imagen').addEventListener('submit', async(event) => {
            try {
                event.preventDefault();
                $('#md_imagen').modal('hide');
                if (data_img != null) {
                    app.preload('open', 'guardando..');
                    
                    let cliente = new Cliente();
                    let modifica = await cliente.ActualizarImagen(data_img);
                    app.preload('close');
                    if (modifica.status === 'OK') {
                        not.Ok(modifica.mssg, '', 3000);
                        document.getElementById('img_preview').setAttribute('src', tmp.img);
                    }else{
                        if (modifica.status === 'WARN') 
                            not.Aviso(modifica.mssg, '', 3000);
                        else
                            not.Error(modifica.mssg, '', 3000);
                    }
                }else{
                    not.Error('Imagen: ', 'Datos de actualizacion no definidos', 3000);
                }
            } catch (error) {
                console.log(error);
                app.preload('close');
                not.Error('Imagen: ', error.message, 3000);
            }
        })
    }
    if (thisForm) {
        thisForm.addEventListener('submit', async(event) => {
            event.preventDefault();
            try {
                app.preload('open', 'Guardando');
                let data = new FormData(event.target);
                let cliente = new Cliente();
                let modifica = await cliente.Actualizar(data);

                app.preload('close');
                if (modifica.status === 'OK') {
                    not.Ok(modifica.mssg, '', 3000);
                }else{
                    if (modifica.status === 'WARN') 
                        not.Aviso(modifica.mssg, '', 3000);
                    else
                        not.Error(modifica.mssg, '', 3000);
                }
            } catch (error) {
                not.Error('Error al enviar los datos', '', 3000);
            }
        })
    }

    // Funciones
    const buscarCiudad = async function(tipo = 'muestra', buscar = ''){
        try {
            app.render('tb_md_ciudad', '<tr class="text-center"><td colspan="3">Cargando..</td></tr>');
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
            app.render('tb_md_ciudad', html);
            let items = document.getElementsByName('ciudad_item');
            items.forEach(element => {
                element.addEventListener('click', event => {
                    try {
                        $('#md_ciudad').modal('hide');
                        thisForm.ciudad.value = event.target.dataset.ciudad;
                        thisForm.ubigeo.value = event.target.dataset.codigo;
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
})();