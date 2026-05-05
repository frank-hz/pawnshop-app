// og-rute: global-code/js

var ENV2 = 'DEV';

class App {
	app_url(){
        let url = '';
		switch (ENV2) {
			case 'PROD':
				url = 'https://electrocashperu.com/sistema/';
				break;
			case 'TESTING':
				url = 'https://electrocashperu.com/sistema-dev/';
				break;
		
			default:
				let protocol = location.protocol;
				let server   = location.host;
				let app      = 'sistema-dev'; 
				url = protocol+'//'+server+'/'+app+'/';
				break;
		}
        return url;
    }	
    get_url(){
        let url;
		switch (ENV2) {
			case 'PROD':
				url = 'https://electrocashperu.com/sistema/';
				break;
			case 'TESTING':
				url = 'https://electrocashperu.com/sistema-dev/';
				break;
		
			default:
				let protocol = location.protocol;
				let server   = location.host;
				let app      = 'sistema-dev'; 
				url = protocol+'//'+server+'/'+app+'/';
				break;
		}
        return url;
    }	
    render(id, html){
		try {
			var element = (typeof id === 'object') ? id : document.getElementById(id);
            element.innerHTML = html;
		} catch(e) {
			console.log('Ocurrio un error al renderizar. :: ', e.message);
		}
	}
    money_format(monto, contadorDec = 2){
        var separador = ",";
        var dec = ".";
        contadorDec = Math.abs(contadorDec);
        contadorDec = isNaN(contadorDec) ? 2 : contadorDec;
        
        var signo = monto < 0 ? "-" : "";
        let i = parseInt(monto = Math.abs(Number(monto) || 0).toFixed(contadorDec)).toString();
        let j = (i.length > 3) ? i.length % 3 : 0;

        return signo + (j ? i.substr(0, j) + separador : "") 
            + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + separador) 
            + (contadorDec ? dec + Math.abs(monto - i).toFixed(contadorDec).slice(2) : "");   
    }
    css_add(id, class_value){
		if (document.getElementById(id)){
			document.getElementById(id).classList.add(class_value);
		}else{
			console.log('elemento objetivo no encontrado.');
		}
	}
	css_remove(id, class_value){
		if (document.getElementById(id)){
			document.getElementById(id).classList.remove(class_value);
		}else{
			console.log('elemento objetivo no encontrado.');
		}
	}
}

class Tools {
	popup(text, type = '', time = 0,icon = 'fi-br-info') {
		var popup_ui = document.getElementById('popup-alert');
		popup_ui.className = 'popup';
		popup_ui.innerHTML = `
			<div class="popup-icon">
				<i class="${ icon }"></i>
			</div>
			<div class="popup-text">${ text }</div>
			<a href="javascript:void(0)" class="popup-close">&#10006;</a>
		`;
		popup_ui.classList.add(`popup-${ type }`)
		popup_ui.classList.add('popup-show');
		if (time > 0) {
			setTimeout(() => {
				popup_ui.classList.remove('popup-show');
			}, time);
		}
	}
	message(message, title, type = 'default', time = 0){
		var message_ui = document.getElementById('message-alert');
		// var css_type = 'message-'+type;
		message_ui.classList.add('message-'+type)
		message_ui.innerHTML = `
			<div class="message-icon">
				<i class="fi-br-info"></i>
			</div>
			<div class="message-header">
				<div class="message-title">
					${ title }              
				</div>
				<a href="javascript:void(0)" class="message-close">&#10006;</a>
			</div>
			<div class="message-content">${ message }</div>
		`;
		message_ui.classList.add('message-show');

		if (time > 0) {
			setTimeout(() => {
				message_ui.classList.remove('message-show');
			}, time);
		}
	}
	priceFormat(monto, contadorDec = 2){
        var separador = ",";
        var dec = ".";
        contadorDec = Math.abs(contadorDec);
        contadorDec = isNaN(contadorDec) ? 2 : contadorDec;
        
        var signo = monto < 0 ? "-" : "";
        let i = parseInt(monto = Math.abs(Number(monto) || 0).toFixed(contadorDec)).toString();
        let j = (i.length > 3) ? i.length % 3 : 0;

        return signo + (j ? i.substr(0, j) + separador : "") 
            + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + separador) 
            + (contadorDec ? dec + Math.abs(monto - i).toFixed(contadorDec).slice(2) : "");   
    }
	render(id, html){
		try {
			document.getElementById(id).innerHTML = html;
		} catch(e) {
			console.log('Ocurrio un error al renderizar. :: ', e.message);
		}
	}
	css_add(id, class_value){
		if (document.getElementById(id)){
			document.getElementById(id).classList.add(class_value);
		}else{
			console.log('elemento objetivo no encontrado.');
		}
	}
	css_remove(id, class_value){
		if (document.getElementById(id)){
			document.getElementById(id).classList.remove(class_value);
		}else{
			console.log('elemento objetivo no encontrado.');
		}
	}
	getDaysInMonth(month,year) {
		return new Date(year, month, 0).getDate();
	}
	modal_open(id){
		try {
			document.getElementById(id).classList.add('modal-show');
		} catch(e) {
			console.log('Error al abrir el modal');
		}
	}
	modal_hide(id){
		try {
			document.getElementById(id).classList.remove('modal-show');
		} catch(e) {
			console.log('Error al cerrar el modal');
		}
	}
	show_modal_alert(id, message, title = 'Aviso',time = 1500){
		try {
			var modal = document.getElementById(id)
			modal.innerHTML = `
				<div class="modal-alert">
					<i class="fi-br-shield-exclamation modal-alert-icon"></i>
	            	<div class="modal-alert-main">
	            		<span class="modal-alert-title">${ title }</span>
	            		<span class="modal-alert-content">
	            			${ message}
	            		</span>
	            	</div>
	            </div>
			`;
			modal.classList.remove('fast-hide');
			setTimeout(() => {
				modal.classList.add('fast-hide');
			}, time)
		} catch(e) {
			// statements
			console.log(e);
		}
	}
	loader_start(id, t, mssg = 'Cargando...', cols = 2){
		try {
			var area = document.getElementById(id)
			var spinn = `
				<div class="spinner-1">
					<span></span>
					<div>${ mssg }</div>
				</div>
			`;
			switch(t){
				case "in_table":
					spinn = `
						<tr>
							<td class="text-center" colspan="${ cols }">
								${ spinn }
							</td>
						<tr>
					`;
					break;
			}
			area.innerHTML = spinn;
		} catch (error) {
			console.log('Loader::Error', error.message)
		}
	}
	loader_end(id){
		var area = document.getElementById(id)
		area.innerHTML = '';
	}

}

const tool = new Tools();
document.addEventListener('click', event => {
	// if (event.target.matches('.modal-close') || event.target.matches('[name=modal-close-dissmiss]')) {
	// 	event.target.parentNode.parentNode.parentNode.classList.remove('modal-show');
	// }
	if (event.target.matches('.popup-close')) {
		event.target.parentNode.classList.remove('popup-show');
	}
})
