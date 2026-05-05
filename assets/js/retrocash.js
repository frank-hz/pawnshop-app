class Retrocash {

	noticeShow(__tipo, __html, __espacio, __tiempo = 3000){
		if (document.getElementById(__espacio)) {
			let elemento = document.getElementById(__espacio);

			switch (__tipo) {
				case 'Ok':
					elemento.className = 'notice notice-ok';
					break;

				case 'Error':
					elemento.className = 'notice notice-error';
					break;

				case 'Aviso':
					elemento.className = 'notice notice-aviso';
					break;

				case 'Activo':
					elemento.className = 'notice notice-activo';
					break;

				default:
					elemento.className = 'notice';
					break;
			}

			elemento.innerHTML = __html;
			elemento.style.display = 'block';
			setTimeout(()=>{
				elemento.style.display = 'none';
				elemento.innerHTML = '';

			}, __tiempo)

		}else{
			console.log('%c Retrocash: Elemento no encontrado', 'background: #141C24; color: #06A8CB'); 
		}
	}

	modalShow(__modal){
		if (document.getElementById(__modal)) {
			let panel = document.getElementById(__modal);
			panel.style.display = 'block';
			
			// let modal = document.querySelector('#'+__modal+' .modal-contenido');
			// modal.style.display = 'block';
		}else{
			console.log('%c Retrocash: Modal no encontrado', 'background: #141C24; color: #06A8CB'); 
		}
	}

	modalClose(__modal){
		if (document.getElementById(__modal)) {
			document.getElementById(__modal).style.display = 'none';
		}else{
			console.log('%c Retrocash: Modal no encontrado', 'background: #141C24; color: #06A8CB');
		}
	}

	window__show(__id){
		if (document.getElementById(__id)) {
			document.getElementById(__id).style.display = 'flex';
		}else{
			console.log('%c Retrocash: Ventana no encontrada', 'background: #141C24; color: #06A8CB');
		}
	}

	window__close(__id){
		if (document.getElementById(__id)) {
			document.getElementById(__id).style.display = 'none';
		}else{
			console.log('%c Retrocash: Ventana no encontrada', 'background: #141C24; color: #06A8CB');

		}
	}

	tabStart(__tab){
		if (document.getElementById(__tab)) {

			let tab  = document.getElementById(__tab);
			let tabs = document.getElementsByClassName('tab');
			let divs = document.getElementsByClassName('tab-contenido');

			if (tabs.length > 0) {
				for (var i = 0; i < tabs.length; i++) {
					tabs[i].className = 'tab';
				}
			}

			if (divs.length > 0) {
				for (var i = 0; i < divs.length; i++) {
					divs[i].style.display = 'none';
				}
			}

			tab.className = 'tab tabActive';
			if (document.getElementById(tab.dataset.vista)) {
				document.getElementById(tab.dataset.vista).style.display = 'block';
			}

		}
	}

	touchspin(event){
		if (event.target.dataset.elem) {

			let elem = document.getElementById(elem);
			let min = (elem.min) ? parseInt(elem.min) : 0;
			let max = (elem.max) ? parseInt(elem.max) : 0;
			
			let new_value = parseFloa(elem.value) ;
		}
	}

}

const retro = new Retrocash();


document.addEventListener('click', (e)=>{

	// if (e.target.matches('.modal_close')) {
	// 	if (e.target.dataset.modal) {
	// 		retro.modalClose(e.target.dataset.modal);
	// 	}
	// }

	if (e.target.matches('.window-close')) {
		if (e.target.dataset.window) {
			retro.window__close(e.target.dataset.window);
		}
	}

	if (e.target.matches('.tab')) {
		if (e.target.id) {
			retro.tabStart(e.target.id);
		}
	}

})