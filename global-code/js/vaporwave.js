
class Vaporwave {
  reminder_open(id, data = {}){
    let e = (typeof id === 'object') ? id : document.getElementById(id);
    let alert = document.createElement('div');
    let cants = document.getElementsByClassName('reminder-item').length;
    cants = (cants) ? cants + 1 : 1;
    let gen_id = e.id+'-a'+cants;
    alert.id = gen_id;
    alert.className = (data.style.length>0) ? 'reminder-item reminder-'+data.style : 'reminder-item';
    alert.innerHTML = `
      <div class="reminder-icon">
        <svg version="1.1" xmlns="http://www.w3.org/2000/svg" 		xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 488.9 488.9" style="enable-background:new 0 0 488.9 488.9;" xml:space="preserve">
        <g><g>
        <path d="M239.15,0c31.9,0,57.7,25.8,57.7,57.7s-25.8,57.7-57.7,57.7s-57.7-25.8-57.7-57.7S207.25,0,239.15,0z M291.65,151.6h-1.5
        h-92.8h-3.4c-19,0-34.3,15.4-34.3,34.3l0,0c0,19,15.4,34.3,34.3,34.3h3.4v200h-37.7v68.7h169.6v-68.7h-37.5V151.6H291.65z"/>
        </g></g>
        </svg>
      </div>
      <div class="reminder-content">
        <div class="reminder-text">${data.message}</div>
      </div>
      <div class="reminder-close" ${data.dataset}>&#10006;</div>
    `;
    e.appendChild(alert);
    e.style.height = `${ cants*54 }px`;
  }

  reminder_close(el_object){
    let ael = (typeof el_object === 'object') ? el_object : document.getElementById(id); 
    let parent_box = ael.parentNode.parentNode;   
    let parent_item = ael.parentNode;
    parent_box.removeChild(parent_item);
    let cants = document.getElementsByClassName('reminder-item').length;
    parent_box.style.height = `${ cants*54 }px`;
  }
  
  table_filter(){
    // Declare variables
    var input, filter, table, tr, td, i, txtValue;
    input = document.getElementById("myInput");
    filter = input.value.toUpperCase();
    table = document.getElementById("myTable");
    tr = table.getElementsByTagName("tr");
  
    // Loop through all table rows, and hide those who don't match the search query
    for (i = 0; i < tr.length; i++) {
      td = tr[i].getElementsByTagName("td")[0];
      if (td) {
        txtValue = td.textContent || td.innerText;
        if (txtValue.toUpperCase().indexOf(filter) > -1) {
          tr[i].style.display = "";
        } else {
          tr[i].style.display = "none";
        }
      }
    }
  }

  float_loading(action){
    let elm = document.getElementById('float-loading-elm');
    if (elm) {
      if (action === 'open') {
        elm.style.top = '10px';
        elm.style.right = '15px';
      }else if(action === 'close') {
        elm.style.top = '-500px';
        elm.style.right = '-500px';
      }
    }
  }
}

var vapor = new Vaporwave();

// document.addEventListener('click', event => {
//     if (event.target && event.target.matches('.reminder-close')) {
//         vapor.reminder_close(event.target);
//     }
// })