class Dashboard{
    SidebarModuleActive(_name_class, _id){
        let items = document.getElementsByClassName(_name_class);
        if (items.length > 0) {
            for (let i = 0; i < items.length; i++) {
                items[i].classList.remove('is_active');
                if (items[i].dataset.ref === _id) {
                    items[i].classList.add('is_active');
                }                
            }
        }
    }
}