(function(){

	var app;
	document.addEventListener('DOMContentLoaded', async ()=>{
		app = new App()
		app.defineEvent('#frm_prenda_editar', 'submit', (e)=>{
			e.preventDefault()

			app.preload('open', 'Procesando..');
			let data = new FormData(e.target)
			fetch(srv.url()+'Prenda/ServiceRegistrar', {
				method: 'POST',
				body: data
			})
			.then(request => {
				if (request.ok) return request.json()
				return Promise.reject(request)
			})
			.then(response => {
				app.preload('close');
				let tipo = (response.status === 'OK') ? 'success' : 'primary';
				app.SysMessage('prendaedt_sysmssg', response.message, tipo)
			})
			.catch(error => {
				app.SysMessage('prendaedt_sysmssg', 'danger', 'Error al procesar.')
			})
			// .finally(function(){
			// 	app.preload('close');
			// })
		})	

	})

	function Feedback(error){
		var form = document.getElementById('')
		if(error.descripcion) {
			form.descripcion.classList.add('is-invalid')
		}
	}


})();


/*
	let send = await fetch(srv.url()+'Categoria/ServiceListar', {
		method: 'POST',
		body: JSON.stringify(prenda_data),
		headers: {
			'Content-type': 'application/json; charset=UTF-8' 
		}
	});

	CARGAR CATEGORIA ACTUAL
	-------------------------
	- parametros: id_categoria
	- solicitar/obtener lista de categorias
	- renderizar categorias y establecer como :selected: al valor actual

	CAMBIAR EL TIPO DE PRENDA
	---------------------------
	- parametros: id_prenda, id_categoria
	- 
	

	PRENDA
	-------
	electro:
		- serial
		- marca
		- modelo
	joya:
		- quilate
		- peso
	vehiculo:
		- marca 
		- modelo
		- año
		- placa
		- kilometraje
		
*/
// cambiar tipo_prenda: recargar las opciones de categoria por defecto
