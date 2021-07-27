/*****
 * 
 * Globální funkce pro všechny
 * 
 */

var wizardValuesArray = [];
const renderLoader = (parent,loaderClass) => {
    const loader = `
    <div class='${loaderClass}'>
        
    </div>
    `;
    parent.insertAdjacentHTML('afterbegin',loader);
    };



const renderNavigation = (parent,stepBack,stepForward) => {

  if(stepBack == 'Two'){

    const navigation = `<br>
    <button onclick="StepOneToTwo('${wizardValuesArray['stepOne']}')">Zpět</button>
    <button type='submit' onclick="step${stepForward}AJAX()">Další</button>
  
    `;
    parent.insertAdjacentHTML('beforeend',navigation);

  }else if(stepBack == 'One'){
  const navigation = `<br>
  <button onclick="stepOneAJAX()">Zpět</button>
  <button type='submit' onclick="step${stepForward}AJAX()">Další</button>

  `;
  parent.insertAdjacentHTML('beforeend',navigation);
  }else{
    const navigation = `<br>
    <button onclick="StepOneToTwo('${wizardValuesArray['stepOne']}')">Zpět k filtrům</button>
    <button type='submit' onclick="step${stepForward}AJAX()">Další</button>
  
    `;
    parent.insertAdjacentHTML('beforeend',navigation);
    }

  //parent.insertAdjacentHTML('beforeend',navigation);
  };    

    



      /**
       * 
       * fillUP
       */
  function fillUpData(step,data){
      // //console.log(`${step} and ${data}`);
      let fieldArray = [];
      fieldArray = data[`step${step}`];
     
      if(step == 'Three' && typeof(fieldArray) !== 'undefined'){


          for (let index = 0; index < fieldArray.length; index++) {
            const element = fieldArray[index];
            //element);
            document.querySelector(`input[name="${element.name}"]`).checked = true;
            
          }
        }

        if(step == 'Two' && typeof(fieldArray) !== 'undefined'){

          
          for (let index = 0; index < fieldArray.length; index++) {
            const element = fieldArray[index];
         
              if(element.type == 'date'){
                    document.getElementById(`${element.filter}`).value = element.data;
                }else if(element.type == 'select-multiple'){
                  //console.log(element);

                }
            }



        }



  }


 


// init wizard
stepOneAJAX();
/*******
 * 
 * Step One functions
 * 
 */


function renderStepOne(data){

    let output = `<h1 class='stepOne'>Zvolte prosím unikátní klíč reportu:</h1>
    
    
    <table><tr></tr>`;
    for (let index = 0; index < data.length; index++) {
        const element = data[index];
             output += '<tr>';
             output += `<td><button class='stepOne' onclick="StepOneToTwo('${element.name}')" value='${element.name}'>${element.nameForUsers}</button><td>`;
             output += `<td><td>`;
             output += `<td><td>`;
             output += '</tr>'; 
    }

    output += '</table>';
    if(wizardValuesArray.length > 1 ){
    output += `<button class='wizard-next' id='next-2'>Další</button>`;
    }

return output;
}






function stepOneAJAX()
{
   wizardValuesArray = [];
  document.querySelector('#wizard').innerHTML = '';
  renderLoader(document.querySelector('#wizard'),'loader');
  let getFields = 1;
  $.ajax({
      
    url:"AJAX/StepOne.php",
    method:"POST",
    data:{getFields:getFields},
    dataType:"json",
    success:function(data)
    {
      document.querySelector('#wizard').innerHTML='';
      document.querySelector('#wizard').insertAdjacentHTML("afterbegin",renderStepOne(data));

    }
  })
  
}




/**************
 * 
 * 
 * From first step to second step
 * 
 * 
 ***********/



 function StepOneToTwo(value){
////console.log(wizardValuesArray);
  if(wizardValuesArray.length == 0){
          wizardValuesArray = {
            'stepOne' : value,
          }
  }
   
   stepTwoAJAX(wizardValuesArray);


 }

 /*******
  * 
  * 
  * Step two
  * 
  * 
  */


