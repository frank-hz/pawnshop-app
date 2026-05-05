"use strict";
(function(d, w){
    var app = new App();

    d.addEventListener('DOMContentLoaded', () => {
        app = new App();
    })

    d.addEventListener('click', (event) => {
        let ev = event.target;
        if (ev && ev.id === 'pass_rvl') {
            let inp_pssw = d.getElementById('input-password');
            let new_value = inp_pssw.type === 'password' ? 'text' : 'password';
            inp_pssw.type = new_value;
            render_password_icon(new_value)
        }
    })

    d.getElementById('login-form').addEventListener('submit', async event => {
		event.preventDefault()
		try {
			btn_preload('start')
            ttoast('close')
			const data = new FormData(event.target)
			const req = await fetch(app.app_url()+'api/v1/user/auth', {
				method: 'POST',
				body: data
			})
			const res = await req.json();
			btn_preload('close')
			if (Object.keys(res) && res.status === 'OK') {
                vapor.float_loading('open')
				ttoast('open', {
                    title: 'Autenticacion Exitosa',
                    message: res.message,
                    style: 'primary'
                })
				setTimeout(()=>{
					window.location.href = app.app_url()+'App/revision';
				}, 750);
			} else{
				ttoast('open', {
                    title: 'Cuidado!',
                    message: res.message,
                    style: 'warning'
                })
			}
		} catch (error) {
			btn_preload('close')
			ttoast('open', {
                title: 'Cuidado!',
                message: 'Error al procesar',
                style: 'danger'
            })
            console.log('[error] '+error.message)
		}

	})

    function render_password_icon(mode){
        if (mode === 'password') {
            d.getElementById('lock_icon').classList.remove('d-none');
            d.getElementById('unlock_icon').classList.add('d-none');
        }else if (mode === 'text'){
            d.getElementById('unlock_icon').classList.remove('d-none');
            d.getElementById('lock_icon').classList.add('d-none');
        }
    }
    function btn_preload(t){
        let btn = document.getElementById('btn-sending');
		if (t === 'start') {
			btn.classList.add('btn-loading');
			btn.disabled = true;

		} else if (t === 'close') {
			btn.classList.remove('btn-loading');
			btn.disabled = false;
		}
    }
    function ttoast(action, data = {}){
        let toast = d.getElementById('ttoast_');
        let title = toast.querySelector('.ttoast-title');
        let mssg = toast.querySelector('.ttoast-message');
        let close = toast.querySelector('.ttoast-close');
        
        toast.className = 'ttoast';
        toast.classList.add('ttoast-'+data.style)
        switch (action) {
            case 'open':
                toast.style.top = '100px';
                title.innerHTML = data.title;
                mssg.innerHTML = data.message;
                break;
        
            case 'close':
                toast.classList.remove('ttoast-'+data.style)
                toast.style.top = '-500px';
                title.innerHTML = '';
                mssg.innerHTML = '';
                break;
        }
        close.addEventListener('click', ev => {            
            ttoast('close')
        })
        if (data.time && data.time > 0) {
            setTimeout(() => {
                ttoast('close')
            }, data.time);
        }
        
    }

})(document, window);