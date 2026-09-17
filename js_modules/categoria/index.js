"use strict";
(function(d, w){
    let app, ui, cate_api, tprenda_api;
    let TableCategoriaSkl;
    let EnProceso = false;
    const SEARCH_FILTER = {
        orden: 'ASC',
        limite: 10,
        nombre: '',
        tipo: 0
    }
    const getEl = (id) => document.getElementById(id);
    const getEls = (selector) => document.querySelectorAll(selector);
    let modal_nuevo = getEl('modal_categoria_nuevo');
    let modal_editar = getEl('modal_categoria_editar');
    let form_nuevo = getEl('form_categoria_nuevo');
    let form_editar = getEl('form_categoria_editar');


    d.addEventListener('DOMContentLoaded', async evt => {
        app = new App();
        ui = new UI();
        cate_api = w.categoria_api;
        tprenda_api = window.tipo_prenda_api;
        // $(modal_nuevo).modal({backdrop: 'static', keyboard: false});
        

        TableCategoriaSkl = new Skeletonx(d.getElementById('tbody_categorias'));
        render_tabla_categorias();
        render_tipos_prenda('filtro');

    });
    // getEl('btn_test_1').addEventListener('click', evt => {

    // });
    // getEl('btn_test_2').addEventListener('click', evt => {
        
    // });

    let searchTimeout;
    getEl('input_categoria_search')?.addEventListener('input', evt => {
        let value = evt.target.value;
        if (value.length < 2) return;
        
        SEARCH_FILTER.nombre = value;
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            render_tabla_categorias();
        }, 200);
    });    
    d.addEventListener('click', async evt => {
        let target = evt.target;        
        if (target.matches('[name=btn_categoria_elimina]')) {
            let id = target.dataset.id;
            if (!Number(id)) {
                not.Aviso('','Categoria no identificada.');
                console.log('Categoria no identificada.');
                return;
            }
            const confirmacion = await Swal.fire({
                title: '¿Estás seguro?',
                text: '¡No podrás revertir esto!',
                icon: 'warning',
                confirmButtonColor: "#ff4103",
                cancelButtonColor: "#8082a6",
                showCancelButton: true,
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            });
            if (confirmacion.value) {
                eliminar_categoria(id);
            }
        }
        if (target.matches('#btn_categoria_nuevo')) {
            if (!modal_nuevo) {
                not.Aviso('', 'No se pudo iniciar el proceso.');
                console.log(`Error al inicializar "modal nuevo"`);
                return;
            }
            if (!form_nuevo) {
                not.Aviso('', 'No se pudo iniciar el proceso.');
                console.log(`Error al inicializar "form nuevo"`);
                return;
            }
            form_nuevo.reset();
            $(modal_nuevo).modal('show');
            setTimeout(() => {
                render_tipos_prenda('modal_nuevo', {input: 'radio'});
            }, 500);
        }
        if (target.matches('#btn_categoria_nuevo')) {}
    });
    d.addEventListener('change', evt => {
        let target = evt.target;
        if (target.matches('[name=filter_tipo_prenda]')) {
            let {id, nombre} = target.dataset;
            if (!id || !nombre) {
                not.Aviso('Datos no encontrados.','');
                console.log('Datos no encontrados.');
                target.checked = !target.checked;
                return;
            }
            const items = d.querySelectorAll('[name=filter_tipo_prenda]');
            if (!items || items.length === 0) {
                console.warn('items no encontrados.');
                return;
            }
            let params_string = '';
            let batch = [];
            items.forEach(element => {
                if (element.checked) {
                    let encodeID = encodeURIComponent(element.dataset.id);
                    batch.push(`tipos_group[]=${encodeID}`);
                }
            });
            params_string = batch.join('&')
            render_tabla_categorias(params_string);
        }
    });
    form_nuevo?.addEventListener('submit', evt => {
        evt.preventDefault();
        let form = evt.target;
        const log = {
            nombre: form.querySelector(`[data-for='nombre']`),
            tipo: form.querySelector(`[data-for='tipo_prenda']`),
        }
        let nombre = form.nombre.value;
        let tipo = form.modal_nuevo_tipo_prenda.value;
        if (nombre.length === 0 || tipo.length === 0) {
            if (nombre.length === 0) log.nombre.textContent = 'Valor invalido.';
            if (tipo.length === 0) log.tipo.textContent = 'Valor invalido.';
            not.Aviso('', 'Proceso invalido.');
            return;
        }
        let form_data = new FormData();
        form_data.append('nombre', nombre);
        form_data.append('tipo_prenda', tipo);
        crear_categoria(form_data);
    });

    const search_in_dom = (ide) => {
        let elm = d.querySelector(ide);
        return elm;
    }


    // F U N C T I O N S
    async function render_tabla_categorias(opts = {}){
        const table = document.getElementById('tbody_categorias');
        if (!table) {
            not.Aviso('Tabla no encontrada.');
            console.log('Tabla no encontrada');
            return;
        }
        let colspan = 4;
        if (EnProceso){
            not.Blank('Espera', 'Hay una tarea en proceso.');
            console.log('Tarea en proceso, espera..');
            return;
        }
        EnProceso = true;
        try {
            TableCategoriaSkl.show('table', {columns: colspan});
            const {ok, data} = await cate_api.getAll(SEARCH_FILTER, opts);
            if (!ok || data.legth === 0) {
                TableCategoriaSkl.hide(`
                    <tr><td class="text-center">
                        <span class="text-muted text-center" colspan="4">
                        Sin Resultados <i class="fe fe-alert-circle"></i></span>
                    </td></tr>
                </span>`);
                return;
            }

            let html = data.map((categoria,ind) => {
                return `
                    <tr class="text-left">
                        <td class="align-middle w-1">${ind+1}</td>
                        <td class="align-middle text-left">${app.safe_esc(categoria.nombre)}</td>
                        <td class="align-middle text-left">${app.safe_esc(categoria.tipo_nombre)}</td>
                        <td>
                            <button class="btn btn-light text-bluex text-indigo" title="Editar" name="categoria_editar_item" data-id="${categoria.id}">
								<i class="fe fe-edit"></i>
							</button>
							<button class="btn btn-light text-danger" title="Eliminar" name="btn_categoria_elimina" data-id="${categoria.id}">
								<i class="fe fe-trash"></i>
							</button>
                        </td>
                    </tr>
                `;
            }).join('');
            setTimeout(() => {
                TableCategoriaSkl.hide(html || `<tr><td colspan="${colspan}">Sin Datos</td></tr>`);
            }, 100);
        } catch (error) {
            console.log(error);
            not.Error('Ocurrió un error inesperado.');
            TableCategoriaSkl.hide(`
                <tr>
                    <td class="text-center" colspan="${colspan}">
                        <span class="text-muted">Error al cargar <i class="fe fe-alert-circle"></i></span>
                    </td>
                </tr>
            `);
        } finally { EnProceso = false; }
    }
    async function fetch_tipos_prenda(){
        try {
            let {ok, data} = await tprenda_api.getAll();
            if (!ok) {
                return [];
            }
            return data;
        } catch (error) {
            console.log('error al obtener "tipos prenda"');
            console.log(error);
            return null;
        }
    }
    async function render_tipos_prenda(area, {idSelected = 0, input = 'checkbox'} = {}){
        const containers = {
            filtro: 'section_filters_tipos',
            modal_nuevo: 'modal_nuevo_tipos_prenda',
            modal_editar: 'modal_editar_tipos_prenda'
        }
        const container = getEl(containers[area]);
        const inputCss = input==='checkbox' ? 'checkz' : 'radioz';
        const containerSkl = new Skeletonx(container);
        if (!containerSkl) {
            not.Aviso("Tipos Prenda", "Sin area de renderizado");
            console.warn('No se encontro un area de renderizado.');
            return;
        }

        try {
            containerSkl.show('list', {count: 1});
            const data = await fetch_tipos_prenda();
            const items_html = data.map((item,indx) => {
                const id_unq = app.createRandomId(5);
                const isSelected = Number(item.id) === Number(idSelected) ? 'selected' : '';
                return `
                    <label for="${id_unq}_item" class="${inputCss}">
                        <input id="${id_unq}_item" type="${input}" name="${area}_tipo_prenda" value="${item.id}" data-id="${item.id}" data-nombre="${item.nombre}" ${indx===0 ? 'required' : ''} ${isSelected}>
                        <span class="iconz"></span>
                        <span class="textz">${item.nombre}</span>
                    </label>
                `;
            }).join('');
            setTimeout(() => {
                containerSkl.render(items_html || 'Sin resultados.');    
            }, 100);

        } catch (error) {
            not.Aviso("[Tipos Prenda] Error al obtener y renderizar");
            console.warn('[Tipos Prenda] Error al obtener y renderizar');
        }
    }
    async function eliminar_categoria(id){
        if (!id) {
            not.Aviso('', 'Categoria no encontrada.');
            console.log('Categoria no encontrada.');
            return;
        }
        if (EnProceso){
            not.Blank('Espera', 'Hay una tarea en proceso.');
            console.log('Tarea en proceso, espera..');
            return;
        }
        EnProceso = true;
        try {
            const {ok, message} = await cate_api.delete(id);
            if (!ok) {
                not.Aviso('', message || 'No se pudo completar el proceso.');
                console.log(message || 'No se pudo completar el proceso.');
                return;
            }
            not.Ok('', message || 'Proceso completado.');
            render_tabla_categorias();
        } catch (error) {
            
        } finally { EnProceso = false; }
    }
    async function crear_categoria(data){
        try {
            // const {ok, message, errors} = await cate_api.create(data);
            const {ok, message} = await simulador(900);
            if (!ok) {
                not.Aviso('', message || 'Ocurrio un error al procesar.');
                console.log(message || 'Ocurrio un error al procesar.');
                return;
            }
            not.Ok('', message || 'Datos guardados.');
            form_nuevo.reset();
            $(modal_nuevo).modal('hide');
            render_tabla_categorias();
        } catch (error) {
            console.log(error);
        }
    }
    function simulador(timex) {
        return new Promise((resolve, reject)=>{
            if (!timex || timex <= 0) {
                console.log('invalidoj');
                reject('Tiempo invalido.');
                return;
            }

            setTimeout(() => {
                console.log('validoj');
                resolve({ok: true, message: `tiempo cumplido: ${timex}`});
            }, timex || 500);
        });
    }
})(document, window);
