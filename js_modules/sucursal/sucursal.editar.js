(function(){
    let thisForm = document.getElementById('frm_editar');
    let app;

    document.addEventListener('DOMContentLoaded', (e)=>{
        app = new App(); 
    });
    document.addEventListener('click', (e)=>{
        if (e.target.matches('[name=ciudad_item]')) {
            if (e.target.dataset.id && e.target.dataset.ciudad) {
                try {
                    thisForm.ciudad.value = e.target.dataset.ciudad;
                    thisForm.ubigeo.value = e.target.dataset.id;
                    $("#md_ciudad").modal('hide');
                } catch (error) {
                    app.renderAlert('aviso', 'error al selecionar ciudad', 2500);
                    console.log(error);
                }
            }
        }
    })
    if (document.getElementById('input_ciudad_buscar')) {
        document.getElementById('input_ciudad_buscar').addEventListener('keyup', (e)=>{
            buscar_ciudad('buscar', e.target.value);
        })
    } 
    if (document.getElementById('btn_ciudad_modal')) {
        document.getElementById('btn_ciudad_modal').addEventListener('click', (e)=>{
            e.preventDefault();
            buscar_ciudad();
            $("#md_ciudad").modal('show');
        })
    }
    if (thisForm) {
        thisForm.addEventListener('submit', async(event) => {
            event.preventDefault();
            try {
                if (event.target.dataset.id) {
                    app.renderAlert('espera', 'Guardando..');
                    let data = new FormData(event.target);
                    data.append('id', event.target.dataset.id);
                    let sucursal = new Sucursal();
                    let modifica = await sucursal.Modificar(data);
                    if (modifica.status === 'OK') {
                        app.renderAlert('ok', modifica.mssg, 3000);
                    }else{
                        let tipo = (modifica.status === 'WARN') ? 'aviso' : 'error';
                        app.renderAlert(tipo, modifica.mssg, 3000);
                        if (modifica.error) feedback(modifica.error);
                    }
                }else{
                    app.renderAlert('aviso', 'Error al procesar', 3000);
                }
            } catch (error) {
                app.preload('close');
                app.renderAlert('error', 'Ocurrio un error al procesar');
            }
        })
    }

    const buscar_ciudad = async function(tipo = 'muestra', buscar = ''){
        try {
            app.render('tb_ciudad', '<tr class="text-center"><td colspan="">Cargando..</td></tr>');
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
            app.render('tb_ciudad', html);
        } catch (error) {
            app.render('tb_ciudad', '<tr class="text-center"><td colspan="">Error al consultar</td></tr>');
        }
    }

    const feedback = function(error){
        if (error.descripcion) {
            app.render('descripcion_feed', error.descripcion);
        }else{
            app.render('descripcion_feed', '');
        }
        if (error.ubigeo) {
            app.render('ubigeo_feed', error.ubigeo);
        }else{
            app.render('ubigeo_feed', '');
        }
        if (error.direccion) {
            app.render('direccion_feed', error.direccion);
        }else{
            app.render('direccion_feed', '');
        }
        if (error.banco) {
            app.render('banco_feed', error.banco);
        }else{
            app.render('banco_feed', '');
        }
        if (error.cuenta_numero) {
            app.render('cuenta_numero_feed', error.cuenta_numero);
        }else{
            app.render('cuenta_numero_feed', '');
        }
    }
})();