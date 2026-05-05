(function(){
    let app;

    document.addEventListener('DOMContentLoaded', () => {
        app = new App();
        CargarTipos();
    });

    if (document.getElementById('btn_tprenda_nuevo')) {
        document.getElementById('btn_tprenda_nuevo').addEventListener('click', event => {
            $('#md_tprenda_registro').modal('show');
        })
    }
    if (document.getElementById('frm_tprenda_registro')) {
        document.getElementById('frm_tprenda_registro').addEventListener('submit', async event => {
            try {
                event.preventDefault();
                app.preload('open', 'Guardando..');
                let data = new FormData(event.target);
                let tipo = new TipoPrenda();
                let guarda = await tipo.Registrar(data);
                
                app.preload('close');
                $('#md_tprenda_registro').modal('hide');
                if (guarda.status === 'OK') {
                    event.target.reset();
                    CargarTipos();
                    not.Ok(guarda.mssg, '', 3000);
                }else{
                    if (guarda.status === 'WARN') 
                        not.Aviso(guarda.mssg, '', 3000);
                    else
                        not.Error(guarda.mssg, '', 3000);
                }
            } catch (error) {
                app.preload('close');
                $('#md_tprenda_registro').modal('hide');
                not.Error('Error al registrar', '', 3000);
            }
        })
    }

    async function CargarTipos(){
        try {
            let tipo = new TipoPrenda();
            let consulta = await tipo.Listar();
            let html = '';
            if (consulta.status === 'OK') {
                if (consulta.result.length > 0) {
                    consulta.result.forEach(element => {
                        html += `
                            <tr>
                                <td>${element.nombre}</td>
                                <td>${element.descripcion}</td>
                                <td>
                                    <a href="${srv.url()}TipoPrenda/editar/${element.id}" class="btn btn-outline-primary">
                                        <i class="fe fe-layers"></i>
                                        Editar
                                    </a>
                                    <button name="tprenda_elimina" class="btn btn-outline-danger" data-id="${element.id}" data-mssg="¿Eliminar ${element.nombre}?">
                                        <i class="fe fe-trash"></i>
                                        Eliminar
                                    </button>
                                </td>
                            </tr>
                        `;
                    })
                }else{
                    html = '<tr class="text-center"><td colspan="3">Datos no encontrados</td></tr>';
                }
            }else{
                html = '<tr class="text-center"><td colspan="3">'+consulta.mssg+'</td></tr>';
            }
            app.render('tb_tprenda', html);

            let items = document.getElementsByName('tprenda_elimina');
            items.forEach(element => {
                element.addEventListener('click', async event => {
                    try {
                        if (event.target.dataset.id && event.target.dataset.mssg) {
                            if (confirm(event.target.dataset.mssg)) {
                                app.preload('open', 'Espere..');
                                let tipo = new TipoPrenda();
                                let elimina = await tipo.Eliminar(event.target.dataset.id);

                                app.preload('close');
                                if (elimina.status === 'OK') {
                                    not.Ok(elimina.mssg, '', 3000);
                                    CargarTipos();
                                }else{
                                    if (elimina.status === 'WARN') 
                                        not.Aviso(elimina.mssg, '', 3000);
                                    else
                                        not.Error(elimina.mssg, '', 3000);
                                }                                
                            }                            
                        }
                    } catch (error) {
                        not.Error('Error al obtener los tipos de prenda');
                        app.preload('close');
                    }
                })
            });
        } catch (error) {
            app.render('tb_tprenda', '<tr class="text-center"><td colspan="3">Error al procesar</td></tr>');
        }
    }
})();