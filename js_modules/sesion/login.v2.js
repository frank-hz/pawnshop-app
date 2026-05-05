// "use strict";
(function(d,w){
    let app;
    let buttonSend;
    d.addEventListener('DOMContentLoaded', () => {
        app = new App();
        buttonSend = d.getElementById('btn-sending');
    })

    d.addEventListener('click', (event) => {
        let ev = event.target;
        if (ev && ev.id === 'password_button') {
            let button_ = ev;
            let input_ = d.getElementById('input_password');
            let new_value = input_.type === 'password' ? 'text' : 'password';
            input_.type = new_value;
            button_.classList.toggle('unlock');
        }
    })

    d.getElementById('LOGIN_FORM').addEventListener('submit', async event => {
		event.preventDefault()
		try {
			btn_preload('start')
            buttonSend.disabled = true;
            ttoast('close')
			const data = new FormData(event.target)
			const req = await fetch(srv.url()+'api/v1/user/auth', {
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
					window.location.href = srv.url()+'sistema/revision';
				}, 750);
			} else{
                buttonSend.disabled = false;
				ttoast('open', {
                    title: 'Cuidado!',
                    message: res.message,
                    style: 'warning',
                    time: 2000
                })
			}
		} catch (error) {
			btn_preload('close')
            buttonSend.disabled = false;
			ttoast('open', {
                title: 'Cuidado!',
                message: 'Error al procesar',
                style: 'danger',
                time: 2000
            })
            console.log('[error] '+error.message)
		}

	})
    function btn_preload(t){
		if (t === 'start') {
			buttonSend.classList.add('btn-loading');
			// btn.disabled = true;

		} else if (t === 'close') {
			buttonSend.classList.remove('btn-loading');
			// btn.disabled = false;
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


})(document,window);