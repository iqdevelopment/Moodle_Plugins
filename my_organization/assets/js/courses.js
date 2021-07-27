import Category from './category.js'
import User from './users.js'
import * as listener from './listeners.js'
import * as general from './output.js'
import AfterRenders from './afterRenders.js'

export default class Course{

    courseid;
    constructor(courseid){
        this.courseid = courseid;
    }


    
    static renderListOfCourses(categoryid,filtered = false,admin = false,divId = null){
        //Course = this;
        if(filtered == true){
            var postString = `${categoryid}&1`;  
        }else{
            var postString = categoryid;
        }
        if(admin == true){
            var postString = `${categoryid}&1&1`; 
        }

        
        $.ajax({
            url:"AJAX/AJAX.php",
            method:"POST",
            data:{getCourseList:postString},
            dataType:"json",
            success:function(data)
            {
              //document.querySelector('#results').innerHTML=''
              
              Course.renderCourseListHead(categoryid,admin);
              for (const [key, value] of Object.entries(data)) {  
                Course.renderListOfCoursesItem(value);
              }
              Course.renderCourseListEnd();
              general.dragOverUsersToCourses('course-list-li');
              AfterRenders.coursePanelListListeners();
              if(divId != null){
                general.cancelLoader(divId); 
              }
              //Course.renderBasicCategoriesForUser(data);
            //return data;

            },error:function (data){
                
              }
          });
    }

    /***
     * 
     * render one item of the list
     */

    static renderListOfCoursesItem(object){
        let output = `
        <li class="course-list-li" courseid="${object.id}" id="course-item-li-${object.id}">
       
        <div courseid="${object.id}" class="course-fullname" id="course-item-li-${object.id}">${object.fullname}</div>${object.startdate}-${object.enddate}
        <div class="course-more-info" courseid="${object.id}"  id="course-item-li-${object.id}"><i class="fa fa-info-circle fa-lg"></i></div>
        </li>`;
        
        document.getElementById('course-list-ul').insertAdjacentHTML('beforeend',output);

    }

    /****
     * 
     * render searchbox for users
     * 
     */

    static renderCourseListHead(categoryid = null,admin = false){

       let headerExists = document.getElementById('search-ul-input-courses');
       if(headerExists){
            var checkme_course = document.getElementById('course-count-control').checked;
            document.getElementById('results-courses').innerHTML = '';
       }
       var output = `
            <input type="search" placeholder="Název, datum kurzu" id="search-ul-input-courses"> </input><input type="checkbox" id="course-count-control" category="${categoryid}"`;
            if(categoryid != null && admin == false){
                output +=  `></input> Jen z kategorie`;
            }else{
                output += `class="hidden">`
            }
        output += `<br>`;
        output +=`<ul class="course-list-ul" id="course-list-ul">`;
        document.getElementById('results-courses').insertAdjacentHTML('beforeend',output);
        document.getElementById('course-count-control').checked = checkme_course;
    }

    /***
     * end of course list
     * 
     */
    static renderCourseListEnd(){
        let output = `</ul>`;
        document.getElementById('results-courses').insertAdjacentHTML('beforeend',output);
    }

    /*****
     * 
     * does the searching in courses
     * 
     */
    static searchUL(){
      let searchText =  document.getElementById('search-ul-input-courses').value;
      if(searchText){
          let fields = document.querySelectorAll('.course-list-li');
          for (let index = 0; index < fields.length; index++) {
              const element = fields[index];
              if(element.innerHTML.toLowerCase().search(searchText.toLowerCase()) > -1){
                let id = element.id.replace('course-item-li-','');
                document.getElementById(`course-item-li-${id}`).classList.remove('hidden');
              }else{
                 let id = element.id.replace('course-item-li-','');
                 document.getElementById(`course-item-li-${id}`).classList.add('hidden'); 
              }
              
              
          }
      }
    }

    /*
     * 
     * render course info window 
     * 
     */


    getCourseInfo(){
        //remove previous window of user or course
        self = this;
        $.ajax({
            url:"AJAX/AJAX.php",
            method:"POST",
            data:{getCourseInfo:self.courseid},
            dataType:"json",
            success:function(data)
            {
    
                let output = `<div class="info-window info-window-background-course"><div class="close-btn-info-window"><i class="fa fa-close"></i></div>`;
               
                let startdate = data.startdate;
                let enddate = data.enddate;
                output += `<div class="course-info"><h2>${data.fullname}</h2><span>${startdate} - ${enddate}</span><br>
                <a href="${HOMEURL}/course/view.php?id=${data.id}">Nastavení kurzu</a></div>`;
                output += self.renderListOfUsers(data);
                        
                output += `</div>`;
                general.closeInfoWindow();
                document.getElementById('results-users').insertAdjacentHTML('afterend',output);
                AfterRenders.closeWindow();
                AfterRenders.courseDetailListeners();
            
    
            },error:function (data){
                
              }
          });

    }


   

