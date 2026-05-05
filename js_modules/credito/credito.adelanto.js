(function(){
    let app;
    let adelantoJson = {
        recibido: 0,
        vuelto: 0,
        subtotal: 0,
        monto : 0
    };
    let concepto = 'ADEL';
    let form = document.getElementById('form_adelanto');
    document.addEventListener('DOMContentLoaded', event => {
        app = new App();
        let t_adel = document.getElementsByName('tipo_adelanto');
        t_adel.forEach(element => {
            element.addEventListener('change', event=>{
                if (event.target.value === 'cuota') {
                    $('#adelanto_section_cuota').slideDown('fast');
                }else{
                    form.monto.value = '';
                    $('#adelanto_section_cuota').slideUp('fast');
                }
            })
        });

        let f_pag = document.getElementsByName('forma');
        f_pag.forEach(element => {
            element.addEventListener('change', event => {
                if (event.target.value === 'DEP' || event.target.value === 'TRANF') {
                    $('#adelanto_banco').slideDown('fast');
                    $('#adelanto_nro_operacion').slideDown('fast');
                }else{
                    $('#adelanto_banco').slideUp('fast');
                    $('#adelanto_nro_operacion').slideUp('fast');
                }
            })
        });
    })
    if (document.getElementById('cuota_cantidad')) {
        document.getElementById('cuota_cantidad').addEventListener('keyup', event => {
            if (form.pago && parseFloat(form.pago.value) > 0) {
                let pago  = parseFloat(form.pago.value);
                let monto = pago * parseInt(event.target.value);
                form.monto.value = app.Rx_round(monto); 
                adelantoJson.monto = app.Rx_round(monto);
                // form.subtotal.value = app.Rx_round(monto); 
                procesado();
            } else {
                not.Aviso('Error al calcular el monto', '', 3000);
            }
        })
        document.getElementById('cuota_cantidad').addEventListener('change', event => {
            if (form.pago && parseFloat(form.pago.value) > 0) {
                let pago  = parseFloat(form.pago.value);
                let monto = pago * parseInt(event.target.value);
                form.monto.value = app.Rx_round(monto); 
                adelantoJson.monto = app.Rx_round(monto);
                // form.subtotal.value = app.Rx_round(monto); 
                procesado();
            } else {
                not.Aviso('Error al calcular el monto', '', 3000);
            }
        })
    }
    if (form.recibido) {
        form.recibido.addEventListener('keyup', event => {
            if (!isNaN(event.target.value) && parseFloat(event.target.value) >= 0) {
                // if (parseFloat(form.monto.value) > parseFloat(event.target.value)) {
                //     app.render('recibido_feed', 'monto insuficiente');
                //     console.log('no error');
                // }else{
                //     console.log('error');
                // }
            } else {
                event.target.value = '';
                app.render('recibido_feed', 'monto invalido');
            }
            procesado();
        })
    }
    if (form) {
        form.addEventListener('submit', async event => {
            try {
                event.preventDefault();
                if (!isNaN(form.monto.value) && form.monto.value > 0) {
                    app.preload('open', 'procesando');
                    let data = new FormData(event.target);
                    data.append('concepto', concepto);
                    data.append('descuento', 0);
                    data.append('total', event.target.monto.value);
                    data.append('subtotal', event.target.monto.value);
                    data.append('recibido', event.target.recibido.value);
                    data.append('devuelto', event.target.vuelto.value);

                    let pago = new Pago();
                    let guardar = await pago.Guardar(data);
                    app.preload('close');
                    if (guardar.status === 'OK') {
                        not.Ok(guardar.mssg, '', 3000);
                        setTimeout(() => {
                            window.location.href = srv.url()+'Pago/detalle/'+guardar.id;
                        }, 700);
                    }else{
                        if(guardat.status === 'WARN') not.Aviso(guardar.mssg, '', 3000);
                        else not.Error(guardar.mssg, '', 3000);
                    }
                }else{
                    not.Error('Errores encontrados', '', 3000);
                    app.render('monto_feed', 'monto invalido');
                }
            } catch (error) {
                app.preload('close');
                not.Error('Errores al procesar', '', 3000);
            }
        })
    }

    const procesado = function(){
        try {
            app.render('monto_feed', '');
            app.render('recibido_feed', '');
            if (parseFloat(form.monto.value) > 0) {
                let vuelto = 0;
                if (parseFloat(form.recibido.value) >= parseFloat(form.monto.value)) {
                    vuelto = form.recibido.value - form.monto.value;
                }else{
                    vuelto = 0;
                    app.render('recibido_feed', 'monto insuficiente');
                }
                vuelto = app.Rx_round(vuelto);
                form.vuelto.value = vuelto;
            }else{
                form.recibido.value = '';
                form.vuelto.value = 0;
                app.render('monto_feed', 'monto a adelantar no definido');
            }
        } catch (error) {
            app.render('monto_feed', '');
            app.render('recibido_feed', '');
            not.Error('Errores al procesar', '', 3000);
        }
    }

})();