"use strict";
(function(d, w){
    let app;
    let ROUTE = {
        update: 'api/v1/profile/update'
    }


    d.addEventListener('DOMContentLoaded', function(){
        app = new App();
        console.log(w.sesion_api)
        d.getElementById('form_profile')?.addEventListener('submit', evt => {
            evt.preventDefault();
            if (confirm('Aplicar cambios?')) {
                console.log("init");
                updatex(new FormData(evt.target));
            }
        });
    });




    async function updatex(data){
        try {
            const {ok, message} = await w.sesion_api.profile_update(data);
            // console.log(ok)
            // console.log("entry 2");
            // return;
            if (!ok) {
                not.Aviso('', message || 'Ocurrio un error inesperado.');
                return;
            }
            
            not.Ok(message || 'Proceso completado.', 2000);
            setTimeout(() => {
                window.location.reload();
            }, 500);
        } catch (error) {
            console.log(error.message);
            not.Aviso(error.message, '', 2000);
        }
    }

    async function update_profile(dataUpd){
        try {
            // let request = await fetch(app.URL('api/v1/usuario/update-profile'), {
            //     method: 'POST',
            //     body: dataUpd
            // });
            let request = await fetch(app.URL('api/UsuarioAPI/update'), {
                method: 'POST',
                body: dataUpd
            });
            let resp = await request.json();
            
            if (!request.ok) { throw new Error(resp.message || "Error de servidor."); }
            if (resp.status !== 'OK'){ throw new Error(resp.message || "Error al actualizar."); }

            not.Ok('', resp.message, 2000);
            setTimeout(() => {
                window.location.reload();
            }, 1000);
        } catch (error) {
            console.log(error.message);
            not.Aviso(error.message, '', 2000);
        }
    }
})(document, window);