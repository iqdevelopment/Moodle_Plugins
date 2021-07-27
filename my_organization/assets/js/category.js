import User from './users.js'
import Course from './courses.js'
import * as listener from './listeners.js'
import * as general from './output.js'
import AfterRenders from './afterRenders.js';
import * as feedback from './feedback.js'

export default class Category{
   constructor(categoryid){
       this.categoryid = categoryid;
   } 

   /***
    * 
    * at category list this render addional buttons after button with name
    * 
    */


   static newCategoryButton(id){
       let output = `<div class="tooltipp"><i category="${id}" class="add-category fa fa-plus-circle fa-lg"></i>
       <span class="tooltipptext">Vytvořit podkategorii</span>
        </div>`;
        output += `<div class="tooltipp"><i category="${id}" class="delete-category fa fa-trash fa-lg"></i>
        <span class="tooltipptext">Smazat podkategorii</span>
         </div>`
         output += `<div class="tooltipp"><i category="${id}" class="edit-category fa fa-edit fa-lg"></i>
         <span class="tooltipptext">Upravit kategorii</span>
          </div>`
        return output;
   }


/***
 * 
 * renders reaches for data, by givven category ID, and uses the renderClickableTree to render fulltree
 * 
 */


   categoryInfo() {
      var self = this;
      const idUser = general.renderFullLoader('#results-users');
      const idCourse = general.renderFullLoader('#results-courses');
       $.ajax({
        url:"AJAX/AJAX.php",
        method:"POST",
        data:{categoryInfo:this.categoryid},
        dataType:"json",
        success:function(data)
        {
          document.querySelector('#results-categories').innerHTML=''
          document.querySelector('#results-categories').insertAdjacentHTML("afterbegin",self.renderClickableTree(data));
          general.dragOverUsersToCategories('category-drag-drop');
          AfterRenders.categoriesShowCategoryTreeButton();
          if(idUser){
            general.cancelLoader(idUser); 
          }
          if(idCourse){
            general.cancelLoader(idCourse); 
          }

          
        },error:function (data){
          document.querySelector('#results-categories').innerHTML=''
          document.querySelector('#results-categories').insertAdjacentHTML("afterbegin",'error');
          
        }
      })
       
   }


    renderClickableTree(object){
        
        let self = this;
        let output = `<div class="category-container">
                    <button id="category-${object.id}" category="${object.id}" class="category-drag-drop category-main-categories" >${object.name}</button>`;
            output += Category.newCategoryButton(object.id);
    
        if (object.fullTree) {
            output +=  self.renderTreeBranch(object.fullTree);
        }
        output += `</div>`;
       // return output;
       return output;
    }


    renderTreeBranch(object){
        let self = this;
        let output = `<div class="subtree"><ul class="subtree-list">`;
        //let categories = object.fullTree;
        for (const index in object){
            const element = object[index];
            output += self.renderTreeItems(element);
            
        }
        output += `</ul></div>`;  
        return output;

    }


    renderTreeItems(object){
        var self = this;
        let output = ` <li class="subtree-item"><button id="category-${object.id}" category="${object.id}"  class="category-drag-drop category-main-categories" >${object.name}</button>`;
        output += Category.newCategoryButton(object.id);
        output += `</li>`;
        if(object.children){
            output += self.renderTreeBranch(object.children);
            
        }
        return output;
    }


    /****
     * 
     * methods for creating new category
     * 
     * 
    */

    //firt is the form

    static renderNewCategoryForm(parentid,message = null){
        let output = `<div class="create-category-form" id="create-category-form"><button class="close-btn"><i class="fa fa-close"></i></button>`;
        if(message){
            output += `<span class="is-invalid">${message}</span><br>`;
        }

        output += `<label>Název kategorie</label><input id="new-category-name" type="text" placeholder="Přívětivý název"></input>
                <br>
                <label>identifikátor kategorie</label><input type="text" id="new-category-idname" placeholder="nazev_bez_mezer"></input>
                <br>
                <button class="create-category" parentid="${parentid}">Potvrdit</button>
                </div>`
        //return output;
        document.querySelector('#results-categories').insertAdjacentHTML("afterbegin",output);
            if(message){
                general.errorblink('create-category-form');
            }

            AfterRenders.closeWindow();
            AfterRenders.createCategory();
    }




