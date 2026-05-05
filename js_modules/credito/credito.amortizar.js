(function(){
    let app;
    let form_pago = document.getElementById('form_pago');
    let pago_json = {
        restante: 0,
        total : 0,
        subtotal : 0,
        total_cuota : 0,
        total_mora : 0,
        total_descuento : 0,
        recibido : 0,
        devuelto : 0,
        concepto : 'AMOR'
    }
    let capital_array = [];
    const FORMAS_PAGO = ['DEP','TRANF'];

    document.addEventListener('DOMContentLoaded', () => {
        app = new App();
        try {
            if (document.getElementById('credito')) {
                let credito = document.getElementById('credito');
                let id = credito.value;
                let monto = parseFloat(credito.dataset.monto);
                let amortiza = parseFloat(credito.dataset.amortizacion);
                let restante = parseFloat(credito.dataset.restante);
                
                if (restante > 0) {
                    capital_array.push({
                        id: id,
                        tipo: 'CAP',
                        referencia: 'Amortizacion de capital',
                        monto: restante
                    });
                }
                pago_json.total = restante;
                pago_json.subtotal = restante;
                pago_json.restante = restante;
                
            }
        } catch (error) {
            console.log(error);
        }

        try {
            let fitems = document.getElementsByName('forma_pago');
            if (fitems.length > 0) {
                fitems.forEach(element => {
                    element.addEventListener('change', event => {
                        if (event.target.value === 'DEP' || event.target.value === 'TRANF') {
                            $("#sect_deposito").slideDown("fast");
                            form_pago.banco.value = '';
                        }else{
                            $("#sect_deposito").slideUp("fast");
                        }
                    })
                });
            }
        } catch (error) {
            console.log(error);
        }
    });
    if (document.getElementById('check_descuento')) {
        document.getElementById('check_descuento').addEventListener('change', event => {
            if (event.target.checked) $("#sect_descuento").slideDown("fast");
            else $("#sect_descuento").slideUp("fast");
        })
    }
    if (document.getElementById('input_recibido')) {
        document.getElementById('input_recibido').addEventListener('keyup', event => {
            try {
                app.render('monto_feed', '');
                if (!isNaN(event.target.value) && event.target.value.length > 0) {
                    let recibido = parseFloat(event.target.value);
                    pago_json.devuelto = (recibido >= pago_json.restante) ? parseFloat(recibido) - pago_json.restante : 0;
                    pago_json.recibido = app.Rx_round(recibido);
                    pago_json.devuelto = app.Rx_round(pago_json.devuelto);
                    capital_array[0].monto = recibido;

                    if (recibido < pago_json.restante) {
                        pago_json.total = recibido;
                        pago_json.subtotal = recibido;
                    }else{
                        pago_json.total = pago_json.restante;
                        pago_json.subtotal = pago_json.restante;
                    }
                }else{
                    pago_json.recibido = 0;
                    if (isNaN(event.target.value)) {
                        app.render('monto_feed', 'Monto invalido.');
                    }
                }
                pago_procesar();
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
                if (pago_json.total > 0) {
                    app.preload('open','Procesando..');
                    event.target.btn_envio.disabled = true;

                    
                    let forma = event.target.forma_pago.value;

                    let comprobante = (FORMAS_PAGO.includes(forma) && event.target.comprobante) 
                        ? event.target.comprobante.value 
                        : '';

                    let banco = (FORMAS_PAGO.includes(forma) && event.target.banco)
                        ? event.target.banco.value 
                        : 0;

                    let data = new FormData();
                    data.append('credito', event.target.credito.value);
                    data.append('forma', forma);
                    data.append('comprobante', comprobante);
                    data.append('banco', banco);
                    data.append('subtotal', pago_json.subtotal);
                    data.append('descuento', pago_json.total_descuento);
                    data.append('total', pago_json.total);
                    data.append('recibido', pago_json.recibido);
                    data.append('devuelto', pago_json.devuelto);
                    data.append('concepto', pago_json.concepto);
                    data.append('detalle_capital', JSON.stringify(capital_array));

                    let pago = new Pago();
                    let guardar = await pago.Guardar(data);
                    
                    app.preload('close');
                    event.target.btn_envio.disabled = false;
                    if (guardar.status === 'OK') {
                        not.Ok(guardar.mssg, '', 3000);
                        setTimeout(() => {
                            window.location.href = srv.url()+'Pago/detalle/'+guardar.id;
                        }, 700);
                    }else{
                        if(guardar.status === 'WARN') not.Aviso(guardar.mssg, '', 3000);
                        else not.Error(guardar.mssg, '', 3000);
                    }
                }else{
                    not.Error('Monto a pagar incorrecto', '', 3000);
                }
            } catch (error) {
                app.preload('close');
                not.Error('Error al procesar', '', 3000);
                event.target.btn_envio.disabled = false;
            }

        })
    }

    const pago_procesar = function(){
        try { 
            // app.render('recibo_recibido', `S/ ${pago_json.recibido}`);
            // app.render('recibo_devuelto', `S/ ${pago_json.devuelto}`);
            // document.getElementById('input_vuelto').value = pago_json.devuelto;
        } catch (error) {
            console.log('pago_procesar:error', error); 
        }
    }

})();