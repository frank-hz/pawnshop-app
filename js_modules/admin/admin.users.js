(function(d){
    let app;

    d.addEventListener('DOMContentLoaded', ()=> {
        app = new App();
    })
    d.addEventListener('click', event => {
        let ev = event.target;
        if (ev.name === 'encrypt-item') {
            if (!ev.dataset.id || ev.dataset.id.length <= 0) return;
            if (confirm('Encriptar Clave?')) encrypt_password(ev.dataset.id);
        }
        if (ev.name === 'reset-item') {
            if (!ev.dataset.id || ev.dataset.id.length <= 0) return;
            if (confirm('Reiniciar Clave?')) reset_password(ev.dataset.id);

        }
    })
    d.addEventListener('change', event => {
        let ev = event.target;
        if (ev.name === 'state-item') {
            if (!ev.dataset.id || !ev.value) return;
            console.log(ev.dataset.id)
            if (confirm('Cambiar Estado?')) update_state(ev.dataset.id, ev.value);

        }
    })

    async function encrypt_password(id){
        try {
            let req = await fetch(srv.url()+'Admin/service_user_encrypt',{
                method: 'POST',
                body: JSON.stringify({id: id})
            });
            let res = await req.json();
            if (res && res.status === 'OK') {
                not.Ok(res.message,'',3000);
                setTimeout(() => {
                    window.location.reload();
                }, 500);
            }else {
                not.Aviso(res.message,'',3000);
            }
        } catch (error) {
            not.Aviso('Error al procesar','',2500);
        }
    }
    async function reset_password(id){
        try {
            let req = await fetch(srv.url()+'Admin/service_user_reset',{
                method: 'POST',
                body: JSON.stringify({id: id})
            });
            let res = await req.json();
            if (res && res.status === 'OK') {
                not.Ok(res.message,'',3000);
                window.location.reload();
            }else {
                not.Aviso(res.message,'',3000);
            }
        } catch (error) {
            not.Aviso('Error al procesar','',2500);
        }
    }
    async function update_state(id,state){
        try {
            let req = await fetch(srv.url()+'Admin/service_user_set_state',{
                method: 'POST',
                body: JSON.stringify({id: id, estado: state})
            });
            let res = await req.json();
            if (res && res.status === 'OK') {
                not.Ok(res.message,'',3000);
            }else {
                not.Aviso(res.message,'',3000);
            }
        } catch (error) {
            not.Aviso('Error al procesar','',2500);
        }
    }

})(document);