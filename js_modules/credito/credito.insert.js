(function(d,w){
    const DEFAULT_PRENDA_TIPO = 'ELECTRO';
    let app;
    let mdl_credito, mdl_cliente, mdl_prenda, mdl_tprenda, mdl_categoria, mdl_creditoapi;
    let wizard = {
        step: 1,        
    }
    let modal = {
        cliente: {
            open: () => { $('#modal_cliente').modal('show'); },
            close: () => { $('#modal_cliente').modal('hide'); }
        },
        prenda: {
            open: () => { $('#modal_prenda').modal('show'); },
            close: () => { $('#modal_prenda').modal('hide'); }
        },
    }
    let form = {
        cliente: d.getElementById('form_cliente'),
        prenda: d.getElementById('form_prenda')
    }
    let loader = {
        cliente: {
            open: () => {
                d.getElementById('modal_cliente_loader').classList.remove('d-none'); 
            },
            close: () => {
                d.getElementById('modal_cliente_loader').classList.add('d-none'); 
            }
        },
        categoria: {
            open: () => {
                d.getElementById('categoria_loader').classList.remove('d-none'); 
            },
            close: () => {
                d.getElementById('categoria_loader').classList.add('d-none'); 
            }
        },
        prenda_card: {
            area: document.getElementById('prenda_dataset'),
            open(){
                this.area.innerHTML = '<div class="spinner-v1-mini"></div> Cargando';
            },
            close(){
                this.area.innerHTML = '';
            }
        }
    }
    let save_button = {
        cliente: {
            enable: ()=>{
                d.getElementById('btn_cliente_save').disabled = false;
            },
            disable: ()=>{
                d.getElementById('btn_cliente_save').disabled = true;
            }
        },
        prenda: {
            enable: ()=>{
                d.getElementById('btn_prenda_save').disabled = false;
            },
            disable: ()=>{
                d.getElementById('btn_prenda_save').disabled = true;
            }
        },
        prestamo: {
            enable: ()=>{
                d.getElementById('wiz-btn-save').disabled = false;
            },
            disable: ()=>{
                d.getElementById('wiz-btn-save').disabled = true;
            }
        }
    }
    let cliente_data = {
        id: 0,
        nombre: '',
        documento: '',
        distrito: '',
        bloqueo: ''
    }

    /* 
    el atributo 'garantia' indica si la solicitud de requerir la garantia
    esta activo o no 
    */
    let prenda_data = {
        id: 0,
        descripcion: 'SIN GARANTIA',
        categoria: '',
        valoracion: 0,
        subdata: '',
        garantia: true
    }
    let prestamo_data = {
        monto: 0,
        periodo: 'SEMANAL',
        ciclo: 'SEMANAL',
        prorroga: 35,
        cargo: 0,
        tipo_tasa: '',
        cuota: 0,
        interes: 0,
        mora: 0,
        tasa: 0,
        penalidad: 0
    }

    d.addEventListener('DOMContentLoaded', async function(){
        app = new App();
        mdl_credito = new Credito();
        mdl_cliente = new Cliente();
        mdl_prenda = new Prenda();
        mdl_tprenda = new TipoPrenda();
        mdl_categoria = new Categoria();
        mdl_creditoapi = new CreditoApi();

        app.inputMoney('dp_monto', (nval)=>{
            prestamo_data.monto = nval;
        });

        await cliente_pre_loading();
        await wizard_render();
    });

    // C L I C K
    d.addEventListener('click', async event => {
        let ev = event.target;
        let wzsp = wizard.step; 
        
        if (ev.id === 'wiz-btn-next'){
            let val = await wizard_validate(wzsp+1);
            if (val != true) return;
            wizard.step = ( wzsp+1 <= 4) ? wzsp + 1 : wzsp;
            wizard_render()
        }
        if (ev.id === 'wiz-btn-prev'){
            // if (!wizard_validate(wzsp-1)) return;
            wizard.step = ( wzsp-1 >= 1) ? wzsp - 1 : wzsp; 
            wizard_render()
        }

        if (ev.id === 'btn_cliente_nuevo') {
            form.cliente.reset();
            loader.cliente.close();
            save_button.cliente.enable();
            modal.cliente.open();
        }

        if (ev.matches('[name=distrito-item]')) {
            let inp_ubigeo = d.getElementById('input_ubigeo');
            let inp_distrito = d.getElementById('input_distrito');
            let list = d.getElementById('distrito-search-list'); 
            if (ev.dataset.ubigeo) {
                inp_ubigeo.value = ev.dataset.ubigeo;
                inp_distrito.value = ev.dataset.name;
                list.innerHTML = '';
            } 
        }

        if (ev.id === 'btn_prenda_nuevo'){
            if (cliente_data.id) {
                prenda_prepare_save_form(); 
            }
        }

        if (ev.id === 'prestamo_edit' || ev.id === 'link__edit_adv') {
            Swal.fire({
                title: `
                    <span class="text-primary">Edicion Avanzada</span>
                `,
                icon: 'info',
                html:                `   
                    <div class="form-group text-left">
                        <div class="form-label">Tipo de Tasa:</div>
                        <select id="edt_tipo" class="form-control">
                            <option value="P" ${prestamo_data.tipo_tasa==='P' ? 'selected' : ''}>Porcentual</option>
                            <option value="F" ${prestamo_data.tipo_tasa==='F' ? 'selected' : ''}>Fijo</option>
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
                preConfirm: async() => {
                    let n_interes = d.getElementById('edt_interes').value; 
                    let n_tipo  = d.getElementById('edt_tipo').value;
                    let n_mora = d.getElementById('edt_mora').value; 
                    console.log(prestamo_data)
                    await tarifa_recalculo(n_interes, n_tipo, n_mora);
                    // await tarifa_calculo();
                    preview_render()
                }
            });
        } 

        if (ev.id === 'wiz-btn-save') {
            const aswer = await Swal.fire({
                type: 'question',
                title: 'Registro',
                text: 'Guardar los datos del credito?',
                showCancelButton: true,
                confirmButtonColor: 'rgb(46, 178, 101)',
                confirmButtonText: 'Si, guardar',
                cancelButtonColor: '#283845',
                cancelButtonText: 'Cancelar',
                allowOutsideClick: false
            }).then(async(result) => {
                if (result.value) {
                    credito_save_data();
                }
            });
        }

    });

    // C H A N G E 
    d.addEventListener('change', async event => {
        let ev = event.target;

        if (ev.name === 'cliente-item') {
            if (!ev.dataset.id) {
                not.Aviso('Error al seleccionar','',2000);
                return;
            }
            cliente_get_data(ev.dataset.id);            
        }

        if (ev.matches('[name=tipo_prenda]')) {
            if (ev.value.length) {
                if (app.empty(ev.value)) {
                    not.Aviso('Tipo no identificado','',2000);
                    return;
                }
                let tipo_id = await prenda_render_tipos({
                    id_default: ev.value
                });
                await prenda_render_categorias({tipo_id: tipo_id});
                let params = {
                    mode: 'NEW',
                    tipo: ev.dataset.nombre
                };
                prenda_render_form(params);
            }
        }

        if (ev.matches('[name=prenda-item]')) {
            try {     
                if (!ev.value) {
                    not.Aviso('Prenda no identificada');
                    prenda_get_all_disponibles();
                    return;
                }

                if (ev.value == 0 && ev.dataset.type == 'NO_GARANTIA') {
                    prenda_data.garantia = false;
                    prenda_data.id = 0;
                    prenda_data.descripcion = 'SIN GARANTIA';
                    prenda_data.categoria = '';
                    prenda_data.valoracion = '';
                    not.Dark({
                        title: 'Tipo de Garantia Definida',
                        time: 2000
                    });
                    return;
                }

                let prenda_verify = await prenda_get_data(ev.value);
                if (app.empty(prenda_verify)) {
                    not.Aviso('Prenda no encontrada');
                    prenda_get_all_disponibles();
                    return;
                }
                
                prenda_data.garantia = true;
                prenda_data.id = prenda_verify.id;
                prenda_data.descripcion = prenda_verify.descripcion;
                prenda_data.categoria = prenda_verify.categoria_nombre;
                prenda_data.valoracion = prenda_verify.valoracion;
                let subdata = prenda_process_subdata(prenda_verify)
                prenda_data.subdata = subdata;
                not.Dark({
                    title: 'Prenda Seleccionada',
                    time: 2000
                });
                console.table(prenda_data)

            } catch (error) {
                console.log('[prenda][seleccion] '+error.message);
            }
        }

        if (ev.matches('[name=ciclo-item]')) {
            prestamo_data.ciclo = ev.value;
            prestamo_data.periodo = ev.value;
            // prestamo_data.periodo = (ev.value == '30') ? 'MENSUAL' : 'SEMANAL';
            let inputProrroga = d.getElementById('dp_prorroga');
            inputProrroga.value = ev.value === 'MENSUAL' ? 1 : 30;
        }
    });

    // I N P U T
    d.addEventListener('input', event => {
        let ev = event.target;
        if (ev.matches('[name=valoracion]')) {
            d.getElementById('valora-range-label').innerHTML = ev.value;
        }        
        if (ev.id === 'prendau_placa') {
            let raw_value = ev.value.replace('-','');
            if (raw_value.length >= 4) {
                let text_1 = raw_value.substr(0,3);
                let text_2 = raw_value.substr(3,raw_value.length);
                let fulltext = `${text_1}-${text_2}`;
                ev.value = fulltext;
            }
        }
    }); 

    // K E Y U P
    d.addEventListener('keyup', async event => {
        let ev = event.target;

        if (ev.matches('[name=distrito]')) {
            let data = await distrito_search_data(ev.value);
            distrito_render_data(data);
        }  
        if (ev.id === 'input-cliente-search') {
            if (ev.value.length > 0){
                cliente_search({
                    search_nombre_like: ev.value,
                    limit: 6
                });
            }
        }
        if (ev.id === 'prendau_placaa') {
            if (ev.value.length === 4) {
                // let raw_value = ev.value.replace('-','');
                return false;
            }
            // console.log('L1:',raw_value)
            // if (raw_value.length === 3){
            //     ev.value += "-";
            // }
            // console.log('L2:',raw_value)
            // if (raw_value.length >= 6){
            //     event.preventDefault()
            // }
        }
    });

    // S U B M I T
    d.addEventListener('submit', event => {
        let ev = event.target;
        event.preventDefault();

        if (ev.id === 'form_cliente') {
            let data = new FormData(ev);
            cliente_save(data);
        }

        if (ev.id === 'form_prenda') {
            let data = new FormData(ev);
            data.append('idcliente', cliente_data.id);
            prenda_save(data);
        }
    });




    // W I Z A R D
    async function wizard_render(){
        try {
            d.getElementById('modulo-loader').style.display = 'flex';
            let wz_cliente = d.getElementById('wiz-cliente');
            let wz_prenda = d.getElementById('wiz-prenda');
            let wz_prestamo = d.getElementById('wiz-prestamo');
            let wz_complete = d.getElementById('wiz-complete');
            let btn_prev = d.getElementById('wiz-btn-prev');
            let btn_next = d.getElementById('wiz-btn-next');
            let btn_save = d.getElementById('wiz-btn-save');
            
            
            switch (wizard.step) {
                case 1:
                    wz_cliente.classList.remove('d-none');
                    wz_prenda.classList.add('d-none');
                    wz_prestamo.classList.add('d-none');
                    wz_complete.classList.add('d-none');
                    
                    btn_next.classList.remove('d-none');
                    btn_prev.classList.add('d-none');
                    btn_save.classList.add('d-none');
                    break;
            
                case 2:
                    wz_cliente.classList.add('d-none');
                    wz_prenda.classList.remove('d-none');
                    wz_prestamo.classList.add('d-none');
                    wz_complete.classList.add('d-none');
                    
                    btn_next.classList.remove('d-none');
                    btn_prev.classList.remove('d-none');
                    btn_save.classList.add('d-none');

                    prenda_get_all_disponibles();
                    break;
                
                case 3:
                    wz_cliente.classList.add('d-none');
                    wz_prenda.classList.add('d-none');
                    wz_prestamo.classList.remove('d-none');
                    wz_complete.classList.add('d-none');

                    btn_next.classList.remove('d-none');
                    btn_prev.classList.remove('d-none');
                    btn_save.classList.add('d-none');
                    break;

                case 4:                
                    if (prestamo_data.monto <= 0) {
                        return;
                    }
                    // await tarifa_calculo();
                    let TarifaEval = await tarifa_calculo();
                    if (!TarifaEval){                        
                        throw new Error('Error al procesar');
                    }
                    wz_cliente.classList.add('d-none');
                    wz_prenda.classList.add('d-none');
                    wz_prestamo.classList.add('d-none');
                    wz_complete.classList.remove('d-none');

                    btn_next.classList.add('d-none');
                    btn_prev.classList.remove('d-none');
                    btn_save.classList.remove('d-none');
                    break;
                
                case 5:
                    // credito_save_data();
                    break;
            }
            setTimeout(() => {
                d.getElementById('modulo-loader').style.display = 'none';
            }, 100);
            preview_render();
        } catch (error) {
            console.log('wizard: '+ error.message);
            d.getElementById('modulo-loader').style.display = 'none';
        }
    } 
    async function wizard_validate(step){
        let state = true;
        step = parseInt(step);

        if (step === 1) state = true;
        if (step === 2){
            if (app.empty(cliente_data.id)) {
                not.Aviso('Cliente no identificado','',2000);
                state = false;
            }
        }
        if (step === 3){
            if (app.empty(prenda_data.id) && prenda_data.garantia) { 
                const aswer = await Swal.fire({
                    type: 'question',
                    title: 'Continuar?',
                    text: 'No se eligio una garantia valida ¿continuar?',
                    showCancelButton: true,
                    confirmButtonColor: '#325dff',
                    confirmButtonText: 'Si, continuar',
                    cancelButtonColor: '#283845',
                    cancelButtonText: 'Cancelar',
                    allowOutsideClick: false
                }).then(async(result) => {
                    if (result.value) {
                        state = true;
                        prenda_data.id = 0;
                        prenda_data.descripcion = 'SIN GARANTIA';
                        prenda_data.garantia = false;
                    }else {
                        state = false;
                    }
                });
            }
        }
        if (step === 4) {
            app.render('monto_feed', '');
            let monto = d.getElementById('dp_monto');
            if (monto.value.length <= 0) {
                app.render('monto_feed', 'El monto es requerido');
                state = false;
            }
            state = prestamo_validar();
        }
        return state;
    }  
    function preview_render(){
        let pvhtml = '';
        if (wizard.step >= 2) {            
            pvhtml += `
            <div id="prev_cliente" class="modulo-preview-item mb-2">
                <div class="modulo-preview-group">
                    <div class="modulo-preview-icon">
                        <i class="fe fe-users"></i>
                    </div>
                    <div id="prev_cliente_content" class="modulo-preview-content">
                        <div class="modulo-preview-tag">
                            ${cliente_data.nombre}
                        </div>
                        <div class="modulo-preview-tag">
                            [DNI: ${cliente_data.documento}] 
                        </div>
                        ${cliente_data.bloqueo}
                    </div>
                </div>
                <div class="modulo-preview-options">                    
                    <div id="prev_cliente_check" class="modulo-preview-check actived">
                        <i class="fe fe-check"></i>
                    </div>                       
                </div>
            </div>
            `;
        }
        if (wizard.step >= 3) {
            let subdata = (prenda_data.id > 0) ? `
                <div class="modulo-preview-tag">
                    Categoria: 
                    ${prenda_data.categoria}
                </div>
                <div class="modulo-preview-tag">
                    Valoracion: 
                    ${prenda_data.valoracion}
                </div> 
                <div class="modulo-preview-tag">
                    ${prenda_data.subdata}
                </div>
            ` : '';
            pvhtml += `
            <div id="prev_prenda" class="modulo-preview-item mb-2">
                <div class="modulo-preview-group">
                    <div class="modulo-preview-icon">
                        <i class="fe fe-tv"></i>
                    </div>
                    <div id="prev_prenda_content" class="modulo-preview-content">
                        <div class="modulo-preview-tag">
                            ${prenda_data.descripcion}
                        </div> 
                        ${subdata}                       
                    </div>
                </div>
                <div class="modulo-options">
                    <div id="prev_prenda_check" class="modulo-preview-check actived">
                        <i class="fe fe-check"></i>
                    </div>
                </div>
            </div>
            `;
        }
        if (wizard.step >= 4) {
            
            
        }
        if (wizard.step === 4) {
            prestamo_data.prorroga = d.getElementById('dp_prorroga').value;
            render_minipreview();
            app.render('modulo-preview', '');
        }else {
            app.render('modulo-preview', pvhtml);
            d.getElementById('modulo-preview').classList.add('opened');
        }
    }  
    function render_minipreview(){
        let html_res = '';
        html_res += `
            <li>
                <div class="rs-title">1. Cliente</div>
                <p>
                    <b>Nombres y Apellidos:</b>
                    <a class="text-dark" href="${srv.url()}Cliente/detalle/${cliente_data.id}" target="_blank">
                        ${cliente_data.nombre}
                    </a>
                </p>
                <p>
                    <b>Documento:</b>
                    <span>${cliente_data.documento}</span>
                </p>
                <p>
                    <b>Direccion:</b>
                    <span>${cliente_data.distrito}</span>
                </p>
            </li>
        `;        
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
                    <td>${prestamo_data.tipo_tasa === 'P' ? prestamo_data.interes+'%' : 'S/ '+prestamo_data.interes}</td>
                </tr>
                <tr>
                    <td>Tipo:</td>
                    <td>${prestamo_data.tipo_tasa === 'P' ? 'Porcentual' : 'Fijo'}</td>
                </tr>
                <tr>
                    <td>Pago ${ prestamo_data.periodo }:</td>
                    <td>S/ ${prestamo_data.cuota}</td>
                </tr>
                <tr>
                    <td>Mora:</td>
                    <td>S/ ${prestamo_data.mora}</td>
                </tr>
                <tr>
                    <td>Prorroga:</td>
                    <td>${prestamo_data.prorroga} dias</td>
                </tr>
                </table>
            </li>
        `;

        app.render('minipreview', html_res);
    }


    // C L I E N T E
    async function cliente_search(search_setts){
        try {
            app.render('cliente_dataset', `
                <div class="enc2454-dataset-blank">
                    <div class="d-flex justify-center align-items-center">
                        Cargando&nbsp;
                        <div class="spinner-v1-mini"></div>
                    </div>
                </div>
            `);
            let req = await mdl_cliente.get_all(search_setts); 
            if (!req || req.code != 'OK') {
                let emssg = (req.code === 'ERR') ? req.message : 'Error de ejecucion';
                throw emssg;
            }
            let data = req.result;
            let html = '';
            if (data.length > 0) {
                data.forEach(e => {
                    let puntua_label = (e.puntuacion) ? e.puntuacion : '-';
                    let blocked_label = '';
                    let selected = (e.checked) ? 'checked' : '';
                    if (e.bloqueo_atencion == 'SI'){
                        blocked_label = `
                        <div class="enc2454-label-blocked py-1">
                            [ListaNegra] ${e.bloqueo_motivo}
                        </div>
                        `; 
                    }
                    let prendas_cant = parseInt(e.prendas_cantidad, 10) || 0;
                    let prendas_cant_format = prendas_cant > 0 && prendas_cant < 10 
                        ? '0'+prendas_cant 
                        : prendas_cant;

                    html += `
                        <div class="enc2454-icheck">
                            <input type="radio" name="cliente-item" id="chk${e.id}" data-id="${e.id}" data-nombre="${e.nombres} ${e.paterno} ${e.materno}" data-dir="${e.direccion}, ${e.distrito}" data-doc="${e.documento}" ${selected}>
                            <label for="chk${e.id}">                        
                                <div class="enc1212-conducta-tag enc1212-conducta-tag-${e.puntuacion}">
                                    ${(puntua_label) ? puntua_label : '-'}
                                </div>
                                <div class="enc2454-icheck-data">
                                    <div class="enc2454-icheck-title">
                                        ${e.nombres} ${e.paterno} ${e.materno} 
                                    </div>
                                    <div class="enc2454-icheck-subtitle">
                                    [${e.tipo_documento}: ${e.documento}] - [Prendas: ${prendas_cant_format}] - [Telf.: ${e.telefono}] 
                                    </div>
                                    ${blocked_label}
                                </div>
                                <div class="enc2454-icheck-control">
                                    <i class="fe fe-check enc2454-icheck-icon"></i>
                                    <i class="fe fe-plus enc2454-icheck-icon"></i>
                                </div>
                            </label>                        
                        </div>
                    `;
                })
            } else{
                html = `
                <div class="enc2454-dataset-blank">
                    [ Sin Datos ] 
                </div>
                `;
            }

            app.render('cliente_dataset', html);
        } catch (error) {
            app.preload('close'); 
            let mssg = (error.name) ? 'Error de ejecucion' : error;
            // not.Aviso(mssg, '', 2500); 
            app.render('cliente_dataset', `
                <div class="enc2454-dataset-blank">
                    [ ${mssg} ] 
                </div>
            `);
        }
    }
    async function cliente_pre_loading(){
        try {
            if (app.empty(d.getElementById('idcliente_base').value)) return; 
            let ide = d.getElementById('idcliente_base').value;
            if (await cliente_get_data(ide)) {
                wizard.step = 2;
                wizard_render();
            }
        } catch (error) {
            console.log(`[Cliente][Preload] `+error.message)
            not.Aviso('Error al seleccionar cliente xD', '', 2000); 
        }
    }
    async function cliente_get_data(id, setts = {}){
        try {
            let req = await mdl_cliente.get_data(id); 
            if (app.empty(req.result)) {
                not.Aviso('Datos del cliente no encontrados', '', 2000); 
                return;
            }
            let data = req.result;
            cliente_data.id = data.id;
            cliente_data.nombre = data.nombre_full,
            cliente_data.documento = data.documento;
            cliente_data.distrito = data.distrito;
            cliente_data.bloqueo = '';
            if (data.bloqueo_atencion == 'SI') {
                cliente_data.bloqueo = `
                    <div class="modulo-preview-tag modulo-preview-tag-red">
                        En Lista Negra: ${data.bloqueo_motivo}
                    </div>
                `;
            }
            not.Dark({
                title: 'Cliente Seleccionado',
                time: 2000
            });
            return true;
        } catch (error) {
            console.log('[cliente] '+error.message)
            let mssg = (error.name) ? 'Error de ejecucion' : error;
            not.Aviso(mssg, '', 3000); 
        }
    }
    async function distrito_search_data(_value){
        const req = await fetch(srv.url() + 'Ubigeo/service_search',{
            method: 'POST',
            body: JSON.stringify({
                search: _value
            })
        });
        const res = await req.json();
        return res;
    }
    async function distrito_render_data(data){        
        let list = d.getElementById('distrito-search-list');
        list.innerHTML = '';

        data.forEach((e, ind) => {
            if (ind <= 2) {
                let item = d.createElement('li');
                item.setAttribute('class', 'tooltip-item');
                item.setAttribute('name', 'distrito-item');
                item.dataset.ubigeo = e.id;
                item.dataset.name = `${e.distrito}, ${e.departamento}`;
                item.innerHTML = `
                    <div class="tooltip-item-title">${e.distrito}</div> 
                    <div class="tooltip-item-subtitle">${e.departamento}</div>  
                `;
                list.appendChild(item);
            }
        });
    }
    async function cliente_save(data){
        try {
            app.preload('open','procesando..');
            save_button.cliente.disable();
            app.rendalert('modal_cliente_rendalert', {clean: true});
            loader.cliente.open();

            let save = await mdl_cliente.save(data);
            app.preload('close'); 
            loader.cliente.close();
            if (app.empty(save)) {
                app.rendalert('modal_cliente_rendalert',{
                    message: 'Error de solicitud',
                    type: 'error'
                });
                save_button.cliente.enable();
            }
            if (save.success && save.id) {
                not.Ok(save.success, '', 2000);
                form.cliente.reset();
                modal.cliente.close();
                
                if (await cliente_get_data(save.id)) {
                    wizard.step = 2;
                    wizard_render();
                }
                return;
            }
            if (save.errors){
                feedback(save.errors);
            }
            if(save.warning) {
                app.rendalert('modal_cliente_rendalert',{
                    message: save.warning,
                    type: 'warning'
                });
                save_button.cliente.enable();
                return;
            }            
            if(save.error) {
                app.rendalert('modal_cliente_rendalert',{
                    message: save.error,
                    type: 'error'
                });
                save_button.cliente.enable();
                return;
            }

            throw 'Error Desconocido';            
        } catch (error) {
            app.preload('close'); 
            console.log('[Cliente][registro] '+error.message)
            not.Error('[Cliente][registro] Error al procesar', '', 2500); 
            save_button.cliente.enable();
            loader.cliente.close();
        }
    }



    // P R E N D A
    async function prenda_get_all_disponibles(setts = {}){
        try {
            // let time_inicio = await performance.now();
            // console.time();
            loader.prenda_card.open();
            let req = await mdl_prenda.get_by_cliente(cliente_data.id, {
                search_estado: ['R','D'],
                order_by: 'creacion',
                order_type: 'DESC'
            });
            if (app.empty(req)) {
                not.Aviso('Error al solicitar datos','',2000);
                return;
            }
            if (app.empty(req.result)) {
                app.render('prenda_dataset', `
                    <div class="d-flex justify-center">
                        [ Sin Datos de Prendas ] 
                    </div>
                    <div class="prenda-item">
                    <input id="NG" type="radio" name="prenda-item" value="0" data-type="NO_GARANTIA">
                        <label for="NG" data-valoracion="" data-descripcion="" class="no-garantia-item text-primary">
                            Sin Garantia 
                            <i class="fe fe-maximize"></i>
                        </label>
                    </div>
                `);
                return;
            } 

            let data = req.result;
            let html = '';
            data.forEach(e => {
                let marked_attr = '';
                // setts.id_default && setts.id_default == e.id
                if (!app.empty(prenda_data.id) && prenda_data.id == e.id) {
                    marked_attr = 'checked';
                    prenda_data.id = e.id;
                    prenda_data.descripcion = e.descripcion;
                    prenda_data.valoracion = e.valoracion;
                    prenda_data.categoria = e.categoria_nombre;
                    prenda_data.garantia = true;
                }
                let ult_credito_id    = e.ultimo_credito_id;
                let ult_credito_serie = e.ultimo_credito_serie;
                let ult_credito_monto = e.ultimo_credito_monto;
                // let ult_credito_link  = ult_credito_id 
                //     ? `<a href="${app.URL('credito/detalle'+ult_credito_id)}">
                //             ${ult_credito_serie}
                //         </a>
                //     ` : 'Sin datos.'; 
                let ult_credito_link  = ult_credito_id 
                    ?  `<span class="font-weight-bold">${ult_credito_serie} [S/ ${ult_credito_monto}]</span>
                    ` : 'Sin datos.'; 
    
                html += `
                    <div class="prenda-item">
                        <input id="${e.id}" type="radio" name="prenda-item" value="${e.id}" data-descripcion="${e.descripcion}" data-categoria="${e.categoria_nombre}" data-valoracion="${e.valoracion}" ${marked_attr}>
                        <label for="${e.id}">
                            <div class="pheader">
                                <h4 class="ptitle">${e.descripcion}</h4>
                                ${e.estado_tag}
                            </div>
                            <div class="pcontent">
                                <p>Categoria: ${e.categoria_nombre} | Valoracion: ${e.valoracion} | ${ prenda_process_subdata (e,'|')}</p>
                                <p>
                                    Ultimo Credito: ${ult_credito_link}
                                </p>
                            </div>
                        </label>
                    </div>
                `;
            });
            html += `
                <div class="prenda-item">
                    <input id="NG" type="radio" name="prenda-item" value="0" data-type="NO_GARANTIA">
                    <label for="NG" data-valoracion="" data-descripcion="" class="no-garantia-item text-primary">
                        Sin Garantia 
                        <i class="fe fe-maximize"></i>
                    </label>
                </div>
            `;
            loader.prenda_card.close();
            app.render('prenda_dataset', html);
            // let time_final = await performance.now();
            // let time_result = time_final - time_inicio;
            // d.getElementById('prenda_timer').textContent = time_result.toFixed(4);
            // console.timeEnd();
        } catch (error) {
            loader.prenda_card.close();
            console.log('[prenda][consulta] '+error.message)
            app.render('prenda_dataset', `
                <div class="d-flex justify-center">
                    [ Sin Datos ] 
                </div>
            `);
        }
    }
    async function prenda_get_data(id){
        try {
            let data = await mdl_prenda.get_data(id);
            if (!app.empty(data)) {
                return data;
            }
            return;
        } catch (error) {
            not.Aviso('[prenda] Error al obtener datos','',2000);
            return;
        }
    }
    function prenda_process_subdata(data, divider = ','){
        let subdata = '';
        switch (data.tipo_nombre) {
            case 'ELECTRO':
                subdata = `
                    Serie: ${data.serial} ${divider}
                    Marca: ${data.marca} ${divider} 
                    Modelo: ${data.modelo}
                `;
                break;

            case 'JOYA':
                subdata = `
                    Quilate: ${data.quilate} ${divider} 
                    Peso: ${data.peso}
                `;
                break;

            case 'VEHICULO':
                subdata = `
                    Placa: ${data.placa} ${divider} 
                    Marca: ${data.marca} ${divider}  
                    Modelo: ${data.modelo}
                `;
                break;

            case 'PREDIO':
                
                break;
        }
        return subdata;
    }
    async function prenda_prepare_save_form(){
        try {
            app.preload('open', 'Cargando datos..'); 
            app.rendalert('modal_prenda_rendalert', {clean: true});
            form.prenda.reset();
            save_button.prenda.enable();
            
            let tipo_id = await prenda_render_tipos({
                name_default: DEFAULT_PRENDA_TIPO
            });
            await prenda_render_categorias({
                tipo_id: tipo_id
            });
            await prenda_render_form({
                mode: 'NEW',
                tipo: DEFAULT_PRENDA_TIPO
            });
            app.preload('close');
            $('#modal_prenda').modal('show');
        } catch (error) {
            console.log('[prenda] '+error.message)
            app.preload('close');
            not.Aviso('[prenda] Error al procesar','',2000);
            $('#modal_prenda').modal('hide');
        } 
    } 
    async function prenda_render_form(config = {}){
        try {
            let request;
            request = await mdl_prenda.get_form_by_tipo(config.tipo);
            if (app.empty(request) || request.code != 'OK') {
                not.Aviso('[prenda] Error al obtener formulario','',2000);
                $('#modal_prenda').modal('hide');
                return;
            }
            let html = request.result;
            d.getElementById('prenda_form_content').innerHTML = html;
        } catch (error) {
            console.log(error)
            not.Aviso('[prenda] Error al procesar formulario','',2000);
            $('#modal_prenda').modal('hide');
        }
    }
    async function prenda_render_tipos(config = {}){
        try {
            let html = '';
            app.render('tprenda_render_area',`
                <div class="d-flex justify-center">
                    <div class="spinner-v1"></div>
                </div>
            `);
            let data = await mdl_tprenda.get_all();  
            if (app.empty(data)){
                app.render('tprenda_render_area',`
                    <div class="text-center">Sin Datos</div>
                `);
                return;
            }
            let id_response = 0;
            data.forEach(e => {  
                let chkd = '';              
                if (config.id_default > 0 || config.name_default) {
                    
                    let val_a = (config.id_default) ? config.id_default : config.name_default;
                    let val_b = (config.id_default) ? e.id : e.nombre;
                    if (val_a === val_b) {
                        chkd = 'checked';
                        id_response = e.id;
                    }
                }
                html += `
                <label class="custom-control custom-radio custom-control-inline">
                    <input type="radio" data-ref="${e.nombre}" data-nombre="${e.nombre}" class="custom-control-input" name="tipo_prenda" value="${e.id}" ${chkd}>
                    <span class="custom-control-label">${e.nombre}</span>
                </label>
                `;
            });
            app.render('tprenda_render_area', html);
            return id_response;
        } catch (error) {
            not.Aviso('[prenda tipos] Error al procesar','',2000);
            app.render('tprenda_render_area','[Error]')
            console.log(error)
        }
    }
    async function prenda_render_categorias(config){
        try {
            loader.categoria.open();           
            let request = await mdl_categoria.get_by_tipo(config.tipo_id);
            if (app.empty(request)){
                app.render('categoria_list','<option value="">-Sin Datos-</option>');
                return;
            }
            let data = request;
            let html = '<option value="">-SELECCIONAR-</option>';
            data.forEach(e => {
                let chkd = (e.id == config.categoria_default) ? 'selected' : '';

                html += `
                    <option value="${e.id}" ${chkd}>${e.nombre}</option>
                `;
            });
            app.render('categoria_list',html);
            loader.categoria.close(); 

        } catch (error) {
            loader.categoria.close(); 
            console.log(error)
        }
    }
    async function prenda_save(data){
        try {
            app.preload('open','procesando..');
            app.rendalert('modal_prenda_rendalert', {clean: true});
            save_button.prenda.disable();

            let save = await mdl_prenda.save(data);
            app.preload('close'); 
            if (save.success && save.id) {
                not.Ok(save.success, '', 2000);
                prenda_data.id = save.id;
                form.prenda.reset();
                modal.prenda.close();
                await prenda_get_all_disponibles();
                if (!app.empty(prenda_data.id)) {
                    prenda_data.garantia = true;
                    wizard.step = 3;
                    wizard_render();
                }
                return;
            }
            if(save.error) {
                app.rendalert('modal_prenda_rendalert',{
                    message: save.error,
                    type: 'error'
                });
                save_button.prenda.enable();
                return;
            }
            if (save.warning) {
                app.rendalert('modal_prenda_rendalert',{
                    message: save.warning,
                    type: 'warning'
                });
                save_button.prenda.enable();
                return;
            }    
            throw 'Error Desconocido';  
        } catch (error) {
            console.log('[prenda][save] '+error.message)
            app.preload('close');
            let mssg = (error.name) ? 'Error de ejecucion' : error;
            save_button.prenda.enable();
            not.Aviso(mssg, '', 2500); 
        }
    }


    // P R E S T A M O
    function prestamo_validar(){
        let rsp = true;
        app.render('monto_feed', '');
        app.render('prorroga_feed', '');

        if (!prestamo_data.monto || isNaN(prestamo_data.monto)) {
            app.render('monto_feed', 'Monto invalido!');
            rsp = false;
        }
        if (!prestamo_data.prorroga || isNaN(prestamo_data.prorroga)) {
            app.render('prorroga_feed', 'Dato invalido!');
            rsp = false;
        }
        return rsp;
    }    
    async function credito_save_data() {
        try {
            app.preload('open','Registrando credito..');    
            app.rendalert('modal_view_rendalert', {clean: true});        
            save_button.prestamo.disable();

            let data = new FormData();
            data.append('idprenda', prenda_data.id);
            data.append('idcliente', cliente_data.id);
            data.append('monto', prestamo_data.monto);
            data.append('pago_cuota', prestamo_data.cuota);
            data.append('mora', prestamo_data.mora);
            data.append('penalidad', prestamo_data.mora);
            data.append('interes', prestamo_data.interes);
            data.append('tasa', prestamo_data.tasa);
            data.append('tipo_tasa', prestamo_data.tipo_tasa);
            data.append('ciclo_pago', prestamo_data.ciclo);
            data.append('prorroga', prestamo_data.prorroga);
            data.append('cargo', prestamo_data.cargo);
            data.append('periodo', prestamo_data.periodo);

            let save = await mdl_creditoapi.save(data);
            app.preload('close');

            if (save.success) {
                not.Ok(save.success, '', 2000);
                setTimeout(() => {
                    window.location.href = srv.url()+'Credito/detalle/'+save.id;
                }, 500);
            }
            if (save.warning) {                
                let warning_conf = {
                    message: save.warning,
                    type: 'warning'
                }
                if (save.validation_errors) {
                    let sub_m = '';
                    let val_errs = save.validation_errors.split('\n');
                    val_errs.forEach(element => {
                        if (!element) return;
                        let ele = element.replace('<p>','').replace('</p>','');
                        sub_m += `<li>${ele}</li>`;
                    });
                    warning_conf.sub_message = `<ul>${sub_m}</ul>`;
                }
                save_button.prestamo.enable();
                app.rendalert('modal_view_rendalert', warning_conf);
                return;
            }
            if (save.error) {                
                app.rendalert('modal_view_rendalert',{
                    message: save.error,
                    type: 'error'
                });
                save_button.prestamo.enable();
                return;
            }
            throw 'Error desconocido';
            
        } catch (error) {
            app.preload('close');
            console.log(error)
            save_button.prestamo.enable();
        }
    }
    async function tarifa_calculo(){
        let data = new FormData()
        data.append('monto', prestamo_data.monto)
        data.append('cargo', prestamo_data.cargo)
        // api_tarifa_evaluation
        // service_calcular_tarifa_v2
        var req = await fetch(srv.url()+'Tarifa/api_tarifa_evaluation', {
            method: 'POST',
            body: data
        });
        var res = await req.json();
        if (res && res.ok) {
            prestamo_data.interes = res.interes;
            prestamo_data.penalidad = res.penalidad;
            prestamo_data.tipo_tasa = res.tipo;
            prestamo_data.cuota = res.cuota;
            prestamo_data.mora = res.mora;

            return true;
        }else {
            let mssg = res.error || 'Error: calculo de tarifa';
            not.Aviso(mssg, '')
            return;
        }
    }
    function tarifa_recalculo(interes,tipo,mora){
        try {
            if (Number(interes) && interes > 0) {
                prestamo_data.interes = interes;
            }
            if (['P','F'].includes(tipo)) {
                prestamo_data.tipo_tasa = tipo;
            }
            if (Number(mora) && mora > 0) {
                prestamo_data.mora = mora;
            }
    
            if (prestamo_data.tipo_tasa === 'P') {
                let px = ((prestamo_data.monto * prestamo_data.interes) / 100);
                prestamo_data.cuota = app.Rx_round(px); 
            } else if(prestamo_data.tipo_tasa === 'F'){
                prestamo_data.cuota = prestamo_data.interes;
            }
            render_minipreview();
            console.log(prestamo_data)
        } catch (error) {
            
        }
    }
    function feedback(error){
        if(error.documento) app.render('feed_documento', error.documento); else app.render('feed_documento', '');
        if(error.tipo_documento) app.render('feed_tipodoc', error.tipo_documento); else app.render('feed_tipodoc', '');
        if(error.nombre) app.render('feed_nombre', error.nombre); else app.render('feed_nombre', '');
        if(error.paterno) app.render('feed_paterno', error.paterno); else app.render('feed_paterno', '');
        if(error.materno) app.render('feed_materno', error.materno); else app.render('feed_materno', '');
        if(error.ubigeo) app.render('feed_ubigeo', error.ubigeo); else app.render('feed_ubigeo', '');
        if(error.direccion) app.render('feed_direccion', error.direccion); else app.render('feed_direccion', '');
        if(error.telefono) app.render('feed_telefono', error.telefono); else app.render('feed_telefono', '');
        if(error.email) app.render('feed_email', error.email); else app.render('feed_email', '');
    }

    
})(document,window);