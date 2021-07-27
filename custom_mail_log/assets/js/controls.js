import listeners from './listeners';
import listener  from './listeners'
import view  from './views'

export default class control{

    static searchTextProcessor(value) {
        if(value == ''){
            this.showFresh()
        }else{
            this.showSearched(value)
        }
    }

    /**
     * renders list items when no searchstring is input
     */

    static showFresh(){
        $.ajax({
            url:"AJAX/AJAX.php",
            method:"POST",
            data:{getlist:true},
            dataType:"json",
            success:function(data)
            {
              //document.querySelector('#results').innerHTML=''
              window.querry = ''
              document.getElementById('results').innerHTML = ''
              view.renderList(data)
              listeners.scrollListener()
              //Course.renderBasicCategoriesForUser(data);
            //return data;

            },error:function (data){
                //console.log(data)
            }
          });
    }

    /**
     * renders next items when no searchString is input
     */

    static showFreshAppend(){
        const rowlist =  document.querySelectorAll('.table-row')
        const id = rowlist[rowlist.length-1].id
        //console.log(id)
        $.ajax({
            url:"AJAX/AJAX.php",
            method:"POST",
            data:{getlist:id},
            dataType:"json",
            success:function(data)
            {
              //document.querySelector('#results').innerHTML=''
              view.renderList(data)

              //Course.renderBasicCategoriesForUser(data);
            //return data;

            },error:function (data){
                //console.log(data)
            }
          });
    }

/**
 * do the custom query search
 * @param {*} value 
 */

    static showSearched(value){
        $.ajax({
            url:"AJAX/AJAX.php",
            method:"POST",
            data:{getbyquerry:value},
            dataType:"json",
            success:function(data)
            {
              //document.querySelector('#results').innerHTML=''
              
              //console.log(data)
              document.getElementById('results').innerHTML = ''
              window.querry = data.sql
              view.renderList(data.data,data.sql)
              //Course.renderBasicCategoriesForUser(data);
            //return data;

            },error:function (data){
                //console.log(data)
            }
          });
    }


        /**
     * renders next items when no searchString is input
     */

         static showSearchedAppend(){
            const rowlist =  document.querySelectorAll('.table-row')
            const id = rowlist[rowlist.length-1].id
            //console.log(id)
            const postItem =  {
                sql : window.querry,
                id : id
            }
            $.ajax({
                url:"AJAX/AJAX.php",
                method:"POST",
                data:{getbyquerryappend:postItem},
                dataType:"json",
                success:function(data)
                {
                    //console.log(data)
                  //document.querySelector('#results').innerHTML=''
                  view.renderList(data.data)
    
                  //Course.renderBasicCategoriesForUser(data);
                //return data;
    
                },error:function (data){
                    //console.log(data)
                }
              });
        }


    static getMoreInfo(value){
        $.ajax({
            url:"AJAX/AJAX.php",
            method:"POST",
            data:{getinfo:value},
            dataType:"json",
            success:function(data)
            {
                //document.querySelector('#results').innerHTML=''
                view.renderGetMoreInfo(data)
                //console.log(data)
                //Course.renderBasicCategoriesForUser(data);
                //return data;

            },error:function (data){
                //console.log(data)
            }
          });
    }
    
}


