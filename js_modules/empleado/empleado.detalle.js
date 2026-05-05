(function(d,w){
    let app;
    const local = {
        UBIGEO_SELECT: document.getElementById('UBIGEO_SELECT') ?? null,
        SUBMIT_BUTTON: document.getElementById('SUBMIT_BUTTON') ?? null,
        EDIT_FORM: document.getElementById('EDIT_FORM') ?? null,
        DELETE_BUTTON: document.getElementById('DELETE_BUTTON') ?? null,
        STATUS_BUTTON: document.getElementById('STATUS_BUTTON') ?? null
    }

    d.addEventListener('DOMContentLoaded', ()=>{
        app = new App();
        
    })
    d.addEventListener('input', async evt => {
        if (evt.target.matches('#UBIGEO_CONTAINER .bs-searchbox input')) {
            let data = await getUbigeoData(evt.target.value);
            renderUbigeoResult(data)
        }
    })

    d.getElementById('EDIT_FORM')?.addEventListener('submit', evt => {
        evt.preventDefault()
        let data = new FormData(evt.target);
        if (!window.ID || app.empty(window.ID)){
            not.Aviso('Cuidado', 'Empleado no identificado');
            return;
        }
        data.append('id', window.ID);
        let {ubigeo} = evt.target;
        if (!ubigeo || app.empty(ubigeo.value)){
            data.append('ubigeo', window.UBIGEO.id)
        }
        updateEmpleado(data);
    })
    
    d.getElementById('EDIT_BUTTON')?.addEventListener('click', evt => {
        renderTiposDocumento();
        renderSedes();
        
        configUbigeoSelect();
        $(function () {
            $(local.UBIGEO_SELECT).selectpicker({
                style: 'form-control border-rock bg-transp',
                actionsBox: true,
                title: window.UBIGEO.distrito
            });
            $(local.UBIGEO_SELECT).selectpicker('refresh');
        });
        SUBMIT_BUTTON.disabled = false;
        $('#MODAL_EDIT').modal('show');
    })

    d.getElementById('DELETE_BUTTON')?.addEventListener('click', evt => {
        if (window.ID && confirm('Eliminar?')){

        }
    })

    d.getElementById('STATUS_BUTTON')?.addEventListener('click', evt => {
        const {new_estado, message} = evt.target.dataset;
        // if (!evt.target.dataset)
        if (!window.ID || !new_estado){
            not.Error('Cuidado!', 'Error al procesar.');
            return;
        }
        if (confirm(message)){
            updateStatus(new_estado)
        }
    })





    // define los mensajes de respuesta de la funcion 'selectpicker'
    function configUbigeoSelect(){
        $.fn.selectpicker.defaults = {
            noneSelectedText: 'Nada seleccionado',
            noneResultsText: 'No hay resultados {0}',
            countSelectedText: '{0} de {1} seleccionados',
            maxOptionsText: ['Límite alcanzado ({n} elementos máximo)', 'Límite del grupo alcanzado ({n} elementos máximo)'],
            selectAllText: 'Seleccionar todos',
            deselectAllText: 'Deseleccionar todos',
            multipleSeparator: ', '
        };

    }

    // solicita y retorna datos de ubigeo (ciudad/distrito)
    async function getUbigeoData(value = ''){
        try {
            let search_ = value.length > 0 ? `&ciudad=${value}` : '' 
            let req = await fetch(app.URL(`api/v1/ubigeo?limite=5${search_}`));
            let resp = await req.json();
            return resp;
        } catch (error) {
            console.log(error);
            return;
        }
    }

    // renderiza lista de datos de ubigeo en el 'selectpicker'
    function renderUbigeoResult(data_){
        try {
            if (local.UBIGEO_SELECT === null){
                throw "Error de Selector";
            }

            $(local.UBIGEO_SELECT).empty();
            $.each(data_, function(index, value) {
                $(local.UBIGEO_SELECT).append(`
                    <option value="${value.id}">${value.distrito+', '+value.departamento}</option>    
                `);
            });
            $(local.UBIGEO_SELECT).selectpicker('refresh');
        } catch (error) {
            console.log(error.message);
        }
    }

    // envia los datos del empleado para su almacenamiento
    async function updateEmpleado(data_){
        try {
            resetErrors();
            SUBMIT_BUTTON.disabled = true;
            SUBMIT_BUTTON.classList.add('btn-loading');
            let request = await fetch(app.URL('api/v1/empleados/editar'),{
                method: 'POST',
                body: data_
            });
            let resp = await request.json();
            SUBMIT_BUTTON.classList.remove('btn-loading');
            if (resp.success){
                not.Ok('Genial!', resp.message);
                // setTimeout(() => {
                //     window.location.href = app.URL(`empleado/detalle/${resp.id_empleado}`)
                // }, 1000);

                getDataEmpleado();
                SUBMIT_BUTTON.disabled = false;
                $('#MODAL_EDIT').modal('hide');
            }else {
                resp.errors && renderErrors(resp.errors);
                let message = resp.message ?? 'Error al procesar.';
                not.Aviso('Cuidado..', message)
                SUBMIT_BUTTON.disabled = false;
            }
        } catch (error) {
            not.Aviso('Error', 'Ocurrio un error al procesar.')
            SUBMIT_BUTTON.disabled = false;
            SUBMIT_BUTTON.classList.remove('btn-loading');
        }
    }

    // Renderiza lista de tipos de documentos en el selector
    async function renderTiposDocumento(){
        try {
            app.render('TIPO_DOC_',`
                <div class="text-center">
                    Cargando&nbsp;<i class="fas fa-circle-notch fa-spin"></i>
                </div>
            `);

            const currentDoc = window.TIPO_DOC;
            const require = await fetch(app.URL('TipoDocumento/rest'));
            const res = await require.json();
            let html = '';
            if (res.length > 0) {
                res.forEach((tdoc,index) => {
                    let chk = (tdoc.nombre === currentDoc) ? 'checked' : '';
                    html += `
                        <label class="custom-control custom-radio custom-control-inline">
                            <input type="radio" class="custom-control-input" name="tipo_documento" value="${tdoc.nombre}" ${chk} required>
                            <span class="custom-control-label">${tdoc.nombre}</span>
                        </label>
                    `;
                });
            }else{
                html = `
                    <b>No se encontraron tipos de documentos.</b>
                `;
            }
            app.render('TIPO_DOC_', html);
        } catch (error) {
            app.render('TIPO_DOC_', 'Error al cargar');
        }
    }

    // Cargar lista de sedes en el selector de sedes
    async function renderSedes(){
        try {
            const currentSede = window.SEDE_ID;
            let req = await fetch(app.URL('api/v1/sedes'))
            let resp = await req.json();
            if (!resp){
                throw "Error de procesamiento.";
            }
            let input_sede = document.getElementById('SEDE_SELECT') ?? null;
            if(input_sede){
                input_sede.innerHTML = '<option value="">- SELECCIONAR -</option>';
                input_sede.innerHTML += resp.map(item => {
                    let status = (currentSede === item.id) ? 'selected' : '';
                    return `<option value="${item.id}" ${status}>${item.nombre}</option>`
                }).join('')
            }
        } catch (error) {
            console.log(error.message);
        }
    }

    // Muestra los errores de los datos enviados
    function renderErrors(error){   
        if (error.empleado) {
            not.Aviso('Cuidado!', error.empleado)
        }   
        if (error.id) {
            not.Aviso('Cuidado!', error.id)
        }    
        if(error.documento) app.render('feed_documento', error.documento);
        if(error.tipo_documento) app.render('feed_tipodoc', error.tipo_documento);
        if(error.nombre) app.render('feed_nombre', error.nombre);
        if(error.paterno) app.render('feed_paterno', error.paterno);
        if(error.materno) app.render('feed_materno', error.materno);
        if(error.ubigeo) app.render('feed_ubigeo', error.ubigeo);
        if(error.direccion) app.render('feed_direccion', error.direccion);
        if(error.telefono) app.render('feed_telefono', error.telefono);
        if(error.sede) app.render('feed_sucursal', error.sede);
    }
    
    // Limpia los errores mostrados
    function resetErrors(){
        app.render('feed_documento', '');
        app.render('feed_tipodoc', '');
        app.render('feed_nombre', '');
        app.render('feed_paterno', '');
        app.render('feed_materno', '');
        app.render('feed_ubigeo', '');
        app.render('feed_direccion', '');
        app.render('feed_telefono', '');
        app.render('feed_sucursal', '');
    }

    // Obtiene y renderiza los datos del empleado
    async function getDataEmpleado(){
        try {
            const id_ = window.ID;
            const req = await fetch(app.URL(`api/v1/empleados/${id_}`));
            const resp = await req.json();

            if (resp.success){
                renderDataEmpleado(resp.data);
            }
        } catch (error) {
            console.log(error);
        }
    }

    // Muestra/Recarga los datos del empleado 
    function renderDataEmpleado(data){
        if (d.getElementById('data_nombre_apellido')){
            d.getElementById('data_nombre_apellido').innerHTML = `${data.nombres} ${data.paterno} ${data.materno}`;    
        }
        if (d.getElementById('data_documento')){
            d.getElementById('data_documento').innerHTML = data?.documento || "";    
        }
        if (d.getElementById('data_tipodoc')){
            d.getElementById('data_tipodoc').innerHTML = data?.tipo_documento || "";    
        }
        if (d.getElementById('data_telefono')){
            d.getElementById('data_telefono').innerHTML = data?.telefono || "";    
        }
        if (d.getElementById('data_email')){
            d.getElementById('data_email').innerHTML = data?.email || "";    
        }
        if (d.getElementById('data_direccion')){
            d.getElementById('data_direccion').innerHTML = data?.direccion || "";    
        }
        if (d.getElementById('data_sede')){
            d.getElementById('data_sede').innerHTML = data?.sede_nombre || "";    
        }

        // d.getElementById('data_tipodoc')?.innerHTML = data?.tipo_documento || "";
        // d.getElementById('data_telefono')?.innerHTML = data?.telefono || "";
        // d.getElementById('data_email')?.textContent = data?.email || "";
        // d.getElementById('data_direccion')?.textContent = data?.direccion || "";
        // d.getElementById('data_sede')?.textContent = data?.sede_nombre || "";
    } 

    // Elimina los datos del empleado
    async function deleteDataEmpleado(id_){
        try {
            let data = new FormData();
            data.append('id', id_);
            const req = await fetch(app.URL('api/v1/empleados/remover'),{
                method: 'POST',
                body: data
            });
            const resp = await req.json();
            if (resp.success){
                not.Ok('Genial!', resp.message)
                setTimeout(()=>{
                    window.location.href = app.URL('empleado');
                },1000)
            }else {
                let message = resp.message || 'Error al procesar';
                not.Aviso('Cuidado!', resp.message)
            }
        } catch (error) {
            not.Error('Error', error.message)
        }
    }

    async function updateStatus(new_estado){
        try {
            const data = new FormData();
            data.append('id', window.ID);
            data.append('estado', new_estado);
            const req = await fetch(app.URL('api/v1/empleados/editar-estado'), {
                method: 'POST',
                body: data
            });
            const resp = await req.json();
            if (resp.success){
                not.Ok('Genial', resp.message);
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            }else {
                // console.log(resp);
                let message = resp.message || 'Error al actualizar';
                not.Aviso('Cuidado', message);
                return;
            }
        } catch (error) {
            
        }
    }
})(document, window);