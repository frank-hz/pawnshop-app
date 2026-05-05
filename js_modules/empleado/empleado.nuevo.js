(function(d,w){
    let app;
    const local = {
        UBIGEO_SELECT: document.getElementById('UBIGEO_SELECT') ?? null,
        SUBMIT_BUTTON: document.getElementById('SUBMIT_BUTTON')
    }

    d.addEventListener('DOMContentLoaded', ()=>{
        app = new App();
        renderTiposDocumento();
        renderSedes();
        
        configUbigeoSelect();
        $(function () {
            $(local.UBIGEO_SELECT).selectpicker({
                style: 'form-control border-rock bg-transp',
                actionsBox: true
            });
        });
    })
    d.addEventListener('input', async evt => {
        if (evt.target.matches('#UBIGEO_CONTAINER .bs-searchbox input')) {
            let data = await getUbigeoData(evt.target.value);
            renderUbigeoResult(data)
        }
    })

    d.getElementById('SUBMIT_FORM_')?.addEventListener('submit', evt => {
        evt.preventDefault()
        let form_data = new FormData(evt.target);
        createEmpleado(form_data)
    })
    

    // define los mensajes de respuesta de la funcion 'selectpicker'
    function configUbigeoSelect(id_){
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
    async function createEmpleado(data_){
        try {
            resetErrors();
            SUBMIT_BUTTON.disabled = true;
            SUBMIT_BUTTON.classList.add('btn-loading');
            let request = await fetch(app.URL('api/v1/empleados/crear'),{
                method: 'POST',
                body: data_
            });
            let resp = await request.json();
            SUBMIT_BUTTON.classList.remove('btn-loading');
            if (resp.success){
                not.Ok('Genial!', 'Empleado Registrado.');
                setTimeout(() => {
                    window.location.href = `${srv.url()}empleado/detalle/${resp.id_empleado}`
                }, 1000);
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

            const require = await fetch(app.URL('TipoDocumento/rest'));
            const res = await require.json();
            let html = '';
            if (res.length > 0) {
                res.forEach((element,index) => {
                    let chk = (index === 0) ? 'checked' : '';
                    html += `
                        <label class="custom-control custom-radio custom-control-inline">
                            <input type="radio" class="custom-control-input" name="tipo_documento" value="${element.nombre}" ${chk} required>
                            <span class="custom-control-label">${element.nombre}</span>
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
            let req = await fetch(app.URL('api/v1/sedes'))
            let resp = await req.json();
            if (!resp){
                throw "Error de procesamiento.";
            }
            let input_sede = document.getElementById('SEDE_SELECT') ?? null;
            if(input_sede){
                input_sede.innerHTML = '<option value="">- SELECCIONAR -</option>';
                input_sede.innerHTML += resp.map(item => {
                    return `<option value="${item.id}">${item.nombre}</option>`
                }).join('')
            }
        } catch (error) {
            console.log(error.message);
        }
    }

    // Muestra los errores de los datos enviados
    function renderErrors(error){        
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
})(document, window);