function stepTwoAJAX(wizardValuesArray)
{
  ////console.log(wizardValuesArray);
  document.querySelector('#wizard').innerHTML = '';
  renderLoader(document.querySelector('#wizard'),'loader');
  
  $.ajax({
      
    url:"AJAX/StepTwo.php",
    method:"POST",
    data:{value:wizardValuesArray},
    dataType:"json",
    success:function(data)
    {
      document.querySelector('#wizard').innerHTML='';
      //document.querySelector('#wizard').insertAdjacentHTML("afterbegin",data);
    //  //console.log(data);
      document.querySelector('#wizard').insertAdjacentHTML("afterbegin",renderStepTwo(data));
            /**
       * 
       * fillUP
       */
      fillUpData('Two',data);
      renderNavigation(document.querySelector('#wizard'),'One','Three');
    },
    error: function(data){
      document.querySelector('#wizard').innerHTML='';

     ////console.log(data);
      // spusti to zase od zacatku
     // stepOneAJAX();
    }
  })
  
}




function renderStepTwo(data){

  let output = `<h1 class='stepOne'>Zvolte prosím filtry, které chcete aplikovat</h1>
  
  
  <table><form action="">`;
 // //console.log(data.fields);
  for (let index = 0; index < data.fields.length; index++) {
      const element = data.fields[index];
        ////console.log(data.fields[index]);
            output += '<tr>';
            output += stepTwoElementRender(data.fields[index]);
            output += '</tr>'; 
  }




return output;
}

/******
 * 
 * 
 * Function that renders the input fields for next step
 * 
 * 
 ********/

function stepTwoElementRender(data){
  //vytvoření array pro porovnání
  if( typeof(wizardValuesArray.stepTwo) !== 'undefined'){
    var fillUpArray = [];
    wizardValuesArray.stepTwo.forEach(element => {

        if(element.type == 'select-multiple'){
          fillUpArray = fillUpArray.concat(element.data);
      }
    });
  }
  
  let additional = '';
  let isSelected = '';
  let multiple = '';
  if(data.type == 'multipleselect'){


        multiple = 'multiple';
        data.type = 'select';
        for (let index = 0; index < data.options.length; index++) {
          const element = data.options[index];
          // pokud v předchozím kroku bylo vybráno, tak označí filtr
              if( typeof(wizardValuesArray.stepTwo) !== 'undefined'){
                      if(fillUpArray.indexOf(`${element}`) !== -1){
                        isSelected = 'selected';
                      }else{
                        isSelected = ''; 
                      }                
              }
          
          additional += `<option value='${data.options[index]}' ${isSelected}>${data.options[index]}</option>`;      
        }

        output = `
        <td>${data.description}: </td>
        <td><${data.type} class='StepTwo' id='${data.name}' name='${data.querry}' ${multiple}>${additional}</select></td>
        <td>Pro vybrání více možností stiskněte ctrl + myší naklikejte pole,<br>případně zde fungují klasícká pravidla pro vybírání jako v systému windows</td>
        `;



  }else{
  
      output = `
      <td>${data.description}: </td>
      <td><input type='${data.type}' class='StepTwo' id='${data.name}' name='${data.querry}' ${multiple}>${additional}</input></td>
      `;
  }


  return output;
}




function collectDataFromStepTwo(wizardValuesArray){
 let nodeArray = document.querySelectorAll('.StepTwo');
 let inputArray = [];
 let inputObject;
  for (let index = 0; index < nodeArray.length; index++) {
        const element = nodeArray[index];
          //select input
          if(element.type == 'select-multiple'){
                let selected = Array.from(element.options).filter(function (option) {
                  return option.selected;
                }).map(function (option) {
                  return option.value;
                });

                inputObject = {'filter':element.id,'data': selected,'querry': element.name, 'type':element.type};
                if(selected.length > 0){
                  inputArray.push(inputObject);
                }
          
            //date and text input    
          }else{
                inputObject = {'filter':element.id,'data': element.value,'querry': element.name, 'type':element.type};
                inputArray.push(inputObject);
            //text input    
          }


        //inputArray.push(inputObject);
    
  }

 wizardValuesArray['stepTwo'] = inputArray;
  return wizardValuesArray;
}


/**********
 * 
 * 
 * Step Three
 * 
 * 
 ***********/


/*******
 * 
 * 
 * check the date inputs
 * 
 * 
 **********/
