(function(){
    let app;
    document.addEventListener('DOMContentLoaded', event => {
        app = new App();
    })

    if (document.getElementById('frm_prenda_editar')) {
        document.getElementById('frm_prenda_editar').addEventListener('submit', async event => {
            event.preventDefault();
            try {
                app.preload('open', 'guardando...');
                let data = new FormData(event.target);
                let prenda = new Prenda();
                let modifica = await prenda.Modificar(data);

                app.preload('close');
                switch (modifica.status) {
                    case 'OK':
                        app.renderAlert('ok', `<b>${modifica.mssg}</b>`, 2000);
                        break;
                
                    case 'ERROR':
                        app.renderAlert('error', `<b>${modifica.mssg}</b>`, 2000);
                        break;
                    
                    case 'WARN':
                        app.renderAlert('aviso', `<b>${modifica.mssg}</b>`, 2000);
                        break;
                }
            } catch (error) {
                app.renderAlert('error', `<b>Ocurrio un error al procesar</b>`, 2000);
                // console.log(error);
            }

        })
    }

})();