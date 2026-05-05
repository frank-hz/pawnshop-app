(function(d,w){
    let app;
    var email_stash = '';
    document.addEventListener('DOMContentLoaded', event => {
        app = new App();
        
    })
    if (document.getElementById('check_darkmode')) {
        document.getElementById('check_darkmode').addEventListener('change', event => {
            document.getElementById('table_detalle').classList.toggle('table-dark');
        })
    }
    if(document.getElementById('btn_anulacion')){
        document.getElementById('btn_anulacion').addEventListener('click', async event => {
            try {
                if (event.target.dataset.pago && event.target.dataset.credito) {
                    if (confirm('Anular el pago?')) {
                        app.preload('open', 'Procesando..');

                        let pago = new Pago();
                        let data = new FormData();
                        data.append('credito_id', event.target.dataset.credito);
                        data.append('pago_id', event.target.dataset.pago);
                        let procesa = await pago.Anular(data);
                        
                        app.preload('close');
                        if (procesa.status === 'OK') {
                            not.Ok(procesa.mssg, '', 3000);
                            setTimeout(()=>{
                                window.location.href = srv.url()+'Credito/detalle/'+event.target.dataset.credito;
                            }, 600);
                        }else{
                            if(procesa.status === 'WARN') not.Aviso(procesa.mssg, '', 3000);
                            else not.Error(procesa.mssg, '', 3000);
                        }
                    }
                }else{
                    not.Aviso('Error al reconocer el pago', '', 2500);
                }
            } catch (error) {
                console.log(error);
                app.preload('close');
                not.Error('Error al procesar', '', 3000);
            }
        })
    }
    if (document.getElementById('controlSendMail')) {
        document.getElementById('controlSendMail').addEventListener('click', async (event) => {
            if (event.target.dataset.pago && event.target.dataset.pago > 0) {
                email_stash = event.target.dataset.email ? event.target.dataset.email : '';

                if (!email_stash || email_stash.length <= 0) {
                    const { value: valueEmail } = await Swal.fire({
                        title: 'Ingrese un email valido',
                        input: 'text',
                        inputLabel: 'El cliente no cuenta con un email registrado, se requiere de uno para enviar el voucher virtual.',
                        inputPlaceholder: 'ingresar email...'
                    })
                    email_stash = valueEmail;
                }

                if (email_stash) {
                    // Swal.fire(`Entered email: ${email}`)
                    var data = new FormData();
                    data.append('id', event.target.dataset.pago);
                    data.append('email', email_stash);
                    var req = await fetch(srv.url()+'Pago/enviarEmail', {
                        method: 'POST',
                        body: data
                    })
                    var res = await req.json();
                    Swal.fire(res.code, res.message, 'success')
                }

            }else{
                alert('id no encontrado');
            }
        })
    }
    d.addEventListener('click', event => {
        let ev = event.target;

        if (ev.id === 'btn-preview') {
            shadow_modal('open');
        }
        if (ev.matches('[name=modal-button]')) {
            console.log('modal button')
            if (ev.dataset.action === 'modal-close') {
                shadow_modal('close');
            }
            if (ev.dataset.action === 'modal-download') {
                let code = (ev.dataset.code) ? ev.dataset.code : '';
                invoice_screenshot(code);
            }
        }

    })


    function shadow_modal(action){
        let mod = d.getElementById('modal-invoice');
        switch (action) {
            case 'open':
                document.body.style = 'overflow: hidden;';
                mod.style.display = 'flex';
                break;
        
            case 'close':
                document.body.style = 'overflow: auto;';
                mod.style.display = 'none';
                break;
        }
    }

    function invoice_screenshot(code = ''){
        html2canvas(document.getElementById('invoice'), {
            letterRending: 1,
            allowTaint: true,
            useCORS: true
        })
        .then(function(canvas){
            // var img = document.getElementById('data-preview');
            // img.src = canvas.toDataURL('image/png', 0.5);
            var a_link = document.createElement('a')
            a_link.href = canvas.toDataURL('image/png', 0.5);
            a_link.download = `electrocash_voucher_${code}.png`;
            a_link.click();


        })
        .catch(e => {
            alert('Error: ', e.message)
        })
    }

})(document, window);