function checkDateInputs(){
  let output,from,to;
   from ={value:''};
   to={value:''};

   if( document.querySelector('#startdate')){
    from = document.querySelector('#startdate');
   }

   if( document.querySelector('#enddate')){
    to = document.querySelector('#enddate');
   }

    if (from.value != '' && to.value != ''){
        if(from.value > to.value){
          output = false;
        }else{
          output = true;
        }
      
    }else{
      output = true;
    }

  
    return output;
}







function stepThreeAJAX()
{
  //console.log(wizardValuesArray);
  let check = checkDateInputs();
  if(check == false){
    let $element = `<br><span class='dateWarning'>Datum začítku musí být menší, než datum konce!</span>`;
    document.querySelector('#startdate').insertAdjacentHTML('afterend',$element);

  }else{
        if( typeof(wizardValuesArray.stepThree) !== 'undefined'){
          let wizardValuesArrayNew = collectDataFromStepTwo(wizardValuesArray);



            if (wizardValuesArrayNew.stepTwo.length > 0 ) {
             //console.log(wizardValuesArrayNew.stepTwo);
             wizardValuesArray['stepTwo'] = wizardValuesArrayNew['stepTwo']; 


            }else if(wizardValuesArray.stepTwo.length < 1 ) {
            //tadz je jeste problem
              wizardValuesArray = wizardValuesArray;
              //console.log(2); 
            }

          //console.log(wizardValuesArray);

          
        }else{
          //console.log(3); 
          wizardValuesArray = collectDataFromStepTwo(wizardValuesArray);
        }
      document.querySelector('#wizard').innerHTML = '';
      renderLoader(document.querySelector('#wizard'),'loader');
     // //console.log(wizardValuesArray);
      $.ajax({
          
        url:"AJAX/StepThree.php",
        method:"POST",
        data:{value:wizardValuesArray.stepOne},
        dataType:"json",
        success:function(data)
        {
          document.querySelector('#wizard').innerHTML='';
          //document.querySelector('#wizard').insertAdjacentHTML("afterbegin",data);
        // //console.log(data);
          document.querySelector('#wizard').insertAdjacentHTML("afterbegin",renderStepThree(data));
          /**
           * 
           * fillUP
           */
          fillUpData('Three',wizardValuesArray);

          renderNavigation(document.querySelector('#wizard'),'Two','Four');
        },
        error: function(data){
          document.querySelector('#wizard').innerHTML='';

        //console.log(data);
          // spusti to zase od zacatku
          renderNavigation(document.querySelector('#wizard'),'Two','Four');
          //stepOneAJAX();
        }
      })
  }
}


function renderStepThree(data){
  let output = `<h1>Zvolte prosím sloupce reportu:</h1>
  <table>`;
 

      for (let subObject in data) {
       
        
        //if (subObject == 'userBasicInfo' || subObject == 'courseBasicInfo') {
          if (subObject.indexOf('Basic') !== -1) {
          var specialCheck = 'checked disabled';
         } else {
          var specialCheck = '';
         }
        

        output += `<tr class='table-category-header'>
                       <td ><input type='checkbox' class='groupCheckbox' onClick="checkAllId('${subObject}')" id='${subObject}' ${specialCheck}></input></td> <td colspan = 2>${data[subObject].categoryName}</td>
                   </tr>`;
                 

                   for (let subSubObject in data[subObject].fields) {
                     const element = data[subObject];
                     //console.log(subObject);
                     /*if (subObject == 'userBasicInfo' || subObject == 'courseBasicInfo') {
                      var specialCheck = 'checked disabled';
                     } else {
                      var specialCheck = '';
                     }*/

                     output += `<tr>
                                    <td><input type='checkbox' class='StepThreeInput' id='${subObject}' name='${element.fields[subSubObject]}' value='${element.type[subSubObject]}' ${specialCheck}></input></td>                              
                                    <td>${element.translations[subSubObject]}</td>
                                    
                     
                                </tr>`;
                     
                     


                     
                   }


        
      }

  output += `</table>`;
return output;
}


/***
 * 
 * check the checkboxes
 */

function checkAllId(id){
  let mainChecker = document.querySelector(`#${id}`);
  var checkboxes = document.querySelectorAll(`#${id}`);
  if(mainChecker.checked === true){ 
          for (var i = 0; i < checkboxes.length; i++) {        
                checkboxes[i].checked = 'true';          
              }
          mainChecker.checked = 'true';
    }else{
          for (var i = 0; i < checkboxes.length; i++) {  
            checkboxes[i].checked = '';
          }
        mainChecker.checked = '';

    }      


}


