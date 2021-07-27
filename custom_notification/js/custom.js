
function lektor_testmail(action) {
    var form = document.getElementById('form1');
    form.action = action;
    form.submit();
  }


if(document.getElementById('help')){
  document.getElementById('help').addEventListener('click',displayHelp);
}


function displayHelp(){
      div = document.getElementById('helpdiv');
      if (div.style.display == 'none'){
    div = document.getElementById('helpdiv').style.display = 'table';
    }else{
    div = document.getElementById('helpdiv').style.display = 'none';
    }
}

function sentOneTimeNotification(courseid){
  let subject = document.querySelector('#subject').value;
  let text = document.querySelector('#text').value;
  inputdata = `courseid=${courseid}&subject=${subject}&text=${text}`;
    $.ajax({
      url:"./AJAX/notification_process_sendmail.php",
      method:"POST",
      data:{data:inputdata},
      dataType:"json"
      /*,
      success:function(data)
      { console.log(data);
        //document.querySelector('.results').innerHTML=''
      //  document.querySelector('.results').insertAdjacentHTML("afterbegin",data);
        
      },error:function (data){
       // document.querySelector('.results').innerHTML=''
       // document.querySelector('.results').insertAdjacentHTML("afterbegin",'error');
        console.log(data);
      }*/
    })
    alert('Nyní začalo odesílání zpráv.');

}

var alertCount = 0;
function resetAllWarninig(text) {
  if(alertCount > 0){  
  }else{
    alert(text);  
    alertCount++;
  }
}
