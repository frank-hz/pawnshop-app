(function(d){
    let app;

    d.addEventListener('DOMContentLoaded', () => {
        app = new App();
        usuarios_render_data();
    })

    async function usuarios_render_data(){
        try {
            let req = await fetch(srv.url()+'Usuario/service_get_all');
            let res = await req.json();
            
            let html = '';
            if (res && res.length > 0) {
                let etags = {
                    'I': '<span class="stag stag-usu-I">Inactivo</span>',
                    'A': '<span class="stag stag-usu-A">Activo</span>'
                }
                res.forEach((e, ind) => {
                    let nombrecomp = e.nombre+' '+e.paterno+' '+e.materno;
                    html += `
                        <tr>
                            <td class="text-center">${ind+1}</td>
                            <td>
                                <a href="${srv.url()}Empleado/detalle/${e.id_empleado}" class="btn-link text-indigo font-weight-500">
                                    ${nombrecomp}
                                </a>
                            </td>
                            <td>${e.usuario}</td>
                            <td>${e.rol}</td>
                            <td>${etags[e.estado]}</td>
                            <td>
                                <a href="${srv.url()}Usuario/detalle/${e.id}" name="sucursal-edit" class="btn-cell" data-id="${e.id}" title="Ver Usuario">
                                    <span class="fe fe-layers"></span> 
                                </a>
                            </td>
                        </tr>   
                    `;
                })
            }else {
                html = `
                    <tr>
                        <td class="text-center" colspan="6">Sin Datos.</td>
                    </tr>
                `;
            }
            app.render('datatable-usuario', html);
        } catch (error) {
            
        }
    }

})(document);