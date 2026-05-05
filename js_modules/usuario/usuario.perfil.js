(function(){
    let app;
    let data_img;
    let upd_state = false; 

    document.addEventListener('DOMContentLoaded', event => {
        app = new App();
    })
    if (document.getElementById('imagen')) {
        document.getElementById('imagen').addEventListener('change', async(event) => {
            try {
                let archivo = event.target.files[0];
                let formatos = ['image/png','image/jpeg','image/jpg'];
                let reader = new FileReader();
                reader.onload = function(){
                    let peso = (parseFloat(archivo.size)/1024).toFixed(2);  
                    let dataURL = reader.result;
                    if (peso < 5120 && formatos.includes(archivo.type)) {
                        let data = new FormData();
                        data.append('id', event.target.dataset.id);
                        data.append('url', event.target.dataset.url);
                        data.append('nombre_imagen', event.target.dataset.nombre);
                        data.append('imagen', archivo, 'nuevaimagen.jpg');
                        data_img = data;
                        upd_state = true;

                        let size = app.fileSize(peso);
                        document.getElementById('imagePreview').setAttribute('src', dataURL);
                        document.getElementById('imagenPeso').innerHTML = size;
                        document.getElementById('imagenFormato').innerHTML = archivo.type;
                        $('#md_imagen').modal('show');
                    }else{
                        if (formatos.includes(archivo.type)) {
                            app.renderAlert('aviso', 'Peso limite superado. Maximo: 5MB');
                            e.target.value = '';
                        }
                        else if(peso > 1120){
                            app.renderAlert('aviso', 'Formato invalido. Formatos permidos: JPG, JPEG, PNG');
                            e.target.value = '';
                        }
                    }
                }
                reader.readAsDataURL(archivo);
            } catch (error) {
                console.log(error);
            }
        })
    }
    if(document.getElementById('btn_imagen')){
        document.getElementById('btn_imagen').addEventListener('click', async(event) => {
            try {
                $('#md_imagen').modal('hide');
                if (upd_state === true) {
                    app.renderAlert('espera', '<b>Actualizando...</b>');
                    let usuario = new Usuario();
                    let modifica = await usuario.ActualizarImagen(data_img);
                    upd_state = false;
                    if (modifica.status === 'OK') {
                        app.renderAlert('ok', modifica.mssg, 3000);
                        setTimeout(() => {
                            window.location.reload();
                        }, 500);
                    }else{
                        let tipo = (modifica.status === 'WARN') ? 'aviso' : 'error';
                        app.renderAlert(tipo, modifica.mssg, 3000);
                    }
                }           
            } catch (error) {
                app.renderAlert('error', 'Error al procesar', 3000);
            }
        }) 
    }
    if (document.getElementById('btn_usuario_editar')) {
        document.getElementById('btn_usuario_editar').addEventListener('click', event => {
            $('#md_editar').modal('show');
        })
    }
    if (document.getElementById('form_editar')) {
        document.getElementById('form_editar').addEventListener('submit', async(event) => {
            event.preventDefault();
            try {
                app.preload('open','Guardando..');
                let data = new FormData(event.target);
                let usuario = new Usuario();
                let modifica = await usuario.Modificar(data);
                app.preload('close');
                if (modifica.status === 'OK') {
                    app.renderAlert('ok', modifica.mssg, 3000);
                }else{
                    let tipo = (modifica.status === 'WARN') ? 'aviso' : 'error';
                    app.renderAlert(tipo, modifica.mssg, 3000);
                }
                $('#md_editar').modal('hide');
                event.target.reset();
                setTimeout(() => {
                    window.location.reload();
                }, 500);
            } catch (error) {
                
            }

        })
    }
})();