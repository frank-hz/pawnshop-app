(function(){
    let app;
    let search_config = {limite: 25} 
    document.addEventListener('DOMContentLoaded', ()=>{
        app = new App();

        solicitud_obtener();
    })

    async function solicitud_obtener(){
        try {
            let solicitud = new Solicitud();
            let consulta = await solicitud.Listar(search_config.limite);
            let html = '';
            if (consulta.status === 'OK') {
                if (consulta.result.length > 0) {
                    consulta.result.forEach(element => {
                        html = `
                            <tr>
                                <td>${element.cliente}</td>
                                <td>${element.telefono}</td>
                                <td>${element.tipo}</td>
                                <td>${element.descripcion}</td>
                                <td>${element.monto}</td>
                                <td class="text-center">
                                    <button class="btn btn-primary" name="solicitud_item" data-id="${element.id}">
                                        Ver 
                                        <i class="fe fe-eye"></i>   
                                    </button>
                                </td>
                            </tr>
                        `;
                    });
                }else{
                    html = '<tr><td colspan="6" class="text-center">No se encontraron resultados.</td></tr>';
                }
            }else{
                html = '<tr><td colspan="6" class="text-center">Error al consultar.</td></tr>';
            }
            app.render('tb_solicitud', html);

            let items = document.getElementsByName('solicitud_item');
            items.forEach(e=>{
                // console.log(e.target.dataset.id);
                e.addEventListener('click', event => {
                    if (event.target.dataset.id) {
                        solicitud_detalle(event.target.dataset.id);
                    }
                })
            })
        } catch (error) {
            console.log(error.message);
            app.render('tb_solicitud', '<tr><td colspan="6" class="text-center">Error al consultar.</td></tr>');
        }

        async function solicitud_detalle(id){
            try {
                let solicitud = new Solicitud();
                let info = await solicitud.Detalle(id);
                if (info.status === 'OK' && info.data) {
                    app.render('detalle_cliente', info.data.cliente);
                    app.render('detalle_telefono', info.data.telefono);
                    app.render('detalle_fecha', info.data.fecha);
                    app.render('detalle_sucursal', info.data.sucursal);
                    app.render('detalle_tipo', info.data.tipo);
                    app.render('detalle_prenda', info.data.prenda);
                    $('#md_solicitud_detalle').modal('show');
                }else{
                    app.renderAlert('aviso', 'Datos de la solicitu no encontrados');
                }
            } catch (error) {
                app.renderAlert('aviso', 'Error al procesar');
            }
        }
    }

})();