/******
 * 
 * 
 * Step Four
 * 
 * 
 ********/

function stepFourAJAX()
{
  wizardValuesArray = collectDataFromStepThree(wizardValuesArray);
  //console.log(wizardValuesArray);
  ////console.log(wizardValuesArray);
  document.querySelector('#wizard').innerHTML = '';
  renderLoader(document.querySelector('#wizard'),'loader');
  
  $.ajax({
      
    url:"AJAX/StepFour.php",
    method:"POST",
    data:{value:wizardValuesArray},
    dataType:"json",
    success:function(data)
    {
      document.querySelector('#wizard').innerHTML='';
     //document.querySelector('#wizard').insertAdjacentHTML("afterbegin",data);

      wizardValuesArray['stepFour'] = data['sql'];
      document.querySelector('#wizard').insertAdjacentHTML("afterbegin",renderStepFour(data['size']));

      
    },
    error: function(data){
      document.querySelector('#wizard').innerHTML='';

     ////console.log(data);
      // spusti to zase od zacatku
      renderNavigation(document.querySelector('#wizard'),'Three','Five');
      //stepOneAJAX();
    }
  })
  
}


function collectDataFromStepThree(wizardValuesArray){
  let nodeArray = document.querySelectorAll('.StepThreeInput');
  let inputArray = [];
  let inputObject;
   for (let index = 0; index < nodeArray.length; index++) {
         const element = nodeArray[index];
           //select input
           if(element.checked == true){
             inputObject = {id: element.id,
                            name: element.name,
                            value: element.value};
 
                   inputArray.push(inputObject); 
                 }
          
             //date input    
           }
 
 
           wizardValuesArray['stepThree'] = inputArray;
         return wizardValuesArray;
     
   }


   function renderStepFour(size){
     if (size > 0) {
      output = 'Zadejte email pro zaslání emailu: <input type="email" id ="sendReport" value="'+userEmail+'" size="35"></input>'
      output += `<br><br>Bylo nalezeno: ${size} výsledků.<br>
                  Odhadovaný čas generování:  ${Math.ceil(size/1200)} min`;
      renderNavigation(document.querySelector('#wizard'),'Three','Five'); 

     } else {
       output = `<h2>Nebyl nalezen žádný výsledek, prosím zvolte jiná kritéria</h2>`;
       output += `<br><button onclick="StepOneToTwo('${wizardValuesArray['stepOne']}')">Zpět k filtrům</button>`;
    //  output += 'Zadejte email pro zaslání emailu: <input type="email" id ="sendReport" value="'+userEmail+'"></input>'
       
     }

     // output = 'Zadejte email pro zaslání emailu: <input type="email" id ="sendReport" value="'+userEmail+'"></input>'
    return output;
   }



   /*****
    * 
    * 
    * Step Five
    * 
    */


   function stepFiveAJAX()
   {
     wizardValuesArray = collectDataFromStepFour(wizardValuesArray);
    // //console.log(wizardValuesArray);
     document.querySelector('#wizard').innerHTML = '';
     renderLoader(document.querySelector('#wizard'),'loader');
     
     $.ajax({
         
       url:"AJAX/StepFive.php",
       method:"POST",
       data:{value:wizardValuesArray},
       dataType:"json",
      /* success:function(data)
       {
         document.querySelector('#wizard').innerHTML='';
        //document.querySelector('#wizard').insertAdjacentHTML("afterbegin",data);
         //console.log(data);
         //document.querySelector('#wizard').insertAdjacentHTML("afterbegin",renderStepFour());
        // wizardValuesArray['stepFour'] = data;
         //console.log(data);
         renderNavigation(document.querySelector('#wizard'),'Three','Five');
       },*/
       /*error: function(data){
         document.querySelector('#wizard').innerHTML='';
   
        //console.log(data);
         // spusti to zase od zacatku
         renderNavigation(document.querySelector('#wizard'),'Three','Five');
         //stepOneAJAX();
       }*/
     });
     document.querySelector('#wizard').innerHTML=`Děkujeme nyní můžete očekávat report na Vašem emailu: ${userEmail}`;
     
   }


   function collectDataFromStepFour(wizardValuesArray){

    wizardValuesArray['stepFive'] = document.querySelector('#sendReport').value;

    return wizardValuesArray;
   }