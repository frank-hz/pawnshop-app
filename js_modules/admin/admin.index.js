(function(d, w){

    d.addEventListener('DOMContentLoaded', function(){

    })
    d.addEventListener('click', event => {
        let ev = event.target;
        if (ev && ev.id === 'btn-remate-orden') {
            if(confirm('¿Aplicar Revision y Ordenamiento?')) {
                remate__ordenamiento_remate()
            }
        }
        if (ev && ev.id === 'btn-usuario-encript') {
            if(confirm('¿Aplicar Encriptacion de Contraseñas?')) {
                usuario__aplicar_encriptacion()
            }
        }
    })
    d.addEventListener('submit', async (event) => {
        event.preventDefault()
        let ev = event.target;
        if (ev && ev.id === 'decrypt-form') {
            let data = new FormData(ev);
            decript_string(data)
        }
        if (ev && ev.id === 'form_util_proxima_cuota') {
            let data = new FormData(ev);
            calculo_cuota_proximo(data)
        }
    })
    
    async function remate__ordenamiento_remate(){
        try {
            let idu = (d.getElementById('usercode-10112022')).value;
            const req = await fetch(srv.url()+'Admin/service_remate_ordenamiento', {
                method: 'POST',
                body: JSON.stringify({user: idu}),
                headers: {
                    'Content-Type': 'json/application'
                }
            })
            const res = await req.json();
            if (Object.keys(res).length) {
                alert(res.message);
                d.getElementById('remate-log-area').innerHTML = res.log;
            }else {
                alert('Error al procesar.');
            }
        } catch (error) {
            alert('Error al procesar.');
            console.log('[Ordenamiento]', error)
        }
    }
    async function usuario__aplicar_encriptacion(){
        try {
            let idu = (d.getElementById('usercode-10112022')).value;
            const req = await fetch(srv.url()+'Admin/service_usuario_encriptacion', {
                method: 'POST',
                body: JSON.stringify({user: idu}),
                headers: {
                    'Content-Type': 'json/application'
                }
            })
            const res = await req.json();
            if (Object.keys(res).length) {
                alert(res.message);
                let htmlresp = '';
                res.log.forEach(e => {
                    htmlresp += `
                        Original: [${e.original}]
                        Encriptado: [${e.encriptado}]
                        Desencriptado: [${e.desencriptado}]
                    `;
                })
                // htmlresp += '</ul>';
                d.getElementById('usuario-log-area').innerHTML = htmlresp;
            }else {
                alert('Error al procesar.');
            }
        } catch (error) {
            alert('Error al procesar.');
            console.log('[Ordenamiento]', error)
        }
    }
    async function decript_string(data_) {
        const req = await fetch(srv.url()+'Admin/service_encrypter', {
            method: 'POST',
            body: data_
        })
        const res = await req.json();
        d.getElementById('decrypt-log-area').innerHTML = res;
    }
    async function calculo_cuota_proximo(data){
        try {
            let req = await fetch(srv.url()+'Admin_api/cuota_calculo_proximo', {
                method: 'POST',
                body: data
            });
            let response = await req.json();
            d.getElementById('calculo_cuota_proximo_area').innerHTML = response.result;
        } catch (error) {
            console.log('[cuota] '+error.message)
        }
    }

})(document, window);