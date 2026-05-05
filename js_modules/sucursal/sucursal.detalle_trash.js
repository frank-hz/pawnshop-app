(function(){
    let id;
    let app;
    document.addEventListener('DOMContentLoaded', event => {
        app = new App();
        id = document.getElementById('_id').value;
        Empleados(id);
    });

    if (document.getElementById('EmpleadoCargar')) {
        document.getElementById('EmpleadoCargar').addEventListener('click', event => {
            Empleados(id);
        })
    }

    if (document.getElementById('optEliminar')) {
        document.getElementById('optEliminar').addEventListener('click', async(event) => {
            event.preventDefault();
            try {
                if (event.target.dataset.id) { 
                    if (event.target.dataset.id) {
                        const aswer = Swal.fire({
                            type: 'warning',
                            title: '¿Eliminar la sucursal?',
                            showCancelButton: true,
                            confirmButtonColor: '#E92C4C',
                            confirmButtonText: 'Si, eliminar',
                            cancelButtonColor: '#283845',
                            cancelButtonText: 'Cancelar'
                        }).then(async(result) => {
                            if (result.value) {
                                let app = new App();
                                app.preload('open', 'Procesando eliminacion..');
                                let sucursal = new Sucursal();
                                let elimina = await sucursal.Eliminar(event.target.dataset.id);
                                
                                app.preload('close');
                                if (elimina.status === 'OK') {
                                    app.renderAlert('ok', `<b>${elimina.mssg}</b><p>volviendo a la lista...</p>`);
                                    setTimeout(() => {
                                        window.location.href = srv.url()+'Sucursal';
                                    }, 500);
                                }else{
                                    let tipo = (elimina.status === 'WARN') ? 'aviso' : 'error';
                                    app.renderAlert(tipo, elimina.mssg, 3000);
                                }
                            }
                        });
                    }  
                }
            } catch (error) {
                let app = new App();
                app.renderAlert('error', 'ocurrio un problema al procesar', 3000);
                // not.Error('Eliminar', 'ocurrio un problema al procesar.');
            }
        })
    }

    const Empleados = async function(id){
        try {
            let app = new App();
            app.render('tb_empleado', `<tr class="text-center"><td colspan="6"><i class="fas fa-spinner fa-pulse"></i>Cargando...</td></tr>`);

            let sucursal = new Sucursal();
            let consulta = await sucursal.ObtenerEmpleados(id);
            
            let data_html = '';
            if (consulta.status === 'OK') {
                if (consulta.resp.length > 0) {
                    consulta.resp.forEach((element, index) => {
                        let estado = '';
                        switch (element.estado) {
                            case 'A':
                                estado = `<span class="stag stag-emp-A">Activo</span>`;
                                break;
                        
                            case 'I':
                                estado = `<span class="stag stag-emp-I">Inactivo</span>`;
                                break;
                        }
                        data_html += `
                            <tr>
                                <td>${index+1}</td>
                                <td>${element.nombres} ${element.paterno} ${element.materno}</td>
                                <td>${element.documento}</td>
                                <td>${element.telefono}</td>
                                <td>${estado}</td>
                                <td>
                                    <a class="btn btn-sm btn-outline-primary" href="${srv.url()}Empleado/detalle/${element.id}">Ver Detalle</a>
                                </td>
                            </tr>
                        `;
                    });
                }else{
                    data_html = `<tr class="text-center"><td colspan="6">No se encontraron datos.</td></tr`;
                }
            }else{
                data_html = `<tr class="text-center"><td colspan="6">Error al consultar.${consulta.mssg}</td></tr`;
            }
            app.render('tb_empleado', data_html);
        } catch (error) {
            console.log(error);
        }
    }

})();