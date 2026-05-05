(function(){
    let app;
    let consulta_init = {tipo: 'nombre', buscar: '', limite: 10};
    document.addEventListener('DOMContentLoaded', (e)=>{
        app = new App();
        Clientes();
    })
    document.addEventListener('change', (event)=>{
        if (event.target.matches('[name=tipo_filtro]')) {
            if (event.target.value.length > 0) {
                consulta_init.tipo = event.target.value;
                Clientes();
            }
        }
    })
    if (document.getElementById('inp_limite')) {
        document.getElementById('inp_limite').addEventListener('change', event => {
            if (!isNaN(event.target.value) && event.target.value > 0) {
                consulta_init.limite = parseInt(event.target.value);
                Clientes();
            }
        })
    }
    if (document.getElementById('inp_buscar')) {
        document.getElementById('inp_buscar').addEventListener('keyup', (event)=>{
            consulta_init.buscar = event.target.value;
            Clientes();
        })
    }
    if (document.getElementById('check_darkmode')) {
        document.getElementById('check_darkmode').addEventListener('change', event => {
            try {
                let tb = document.getElementById('tabla_cliente');
                if (event.target.checked) tb.classList.add('table-dark');
                else tb.classList.remove('table-dark');
            } catch (error) {
                console.log('darkmode::error', error.getMessage());
            }
        })
    }
    // Consulta los registros de clientes
    const Clientes = async function(){
        try {
            app.render('tb_cliente', `<tr class="text-center"><td colspan="7"><div class="loader"></div> Cargando..</td></tr>`);
            // let cliente = new Cliente();
            // const consulta = await cliente.Listar(consulta_init.tipo, consulta_init.buscar, consulta_init.limite);
            let data_request = new FormData();
            data_request.append('tipo', consulta_init.tipo);
            data_request.append('buscar', consulta_init.buscar);
            data_request.append('limite', consulta_init.limite);
            let request = await fetch(srv.url()+'Cliente/ServBuscar', {
                method: 'POST',
                body: data_request
            });
            let consulta = await request.json();
            
            let html = '';
            if (consulta.status === 'OK') {
                if (consulta.result.length > 0) {
                    consulta.result.forEach((element, index) => {
                        html += `
                            <tr>
                                <td class="text-center">${parseInt(index)+1}</td>
                                <td>${element.nombres} ${element.paterno} ${element.materno}</td>
                                <td>${element.documento}</td>
                                <td>${element.ciudad}</td>
                                <td>${element.telefono}</td>
                                <td>${element.creditos_activos}</td>
                                <td class="text-center">
                                    <a class="btn btn-primary" href="${srv.url()}Cliente/detalle/${element.id}" title="Ver Detalle">
                                        <i class="fe fe-layers"></i>
                                    </a>
                                </td>
                            </tr>
                        `;
                    });
                    
                }else{
                    html = `<tr class="text-center"><td colspan="7">No se encontraron resultados</td></tr>`;
                }
            }else{
                html = `<tr class="text-center"><td colspan="7">Ocurrio un error al consultar los datos</td></tr>`;
            }
            app.render('tb_cliente', html);
        } catch (error) {
            console.log(error);
            app.render('tb_cliente', `<tr class="text-center"> <td colspan="7">Ocurrio un error al procesar la solicitud</td></tr>`);
        }
    }


})();