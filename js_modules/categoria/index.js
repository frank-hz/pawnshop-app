"use strict";
(function(d, w){
    let app;
    const filters = {
        orden: 'ASC',
        limite: 10,
        nombre: ''
    }
    const ELEMS = {};

    d.addEventListener('DOMContentLoaded', evt => {
        app = new App();

        init_elements();
        console.log(ELEMS)
    });


    function init_elements(){
        if (typeof app.getViewElements === 'function') {
            // ELEMS.table_categoria = app.getID('dataset_categoria') || null;
            ELEMS = app.getElements([
                'dataset_categoria',
                'select_tipos',
                'input_search',
            ]);
        } else {
            console.error('metodo getID no disponible.');
            ELEMS.table_categoria = null;
        }

    }
    


})(document, window);