
class Soda {
	loader(mode, area, message = 'Cargando'){
		var content = document.querySelector(area);
		if (mode === 'open') {
			content.innerHTML = `
			<div class="side-loader">
				<span>${ message } </span>
	          	<svg xmlns="https://www.w3.org/2000/svg" viewBox="0 0 16 16">
	            	<circle r="4" cx="8" cy="8" fill="transparent"/>
	          	</svg>
			</div>	          	
			`;
			content.classList.add('visibled');
		} else{
			setTimeout(() => {
				content.innerHTML = '';
				content.classList.remove('visibled');
			}, 500)
		}
	}
}