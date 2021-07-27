import Category from './category.js'
import Course from './courses.js'
import * as listener from './listeners.js'
import * as general from './output.js'
import AfterRenders from './afterRenders.js'
import * as feedback from './feedback.js'

export default class User{

    contextArray;
    constructor(userid){
        this.userid = userid;
    } 

    /*****
     * 
     * for rendering main categories for users
     * 
     */
    getUserRights(userid = -1){
        const idCategory = general.renderFullLoader('#results-categories');
        self = this;//let returnObject;
        $.ajax({
            url:"AJAX/AJAX.php",
            method:"POST",
            data:{getUserRights:userid},
            dataType:"json",
            success:function(data)
            {  

                
              
              Category.renderBasicCategoriesForUser(data,idCategory);

              AfterRenders.categoriesShowCategoryTreeButton();
            
            },error:function (data){
                
              }
          });
          
    }


    getAdminRights(){
        const idUser = general.renderFullLoader('#results-users');
        const idCategory = general.renderFullLoader('#results-categories');
        const idCourse = general.renderFullLoader('#results-courses');
        $.ajax({
            url:"AJAX/AJAX.php",
            method:"POST",
            data:{getAdminRights:true},
            dataType:"json",
            success:function(data)
            {  
                
              //document.querySelector('#results').innerHTML=''
              if(data){
                    Category.renderBasicCategoriesForUser(data);
                   // general.cancelLoader(idCategory); 
                    if(document.getElementsByClassName('category-container')){
                        Category.DropHandlerUserPanelToCategoryButton('category-container');
                    }
                    let user = new User(0);
                    user.renderListOfUsers(0,false,true,idUser);
                   
                    Course.renderListOfCourses(0,false,true,idCourse);
                   
                   AfterRenders.categoriesShowCategoryTreeButton();
                }
            
            },error:function (data){
                
              }
          });
          
    }



    /***
     * AJAX call to get all the user data and renders a list
     * 
     */

    renderListOfUsers(categoryid,filtered = false,admin = false,divId = null){
        self = this;
        if(filtered == true){
            var postString = `${categoryid}&1`;  
        }else{
            var postString = categoryid;
        }
        if(admin == true){
            postString += `%1`;
        }
        
        $.ajax({
            url:"AJAX/AJAX.php",
            method:"POST",
            data:{getUsersList:postString},
            dataType:"json",
            success:function(data)
            {
               

              self.renderUserListHead(categoryid,admin);
              for (const [key, value] of Object.entries(data.childUsers)) {  
                self.renderListOfUsersItem(value);
              }
              self.renderUserListEnd();
              AfterRenders.userPanelListeners();
              if(divId != null){
                general.cancelLoader(divId); 
              }
              //self.renderBasicCategoriesForUser(data);
            //return data;

            },error:function (data){
                
              }
          });
    }

    /***
     * 
     * render one item of the list
     */

    renderListOfUsersItem(object){
        let output = `
        <li userid="${object.id}"
         class="user-list-li" id="user-item-li-${object.id}" 
         class="user-list-li-text" id="user-list-text-${object.id}" 
         draggable="true" 
         
         >
        <input userid="${object.id}" type="checkbox" class="checkbox-user-li" id="user-item-${object.id}" value="${object.id}"></input>
        ${object.lastname} ${object.firstname} <br>
        ${object.email}
        <div class="user-more-info" userid="${object.id}" ><i class="fa fa-info-circle fa-lg"></i></div></li>`;
        
        document.getElementById('user-list-ul').insertAdjacentHTML('beforeend',output);
    }

    /****
     * 
     * render searchbox for users
     * 
     */

    renderUserListHead(categoryid = null,admin = false){

       let headerExists = document.getElementById('search-ul-input');
       if(headerExists){
            var checkme_user = document.getElementById('user-count-control').checked;
            document.getElementById('results-users').innerHTML = '';
       }
       var output = `<input type="search" id="search-ul-input" placeholder="Jméno, příjmení, email"></input>
       <input category="${categoryid}" type="checkbox" id="user-count-control"`
       if(categoryid != null && admin == false){
        output += `></input> Jen z kategorie`;
       }else{
        output += ` class="hidden">`;
       }
       output += `<br>`;
        output +=`<ul class="user-list-ul" id="user-list-ul">`;
        document.getElementById('results-users').insertAdjacentHTML('beforeend',output);
        document.getElementById('user-count-control').checked = checkme_user;
    }

        /****
         * 
         * renders end of a user list
         * 
         */

    renderUserListEnd(){
        let output = `</ul>`;
        document.getElementById('results-users').insertAdjacentHTML('beforeend',output);
    }

            /****
         * 
         * does the "search"
         * 
         */

