(function(){
    let app;

    document.addEventListener('DOMContentLoaded', (e)=>{
        app = new App();
        // Sucursales();
    })

    const Sucursales = async function(tipo = '',  buscar = ''){
        try {
            app.render('sucursalLista', '<tr class="text-center"><td colspan="6"><div class="loader"></div></td><tr>');      
            let sucursal = new Sucursal();
            const consulta = await sucursal.Listar(tipo, buscar);
            let html = '';
            if (consulta.code === 'Ok') {
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
                            default:
                                break;
                        }
                        html += `
                            <tr>
                                <td class="text-center">${parseInt(index)+1}</td>
                                <td>${element.descripcion}</td>
                                <td>${element.ciudad}</td>
                                <td>${element.direccion}</td>
                                <td>${estado}</td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-outline-primary border dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                                        Accion
                                    </button>
                                    <div class="dropdown-menu" x-placement="bottom-start" style="position: absolute; transform: translate3d(0px, 38px, 0px); top: 0px; left: 0px; will-change: transform;">
                                    <a class="dropdown-item" href="${srv.url()}Sucursal/detalle/${element.id}">Ver detalle</a>
                                    <a class="dropdown-item" href="${srv.url()}Sucursal/editar/${element.id}">Editar</a>
                                    </div>
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
            app.render('sucursalLista', html); 
        } catch (error) {
            console.log(error);
            app.render('sucursalLista', '<tr class="text-center"><td colspan="6">Ocurrio un error al procesar la solicitud</td><tr>'); 
        }
        
    }

})();