    static createCategory(parentid){
        let nameInput = document.getElementById('new-category-name').value;
        let idInput = document.getElementById('new-category-idname').value;
        let exportString = `${nameInput}&${idInput}&${parentid}`
        $.ajax({
            url:"AJAX/AJAX.php",
            method:"POST",
            data:{createCategory:exportString},
            dataType:"json",
            success:function(data)
            {
                //document.querySelector('#results-categories').innerHTML=''
                
                if(data.type == 'positive'){
                        document.querySelector('.create-category-form').remove();
                        let showTreeId = document.querySelector('.category-container').childNodes;
                        showTreeId = showTreeId[1].getAttribute('category');
                        general.closeForm();
                        general.showTree(showTreeId);
                }else{
                        let id = document.querySelector('.category-container').children[0].id.replace('category-','');
                        general.closeForm();
                        let showTreeId = document.querySelector('.category-container').childNodes;
                        showTreeId = showTreeId[1].getAttribute('category');
                        Category.renderNewCategoryForm(showTreeId,data.data);
                        //general.showTree(id);
                }
                    feedback.feedBack(data);
                
              
            },error:function (data){
              document.querySelector('#results-categories').innerHTML=''
              document.querySelector('#results-categories').insertAdjacentHTML("afterbegin",'error');
              
            }
          })

    }

    static createCategoryIdname(){
        let nameInput = document.getElementById('new-category-name').value;
        let newNameInput = nameInput.normalize('NFD').replace(/([\u0300-\u036f]|[^0-9a-zA-Z])/g, '');
        document.getElementById('new-category-idname').value = newNameInput;

    }

    static renderDeleteCategoryForm(id,message = null){
        //document.getElementById('results-categories').remove();
        let output = `<div class="delete-category-form" id="create-category-form"><button  class="close-btn"><i class="fa fa-close"></i></button>`;
        if(message){
            output += `<span class="is-invalid">${message}</span><br>`;
        }

        output += `<label>Chystáte se smazat tuto kategorii, co se má stát s podkategoriemi a kurzy?</label>
                <select category="${id}" id = "delete-category-select">
                <option value="delete">Vše smazat</option>
                <option value="move">Přesunout</option>
                </select>
                <div id="delete-move-select"></div>
                <button category="${id}" class="delete-category">Potvrdit</button>
                </div>`
        //return output;
        document.getElementById('results-categories').insertAdjacentHTML("afterbegin",output);

            if(message){
                general.errorblink('create-category-form');
            }

        AfterRenders.closeWindow();
        AfterRenders.deleteCategory();
    }


    renderUpdateCategoryForm(id,roleid = 0) {
        
        this.roleid = roleid;
        self = this;
        
        //remove previous window of user or course
        $.ajax({
            url:"AJAX/AJAX.php",
            method:"POST",
            data:{updateCategory:id},
            dataType:"json",
            success:function(data)
            {
                
              self.renderCategoryInfoWindow(data,this.roleid);
             
    
            },error:function (data){
                
              }
          });
    }







    /**
     * 
     * helpfull method to create list of all categories user can access and renders is into an parent element
     */


