
const defaultCategoryElement = document.getElementById('id_s_local_my_organization_default_category');
const defaultRoleElement = document.getElementById('id_s_local_my_organization_default_role');
const newElement = `<div class="invalid-info-notice form-description alert-danger alert">Není možné aby bylo nastavena výchozí role na žádná a výchozí kategorie na hodnotu jinou než žádná</div>`;
defaultCategoryElement.addEventListener('change', () => {changeCheck();});
defaultRoleElement.addEventListener('change', () => {changeCheck();});

function changeCheck() {
    let defaultCategory = document.getElementById('id_s_local_my_organization_default_category').value;
    let defaultRole = document.getElementById('id_s_local_my_organization_default_role').value;  
    if(defaultCategory > -1 && defaultRole == -1){
        defaultCategoryElement.classList.add('is-invalid');
        deleteExcesive();
        defaultCategoryElement.insertAdjacentHTML('afterend',newElement); 
    }else{
        defaultCategoryElement.classList.remove('is-invalid');
        deleteExcesive();
       
    }
}


function deleteExcesive() {
    let el =  document.querySelector('.invalid-info-notice');
       if(el){
        document.querySelectorAll('.invalid-info-notice').forEach(function(a){
            a.remove()
            });
       }
    
}