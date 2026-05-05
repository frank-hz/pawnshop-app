(function(d,w){
    let app;
    let sede_selector, sede_loader;
    let controls  = {
        sede_retry_btn: d.getElementById('_btn_sucursal_retry'),
        sede_feed: d.getElementById('_feed_sede')
    }
    
    document.addEventListener('DOMContentLoaded', function(){
        app = new App();
        sede_selector = d.getElementById('_sede_select');
        sede_loader = d.getElementById('_sede_loader');
        if (sede_selector){
            sede_selector.addEventListener('change', evt => {
                let data = new FormData();
                data.append('sucursal_id', evt.target.value);
                if(!create_update_data(data)){
                    get_sucursal_available()
                }
            })
        }

        get_sucursal_available()
    })
    d.addEventListener('click', evt => {
        if (evt.target.matches('#_btn_sucursal_retry')){
            get_sucursal_available()
        }
    })


    async function get_sucursal_available(){
        try {
            controls.sede_retry_btn.classList.toggle('d-none', 1);
            controls.sede_feed.innerHTML = '';
            sede_loader.classList.toggle('hidden', 0);
            sede_selector.innerHTML = '<option value="">Cargando...</option>';
            
            const request = await fetch(app.URL('api/v1/sucursal/get-available-user'),{
                method: 'POST'
            });
            const data = await request.json();
            
            
            let html = '<option value="">- No definido -</option>';
            if (data){
                data.forEach(item => {
                    html += `
                        <option value="${item.id}" ${item.active ? 'selected':''}>
                            ${item.nombre}
                        </option>
                    `;
                })
            }

            setTimeout(() => {
                sede_selector.innerHTML = html;
                sede_loader.classList.toggle('hidden', 1);
            }, 250);
        } catch (error) {
            sede_loader.classList.toggle('hidden', 1);
            sede_selector.innerHTML = `<option value="">- Request Error -</option>`;
            controls.sede_retry_btn.classList.toggle('d-none', 0);
            controls.sede_feed.innerHTML = '[error]';
        }
    } 
    async function create_update_data(data_send){
        try {        
            sede_loader.classList.toggle('hidden', 0);    
            const request = await fetch(app.URL('api/v1/preferencias/create_update'),{
                method: 'POST',
                body: data_send
            })
            const data = await request.json();
            setTimeout(() => {
                sede_loader.classList.toggle('hidden', 1);
            }, 200);
            if (data && data.ok){
                not.Ok(data.ok, '');
                return true;
            }
            if (data.error){
                not.Aviso(data.error, '');
                return;
            }
        } catch (error) {
            sede_loader.classList.toggle('hidden', 1);
            console.log(error);
            not.Error('Error al procesar', '');
            return;
        }
    }

    

})(document,window);