    static renderSelectOfCategories(noShow = -1,elementid){
        $.ajax({
            url:"AJAX/AJAX.php",
            method:"POST",
            data:{getMyCategories:noShow},
            dataType:"json",
            success:function(data)
            {
                //document.querySelector('#results-categories').innerHTML=''
                let output = `<select id="category-select">`;

                let outputArray = [];    
                //from main contexts
                for (const [key, value] of Object.entries(data.contextArray)) {
                    let arrayItem = {"name" : value.name , "categoryid" : value.categoryid};
                    outputArray.push(arrayItem);  
                }
                //from all child contexts
                for (let index = 0; index < data.childContexts.length; index++) {
                    const element = data.childContexts[index];
                    //output += `<option value="${element.categoryid}">${element.name}</option>`;
                    let arrayItem = {"name" : element.name , "categoryid" : element.categoryid};
                    outputArray.push(arrayItem);
                }
    
                //sort the array alpha
                outputArray.sort();
                for (let index = 0; index < outputArray.length; index++) {
                    const element = outputArray[index];
                    output += `<option value="${element.categoryid}">${element.name}</option>`;
                }
  
                output += `</select>`;
                
                document.getElementById(elementid).insertAdjacentHTML("afterbegin",output);
                //return output;
            
                
              
            },error:function(data){
                //error
                
            }
          })
    }


    /***
     * 
     * definitive delete or move of category
     */

    deleteCategory(){
       
        var self = this;

        let action = document.getElementById('delete-category-select').value;
        let requestString;
        if(action == 'move'){
            let moveTo = document.getElementById('category-select').value;
            requestString=`${self.categoryid}&${action}&${moveTo}` ;

        }else{ 
            requestString=`${self.categoryid}&${action}`;
        }
        
        const divId = general.renderFullLoader('#results');
        $.ajax({
            url:"AJAX/AJAX.php",
            method:"POST",
            data:{deleteCategory:requestString},
            dataType:"json",
            success:function(data)
            {
                
                
                if(data.data.search('!') > 1){
                    
                    data.type = 'negative';
                    feedback.feedBack(data,true);
                    general.closeForm();
                }else{
                    feedback.feedBack(data);
                    general.closeForm();
                }
            general.rerenderCategoryList();  
            if(divId != null){
                general.cancelLoader(divId); 
              }  
            },error:function(data){
               /*  document.getElementById('results-categories').insertAdjacentHTML("afterbegin",data.responseText);
                
                if(data.responseText.search('deletete_default') > 1){
                    
                } */
                
                if(data.responseText.search('!') > 1){
                    
                    data.type = 'negative';
                    data.data = data.responseText; 
                    feedback.feedBack(data,true);
                    general.closeForm();
                }else{
                    data.type = 'neutral';
                    data.data = data.responseText;
                    let array = data.data.split('{');
                    let test = `{${array[1]}`;
                    let testJSON = {
                        data : JSON.parse(test).data,
                        type : 'positive'
                    };
                    
                    data.data = data.data.replace(test,'');
                   
                   
                    feedback.feedBack(data,false,6);
                    feedback.feedBack(testJSON,false,6);
                    general.closeForm();
                } 
                //render the list new  
                general.rerenderCategoryList();
                if(divId != null){
                    general.cancelLoader(divId); 
                  }
            }
          });
          AfterRenders.closeWindow();
          


          /* let id = document.querySelector('.category-container').children[0].id.replace('category-','');
              showTree(id); */
    }





    /*****
     * 
     * renders carousel where you can drag and drop new users and asign them the role
     * 
     */

     renderCategoryInfoWindow(){
        let roleidPassed = this.roleid
         
         self = this;
         //closeInfoWindow();
         $.ajax({
            url:"AJAX/AJAX.php",
            method:"POST",
            data:{updateCategory:self.categoryid},
            dataType:"json",
            success:function(data)
            {
                //header
                let output = `<div class="info-window info-window-background-category"><div class="close-btn-info-window" onclick="closeInfoWindow()"><i class="fa fa-close"></i></div><div class="header-info">
                <h1 clas=''>${data.name}</h1>
                <a href="${HOMEURL}/course/management.php?categoryid=${data.id}">Nastavení kategorie</a><br>
                <a href="${HOMEURL}/admin/tool/my_organization_institut_uploaduser/index.php?id=${data.id}">Importovat do kategorie</a><br>
                </div>`;
               
                //carousel itself
                output += self.renderCarouselSwitches(data,roleidPassed);
                output += self.renderCarouselLists(data,roleidPassed);

                //footer and rendering into document
                output += `</div>`;
                general.closeInfoWindow();
                document.getElementById('results-users').insertAdjacentHTML('afterend',output);  
                //setTimeout(() => {  Category.dragOverCategory('roles-carousel'); }, 950);
                //setTimeout(() => {  self.dragDropCategory('category-role-user-list'); }, 950); 
                AfterRenders.closeWindow();
                AfterRenders.categoryInfoPanelListeners();
                
              
            },error:function(data){
               
            }
          });
     }