    static searchUL(){
        let searchText =  document.getElementById('search-ul-input').value;
        let fields = document.querySelectorAll('.user-list-li');

        if(searchText){
            for (let index = 0; index < fields.length; index++) {
                const element = fields[index];
                if(element.innerHTML.toLowerCase().search(searchText.toLowerCase()) > -1){
                    let id = element.id.replace('user-item-li-','');
                    document.getElementById(`user-item-li-${id}`).classList.remove('hidden');
                }else{
                    let id = element.id.replace('user-item-li-','');
                    document.getElementById(`user-item-li-${id}`).classList.add('hidden'); 
                }
                
                
            }
        }else{
        
            for (let index = 0; index < fields.length; index++) {
                const element = fields[index]; 
                let id = element.id.replace('user-item-li-','');
                document.getElementById(`user-item-li-${id}`).classList.remove('hidden');
            }

        } 
    }

    /****
     * 
     * uncheacks all user checkboxes
     * 
     */
    static uncheckAll(){
        for (const dropCategories of document.querySelectorAll(`.checkbox-user-li`)){
            dropCategories.checked = false;
            }
            User.markChecked();
    }

    /***
     * 
     * gives user list a color if checked
     * 
     */

    static markChecked(){
        
        for (const dropCategories of document.querySelectorAll(`.checkbox-user-li`)){
            let id = dropCategories.id.replace('user-item-','');
            var element = document.getElementById(`user-item-li-${id}`);
            if(dropCategories.checked == true){
                element.classList.add('in-dragg'); 
            }else{
                element.classList.remove('in-dragg');
            }
        }
    } 
    
    static checkUser(id,checkbox = false){

        if(checkbox){

            if(document.getElementById(`user-item-${id}`).checked){
                document.getElementById(`user-item-${id}`).checked = true;
            }else{
                document.getElementById(`user-item-${id}`).checked = false;  

            }

        }else{
            if(document.getElementById(`user-item-${id}`).checked){
                document.getElementById(`user-item-${id}`).checked = false;
            }else{
                document.getElementById(`user-item-${id}`).checked = true;  
            }
        }
        User.markChecked();

    }
    
    /****
     * 
     * creates new user role assignment
     * 
     */

    assignUserCategory(categoryid,roleid = null){
        if(roleid){
            var postString = `${this.userid}&${categoryid}&${roleid}`;
        }else{
            var postString = `${this.userid}&${categoryid}`;
        }
        
        $.ajax({
            url:"AJAX/AJAX.php",
            method:"POST",
            data:{assignUserCategory:postString},
            dataType:"json",
            success:function(data)
            {
              //document.querySelector('#results').innerHTML=''
              
              feedback.feedBack(data);
              
              //if there is 
                if(document.querySelector('.info-window')){
                    
                    general.updateCategoryForm(categoryid,roleid);
                   // let user = new User(this.userid)
                  // let output = `<li>${value.firstname} ${value.lastname}<br> ${value.email}</li>`;
                }

            },error:function (data){
                
              }
          });

    }



    /****
     * 
     * updates user role in given category
     * 
     */


     updateUserRole(categoryid,roleid,actualRoleId){
    
        var postString = `${this.userid}&${categoryid}&${roleid}`;
        
        $.ajax({
            url:"AJAX/AJAX.php",
            method:"POST",
            data:{updateUserRole:postString},
            dataType:"json",
            success:function(data)
            {

                feedback.feedBack(data);
                if(document.querySelector('.info-window')){
                    
                    general.updateCategoryForm(categoryid,actualRoleId);
                   // let user = new User(this.userid)
                  // let output = `<li>${value.firstname} ${value.lastname}<br> ${value.email}</li>`;
                }

            },error:function (data){
                
              }
          });

    }



    /****
     * 
     * enrols user into ocourse
     * 
     */

    enrolUserCourse(courseid,dontShowList = false){
        var postString = `${this.userid}&${courseid}`;
        $.ajax({
            url:"AJAX/AJAX.php",
            method:"POST",
            data:{enrolUserCourse:postString},
            dataType:"json",
            success:function(data)
            {
              //document.querySelector('#results').innerHTML=''
              
              feedback.feedBack(data);
              if(dontShowList != true){
                Course.courseInfo(courseid);
              }
              //Course.courseInfo(courseid);
              
    
            },error:function (data){
                
                feedback.feedBack(data);
              }
          });

    }

/******
 * 
 * renders windows with user info
 * 
 */
    getUserInfo(){
        //remove previous window of user or course
        self = this;
        $.ajax({
            url:"AJAX/AJAX.php",
            method:"POST",
            data:{getUserInfo:self.userid},
            dataType:"json",
            success:function(data)
            {
              //document.querySelector('#results').innerHTML=''

              User.renderUserInfoWindow(data);
    
            },error:function (data){
                
              }
          });

    }

    /******
     * sub function fo getUserInfo to do the staff AJAX returns
     * 
     * * */

     static renderUserInfoWindow(data){
        //close previous window
        general.closeInfoWindow();
        User.checkUser(data.userid);

        //head
        let output = `<div class="info-window info-window-background-user"><div class="close-btn-info-window"><i class="fa fa-close"></i></div>
        <div class="header-info">
        <h1>${data.firstname} ${data.lastname}</h1>
        <b>email: ${data.email}</b>
        <a href="${HOMEURL}/user/profile.php?id=${data.userid}"><button class="user-btn">Nastavení profilu</button></a></div>`;

        //roles
        if(data.contextArray){
            output += User.renderUserRolesList(data.contextArray);  
        }else{
            
        }  

        if(data.courses){
            output += User.renderUserCoursesList(data.courses,data.userid);  
        } 
        output += `</div>`;
        document.getElementById('results-users').insertAdjacentHTML('afterend',output);
        AfterRenders.closeWindow();
        AfterRenders.userInfoPanelListeners();

    }



