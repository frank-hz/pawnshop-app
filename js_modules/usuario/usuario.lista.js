(function(){

    document.addEventListener('DOMContentLoaded', event => {
        Usuarios();
    })


    const Usuarios = async function(tipo = 'lista', buscar = ''){
        try {
            let app = new App();
            app.render('tdata__usuario', `<tr class="text-center"><td class="colspan="5"><i class="fas fa-spinner fa-pulse"></i>Cargando...</td></tr>`);

            let usuario = new Usuario();
            let consulta = await usuario.Listar(tipo, buscar);

            let html = '';
            if (consulta.code === 'Ok') {
                consulta.resp.forEach((element,index) => {
                    let estado = '';
                    switch (element.estado) {
                        case 'A':
                            estado = '<span class="stag stag-usu-A">Activo</span>';
                            break;
                    
                        case 'I':
                            estado = '<span class="stag stag-usu-I">Inactivo</span>';
                            break;
                    }
                    html += `
                        <tr>
                            <td>${index+1}</td>
                            <td>${element.empleado}</td>
                            <td>${element.usuario}</td>
                            <td>${element.rol}</td>
                            <td>${estado}</td>
                            <td class="text-center">
                                <a class="btn btn-primary" href="${srv.url()}Usuario/detalle/${element.id}" title="Ver Detalle">
                                    <i class="fe fe-layers"></i>
                                </a>
                            </td>  
                        </tr>
                    `;
                });
            }else{
                html = `
                    <tr class="text-center">
                        <td colspan="5">No se encontraron resultados.</td>
                    </tr>
                `;
            }
            app.render('tdata__usuario', html);
        } catch (error) {
            console.log(error);
        }
    }

})();