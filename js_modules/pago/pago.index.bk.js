(function(d){
    var app;
    var search_object = {
        type: 'DIA',
        in_text: '',
        in_date: '',
        in_range_1: '',
        in_range_2: '',
        order: 'DESC',
        limit: 10        
    }

    d.addEventListener('DOMContentLoaded', function(){
        app = new App();
        options_area_render()
        data_render()

         // $('[data-toggle="datepicker"]').datepicker({
        //     format: 'dd-mm-yyyy',
        //     language: 'es-ES'
        // }).on('change', event => {
        //     console.log('processing...'+event.target.value)
        //     if (event.target && event.target.id === 'search_input_date') {
        //         search_object.in_date = event.target.value;
        //         data_render()
        //     }
        //     if (event.target && event.target.id === 'search_input_range_1') {
        //         search_object.in_range_1 = event.target.value;
        //         if (search_object.in_range_1.length > 0 && search_object.in_range_2.length > 0){
        //             data_render()
        //         }
        //     }
        //     if (event.target && event.target.id === 'search_input_range_2') {
        //         search_object.in_range_2 = event.target.value;
        //         if (search_object.in_range_1.length > 0 && search_object.in_range_2.length > 0){
        //             data_render()
        //         }
        //     }
        // });
    })
    d.addEventListener('change', async event => {
        console.log('change...')
        
        if (event.target && event.target.id === 'search_select_type') {
            search_object.type = event.target.value;
            options_area_render()
        }
        
        
        if (event.target && event.target.id === 'config_order_data') {
            search_object.order = event.target.value;
            data_render()
        }
    })
    d.addEventListener('keyup', event => {        
        if (event.target && event.target.id === 'search_input_text') {
            search_object.in_text = event.target.value;
            data_render()
        }
        if (event.target && event.target.id === 'config_limit_data') {
            if (event.target.value.length > 0){
                search_object.limit = parseInt(event.target.value);
                data_render()
            }
        }
    })

    async function data_render(){
        try {
            table_data_preload()
            const req = await fetch(srv.url()+'Pago/service_all', {
                method: 'POST',
                body: JSON.stringify(search_object),
                headers: {
                    'Content-Type': 'application/json'
                }
            })
            const res = await req.json();
            var data_html = '';
            if (res.length > 0) {
                res.forEach(e => {
                    data_html += `
                        <tr>
                            <td>
                                <a href="${srv.url()}Pago/detalle/${e.pago_id}" class="btn-link">
                                    #${e.pago_serie}
                                </a>
                            </td>
                            <td>
                                <a href="${srv.url()}Credito/detalle/${e.credito_id}" class="btn-link">
                                    #${e.credito_serie}
                                </a>
                            </td>
                            <td>${e.cliente}</td>
                            <td>${e.concepto}</td>
                            <td>${e.fecha}</td>
                            <td>${e.forma_pago}</td>
                            <td>S/ ${e.total}</td>
                            <td>
                                <a href="${srv.url()}Pago/detalle/${e.pago_id}" class="btn btn-primary">
                                    Detalle
                                </a>
                            </td>
                        </tr>
                    `;
                })
            }else {
                data_html = `
                    <tr>
                        <td colspan="7" class="text-center">Sin Datos</td>
                    </tr>
                `;
            }
            render('pago_index_datatable', data_html);
        } catch (error) {
            render('pago_index_datatable', `
                <tr>
                    <td colspan="7" class="text-center">Error al Procesar...</td>
                </tr>
            `);
        }
    }
    function options_area_render(){
        d.getElementById('search_input_text').value = '';
        d.getElementById('search_input_date').value = '';
        d.getElementById('search_input_range_1').value = '';
        d.getElementById('search_input_range_2').value = '';
        search_object.in_text = '';
        search_object.in_date = '';
        search_object.in_range_1 = '';
        search_object.in_range_2 = '';
        switch(search_object.type){
            case 'COD':
                look_at('search_input', 'show');
                look_at('search_fecha', 'hide');
                look_at('search_rango_1', 'hide');
                look_at('search_rango_2', 'hide');
                break;
            case 'CRED':
                look_at('search_input', 'show');
                look_at('search_fecha', 'hide');
                look_at('search_rango_1', 'hide');
                look_at('search_rango_2', 'hide');
                break;
            case 'CLI':
                look_at('search_input', 'show');
                look_at('search_fecha', 'hide');
                look_at('search_rango_1', 'hide');
                look_at('search_rango_2', 'hide');
                break;
            case 'FECHA':
                look_at('search_input', 'hide');
                look_at('search_fecha', 'show');
                look_at('search_rango_1', 'hide');
                look_at('search_rango_2', 'hide');
                break;
            case 'RANGO':
                look_at('search_input', 'hide');
                look_at('search_fecha', 'hide');
                look_at('search_rango_1', 'show');
                look_at('search_rango_2', 'show');
                break;
            default: 
                look_at('search_input', 'hide');
                look_at('search_fecha', 'hide');
                look_at('search_rango_1', 'hide');
                look_at('search_rango_2', 'hide');
                break;
        }
    }
    function render(id, content) {
        var el = (typeof id === 'object') ? id : document.getElementById(id);
        el.innerHTML = content;
    }
    function look_at(id, act){
        var el = (typeof id === 'object') ? id : document.getElementById(id);
        switch (act) {
            case 'show':
                el.style.display = 'block';
                break;
        
            case 'hide':
                el.style.display = 'none';
                break;
        }
    }
    function table_data_preload(){
        let tbody = document.getElementById('pago_index_datatable');
        tbody.innerHTML = `
            <tr class="text-center">
                <td colspan="7" >
                    <div class="spinner-icon"></div>
                </td>
            </tr>
        `
    }
})(document);