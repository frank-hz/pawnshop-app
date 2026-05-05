(function(){
    let app;
    document.addEventListener('DOMContentLoaded', async() => {
        app = new App();
        aplicar_revision();
    });
    
    document.addEventListener('click', event => {
        if (event.target.matches('#btnIntento')) {
            aplicar_revision();
        }
    })

    const aplicar_revision = function(){
        try {
            let bar = document.getElementById('revision_progreso');
            let html_resp = '';
            app.render('revision_log', 'Iniciando revision...');
            setTimeout(async() => {
                app.render('revision_log', 'Revisando... <i class="fas fa-spinner fa-spin"></i>');
                bar.style.width = '30%';
                let rev = new Revision();
                let procesa = await rev.revision_completa();
                if (procesa.status === 'OK') {
                    bar.style.width = '100%';
                    html_resp += `
                        <ul class="list-group">
                            <li class="list-group-item">
                                <p class="mg-b-0">
                                    <i class="fe fe-check-square tx-primary mg-r-8"></i>
                                    <span class="text-muted">${procesa.calculo}</span>
                                </p>
                            </li>
                            <li class="list-group-item">
                                <p class="mg-b-0">
                                    <i class="fe fe-check-square tx-primary mg-r-8"></i>
                                    <span class="text-muted">${procesa.generacion}</span>
                                </p>
                            </li>
                            <li class="list-group-item">
                                <p class="mg-b-0">
                                    <i class="fe fe-check-circle font-weight-bold tx-success mg-r-8"></i>
                                    <span class="text-muted">Revision completa.</span>
                                </p>
                            </li>
                        </ul>
                        <div class="p-3">
                            <p class="text-blue">Redireccionando en breve....</p>
                        </div>
                    `;
                    // setTimeout(() => {
                    //     window.location.href = srv.url()+'Inicio';
                    // }, 700);
                } else {
                    bar.style.width = '0%';
                    html_resp = `
                        <div class="p-3">
                            <b>${procesa.mssg}</b><br>
                            <button id="btnIntento" class="btn btn-primary">
                                Intentar otra vez
                                <i class="fe fe-rotate-cw"></i>
                            </button>
                            <a href="${srv.url()}Inicio" class="btn btn-secondary">
                                Saltar revision
                                <i class="fe fe-chevrons-right"></i>
                            </a>
                        </div>
                    `;
                }
                app.render('revision_log', procesa.mssg);
                app.render('revision_area', html_resp);
            }, 1000);
            
        } catch (error) {
            app.render('revision_log', 'Error al procesar. ['+error.message+']');
            app.render('revision_area', '');
        }
    }

})();