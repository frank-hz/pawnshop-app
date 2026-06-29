"use strict";
(function(d,w){
    let app;
    let buttonSend, FORM_LOGIN, ButtonPasswordLock;
    d.addEventListener('DOMContentLoaded',() => {
        app = new App();
        buttonSend = document.getElementById('_btn_send');
        FORM_LOGIN = document.getElementById('_LOGIN_FORM');
        
        console.log(app.URL())
        

        if (document.getElementById('password_button')){
            ButtonPasswordLock = document.getElementById('password_button');
            ButtonPasswordLock.addEventListener('click', (evt) => {
                let button_password = evt.target;
                let input_password = d.getElementById('input_password');
                let new_value = input_password.type === 'password' ? 'text' : 'password';
                input_password.type = new_value;
                button_password.classList.toggle('unlock');
            })
        }
        FORM_LOGIN.addEventListener('submit', async evt => {
            evt.preventDefault()
            try {
                buttonSend.classList.add('btn-loading');
                buttonSend.disabled = true;
                show_ttoast(0)

                const data = new FormData(evt.target)
                const {ok, message} = await sesion_api.login(data);
                buttonSend.classList.remove('btn-loading');
                if (!ok) {
                    buttonSend.disabled = false;
                    show_ttoast(true, {
                        title: 'Cuidado!',
                        message: message || 'Ocurrio un error inesperado.',
                        style: 'warning',
                        time: 2000
                    });
                    return;
                }
                show_ttoast(true, {
                    title: 'Autenticacion Exitosa',
                    message: message || 'Verificacion completada.',
                    style: 'primary'
                })
                setTimeout(()=>{
                    window.location.href = srv.url()+'revision';
                }, 750);
                
            } catch (error) {
                buttonSend.classList.remove('btn-loading');
                buttonSend.disabled = false;
                show_ttoast(true, {
                    title: 'Cuidado!',
                    message: 'Error al procesar',
                    style: 'danger',
                    time: 2000
                })
                console.log('[error] '+error.message)
            }
    
        })
    })
    w.addEventListener('load', renderTag)


    function renderTag(){
        if (d.getElementById('env_tag')){   
            const env = app.ENV();
            let renderElement = d.getElementById('env_tag');
            if (!env) return;
            
            if (env === 'DEV'){
                renderElement.innerHTML = 'Development';
                renderElement.className = 'tag tag-dev';
            }
            if (env === 'TESTING'){
                renderElement.innerHTML = 'Testing';
                renderElement.className = 'tag tag-testing';
            }
        }
    }
    function show_ttoast(show = true, data = {}){
        try {
            const toast = {
                container: d.getElementById('ttoast_'),
                title: d.querySelector('#ttoast_ .ttoast-title'),
                message: d.querySelector('#ttoast_ .ttoast-message'),
                close: d.querySelector('#ttoast_ .ttoast-close')
            }
    
            toast.container.className = 'ttoast';
            toast.container.classList.add('ttoast-'+data.style)
            toast.container.style.top = (show) ? '100px' : '-500px';
            toast.title.innerHTML = (show) ? data.title : '';
            toast.message.innerHTML = (show) ? data.message : '';
    
            if (show){
                toast.close.addEventListener('click', ev => {            
                    show_ttoast(false)
                })
            }
            if (show && data.time && data.time > 0) {
                setTimeout(() => {
                    show_ttoast(false)
                }, data.time);
            }
        } catch (error) {
            console.log(error);
        }
    }


   


})(document,window);