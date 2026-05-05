(function(d,w){

    d.addEventListener('DOMContentLoaded', () => {
        

    })
    d.getElementById('btnPrint')?.addEventListener('click', evt => {
        $("#contrato").print({
            globalStyles: true,
            mediaPrint: false,
            stylesheet: null,
            noPrintSelector: ".no-print",
            iframe: true,
            append: null,
            prepend: null,
            manuallyCopyFormValues: true,
            deferred: $.Deferred(),
            timeout: 750,
            title: '.',
            doctype: '<!doctype html>'
        });
    })
    d.getElementById('btnExport')?.addEventListener('click', evt => {
        const target = evt.target;
        generarPDF();

    })
    

    function generarPDF(){
        const element = d.getElementById('contrato');
        html2pdf().from(element).save();
    }

})(document, window);