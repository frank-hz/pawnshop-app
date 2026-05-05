// og-rute: global-code/js

document.addEventListener('click', event => {
	if (event.target.matches('.tab-item') || event.target.matches('[name=modal-close-dissmiss]')) {
        let tabs = document.getElementsByClassName('tab-item');
        tabs = [].slice.call(tabs);
        tabs.forEach(e => {
            e.classList.remove('active');
        })
        event.target.classList.add('active');
	}
})
