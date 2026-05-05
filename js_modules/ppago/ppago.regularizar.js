(function(){
    let app;
    let form_registro = document.getElementById('form_pago_registro');
    let pago_json = {
        total : 0,
        subtotal : 0,
        total_cuota : 0,
        total_mora : 0,
        total_descuento : 0,
        recibido : 0,
        devuelto : 0,
        concepto : 'REG',
		adicional: 0
    }
    let cuota_array = [];
    let mora_array = [];
    let table_sort = 'desc';
    
    document.addEventListener('DOMContentLoaded', function(){
        app = new App();
        cuotas_recuento_base();

        
    });
    document.addEventListener('change', event => {
        if (event.target.matches('[name=forma_pago]')) {
            if (event.target.value === 'DEP' || event.target.value === 'TRANF') {
                $("#forma_operacion").slideDown("fast");
            }else{
                $("#forma_operacion").slideUp("fast");
            }
        }
        if (event.target.matches('[name=pospuesto_item]')) {
            try {
                if (event.target.checked) {
                    mora_array.push({
                        id: event.target.dataset.id,
                        tipo: 'MO',
                        referencia: 'Mora',
                        monto: parseFloat(event.target.dataset.monto),
                        accion: 'C'
                    });
                }else{
                    for (let i = 0; i < mora_array.length; i++) {
                        if (mora_array[i].id === event.target.dataset.id) {
                            mora_array.splice(i, 1);
                        }
                    }
                }
                procesar_pago();
            } catch (error) {
                console.log(error);
                not.Error('Error al seleccionar', '', 3000);
            }
        }
        if (event.target.matches('[name=mora_accion]')) {
            try {
                if (event.target.dataset.id) {
                    for(let i=0; i < mora_array.length; i++){
                        if(mora_array[i].id === event.target.dataset.id){
                            mora_array[i].accion = event.target.value;
                        }
                    }
                }
                procesar_pago();
            } catch (error) {
                console.log(error);
                not.Error('Error al seleccionar', '', 3000);
            }
        }
        if (event.target.matches('[name=cuota_item]')) {
            // let orden = event.target.dataset.orden;
            // if (!orden || orden == undefined) {
            //     console.log('error al actualizar')
            //     return;
            // }
            // cuotas_update_stash(action, event.target);
            
            let action = (event.target.checked) ? 'ADD' : 'REMOVE';
            let orden = event.target.dataset.orden;
            cuotas_stash_delete(action, orden);
            cuotas_recuento_base();
        }
    });
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
    if (document.getElementById('check_custom')) {
        document.getElementById('check_custom').addEventListener('change', event => {
            document.getElementById('input_descuento').value = '';
            if (event.target.checked){
				$('#section_descuento').slideDown('fast');
				$('#section_adicional').slideDown('fast');
				// $('#section_comentario').slideDown('fast');				
			} else {
				$('#section_descuento').slideUp('fast');
				$('#section_adicional').slideUp('fast');
				// $('#section_comentario').slideUp('fast');
			} 
        })
    }
    if (document.getElementById('check_seleccion_total')) {
        document.getElementById('check_seleccion_total').addEventListener('change', event => {
            if (event.target.checked) cuota_seleccion_total(true);
            else cuota_seleccion_total(false);
            
        })
    }
    if (document.getElementById('input_descuento')) {
        document.getElementById('input_descuento').addEventListener('keyup', event => {
            try {
                app.render('descuento_feed', '');
                if (!isNaN(event.target.value) && event.target.value.length > 0) {
                    let desc = (event.target.value >= 0) ? event.target.value : 0;
                    if (desc <= parseFloat(pago_json.total_cuota + pago_json.total_mora)) {
                        pago_json.total_descuento = parseFloat(desc);
                    }else{
                        pago_json.total_descuento = 0;
                        app.render('descuento_feed', 'Limite de descuento sobrepasado.');
                    }
                }else{
                    pago_json.total_descuento = 0;
                    if (isNaN(event.target.value)) {
                        app.render('descuento_feed', 'Monto invalido.');
                    }
                }
                procesar_pago();
            } catch (error) {
                app.render('descuento_feed', 'Error al procesar.');
            }
        })
    }
	if (document.getElementById('input_adicional')) {
        document.getElementById('input_adicional').addEventListener('keyup', event => {
            try {
                app.render('adicional_feed', '');
                if (!isNaN(event.target.value) && event.target.value.length > 0) {
                    let adic = (event.target.value >= 0) ? event.target.value : 0;
                    pago_json.adicional = parseFloat(adic);
                }else{
                    pago_json.adicional = 0;
                    if (isNaN(event.target.value)) {
                        app.render('adicional_feed', 'Monto invalido.');
                    }
                }
                procesar_pago();
            } catch (error) {
                app.render('adicional_feed', 'Error al procesar.');
            }
        })
    }
    if (document.getElementById('input_recibido')) {
        document.getElementById('input_recibido').addEventListener('keyup', event => {
            try {
                app.render('monto_feed', '');
                if (!isNaN(event.target.value) && event.target.value.length > 0) {
                    let recibido = parseFloat(event.target.value);

                    pago_json.recibido = recibido;
                    
                    if (recibido < parseFloat(pago_json.total)) {
                        app.render('monto_feed', 'Monto insuficiente.');
                    }
                }else{
                    pago_json.recibido = 0;
                    if (isNaN(event.target.value)) {
                        app.render('monto_feed', 'Monto invalido.');
                    }
                }
                procesar_pago();
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
					data.append('adicional', pago_json.adicional);
					data.append('comentario', document.getElementById('pago_comentario').value);
                    let pago = new Pago();
                    let envio = await pago.Guardar(data);
    
                    app.preload('close');
                    event.target.btn_envio.disabled = false;
                    if (envio.status === 'OK') {
                        not.Ok(envio.mssg, '', 3000);
                        setTimeout(() => {
                            window.location.href = srv.url()+'Pago/detalle/'+envio.id;
                        }, 700);
                    }else{
						feedback(envio.error)
                        if(envio.status === 'WARN') not.Aviso(envio.mssg, '', 3000);
                        else not.Error(envio.mssg, '', 3000);
                    }
                }

            } catch (error) {
                console.log(error);
                event.target.btn_envio.disabled = false;
                app.preload('close');
                not.Error('Error al registrar el pago', '', 3000);
            }

        })
    }
    if (document.getElementById('btn_sort')){
        document.getElementById('btn_sort').addEventListener('click', event => {
            let t = (table_sort=='asc') ? 'desc' : 'asc';
            order_table(t)
        })
    }

    function procesar_pago(){
        try {
            pago_json.total_cuota = cuota_array.reduce((acum, current) => {
                return parseFloat(acum + current.monto);
            }, 0);
            pago_json.total_mora = mora_array.reduce((acum, current) => {
                if(current.accion === 'C')
                    return parseFloat(acum + current.monto); 
                else
                    return 0;
            }, 0);     
            pago_json.subtotal = pago_json.total_cuota + pago_json.total_mora;   
            pago_json.total = pago_json.total_cuota + pago_json.total_mora + pago_json.adicional - pago_json.total_descuento;
            pago_json.total = app.Rx_round(pago_json.total);
            pago_json.devuelto = (pago_json.recibido >= pago_json.total) ? parseFloat(pago_json.recibido) - pago_json.total : 0;
            pago_json.devuelto = app.Rx_round(pago_json.devuelto);

            app.render('recibo_cuota', `+ S/ ${app.Rx_round(pago_json.total_cuota)}`);
            app.render('recibo_mora', `+ S/ ${app.Rx_round(pago_json.total_mora)}`);
            app.render('recibo_descuento', `- S/ ${app.Rx_round(pago_json.total_descuento)}`);
			app.render('recibo_adicional', '+ S/ '+app.Rx_round(pago_json.adicional));
            app.render('recibo_total', `S/ ${app.Rx_round(pago_json.total)}`);
            // app.render('input_devuelto', app.Rx_round(pago_json.devuelto));
            app.render('total_cuotas_detalle', cuota_array.length+' cuotas')

            form_registro.vuelto.value = app.Rx_round(pago_json.devuelto);
        } catch (error) {
            console.log('procesar_pago:error', error); 
        }
    }    
	const cuota_seleccion_total = (estado) => {
        try {
            let c_items = document.getElementsByName('cuota_item');
            for (let i = 0; i < c_items.length; i++) {
                c_items[i].checked = estado;
                if (estado) {
                    cuota_array.push({
                        id: c_items[i].dataset.id,
                        tipo: 'CU',
                        referencia: 'Cuota '+c_items[i].dataset.orden,
                        monto: parseFloat(c_items[i].dataset.monto)
                    });
                }else{
                    cuota_array = [];
                }
            }
            procesar_pago();
        } catch (error) {
            not.Error('Error al seleccionar las cuotas', '', 3000);
        }
    }
	function feedback(err){
		if (err) {
			if (err.descuento) {
				document.getElementById('descuento_feed').innerHTML = err.descuento;
			}

			if (err.recibido) {
				document.getElementById('monto_feed').innerHTML = err.recibido;
			}
		}
	}

    // recode: 17-06-2023
    function cuotas_recuento_base(){
        cuota_array = [];
        let cuotas = document.querySelectorAll('[name=cuota_item]');
        if (cuotas == undefined || cuotas.length <= 0) return;
        cuotas.forEach(cuota => {
            if (cuota.checked){
                cuota_array.push({
                    id: cuota.dataset.id,
                    tipo: 'CU',
                    referencia: 'Cuota '+cuota.dataset.orden,
                    monto: parseFloat(cuota.dataset.monto),
                    orden: parseInt(cuota.dataset.orden)
                });
            }
        });
        procesar_pago();

    }
    function cuotas_stash_delete(action,nro_orden){
        try {
            let items = document.querySelectorAll('[name=cuota_item]');

            if (action == 'ADD') {
               
                items.forEach(item => {
                    if (parseInt(item.dataset.orden) < nro_orden) {
                        item.checked = true;
                        // console.log('add: '+item.dataset.orden)
                    }
                });
            }else if (action === 'REMOVE') {
                cuota_array.forEach((cuota,index) => {
                    if (cuota.orden >= nro_orden) {
                        cuota_array.splice(index, 1);
                        // console.log('delete: '+index)
                    }
                });
                items.forEach(item => {
                    if (parseInt(item.dataset.orden) >= nro_orden) {
                        item.checked = false;
                    }
                });
            }
        } catch (error) {
            console.log(error)
        }
    }

   
})();

