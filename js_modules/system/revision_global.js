(function(d,w){
    let app;
    
    document.addEventListener('DOMContentLoaded', async() => {
        app = new App();

        
        startProcess();
    })

    d.addEventListener('click', evt => {
        if (evt.target.matches('#_button_retry')){
            startProcess(200)
        }
    })

    function startProcess(t = 300){
        setTimeout(() => {
            RevisionProcess()
        }, t);
    }

    function ShowStatus(status_){
        let taskVerificacion = d.getElementById('_task_verificacion');
        let taskGeneracion = d.getElementById('_task_generacion');
        let taskCalculo = d.getElementById('_task_calculo');
        let themes = {
            'progress': 'task-in-progress',
            'success': 'task-success',
            'error': 'task-error'
        }

        taskVerificacion.className = 'file-task';
        taskGeneracion.className = 'file-task';
        taskCalculo.className = 'file-task';

        taskVerificacion.classList.add(themes[status_])
        taskGeneracion.classList.add(themes[status_])
        taskCalculo.classList.add(themes[status_])

    }

    async function RevisionProcess(){
        let log = d.getElementById('_progress_log');
        log.innerHTML = '';
        log.classList.remove('text-danger');
        
        ShowStatus('progress');
        ShowProgress(25);

        await fetch(app.URL('api/v1/revision/global'),{
            method: 'POST'
        })
        .then(response => {
            ShowProgress(50);
            if (!response.ok) { throw new Error('No se pudo procesar la solicitud') }
            return response.json();
        })
        .then(data => {
            if (!data || !data.ok){
                throw new Error('Proceso no completado')
            }
            d.getElementById('_value_generacion').innerHTML = data.generacion + ' cuotas';

            ShowProgress(100);
            ShowStatus('success');  
            d.getElementById('_final_result').innerHTML = `
                Revision Completa 
                <i class="far fa-check-circle"></i>
            `; 
            setTimeout(() => {
                window.location.href = app.URL();
            }, 500);        
        })
        .catch(err => {
            console.warn(err);
            ShowProgress(0);
            ShowStatus('error');
            log.innerHTML = `
                ${err.message} &nbsp;
                <a href="javascript:void(0)" id="_button_retry" class="btn-link">
                    Reintentar
                </a>
            `;
            log.classList.add('text-danger');            
            return;
        })
    }

    function ShowProgress(porcentual, message = ''){
        let progress = d.getElementById('_progress');
        let log = d.getElementById('_progress_log');

        progress.style.width = porcentual + '%';
        progress.classList.toggle('bg-primary', porcentual == '100');
        log.innerHTML = message;
    }
    // task-in-progress
    // task-success
    // task-error
    // task-pending
})(document,window);