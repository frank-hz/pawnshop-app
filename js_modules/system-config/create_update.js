(function(d,w){
    let app;
    
    d.addEventListener('DOMContentLoaded', function(){
        app = new App();
    })


    d.addEventListener('change', evt => {
        // evt.preventDefault()
        if (evt.target.matches('[name=login_view_item]')){
            let data = new FormData();
            data.append('view', evt.target.value);
            save_data(data)
        }
    })

    async function save_data(data_){
        await fetch(app.URL('api/v1/config-system/update'),{
            method:'POST',
            body: data_
        })
        .then(response => {
            if (!response.ok){
                throw new Error('No se pudo procesar la solicitud');
            }
            return response.json();
        })
        .then(data => {
            if (data.ok){
                not.Ok(data.ok, '');
                setTimeout(() => {
                    location.reload();
                }, 1000);
                
                return;
            }
            if (data.error){
                throw new Error(data.error || 'Lo sentimos, ha ocurrido un error');
            }            
        })
        .catch(error => {
            not.Error(error.message, '');

        })
    } 
})(document,window);