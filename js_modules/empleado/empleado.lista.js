(function(){
    let app;
    let request_init = {tipo: 'nombres', buscar: '', limite: 10};
     
    document.addEventListener('DOMContentLoaded', (e)=>{
        app = new App();
        Empleados();
        try {
            let items = document.getElementsByName('tipo_filtro');
            if (items.length > 0) {
                items.forEach(element => {
                    element.addEventListener('change', event => {
                        request_init.tipo = event.target.value;
                        Empleados();
                    })
                });
            }
        } catch (error) {
            
        }
    });
    if (document.getElementById('check_darkmode')) {
        document.getElementById('check_darkmode').addEventListener('change', event => {
            try {
                let tb = document.getElementById('tabla_empleado');
                if (event.target.checked) tb.classList.add('table-dark');
                else tb.classList.remove('table-dark');
            } catch (error) {
                console.log('darkmode::error', error.getMessage());
            }
        })
    }
    if (document.getElementById('inp_buscar')) {
        document.getElementById('inp_buscar').addEventListener('keyup', event => {
            try {
                request_init.buscar = event.target.value;
                Empleados();
            } catch (error) {
                console.log(error);
            }
        })
    }
    if (document.getElementById('inp_limite')) {
        document.getElementById('inp_limite').addEventListener('change', event => {
            try {
                request_init.limite = event.target.value;
                Empleados();
            } catch (error) {
                console.log(error);
            }
        })
    }

    const Empleados = async function(){
        try {
            app.render('tb_empleado', '<tr class="text-center"><td colspan="6" class="text-blue"><div class="loader"></div> Cargando...</td></tr>');
            let empleado = new Empleado();
            const consulta = await empleado.Listar(request_init.tipo, request_init.buscar, request_init.limite);

            let html = '';
            if (consulta.status === 'OK') {
                if (consulta.result.length > 0) {
                    consulta.result.forEach((element, index) => {
                        let estado = '';
                        switch (element.estado) {
                            case 'A':
                                estado = `<span class="stag stag-emp-A">Activo</span>`;
                                break;
                            case 'I':
                                estado = `<span class="stag stag-emp-I">Inactivo</span>`;
                                break;
                            default:
                                break;
                        }
                        html += `
                            <tr>
                                <td class="text-center">${parseInt(index)+1}</td>
                                <td>${element.nombres} ${element.paterno} ${element.materno}</td>
                                <td>${element.documento}</td>
                                <td>${element.sucursal}</td>
                                <td>${element.direccion}</td>
                                <td class="text-center">
                                    <a class="btn btn-primary" href="${srv.url()}Empleado/detalle/${element.id}" title="Ver Detalle">
                                        <i class="fe fe-layers"></i>   
                                    </a>
                                </td>
                            </tr>
                        `;
                    });
                }else{
                    html = `
                        <tr class="text-center">
                            <td colspan="6">No se encontraron resultados.</td>
                        </tr>
                    `;
                }
            }else{
                html = `
                    <tr class="text-center">
                        <td colspan="6">Ocurrio un error al consultar los datos.</td>
                    </tr>
                `;
            }
            app.render('tb_empleado', html);            
        } catch (error) {
            app.render('tb_empleado', '<tr class="text-center"><td colspan="6" class="text-blue">Error al consultar</td></tr>');
        }
        
    }

})();