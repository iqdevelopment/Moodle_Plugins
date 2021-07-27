
    /*
     * 
     * positive feedback
     * 
     */

    function positiveFeedback(message,persist = false,customtime = 2) {
        let tempID =  "ID" + Math.random().toString(36).substring(2, 15) + Math.random().toString(36).substring(2, 15);
        const output = `<div id="${tempID}" class="alert alert-success" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <strong>${message}</strong>
      </div>`;
        document.querySelector('.alerts-div').insertAdjacentHTML('afterbegin',output);
        fadeRemove(tempID,persist);
    }

     /*
     * 
     * negative feedback
     * 
     */


    function negativeFeedback(message,persist = false,customtime = 2) {
        let tempID =  "ID" + Math.random().toString(36).substring(2, 15) + Math.random().toString(36).substring(2, 15);
        const output = `<div id="${tempID}" class="alert alert-danger" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <strong>${message}</strong>
      </div>`;
        document.querySelector('.alerts-div').insertAdjacentHTML('afterbegin',output);
        fadeRemove(tempID,persist);
    }

     /*
     * 
     * neutral
     * 
     */


    function neutralFeedback(message,persist = false,customtime = 2) {
        let tempID =  "ID" + Math.random().toString(36).substring(2, 15) + Math.random().toString(36).substring(2, 15);
        const output = `<div id="${tempID}" class="alert alert-warning" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <strong>${message}</strong>
      </div>`;
        document.querySelector('.alerts-div').insertAdjacentHTML('afterbegin',output);
        fadeRemove(tempID,persist);
    }



    /* 
     * gives feedback the fade effect 
     */

    function fadeRemove(tempID,persist = fals, customtime = 2) {
        if(persist){

        }else{
            window.setTimeout(function() {
                $(`#${tempID}`).fadeTo(500, 0).slideUp(500, function(){
                    $(this).remove(); 
                });
            }, customtime*1000);
         }
    }

    /***
     * 
     * this function controls type of feedback
     * 
     */

    export function feedBack(obj,persist = false,customtime = 2) {
        let message = obj.data;
        let type = obj.type;

        if(type== 'positive'){
            positiveFeedback(message,persist,customtime);
        }else if(type == 'neutral'){
            neutralFeedback(message,persist,customtime);
        }else{
            negativeFeedback(message,persist,customtime);
        }

        
        
    }



