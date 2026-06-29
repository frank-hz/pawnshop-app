const APP_CONFIG = {
    PROTOCOL: 'http',
    HOST: 'localhost:9090',
    NAME: '',
    ENV: 'DEV'
}


// if (APP_CONFIG.ENV === 'TESTING') {
//     let dash = document.getElementById('dashboard-app');
//     let tag = document.getElementById('testing-tag');

//     if (dash && tag) {
//         dash.classList.add('dash-testing');
//         tag.classList.remove('d-none');
//     }
// }

// (function(){
//     console.log('ads');
//     // initConfig()
// })()

const MY_CONFIG = {
    HOST: 'localhost:9090',
    NAME: '',
    ENV: 'DEV'
}

class Servidor{
    url(){
        const path = MY_CONFIG.NAME ? `${MY_CONFIG.NAME}/` : '';
        return `${location.protocol}//${MY_CONFIG.HOST}/${path}`;
        // return `${location.protocol}//${APP_CONFIG.HOST}/${APP_CONFIG.NAME}/`;
    }
}

class App {
    ENV(){
        return MY_CONFIG.ENV;
    }    
    URL(dir = ''){
        // const path = MY_CONFIG.NAME ? `${MY_CONFIG.NAME}/` : '';
        // const base = `${location.protocol}//${MY_CONFIG.HOST}/${path}`;
        // return `${base}${dir}`

        // return `${location.protocol}//${APP_CONFIG.HOST}/${APP_CONFIG.NAME}/${dir}`;
        // let part_1 = 

        const host = MY_CONFIG.HOST;
        const name = MY_CONFIG.NAME;
        const path = name ? `${host}/${name}` : `${host}`;
        return `${location.protocol}//${path}/${dir}`;
    }
    static URL_STATIC(dir = ''){
        const host = MY_CONFIG.HOST;
        const name = MY_CONFIG.NAME;
        const path = name ? `${host}/${name}` : `${host}`;
        return `${location.protocol}//${path}/${dir}`;

        // return `${location.protocol}//${APP_CONFIG.HOST}/${APP_CONFIG.NAME}/${dir}`;
    }

    getID = (id) => {
        const el = d.getElementById(id);
        if (!el){
            console.log(`Elemento ${id} no encontrado.`);
        }
        return el;
    }
    getElements = (ids) => {
        return ids.reduce((acum, id) => {
            acum[id] = this.getID(id);
            return acum;
        }, {});
    }


    // Convertir json a FormData
    jsonToFormData(json_object){
        const form_data = new FormData();
        for (let key in json_object){
            if (json_object.hasOwnProperty(key)){
                form_data.append(key, json_object[key]);
            }
        }
        return form_data;
    }

    deadTime(time_ = 1000){
        return new Promise(resolve => setTimeout(resolve, time_));
    }

    // limita la cantidad de caracteres de un texto
    shortText(string, setting = {}){
        if (!string) return "No Data";
        let limite = setting.limite ?? 30;
        let simbol = setting.simbolo ?? "...";
        return string.length > limite ?  string.slice(0, limite) + simbol : string;
    }

