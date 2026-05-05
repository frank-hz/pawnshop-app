(function(d){
    let app;
    let EXCEL_TYPE = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;charset=UTF-8';
	let EXCEL_EXTENSION = '.xlsx';
    let data_export = [];

    d.addEventListener('DOMContentLoaded', event => {
        app = new App();
        sucursal_get_all();
    })
    d.addEventListener('click', event => {
        let ev = event.target;
        if (ev.id === 'btn-export-excel') {
            export_to_excel();
        }
    })


    async function sucursal_get_all(){
        try {
            app.preload('open', 'Cargando...');
            let req = await fetch(srv.url()+'Sucursal/service_get_all');
            let res = await req.json();
            res = res.result;
            app.preload('close');
            
            let html = '';
            if (res.length > 0) {
                // data_export = res;
                let states = {
                    'A': 'Activo',
                    'I': 'Inactivo'
                };
                data_export = [];
                res.forEach((e, index) => {
                    data_export.push(e);
                    html += `
                        <tr>
                            <td class="text-center">${index+1}</td>
                            <td>${e.nombre}</td>
                            <td>${e.ciudad}</td>
                            <td>${e.direccion}</td>
                            <td>${states[e.estado]}</td>
                            <td>
                                <div class="d-flex justify-center">
                                    <a href="${srv.url()}Sucursal/detalle/${e.id}" name="sucursal-edit" class="btn-cell" data-id="${e.id}" title="Ver Sucursal">
                                        <span class="fe fe-layers"></span> 
                                    </a>
                                </div>
                            </td>
                        </tr>
                    `;
                })
            }else{
                html = '<tr><td colspan="6" class="text-center">Sin Resultados</td></tr>';
            }
            app.render('datatable-sucursal', html);

        } catch (error) {
            app.preload('close');
            html = '<span>Error al cargar</span>';
            console.log(error)
        }
    }
    function export_to_excel(){
        let worksheet = XLSX.utils.json_to_sheet(data_export);
        let workbook = {
            Sheets: {
                'data': worksheet
            },
            SheetNames: ['data']
        };
        let excelBuffer = XLSX.write(workbook, {bookType: 'xlsx', type: 'array'})
        var data = new Blob([excelBuffer], {type: EXCEL_TYPE});
        saveAs(data,'Electrocash - Lista de Sucursales'+'_'+EXCEL_EXTENSION);
    }
})(document);