     /****
      * 
      * handles data from renderCategoryRolesCarousel AJAX call and renders the carousel itself
      * 
      */


    renderCarouselLists(data,roleid){
        self = this;
        
        let output = `<div class="roles-carousel-container">`;
        for (const [key, valueData] of Object.entries(data.roles)) {
            if(valueData.roleid == roleid){
                output += `<div id="roles-carousel-${valueData.roleid}" category="${data.id}" class="roles-carousel"> <input roleid="${valueData.roleid}" class="search-carousel" placeholder="Jméno, příjmení nebo email" type="search"> <ul class="category-role-user-list">`;
            }else{   
                output += `<div id="roles-carousel-${valueData.roleid}" category="${data.id}" class="roles-carousel hidden"> <input roleid="${valueData.roleid}" class="search-carousel" placeholder="Jméno, příjmení nebo email" type="search"> <ul class="category-role-user-list">`;   
            }
                if(Object.keys(valueData.users).length > 0){
                    for (const [key, value] of Object.entries(valueData.users)) {

                        output += `<li class="drag-category-role-user" draggable="true" userid="${value.id}" >${value.lastname} ${value.firstname}<br> ${value.email}</li>`;
                    }
                }


            output += `</ul> </div>`;
        }
        output += `</div>`;
    
    
        return output;


    }


    renderCarouselSwitches(data,roleid){
        self = this;
        let output = `<div class="roles-carousel-switcher-container" category="${data.id}">`;
        for (const [key, valueData] of Object.entries(data.roles)) {
            
            if(valueData.roleid == roleid){
                output += `<button class="carousel-btn active" roleid="${valueData.roleid}" id="carousel-btn-${valueData.roleid}" >${valueData.name}</button>`;
            }else{   
                output += `<button class="carousel-btn" roleid="${valueData.roleid}" id="carousel-btn-${valueData.roleid}">${valueData.name}</button>`;
            }
            
        }
        output += `</div>`;
    
    
        return output;


    }

    /***
     * 
     * switch visible list of users
     * 
     */


    static caurouselSwitch(roleid){

        for (const categories of document.querySelectorAll(`.roles-carousel`)){
            categories.classList.add('hidden');            
        }

        for (const categories of document.querySelectorAll(`.carousel-btn`)){
            categories.classList.remove('active');            
        }

        document.getElementById(`roles-carousel-${roleid}`).classList.remove('hidden');
        document.getElementById(`carousel-btn-${roleid}`).classList.add('active');

    }

    
    static searchULCarousel(categoryid){
        let searchText =  event.srcElement.value;
        let parent = document.getElementById(`roles-carousel-${categoryid}`);
            let ul = parent.children[1];
            let fields = ul.children;
        
            if(searchText){
            
            for (let index = 0; index < fields.length; index++) {
                const element = fields[index];
                 if(element.innerHTML.replace('<br>',' ').toLowerCase().search(searchText.toLowerCase()) > -1){
                  element.classList.remove('hidden');
                }else{
                   element.classList.add('hidden'); 
                }           
            }
        }else{
        
            for (let index = 0; index < fields.length; index++) {
                const element = fields[index]; 
                element.classList.remove('hidden');
            }

        } 
      }




      /***
       * 
       * function to do the event listeners for carousel
       * 
       */