    // Verifica si un valor es vacio, 
    // funcion equivalente al empty en PHP
    empty(value_){
        const empty_values = ['undefined', null, false, 0, '', '0'];
        let len = empty_values.length;
        for (let i = 0; i < len; i++) {
            if (value_ === empty_values[i]) {
                return true
            }
        }
        if (typeof value_ === 'object') {
            for (let key in value_) {
                if (value_.hasOwnProperty(key)) {
                    return false
                }
            }
            return true
        }
        return false
    }
    // Campo que solo permite numeros y los convierte a formato de moneda
    inputMoney(id,  callback){
        try {
            if (!id) throw "input not found";
            let inp = (typeof id === 'object') ? id : document.getElementById(id);
            inp.classList.add('money-input');

            let raw_value = 0;
            inp.addEventListener('keyup', e => {
                let input_val = e.target.value;
                let dataraw = 0;

                if (!input_val) return;
                let sign = "S/ ";
                if (input_val.indexOf(".") >= 0) {
                    let decimal_pos = input_val.indexOf(".");            
                    let left_side = input_val.substring(0, decimal_pos);
                    let right_side = input_val.substring(decimal_pos);            
                    left_side = formatted(left_side);
                    right_side = formatted(right_side);
                    right_side = right_side.substring(0, 2);
                    input_val = sign + left_side + "." + right_side;
                    dataraw = left_side + "." + right_side;          
                }else {
                    input_val = formatted(input_val);
                    input_val = sign + input_val;
                }
                function formatted(v) {
                    return v.replace(/\D/g, "").replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                }
                e.target.value = input_val;
                dataraw = input_val.replace(",", "").replace(sign, "");
                dataraw = (dataraw) ? dataraw : 0;
                callback(dataraw)
            });
        } catch (error) {
            console.log('[MoneyInput]'+error.message)
        }
        

    }
    inputLicence(id, callback){
        try {
            if (!id) throw "input not found";
            let inp = (typeof id === 'object') ? id : document.getElementById(id);
            inp.classList.add('licence-input');
            inp.addEventListener('keypress', e => {
                if (e.target.value.length === 3){
                    e.target.value += "-";
                }
                if (e.target.value.length === 7){
                    e.preventDefault()
                }              
            })
        } catch (error) {
            console.log('[LicenceInput]'+error.message)
        }

    }
    validata(t, value){
        let regExp = {
            id: /^\+?(0|[1-9]\d*)$/gm,
            number: /0-9/d,
            text: /a-zA-Z/
        }
        if (regExp.hasOwnProperty(t)){
            return regExp[t].test(value);
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
    round_money(monto, format = false) {
        // Redondeo a 1 decimal con HALF_DOWN
        const redondeado = Math.round((monto * 10) - 0.0000001) / 10;        
        // Retornar con 2 decimales (siempre con cero)
        if (format) {
            redondeado.toFixed(2);
        }
        return redondeado;
    }
    rowPreload(id, content, colspan = 5){
        let tb = document.getElementById(id)
        tb.innerHTML = `
            <tr class="text-center">
                <td colspan="${colspan}" class="text-center">
                    <div class="loader"></div> 
                    ${content}
                </td>
            </tr>
        `;
    }
    get_id(id){
        try {
            return document.getElementById(id);
        } catch (error) {
            return false;
            console.log('@get_id [error]');
        }
    }

    setEvent(id, action, tasks){
        try {
            var obj = (typeof id === 'object') ? id : document.getElementById(id);
            if (!obj) throw "ERR_01";
            obj.addEventListener(action, tasks);
        } catch (error) {
            if (error === 'ERR_01') {
                console.log('objeto no encontrado', id)
            }
        }
    }

    // id: elemento donde renderizar el mensaje
    // tipo_mssg: variante de mensaje segun su motivo. error, exito, advertencia
    // content: valor del mensaje
    //  time: lapso de tiempo en milisegundos en el que se mostrara el mensaje 
    SysMessage(id, content, tipo_mssg = 'secondary', time = 2200){
        try {
            var el = document.getElementById(id)
            if (!el) throw new Error('area de renderizado no encontrado.')
            let icon;
            switch (tipo_mssg) {
                case 'success':
                    icon = 'fe fe-check-circle'
                    break;
                case 'error':
                    icon = 'fe fe-x-circle';
                    break;
                case 'warning':
                    icon = 'fe fe-alert-triangle';
                    break;
                default:
                    icon = 'fe fe-bell';
                    break;
            }
            // el.innerHTML = `div class="alert alert-${tipo_mssg}" role="alert" style="border-color: transparent; border-radius: 1px">
            //         <i class="${icon} mr-2" aria-hidden="true"></i>
            //         ${content}
            //     </div>`
            let div = document.createElement('div')
            let i   = document.createElement('i')
            let txt =  document.createTextNode(content)
            div.setAttribute('role', 'alert')
            div.setAttribute('class', 'alert alert-'+tipo_mssg)
            div.style.border = 'none'
            i.setAttribute('aria-hidden', 'true')
            i.setAttribute('class', icon+' mr-2')
            
            el.textContent = ''
            div.appendChild(i)
            div.appendChild(txt)
            el.appendChild(div)

            if(window.jQuery){
                $('#'+id).slideDown('fast')
                setTimeout(function(){ $('#'+id).slideUp('fast') }, time);
            }else{                
                el.style.display = 'block'
                setTimeout(function(){ el.style.display = 'none' }, time);
            }            
        } catch(e) {
            console.log('SysMessage:: '+e.message);
        }
    }
    defineEvent(el, act, fnc, def_global = false){
        if (document.querySelector(el)) {
            if (def_global) {
                document.addEventListener(act, ev => {
                    if (ev.target.matches(el)) fnc
                })
            }else{
                document.querySelector(el).addEventListener(act, fnc)
            }
        }
    }
    render(target, html){
        try {
            let elem = (typeof target === 'string') 
                ? document.getElementById(target) : target;
            elem.innerHTML = html; 
        } catch (error) {
            console.log('%c No renderizado: '+target, 'background: #1277BA; color: #fff; display: block;');
        }
    }
    renderAlert(tipo, texto, tiempo = 0){
        try {
            let logConsole = document.getElementById('main_notification');
            // document.getElementById("myDIV").style.transition = "all 2s";
            // logConsole.style.transition = "all 6s ease-in";
            let alerta = '';
            switch (tipo) {
                case 'ok':
                    alerta = `
                        <div class="alert alert-icon alert-success" role="alert">
                            <i class="fe fe-check-circle mr-2" aria-hidden="true"></i>
                            <button data-dismiss="alert" class="close"></button>
                            ${texto}
                        </div>`;
                    break;
                case 'error':
                    alerta = `
                        <div class="alert alert-icon alert-danger" role="alert">
                            <i class="fe fe-x-square mr-2" aria-hidden="true"></i>
                            <button data-dismiss="alert" class="close"></button>
                            ${texto}
                        </div>`;
                    break;
                case 'aviso':
                    alerta = `
                        <div class="alert alert-icon alert-warning" role="alert">
                            <i class="fe fe-alert-triangle mr-2" aria-hidden="true"></i>
                            <button data-dismiss="alert" class="close"></button>
                            ${texto} 
                        </div>`;
                    break;
                case 'espera':
                    alerta = `
                        <div class="alert alert-icon alert-primary" role="alert">
                            <i class="fas fa-spinner fa-pulse mr-2"></i>
                            <button data-dismiss="alert" class="close"></button>
                            ${texto} 
                        </div>`;
                    break;
            
                default:
                    alerta = `
                        <div class="alert alert-icon alert-primary" role="alert">
                            <i class="fe fe-terminal mr-2" aria-hidden="true"></i>
                            <button data-dismiss="alert" class="close"></button>
                            ${texto} 
                        </div>`;
                    break;
            }
            logConsole.innerHTML = alerta;

            if (!isNaN(tiempo) && tiempo > 0) {
                setTimeout(() => {
                    logConsole.innerHTML = '';
                }, tiempo);
            }
        } catch (error) {
            console.log('%c app:renderAlert_'+error.message, 'background: #35495E; color: #42B883; display: block;');
        }
    }
    renderAlertFree(id, tipo, texto, tiempo = 0){
        try {
            let logConsole = document.getElementById(id);
            let alerta = '';
            switch (tipo) {
                case 'ok':
                    alerta = `
                        <div class="alert alert-icon alert-ok" role="alert">
                            <i class="fe fe-check-circle mr-2" aria-hidden="true"></i>
                            <button data-dismiss="alert" class="close"></button>
                            ${texto}
                        </div>`;
                    break;
                case 'error':
                    alerta = `
                        <div class="alert alert-icon alert-error" role="alert">
                            <i class="fe fe-x-square mr-2" aria-hidden="true"></i>
                            <button data-dismiss="alert" class="close"></button>
                            ${texto}
                        </div>`;
                    break;
                case 'aviso':
                    alerta = `
                        <div class="alert alert-icon alert-aviso" role="alert">
                            <i class="fe fe-alert-triangle mr-2" aria-hidden="true"></i>
                            <button data-dismiss="alert" class="close"></button>
                            ${texto} 
                        </div>`;
                    break;
                case 'espera':
                    alerta = `
                        <div class="alert alert-icon alert-secondary" role="alert">
                            <i class="fas fa-spinner fa-pulse mr-2"></i>
                            <button data-dismiss="alert" class="close"></button>
                            ${texto} 
                        </div>`;
                    break;
            
                default:
                    alerta = `
                        <div class="alert alert-icon alert-primary" role="alert">
                            <i class="fe fe-terminal mr-2" aria-hidden="true"></i>
                            <button data-dismiss="alert" class="close"></button>
                            ${texto} 
                        </div>`;
                    break;
            }
            logConsole.innerHTML = alerta;

            if (!isNaN(tiempo) && tiempo > 0) {
                setTimeout(() => {
                    logConsole.innerHTML = '';
                }, tiempo);
            }
        } catch (error) {
            console.log('%c app:renderAlertFree_'+error.message, 'background: #35495E; color: #42B883; display: block;');
        }
    }
    preload(accion, contenido = ''){
        try {
            let div = document.getElementById('preload_app');
            let text = document.getElementById('pre_text');
            if (accion === 'open'){
                div.style.visibility = 'visible';
                text.innerHTML = contenido;
            }else{
                div.style.visibility = 'hidden';
            }
        } catch(e) {
            console.log('@preload [error]');
        }
    }

    async_progress(accion, data){
        try {
            let div  = document.getElementById('async_page');
            let title = document.getElementById('async_title');
            let text = document.getElementById('async_text');
            let bar  = document.getElementById('async_bar');

            switch (accion) {
                case 'open':
                    div.style.visibility = "visible";
                    bar.style.width = "10%";
                    title.textContent = data.titulo;
                    text.textContent = data.contenido;
                    break;
            
                case 'upd':
                    bar.style.width = data.porcentaje;
                    break;

                case 'close':
                    
                    setTimeout(() => {
                        div.style.visibility = "hidden";
                    }, 2000);
                    setTimeout(() => {
                        bar.style.width = "0%";
                        title.textContent = "";
                        text.textContent = "";
                    }, 1000);
                    break;
            }
        } catch(e) {
            console.log(`@async_progress [${e}]`);
        }
    }

    windowLoader(accion, tipo, contenido = ''){
        try {
            let element = document.getElementById('window_loader_app')
            if(accion === 'open'){
                element.innerHTML = `
                    <div class="window-${tipo}">
                        ${contenido}
                    </div>
                `;
                element.style.visibility = 'visible'
            }else{
                element.style.visibility = 'hidden'
                element.innerHTML = ''
            }
        } catch(e) {
            console.log(e);
        }
    }

    set_value(id, valor){
        try {
            let element = document.getElementById(id);
            element.value = valor;
        } catch (error) {
            console.log('set_value: @'+error.message);
        }
    }
    
    disabled(id, estado){
        try {
            let element = document.getElementById(id);
            element.disabled = estado;
        } catch (error) {
            console.log('enabled: @'+error.message);
        }
    }
    Rx_round(num, dec = 1){
        if ((typeof num !== 'number') || (typeof dec !== 'number')) return num; 
        var num_sign = num >= 0 ? 1 : -1;
        return (Math.round((num*Math.pow(10,dec))+(num_sign*0.0001))/Math.pow(10,dec)).toFixed(dec);
    }
    NumRound(){
        let precio = 19.56789;
        let precioFormateado = precio.toFixed(2); // Devuelve un string
        console.log(precioFormateado); // "19.57"
        return precioFormateado;

    }
    fileSize(bytes){
        var sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
        if (bytes == 0) return '0 Byte';
        var i = parseInt(Math.floor(Math.log(bytes) / Math.log(1024)));
        return Math.round(bytes / Math.pow(1024, i), 2) + ' ' + sizes[i];
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

    stringval(val){
        if (val === null || val.trim() === "") {
            return false;
        } else {
            return true;
        }
    }
    // (remake) renderiza una alerta en el contenedor especificado
    rendalert(id, data = {}){
        // id, message, type = 'secondary', time = 0
        try {
            let container = (typeof id === 'object') ? id : document.getElementById(id);
            if (!container) {
                throw new Error('[rendalert] Area de renderizado no encontrado.');
            }
            if (data.clean) {
                container.innerHTML = '';
                return;
            }            
            let icon;
            switch (data.type) {
                case 'success':
                    icon = 'fe fe-check-circle'
                    break;
                case 'error':
                    icon = 'fe fe-x-circle';
                    break;
                case 'warning':
                    icon = 'fe fe-alert-triangle';
                    break;
                default:
                    icon = 'fe fe-bell';
                    break;
            }
            let div = document.createElement('div');
            let i   = document.createElement('i');
            let txt =  document.createTextNode(data.message);
            div.setAttribute('role', 'alert');
            div.setAttribute('class', 'alert alert-'+data.type);
            div.style.border = 'none';
            i.setAttribute('aria-hidden', 'true');
            i.setAttribute('class', icon+' mr-2');
            
            container.textContent = '';
            div.appendChild(i);
            div.appendChild(txt);            
            if (data.sub_message) {
                div.innerHTML += `
                    <div class="font-weight-500">
                    ${data.sub_message}  
                    </div>
                `;
            }
            container.appendChild(div);

            if(window.jQuery){
                $('#'+id).slideDown('fast')
                setTimeout(function(){ $('#'+id).slideUp('fast') }, time);
            }else{                
                el.style.display = 'block';
                setTimeout(function(){ container.style.display = 'none' }, time);
            }            
        } catch(e) {
            console.log('[rendalert] '+data.message);
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
    modal_close(id){
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
class Notificacion{
    Ok( titulo, mensaje, tiempo = 3000, posicion = 'topCenter'){
        iziToast.show({
            title: titulo,
            titleSize: '15px',
            message: mensaje,
            position: posicion,
            backgroundColor: '#42B883',
            messageColor: '#fff',
            titleColor: '#fff',
            timeout : tiempo,
            icon : 'fe fe-check-circle',
            iconColor: '#fff' 
        });
    }

    Aviso(titulo, mensaje, tiempo = 3000, posicion = 'topCenter'){
        iziToast.show({
            title: titulo,
            titleSize: '15px',
            message: mensaje,
            position: posicion,
            backgroundColor: '#FCC70A',
            messageColor: '#204056',
            titleColor: '#204056',
            timeout : tiempo,
            icon : 'fe fe-alert-triangle',
            iconColor: '#204056' 
        });
    }

    Error(titulo, mensaje, tiempo = 3000, posicion = 'topCenter'){
        iziToast.show({
            title: titulo,
            titleSize: '15px',
            message: mensaje,
            position: posicion,
            backgroundColor: '#E92C4C',
            messageColor: '#fff',
            titleColor: '#fff',
            timeout : tiempo,
            icon : 'fe fe-x-circle',
            iconColor: '#fff' 
        });
    }
    
    Blank( titulo, mensaje, posicion = 'topCenter', time = 2000){
        iziToast.show({
            title: titulo,
            titleSize: '15px',
            message: mensaje,
            position: posicion,
            backgroundColor: '#EBEEF3',
            messageColor: '#35495E',
            titleColor: '#2E51A2',
            timeout : time,
            icon : 'fe fe-zap',
            iconColor: '#2E51A2' 
        });
    }

    Dark(config){
        iziToast.show({
            title: config.title,
            titleSize: '15px',
            message: config.message ? config.message : '',
            position: config.position ? config.position : 'topCenter',
            backgroundColor: '#0f162e',
            messageColor: '#c6cdff',
            titleColor: '#c6cdff',
            timeout : config.time,
            icon : 'fe fe-terminal',
            iconColor: '#c6cdff' 
        });
    }

    ServerError( mensaje, posicion = 'topCenter', _icono = 'fe fe-terminal' ){
        iziToast.show({
            title: '',
            titleSize: '15px',
            message: mensaje,
            position: posicion,
            backgroundColor: '#fff',
            messageColor: '#F03C5B',
            titleColor: '#F03C5B',
            timeout : 3000,
            icon : _icono,
            iconColor: '#F03C5B' 
        });
    }
}






class Sucursal {
    async Listar(tipo){
        try {
            let data = new FormData();
            data.append('tipo', tipo);
            const request = await fetch(srv.url()+'api/v1/sucursal/public/getall', {
                method : 'POST',
                body : data
            });
            const res = await request.json();
            return {
                status: 'OK',
                result: res,
                mssg: 'correcto'
            };
        } catch (error) {
            return{
                status: 'ERROR',
                result: [],
                mssg: error.message
            };       
        }
    }

    async Registrar(data){
        try {
            const request = await fetch(srv.url()+'Sucursal/registrar', {
                method : 'POST',
                body : data
            });
            const res = await request.json();
            return {
                status: res.status,
                mssg: res.message,
                id: res.id,
                error: res.error
            };
        } catch (error) {
            return{
                status: 'ERROR',
                mssg: 'Error al enviar solicitud.'
            };
        }
    }

    async Eliminar(id){
        try {
            let data = new FormData();
            data.append('id', id);
            const request = await fetch(srv.url()+'Sucursal/eliminar', {
                method : 'POST',
                body : data
            });
            const res = await request.json();
            return {
                status: res.status,
                mssg: res.message
            };
        } catch (error) {
            return{
                status: 'ERROR',
                mssg: 'Error al enviar solicitud.'
            };
        }
    }

    async Modificar(data){
        try {
            const request = await fetch(srv.url()+'Sucursal/modificar', {
                method : 'POST',
                body : data
            });
            const res = await request.json();
            return {
                status: res.status,
                mssg: res.message
            };
        } catch (error) {
            return{
                status: 'ERROR',
                mssg: 'Error al enviar solicitud.'
            };
        }
    }

    async ObtenerEmpleados(id){
        try {
            let data = new FormData();
            // data.append('tipo', 'empleados');
            data.append('id', id);
            const request = await fetch(srv.url()+'Sucursal/consulta_empleados', {
                method : 'POST',
                body : data
            });
            const res = await request.json();
            return {
                status: 'OK',
                resp: res,
                mssg: 'correcto'
            };
        } catch (error) {
            return{
                status: 'ERROR',
                resp: [],
                mssg: error.message
            };       
        }
    }

    async ObtenerActivos(){
        try {
            let data = new FormData();
            data.append('tipo', 'activos');
            data.append('buscar', '');
            const request = await fetch(srv.url()+'Sucursal/consulta', {
                method : 'POST',
                body : data
            });
            const res = await request.json();
            return {
                status: 'OK',
                resp: res,
                mssg: 'correcto'
            };
        } catch (error) {
            return{
                status: 'ERROR',
                resp: [],
                mssg: error.message
            };       
        }
    }
}
class Revision {
    async revision_unitaria(id){
        try {
            let data = new FormData();
            data.append('id', id);
            let request = await fetch(srv.url()+'App/revision_unitaria', {
                method: 'POST',
                body: data
            });
            let revision = await request.json();
            return {
                status: revision.status,
                mssg: revision.message,
                calculo: revision.rev_calculo,
                cuotas: revision.rev_cuotas
            };
        } catch (error) {
            return{
                code: 'ERROR',
                mssg: 'Ocurrio un error al consultar',
                log: error.message
            };
        }
    }

    async revision_completa(){
        try {
            let request = await fetch(srv.url()+'App/revision_completa');
            let revision = await request.json();
            return {
                status: revision.status,
                mssg: revision.message,
                calculo: revision.cuota_calculo,
                generacion: revision.cuota_generacion
            };
        } catch (error) {
            return{
                code: 'ERROR',
                mssg: 'Ocurrio un error al consultar',
                log: error.message
            };
        }
    }

    async Diario(){
        try {
            let request = await fetch(srv.url()+'App/RevisionDiaria');
            let consulta = await request.json();
            return {
                code: 'Ok',
                rev1: consulta.revision_01,
                rev2: consulta.revision_02,
                cuotas: consulta.cuotas
            };
        } catch (error) {
            return{
                code: 'Error',
                mssg: 'Ocurrio un error al consultar',
                log: error.message
            };
        }
    }

    async Calculo(){
        try {
            let request = await fetch(srv.url()+'App/RevisionDiaria');
            let consulta = await request.json();
            return {
                code: consulta.code,
                mssg: consulta.message
            };
        } catch (error) {
            return{
                code: 'Error',
                mssg: 'Ocurrio un error al consultar',
                log: error.message
            };
        }
    }
}
class Ubigeo{
    async ListarCiudad(t = '', b = ''){
        try {
            let data = new FormData();
            data.append('tipo', t);
            data.append('buscar', b);
            const request = await fetch(srv.url()+'Ubigeo/consulta', {
                method : 'POST',
                body : data
            });
            const res = await request.json();
            return {
                status: 'OK',
                result: res,
                mssg: 'correcto'
            };
        } catch (error) {
            return{
                code: 'ERROR',
                result: [],
                mssg: error.message
            };       
        }
    }
}
class Empleado {

    async Listar(tipo, buscar, limite){
        try {
            let data = new FormData();
            data.append('tipo', tipo);
            data.append('buscar', buscar);
            data.append('limite', limite);
            const request = await fetch(srv.url()+'Empleado/consulta', {
                method : 'POST',
                body : data
            });
            const res = await request.json();
            return {
                status: 'OK',
                result: res,
                mssg: 'correcto'
            };
        } catch (error) {
            return{
                status: 'ERROR',
                result: [],
                mssg: error.message
            };       
        }
    }

    async Registrar(data){
        try {
            const request = await fetch(srv.url()+'Empleado/registrar', {
                method : 'POST',
                body : data
            });
            const res = await request.json();
            return {
                status: res.status,
                mssg: res.message,
                id: res.id,
                error: res.error
            };
        } catch (error) {
            return{
                status: 'ERROR',
                mssg: 'Error al enviar solicitud.'
            };
        }
    }

    async Actualizar(data){
        try {
            const request = await fetch(srv.url()+'Empleado/modificar', {
                method : 'POST',
                body : data
            });
            const res = await request.json();
            return {
                status: res.status,
                mssg: res.message,
                error: res.error
            };
        } catch (error) {
            return{
                status: 'ERROR',
                mssg: 'Error al enviar solicitud.'
            };
        }
    }

    async Eliminar(id){
        try {
            let data = new FormData();
            data.append('id', id);
            const request = await fetch(srv.url()+'Cliente/eliminar', {
                method : 'POST',
                body : data
            });
            const res = await request.json();
            return {
                status: res.status,
                mssg: res.message
            };
        } catch (error) {
            return{
                status: 'ERROR',
                mssg: 'Error al enviar solicitud.'
            };
        }
    }

    async ActualizarImagen(data){
        try {
            const request = await fetch(srv.url()+'Cliente/modificar_imagen', {
                method : 'POST',
                body : data
            });
            const res = await request.json();
            return {
                status: res.status,
                mssg: res.message
            };
        } catch (error) {
            return{
                status: 'ERROR',
                mssg: 'Error al enviar solicitud.'
            };
        }
    }

    async obtenerCreditos(id){
        try {
            let data = new FormData();
            data.append('tipo', 'creditos');
            data.append('buscar', id);
            const request = await fetch(srv.url()+'Cliente/REST', {
                method : 'POST',
                body : data
            });
            const res = await request.json();
            return {
                code: 'Ok',
                resp: res
            };
        } catch (error) {
            return{
                code: 'Error',
                mssg: 'Error al enviar solicitud.'
            };
        }
    }
}
class Usuario {

    async Autenticar(data){
        try {
            const request = await fetch(srv.url()+'Acceso/Autenticar', {
                method : 'POST',
                body : data
            });
            const res = await request.json();
            return {
                code: res.code,
                mssg: res.message
            };
        } catch (error) {
            return{
                code: 'Error',
                mssg: 'Error al enviar solicitud.'
            };
        }
    }

    async Listar(t = 'lista', b = ''){
        try {
            let data = new FormData();
            data.append('tipo', t);
            data.append('buscar', b);
            const request = await fetch(srv.url()+'Usuario/REST', {
                method : 'POST',
                body : data
            });
            const res = await request.json();
            return {
                code: 'Ok',
                resp: res,
                mssg: 'correcto'
            };
        } catch (error) {
            return{
                code: 'Error',
                resp: [],
                mssg: error.message
            };       
        }
    }

    async Eliminar(id){
        try {
            let data = new FormData();
            data.append('id', id);
            const request = await fetch(srv.url()+'Cliente/Eliminar', {
                method : 'POST',
                body : data
            });
            const res = await request.json();
            return {
                code: res.code,
                mssg: res.message
            };
        } catch (error) {
            return{
                code: 'Error',
                mssg: 'Error al enviar solicitud.'
            };
        }
    }

    async Modificar(data){
        try {
            const request = await fetch(srv.url()+'Usuario/modificar', {
                method : 'POST',
                body : data
            });
            const res = await request.json();
            return {
                status: res.status,
                mssg: res.message,
                error: res.error
            };
        } catch (error) {
            return{
                status: 'ERROR',
                mssg: 'Error al enviar solicitud.'
            };
        }
    }

    async ModificarEstado(data){
        try {
            const request = await fetch(srv.url()+'Usuario/modificar_estado', {
                method : 'POST',
                body : data
            });
            const res = await request.json();
            return {
                status: res.status,
                mssg: res.message
            };
        } catch (error) {
            return{
                status: 'ERROR',
                mssg: 'Error al enviar solicitud.'
            };
        }
    }

    async ActualizarImagen(data){
        try {
            const request = await fetch(srv.url()+'Usuario/modificar_imagen', {
                method : 'POST',
                body : data
            });
            const res = await request.json();
            return {
                status: res.status,
                mssg: res.message
            };
        } catch (error) {
            return{
                status: 'ERROR',
                mssg: 'Error al enviar solicitud.'
            };
        }
    }

    async Modulos(id){
        try {
            let data = new FormData();
            data.append('id', id);
            const request = await fetch(srv.url()+'Usuario/consulta_modulos', {
                method : 'POST',
                body : data
            });
            const res = await request.json();
            return {
                status: 'OK',
                result: res
            };
        } catch (error) {
            return{
                status: 'ERROR',
                mssg: 'Error al enviar solicitud.['+error.message+']'
            };
        }
    }

    async Permisos(id){
        try {
            let data = new FormData();
            data.append('id', id);
            const request = await fetch(srv.url()+'Usuario/consulta_permisos', {
                method : 'POST',
                body : data
            });
            const res = await request.json();
            return {
                status: 'OK',
                result: res
            };
        } catch (error) {
            return{
                status: 'ERROR',
                mssg: 'Error al enviar solicitud. '+error.message
            };
        }
    }

    async ActualizarPermiso(data){
        try {
            const request = await fetch(srv.url()+'Usuario/administrarPermiso', {
                method : 'POST',
                body : data
            });
            const res = await request.json();
            return {
                code: res.code,
                mssg: res.message,
                error: res.error
            };
        } catch (error) {
            return{
                code: 'Error',
                mssg: 'Error al enviar solicitud.'
            };
        }
    }

    async ActualizarModulo(data){
        try {
            const request = await fetch(srv.url()+'Usuario/administrarModulo', {
                method : 'POST',
                body : data
            });
            const res = await request.json();
            return {
                code: res.code,
                mssg: res.message,
                error: res.error
            };
        } catch (error) {
            return{
                code: 'Error',
                mssg: 'Error al enviar solicitud.'
            };
        }
    }

    async Reiniciar(empleado, usuario){
        try {
            let data = new FormData();
            data.append('empleado', empleado);
            data.append('usuario', usuario);
            const request = await fetch(srv.url()+'Usuario/reinicio', {
                method : 'POST',
                body : data
            });
            const res = await request.json();
            return {
                status: res.status,
                mssg: res.message
            };
        } catch (error) {
            return{
                status: 'ERROR',
                mssg: 'Error al enviar solicitud.'
            };
        }
    }

    
}
class Mora{
    async Detalle(id){
        try {
            let data = new FormData();
            data.append('id', id);
            const request = await fetch(srv.url()+'Mora/buscar', {
                method : 'POST',
                body : data
            });
            const res = await request.json();
            return {
                status: 'OK',
                result: res,
                mssg: 'correcto'
            };
        } catch (error) {
            return{
                status: 'ERROR',
                result: [],
                mssg: 'Error al procesar. ['+error.message+']'
            };       
        }
    }

    async Modificar(data){
        try {
            const request = await fetch(srv.url()+'Mora/modificar', {
                method : 'POST',
                body : data
            });
            const res = await request.json();
            return {
                status: res.status,
                mssg: res.message
            };
        } catch (error) {
            return{
                status: 'ERROR',
                mssg: 'Error al enviar solicitud.'
            };
        }
    }

    async Eliminar(id){
        try {
            let data = new FormData();
            data.append('id', id);
            const request = await fetch(srv.url()+'Mora/eliminar', {
                method : 'POST',
                body : data
            });
            const res = await request.json();
            return {
                status: res.status,
                mssg: res.message
            };
        } catch (error) {
            return{
                status: 'ERROR',
                mssg: 'Error al enviar solicitud.'
            };
        }
    }
}
class Debitacion{

    async Buscar(id){
        try {
            let data = new FormData();
            data.append('id', id);
            const request = await fetch(srv.url()+'Debitacion/consulta', {
                method : 'POST',
                body : data
            });
            const res = await request.json();
            return {
                status: 'OK',
                result: res,
                mssg: 'correcto'
            };
        } catch (error) {
            return{
                status: 'ERROR',
                result: [],
                mssg: error.message
            };       
        }
    }

    async Aplicar(data){
        try {
            const request = await fetch(srv.url()+'Debitacion/guardar', {
                method : 'POST',
                body : data
            });
            const res = await request.json();
            return {
                status: res.status,
                mssg: res.message,
                id: res.id,
                error: res.error
            };
        } catch (error) {
            return{
                status: 'ERROR',
                mssg: 'Error al enviar solicitud.'
            };
        }
    }

    async obtenerTarifa(monto){
        try {
            let data = new FormData();
            data.append('tipo', 'calculo');
            data.append('buscar', monto);
            const request = await fetch(srv.url()+'Tarifa/REST', {
                method : 'POST',
                body : data
            });
            const res = await request.json();
            return {
                code: 'Ok',
                resp: res,
                mssg: 'correcto'
            };
        } catch (error) {
            return{
                code: 'Error',
                resp: [],
                mssg: error.message
            };       
        }
    }
}
class Tarifa{

    async Listar(t, b){
        try {
            let data = new FormData();
            data.append('tipo', t);
            data.append('buscar', b);
            const request = await fetch(srv.url()+'Tarifa/consulta', {
                method : 'POST',
                body : data
            });
            const res = await request.json();
            return {
                status: 'OK',
                result: res,
                mssg: 'correcto'
            };
        } catch (error) {
            return{
                status: 'ERROR',
                result: [],
                mssg: error.message
            };       
        }
    }

    async Detalle(id){
        try {
            let data = new FormData();
            data.append('id', id);
            const request = await fetch(srv.url()+'Tarifa/consulta_detalle', {
                method : 'POST',
                body : data
            });
            const res = await request.json();
            return {
                status: 'OK',
                result: res,
                mssg: 'correcto'
            };
        } catch (error) {
            return{
                status: 'ERROR',
                result: [],
                mssg: error.message
            };       
        }
    }

    async Registrar(data){
        try {
            const request = await fetch(srv.url()+'Tarifa/registrar', {
                method : 'POST',
                body : data
            });
            const res = await request.json();
            return {
                status: res.status,
                mssg: res.message,
                id: res.id,
                error: res.error
            };
        } catch (error) {
            return{
                status: 'ERROR',
                mssg: 'Error al enviar solicitud.'
            };
        }
    }

    async Eliminar(id){
        try {
            let data = new FormData();
            data.append('id', id);
            const request = await fetch(srv.url()+'Tarifa/eliminar', {
                method : 'POST',
                body : data
            });
            const res = await request.json();
            return {
                status: res.status,
                mssg: res.message
            };
        } catch (error) {
            return{
                status: 'ERROR',
                mssg: 'Error al enviar solicitud.'
            };
        }
    }

    async Actualizar(data){
        try {
            const request = await fetch(srv.url()+'Tarifa/modificar', {
                method : 'POST',
                body : data
            });
            const res = await request.json();
            return {
                status: res.status,
                mssg: res.message,
                error: res.error
            };
        } catch (error) {
            return{
                status: 'ERROR',
                mssg: 'Error al enviar solicitud.'
            };
        }
    }

    async obtenerTarifa(monto){
        try {
            let data = new FormData();
            data.append('tipo', 'calculo');
            data.append('buscar', monto);
            const request = await fetch(srv.url()+'Tarifa/REST', {
                method : 'POST',
                body : data
            });
            const res = await request.json();
            return {
                code: 'Ok',
                resp: res,
                mssg: 'correcto'
            };
        } catch (error) {
            return{
                code: 'Error',
                resp: [],
                mssg: error.message
            };       
        }
    }
}
class NotasCredito {
    async get_by_credito(id,settings = {}){
        try {
            let data = new FormData();
            data.append('idcredito', id);
            if (Object.keys(settings).length > 0) {
                for ( var k in settings) {
                    data.append(k, settings[k]);
                }
            }
            const request = await fetch(srv.url()+'api/v1/credito-notas/getbycredito',{
                method : 'POST',
                body : data
            });
            const res = await request.json();
            return res;
        } catch (error) {
            return;       
        }
    }
    async get_all(id){
        try {
            let data = new FormData();
            data.append('id', id);            
            const request = await fetch(srv.url()+'NotaCredito/lista', {
                method : 'POST',
                body : data
            });
            const res = await request.json();
            return res;
        } catch (error) {
            return;       
        }
    }
    async get_data(id){
        try {
            let data = new FormData();
            data.append('id', id);
            const request = await fetch(srv.url()+'api/v1/credito-notas/getdata', {
                method : 'POST',
                body : data
            });
            const res = await request.json();
            return res;
        } catch (error) {
            return;       
        }
    }
    async save(data){
        try {
            const request = await fetch(srv.url()+'api/v1/credito-notas/save', {
                method : 'POST',
                body : data
            });
            const res = await request.json();
            return res;
        } catch (error) {
            return{
                code: 'ERR',
                mssg: 'Error al enviar solicitud.'
            };
        }
    }    
    async update(data){
        try {
            const request = await fetch(srv.url()+'api/v1/credito-notas/update', {
                method : 'POST',
                body : data
            });
            const res = await request.json();
            return res;
        } catch (error) {
            return{
                code: 'ERROR',
                mssg: 'Error al enviar solicitud.'
            };
        }
    }
    async delete(id){
        try {
            let data = new FormData();
            data.append('id', id);
            const request = await fetch(srv.url()+'api/v1/credito-notas/delete', {
                method : 'POST',
                body : data
            });
            const res = await request.json();
            return res;
        } catch (error) {
            return{
                code: 'ERROR',
                mssg: 'Error al enviar solicitud.'
            };
        }
    }
}
class NotasCliente {
    async get_by_cliente(id,settings = {}){
        try {
            let data = new FormData();
            data.append('idcliente', id);
            if (Object.keys(settings).length > 0) {
                for ( var k in settings) {
                    data.append(k, settings[k]);
                }
            }
            const request = await fetch(srv.url()+'api/v1/cliente-notas/getbycliente',{
                method : 'POST',
                body : data
            });
            const res = await request.json();
            return res;
        } catch (error) {
            return;       
        }
    }
    async get_data(id){
        try {
            let data = new FormData();
            data.append('id', id);
            const request = await fetch(srv.url()+'api/v1/cliente-notas/getdata', {
                method : 'POST',
                body : data
            });
            const res = await request.json();
            return res;
        } catch (error) {
            return;       
        }
    }
    async save(data){
        try {
            const request = await fetch(srv.url()+'api/v1/cliente-notas/save', {
                method : 'POST',
                body : data
            });
            const res = await request.json();
            return res;
        } catch (error) {
            return{
                code: 'ERR',
                mssg: 'Error al enviar solicitud.'
            };
        }
    }    
    async update(data){
        try {
            const request = await fetch(srv.url()+'api/v1/cliente-notas/update', {
                method : 'POST',
                body : data
            });
            const res = await request.json();
            return res;
        } catch (error) {
            return{
                code: 'ERROR',
                mssg: 'Error al enviar solicitud.'
            };
        }
    }
    async delete(id){
        try {
            let data = new FormData();
            data.append('id', id);
            const request = await fetch(srv.url()+'api/v1/cliente-notas/delete', {
                method : 'POST',
                body : data
            });
            const res = await request.json();
            return res;
        } catch (error) {
            return{
                code: 'ERROR',
                mssg: 'Error al enviar solicitud.'
            };
        }
    }
}
class Cuota{
    async get_by_credito(idcredito, settings = {}){
        try {
            let data = new FormData();
            data.append('idcredito', idcredito);
            if (settings.orden_objetivo) {
                data.append('orden_objetivo', settings.orden_objetivo);
            }
            if (settings.orden_tipo) {
                data.append('orden_tipo', settings.orden_tipo);
            }
            const request = await fetch(srv.url()+'api/v1/cuota/getbycredito', {
                method : 'POST',
                body : data
            });
            const res = await request.json();
            return res;
        } catch (error) {
            return;       
        }
    }
}
class Pago{
    async Listar(data){
        try {
            const request = await fetch(srv.url()+'Pago/consulta', {
                method : 'POST',
                body : data
            });
            const res = await request.json();
            return {
                status: 'OK',
                result: res,
                mssg: 'correcto'
            };
        } catch (error) {
            return{
                status: 'ERROR',
                result: [],
                mssg: 'Error al procesar. ['+error.message+']'
            };       
        }
    }

    async Guardar(data){
        try {
            const request = await fetch(srv.url()+'Pago/registrar', {
                method : 'POST',
                body : data
            });
            const res = await request.json();
            return {
                status: res.status,
                mssg: res.message,
                error: res.error,
                id: res.id
            };
        } catch (error) {
            return{
                status: 'ERROR',
                mssg: 'Error al enviar solicitud.'
            };
        }
    }

    async Anular(data){
        try {
            const request = await fetch(srv.url()+'Pago/anulacion', {
                method : 'POST',
                body : data
            });
            const res = await request.json();
            return {
                status: res.status,
                mssg: res.message
            };
        } catch (error) {
            return{
                status: 'ERROR',
                mssg: 'Error al enviar solicitud.'
            };
        }
    }
    async get_by_credito(idcredito, settings = {}){
        try {
            let data = new FormData();
            data.append('idcredito', idcredito);
            if (settings.orden_objetivo) {
                data.append('orden_objetivo', settings.orden_objetivo);
            }
            if (settings.orden_tipo) {
                data.append('orden_tipo', settings.orden_tipo);
            }
            const request = await fetch(srv.url()+'api/v1/pago/getbycredito', {
                method : 'POST',
                body : data
            });
            const res = await request.json();
            return res;
        } catch (error) {
            return;       
        }
    }
}
class Credito{
    async Listar(tipo, buscar, limite){
        try {
            let data = new FormData();
            data.append('tipo', tipo);
            data.append('buscar', buscar);
            data.append('limite', limite);
            const request = await fetch(srv.url()+'Credito/lista', {
                method : 'POST',
                body : data
            });
            const res = await request.json();
            return {
                status: 'OK',
                result: res,
                mssg: 'correcto'
            };
        } catch (error) {
            return{
                costatusde: 'ERROR',
                result: [],
                mssg: error.message
            };       
        }
    }    
    async ObtenerCuotas(id){
        try {
            let data = new FormData();
            data.append('tipo', 'cuotas');
            data.append('id', id);
            const request = await fetch(srv.url()+'Credito/consulta_cuotas', {
                method : 'POST',
                body : data
            });
            const res = await request.json();
            return {
                status: 'OK',
                result: res,
                mssg: 'correcto'
            };
        } catch (error) {
            return{
                status: 'ERROR',
                result: [],
                mssg: error.message
            };       
        }
    }
    async ObtenerMoras(id){
        try {
            let data = new FormData();
            data.append('id', id);
            const request = await fetch(srv.url()+'Credito/consulta_moras', {
                method : 'POST',
                body : data
            });
            const res = await request.json();
            return {
                status: 'OK',
                result: res,
                mssg: 'correcto'
            };
        } catch (error) {
            return{
                status: 'ERROR',
                result: [],
                mssg: error.message
            };       
        }
    }
    async ObtenerPagos(id){
        try {
            let data = new FormData();
            data.append('id', id);
            const request = await fetch(srv.url()+'Credito/consulta_pagos', {
                method : 'POST',
                body : data
            });
            const res = await request.json();
            return {
                status: 'OK',
                result: res,
                mssg: 'correcto'
            };
        } catch (error) {
            return{
                status: 'ERROR',
                resp: [],
                mssg: error.message
            };       
        }
    }
    async ObtenerProrrogas(id){
        try {
            let data = new FormData();
            data.append('id', id);
            const request = await fetch(srv.url()+'Credito/consulta_prorrogas', {
                method : 'POST',
                body : data
            });
            const res = await request.json();
            return {
                status: 'OK',
                result: res,
                mssg: 'correcto'
            };
        } catch (error) {
            return{
                status: 'ERROR',
                result: [],
                mssg: error.message
            };       
        }
    }
    async Registrar(data){
        try {
            const request = await fetch(srv.url()+'Credito/registrar', {
                method : 'POST',
                body : data
            });
            const res = await request.json();
            return {
                status: res.status,
                mssg: res.message,
                id: res.id,
                error: res.error
            };
        } catch (error) {
            return{
                status: 'ERROR',
                mssg: 'Error al enviar solicitud.'
            };
        }
    }
    async Congelar(id){
        try {
            let data = new FormData();
            data.append('id', id);
            const request = await fetch(srv.url()+'Credito/procesar_congelacion', {
                method : 'POST',
                body : data
            });
            const res = await request.json();
            return {
                status: res.status,
                mssg: res.message
            };
        } catch (error) {
            return{
                status: 'ERROR',
                mssg: 'Error al enviar solicitud.'
            };
        }
    }
    async Remate(id){
        try {
            let data = new FormData();
            data.append('id', id);
            const request = await fetch(srv.url()+'Credito/procesar_remate', {
                method : 'POST',
                body : data
            });
            const res = await request.json();
            return {
                status: res.status,
                mssg: res.message
            };
        } catch (error) {
            return{
                status: 'ERROR',
                mssg: 'Error al enviar solicitud.'
            };
        }
    }
    async ModificaPenalidad(id, estado){
        try {
            let data = new FormData();
            data.append('id', id);
            data.append('estado', estado);
            const request = await fetch(srv.url()+'Credito/modificar_penalidad', {
                method : 'POST',
                body : data
            });
            const res = await request.json();
            return {
                status: res.status,
                mssg: res.message
            };
        } catch (error) {
            return{
                status: 'ERROR',
                mssg: 'Error al enviar solicitud.'
            };
        }
    }
    async Eliminar(id){
        try {
            let data = new FormData();
            data.append('id', id);
            const request = await fetch(srv.url()+'Credito/eliminar', {
                method : 'POST',
                body : data
            });
            const res = await request.json();
            return {
                status: res.status,
                mssg: res.message
            };
        } catch (error) {
            return{
                status: 'ERROR',
                mssg: 'Error al enviar solicitud.'
            };
        }
    }


    async save(data){
        try {
            let req = await fetch(srv.url()+'api/v1/credito/save', {
                method: 'POST',
                body: data
            });
            let res = await req.json();
            return res;
        } catch (error) {
            return;
        }
    }
    async delete(id){
        try {
            let data = new FormData();
            data.append('id',id)
            let req = await fetch(srv.url()+'api/v1/credito/delete', {
                method: 'POST',
                body: data
            });
            let res = await req.json();
            return res;
        } catch (error) {
            console.log(error)
            return;
        }
    }
    async apply_congelacion(id){
        try {
            let data = new FormData();
            data.append('id', id);
            const request = await fetch(srv.url()+'api/v1/credito/set-pause',{
                method : 'POST',
                body : data
            });
            const res = await request.json();
            return {
                status: res.status,
                mssg: res.message
            };
        } catch (error) {
            return{
                status: 'ERROR',
                mssg: 'Error al enviar solicitud.'
            };
        }
    }
    async apply_remate(id){
        try {
            let data = new FormData();
            data.append('id', id);
            const request = await fetch(srv.url()+'api/v1/credito/setremate', {
                method : 'POST',
                body : data
            });
            const res = await request.json();
            return {
                status: res.status,
                mssg: res.message
            };
        } catch (error) {
            return{
                status: 'ERROR',
                mssg: 'Error al enviar solicitud.'
            };
        }
    }
    async get_by_cliente(id){
        try {
            let data = new FormData();
            data.append('idcliente', id);
            const request = await fetch(srv.url()+'api/v1/credito/getbycliente', {
                method : 'POST',
                body : data
            });
            const res = await request.json();
            return res;
        } catch (error) {
            return;       
        }
    }

}
class Prenda{
    async Listar(tipo = '', buscar, limite){
        try {
            let data = new FormData();
            data.append('tipo', tipo);
            data.append('buscar', buscar);
            data.append('limite', limite);
            const request = await fetch(srv.url()+'Prenda/consulta', {
                method : 'POST',
                body : data
            });
            const res = await request.json();
            return {
                status: 'OK',
                result: res,
                mssg: 'correcto'
            };
        } catch (error) {
            return{
                status: 'ERROR',
                result: [],
                mssg: error.message
            };       
        }
    }
    async Registrar(data){
        try {
            const request = await fetch(srv.url()+'Prenda/registrar', {
                method : 'POST',
                body : data
            });
            const res = await request.json();
            return {
                status: res.status,
                mssg: res.message,
                error: res.error,
                id: res.id
            };
        } catch (error) {
            return{
                status: 'ERROR',
                error: [],
                mssg: 'Error al enviar solicitud.'
            };
        }
    }
    async ObtenerPropiedad(id){
        try {
            let data = new FormData();
            data.append('id', id);
            const request = await fetch(srv.url()+'Prenda/consulta_propiedad', {
                method : 'POST',
                body : data
            });
            const res = await request.json();
            return {
                status: 'OK',
                result: res,
                mssg: 'correcto'
            };
        } catch (error) {
            return{
                status: 'ERROR',
                result: [],
                mssg: error.message
            };       
        }
    }
    async Eliminar(id){
        try {
            let data = new FormData();
            data.append('id', id);
            const request = await fetch(srv.url()+'Prenda/Eliminar', {
                method : 'POST',
                body : data
            });
            const res = await request.json();
            return {
                status: res.status,
                error: res.error,
                mssg: res.message
            };
        } catch (error) {
            return{
                status: 'ERROR',
                error: [],
                mssg: 'Error al enviar solicitud.'
            };
        }
    }
    async Modificar(data){
        try {
            const request = await fetch(srv.url()+'Prenda/modificar', {
                method : 'POST',
                body : data
            });
            const res = await request.json();
            return {
                status: res.status,
                mssg: res.message
            };
        } catch (error) {
            return{
                status: 'ERROR',
                mssg: 'Error al enviar solicitud.'
            };
        }
    }
    async Transferir(data){
        try {
            const request = await fetch(srv.url()+'Propiedad/Establecer', {
                method : 'POST',
                body : data
            });
            const res = await request.json();
            return {
                status: res.status,
                mssg: res.message,
                id: res.id
            };
        } catch (error) {
            return{
                status: 'ERROR',
                mssg: 'Error al enviar solicitud.'
            };
        }
    }


    async get_data(id){
        try {
            let data = new FormData();
            data.append('id', id);
            const request = await fetch(srv.url()+'api/v1/prenda/getdata', {
                method : 'POST',
                body : data
            });
            const res = await request.json();
            return res;
        } catch (error) {
            console.log('[Prenda][gtID] '+error.message);
            return;       
        }
    }
    async get_by_cliente(id, settings = {}){
        try {
            let data = new FormData();
            if (Object.keys(settings).length > 0) {
                for ( var k in settings) {
                    data.append(k, settings[k]);
                }
            }
            data.append('idcliente', id);
            const request = await fetch(srv.url()+'api/v1/prenda/getbycliente', {
                method : 'POST',
                body : data
            });
            const res = await request.json();
            return res;
        } catch (error) {
            console.log('[Prenda][gtCliente] '+error.message);
            return;       
        }
    }
    async save(data){
        try {
            const request = await fetch(srv.url()+'api/v1/prenda/save', {
                method : 'POST',
                body : data
            });
            const res = await request.json();
            return res;
        } catch (error) {
            console.log('[Prenda][save] '+error.message);
            return;
        }
    }    
    async update(data){
        try {
            const request = await fetch(srv.url()+'api/v1/prenda/update', {
                method : 'POST',
                body : data
            });
            const res = await request.json();
            return res;
        } catch (error) {
            console.log('[Prenda][update] '+error.message);
            return;
        }
    }
    async delete(id,idcliente){
        try {
            let data = new FormData();
            data.append('id', id);
            data.append('idcliente', idcliente);
            const request = await fetch(srv.url()+'api/v1/prenda/delete', {
                method : 'POST',
                body : data
            });
            const res = await request.json();
            return res;
        } catch (error) {
            console.log('[Prenda][delete] '+error.message);
            return;
        }
    }
    async get_form_edit(id,tipo){
        try {
            let data = new FormData();
            data.append('id', id);
            data.append('tipo', tipo);
            const request = await fetch(srv.url()+'api/v1/prenda/get_form_edit', {
                method : 'POST',
                body : data
            });
            const res = await request.json();
            return res;
        } catch (error) {
            console.log('[Prenda][FormEdit] '+error.message);
            return;       
        }
    }
    async get_form_by_tipo(tipo){
        try {
            let data = new FormData();
            data.append('tipo', tipo);
            const request = await fetch(srv.url()+'api/v1/prenda/get_form_by_tipo', {
                method : 'POST',
                body : data
            });
            const res = await request.json();
            return res;
        } catch (error) {
            console.log('[Prenda][FormNew] '+error.message);
            return;       
        }
    }
}
class Categoria{
    async Listar(tipo = 'todo', buscar = ''){
        try {
            let data = new FormData();
            data.append('tipo', tipo);
            data.append('buscar', buscar);
            const request = await fetch(srv.url()+'Categoria/consulta', {
                method : 'POST',
                body : data
            });
            const res = await request.json();
            return {
                status: 'OK',
                result: res,
                mssg: 'correcto'
            };
        } catch (error) {
            return{
                status: 'ERROR',
                result: [],
                mssg: error.message
            };       
        }
    }
    async Registrar(data){
        try {
            const request = await fetch(srv.url()+'Categoria/registrar', {
                method : 'POST',
                body : data
            });
            const res = await request.json();
            return {
                status: res.status,
                mssg: res.message,
                id: res.id,
                error: res.error
            };
        } catch (error) {
            return{
                status: 'ERROR',
                mssg: 'Error al enviar solicitud.'
            };
        }
    }
    async Actualizar(data){
        try {
            const request = await fetch(srv.url()+'Categoria/modificar', {
                method : 'POST',
                body : data
            });
            const res = await request.json();
            return {
                status: res.status,
                mssg: res.message,
                id: res.id,
                error: res.error
            };
        } catch (error) {
            return{
                status: 'ERROR',
                mssg: 'Error al enviar solicitud.'
            };
        }
    }
    async Eliminar(id){
        try {
            let data = new FormData();
            data.append('id', id);
            const request = await fetch(srv.url()+'Categoria/eliminar', {
                method : 'POST',
                body : data
            });
            const res = await request.json();
            return {
                status: res.status,
                mssg: res.message
            };
        } catch (error) {
            return{
                status: 'ERROR',
                mssg: 'Error al enviar solicitud.'
            };
        }
    }
    async ObtenerSegunTipo(tipo){
        try {
            let data = new FormData();
            data.append('tipo', 'por_tipo');
            data.append('valorA', tipo);
            data.append('valorB', '');
            const request = await fetch(srv.url()+'Categoria/REST', {
                method : 'POST',
                body : data
            });
            const res = await request.json();
            return {
                code: 'Ok',
                resp: res,
                mssg: 'correcto'
            };
        } catch (error) {
            return{
                code: 'Error',
                resp: [],
                mssg: error.message
            };       
        }
    }


    async get_by_tipo(idtipo){
        try {
            let data = new FormData();
            data.append('idtipo', idtipo);
            const request = await fetch(srv.url()+'api/v1/categoria/getbytipo', {
                method: 'POST',
                body: data
            });
            const res = await request.json();
            return res;
        } catch (error) {
            return;       
        }
    }
}
class TipoPrenda{
    async Listar(){
        try {
            const request = await fetch(srv.url()+'TipoPrenda/consulta');
            const res = await request.json();
            return {
                status: 'OK',
                result: res,
                mssg: 'correcto'
            };
        } catch (error) {
            return{
                status: 'ERROR',
                result: [],
                mssg: error.message
            };       
        }
    }
    async Registrar(data){
        try {
            const request = await fetch(srv.url()+'TipoPrenda/registrar', {
                method : 'POST',
                body : data
            });
            const res = await request.json();
            return {
                status: res.status,
                mssg: res.message,
                id: res.id,
                error: res.error
            };
        } catch (error) {
            return{
                status: 'ERROR',
                mssg: 'Error al enviar solicitud.'
            };
        }
    }
    async Eliminar(id){
        try {
            let data = new FormData();
            data.append('id', id);
            const request = await fetch(srv.url()+'TipoPrenda/eliminar', {
                method : 'POST',
                body : data
            });
            const res = await request.json();
            return {
                status: res.status,
                mssg: res.message
            };
        } catch (error) {
            return{
                status: 'ERROR',
                mssg: 'Error al enviar solicitud.'
            };
        }
    }

    async get_all(){
        try {
            const request = await fetch(srv.url()+'api/v1/tipoprenda/getall');
            const res = await request.json();
            return res;
        } catch (error) {
            return;       
        }
    }
}
class Conducta{
    async getbycliente(idcliente){
        try {
            let data = new FormData();
            data.append('idcliente', idcliente);
            const request = await fetch(srv.url()+'api/v1/conducta/getbycliente', {
                method : 'POST',
                body : data
            });
            const res = await request.json();
            return res;
        } catch (error) {
            // return;       
        }
    }
    async save(data){
        try {
            const request = await fetch(srv.url()+'api/v1/conducta/save', {
                method : 'POST',
                body : data
            });
            const res = await request.json();
            return res;
        } catch (error) {
            return{
                code: 'ERR',
                mssg: 'Error al enviar solicitud.'
            };
        }
    } 
}
class Cliente {
    async Listar(t = 'TODO', b = '', l = 10){
        try {
            let data = new FormData();
            data.append('tipo', t);
            data.append('buscar', b);
            data.append('limite', l);
            const request = await fetch(srv.url()+'Cliente/consulta', {
                method : 'POST',
                body : data
            });
            const res = await request.json();
            return {
                status: 'OK',
                result: res,
                mssg: 'correcto'
            };
        } catch (error) {
            return{
                status: 'ERROR',
                result: [],
                mssg: error.message
            };       
        }
    }
    async Registrar(data){
        try {
            const request = await fetch(srv.url()+'Cliente/registrar', {
                method : 'POST',
                body : data
            });
            const res = await request.json();
            return {
                status: res.status,
                mssg: res.message,
                id: res.id,
                error: res.error
            };
        } catch (error) {
            return{
                status: 'ERROR',
                mssg: 'Error al enviar solicitud.'
            };
        }
    }
    async Eliminar(id){
        try {
            let data = new FormData();
            data.append('id', id);
            const request = await fetch(srv.url()+'Cliente/eliminar', {
                method : 'POST',
                body : data
            });
            const res = await request.json();
            return {
                code: res.code,
                mssg: res.message
            };
        } catch (error) {
            return{
                code: 'Error',
                mssg: 'Error al enviar solicitud.'
            };
        }
    }
    async Actualizar(data){
        try {
            const request = await fetch(srv.url()+'Cliente/modificar', {
                method : 'POST',
                body : data
            });
            const res = await request.json();
            return {
                status: res.status,
                mssg: res.message,
                error: res.error
            };
        } catch (error) {
            return{
                status: 'ERROR',
                mssg: 'Error al enviar solicitud.'
            };
        }
    }
    async ActualizarImagen(data){
        try {
            const request = await fetch(srv.url()+'Cliente/modificar_imagen', {
                method : 'POST',
                body : data
            });
            const res = await request.json();
            return {
                status: res.status,
                mssg: res.message,
                url_resp: res.url
            };
        } catch (error) {
            return{
                status: 'ERROR',
                mssg: 'Error al enviar solicitud.'
            };
        }
    }
    async BuscarPrendas(id, tipo = 'todo'){
        try {
            let data = new FormData();
            data.append('tipo', tipo);
            data.append('id', id);
            const request = await fetch(srv.url()+'Cliente/consulta_prendas', {
                method : 'POST',
                body : data
            });
            const res = await request.json();
            return {
                status: 'OK',
                result: res
            };
        } catch (error) {
            return{
                status: 'ERROR',
                mssg: 'Error al enviar solicitud.'
            };
        }
    }
    async BuscarCreditos(id, orden = 'DESC', limite = 0){
        try {
            let data = new FormData();
            data.append('id', id);
            data.append('limite', limite);
            data.append('orden', orden);
            const request = await fetch(srv.url()+'Cliente/consulta_creditos', {
                method : 'POST',
                body : data
            });
            const res = await request.json();
            return {
                status: 'OK',
                result: res
            };
        } catch (error) {
            return{
                status: 'ERROR',
                mssg: 'Error al enviar solicitud.'
            };
        }
    }
    async CreditosRecientes(id){
        try {
            let data = new FormData();
            data.append('tipo', 'cliente_reciente');
            data.append('id', id);
            const request = await fetch(srv.url()+'Credito/Consultar', {
                method : 'POST',
                body : data
            });
            const res = await request.json();
            return {
                status: 'OK',
                result: res
            };
        } catch (error) {
            return{
                status: 'Error',
                mssg: 'Error al enviar solicitud.'
            };
        }
    }
    async EnviarCorreo(data){
        try {
            const request = await fetch(srv.url()+'Cliente/envio_email', {
                method : 'POST',
                body : data
            });
            const res = await request.json();
            return {
                status: res.status,
                mssg: res.message
            };
        } catch (error) {
            return{
                status: 'ERROR',
                mssg: 'Error al enviar solicitud.'
            };
        }
    }

    async get_all(settings = {}){
        try {
            let data = new FormData();
            if (Object.keys(settings).length > 0) {
                for ( var k in settings) {
                    data.append(k, settings[k]);
                }
            }
            let req = await fetch(srv.url()+'api/v1/cliente/getall', {
                method: 'POST',
                body: data
            });
            let res = await req.json();
            return res;
        } catch (error) {
            console.log('[Cliente][GetAll] '+error)
            return;
        }
    }
    async get_data(id){
        try {
            let data = new FormData();
            data.append('id', id);
            const request = await fetch(srv.url()+'api/v1/cliente/getdata', {
                method : 'POST',
                body : data
            });
            const res = await request.json();
            return res;
        } catch (error) {
            console.log('[Cliente][gtID] '+error.message);
            return;       
        }
    }
    async save(data){
        try {
            const request = await fetch(srv.url()+'api/v1/cliente/save', {
                method : 'POST',
                body : data
            });
            const res = await request.json();
            return res;
        } catch (error) {
            console.log('[Cliente][save] '+error.message);
            return;
        }
    }    
    async update(data){
        try {
            const request = await fetch(srv.url()+'api/v1/cliente/update', {
                method : 'POST',
                body : data
            });
            const res = await request.json();
            return res;
        } catch (error) {
            console.log('[Cliente][update] '+error.message);
            return;
        }
    }
    async delete(id){
        try {
            let data = new FormData();
            data.append('id', id);
            const request = await fetch(srv.url()+'api/v1/cliente/delete', {
                method : 'POST',
                body : data
            });
            const res = await request.json();
            return res;
        } catch (error) {
            console.log('[Cliente][delete] '+error.message);
            return;
        }
    }

}
class CreditoApi {
    async save(data){
        try {
            let req = await fetch(srv.url()+'api/v1/credito/save', {
                method: 'POST',
                body: data
            });
            let res = await req.json();
            return res;
        } catch (error) {
            console.log('[Credito][save] '+error.message);
            return;
        }
    }
}


class Corex {
    // funcion estandarizada para el manejo de solicitudes HTTP
    async request(endpoint, { method = 'GET', body = null } = {}){
        try {
            const req = await fetch(App.URL_STATIC(endpoint), { method, body });
            if (!req.ok){
                return { ok: false, data: null, error: `Error HTTP ${ req.status }` };
            }

            const resp = await req.json(); 
            if (!resp.success && resp.status != 'OK') {
                return { ok: false, data: null, message: resp.message || 'Respuesta erronea del servidor.', errors: resp.errors || null };
            }

            return { 
                ok: true, 
                data: resp.result || [], 
                message: resp.message || 'Sin respuesta.', 
                errors: resp.errors || null,
                id: resp.id || null,
                rows_affected: resp.rows_affected || null
            };
        } catch (error) {
            console.error(`[${ endpoint }]`, error);
            return { ok: false, data: null, error: error.message || 'Error al procesar.'};
        }
    }
} 


let srv = new Servidor();
let not = new Notificacion();
let tool = new Tools();