(function(d,w){
  let app;
  const EXCEL_TYPE = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;charset=UTF-8';
	const EXCEL_EXTENSION = '.xlsx';
  let dataExport = [];
  let dataBalanceSemanal = [];
  const reporte = {
    creditosActivos : [],
    dataExport : [],
    dataBalanceSemanal: [],
    filtro: 'TODO',
    imageExportName: 'Resumen'
  }


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
  d.getElementById('btn_resumen_open')?.addEventListener('click', evt => {
    let modal = d.getElementById('VIEW_RESUMEN_DIA');
    modal.classList.add('isActive');
    renderResumenDia();
  });
  d.getElementById('btn_resumen_close')?.addEventListener('click', evt => {
    let modal = d.getElementById('VIEW_RESUMEN_DIA');
    modal.classList.remove('isActive');
  });
  d.getElementById('VIEW_RESUMEN_DIA')?.querySelector('.resumen-grid').addEventListener('click', evt => {
    evt.stopPropagation();
  });
  d.getElementById('VIEW_RESUMEN_DIA')?.addEventListener('click', evt => {
    evt.target.classList.remove('isActive');
  });
  d.getElementById('btn_resumen_download')?.addEventListener('click', evt => {
    downloadCaptureScreen();
  });





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


  async function renderResumenDia(){
    let data_request = await getDataResumenDia();
    
    if (!data_request?.result){
      console.log('success');
      return;
    }

    const {
      fecha,
      total_ingreso,
      total_egreso,
      balance,
      creditos = [],
      pagos = [],
      sede
    } = data_request.result;
    
    fecha && (reporte.imageExportName = `Resumen - ${fecha}`);
    
    const setData = (id, text) => {
      const elm = document.getElementById(id);
      if (elm) elm.textContent = text;
    }
    const setRowLoader = (id, cspan) => {
      const row = document.getElementById(id);
      row.innerHTML = `
        '<tr>
          <td class="text-center" colspan="${cspan}">
            Cargando... 
          </td>
        </tr>'
      `;
    }

    setData('RESUMEN_TITLE', `RESUMEN [${fecha}]`);
    setData('TOTAL_INGRESO', `S/ ${total_ingreso}`);
    setData('TOTAL_EGRESO', `S/ ${total_egreso}`);
    setData('TOTAL_BALANCE', `S/ ${balance}`);
    setData('SEDE_NAME', `${sede}`);
    


    setRowLoader('TABLE_CREDITOS', 5);
    setRowLoader('TABLE_PAGOS', 6);
    app.deadTime(500);

    let tableCreditos = document.getElementById('TABLE_CREDITOS');
    if (!tableCreditos) return;

    if (creditos.length > 0){
      tableCreditos.innerHTML = creditos.map(c => {
        return `
          <tr>
            <td>${c.serie}</td>
            <td>${c.cliente_nombre_full}</td>
            <td>${c.prenda_descripcion}</td>
            <td>-----</td>
            <td>S/ ${c.monto}</td>
          </tr>
        `;
      }).join('');

      tableCreditos.innerHTML += `
        <tfoot>
          <tr>
            <td colspan="4" class="text-right font-weight-bold">TOTAL</td>
            <td colspan="1" class="font-weight-bold">S/ ${total_egreso}</td>
          </tr>
        </tfoot>
      `;
    }else {
      tableCreditos.innerHTML = `
        <tr>
          <td class="text-center text-blue2" colspan="5">
            Sin Datos
          </td>
        </tr>'
      `
    }

    let tablePagos = document.getElementById('TABLE_PAGOS');
    if (!tablePagos) return;

    if (pagos.length > 0){
      tablePagos.innerHTML = pagos.map(p => {
        return `
          <tr>
            <td>${p.serie}</td>
            <td>${p.cliente_nombre_full}</td>
            <td>${p.prenda_descripcion}</td>
            <td>${p.forma_pago}</td>
            <td>${p.concepto}</td>
            <td>S/ ${p.total}</td>
          </tr>
        `;
      }).join('')

      tablePagos.innerHTML += `
        <tfoot>
          <tr>
            <td colspan="5" class="text-right font-weight-bold">TOTAL</td>
            <td colspan="1" class="font-weight-bold">S/ ${total_ingreso}</td>
          </tr>
        </tfoot>
      `;
    }else {
      tablePagos.innerHTML = `
        <tr>
          <td class="text-center text-blue2" colspan="6">
            Sin Datos
          </td>
        </tr>'
      `;
    }
  }
  async function getDataResumenDia(){
    try {
      let data_send = new FormData();
      // data_send.append('fecha', '2026-04-20'); 
      let req = await fetch(app.URL('api/resumen/get_resumen_dia'), {
        method: 'POST',
        body: data_send
      });
      let resp = await req.json();
      if (!resp){
        throw Error("Error de procesamiento.");
      }
      return resp;
    } catch (error) {
      console.log(`Error: ${error.message}`);
      return;
    }
  }
  function downloadCaptureScreen(){
    try {
      const view = d.getElementById('RESUMEN_VIEW');
      const clone = view.cloneNode(true);
      clone.style.overflow = "visible";
      clone.style.maxHeight = "none";
      clone.style.position = "absolute";
      clone.style.left = "-9999px";
      document.body.appendChild(clone);
      
      html2canvas(clone).then(canvas => {
        const link = d.createElement('a');
        link.download = `${reporte.imageExportName}.jpg`;
        link.href = canvas.toDataURL(clone);
        link.click();
        document.body.removeChild(clone);
      });
    } catch (error) {
      console.log(error.message);
    }
    
  }

})(document, window);