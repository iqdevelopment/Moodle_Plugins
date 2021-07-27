/*****
 * 
 * funkce, ktera zajisti vygenerovani dochazky
 * 
 ******/

function startAttendanceGeneration(){
    const queryString = window.location.search;
    const urlParams = new URLSearchParams(queryString);
    const courseId = urlParams.get('id');
    const bbbId = urlParams.get('bbbid');
    generationAjax(courseId,bbbId);

}



  function generationAjax(courseId,bbbId)
  {
    
    document.querySelector('.results').innerHTML = '';
    renderLoader(document.querySelector('.results'),'loader-courses');
    let inputdata = `${courseId}-${bbbId}`;
    $.ajax({
      url:"./AJAX/dochazka_process_bbb.php",
      method:"POST",
      data:{data:inputdata},
      dataType:"json",
      success:function(data)
      {
        document.querySelector('.results').innerHTML=''
        document.querySelector('.results').insertAdjacentHTML("afterbegin",data);
        
      },error:function (data){
        document.querySelector('.results').innerHTML=''
        document.querySelector('.results').insertAdjacentHTML("afterbegin",'error');
        console.log(data);
      }
    })
   
  }

  const renderLoader = (parent,loaderClass) => {
    const loader = `
    <div class='${loaderClass}'>
        
    </div>
    `;
    parent.insertAdjacentHTML('afterbegin',loader);
    };
  
  
   const clearLoader = parrent => {
    document.querySelector(parrent).innerHTML=''
  
  }


  function selectAll(source) {
		checkboxes = document.getElementsByName('selectedusers[]');
		for(var i in checkboxes)
			checkboxes[i].checked = source.checked;
	}


  //document.querySelector('#percentageinput').value
  function showWhoPasses(){
    let hours = document.querySelector('#hoursInput').value;

    
    
    hours = hours.replace(',','.');
    if (hours == ''){hours = 70;}
      let x = document.getElementsByClassName("student");
      let y;
      for (i = 0; i < x.length; i++) {
        let value,min,hour,realtime;
       value = x[i].querySelector('#realtime').innerHTML;
       realtime = value.split('%')[0];
             
          if(realtime < hours){
            x[i].style.backgroundColor = "#ff9999";
            x[i].querySelector('.checkbox').checked = false;   
          }else{

            x[i].style.backgroundColor = "#9ae59a";
            x[i].querySelector('.checkbox').checked = true;  
          }



        //x[i].style.backgroundColor = "red";
      }
  }


  function exportTableToCSV(filename) {
    var csv = [];
    let table = document.querySelector('#exportable');
    changeCheckBoxToText(table);
    var rows = table.querySelectorAll("table tr");  
    for (var i = 0; i < rows.length; i++) {
        var row = [], cols = rows[i].querySelectorAll("td, th");
        
        for (var j = 0; j < cols.length; j++) 
            row.push(cols[j].innerText);                       
        
        csv.push(row.join(";"));        
    }
    console.log(csv);
    // Download CSV file
    downloadCSV(csv.join("\n"), filename);
}


  function downloadCSV(csv, filename) {
    var csvFile;
    var downloadLink;

    // CSV file
    // kodovani
    var BOM = "\uFEFF";
    var    csv = BOM + csv;
    csvFile = new Blob([csv], {type: "text/csv;charset=utf-8"});
    // Download link
    downloadLink = document.createElement("a");
    // File name
    downloadLink.download = filename;
    // Create a link to the file
    downloadLink.href = window.URL.createObjectURL(csvFile);
    // Hide download link
    downloadLink.style.display = "none";
    // Add the link to DOM
    document.body.appendChild(downloadLink);
    // Click download link
    downloadLink.click();
}


/* function exportTableToPDF(pdf) {
  pdf= 0;
  var kvi;
  var prtContent = document.querySelector('.results');

  var WinPrint = window.open();
  
  WinPrint.document.write(prtContent.innerHTML);
  
  WinPrint.document.close();
  WinPrint.focus();
  WinPrint.print();
  WinPrint.document.close();

} */

function exportTableToPDF(pdf) {
  pdf= 0;
  var prtContent = document.querySelector('.results');
    checkBoxToText();
    deleteBeforePrint();
    prtContent.id = 'results';
    printJS('results','html');

    startAttendanceGeneration();
  
  /* var WinPrint = window.open();
  
  WinPrint.document.write(prtContent.innerHTML);
  
  WinPrint.document.close();
  WinPrint.focus();
  WinPrint.print();
  WinPrint.document.close(); */

}

function deleteBeforePrint(){
  document.getElementById("lector-sign").innerHTML = "Podpis lektora: ";
  for (const dropCategories of document.querySelectorAll(`.to-hide`)){
    dropCategories.remove();
  }
}

function checkBoxToText(){
  document.getElementById('selectall').remove();
    for (const dropCategories of document.querySelectorAll(`.check`)){
        let checked = dropCategories.childNodes[0].checked;
        console.log(checked);
        if(checked){
          dropCategories.innerHTML = "splněno";
        }else{
          dropCategories.innerHTML = "nesplněno";
        }

    }
}


function changeCheckBoxToText(table){
  let rows = table.querySelectorAll('.check');
 
    for (var i = 0; i < rows.length; i++) {
      //console.log(rows[i].querySelector('.checkbox').checked);
        if(rows[i].querySelector('.checkbox').checked === false){
          rows[i].innerHTML += 'nesplněno';
          rows[i].querySelector('.checkbox').checked = false;
          
        }else if(rows[i].querySelector('.checkbox').checked === true){
          rows[i].innerHTML += 'splněno';
          rows[i].querySelector('.checkbox').checked = true;
        }

    }
    document.querySelector('.check-header').parentElement.innerHTML += 'splněno';  

}

function AttendanceAlterAjax(courseId,bbbId)
{
  document.querySelector('.results').innerHTML = '';
  renderLoader(document.querySelector('.results'),'loader-courses');
  let inputdata = `${courseId}-${bbbId}`;
  $.ajax({
    url:"./AJAX/dochazka_alter_bbb.php",
    method:"POST",
    data:{data:inputdata},
    dataType:"json",
    success:function(data)
    {
      document.querySelector('.results').innerHTML=''
      document.querySelector('.results').insertAdjacentHTML("afterbegin",data);
      if(data == 'reload'){location.reload();}
      
    },error:function (data){
      document.querySelector('.results').innerHTML=''
      document.querySelector('.results').insertAdjacentHTML("afterbegin",'error');
      console.log(data);
    }
  })
 
}


function zeroesAreNumbers(){
  let nodeArray = document.querySelectorAll('input[type="number"]');
  nodeArray.forEach(element => {
    if (element.value !== '' && element.value == 0) {
     element.value = '00';
    }
    
  });

}