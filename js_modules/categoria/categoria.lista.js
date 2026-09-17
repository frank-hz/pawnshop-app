"use strict";
(function(){
    let app;
    let frm_editar = document.getElementById('frm_editar');
    document.addEventListener('DOMContentLoaded', (e)=>{
        app = new App();
        Categorias();
        // not.Ok('Mensaje de prueba', '', 3000);
    });

    if (document.getElementById('btn_categoria_agregar')) {
        document.getElementById('btn_categoria_agregar').addEventListener('click', async event=>{
            get_tipos_prenda();
            $('#md_categoria_nuevo').modal('show');
        });
    }

    if (document.getElementById('frm_registro')) {
        document.getElementById('frm_registro').addEventListener('submit', async(event) => {
            event.preventDefault();
            try {
                $('#md_categoria_nuevo').modal('hide');
                app.preload('open', 'Guardando..');
                let data = new FormData(event.target);
                let categoria = new Categoriax();
                let registro = await categoria.Registrar(data);

                app.preload('close');
                if (registro.status === 'OK') {
                    not.Ok(registro.mssg, '', 3000);
                    Categorias();
                }else{
                    if (registro.status === 'WARN') 
                        not.Aviso(registro.mssg, '', 3000);
                    else
                        not.Error(registro.mssg, '', 3000); 
                }
            } catch (error) {
                not.Error('Oops', 'Error al procesar', 3000);
            }
        })
    }

    if (frm_editar) {
        frm_editar.addEventListener('submit', async(event) => {
            event.preventDefault();
            try {
                $('#md_categoria_editar').modal('hide');
                app.preload('open', 'Guardando..');
                let data = new FormData(event.target);
                let categoria = new Categoriax();
                let modifica = await categoria.Actualizar(data);

                app.preload('close');
                if (modifica.status === 'OK') {
                    not.Ok(modifica.mssg, '', 3000);
                    Categorias();
                }else{
                    if (modifica.status === 'WARN') 
                        not.Aviso(modifica.mssg, '', 3000);
                    else
                        not.Error(modifica.mssg, '', 3000); 
                }
            } catch (error) {
                not.Error('Oops', 'Error al procesar', 3000);
            }
        })
    }

    async function Categorias(){
        try {
            app.preload('open', 'Cargando..');
            let categoria = new Categoriax();
            let consulta = await categoria.Listar();
            
            app.preload('close');
            let html = '';
            if (consulta.status === 'OK') {
                if (consulta.result.length > 0) {
                    consulta.result.forEach((element, index) => {
                        html += `
                            <tr>
                                <td class="text-center">${parseInt(index)+1}</td>
                                <td>${element.nombre}</td>
                                <td>${element.tipo}</td>
                                <td class="text-center">
                                    <btn name="editar_item" class='btn btn-outline-primary' title="Editar" data-id="${element.id}" 
                                    data-nombre="${element.nombre}" data-tipo="${element.tipo_id}">  
                                        <i class="fe fe-edit-2"></i>
                                        Editar
                                    </btn>
                                    <btn class='btn btn-outline-danger' name="eliminar_item" title="Eliminar" 
                                    data-nombre="${element.nombre}" data-id="${element.id}">  
                                       <i class="fe fe-trash"></i>
                                       Eliminar
                                    </btn>
                                </td>
                            </tr>
                        `;
                    });
                }else{
                    html = `<tr class="text-center"><td colspan="4">No se encontraron resultados.</td></tr>`;
                }
            }else{
                html = `<tr class="text-center"><td colspan="4">${consulta.mssg}</td></tr>`;
            }
            app.render('tb_categoria', html);
            let edita_item = document.getElementsByName('editar_item');
            let elimina_item = document.getElementsByName('eliminar_item');
            edita_item.forEach(element => {
                element.addEventListener('click', event => {
                    if (event.target.dataset.id && event.target.dataset.nombre && event.target.dataset.tipo) {
                        get_tipos_prenda(event.target.dataset.tipo);
                        frm_editar.id.value = event.target.dataset.id;
                        frm_editar.nombre.value = event.target.dataset.nombre;
                        $('#md_categoria_editar').modal('show');
                    }else{
                        not.Error('Oops', 'Error al procesar', 3000);
                    }
                })
            });
            elimina_item.forEach(element => {
                element.addEventListener('click', async(event) => {
                    if (event.target.dataset.id) {
                        try {
                            if (confirm('Eliminar categoria?')) {
                                app.preload('open', 'Procesando..');
                                let categoria = new Categoriax();
                                let elimina = await categoria.Eliminar(event.target.dataset.id);

                                app.preload('close');
                                if (elimina.status === 'OK') {
                                    not.Ok(elimina.mssg, '', 3000);
                                    Categorias();
                                }else{
                                    if (elimina.status === 'WARN') 
                                        not.Aviso(elimina.mssg, '', 3000);
                                    else
                                        not.Error(elimina.mssg, '', 3000); 
                                }
                            }
                        } catch (error) {
                            app.preload('close');
                            not.Error('Oops', 'Error al procesar', 3000);
                        }
                    }
                })
            });
        } catch (error) {
            app.preload('close');
            app.render('tb_categoria', '<tr class="text-center"><td colspan="4" class="text-blue">Error al consultar</td></tr>');
        }
        
    }

    async function get_tipos_prenda(_id = 0){
        try {
            document.getElementById('frm_registro').reset();
            let tipo = new TipoPrendax();
            let tconsulta = await tipo.Listar();
            let html = '';
            if (tconsulta.status === 'OK') {
                if (tconsulta.result.length > 0) {
                    tconsulta.result.forEach(element => {
                        let state = '';
                        if (_id > 0) {
                           if (element.id === _id) state = 'checked';  
                        }
                        html += `
                            <label class="custom-control custom-radio custom-control-inline">
                                <input type="radio" class="custom-control-input" name="tipo" value="${element.id}" ${state} required>
                                <span class="custom-control-label">${element.nombre}</span>
                            </label>
                        `;
                    })
                }else{
                    html = '<span>No se encontraron datos</span>'; 
                }
            }else{
                html = '<span>Error al consultar</span>';
            }
            app.render('tprenda_nuevo_area', html); 
            app.render('tprenda_edita_area', html);             
        } catch (error) {
            app.render('tprenda_nuevo_area', '<span>Error al consultar</span>');
            app.render('tprenda_edita_area', '<span>Error al consultar</span>');
        }
    }

})();