     /****
      * 
      * handles data from renderCategoryRolesCarousel AJAX call and renders the carousel itself
      * 
      */




    renderListOfUsers(data){
        self = this;
        let output = `<div class="course-users" course="${data.id}"> <input type="search" id="search-ul-input-courses-users" placeholder="Jméno, příjmení, email uživatele" ></input> 
                <select id="role-filter">
                <option value='all'>vše</option>
                <option value='student'>studenti</option>
                <option value='else'>lektoři</option>
                </select> <ul>`;
        for (const [key, value] of Object.entries(data.users)) {
            let roleClass = '';
            let roleName = '';
            if(value.rolename == 'student'){

            }else{
                roleClass = 'teacher-in-course';
                roleName = `<b class='align-right'>lektor</b>: `;
            }
            output += `<li draggable="true" class="course-user-list-li ${roleClass}" userid="${value.id}" rolename="${value.rolename}" id="user-item-li-${data.id}">${roleName}${value.lastname} ${value.firstname}<br>${value.email}</li>`;
           
            
        }
        output += `</ul></div>`;
    
    
        return output;


    }



     /*****
     * 
     * does the searching in courses
     * 
     */
       

      static searchULList(categoryid){
        let searchText =  event.srcElement.value;
        let fields = document.querySelectorAll('.course-user-list-li');
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
 * shows bin and attaches event listeners, also sets the transferText
 * 
 */

      static showBin(ev) {
        //do it for multiple separated by comas
        let draggedText = `${ev.target.getAttribute("userid")}&${general.getParrentDiv(ev.srcElement).getAttribute("course")}`;
         ev.dataTransfer.setData("text",draggedText);
         Course.renderBinToDrop();
    } 
  
 /****
  * 
  * removes bin incon
  * 
  */


    static endBin() {
         document.querySelector('.drop-trash').remove();
    }

/***
 * 
 * bin and listeners
 * 
 */

    static renderBinToDrop(courseid){
        let output = `<div class="drop-trash" trash="${courseid}"><i class="fa fa-trash fa-10x"></i></div>`;
        document.getElementById('results-users').insertAdjacentHTML('afterend',output); 
        let element = document.querySelector('.drop-trash');
        //events
        element.addEventListener("drop", listener.dropHandlerUserIntoCourseEnrollAndUnenroll);
        element.addEventListener("dragleave", listener.dragLeaveHandlerNP);
        element.addEventListener("dragover", listener.dragOverHandlerNP);
        
    }



    /*****
     * 
     * assing event listeners for course assignment from left user bar
     * 
     */
    

     static dragOverUsers(cssClass) {

        for (const dropCategories of document.querySelectorAll(`.${cssClass}`)){
                dropCategories.addEventListener("dragover", listener.dragOverHandlerNP);
        }

        //change style and allow dropping
        for (const dropCategories of document.querySelectorAll(`.${cssClass}`)){
                dropCategories.addEventListener("dragleave", listener.dragLeaveHandlerNP);
        }
    
        //dropy
        for (const dropCategories of document.querySelectorAll(`.${cssClass}`)){
                dropCategories.addEventListener("drop", listener.dropHandlerUserIntoCourseEnrollAndUnenroll);
        }
    }


    /****
 * 
 * render info of course
 * 
 */
static courseInfo(id) {
    let course = new Course(id);
    course.getCourseInfo();
}

/****
 * 
 * in course detail panel filters users to all,students, and else
 * 
 */

static filterCourseUsers(){
    const filter = document.getElementById('role-filter');
    if(filter){
        const value = filter.value;

        const fields = document.querySelectorAll('.course-user-list-li');

        for (let index = 0; index < fields.length; index++) {
            const element = fields[index];
            const role = element.getAttribute('rolename');
             if(value == 'all'){
              element.classList.remove('hidden');
            }else if (role == value && value == 'student'){
               element.classList.remove('hidden'); 
            }else if (value == 'else' && role != 'student'){
                element.classList.remove('hidden'); 
            }else{
                element.classList.add('hidden');  
            }           
        }

    }
}



}