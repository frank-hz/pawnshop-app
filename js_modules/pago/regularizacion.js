"strict mode";
(function(d){
    let app;
    let PROCESS = {
        id: 0,
        SaveStatus: true,
        pending: [],

    }

    d.addEventListener('DOMContentLoaded', ()=>{
        app = new App();
        Init()
    });

    async function Init(){
        try {
            if (!dataStartValidation()){
                PROCESS.SaveStatus = false;
                not.Aviso('Error al inicializar', '', 2000)
                return;
            }
            await RenderPendings()
            
        } catch (error) {
            
        }
    }
    function InitCuotas(){
        
    }
    // function InitMora
    function dataStartValidation(){
        try {
            let status = false;
            let id = window.IDE;
           
            if (window.IDE != null || window.IDE != undefined){
                status = true;
                PROCESS.id = window.IDE;
            }
            if (window.PENDING != null || window.PENDING != undefined){
                status = true;
                PROCESS.pending = window.PENDING;
            }
            
            return status;
        } catch (error) {
            return false;
        }
    }
    function RenderPendings(){
        try {            
            let html = '';
            let data = PROCESS.pending;
            // console.log(PROCESS);
            data.forEach(item => {
                let isActive = ['V','E','CC'].includes(item.estado);
                html += `
                    <tr class="text-center">
                        <td class="text-center">${item.id}</td>
                        <td>${item.descripcion}</td>
                        <td>${item.vencimiento}</td>
                        <td>S/ ${item.monto}</td>
                        <td>${item.estado}</td>
                        <td class="text-center">
                            <input type="checkbox" name="cuota_item" 
                            data-id="${item.id}" data-monto="${item.monto}"
                            data-orden="${item.orden}" ${isActive ? 'checked' : ''}
                            >   
                        </td>
                    </tr>
                `;
            })
            // console.log(html);
            d.getElementById('TABLE_PENDING').innerHTML = html;
        } catch (error) {
            
        }
    }


})(document);