    /*****
     * 
     * renders list of user roles for RenderUserInfoWindow
     * 
     */

     static renderUserRolesList(contextArray){

        let output = `<div class="user-roles"><h3>Přiřazené členství</h3>
            <table class="table-view">
            <thead>
                <tr>
                    <th width="150px">Role</th>
                    <th width="200px">Kategorie</th>
                    <th>Akce</th>
                </tr>
            </thead><tbody>
            `;

            for (const [key, value] of Object.entries(contextArray)) {
                output += `<tr>
                                <td width="150px">${value.rolename}</td>
                                <td width="200px">${value.name}</td>
                                <td>${User.caneditCategory(value)}</td>
                                <td><button class="update-category-from-user-form" categoryid="${value.categoryid}">Upravit kategorii</button></td>
                            </tr>`;
            }

            output += `</tbody></table></div>`;
        return output;

    }


    /****
     * just renders button if user can remove user roles
     * 
     */
    static caneditCategory(contextArray){

        if(contextArray.canedit){
            return `<button class="remove-member" contextid="${contextArray.roleassignid}">Odebrat členství</button>`;
        }else{
            return '';
        }

    }


    static renderUserCoursesList(courses,userid){
        let output = `<div class="user-courses">
        <div class="user-roles"><h3>Zapsané kurzy</h3>
        <table class="table-view">
        <thead>
            <tr>
                <th width="100px">Role</th>
                <th width="550px">Kurz</th>
                <th>Akce</th>
            </tr>
        </thead><tbody>`;
        for (const [key, value] of Object.entries(courses)) {  
            output += `<tr>
                                <td width="100px">${value.rolename}</td>
                                <td width="550px"><a href="${HOMEURL}/course/view.php?id=${value.id}">${value.fullname}</a></td>
                                <td>${User.caneditUnenroll(value,userid)}</td>
                            </tr>`;
        } 

        output += `</tbody></table></div>`;
        return output;


    }


      /****
     * just renders button if user can remove user roles
     * 
     */
       static caneditUnenroll(courses,userid){

        if(courses.canedit){
            return `<button value="${userid}" courseid="${courses.id}" class="unenrol-user-btn">Odhlásit</button>`;
        }else{
            return '';
        }

    }
/****
 * 
 * unasign user role, based only on role assignment id
 * 
 */

    static unAsignUserRole(assigneid){
        
        let element = event.srcElement.parentElement.parentElement;
        
        $.ajax({
            url:"AJAX/AJAX.php",
            method:"POST",
            data:{removeRoleAssignment:assigneid},
            dataType:"json",
            success:function(data)
            {
              //document.querySelector('#results').innerHTML=''
              
              general.removeEffect(element);
              feedback.feedBack(data);
              //User.renderUserInfoWindow(data);
    
            },error:function (data){
                
              }
          });
    }



    static unAsignUserRoleCategory(data){
        let transferArray = data.split('&');
        let userid = transferArray[0];
        let roleid = transferArray[1];
        var categoryid = transferArray[2];;

        $.ajax({
            url:"AJAX/AJAX.php",
            method:"POST",
            data:{unAsignUserRoleCategory:data},
            dataType:"json",
            success:function(data)
            {
              
              feedback.feedBack(data);

                    if(document.querySelector('.info-window')){
                        
                        general.updateCategoryForm(categoryid,roleid);

            }
    
            },error:function (data){
                
              }
          });
    }



    /*****
     * 
     * unenroll AJAX
     * 
     */

      unEnroll(courseid){
        
        let userid = event.srcElement.value;
        let element = event.srcElement.parentElement.parentElement;
        let postString = `${userid}&${courseid}`;
        $.ajax({
            url:"AJAX/AJAX.php",
            method:"POST",
            data:{unenrollUser:postString},
            dataType:"json",
            success:function(data)
            {
              //document.querySelector('#results').innerHTML=''
              
              general.removeEffect(element);
              feedback.feedBack(data);
              //User.renderUserInfoWindow(data);
    
            },error:function (data){
                
              }
          }); 
          
    }


     /*****
     * 
     * unenroll for drag and drop
     * 
     */

      unEnrollDrag(courseid){
        
       
        let postString = `${this.userid}&${courseid}`;
        $.ajax({
            url:"AJAX/AJAX.php",
            method:"POST",
            data:{unenrollUser:postString},
            dataType:"json",
            success:function(data)
            {
              //document.querySelector('#results').innerHTML=''
              
              feedback.feedBack(data);
              Course.courseInfo(courseid);
              //User.renderUserInfoWindow(data);
    
            },error:function (data){
                
              }
          }); 
    }



    

   

}