(function(){
    let id;
    let rol;
    let app;
    
    document.addEventListener('DOMContentLoaded', event => {
        app = new App();
        id = document.getElementById('id').value;
        rol = document.getElementById('rol').value;
        Modulos(id, rol);
        Permisos(id, rol);
    })

    document.addEventListener('click', async(event) => {
        if (event.target.matches('[name=permiso_agregar]')) {
            if (event.target.dataset.usuario && event.target.dataset.permiso) {
                try {
                    let data = new FormData();
                    data.append('tipo', 'agregar');
                    data.append('valorA', event.target.dataset.usuario);
                    data.append('valorB', event.target.dataset.permiso);
                    let usuario = new Usuario();
                    let agregar = await usuario.ActualizarPermiso(data);
                    
                    if (agregar.code === 'Ok') {
                        Permisos(id, rol);
                        not.Ok(agregar.mssg, '', 2000);
                    }else{
                        if(agregar.code === 'Warn') not.Aviso(agregar.mssg, '', 2000);
                        else not.Error(agregar.mssg, '', 2000);
                    }
                } catch (error) {
                    not.Error('Error al agregar permiso', '', 2000);
                }
            }
        }

        if (event.target.matches('[name=permiso_remover]')) {
            if (event.target.dataset.usuario && event.target.dataset.permiso) {
                try {
                    let data = new FormData();
                    data.append('tipo', 'remover');
                    data.append('valorA', event.target.dataset.usuario);
                    data.append('valorB', event.target.dataset.permiso);
                    let usuario = new Usuario();
                    let remover = await usuario.ActualizarPermiso(data);
                    if (remover.code === 'Ok') {
                        Permisos(id, rol);
                        not.Ok(remover.mssg, '', 2000);
                    }else{
                        if(remover.code === 'Warn') not.Aviso(remover.mssg, '', 2000);
                        else not.Error(remover.mssg, '', 2000);
                    }
                } catch (error) {
                    not.Error('Error al remover permiso', '', 2000);
                }
            }
        }

        if (event.target.matches('[name=modulo_agregar]')) {
            if (event.target.dataset.usuario && event.target.dataset.modulo) {
                try {
                    let data = new FormData();
                    data.append('tipo', 'agregar');
                    data.append('valorA', event.target.dataset.usuario);
                    data.append('valorB', event.target.dataset.modulo);
                    let usuario = new Usuario();
                    let agregar = await usuario.ActualizarModulo(data);
                    
                    if (agregar.code === 'Ok') {
                        Modulos(id, rol);
                        not.Ok(agregar.mssg, '', 2000);
                    }else{
                        if(agregar.code === 'Warn') not.Aviso(agregar.mssg, '', 2000);
                        else not.Error(agregar.mssg, '', 2000);
                    }
                } catch (error) {
                    not.Error('Error al agregar modulo', '', 2000);
                }
            }
        }

        if (event.target.matches('[name=modulo_remover]')) {
            if (event.target.dataset.usuario && event.target.dataset.modulo) {
                try {
                    let data = new FormData();
                    data.append('tipo', 'remover');
                    data.append('valorA', event.target.dataset.usuario);
                    data.append('valorB', event.target.dataset.modulo);
                    let usuario = new Usuario();
                    let remover = await usuario.ActualizarModulo(data);
                    
                    if (remover.code === 'Ok') {
                        Modulos(id, rol);
                        not.Ok(remover.mssg, '', 2000);
                    }else{
                        if(remover.code === 'Warn') not.Aviso(remover.mssg, '', 2000);
                        else not.Error(remover.mssg, '', 2000);
                    }
                } catch (error) {
                    not.Error('Error al remover modulo', '', 2000);
                }
            }
        }
    })

    if (document.getElementById('btn_reiniciar')) {
        document.getElementById('btn_reiniciar').addEventListener('click', async(event) => {
            event.preventDefault();
            try {
                if (event.target.dataset.usuario && event.target.dataset.empleado) {
                    const aswer = Swal.fire({
                        type: 'warning',
                        title: '¿Reiniciar las credenciales del usuario?',
                        showCancelButton: true,
                        confirmButtonColor: '#0073D1',
                        confirmButtonText: 'Si, reiniciar',
                        cancelButtonColor: '#283845',
                        cancelButtonText: 'Cancelar'
                    }).then(async(result) => {
                        if (result.value) {
                            app.preload('open', 'Procesando..');
                            let usuario = new Usuario();
                            let reinicio = await usuario.Reiniciar(event.target.dataset.empleado, event.target.dataset.usuario);
                            app.preload('close');
                            if (reinicio.status === 'OK') {
                                not.Ok(reinicio.mssg, '', 2000);
                                setTimeout(() => {
                                    window.location.reload();
                                }, 800);
                            }else{
                                if(reinicio.code === 'Warn') not.Aviso(reinicio.mssg, '', 2000);
                                else not.Error(reinicio.mssg, '', 2000);
                            }
                        }
                    });
                }else{
                    not.Error('Error al reconocer usuario', '', 2000);
                }
            } catch (error) {
                not.Error('Error al reiniciar', '', 2000);
            }
        })
    }

    if (document.getElementById('btn_usuario_estado')) {
        document.getElementById('btn_usuario_estado').addEventListener('click', async(event) => {
            try {
                event.preventDefault();
                if (event.target.dataset.id && event.target.dataset.nestado && event.target.dataset.mssg) {
                    const aswer = Swal.fire({
                        type: 'warning',
                        title: event.target.dataset.mssg,
                        showCancelButton: true,
                        confirmButtonColor: '#0073D1',
                        confirmButtonText: 'Si, adelante',
                        cancelButtonColor: '#283845',
                        cancelButtonText: 'Cancelar'
                    }).then(async(result) => {
                        if (result.value) {
                            app.preload('open', 'Procesando..');
                            let data = new FormData();
                            data.append('id', event.target.dataset.id);
                            data.append('id_empleado', event.target.dataset.idemp);
                            data.append('estado', event.target.dataset.nestado);
                            let usuario = new Usuario();
                            let modifica = await usuario.ModificarEstado(data);

                            app.preload('close');
                            if (modifica.status === 'OK') {
                                not.Ok(modifica.mssg, '', 2000);
                                setTimeout(() => {
                                    window.location.reload();
                                }, 500);
                            }else{
                                if(modifica.code === 'Warn') not.Aviso(modifica.mssg, '', 2000);
                                else not.Error(modifica.mssg, '', 2000);
                            }
                        }
                    });
                }else{
                    not.Error('Error al modificar', '', 2000);
                }
            } catch (error) {
                not.Error('Error al procesar', '', 2000);
            }
        })
    }

    const Modulos = async function(id, rol){
        try {
            app.render('tb_modulo', '<tr class="text-center"><td colspan="4">Cargando...</td></tr>');
            let usuario = new Usuario();
            let consulta = await usuario.Modulos(id);

            let html = '';
            if (consulta.status === 'OK') {
                if (consulta.result.length > 0) {
                    consulta.result.forEach(element => {
                        let opcion = '';
                        if( rol === 'Empleado' ){
                            if (element.estado === 'SI') {
                                opcion = `<button name="modulo_remover" data-usuario="${id}" data-modulo="${element.id}" class="btn btn-outline-danger btn-sm">
                                Remover</button>`; 
                            }else{
                                opcion = `<button name="modulo_agregar" data-usuario="${id}" data-modulo="${element.id}" class="btn btn-outline-success btn-sm">
                                Agregar</button>`;
                            }
                        }
                        html += `
                            <tr>
                                <td>${element.nombre}</td>
                                <td>${element.estado}</td>
                                <td>${opcion}</td>
                            <tr>
                        `;
                    });    
                }else{
                    html = `<tr class="text-center"><td colspan="3">No se encontraron resultados.</td></tr>`;
                }
            } else {
                html = `<tr class="text-center"><td colspan="3">${consulta.mssg}.</td></tr>`;                
            }
            app.render('tb_modulo', html);
        } catch (error) {
            app.render('tb_modulo', '<tr class="text-center"><td colspan="3">Error al consultar</td></tr>');
        }
    }
    const Permisos = async function(id, rol){
        try {
            app.render('tb_permiso', '<tr class="text-center"><td colspan="4">Cargando...</td></tr>');
            let usuario = new Usuario();
            let consulta = await usuario.Permisos(id);

            let html = '';
            if (consulta.status === 'OK') {
                if (consulta.result.length > 0) {
                    consulta.result.forEach(element => {
                        let opcion = '';
                        if(rol === 'Empleado'){
                            if (element.estado === 'SI') {
                                opcion = `<button name="permiso_remover" data-usuario="${id}" data-permiso="${element.nombre}" class="btn btn-outline-danger btn-sm">
                                Remover</button>`; 
                            }else{
                                opcion = `<button name="permiso_agregar" data-usuario="${id}" data-permiso="${element.nombre}" class="btn btn-outline-success btn-sm">
                                Agregar</button>`;
                            }
                        }
                        html += `
                            <tr>
                                <td>${element.titulo}</td>
                                <td>${element.descripcion}</td>
                                <td>${element.estado}</td>
                                <td>${opcion}</td>
                            <tr>
                        `;
                        
                    });    
                }else{
                    html = `<tr class="text-center"><td colspan="4">No se encontraron resultados.</td></tr>`;
                }
            } else {
                html = `<tr class="text-center"><td colspan="4">${consulta.mssg}.</td></tr>`;                
            }
            app.render('tb_permiso', html);
        } catch (error) {
            app.render('tb_permiso', '<tr class="text-center"><td colspan="4">Error al consultar</td></tr>');
        }
    }

})();