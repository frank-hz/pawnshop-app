(function(d){
    const DEFAULT_TIPO = 'ELECTRO';
    let app;
    let mdl_credito, mdl_cliente, mdl_prenda, mdl_tprenda, mdl_categoria;
    let wizard = {
        step: 1,        
    }
    let cliente_data = {
        id: 0,
        nombre: '',
        documento: '',
        distrito: ''
    }
    let prenda_data = {
        id: 0,
        descripcion: 'SIN GARANTIA',
        categoria: '',
        valoracion: 0
    }
    let prestamo_data = {
        monto: 0,
        periodo: 'SEMANAL',
        ciclo: 7,
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

        app.inputMoney('dp_monto', (nval)=>{
            prestamo_data.monto = nval;
        });

        await wizard_render();
        await cliente_preload();
    });
    d.addEventListener('click', async function(event) {
        let ev = event.target;
        let wzsp = wizard.step; 

        if (ev.id === 'btn_test') {
            let saved = await mdl_credito.save(new FormData());
            console.log(saved)
        }        
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
            $('#modal_cliente').modal('show');
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
                try {
                    await prenda_render_tipos({name: DEFAULT_TIPO});
                    let form = d.getElementById('form_prenda');       
                    prenda_render_form(DEFAULT_TIPO);
                    $('#modal_prenda').modal('show');
                } catch (error) {
                    let mssg = (error.name) ? error.name : error;
                    console.log(`[Prenda][Nuevo] ${error}`)
                } 
            }
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
                    // console.log('terminando..',n_interes,n_tipo,n_mora)
                    console.log(prestamo_data)
                    await tarifa_recalculo(n_interes, n_tipo, n_mora);
                    // await tarifa_calculo();
                    preview_render()
                }
            });
        } 
    });
    d.addEventListener('keyup', async function(event){
        let ev = event.target;
        
        if (ev.id === 'input-cliente-search') {
            if (ev.value.length > 0){
                cliente_search({
                    search_quick: ev.value,
                    limit: 5
                });
            }
        }
        if (ev.matches('[name=distrito]')) {
            if (ev.value.length > 0) distrito_search_data(ev.value);
        }
    });
    d.addEventListener('input', event => {
        let ev = event.target;
        if (ev.matches('[name=valoracion]')) {
            console.log(ev.value)
            d.getElementById('valora-range-label').innerHTML = ev.value;
        }
    });
    d.addEventListener('change', async event => {
        let ev = event.target;
        if (ev.name === 'cliente-item') {
            if (!ev.dataset.id) {
                not.Aviso('Error al seleccionar','',2000);
                return;
            }
            cliente_get_data(ev.dataset.id);            
        }
        if (ev.matches('[name=prenda-item]')) {       
            prenda_data.id = ev.value;
            prenda_data.descripcion = (ev.value > 0) ? ev.dataset.descripcion : 'SIN GARANTIA';
            prenda_data.categoria = (ev.value > 0) ? ev.dataset.categoria : '';
            prenda_data.valoracion = (ev.value > 0) ? ev.dataset.valoracion : 0;
            not.Dark({
                title: 'Prenda Seleccionada',
                time: 2000
            });
        }
        if (ev.matches('[name=tipo_prenda]')) {
            if (ev.value.length) {
                prenda_render_categorias(parseInt(ev.value))
                prenda_render_form(ev.dataset.ref);
            }
        }
        if (ev.id === 'dp_cargo_almacena') {
        }
        if (ev.matches('[name=ciclo-item]')) {
            prestamo_data.ciclo = ev.value;
            prestamo_data.periodo = (ev.value == '30') ? 'MENSUAL' : 'SEMANAL';
            // let backvalue = (ev.value == 7) ? 30 : 7;
            // console.log('current: '+ev.value)
            // console.log('back: '+backvalue)
            // let tag = (ev.value == 7) ? 'SEMANAL' : 'MENSUAL';
            // let tagrvt = (ev.value == 7) ? 'MENSUAL' : 'SEMANAL';
            // if (!confirm(`Cambiar ciclo de pago a ${tag}?`)){
            //     ev.value = backvalue;
            //     let items = d.getElementsByName('ciclo-item');
            //     items.forEach(item => {
            //         item.checked = false;
            //         if (item.value == backvalue) {
            //             console.log('checked default: '+item.value)
            //             item.checked = true;
            //         }
            //     })
            // }
           
            // let newvalor = (ev.checked) ? 'MENSUAL' : 'SEMANAL';
            // if (newvalor === 'MENSUAL') {
            //     if (confirm(`El credito pasara a de cobro semanal a mensual, ¿esta seguro de continuar?`)) {
            //         data_stash.ciclo = 'MENSUAL';
                    
            //     }
            // } else if(newvalor === 'SEMANAL'){
            //     if (confirm(`El credito pasara a de cobro mensual a semanal, ¿esta seguro de continuar?`)) {
            //         data_stash.ciclo = 'SEMANAL';
            //     }
            // }  
        }
    });
    d.addEventListener('submit', async event => {
        event.preventDefault();
        let ev = event.target;
        if (ev.id === 'form_cliente') {
            let data = new FormData(ev);            
            let save = await cliente_save_data(data);
            if (!save || save == undefined) {
                not.Aviso('Ocurrio un error al procesar', '', 2200);
                return;
            }
            d.getElementById('form_cliente').reset();
            $('#modal_cliente').modal('hide');

            if (await cliente_get_data(save.id)) {
                wizard.step = 2;
                wizard_render();
            }
        }
        if (ev.id === 'form_prenda') {
            try {
                app.preload('open','Registrando prenda..');  
                d.getElementById('btn_prenda_save').disabled = true;
                let data = new FormData(ev);
                data.append('idcliente', cliente_data.id);
                let save = await prenda_save(data);
                if (!save) {
                    not.Aviso('Ocurrio un error al procesar', '', 2200);
                    d.getElementById('btn_prenda_save').disabled = false;
                    return;
                }
                d.getElementById('form_prenda').reset();
                $('#modal_prenda').modal('hide');
                setTimeout(() => {
                    app.preload('close');
                }, 500);
                if (prenda_get_all({id_default: save.id}) && prenda_data.id > 0){
                    wizard.step = 3;
                    wizard_render();
                }
            } catch (error) {
                d.getElementById('btn_prenda_save').disabled = false;
                app.preload('close');
            }
        }
    });


    async function wizard_render(){
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

                prenda_get_all();
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
                await tarifa_calculo();
                wz_cliente.classList.add('d-none');
                wz_prenda.classList.add('d-none');
                wz_prestamo.classList.add('d-none');
                wz_complete.classList.remove('d-none');

                btn_next.classList.add('d-none');
                btn_prev.classList.remove('d-none');
                btn_save.classList.remove('d-none');
                break;
            
            case 5:
                credito_save_data();
                break;
        }
        setTimeout(() => {
            d.getElementById('modulo-loader').style.display = 'none';
        }, 100);
        preview_render();
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
    async function wizard_validate(step){
        let state = true;
        step = parseInt(step);

        if (step === 1) state = true;
        if (step === 2){
            if (!cliente_data.id) {
                not.Aviso('Cliente no identificado','',2000);
                state = false;
            }
        }
        if (step === 3){
            if (prenda_data.id <= 0) { 
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


    // Cliente
    async function cliente_preload(){
        try {
            if (d.getElementById('idcliente_base') == undefined) return; 
            let ide = d.getElementById('idcliente_base').value;
            if (await cliente_get_data(ide)) {
                wizard.step = 2;
                wizard_render();
                console.log(`[Cliente] ${cliente_data.nombre}`);
            }
        } catch (error) {
            console.log(`[Cliente][Preload] Error al cargar`)
        }
    }
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
                    if (e.bloqueo_atencion > 0){
                        blocked_label = `
                        <div class="enc2454-label-blocked py-1">
                            [ListaNegra] ${e.bloqueo_motivo}
                        </div>
                        `; 
                    }
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
                                    [${e.tipo_documento}: ${e.documento}] - ${e.direccion}, ${e.distrito} 
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
    async function cliente_get_data(id, setts = {}){
        try {
            let req = await mdl_cliente.get_data(id); 
            if (!req || req.code != 'OK') {
                not.Aviso('Error al seleccionar cliente xD', '', 2000); 
                return;
            }
            let data = req.result;
            cliente_data.id = data.id;
            cliente_data.nombre = data.nombre_full,
            cliente_data.documento = data.documento;
            cliente_data.distrito = data.distrito;
            not.Dark({
                title: 'Cliente Seleccionado',
                time: 2000
            });
            return true;
        } catch (error) {
            let mssg = (error.name) ? 'Error de ejecucion' : error;
            not.Aviso(mssg, '', 3000); 
        }
    }
    async function cliente_save_data(data){
        try {
            app.preload('open','Registrando cliente..');
            d.getElementById('btn_cliente_save').disabled = true;
            d.getElementById('modal_cliente_loader').classList.remove('d-none');

            let save = await mdl_cliente.save(data);
            d.getElementById('modal_cliente_loader').classList.add('d-none');
            if (!save || save.code != 'OK') {
                throw (save.code == 'ERR') ? save.message : 'Error al registrar';
                d.getElementById('btn_cliente_save').disabled = false;
                return;
            }
            not.Ok('Cliente registrado','',2000);
            setTimeout(() => {
                app.preload('close');
            }, 500);
            return save;
        } catch (error) {
            app.preload('close');
            d.getElementById('modal_cliente_loader').classList.add('d-none');
            d.getElementById('btn_cliente_save').disabled = false;
            let mssg = (error.name) ? 'Error de ejecucion' : error;
            not.Aviso(mssg, '', 2500); 
        }
    }
    async function distrito_search_data(search_val){
        let req = await fetch(srv.url() + 'Ubigeo/service_search',{
            method: 'POST',
            body: JSON.stringify({
                search: search_val
            })
        });
        let data = await req.json(); 
        const list = d.getElementById('distrito-search-list');

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

    // Prenda
    async function prenda_get_all(setts = {}){
        try {
            app.render('prenda_dataset', `
                <div class="enc2454-dataset-blank">
                    <div class="d-flex justify-center align-items-center">
                        Cargando&nbsp;
                        <div class="spinner-v1-mini"></div>
                    </div>
                </div>
            `);
            let req = await mdl_prenda.get_by_cliente(cliente_data.id);
            if (!req || req.code === 'ERR') {
                not.Aviso('Error al cargar las prendas', '', 2000);
                app.render('prenda_dataset', '[ Error al procesar ]');
                return;
            }
            if (req.result.length == 0) app.render('prenda_dataset', `
                <div class="d-flex justify-center">
                    [ Sin Datosddd ] 
                </div>
            `);

            let data = req.result;
            let html = '';
            data.forEach(e => {
                let marked_attr = '';
                if (setts.id_default && setts.id_default == e.id) {
                    marked_attr = 'checked';
                    prenda_data.id = e.id;
                    prenda_data.descripcion = e.descripcion;
                    prenda_data.valoracion = e.valoracion;
                    prenda_data.categoria = e.categoria_nombre;
                }
    
                html += `
                <div class="enc2454-checklist-item">
                    <input id="${e.id}" type="radio" name="prenda-item" value="${e.id}" data-descripcion="${e.descripcion}" data-categoria="${e.categoria_nombre}" data-valoracion="${e.valoracion}" ${marked_attr}>
                    <label for="${e.id}">
                        <span class="enc2454-checklist-icon"></span>
                        <div class="enc2454-checklist-data">
                            <div class="enc2454-checklist-title">
                                ${e.descripcion}
                            </div>
                            <div class="enc2454-checklist-subtitle">
                                Categoria: ${e.categoria_nombre} | Estado: ${e.valoracion}
                            </div>
                        </div>
                    </label>
                </div>
                `;
            });
            html += `
                <div class="enc2454-checklist-item">
                    <input id="NG" type="radio" name="prenda-item" value="0">
                    <label for="NG" data-valoracion="" data-descripcion="" ${(prenda_data.id > 0) ? 'checked' : ''}>
                        <span class="enc2454-checklist-icon"></span>
                        <div class="enc2454-checklist-data">
                            <div class="enc2454-checklist-title">
                                SIN GARANTIA
                            </div>
                        </div>
                    </label>
                </div>
            `;
            app.render('prenda_dataset', html);
        } catch (error) {
            app.render('prenda_dataset', `
                <div class="d-flex justify-center">
                    [ Sin Datos ] 
                </div>
            `);
        }
        
    }
    async function prenda_render_tipos(default_data = {}){
        try {
            let area = d.getElementById('tprenda-render-area');
            area.innerHTML = `
                <div class="d-flex justify-center">
                    <div class="spinner-v1"></div>
                </div>
            `;
            let data = await mdl_tprenda.get_all();      
            area.innerHTML = (data) ? '' : '<div class="text-center">Sin Datos</div>';

            let html = '', call_categorias = 0;
            data.forEach(e => {  
                let chkd = '';              
                if (default_data.id || default_data.name) {
                    let val_a = (default_data.id) ? default_data.id : default_data.name;
                    let val_b = (default_data.id) ? e.id : e.nombre;
                    if (val_a === val_b) {
                        chkd = 'checked';
                        call_categorias = e.id;
                    }
                }
                area.innerHTML += `
                <label class="custom-control custom-radio custom-control-inline">
                    <input type="radio" data-ref="${e.nombre}" data-nombre="${e.nombre}" class="custom-control-input" name="tipo_prenda" value="${e.id}" ${chkd}>
                    <span class="custom-control-label">${e.nombre}</span>
                </label>
                `;
            });
            if (call_categorias > 0) prenda_render_categorias(call_categorias); 
        } catch (error) {
        }
    }
    async function prenda_render_categorias(tipo, default_checked = 0){
        try {
            let list = d.getElementById('categoria-list');
            let loader = d.getElementById('categoria-loader');
            loader.classList.add('spinner-v1-small');

            let res = await mdl_categoria.get_by_tipo(tipo);
            list.innerHTML = '<option value="">-SELECCIONAR-</option>';
            res.forEach(e => {
                let item = d.createElement('option');
                item.setAttribute('value', e.id);
                item.textContent = e.nombre;
                if (e.id == default_checked) item.setAttribute('selected', 'selected');
                list.appendChild(item);
            });
            loader.classList.remove('spinner-v1-small');
        } catch (error) {
            app.preload('close');
            let mssg = (error.name) ? 'Error de ejecucion' : error;
            not.Aviso(mssg, '', 2500); 
        }
    }
    async function prenda_render_form(tipo){
        try {
            app.render('prenda_form_content', `
            <div class="d-flex justify-center">
                <div class="spinner-v1-mini"></div>
            </div>
            `);
            let settsdata = await mdl_prenda.get_form_by_tipo(tipo);
            let validate = typeof settsdata;
            if (validate === 'undefined' || validate === null) {
                throw ('Error al obtener el formulario');
            }
            app.render('prenda_form_content', settsdata.result);
            return true;
        } catch (error) {
            app.render('prenda_form_content', '');
            let mssg = (error.name) ? 'Error al procesar' : error;
            not.Aviso('',mssg,2500);
        }
    }
    async function prenda_save(data){
        try {  
            let save = await mdl_prenda.save(data);            
            if (save.code === 'OK' && save.id) {
                not.Ok('Prenda Registrada', '', 2000);  
                return save;
            }else if(save.code === 'ERR') {
                not.Aviso(save.message, '', 2500);                 
                return;
            }
            throw 'El proceso no se completo';       
        } catch (error) {
            let mssg = (error.name) ? 'Error de ejecucion' : error;
            not.Aviso(mssg, '', 2500); 
        }
    }

    // Prestamo
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
            d.getElementById('wiz-btn-save').disabled = true;
            let data = new FormData();
            data.append('prenda', prenda_data.id);
            data.append('cliente', cliente_data.id);
            data.append('monto', prestamo_data.monto);
            data.append('pago', prestamo_data.cuota);
            data.append('mora', prestamo_data.mora);
            data.append('cargo', prestamo_data.cargo);
            data.append('tasa', prestamo_data.tasa);
            data.append('interes', prestamo_data.interes);
            data.append('tipo', prestamo_data.tipo_tasa);
            data.append('ciclo', prestamo_data.ciclo);
            data.append('prorroga', prestamo_data.prorroga);
            data.append('periodo', prestamo_data.periodo);

            // data.append('prorroga', parseInt(document.getElementById('prorroga').value)); 
            const req = await fetch(srv.url()+'Credito/registrar', {
                method: 'POST',
                body: data
            });
            const save = await req.json();
            setTimeout(() => {
                app.preload('close');
            }, 500);
            if (save.status === 'OK') {
                not.Ok(save.message, '', 2000);
                setTimeout(() => {
                    window.location.href = srv.url()+'Credito/detalle/'+save.id;
                }, 300);
            }else{
                not.Aviso(save.message, '', 2500);
                d.getElementById('wiz-btn-save').disabled = false;
            }
            
        } catch (error) {
            app.preload('close');
            d.getElementById('wiz-btn-save').disabled = false;
        }
    }

    // new
    async function tarifa_calculo(){
        var req = await fetch(srv.url()+'Tarifa/service_calcular_tarifa_v2', {
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
        if (res) {
            prestamo_data.interes = res.interes;
            prestamo_data.penalidad = res.penalidad;
            prestamo_data.tipo_tasa = res.tipo;
            prestamo_data.cuota = res.cuota;
            prestamo_data.mora = res.mora;
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
    
})(document);