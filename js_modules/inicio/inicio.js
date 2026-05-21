"use strict";
(function(d,w){
  let app;
  const ROUTES = {
    resumen_dia: 'api/v1/inicio/resumen-dia'
  }
  const EXCEL_CONFIG = {
    type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;charset=UTF-8',
    extension: '.xlsx'
  }
  const ids = {
    tbody_reporte: '_DATA_REPORTE_'
  }
  const reporte = {
    creditosActivos : [],
    dataExport : [],
    dataBalanceSemanal: [],
    filtro: 'TODO',
    imageExportName: 'Resumen'
  }

  d.addEventListener('DOMContentLoaded', ()=>{
    app = new App();
    reporte.creditosActivos = window.creditos ?? [];
    reporte.dataExport = window.creditos ?? [];
    reporte.dataBalanceSemanal = window.balance_semanal ?? [];

    renderCreditosActivos();
    renderGraficoBalanceSemanal();
  })

  d.querySelectorAll('[name=REPORTE_FILTROS]').forEach(filtro => {
    filtro.addEventListener('change', evt => {
      if (!["TODO","VENCE_HOY"].includes(evt.target.value)){
        not.Aviso("","Filtro invalido.",2000);
        return;
      }
      reporte.filtro = evt.target.value;
      renderCreditosActivos();
    })
  })

  d.getElementById('btn_reporte_download')?.addEventListener('click', evt => {
    ExportarReporte();
    
  })
  d.getElementById('btn_resumen_download')?.addEventListener('click', evt => {
    downloadCaptureScreen();
    
  })

  // MODAL RESUMEN
  d.getElementById('btn_resumen_close')?.addEventListener('click', evt => {
    let modal = d.getElementById('VIEW_RESUMEN_DIA');
    modal.classList.remove('isActive');
  })
  d.getElementById('btn_resumen_open')?.addEventListener('click', evt => {
    let modal = d.getElementById('VIEW_RESUMEN_DIA');
    modal.classList.add('isActive');
    renderDataResumenDia();
  })
  
  d.getElementById('VIEW_RESUMEN_DIA')?.querySelector('.resumen-grid').addEventListener('click', (e) => {
    e.stopPropagation();
    
  });

  d.getElementById('VIEW_RESUMEN_DIA')?.addEventListener('click', evt => {
    d.getElementById('VIEW_RESUMEN_DIA').classList.remove('isActive');
  });
  


  // F U N C T I O N S
  async function renderCreditosActivos(){
    let table = d.getElementById(ids.tbody_reporte);
    try {
      table.innerHTML = `
        <tr>
          <td colspan="8" class="text-center py-3">
            Cargando... <span class="spinner-v1-small"></span>
          </td> 
        </tr>
      `;
      await app.deadTime(100);

      if (reporte.creditosActivos.length == 0) {
        table.innerHTML = `<tr><td colspan="8" class="text-center">Sin Datos</td></tr>`;
        return;
      }

      let dataProcesada = (reporte.filtro === 'VENCE_HOY') 
        ? reporte.creditosActivos.filter(item => item.vence_hoy === 'SI')
        : reporte.creditosActivos;

      if (dataProcesada.length == 0) {
        table.innerHTML = `<tr><td colspan="8" class="text-center">Sin Datos</td></tr>`;
        return;
      }

      table.innerHTML = dataProcesada.map((credito,ind) => {
        return `
          <tr>
            <td>${ind+1}</td>
            <td>
              <a href="${app.URL('credito/detalle/') + credito.id}">
                ${ credito.serie }
              </a>
            </td>
            <td>
              <a href="${app.URL('cliente/detalle/') + credito.cliente_id}">
                ${ credito.cliente_nombre }
              </a>
            </td>
            <td class="text-center">
              <span class="enc1217-conducta-sm-tag enc1212-conducta-tag-${credito.cliente_puntuacion}">
                ${ credito.cliente_puntuacion }
              </span>
            </td>
            <td>S/ ${ credito.monto }</td>
            <td> ${ credito.pago_monto} </td>
            <td> ${ credito.pago_proximo} </td>
            <td> ${ credito.ultimo_pago} </td>            
            <td class="text-center">${ credito.cuotas_vencidas }</td>
            <td>
              <a href="${app.URL("prenda/detalle/" + credito.prenda_id)}">
                ${ app.shortText(credito.prenda_descripcion) }
              </a>
            </td>
          </tr>
        `
      }).join('');
    } catch (error) {

      table.innerHTML = `
        <tr>
          <td colspan="10" class="text-center">${error.message}.</td>
        </tr>
      `;
      console.log(error.message);
    }
  }
  function ExportarReporte(){
    const worksheet = XLSX.utils.json_to_sheet(reporte.dataExport);
    const workbook = {
      Sheets: {
        'data': worksheet
      },
      SheetNames: ['data']
    };
    const excelBuffer = XLSX.write(workbook, {bookType: 'xlsx', type: 'array'})
    exportar_archivo_excel(excelBuffer, 'Electrocash - Creditos Activos');
  }
  function exportar_archivo_excel(buffer, filename) {
		var data = new Blob([buffer], {type: EXCEL_CONFIG.type});
		saveAs(data,filename+'_'+EXCEL_CONFIG.extension);
  }
  function renderGraficoBalanceSemanal(){
    const labels = reporte.dataBalanceSemanal.map(item => item.nombre);
    const data = {
    labels: labels,
    datasets: [{
      label: 'Balance',
      data: reporte.dataBalanceSemanal.map(item => item.balance),
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
  async function getDataResumenDia(){
    try {
      let data_send = new FormData();
      data_send.append('fecha', '2024-12-23'); 
      console.log(ROUTES.resumen_dia)
      let req = await fetch(app.URL(ROUTES.resumen_dia), {
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
  async function renderDataResumenDia(){
    let data_request = await getDataResumenDia();
    
    if (!data_request?.result){
      console.log('success');
      return;
    }
    console.log(data_request.result)
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

    let recuperados = pagos.filter(pago => pago.concepto === 'REC').length;
    setData('RESUMEN_TITLE', `RESUMEN [${fecha}]`);
    setData('CREDITOS_CREADOS', creditos.length ?? '--');
    setData('PAGOS_RECIBIDOS', pagos.length ?? '--');
    setData('RECUPERACIONES', recuperados ?? '--');


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