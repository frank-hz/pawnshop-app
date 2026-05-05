(function(d,w){
    let app;
    const EXCEL_TYPE = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;charset=UTF-8';
	const EXCEL_EXTENSION = '.xlsx';
    let dataExport = [];
    let dataBalanceSemanal = [];

    d.addEventListener('DOMContentLoaded', ()=>{
        app = new App();
        dataExport = window.creditos ?? [];
        dataBalanceSemanal = window.balance_semanal ?? [];
        GraficoBalanceSemanal();
    })

    d.getElementById('btn_exportar')?.addEventListener('click', evt => {
        try {
            evt.target.classList.add('btn-loading')
            evt.target.disabled = true;

            const worksheet = XLSX.utils.json_to_sheet(dataExport);
            const workbook = {
                Sheets: {
                    'data': worksheet
                },
                SheetNames: ['data']
            };
            const excelBuffer = XLSX.write(workbook, {bookType: 'xlsx', type: 'array'})
            excel_export_file(excelBuffer, 'Electrocash - Creditos Activos');
        } catch (error) {
            console.log(error);
        } finally {
            evt.target.classList.remove('btn-loading');
            evt.target.disabled = false;
        }
        
    })




    function GraficoBalanceSemanal(){
        const labels = dataBalanceSemanal.map(item => item.nombre);
        const data = {
        labels: labels,
        datasets: [{
            label: 'Balance',
            data: dataBalanceSemanal.map(item => item.balance),
            fill: false,
            borderColor: '#3E54D3',
            tension: 0.1
        }]
        };

        const ctx = document.getElementById('chartBalance').getContext('2d');
        const config = {
            type: 'line',
            data: data,
            options: {
              responsive: true,
              plugins: {
                title: {
                  display: true,
                  text: 'Balance Ultimos 7 Dias'
                },
                tooltip: {
                    callbacks: {
                      label: function (context) {
                        // Formato de dinero para tooltip
                        let value = context.raw;
                        return `S/ ${value}`;
                      }
                    }
                }
              },
              interaction: {
                mode: 'index',
                intersect: false
              },
              scales: {
                x: {
                  display: true,
                  title: {
                    display: true,
                    text: 'Semana'
                  }
                },
                y: {
                  display: true,
                  title: {
                    display: true,
                    text: 'Monto'
                  }
                }
              }
            },
        };

        const chart = new Chart(ctx, config);
    }


    function excel_export_file(buffer, filename) {
		var data = new Blob([buffer], {type: EXCEL_TYPE});
		// saveAs(data,filename+'_export_'+new Date().getTime()+EXCEL_EXTENSION);
		saveAs(data,filename+'_'+EXCEL_EXTENSION);
	}

})(document, window);