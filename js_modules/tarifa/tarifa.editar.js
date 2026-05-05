(function(){
    let app;

    document.addEventListener('DOMContentLoaded', event => {
        app = new App();

    });
    
    if (document.getElementById('form_tarifa_editar')) {
        document.getElementById('form_tarifa_editar').addEventListener('submit', async event => {
            event.preventDefault();
            try {
                app.renderAlert('espera', `<h4>Aplicando...</h4>`);
                let data = new FormData(event.target);
                let tarifa = new Tarifa();
                let actualizacion = await tarifa.Actualizar(data);

                switch (actualizacion.code) {
                    case 'Ok':
                        app.renderAlert('ok', `<h4>${actualizacion.mssg}</h4><p>Aplicando revision...</p>`, 3000);
                        // (async function(){
                        //     let revision = new Revision();
                        //     let calculo = await revision.Calculo();
                        //     app.renderAlert(calculo.code.toLowerCase(), `<b>${calculo.mssg}</b>`, 3000);
                        // })
                        break;

                    case 'Error':
                            app.renderAlert('error', `<h4>${actualizacion.mssg}</h4>`, 3000);
                        break;

                    case 'Warn':
                            app.renderAlert('aviso', `<h4>${actualizacion.mssg}</h4>`, 3000);
                        break;

                    default:
                        not.Blank('Editar', 'Sin respuesta');
                        break;
                }
            } catch (error) {
                
            }

        });
    }

})();