const renderLoader = (parent,loaderClass) => {
    const loader = `
    <div class='${loaderClass}'>
        
    </div>
    `;
    parent.insertAdjacentHTML('afterbegin',loader);
    };

function search(showall = 0)
{

  ////console.log(wizardValuesArray);
  document.querySelector('#results').innerHTML = '';
  let searchText = document.getElementById('site-search').value;
  if (searchText.length > 2 || showall == 1) {
        renderLoader(document.querySelector('#results'),'loader');
        if(showall == 1){
            searchText = 1; 
        }

        $.ajax({
      
            url:"AJAX/AJAX.php",
            method:"POST",
            data:{search:searchText},
            dataType:"json",
            success:function(data)
            {
              document.querySelector('#results').innerHTML='';
              //console.log(data);
              document.getElementById('results').insertAdjacentHTML('afterbegin',data);
            
            },
            error: function(data){
              document.querySelector('#results').innerHTML='';
              console.log(data);
            }
          }) 

  }else{
      console.log('smaller');
  }
}

//to users can enter it
const searchTextInput = document.getElementById('site-search');
    searchTextInput.addEventListener("keyup",function (event) {
        if(event.key == "Enter"){
            search();
        }
        
    });


function deleteRecord(id) {
    console.log(`So you want delete ${id}`);

     $.ajax({
      
        url:"AJAX/AJAX.php",
        method:"POST",
        data:{remove:id},
        dataType:"json",
        success:function(data)
        {
          /* document.querySelector('#results').innerHTML='';
          console.log(data);
          document.getElementById('results').insertAdjacentHTML('afterbegin',data); */
        
        },
        error: function(data){
         // console.log(data);
        }
      })  
      document.getElementById(`row-${id}`).classList.add('removed-item');
      setTimeout(() => {  document.getElementById(`row-${id}`).remove(); }, 950);
   
    
}

/********
 * 
 * deletes all checked row
 * 
 */

function deleteMulti() {
    let prompt = confirm('Opravdu smazat více záznamů?')
    if (prompt) {
        var inputs = document.querySelectorAll('.check-check'); 
        for (var i = 0; i < inputs.length; i++) {   
            if(inputs[i].checked == true){
                deleteRecord(inputs[i].name);  
            }   
        }  
    }
}


function markEmpty() {
    var inputsUsers = document.querySelectorAll('.email');
    var inputsCourses = document.querySelectorAll('.coursename');

    for (var i = 0; i < inputsUsers.length; i++) {   
        if(inputsUsers[i].value == 'N/A'){
            console.log(`yup ${inputsUsers[i]}`);
            /* let row = inputsUsers[i].id
            row = row.replace('email-','');

            document.getElementById(`check-${row}`).checked = true; */

           // .checked == true  
        }   
    } 
    for (var i = 0; i < inputsCourses.length; i++) {   
        if(inputsCourses[i].checked == true){
          //  deleteRecord(inputsCourses[i].name);  
        }   
    }   
}

/**
 * 
 * just to create input field and ok button
 */

function editRecord(id) {
   let originalValue = document.getElementById(`vcode-${id}`).innerText;
   document.getElementById(`vcode-${id}`).innerText = '';
   let inputField = `<input type="text" size="10" value="${originalValue}" id="newValueRecord-${id}"></input><input type="button" value="OK" class="ok-button" onclick="updateRecord(${id})"></input>`;
   document.getElementById(`vcode-${id}`).insertAdjacentHTML('afterbegin',inputField);
   console.log(originalValue);
    
}

/***
 * 
 * from redirRecord input AJAX call to update in database and some fancy efect for the user
 * 
 */

function updateRecord(id) {
    let newValue = document.getElementById(`newValueRecord-${id}`).value;
    if(validateUpdate(newValue)){ 
        $.ajax({
        
            url:"AJAX/AJAX.php",
            method:"POST",
            data:{update:`${id}-${newValue}`},
            dataType:"json",
            success:function(data)
            {   
            
            },
            error: function(data){
            
            }
        })
        document.getElementById(`newValueRecord-${id}`).classList.remove('is-invalid');
        document.getElementById(`vcode-${id}`).classList.remove('is-invalid-bg');  
        console.log('doing it');
                document.getElementById(`vcode-${id}`).innerHTML = '';
                document.getElementById(`vcode-${id}`).innerText = newValue;
                document.getElementById(`row-${id}`).classList.add('record-updated');
                setTimeout(() => {  document.getElementById(`row-${id}`).classList.remove('record-updated'); }, 950);
                console.log('end');
    }else{
        document.getElementById(`newValueRecord-${id}`).classList.add('is-invalid');
        document.getElementById(`vcode-${id}`).classList.add('is-invalid-bg');
    }
    

}


function validateUpdate(value) {
    if(value.length > 0 && value.length <  61 && !isNaN(value)){
        return true;
    }else{
        return false;
    }
}


function check(id) {
    if(document.getElementById(`check-${id}`).checked == true) {
        document.getElementById(`check-${id}`).checked = false
        document.getElementById(`row-${id}`).classList.remove('clicked');
    }else{
        document.getElementById(`check-${id}`).checked = true
        document.getElementById(`row-${id}`).classList.add('clicked');  
    }
}

function classCheckboxChange(id) {
    if(document.getElementById(`check-${id}`).checked == true) {
        //document.getElementById(`check-${id}`).checked = false
        document.getElementById(`row-${id}`).classList.add('clicked');
    }else{
        //document.getElementById(`check-${id}`).checked = true
        document.getElementById(`row-${id}`).classList.remove('clicked');  
    }
}


function checkAll() {
    let value = document.getElementById('maincheck');
    let elementArray = document.getElementsByClassName('check-check');
    
    for (let index = 0; index < elementArray.length; index++) {
        const element = elementArray[index];
        element.checked = value.checked;
       // console.log(element.name);
        if (value.checked == false){
            document.getElementById(`row-${element.name}`).classList.remove('clicked');
        }else{
            document.getElementById(`row-${element.name}`).classList.add('clicked'); 
        }

        
    }
}