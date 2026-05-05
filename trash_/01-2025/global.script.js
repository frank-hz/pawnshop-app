// og-rute: global-code/js

(function(d){
    d.addEventListener('DOMContentLoaded', () => {
        menu_dashboard_active()
    })

    if (d.getElementById('toggle-switch-btn')) {
        d.getElementById('toggle-switch-btn').addEventListener('click', event => {
            console.log(event.target)
            event.target.classList.toggle('toggle-active')
        })
    }


    function menu_dashboard_active(){
        try {
            let url = window.location.href.split('/');
            let url_module = url[4].replace("#", "");
            url_module = url_module.toUpperCase();
            url_module = (url_module) ? url_module : 'INICIO';

            let items = d.getElementsByName('menu-header-item');
            // d.querySelector('.menu-spring-item-active').classList.remove('menu-spring-item-active');
            if (items.length) {
                items.forEach(e => {                    
                    if (e.dataset.ref) {
                        let name_module = e.dataset.ref.toUpperCase();
                        if (name_module === url_module) {
                            e.classList.add('menu-spring-item-active');
                        } else{
                            e.classList.remove('menu-spring-item-active');
                        }
                    }
                })
            }
        } catch (error) {
            console.warn('[Dashboard Menu] '+error.message)
            console.log(error)
        }
    }
    

})(document);