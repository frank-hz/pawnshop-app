(function(){
    let app;
    let _id = 0;
    document.addEventListener('DOMContentLoaded', event =>{
        app = new App();
        _id = document.getElementById('_id').value;
        cargar_formulario();
    })

    async function cargar_formulario(){
        try {
            let tipo = new TipoPrenda();
            let ui_form = await tipo.ListarFormulario(_id);

            let html = '';
            if (ui_form.status === 'OK') {
                if (ui_form.result.length > 0) {
                    ui_form.result.forEach(element => {
                        let chk = (element.estado === 'SI') ? 'checked' : '';
                        html += `
                            <tr>    
                                <td>${element.nombre}</td>
                                <td>${element.descripcion}</td>
                                <td class="text-center">
                                    <input name="form_tipo_check" type="checkbox" data-atributo="${element.id}" data-tipo="${_id}" data-estado="${element.estado}" ${chk}>
                                </td>
                            </tr>
                        `;
                    });
                }else{
                    html = '<tr class="text-center"><td colspan="3">Datos no encontrados</td></tr>';
                }
            }else{
                html = '<tr class="text-center"><td colspan="3">Error al procesar</td></tr>';
            }
            app.render('tb_form', html);

            let items = document.getElementsByName('form_tipo_check');
            items.forEach(element => {
                element.addEventListener('change', async event => {
                    if (event.target.dataset.atributo && event.target.dataset.tipo && event.target.dataset.estado) {
                        try {
                            app.preload('open', 'Procesando..');
                            let tipo = new TipoPrenda();
                            let data = new FormData();
                            data.append('tipo', event.target.dataset.tipo);
                            data.append('atributo', event.target.dataset.atributo);
                            data.append('estado', event.target.dataset.estado);
                            let envio = await tipo.ModificarAtributo(data);
                            app.preload('close');
                            
                            if (envio.status === 'OK') {
                                not.Ok(envio.mssg, '', 2000);
                                cargar_formulario();
                            }else{
                                if (envio.status === 'WARN') 
                                    not.Aviso(envio.mssg, '', 2000);
                                else
                                    not.Error(envio.mssg, '', 2000);
                            }
                        } catch (error) {
                            
                        }
                    }else{
                        not.Error('Error la procesar', '', 2000);
                    }
                });
            });
        } catch (error) {
            app.render('tb_form', '<tr class="text-center"><td colspan="3">Error al procesar</td></tr>');
        }
    }
})();