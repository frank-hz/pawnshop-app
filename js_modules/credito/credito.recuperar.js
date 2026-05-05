(function(){
    let app;
    let pago_json = {
        total : 0,
        subtotal : 0,
        pendiente : 0,
        total_cuota : 0,
        total_mora : 0,
        total_descuento : 0,
        total_atraso: 0,
        total_capital: 0,
        recibido : 0,
        devuelto : 0,
        monto_cuota:0,
        monto_prorrateo: 0,
        cant_cuota : 0,
        cant_prorrateo: 0,
        concepto : 'REC'
    }
    let capital_array = [];
    let cuota_array = [];
    let mora_array = [];

    document.addEventListener('DOMContentLoaded', event => {
        try {
            app = new App();
            let pendiente_items = document.getElementsByName('pendiente_item');
            if (pendiente_items.length > 0) {
                pendiente_items.forEach(element => {
                    if (element.dataset.tipo === 'CU') {
                        cuota_array.push({
                            id: element.dataset.id,
                            tipo: element.dataset.tipo,
                            referencia: 'Cuota '+element.dataset.orden,
                            monto: parseFloat(element.value),
                            cobro: element.dataset.cobro
                        });
                        if (element.dataset.cobro === 'NORM') pago_json.cant_cuota++;
                        if (element.dataset.cobro === 'PROR') pago_json.cant_prorrateo++;
                    }else if(element.dataset.tipo === 'CAP'){
                        capital_array.push({
                            id: element.dataset.id,
                            tipo: element.dataset.tipo,
                            referencia: 'Capital',
                            monto: parseFloat(element.value)
                        });
                    }else if(element.dataset.tipo === 'MO'){
                        mora_array.push({
                            id: element.dataset.id,
                            tipo: element.dataset.tipo,
                            referencia: 'Mora',
                            atraso: element.dataset.atraso,
                            monto: parseFloat(element.value),
                            accion: 'C'
                        });
                    }
                });
            }
            Procesar_pago();
        } catch (error) {
            console.log(error);
        }
    })
    if (document.getElementById('check_darkmode')) {
        document.getElementById('check_darkmode').addEventListener('change', event => {
            try {
                let tb = document.getElementById('tabla_pendiente');
                if (event.target.checked) tb.classList.add('table-dark');
                else tb.classList.remove('table-dark');
            } catch (error) {
                console.log('darkmode::error', error.message);
            }
        })
    }
    if (document.getElementById('check_descuento')) {
        document.getElementById('check_descuento').addEventListener('change', event => {
            document.getElementById('input_descuento').value = '';
            pago_json.total_descuento = 0;
            if (event.target.checked) {
                $('#section_descuento').slideDown('fast');
            }else{
                $('#section_descuento').slideUp('fast');
            }
            Procesar_pago();
        })
    }
    document.addEventListener('change', event => {
        if (event.target.matches('[name=forma_pago]')) {
            if (event.target.value === 'DEP' || event.target.value === 'TRANF') {
                $("#forma_operacion").slideDown("fast");
            }else{
                $("#forma_operacion").slideUp("fast");
            }
        }
    })
    if (document.getElementById('input_descuento')) {
        document.getElementById('input_descuento').addEventListener('keyup', event => {
            try {
                // if (!isNaN(event.target.value) && event.target.value.length > 0) {
                    // let desc = (event.target.value >= 0) ? event.target.value : 0;
                    // pago_json.total_descuento = event.target.value;
                    
                    // if (desc <= parseFloat(pago_json.subtotal)) {
                    //     pago_json.total_descuento = parseFloat(desc);
                    // }else{
                    //     pago_json.total_descuento = 0;
                    //     app.render('descuento_feed', 'Limite de descuento sobrepasado.');
                    // }
                // }else{
                //     pago_json.total_descuento = 0;
                //     if (isNaN(event.target.value)) {
                //         app.render('descuento_feed', 'Monto invalido.');
                //     }
                // }
                pago_json.total_descuento = event.target.value;
                Procesar_pago();
            } catch (error) {
                app.render('descuento_feed', 'Error al procesar.');
            }
        })
    }
    if (document.getElementById('input_recibido')) {
        document.getElementById('input_recibido').addEventListener('keyup', event => {
            try {
                pago_json.recibido = event.target.value;
                Procesar_pago();
                // if (!isNaN(event.target.value) && event.target.value.length > 0) {
                //     let recibido = parseFloat(event.target.value);

                //     pago_json.recibido = recibido;
                    
                //     if (recibido < parseFloat(pago_json.total)) {
                //         app.render('monto_feed', 'Monto insuficiente.');
                //     }
                // }else{
                //     pago_json.recibido = 0;
                //     if (isNaN(event.target.value)) {
                //         app.render('monto_feed', 'Monto invalido.');
                //     }
                // }
            } catch (error) {
                console.log(error);
                app.render('monto_feed', 'Error al procesar.'+error.message);
            }
        })
    }
    if (document.getElementById('form_pago_registro')) {
        document.getElementById('form_pago_registro').addEventListener('submit', async(event) => {
            event.preventDefault();
            try {                
                app.render('_noperacion_feed', '');
                app.render('_banco_feed', '');
                let forma = event.target.forma_pago.value;
                if ((forma === 'DEP' || forma === 'TRANF') 
                    && (event.target.comprobante.value.length === 0 ||
                        event.target.banco.value.length === 0)) {
                    if(event.target.comprobante.value.length === 0){
                        app.render('_noperacion_feed', 'Campo requerido');
                    }
                    if (event.target.banco.value.length === 0 ) {
                        app.render('_banco_feed', 'Campo requerido');
                    }
                }else{
                    app.preload('open', 'Procesando..');
                    event.target.btn_envio.disabled = true;
                    let comprobante = (forma === 'DEP' || forma === 'TRANF') 
                        ? event.target.comprobante.value 
                        : '';
                    let banco = (forma === 'DEP' || forma === 'TRANF') 
                        ? event.target.banco.value 
                        : 0;
                    let data = new FormData();
                    data.append('credito', event.target.credito.value);
                    data.append('prenda', event.target.prenda.value);
                    data.append('forma', forma);
                    data.append('comprobante', comprobante);
                    data.append('banco', banco);
                    data.append('subtotal', pago_json.subtotal);
                    data.append('descuento', pago_json.total_descuento);
                    data.append('total', pago_json.total);
                    data.append('recibido', pago_json.recibido);
                    data.append('devuelto', pago_json.devuelto);
                    data.append('concepto', pago_json.concepto);
                    data.append('detalle_cuota', JSON.stringify(cuota_array));
                    data.append('detalle_mora', JSON.stringify(mora_array));
                    data.append('detalle_capital', JSON.stringify(capital_array));
                    let pago = new Pago();
                    let envio = await pago.Guardar(data);
    
                    app.preload('close');
                    event.target.btn_envio.disabled = false;
                    if (envio.status === 'OK') {
                        not.Ok(envio.mssg, '', 3000);
                        setTimeout(() => {
                            window.location.href = srv.url()+'Pago/detalle/'+envio.id;
                        }, 750);
                    }else{
                        if(envio.status === 'WARN') not.Aviso(envio.mssg,'', 3000);
                        else not.Error(envio.mssg,'', 3000);
                    }

                }
            } catch (error) {
                console.log(error);
                event.target.btn_envio.disabled = false;
                app.preload('close');
                not.Error('Error al procesar recuperacion','', 3000);
            }

        })
    }

    const Procesar_pago = function(){
        try {
            app.render('descuento_feed', '');
            app.render('monto_feed', '');
            // Descuento
            if (!isNaN(pago_json.total_descuento) && pago_json.total_descuento != "") {
                if (pago_json.total_descuento > pago_json.subtotal) {
                    pago_json.total_descuento = 0;
                    app.render('descuento_feed', 'Limite de descuento sobrepasado');    
                }
            }else{
                if (isNaN(pago_json.total_descuento)) {
                    app.render('descuento_feed', 'Monto invalido.');
                }
                pago_json.total_descuento = 0;
            }
            // Recibido
            if (!isNaN(pago_json.recibido) && pago_json.recibido != "") {
                pago_json.recibido = parseFloat(pago_json.recibido);
                if (pago_json.recibido < parseFloat(pago_json.total)) {
                    app.render('monto_feed', 'Monto insuficiente.');    
                }
            }else{
                if (isNaN(pago_json.recibido)) {
                    app.render('monto_feed', 'Monto invalido.');
                }
                pago_json.recibido = 0;
            }

            pago_json.total_cuota = cuota_array.reduce((acum, current) => {
                return parseFloat(acum + current.monto);
            }, 0);

            pago_json.monto_cuota = cuota_array.reduce((acum, current) => {
                if (current.cobro === 'NORM') {
                    return parseFloat(acum + current.monto);
                }else{
                    return acum + 0;
                }
            }, 0);
            pago_json.monto_prorrateo = cuota_array.reduce((acum, current) => {
                if (current.cobro === 'PROR') {
                    return parseFloat(acum + current.monto);
                }else{
                    return acum + 0;
                }
            }, 0);

            pago_json.total_mora = mora_array.reduce((acum, current) => {
                return parseFloat(acum + current.monto); 
            }, 0);  
            pago_json.total_capital = capital_array.reduce((acum, current) => {
                return parseFloat(acum + current.monto);
            }, 0);   
            pago_json.total_atraso = mora_array.reduce((acum, current) => {
                return parseFloat(acum + parseInt(current.atraso));
            }, 0);
            pago_json.subtotal = pago_json.total_cuota + pago_json.total_mora + pago_json.total_capital;   

            pago_json.total = pago_json.total_cuota + pago_json.total_mora + pago_json.total_capital - pago_json.total_descuento;
            pago_json.total = parseFloat(pago_json.total);

            pago_json.devuelto = (pago_json.recibido >= pago_json.total) ? parseFloat(pago_json.recibido) - pago_json.total : 0;
            pago_json.devuelto = app.Rx_round(pago_json.devuelto);
            pago_json.devuelto = parseFloat(pago_json.devuelto);

            app.render('recibo_cuota', '+ S/ '+app.Rx_round(pago_json.monto_cuota));
            app.render('recibo_cuota_sub', (pago_json.cant_cuota) ? ''+pago_json.cant_cuota+' cuotas' : '0'+pago_json.cant_cuota+' cuotas');
            app.render('recibo_prorrateo', '+ S/ '+app.Rx_round(pago_json.monto_prorrateo));
            app.render('recibo_prorrateo_sub', '');
            app.render('recibo_mora', '+ S/ '+app.Rx_round(pago_json.total_mora));
            app.render('recibo_mora_sub', (pago_json.total_atraso > 9) ? pago_json.total_atraso+' dias' : '0'+pago_json.total_atraso+' dias');
            app.render('recibo_capital', '+ S/ '+app.Rx_round(pago_json.total_capital));
            app.render('recibo_descuento', '- S/ '+app.Rx_round(pago_json.total_descuento));
            app.render('recibo_total', 'S/ '+app.Rx_round(pago_json.total));
            document.getElementById('input_devuelto').value = app.Rx_round(pago_json.devuelto);
            console.table(cuota_array);
        } catch (error) {
            console.log('Procesamiento:error', error); 
        }
    }

})();