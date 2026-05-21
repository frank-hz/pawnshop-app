
(function(d, w){

    d.addEventListener('DOMContentLoaded', function(){
        // console.log('start...')
    });
    d.getElementById('btn_print')?.addEventListener('click', evt => {
        console.log('print...')
        $("#anexo_file").print({
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
    d.getElementById('btn_export')?.addEventListener('click', evt => {
        const target = evt.target;
        const nombre = target.dataset.nombre || "Contrato";
        generatePdf("Contrato "+nombre);
        // console.log('export..');

    })
    

    function generarPDF(){
        const element = d.getElementById('contrato');
        html2pdf().from(element).save();
    }

    async function generateImage(){
        try {
            console.log('generando..');
            const view = d.getElementById('contrato');
            const clone = view.cloneNode(true);
            clone.style.overflow = "visible";
            clone.style.maxHeight = "none";
            clone.style.position = "absolute";
            clone.style.left = "-9999px";
            clone.style.width = "794px";
            clone.style.height = "1123px";
            d.body.appendChild(clone);
            
            html2canvas(clone).then(canvas => {
                const button = d.createElement('a');
                button.download = 'contrato x';
                button.href = canvas.toDataURL(clone);
                button.click();
                d.body.removeChild(clone);
            })
        } catch (error) {
            console.log(error.message);
        }
    }

    async function generatePdf(nombre_archivo){
        try {
            console.log('generando..');
            const view = d.getElementById('contrato');
            const clone = view.cloneNode(true);
            clone.classList.add('export-style');
            clone.style.overflow = "visible";
            clone.style.maxHeight = "none";
            clone.style.position = "absolute";
            clone.style.padding = "40px";
            clone.style.width = "794px";
            clone.style.height = "1123px";
            d.body.appendChild(clone);

            const { jsPDF } = window.jspdf; 
            const canvas = await html2canvas(clone, {
                scale: 2,
                useCORS: true
            });
            const img = canvas.toDataURL("image/png");
            const pdf = new jsPDF({
                orientation: "portrait",
                unit: "mm",
                format: "a4",
                putOnlyUsedFonts:true
            });
            const page_width = pdf.internal.pageSize.getWidth();
            const page_height = pdf.internal.pageSize.getHeight();
            const img_width = page_width;
            const img_height = (canvas.height * img_width) / canvas.width;

            const scale = Math.max(page_width / canvas.width, page_height / canvas.height);

            const img_w = canvas.width * scale;
            const img_h = canvas.height * scale;

            const offSetX = (page_width - img_w) / 2;
            const offSetY = (page_height - img_h) / 2;



            pdf.addImage(img, "PNG", offSetX, offSetY, img_w, img_h);
            pdf.save(`${nombre_archivo}.pdf`);
            
            d.body.removeChild(clone);
        } catch (error) {
            console.log(error.message);
        }
    }
})(document, window);