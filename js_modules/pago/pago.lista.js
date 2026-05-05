(function(){
    let app;
    let request_init = {tipo: 'fecha', buscar: '', limite: 10, inicio: '', final: ''};

    document.addEventListener('DOMContentLoaded', event => {
        app = new App();
        consultar_pagos();

        app.defineEvent('#_form_filtro_rango', 'submit', event => {
            event.preventDefault()
            request_init.inicio = event.target.inicio.value;
            request_init.final = event.target.final.value;
            consultar_pagos();
            request_init.tipo = 'cliente';
        })
        app.defineEvent('#_btn_dia_actual', 'click', event => {
            event.preventDefault();
            request_init.tipo = 'fecha';
            consultar_pagos();
        })
        app.defineEvent('#_select_cantidad', 'change', event => {
            if (!isNaN(event.target.value)) {
                request_init.limite = parseInt(event.target.value);
            }else{
                request_init.limite = 10;
                event.target.value = 10;
            }
            consultar_pagos();
        })
        app.defineEvent('#_input_buscar', 'keyup', event => {
            if (event.target.value) {
                request_init.buscar = event.target.value;
                consultar_pagos();
            }
        })
        app.defineEvent('#_check_darkmode', 'change', event => {
            let tb = document.getElementById('_pago_table');
            if (event.target.checked) tb.classList.add('table-dark');
            else tb.classList.remove('table-dark');
        })
    })
    document.addEventListener('change', event => {
        if (event.target.matches('[name=_tipo_filtro_item]')) {
            request_init.tipo = event.target.value;
            switch (event.target.value) {
                case 'cliente':
                    $("#_filtro_texto_section").slideDown("fast");
                    $("#_filtro_select_section").slideUp("fast");
                    $("#_form_filtro_rango").slideUp("fast");
                    break;
            
                case 'credito':
                    $("#_filtro_texto_section").slideDown("fast");
                    $("#_filtro_select_section").slideUp("fast");
                    $("#_form_filtro_rango").slideUp("fast");
                    break;
                
                case 'forma':
                    $("#_filtro_texto_section").slideUp("fast");
                    $("#_filtro_select_section").slideDown("fast");
                    $("#_form_filtro_rango").slideUp("fast");
                    break;
                
                case 'fecha_rango':
                    $("#_filtro_texto_section").slideUp("fast");
                    $("#_filtro_select_section").slideUp("fast");
                    $("#_form_filtro_rango").slideDown("fast");
                    break;
                case 'fecha':
                    $("#_filtro_texto_section").slideUp("fast");
                    $("#_filtro_select_section").slideUp("fast");
                    $("#_form_filtro_rango").slideUp("fast");
                    request_init.tipo = 'fecha';
                    break;
            }
            consultar_pagos();
        }
        if (event.target.matches('[name=_forma_pago_item]')) {
            request_init.buscar = event.target.value;
            consultar_pagos();
        }
    })
    const consultar_pagos = async function(){
        try {
            // app.render('_pago_dataset', '<tr class="text-center"><td colspan="8"><div class="loader"></div>Cargando...</td></tr>');
            $('#_caption_dataset_pago').slideDown('fast');

            let data = new FormData();
            data.append('tipo', request_init.tipo);
            data.append('buscar', request_init.buscar);
            data.append('limite', request_init.limite);
            data.append('inicio', request_init.inicio);
            data.append('final', request_init.final);
            let pago = new Pago();
            let consulta = await pago.Listar(data);
            let html = '';
            if (consulta.status === 'OK') {
                if (consulta.result.length > 0) {
                    consulta.result.forEach((element, index) => {
                        html += `
                            <tr>
                                <td>${index+1}</td>
                                <td>${element.serie}</td>
                                <td>${element.credito}</td>
                                <td>${element.concepto}</td>
                                <td>${element.fecha}</td>
                                <td>${element.cliente}</td>
                                <td>${element.forma}</td>
                                <td>S/ ${element.total}</td>
                                <td class="text-center">
                                    <a class="btn btn-primary btn-sm" href="${srv.url()}Pago/detalle/${element.id}" title="Ver Detalle">
                                        <i class="fe fe-layers"></i>
                                        Ver Detalle 
                                    </a>
                                </td>
                            </tr>
                        `;
                    });
                }else{
                    html = '<tr><td colspan="9" class="text-center"><i class="fe fe-bar-chart-2"></i>Sin resultados</td></tr>';
                }
            }else{
                html = '<tr><td colspan="9" class="text-center">i class="fe fe-x-circle"></i>Error al consultar</td></tr>';
            }
            app.render('_pago_dataset', html);
        } catch (error) {
            app.render('_pago_dataset', '<tr><td colspan="9" class="text-center"><i class="fe fe-x-circle"></i>Error al consultar</td></tr>');
        }
        finally{
            $('#_caption_dataset_pago').slideUp('fast');
        }
    }
})();