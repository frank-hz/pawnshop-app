(function(){
    let app;
    let request_init = {tipo: 'nombre', buscar: '', limite: 10, propietario: ''};
    let form = document.getElementById('frm_transferencia');

    document.addEventListener('DOMContentLoaded', event => {
        app = new App();
        request_init.propietario = form.propietario.value;
    })
    if (document.getElementById('btn_md_cliente')) {
        document.getElementById('btn_md_cliente').addEventListener('click', event => {
            event.preventDefault();
            buscar_cliente();
            $('#md_cliente').modal('show');
        })
    }
    if (document.getElementById('md_cliente_filtro')) {
        document.getElementById('md_cliente_filtro').addEventListener('change', event => {
            request_init.tipo = event.target.value;
            buscar_cliente();
        })
    }
    if (document.getElementById('md_cliente_input')) {
        document.getElementById('md_cliente_input').addEventListener('keyup', event => {
            request_init.buscar = event.target.value;
            buscar_cliente();
        })
    }
    if (form) {
        form.addEventListener('submit', async(event) => {
            event.preventDefault();
            try {
                app.preload('open', 'Procesando..');
                let data = new FormData(event.target);
                let prenda = new Prenda();
                let transfer = await prenda.Transferir(data);

                app.preload('close');
                switch (transfer.status) {
                    case 'OK':
                        app.renderAlert('ok', transfer.mssg, 1000);
                        setTimeout(() => {
                            window.location.href = srv.url()+'Prenda/detalle/'+transfer.id;
                        }, 1000);
                        break;
                    case 'ERROR':
                        app.renderAlert('error', transfer.mssg, 3000);
                        break;
                    case 'WARN':
                        app.renderAlert('aviso', transfer.mssg, 4000);
                        break;
                    default:
                        break;
                }
            } catch (error) {
                app.preload('close');
                app.renderAlert('error','Error al procesar', 3000);
            }
            
            
        })
    }

    const buscar_cliente = async function(){
        try {
            app.render('md_tb_cliente', '<tr class="text-center"><td colspan="3">Cargando..</td></tr>');
            // let cliente = new Cliente();
            // let consulta = await cliente.Listar(request_init.tipo, request_init.buscar, request_init.limite);
            let data_env = new FormData();
            data_env.append('tipo', request_init.tipo);
            data_env.append('buscar', request_init.buscar);
            data_env.append('limite', request_init.limite);
            const req = await fetch(srv.url()+'Cliente/ServBuscar', {
                method: 'POST',
                body: data_env
            });
            const consulta = await req.json();            
            let html = '';
            if (consulta.status === 'OK') {
                if (consulta.result.length > 0) {
                    consulta.result.forEach((element, index) => {
                        let btn = '';
                        if (element.id != request_init.propietario) {
                            btn = `
                                <button class="btn btn-sm btn-outline-success" name="cliente_item" data-id="${element.id}" 
                                data-nombre="${element.nombres+' '+element.paterno+' '+element.materno}">
                                    Elegir
                                </button>`;
                        }
                        html += `
                            <tr>
                                <td>${element.nombres} ${element.paterno} ${element.materno}</td>
                                <td>${element.documento}</td>
                                <td>
                                    ${btn}
                                </td>
                            </tr>
                        `;
                    });
                }
            }else{
                html = ` <tr class="text-center"><td colspan="3">${consulta.mssg}</td></tr>`;
            }
            app.render('md_tb_cliente', html);
            let items = document.getElementsByName('cliente_item');
            if (items.length > 0) {
                items.forEach(element => {
                    element.addEventListener('click', event => {
                        $('#md_cliente').modal('hide');
                        if (event.target.dataset.id && event.target.dataset.nombre) {
                            form.cliente.value = event.target.dataset.id;
                            form.clienteNombre.value = event.target.dataset.nombre;
                        }
                    })
                });
            }
        } catch (error) {
            app.render('md_tb_cliente', '<tr class="text-center"><td colspan="3">Error al consultar los datos.</td></tr>');
        }
    }
})();