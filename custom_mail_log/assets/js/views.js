import listener  from './listeners'
import control  from './controls'

export default class view{

    static renderList(objectArray,sql = null) {
        this.renderLoader()
        ////console.log(objectArray)
        const check = document.querySelector('.render-table')
        if(!check){
            this.renderTable()
        }
        const table = document.querySelector('.render-table')



        if(view.denyTooMuchRender(objectArray)){
            for (const [key, value] of Object.entries(objectArray)) {
                let newRow = table.insertRow(-1);
                newRow.setAttribute("class", "table-row");
                newRow.setAttribute("id", value.id);
                let newCell0 = newRow.insertCell(0);  
                let newText0 = document.createTextNode(value.timestamp)
                newCell0.appendChild(newText0);  
                
                let newCell1 = newRow.insertCell(1) 
                let newText1 = document.createTextNode(value.recipient)
                newCell1.appendChild(newText1);

                let newCell2 = newRow.insertCell(2) 
                let newText2 = document.createTextNode(value.status)
                newCell2.appendChild(newText2);

                let newCell3 = newRow.insertCell(3) 
                let newText3 = document.createTextNode('')
                newCell3.appendChild(newText3)
                newCell3.innerHTML = '<span class="more-info"><i class="fa fa-info-circle"></i></span>'
                newCell3.children[0].addEventListener('click',listener.moreInfo) 
            }
        }

        this.cancelLoader()
        document.addEventListener('scroll',listener.scrollAction)
    
    }


    static renderTable(){
        let output = `<table class="render-table">
        
            <tr>
                <th>Datum</th>
                <th>Adresát</th>
                <th>Stav</th>
                <th>Info</th>
            </tr>
           
                    </table>`
        document.querySelector('#results').insertAdjacentHTML('afterbegin',output)
    }


    /**
     * constols if searchbutton is clicked, or input field is entered by enter key
     * @param {*} ev 
     */
    static searchText(ev){
        if(ev.key == 'Enter' || ev.type == 'click'){
            const searchText = document.getElementById('search-field').value
            //console.log(searchText)
        }
        
    }

/**
 * prevent rendering duplicities
 * @param {*} objectArray 
 */
    static denyTooMuchRender(objectArray){
        
        const rowlist =  document.querySelectorAll('.table-row')
      // //console.log(rowlist)
        if(rowlist.length == 0){
            return true
        }
        const id = rowlist[rowlist.length-1].id

        for (const [key, value] of Object.entries(objectArray)) {
           if(value.id == id){
               //console.log('found id stop this madness')
               return false
           }
        }
        return true

    }


    /**
     * renders loader
     */
    static renderLoader(){

        parent = document.querySelector(`#results`);
       ////console.log('called loader')
        
        const loader = `<div class="loader-space">
                    <div class='loader'>
                    </div>
                </div>
        `;
 
        parent.insertAdjacentHTML('afterend',loader);

        
        

    }

     /**
     * cancels loader
     */
    static cancelLoader(){
      //  //console.log('called cancel loader')
        const element = document.querySelector(`.loader-space`);
            if (element){
                element.remove();
            }
    }


    /**
     * renders more info window
     * @param {*} data 
     */
    static renderGetMoreInfo(data){
        let detail = data.detail
        if(detail == null){ detail = '-'}
        const output = `<tr><td colspan="4" class='more-info-line'><div class="more-info-window">
                    <ul>
                        <li>id zprávy: ${data.messageid}</li>
                        <li>server: ${data.relay}</li>
                        <li>detail: ${detail}</li>
                    <ul>
                
                </div></td></tr>`
        
        document.getElementById(data.id).insertAdjacentHTML('afterend',output)

    }
    


    
        
       
}