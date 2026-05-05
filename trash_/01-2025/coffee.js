// og-rute: global-code/js

class Module {
    loader(id, action){
		let area = (typeof id === 'object') ? id : document.getElementById(id);	
		if (area) {
			if (action === 'open') {
				area.classList.add('openx');
			}else if(action === 'close'){
				area.classList.remove('openx');
			}
		}
	}
}