    static dragOverCategory(cssClass) {

        for (const dropCategories of document.querySelectorAll(`.${cssClass}`)){
                dropCategories.addEventListener("dragover", listener.dragOverHandler);
        }

        //change style and allow dropping
        for (const dropCategories of document.querySelectorAll(`.${cssClass}`)){
                dropCategories.addEventListener("dragleave", listener.dragLeaveHandler);
        }
    
        //dropy
        for (const dropCategories of document.querySelectorAll(`.${cssClass}`)){
                dropCategories.addEventListener("drop", listener.dragDropHandler);
        }
    }

    

    static DropHandlerUserPanelToCategoryButton(cssClass) {

        for (const dropCategories of document.querySelectorAll(`.${cssClass}`)){
                dropCategories.addEventListener("dragover", listener.dragOverHandler);
        }

        //change style and allow dropping
        for (const dropCategories of document.querySelectorAll(`.${cssClass}`)){
                dropCategories.addEventListener("dragleave", listener.dragLeaveHandler);
        }
    
        //dropy
        for (const dropCategories of document.querySelectorAll(`.${cssClass}`)){
                dropCategories.addEventListener("drop", listener.DropHandlerUserPanelToCategoryButton);
        }
    }






    static dragOverCategoryNoParrents(cssClass) {

        for (const dropCategories of document.querySelectorAll(`.${cssClass}`)){
                dropCategories.addEventListener("dragover", listener.dragOverHandlerNP);
        }

        //change style and allow dropping
        for (const dropCategories of document.querySelectorAll(`.${cssClass}`)){
                dropCategories.addEventListener("dragleave", listener.dragLeaveHandlerNP);
        }
    
        //dropy
        for (const dropCategories of document.querySelectorAll(`.${cssClass}`)){
                dropCategories.addEventListener("drop", listener.dragDropHandlerNP);
        }
    }

        /***
         * 
         * removes listeners
         * 
         */

    static dragOverCategoryEnd(cssClass) {
        for (const dropCategories of document.querySelectorAll(`.${cssClass}`)){
                dropCategories.removeEventListener("dragover", listener.dragOverHandler);
        }

        //change style and allow dropping
        for (const dropCategories of document.querySelectorAll(`.${cssClass}`)){
                dropCategories.removeEventListener("dragleave", listener.dragLeaveHandler);
        }

        //dropy
        for (const dropCategories of document.querySelectorAll(`.${cssClass}`)){
                dropCategories.removeEventListener("drop", listener.dragDropHandler);
        }
       
    }


    static renderBinToDrop(){
        let output = `<div class="drop-trash"><i class="fa fa-trash fa-10x"></i></div>`;
        document.getElementById('results-users').insertAdjacentHTML('afterend',output); 
        let element = document.querySelector('.drop-trash');
        //events
        element.addEventListener("drop", listener.dragDropHandlerCategory);
        element.addEventListener("dragleave", listener.dragLeaveHandlerNP);
        element.addEventListener("dragover", listener.dragOverHandlerNP);
        
    }




    /**
     * 
     * function to render table with infoes 
     */

    static renderBasicCategoriesForUser(data,divId = null){
        let numberOfCategories = Object.keys(data).length;
        let output = `<div class="category-container">`;
        if(numberOfCategories > 0){
            output += `<table class="category-overview"><tr>
                <th class="table-name">Kategorie</th><th class="table-role">Role</th><th class="table-courses">Kurzy</th>
            </tr>`;
            for (const [key, value] of Object.entries(data)) {
                output += `
                <tr>
                    <td class="table-name"><button id="category-${value.categoryid}" class="category-main-categories" category="${value.categoryid}">${value.name}</button></td>
                    <td class="table-role">${value.rolename}</td>
                    <td class="table-courses">${value.coursesAll}</td>
                </tr>`;
                    
                
            }
            output += `</table>`;
        }else{
            output += `Nyní nemáte žádnou kategorii, kterou můžete spravovat, požádejte administrátora, pokud jste administrátor, použijte tlačítko "Jsem administrátor"`; 
        }
        output += `</div>`;
        document.getElementById('results-categories').innerHTML = output;

    }




}