(function(){
    let app;
    document.addEventListener('DOMContentLoaded', event => {
        app = new App();
        get_tarifas();
    })

    if (document.getElementById('btn_nuevo')) {
        document.getElementById('btn_nuevo').addEventListener('click', event => {
            reset_feedback();
            $('#md_tarifa_nuevo').modal('show');
        })
    }  
    if (document.getElementById('form_tarifa_nueva')) {
        document.getElementById('form_tarifa_nueva').addEventListener('submit', async(event) => {
            event.preventDefault();
            try {
                app.preload('open', 'Registrando..');
                let tarifa = new Tarifa();
                let data = new FormData(event.target);
                let registro = await tarifa.Registrar(data);
                app.preload('close');
                if (registro.status === 'OK') {
                    event.target.reset();
                    $('#md_tarifa_nuevo').modal('hide');
                    not.Ok(registro.mssg, '', 3000);
                    get_tarifas();
                }else{
                    if (registro.error) feedback(registro.error);
                    if (registro.status === 'WARN') 
                        not.Aviso(registro.mssg, '', 3000);
                    else
                        not.Error(registro.mssg, '', 3000);
                }
            } catch (error) {
                event.target.reset();
                $('#md_tarifa_nuevo').modal('hide');
                app.Error('Error al procesar','', 3000);
            }
        })
    }
    if (document.getElementById('form_tarifa_editar')) {
        document.getElementById('form_tarifa_editar').addEventListener('submit', async(event) => {
            event.preventDefault();
            try {
                app.preload('open', 'Guardando..');
                let tarifa = new Tarifa();
                let data = new FormData(event.target);
                let modifica = await tarifa.Actualizar(data);
                app.preload('close');
                if (modifica.status === 'OK') {
                    event.target.reset();
                    $('#md_tarifa_edita').modal('hide');
                    not.Ok(modifica.mssg, '', 3000);
                    get_tarifas();
                }else{
                    if (modifica.error) feedback_2(modifica.error);
                    if (modifica.status === 'WARN') 
                        not.Aviso(modifica.mssg, '', 3000);
                    else
                        not.Error(modifica.mssg, '', 3000);
                }
            } catch (error) {
                event.target.reset();
                $('#md_tarifa_edita').modal('hide');
                not.Error('Error al procesar','', 3000);
            }
        })
    }

    const get_tarifas = async function() {
        try {
            app.render('tdata_tarifa', '<tr class="text-center"><td colspan="6"><div class="loader"></div> Cargando..</td></tr>');
            let tarifa = new Tarifa();
            let consulta = await tarifa.Listar('lista', '');
            
            let html = '';
            if (consulta.status === 'OK') {
                if (consulta.result.length > 0) {
                    consulta.result.forEach((element, index) => {
                        let simb = (element.tipo === 'F') ? 'S/' : '%';
                        let tip  = (element.tipo === 'F') ? 'Fijo' : 'Porcentual';
                        html += `
                            <tr>
                                <td>${tip}</td>
                                <td>S/ ${element.minimo}</td>
                                <td>S/ ${element.maximo}</td>
                                <td>${simb} ${element.interes}</td>
                                <td>${simb} ${element.mora}</td>
                                <td>
                                    <a href="#" data-id="${element.id}" class="btn btn-outline-primary" name="tarifa_edita">
                                        <i class="fe fe-edit-2"></i> Editar
                                    </a>
                                    <a href="#" data-id="${element.id}" class="btn btn-outline-danger" name="tarifa_elimina">
                                        <i class="fe fe-trash-2"></i> Eliminar
                                    </a>
                                </td>
                            </tr>
                        `;
                    });
                } else {
                    html = `
                        <tr class="text-center">
                            <td colspan="6">No se encontraron resultados</td>
                        </tr>
                    `;
                }
            }else{
                html = `
                    <tr class="text-center">
                        <td colspan="6">${consulta.mssg}</td>
                    </tr>
                `;
            }
            app.render('tdata_tarifa', html);
            let elims = document.getElementsByName('tarifa_elimina');
            let edits = document.getElementsByName('tarifa_edita');
            elims.forEach(element => {
                element.addEventListener('click', async(event) => {
                    if (event.target.dataset.id) {
                        if (confirm('¿Eliminar la tarifa?')) {
                            try {
                                let tarifa = new Tarifa();
                                let elimina = await tarifa.Eliminar(event.target.dataset.id);
                                if (elimina.status === 'OK') {
                                    not.Ok(elimina.mssg, '', 3000);
                                    get_tarifas();
                                }else{
                                    if (elimina.status === 'WARN') 
                                        not.Aviso(elimina.mssg, '', 3000);
                                    else
                                        not.Error(elimina.mssg, '', 3000);
                                }
                            } catch (error) {
                                
                            }
                        }
                    }
                })
            });
            edits.forEach(element => {
                element.addEventListener('click', async(event) => {
                    if (event.target.dataset.id) {
                        let tarifa = new Tarifa();
                        let detalle = await tarifa.Detalle(event.target.dataset.id);
                        if (detalle.status === 'OK' && detalle.result != undefined) {
                            let form = document.getElementById('form_tarifa_editar');
                            form.id.value = detalle.result.id;
                            form.minimo.value = detalle.result.minimo;
                            form.maximo.value = detalle.result.maximo;
                            form.interes.value = detalle.result.interes;
                            form.mora.value = detalle.result.mora;
                            form.tipo.value = detalle.result.tipo;
                            reset_feedback();
                            $('#md_tarifa_edita').modal('show');
                        }else{
                            not.Error('aviso', 'No se encontraron datos de la tarifa.', 3000);
                        }
                        
                    }
                })
            });
        } catch (error) {
            // not.Error('Error al procesar', '', 3000);
            app.render('tdata_tarifa', '<tr class="text-center"><td colspan="6">Ocurrio un error al ejecutar.</td></tr>');
        }

    }
    const feedback = function(error){
        if (error.maximo) app.render('maximo_feed', error.maximo);
            else app.render('maximo_feed', '');

        if (error.minimo) app.render('minimo_feed', error.minimo);
            else app.render('minimo_feed', '');

        if (error.tipo) app.render('tipo_feed', error.tipo);
            else app.render('tipo_feed', '');

        if (error.interes) app.render('interes_feed', error.interes);
            else app.render('interes_feed', '');

        if (error.mora) app.render('mora_feed', error.mora);
            else app.render('mora_feed', '');

        if (error.repetido) {
            not.Aviso(error.repetido, '', 3000);
        }
    }
    const feedback_2 = function(error){
        if (error.tarifa) not.Aviso(error.repetido, '', 3000); 

        if (error.maximo) app.render('maximo_feed2', error.maximo);
            else app.render('maximo_feed2', '');

        if (error.minimo) app.render('minimo_feed2', error.minimo);
            else app.render('minimo_feed2', '');

        if (error.tipo) app.render('tipo_feed2', error.tipo);
            else app.render('tipo_feed2', '');

        if (error.interes) app.render('interes_feed2', error.interes);
            else app.render('interes_feed2', '');

        if (error.mora) app.render('mora_feed2', error.mora);
            else app.render('mora_feed2', '');

        if (error.repetido) {
            not.Aviso(error.repetido, '', 3000);
        }
    }
    const reset_feedback = function(){
        app.render('maximo_feed', '');
        app.render('minimo_feed', '');
        app.render('tipo_feed', '');
        app.render('interes_feed', '');
        app.render('mora_feed', '');
        
        app.render('maximo_feed2', '');
        app.render('minimo_feed2', '');
        app.render('tipo_feed2', '');
        app.render('interes_feed2', '');
        app.render('mora_feed2', '');
    }
})();