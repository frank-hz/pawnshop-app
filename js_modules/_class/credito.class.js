import Server from './server.module.js';
// console.log(server.URL());

class Creditox extends Server {
    constructor(){
        super()
        console.log(this.URL());
    }

    async get(id){
        try {
            const data = new FormData();
            data.append('id', id);
            const req = await fetch(super.URL(), {
                body: data
            });
            const resp = await req.json();
            if (!resp) throw resp || "Error de Solicitud";

            return resp;
        } catch (error) {
            console.log('[CREDITO][get] ' + error.message);
            return;
        }
    }

    async getAll(settings = {}){
        try {
            const req = await fetch(super.URL(), {
                method: 'POST',
                body: JSON.stringify(settings),
                headers: {
                    'Content-Type': 'application/json'
                }
            });
            const resp = await req.json();
            if (!resp) throw resp || "Error de Solicitud";

            return resp;
        } catch (error) {
            console.log('[CREDITO][get] ' + error.message);
            return;
        }
    }

    create(){
        
    }

    update(){

    }

    delete(){

    }
}