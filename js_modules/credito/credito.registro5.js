(function(){
    var app, notify_area;
    var cliente_search = {
        'filtro': 'nombre_apellido',
        'buscar': '',
        'limite': 6
    },
    cliente_data = {'id': 0, 'nombres': '', 'documento': '', 'distrito': ''},
    prenda_data = {'id': 0, 'descripcion': '', 'categoria': '', 'tipo': '', 'valoracion': '', 'sub_tipo': ''},
    prestamo_data = {
        'garantia': true,
        'monto': 0,
        'interes': 0,
        'penalidad': 0,
        'tipo': '',
        'cuota': 0,
        'mora': 0,
        'tasa': 0,
        'prorroga': 3,
        'cargo': 0,
		'ciclo': 'SEMANAL'
    }

    document.addEventListener('DOMContentLoaded', function(){
        app = new App();
        wizard_redirect(1);
        app.defineEvent('#credito_nuevo_form', 'submit', function(event){
			event.preventDefault()
			wizard_redirect(parseInt(event.target.dataset.sig))
        });
        app.defineEvent('#cliente_input_buscar', 'keyup', function(event){
			if(event.target.value.length > 0){
				cliente_search.buscar = event.target.value
				render_clientes()
			}
        });
        app.defineEvent('#cliente_select_tipo', 'change', function(event){
			if(event.target.value.length > 0){
				cliente_search.filtro = event.target.value
				render_clientes()
			}
        });
        app.defineEvent('#btn_prenda_nueva', 'click', function(event){
            if(cliente_data.id){
                app.render('prenda_categoria', '<option value="">--Sin Datos--</option>');
                render_tipo_prenda();
                modal_prenda_reset();
                prenda_registro_reset_feedback();
                app.render('FormArea', '');
            }
        });
        app.defineEvent('#check_cargo', 'change', function(event){
            prestamo_data.cargo = event.target.checked ? 1 : 0;
        });
		app.defineEvent('#check_ciclo_pago', 'change', function(event){
            event.preventDefault()
            let change_string = (prestamo_data.ciclo === 'MENSUAL') ? 'mensual a semanal' : 'semanal a mensual';

            if (prestamo_data.ciclo === 'SEMANAL') {
                if (confirm(`El credito pasara a de cobro semanal a mensual,esta seguro de continuar?`)) {
                    event.target.checked;
                    prestamo_data.ciclo = 'MENSUAL';
                }
            } else if(prestamo_data.ciclo === 'MENSUAL'){
                if (confirm(`El credito pasara a de cobro mensual a semanal,esta seguro de continuar?`)) {
                    event.target.checked = false;
                    prestamo_data.ciclo = 'SEMANAL';
                }
            }
        });
        // Prestamo Form
        app.defineEvent('#link__edit_adv', 'click', function(){
            // <div class="form-group text-left">
            //     <div class="form-label">Monto:</div>
            //     <input type="number" step="any" class="form-control" 
            //         id="edt_monto" value="${prestamo_data.monto}">
            // </div>
            // <div class="form-group text-left">
            //     <div class="form-label">Pago Semanal:</div>
            //     <input type="number" step="any" class="form-control" 
            //         id="edt_cuota" value="${prestamo_data.cuota}">
            // </div>
            Swal.fire({
                title: `
                    <span class="text-primary">Edicion Avanzada</span>
                `,
                icon: 'info',
                html:                `   
                    <div class="form-group text-left">
                        <div class="form-label">Tipo de Tasa:</div>
                        <select id="edt_tipo" class="form-control" value="${prestamo_data.tipo}">
                            <option value="P">Porcentual</option>
                            <option value="F">Fijo</option>
                        </select>
                    </div>
                    <div class="form-group text-left">
                        <div class="form-label">Tasa:</div>
                        <input type="number" step="any" class="form-control" 
                            id="edt_interes" value="${prestamo_data.interes}">
                    </div>                    
                    <div class="form-group text-left">
                        <div class="form-label">Mora:</div>
                        <input type="number" step="any" class="form-control" 
                            id="edt_mora" value="${prestamo_data.mora}">
                    </div>                    
                `,
                showCloseButton: true,
                focusConfirm: false,
                confirmButtonText: "Guardar",
                preConfirm: () => {
                    // prestamo_data.monto = document.getElementById('edt_monto').value;
                    // prestamo_data.cuota = document.getElementById('edt_cuota').value;
                    // prestamo_data.interes = document.getElementById('edt_interes').value;
                    // prestamo_data.mora = document.getElementById('edt_mora').value;
                    // prestamo_data.tipo = document.getElementById('edt_tipo').value;
                    let ed_int = app.get_id('edt_interes').value, 
                    ed_mora = app.get_id('edt_mora').value, 
                    ed_tip  = app.get_id('edt_tipo').value;
                    prestamo_edit_validate(ed_int, ed_tip, ed_mora);
                    render_resumen(['cliente', 'prenda', 'prestamo']);
                }
            })
        });
        app.defineEvent('#prestamo_monto', 'keyup', function(event){
            app.render('feed_monto', '')
            if (event.target.value && Number(event.target.value)) {
                prestamo_data.monto = app.Rx_round(event.target.value);
            }else{
                app.render('feed_monto', 'Monto Invalido')
            }
        });
        app.defineEvent('#btn_test', 'click', function(){
            
        })
        app.defineEvent('#prestamo_interes', 'keyup', function(event){
            app.render('feed_interes', '')
            if (event.target.value && Number(event.target.value)) {
                prestamo_data.interes = app.Rx_round(event.target.value);
            }else{
                app.render('feed_interes', 'Monto Invalido')
            }
        });
        app.defineEvent('#prestamo_penalidad', 'keyup', function(event){
            app.render('feed_interes', '')
            if (event.target.value && Number(event.target.value)) {
                prestamo_data.penalidad = app.Rx_round(event.target.value);
            }else{
                app.render('feed_interes', 'Monto Invalido')
            }
        });
        app.defineEvent('#form_prenda_registro', 'submit', async event => {
            event.preventDefault();
            if (prenda_validate(event.target)) {
                try {
                    app.preload('open', 'Guardando..');
                    var pdata = new FormData(event.target);
                    pdata.append('cliente', cliente_data.id);
                    pdata.append('tipo', prenda_data.sub_tipo);
                    var req = await fetch(srv.url()+'Prenda/prot_registro', {
                        method: 'POST',
                        body: pdata 
                    });
                    var res = await req.json();
                    // let prenda = new Prenda();
                    // let registro = await prenda.Registrar(pdata);
                    
                    app.preload('close');
                    if (res.status === 'OK') {
                        event.target.reset();
                        $("#modal_prenda_registro").modal("hide");
                        render_prendas(parseInt(res.id));
                        not.Ok('Prenda registrada', '', 2500);
                    }else{
                        if (res.status === 'ERROR') {
                            console.log('ruta: error')
                            not.Error(res.message, '', 2500);
                            prenda_registro_feedback(res.error);
                        }else{
                            not.Aviso(res.message, '', 2500);
                        }
                    }
                } catch (error) {
                    console.log(`@registrar_prenda [${error}]`);
                    app.preload('close');
                    not.Aviso('Ocurrio un error al registrar', '', 1500);
                }
            }
        })
    })
    document.addEventListener('click', event => {
		if(event.target.matches('#btn_atras')){
			wizard_redirect(parseInt(event.target.dataset.target))
		}
		if(event.target.matches('[name=cliente_detalle_item]')){
			if (event.target.dataset.id) {
				render_cliente_conducta(event.target.dataset.id, event.target.dataset.nombre)
			}
		}
    });
    document.addEventListener('change', event => {
		if (event.target.matches('[name=cliente_item]')){
			if(event.target.dataset.id){
				cliente_data.id = event.target.dataset.id;
				cliente_data.documento = event.target.dataset.documento;
				cliente_data.nombres = event.target.dataset.nombre;
                cliente_data.distrito = event.target.dataset.distrito;
                render_cliente_detalle();
			}else{
				app.SysMessage(notification_id, 'Cliente no identificado.', 'warning');
			}
        }
        if (event.target.matches('[name=tprenda_item]')){
            if (event.target.value) {
                render_categorias(parseInt(event.target.value));
                render_prenda_form(event.target.dataset.ref);
                prenda_data.sub_tipo = event.target.dataset.ref;
            }
        }
        if (event.target.matches('[name=prenda_item]')){
            if (event.target.dataset.garantia === "NO") {
                prestamo_data.garantia = false;
                prenda_data.id = 0;
                prenda_data.descripcion = "Sin Garantia";
                prenda_data.categoria = "Sin Garantia";
                prenda_data.tipo = "Sin Garantia";
                prenda_data.valoracion = "Sin Garantia";
            }else{
                prestamo_data.garantia = true;
                prenda_data.id = parseInt(event.target.value);
                prenda_data.descripcion = event.target.dataset.descripcion;
                prenda_data.categoria = event.target.dataset.categoria;
                prenda_data.tipo = event.target.dataset.tipo;
                prenda_data.valoracion = event.target.dataset.valoracion;
            }
        }
	});

    function modal_prenda_reset(){
        app.get_id('form_prenda_registro').reset();
        $('#electro_form').slideUp('fast');
        $('#joya_form').slideUp('fast');
        $('#vehiculo_form').slideUp('fast');
        $('#modal_prenda_registro').modal('show');
    }

    function prestamo_edit_validate(int, tipo, mora){
        if (Number(int) && int > 0) {
            prestamo_data.interes = int;
        }
        if (tipo.length > 0) {
            prestamo_data.tipo = tipo;
        }
        if (Number(mora) && mora > 0) {
            prestamo_data.mora = mora;
        }

        if (prestamo_data.tipo === 'P') {
            let px = ((prestamo_data.monto * prestamo_data.interes) / 100);
            prestamo_data.cuota = app.Rx_round(px); 
        } else if(prestamo_data.tipo === 'F'){
            prestamo_data.cuota = prestamo_data.interes;
        }
    }

    function prenda_validate(data){
        reset_prenda_feed();
        var res = true;
        if (data.descripcion.value.length <= 0) {
            app.render('descripcion_feed', 'Campo requerido');
            res = false;
        }
        switch (data.categoria.dataset.ref) {
            case 'ELECTRO':
                if (data.serie.value.length <= 0) {
                    app.render('serie_feed', 'Campo requerido');
                    res = false;
                }
                break;
        
            case 'JOYA':
                if (data.quilate.value.length <= 0) {
                    app.render('quilate_feed', 'Campo requerido');
                    res = false;
                }
                if (data.peso.value.length <= 0) {
                    app.render('peso_feed', 'Campo requerido');
                    res = false;
                }
                break;
            case 'VEHICULO':
                if (data.marca.value.length <= 0) {
                    app.render('marca_feed', 'Campo requerido');
                    res = false;
                }
                if (data.modelo.value.length <= 0) {
                    app.render('modelo_feed', 'Campo requerido');
                    res = false;
                }
                if (data.anio.value.length <= 0) {
                    app.render('anio_feed', 'Campo requerido');
                    res = false;
                }
                if (data.placa.value.length <= 0) {
                    app.render('placa_feed', 'Campo requerido');
                    res = false;
                }
                break;
        }
        return res;
    }

    function prenda_registro_feedback(data){
        reset_prenda_feed();
        if (data.descripcion) app.render('descripcion_feed', data.descripcion);
        if (data.serie) app.render('serie_feed', data.serie);
        if (data.marca) app.render('marca_feed', data.marca);
        if (data.modelo) app.render('modelo_feed', data.modelo);
        if (data.quilate) app.render('quilate_feed', data.quilate);
        if (data.peso) app.render('peso_feed', data.peso);
        if (data.anio) app.render('anio_feed', data.anio);
        if (data.placa) app.render('placa_feed', data.placa);
        if (data.kilometraje) app.render('kilometraje_feed', data.kilometraje);
    }
    
    function prenda_registro_reset_feedback(){
        reset_prenda_feed();
        app.render('descripcion_feed', '');
        app.render('serie_feed', '');
        app.render('marca_feed', '');
        app.render('modelo_feed', '');
        app.render('quilate_feed', '');
        app.render('peso_feed', '');
        app.render('anio_feed', '');
        app.render('placa_feed', '');
        app.render('kilometraje_feed', '');
    }

    function reset_prenda_feed(){
        app.render('descripcion_feed', '');
        app.render('modelo_feed', '');
        app.render('marca_feed', '');
        app.render('serie_feed', '');
        app.render('placa_feed', '');
        app.render('peso_feed', '');
        app.render('anio_feed', '');
    }

    function reset_prenda_form(){
        $("#electro_form").slideUp('fast');
        $("#joya_form").slideUp('fast');
        $("#vehiculo_form").slideUp('fast');
        app.render("tprenda_area", "");
    }

    async function wizard_redirect(ind){
        let form = document.getElementById('credito_nuevo_form');
        switch (ind) {
            case 1:
                form.setAttribute('data-actual', 1);
                form.setAttribute('data-sig', 2);
                wizard_render('cliente');
                render_resumen();
                // reset_module({'cliente': true});
                break;
        
            case 2:
                if (wizard_validate(['cliente'])) {
                    form.setAttribute('data-actual', 2);
                    form.setAttribute('data-sig', 3);
                    wizard_render('prenda');
                    render_resumen(['cliente']);
                    render_prendas();
                    reset_data_prendas();
                    prestamo_data.garantia = true;
                }else{
                    app.SysMessage('creditoreg_sysmssg', 'Cliente no seleccionado!', 'warning');
                }
                break;
            
            case 3:
                if ( wizard_validate(['cliente', 'prenda']) ) {
                    form.setAttribute('data-actual', 3);
                    form.setAttribute('data-sig', 4);
                    wizard_render('prestamo');
                    render_resumen(['cliente','prenda']);
                }
                break;

            case 4:
                let monto_g = document.getElementById('monto').value;
                prestamo_data.monto = app.Rx_round(monto_g);
                if ( wizard_validate(['cliente', 'prenda', 'prestamo']) ) {
                    form.setAttribute('data-actual', 4);
                    form.setAttribute('data-sig', 5);
                    await procesar_tarifa();
                    
                    wizard_render('registro');
                    render_resumen(['cliente','prenda', 'prestamo']);
                }
                break;
                
            case 5:
                try {
                    if (wizard_validate(['cliente', 'prenda', 'prestamo'])) {
                        // console.log(prestamo_data);
                        app.preload('open', 'Registrando..');
                        let data = new FormData(event.target);
                        let p_id = (prestamo_data.garantia) ? prenda_data.id : 0;
                        data.append('prenda', p_id);
                        data.append('cliente', cliente_data.id);
                        data.append('monto', prestamo_data.monto);
                        data.append('pago', prestamo_data.cuota);
                        // data.append('tarifa', prestamo_data.tarifa);
                        data.append('mora', prestamo_data.mora);
                        data.append('cargo', prestamo_data.cargo);
                        data.append('tasa', prestamo_data.tasa);
                        data.append('interes', prestamo_data.interes);
                        data.append('tipo', prestamo_data.tipo);
						data.append('ciclo', prestamo_data.ciclo);
                        data.append('prorroga', parseInt(document.getElementById('prorroga').value));                                                     

                        app.preload('open', 'Guardando..');
                        let credito = new Credito();
                        let registro = await credito.Registrar(data);
                        app.preload('close');
                        if (registro.status === 'OK') {
                            app.renderAlert('ok', registro.mssg, 2500);
                            setTimeout(() => {
                                window.location.href = srv.url()+'Credito/detalle/'+registro.id;
                            }, 500);
                        }else{
                            app.renderAlert('aviso', registro.mssg, 2500);
                        }
                    }
                } catch (error) {
                    
                }
                break;
        }
    }

    function wizard_validate(mod){
        var result = true;

        if (mod.includes('cliente') && (!cliente_data.id || parseInt(cliente_data.id) <= 0) ) {
            result = false;
            app.SysMessage('creditoreg_sysmssg', 'Cliente no seleccionado!', 'warning'); 
        }

        if (mod.includes('prenda')) {
            if (prestamo_data.garantia === true && (!prenda_data.id || parseInt(prenda_data.id) <= 0) ) {
                result = false;
                app.SysMessage('creditoreg_sysmssg', 'Garantia no definida', 'warning');
            }
        }

        if (mod.includes('prestamo')) {
            if (!Number(prestamo_data.monto) && prestamo_data.monto <= 0) {
                result = false;
                app.SysMessage('creditoreg_sysmssg', 'Prestamo no definido o invalido', 'warning');
            }
        }
        return result;
    }

    function wizard_render(view){
		switch (view) {
			case 'cliente':
                app.render('view_titulo', '1. Seleccion de Cliente');
				$('#view_cliente').slideDown('fast');
                $('#view_prenda').slideUp('fast');
                $('#view_prestamo').slideUp('fast');
                $('#view_registro').slideUp('fast');
                app.render('view_controls', `
                    <button type="submit" class="btn btn-lg btn-bsteel">
                        Siguiente 
                        <i class="fe fe-arrow-right"></i>
                    </button>
                `);
				break;
			case 'prenda':
                app.render('view_titulo', '2. Seleccion de Prenda');
				$('#view_prenda').slideDown('fast');
                $('#view_prestamo').slideUp('fast');
                $('#view_cliente').slideUp('fast');
                $('#view_registro').slideUp('fast');
                app.render('view_controls', `
                    <a id="btn_atras" href="javascript:void(0)" class="btn btn-lg btn-secondary" data-target="1">
                        <i class="fe fe-arrow-left"></i> Atras
                    </a>
                    <button type="submit" class="btn btn-lg btn-bsteel">
                        Siguiente 
                        <i class="fe fe-arrow-right"></i>
                    </button>
                `);
				break;
			case 'prestamo':
                app.render('view_titulo', '3. Calculo de Prestamo');
				$('#view_prestamo').slideDown('fast');
                $('#view_cliente').slideUp('fast');
                $('#view_prenda').slideUp('fast');
                $('#view_registro').slideUp('fast');
                document.getElementById('check_ciclo_pago').checked = (prestamo_data.ciclo === 'MENSUAL') ? true : false;
                app.render('view_controls', `
                    <a id="btn_atras" href="javascript:void(0)" class="btn btn-lg btn-secondary" data-target="2">
                        <i class="fe fe-arrow-left"></i> Atras
                    </a>
                    <button type="submit" class="btn btn-lg btn-bsteel">
                        Siguiente 
                        <i class="fe fe-arrow-right"></i>
                    </button>
                `);
                break;
            case 'registro':
                app.render('view_titulo', '4. Registrar Credito');
				$('#view_prestamo').slideUp('fast');
                $('#view_cliente').slideUp('fast');
                $('#view_prenda').slideUp('fast');
                $('#view_registro').slideDown('fast');
                app.render('view_controls', `
                    <a id="btn_atras" href="javascript:void(0)" class="btn btn-lg btn-secondary" data-target="3">
                        <i class="fe fe-arrow-left"></i> Atras
                    </a>
                    <button type="submit" class="btn btn-success">
                        <i class="fe fe-save"></i>
                        Registrar
                    </button>
                `);
                break;
        }
        console.log(prestamo_data)
	}
   
    function render_resumen(data_pull = []){
        $("#__resumen_foot").slideUp('fast');
        var html_res = '';
        
        if (data_pull.includes('cliente')) {
            html_res += `
                <li>
                    <div class="rs-title">1. Cliente</div>
                    <p>
                        <b>Nombres y Apellidos:</b>
                        <a class="text-dark" href="${srv.url()}Cliente/detalle/${cliente_data.id}" target="_blank">
                            ${cliente_data.nombres}
                        </a>
                    </p>
                    <p>
                        <b>Documento:</b>
                        <span>${cliente_data.documento}</span>
                    </p>
                    <p>
                        <b>Distrito:</b>
                        <span>${cliente_data.distrito}</span>
                    </p>
                </li>
            `;
        }
        
        if (data_pull.includes('prenda')) {
            html_res += `
                <li>
                    <div class="rs-title">2. Prenda</div>
                    <p>
                        <b>Descripcion:</b>
                        <span>${prenda_data.descripcion}</span> 
                    </p>
                    <p>
                        <b>Categoria:</b>
                        <span>${prenda_data.categoria}</span>
                    </p>
                    <p>
                        <b>Estado:</b>
                        <span>${prenda_data.valoracion}</span>
                    </p>
                </li>
            `;
        }

        if (data_pull.includes('prestamo')) {
            $("#__resumen_foot").slideDown('fast');
            let pdata = prestamo_data;
            html_res += `
                <li>
                    <div class="rs-title">3. Prestamo</div>
                    <table class="rs-table">
                    <tr>
                        <td>Monto:</td>
                        <td>S/ ${prestamo_data.monto}</td>
                    </tr>
                    <tr>
                        <td>Interes:</td>
                        <td>${prestamo_data.tipo === 'P' ? prestamo_data.interes+'%' : 'S/ '+prestamo_data.interes}</td>
                    </tr>
                    <tr>
                        <td>Tipo:</td>
                        <td>${prestamo_data.tipo === 'P' ? 'Porcentual' : 'Fijo'}</td>
                    </tr>
                    <tr>
                        <td>Pago ${ prestamo_data.ciclo }:</td>
                        <td>S/ ${prestamo_data.cuota}</td>
                    </tr>
                    <tr>
                        <td>Mora:</td>
                        <td>S/ ${prestamo_data.mora}</td>
                    </tr>
                    </table>
                </li>
            `;
        }

        // if(!data_pull.cliente && 
        //     !data_pull.prenda && 
        //     !data_pull.prestamo){
        //     $("#res_blank").slideDown('fast');
        // }
        if (data_pull.length <= 0) html_res = '<li class="text-center">Sin Data</li>';
        app.render('__resumen_view', html_res);
    }

    async function get_clientes(){
        try {
            var req = await fetch(srv.url()+'Cliente/ServiceConsulta', {
                method: 'POST',
                body: JSON.stringify(cliente_search),
                headers: {
                    'Content-type': 'application/json; charset=UFT-8'
                }
            });
            var res = await req.json();
            return res;
        } catch (error) {
            app.SysMessage('creditoreg_sysmssg', 'La busqueda de datos no se completo.')
            return [];
        }
    }

    async function render_clientes(){
        // $('#caption_cliente').slideDown('fast');
        app.render('view_cliente_table', `
            <tr>
                <td colspan="4" class="text-center text-primary">
                    <i class="fas fa-spinner fa-pulse"></i>&nbsp;
                    Cargando...
                </td>
            </tr>
        `);
    	let html_resp = "";
    	var data = await get_clientes();
        
        if (data.length > 0) {
            data.forEach((element, index) => {
                html_resp += `
                    <tr>
                        <td>
                            <a class="btn-link" href="${srv.url()}Cliente/detalle/${element.id}">
                                ${element.nombres} ${element.paterno} ${element.materno}
                            </a>
                        </td>
                        <td>${element.documento}</td>
                        <td>${element.distrito}</td>
                        <td class="text-center">
                            <label class="selectgroup-item" style="margin-bottom:0;">
                                <input type="radio" class="selectgroup-input" name="cliente_item" data-id="${element.id}" 
                                data-nombre="${element.nombres} ${element.paterno} ${element.materno}" 
                                data-documento="${element.documento}" data-distrito="${element.ciudad}">
                                <span class="ndc_selectgroup-button">
                                    ELEGIR
                                </span>
                            </label>
                        </td>
                    </tr>`;
            })
        }else{
            html_resp += `
                <tr>
                    <td colspan="6" class="text-center">
                        <i class="fe fe-bar-chart-2"></i>
                        Sin resultados.
                    </td>
                </tr>`;
        }
        $('#caption_cliente').slideUp('fast');
        app.render('view_cliente_table', html_resp);
    }

    async function get_cliente_conducta(_id){
        try {
            var req = await fetch(srv.url()+'Cliente/ServiceConducta', {
                method: 'POST',
                body: JSON.stringify({id: _id}),
                headers: {
                    'Content-type': 'application/json; chartset=UFT-8'
                }
            });
            var res = await req.json();
            return res;
        } catch (error) {
            return [];
        }

    }

    async function render_cliente_conducta(_id, _cliente = ''){
        var data = await get_cliente_conducta(_id);
        var calificacion = `
            <div class="mb-2 p-1">
                <h3>---</h3>
                <small class="m-0 text-muted">Calificacion</small>
            </div>`;
        var blacklist = '', alerta_res = '';
        if (data) {
            if (data.conducta) {
                calificacion = `
                    <div class="mb-2 p-1">
                        <h3>${data.conducta.calificacion}</h3>
                        <small class="m-0 text-muted">Calificacion</small>
                    </div>
                `;
            }
            if (data.conducta && data.conducta.blacklist === 'SI') {
                blacklist = `
                    <div class="alert alert-error p-3 text-justify">
                        <b class="d-block mb-2">Cliente en Lista Negra</b>
                        ${ data.conducta.motivo }   
                    </div>
                `;
            }
            if (data.alerta && data.alerta.length > 0) {
                alerta_res += '<div class="alert alert-aviso p-3 text-justify">';
                alerta_res += '<b class="d-block mb-2">Alertas Pendientes:</b>';
                data.alerta.forEach(element => {
                    alerta_res += '<p>'+element.contenido+'</p>'
                })
                alerta_res += '</div>';
            }            
        }
        Swal.fire({
            title: `
                <h4 class="text-primary">
                    ${ _cliente }
                </h4>
            `,
            icon: 'info',
            html:
            `   ${ calificacion }
                ${ blacklist }
                ${ alerta_res }                        
                `,
            showCloseButton: true,
            focusConfirm: false,
            confirmButtonText:
                'De acuerdo'
        })
    }

    async function render_cliente_detalle(){
        let html = '';
        let html_blacklist = '';
        if (cliente_data.id && cliente_data.id > 0) {
            html = `
                <div class="">
                    <label for="" class="form-label">Nombres y Apellidos: </label>
                    <span>${cliente_data.nombres}</span>
                </div>
                <div class="">
                    <label for="" class="form-label">Documento: </label>
                    <span>${cliente_data.documento}</span>
                </div>
                <div class="">
                    <label for="" class="form-label">Ciudad: </label>
                    <span>${cliente_data.distrito}</span>
                </div>
            `;

            // let clasifica = new Clasificacion();
            // let info = await clasifica.Consulta(cliente_data.id);
            // if (info.data) {
            //     if (info.data.calificacion) {
            //         document.getElementById('pcliente_calificacion').style.display = '';
            //         app.render('pcliente_calificacion', `
            //             <div class="text-center text-primary" style="font-size: 34px; font-weight: bold;">${info.data.calificacion}</div>
            //             <p class="text-secondary text-center">Calificacion</p>
            //         `);
            //     }else{
            //         document.getElementById('pcliente_calificacion').style.display = 'none';
            //         app.render('pcliente_calificacion', `
            //             <div class="text-center text-primary" style="font-size: 34px; font-weight: bold;">--</div>
            //             <p class="text-secondary text-center">Calificacion</p>
            //         `);
            //     }

            //     if (info.data.blacklist === 'SI') {
            //         document.getElementById('pcliente_blacklist').style.display = '';
            //         html_blacklist = `
            //             <h6 class="p-0 text-center">CLIENTE EN LISTA NEGRA <i class="fe fe-slash"></i> </h6>
            //             <span class="d-block text-center">MOTIVO: ${info.data.motivo}</span>
            //         `;
            //     }else{
            //         document.getElementById('pcliente_blacklist').style.display = 'none';
            //     }
            // }else{
            //     document.getElementById('pcliente_blacklist').style.display = 'none';
            // }
        }else{
            html = `
                <div class="text-center">
                    <i class="fe fe-info"></i> No Seleccionado
                </div>
            `;
        }
        app.render('pcliente_data', html);
        app.render('pcliente_blacklist', html_blacklist);
    }

    async function reset_module(module){
        if (module.cliente) {
            // app.render('view_cliente_table', `
            //     <tr>
            //         <td colspan="6" class="text-center p-4 font-weight-bold text-bsteel">
            //             <i class="fe fe-chevrons-right"></i>
            //             Inicia la busqueda
            //         </td>
            //     </tr>
            // `);
            cliente_data.id = 0;
            cliente_data.nombres = "";
            cliente_data.distrito = "";
            // document.getElementById('cliente_input_buscar').value = "";
        }
    }

    async function get_tipo_prenda(){
        try {
            var req = await fetch(srv.url()+'TipoPrenda/consulta');
            var res = await req.json();
            return res;
        } catch (error) {
            return []; 
        }
    }

    async function render_tipo_prenda(){
        var data = await get_tipo_prenda();
        var html_resp = '';
        if (data) {
            data.forEach(element => {
                html_resp += `
                    <label class="custom-control custom-radio custom-control-inline">
                        <input type="radio" data-ref="${element.nombre}" 
                        class="custom-control-input" name="tprenda_item" value="${ element.id }">
                        <span class="custom-control-label">${ element.nombre }</span>
                    </label>
                `;
            })
        }else{
            html_resp = '<span class="text-muted"></span>';
        }
        app.render('tprenda_area', html_resp);
    }

    async function get_categorias(tipo){
        try {
            var req = await fetch(srv.url()+'Categoria/ServiceListarPorTipo', {
                method: 'POST',
                body: JSON.stringify({ 'tipo': tipo })
            });
            var res = await req.json();
            return res;
        } catch (error) {
            return []; 
        }
    }

    async function render_categorias(id){
        var data = await get_categorias(id);
        var html_resp = '<option value="">-- Seleccionar --</option>';
        if (data) {
            data.forEach(element => {
                html_resp += `
                    <option value="${ element.id }" data-tipo="${element.tipo}">
                        ${ element.nombre }
                    </option>
                `;
            })
        }else{
            html_resp = '<option value="">--Sin Datos--</option>';
        }
        app.render('prenda_categoria', html_resp);
    }

    async function render_prenda_form(t){
        switch (t) {
            case 'ELECTRO':
                // $("#vehiculo_form").slideUp('fast');
                // $("#joya_form").slideUp('fast');
                // $("#electro_form").slideDown('fast');
                app.render('FormArea', `
                    <div class="col-md-6 form-group">
                        <div class="form-label">Serie:</div>
                        <input type="text" class="form-control border-rock" name="serie">
                        <small id="serie_feed" class="text-danger"></small>
                    </div>
                    <div class="col-md-6 form-group">
                        <div class="form-label">Marca:</div>
                        <input type="text" class="form-control border-rock text-uppercase" name="marca">
                        <small id="marca_feed" class="text-danger"></small>
                    </div>
                    <div class="col-md-6 form-group">
                        <div class="form-label">Modelo:</div>
                        <input type="text" class="form-control border-rock text-uppercase" name="modelo">
                        <small id="modelo_feed" class="text-danger"></small>
                    </div>
                    <div class="col-md-6 form-group">
                        <div class="form-label">Estado:</div>
                        <input type="number" step="any" class="form-control border-rock" name="estado" value="5">
                    </div>
                    <div class="col-md-12 form-group">
                        <div class="form-label">Observaciones:</div>
                        <textarea name="observacion" class="form-control border-rock text-uppercase" cols="3" rows="3"></textarea>
                    </div>
                    <div class="col-md-12 form-group">
                        <div class="form-label">Comentarios:</div>
                        <textarea name="comentario" class="form-control border-rock text-uppercase" cols="3" rows="3"></textarea>
                    </div>
                `);
                break;
        
            case 'JOYA':
                // $("#vehiculo_form").slideUp('fast');
                // $("#joya_form").slideDown('fast');
                // $("#electro_form").slideUp('fast');
                app.render('FormArea', `
                    <div class="col-md-6 form-group">
                        <div class="form-label">Quilate:</div>
                        <input type="text" class="form-control border-rock text-uppercase" name="quilate">
                        <small id="quilate_feed" class="text-danger"></small>
                    </div>
                    <div class="col-md-6 form-group">
                        <div class="form-label">Peso:</div>
                        <input type="text" class="form-control border-rock" name="peso">
                        <small id="peso_feed" class="text-danger"></small>
                    </div>
                    <div class="col-md-6 form-group">
                        <div class="form-label">Estado:</div>
                        <input type="number" step="any" class="form-control border-rock" name="estado" value="5">
                    </div>
                    <div class="col-md-12 form-group">
                        <div class="form-label">Comentarios:</div>
                        <textarea name="comentario" class="form-control border-rock text-uppercase" cols="3" rows="3"></textarea>
                    </div>
                `);
                break;

            case 'VEHICULO':
                // $("#vehiculo_form").slideDown('fast');
                // $("#joya_form").slideUp('fast');
                // $("#electro_form").slideUp('fast');
                app.render('FormArea', `
                    <div class="col-md-6 form-group">
                        <div class="form-label">Marca:</div>
                        <input type="text" class="form-control border-rock text-uppercase" name="marca">
                        <small id="marca_feed" class="text-danger"></small>
                    </div>
                    <div class="col-md-6 form-group">
                        <div class="form-label">Modelo:</div>
                        <input type="text" class="form-control border-rock text-uppercase" name="modelo">
                        <small id="modelo_feed" class="text-danger"></small>
                    </div>
                    <div class="col-md-6 form-group">
                        <div class="form-label">A���o:</div>
                        <input type="text" class="form-control border-rock" name="anio">
                        <small id="anio_feed" class="text-danger"></small>
                    </div>
                    <div class="col-md-6 form-group">
                        <div class="form-label">Placa:</div>
                        <input type="text" class="form-control border-rock text-uppercase" name="placa">
                        <small id="placa_feed" class="text-danger"></small>
                    </div>
                    <div class="col-md-6 form-group">
                        <div class="form-label">Kilometraje:</div>
                        <input type="text" class="form-control border-rock text-uppercase" name="kilometraje">
                        <small id="kilometraje_feed" class="text-danger"></small>
                    </div>
                    <div class="col-md-6 form-group">
                        <div class="form-label">Estado:</div>
                        <input type="number" step="any" class="form-control border-rock" name="estado" value="5">
                    </div>
                    <div class="col-md-12 form-group">
                        <div class="form-label">Observaciones:</div>
                        <textarea name="observacion" class="form-control border-rock text-uppercase" cols="3" rows="3"></textarea>
                    </div>
                    <div class="col-md-12 form-group">
                        <div class="form-label">Comentarios:</div>
                        <textarea name="comentario" class="form-control border-rock text-uppercase" cols="3" rows="3"></textarea>
                    </div>
                `);
                break;
            }
            $("#FormArea").slideDown('fast');
    }
    
    async function get_prendas(){
        try {
            var cliente = new Cliente();
            var res = await cliente.BuscarPrendas(cliente_data.id, 'disponible');
            return res;
        } catch (error) {
            console.log(error)
            return [];
        }
    }

    async function render_prendas(idsearch = 0){
        $('#caption_cliente').slideDown('fast');
        var data = await get_prendas();
        var html = "";
        if (data && data.status === 'OK' && data.result.length > 0) {           
            data.result.forEach(element => {
                var chck_prenda = `
                    <label class="selectgroup-item" style="margin-bottom:0;">
                        <input type="radio" class="selectgroup-input" name="prenda_item" value="${element.id}" 
                        data-descripcion="${element.descripcion}" data-categoria="${element.categoria}" 
                        data-tipo="${element.tipo}" data-valoracion="${element.valoracion}">
                        <span class="ndc_selectgroup-button">
                            ELEGIR
                        </span>
                    </label>
                `;
                if (idsearch > 0) {
                    if (parseInt(element.id) === idsearch){
                        chck_prenda = `
                            <label class="selectgroup-item" style="margin-bottom:0;">
                                <input type="radio" class="selectgroup-input" 
                                name="prenda_item" value="${element.id}" 
                                data-descripcion="${element.descripcion}" data-categoria="${element.categoria}" 
                                data-tipo="${element.tipo}" data-valoracion="${element.valoracion}" 
                                checked>
                                <span class="ndc_selectgroup-button">
                                    ELEGIR
                                </span>
                            </label>
                        `;
                        prestamo_data.garantia = true;
                        prenda_data.id = parseInt(element.id);
                        prenda_data.descripcion = element.descripcion;
                        prenda_data.categoria = element.categoria;
                        prenda_data.tipo = element.tipo;
                        prenda_data.valoracion = element.valoracion;
                    }
                }

                html += `
                    <tr>
                        <td>${ element.descripcion }</td>
                        <td>${ element.categoria }</td>
                        <td>${ element.tipo }</td>
                        <td>${ element.valoracion }</td>
                        <td>
                            <a class="btn btn-sm btn-primary" href="${srv.url()}Prenda/detalle/${element.id}" target="_blank">
                                <i class="fe fe-layers"></i> Ver Detalle
                            </a>
                        </td>
                        <td class="text-center">
                            ${chck_prenda}
                        </td>
                    </tr>
                `;
            });
        }else{
            html = `
                <tr>
                    <td colspan="6" class="text-center">Prendas no encontradas</td>
                </tr>
            `;
        }
        html += `
            <tr>
                <td colspan="5" class="text-center text-primary font-weight-bold">SIN GARANTIA</td>
                <td class="text-center">
                    <label class="selectgroup-item" style="margin-bottom:0;">
                        <input type="radio" class="selectgroup-input" name="prenda_item" data-garantia="NO">
                        <span class="ndc_selectgroup-button">
                            ELEGIR
                        </span>
                    </label>
                </td>
            </tr>
        `;
        $('#caption_cliente').slideUp('fast');
        app.render('tdata_prenda', html);
    }

    function reset_data_prendas(){
        let files = document.getElementsByName('prenda_item');
        files.forEach(element => {
            element.cheked = false;
        })
    }

    async function get_tarifa(){
        try {
            app.preload('open', 'Obteniendo tarifa..');
            var req = await fetch(srv.url()+'Tarifa/service_calcular_tarifa', {
                method: 'POST',
                body: JSON.stringify({
                    'monto': prestamo_data.monto,
                    'cargo': prestamo_data.cargo
                }),
                headers: {
                    'Content-type': 'application/json; charset=UTF-8'
                }
            });
            var res = await req.json();
            app.preload('close');
            return res;
        } catch (error) {
            return [];
        }
    }

    async function procesar_tarifa(){
        var data = await get_tarifa();
        if (data) {
            prestamo_data.interes = data.interes;
            prestamo_data.penalidad = data.penalidad;
            prestamo_data.tipo = data.tipo;
            prestamo_data.cuota = data.cuota;
            prestamo_data.mora = data.mora;
        }
    }

})();
