(function(d,w){
	let app;
	let data_stash = [];

	d.addEventListener('DOMContentLoaded', async () => {
		app = new App();


	})

	// Obtener datos
	async function reporte__get_data(){
		try {
			const req = await fetch(srv.url()+'api-reporte/procesado/v1');
			const res = await req.json();
			return res;			
		} catch(e) {
			console.log(e.message);
			return [];
		}
	}

	async function reporte__prepare_html_data(){
		
	}
	
})(document,window);