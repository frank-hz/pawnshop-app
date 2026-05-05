(function(){
    let app; 
    let debito_json = {credito: 0, monto: 0, amortizacion: 0, restante: 0}
    document.addEventListener('DOMContentLoaded', event => {
        app = new App();
        try {
            let credito_inp = document.getElementById('credito');
            debito_json.amortizacion = parseFloat(credito_inp.dataset.amortizacion);
            debito_json.credito = credito_inp.value;
        } catch (error) {
            // app.renderAlert('aviso', 'error al inicializar');
            app.disabled('btn_envio', true);
        }
    })

    if (document.getElementById('input_recibido')) {
        document.getElementById('input_recibido').addEventListener('keyup', event => {
            try {
                app.render('monto_feed', '');
                console.log(event.target.value);
                if (!isNaN(event.target.value) && event.target.value > 0) {
                    debito_json.monto = parseFloat(event.target.value);  
                    if (debito_json.monto <= debito_json.amortizacion) {
                        debito_json.restante = debito_json.amortizacion - debito_json.monto;
                        app.disabled('btn_envio', false);
                        
                    }else{
                        debito_json.monto = 0;
                        debito_json.restante = debito_json.amortizacion;
                        app.render('monto_feed', 'Limite de amortizacion sobrepasado');
                        app.disabled('btn_envio', true);
                    }                  
                }else{
                    debito_json.monto = 0;
                    app.disabled('btn_envio', true);
                    if (isNaN(event.target.value)) {
                        app.render('monto_feed', 'Monto invalido.');
                    }
                }

                
                app.set_value('input_restante', (debito_json.amortizacion - debito_json.monto));
            } catch (error) {
                console.log(error);
                app.render('monto_feed', 'Error al procesar.');
            }
        })
    }
    if (document.getElementById('form_pago')) {
        document.getElementById('form_pago').addEventListener('submit', async(event) => {
            event.preventDefault();
            try {
                if (debito_json.monto > 0 && debito_json.restante >= 0) {
                    app.preload('open','Aplicando..');
                    event.target.btn_envio.disabled = true;
                    let data = new FormData();
                    data.append('credito', debito_json.credito);
                    data.append('monto', debito_json.restante);
                    data.append('descripcion', event.target.descripcion.value);
                    let debita = new Debitacion();
                    let aplica = await debita.Aplicar(data);
                    
                    app.preload('close');
                    event.target.btn_envio.disabled = false;
                    if (aplica.status === 'OK') {
                        app.renderAlert('ok', aplica.mssg, 1200);
                        setTimeout(() => {
                            window.location.href = srv.url()+'Credito/detalle/'+debito_json.credito;
                        }, 700);
                    }else{
                        let tipo_mesg = (aplica.status === 'WARN') ? 'aviso' : 'error';
                        app.renderAlert(tipo_mesg, aplica.mssg, 3000);
                    }
                }else{
                    app.renderAlert('aviso', 'Monto a debitar incorrecto');
                }
            } catch (error) {
                app.preload('close');
                app.renderAlert('error', 'Ocurrio un problema al procesar', 3000);
                event.target.btn_envio.disabled = false;
                console.log(error);
            }

        })
    }
    

})();