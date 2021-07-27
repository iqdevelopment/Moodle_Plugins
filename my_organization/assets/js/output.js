import Category from './category.js'
import User from './users.js'
import Course from './courses.js'
import * as listener from './listeners.js'
import AfterRenders from './afterRenders.js'

export function showTree(categoryid,userId,CoursesId) {
    let state = false;
    let obj = new Category(categoryid);
    obj.categoryInfo();

    if(document.getElementById('user-count-control')){
    state = document.getElementById('user-count-control').checked;
    }
    //render users
    let user = new User(USER);
    user.renderListOfUsers(categoryid,state,false,userId);

    if(document.getElementById('user-count-control')){
        state = document.getElementById('course-count-control').checked;
    }
    Course.renderListOfCourses(categoryid,state,false,CoursesId);
    
}


// on click export function to show from to create new 
export function createCategoryForm(parent){
    Category.renderNewCategoryForm(parent);

}

export function createCategory(parent){
    Category.createCategory(parent);

}

export function createCategoryIdname() {
    Category.createCategoryIdname();
}

export function errorblink(elementid) {
    let element_instance = document.getElementById(elementid);
    //setTimeout(() => {  element_instance.classList.add('invalid-blink'); }, 950);
    element_instance.classList.add('invalid-blink');
    setTimeout(() => {  element_instance.classList.remove('invalid-blink'); }, 950);
    
}

export function closeForm() {
    if(document.getElementById('create-category-form')){
        document.getElementById('create-category-form').remove();
    }
}

export function deleteCategoryForm(id) {
    Category.renderDeleteCategoryForm(id);
}

export function updateCategoryForm(id,roleid = 0) {
    let category = new Category(id);
    category.renderUpdateCategoryForm(id,roleid);
}

/***
 * Creates the move-to dropdown of possible categories where user can move the items from deleted category
 * 
 */

export function whereToMoveDeletedCourses(id) {
    let value = document.getElementById('delete-category-select').value;
    if(value == 'move'){
    document.getElementById('delete-move-select').innerHTML = '';
    Category.renderSelectOfCategories(id,'delete-move-select');

    }else{
    document.getElementById('delete-move-select').innerHTML = '';
    }
    
}

/***
 * 
 * AJAX task to delete category
 */
export function deleteCategory(id){
   // renderLoader(document.getElementById('create-category-form'),'loader');
    let obj = new Category(id);
    obj.deleteCategory();
    //cancelLoader('loader');

}

/****
 * 
 * 
 */
//loader
export function renderFullLoader(parentSelector){
    parent = document.querySelector(`${parentSelector}`);
    let id = "ID" + Math.random().toString(36).substring(2, 15) + Math.random().toString(36).substring(2, 15);
    
    const loader = `<div  id=${id} class="loader-space">
                <div class='loader'>
                </div>
            </div>
    `;
    parent.insertAdjacentHTML('afterbegin',loader);
    
    return id;
    }

export function cancelLoader(id){
    
    const element = document.querySelector(`#${id}`);
    if (element){
        element.remove();
    }
    };


    
    /****
     * 
     * export function on click to show users categories
     * 
     */

export function showMyCategories(userid) {
    let user = new User(userid);
    closeForm();
    closeInfoWindow();
    user.getUserRights();

    
    document.getElementById('results-users').innerHTML = '';
    document.getElementById('results-courses').innerHTML = '';

}


/***
 * 
 * show all users and categories for administrator
 * 
 */

export function showAdminMyCategories() {
    closeForm();
    closeInfoWindow();
    document.getElementById('results-users').innerHTML = '';
    document.getElementById('results-courses').innerHTML = '';
    let user = new User(0);
    user.getAdminRights();
   
    
}


/***
 * 
 * on start of drag one item, starts the drag watterfall of events
 * 
 */

export function dragWatterFall(ev) {
   
    // checks if multiple users are selected
    let check = dragMultiple();
    if(check){
        var draggedText = check;
    }else{
        var draggedText = ev.target.id.replace('user-item-li-',''); 
    }

    //sets what data are dragged
    ev.dataTransfer.setData("text",draggedText);
    
    // sets class to look pretier 
    document.getElementById(ev.target.id).classList.add('in-dragg');
    document.getElementById(ev.target.id).addEventListener("dragend", e => {
        e.target.classList.remove('in-dragg');
    });

    //sets event listeners for category assig - via category detail and caoursel
 
    if(document.getElementsByClassName('category-container')){
        Category.dragOverCategory('roles-carousel');
    }

    //event listeners for enrolling users
   if(document.getElementsByClassName('course-info')){
        Course.dragOverUsers('course-users');
    } 

    
}


/****
 * end of drag
 * 
 */

export function dragWatterFallEnd() {
   
    if(document.getElementsByClassName('close-btn-info-window')){
        Category.dragOverCategoryEnd('roles-carousel');
    }
    
}







export function dragOver(cssClass) {

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
        User.uncheckAll();
}




/****
 * 
 * event listener for left pane users to categories
 * 
 */



 export function dragOverUsersToCategories(cssClass) {

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
    User.uncheckAll();
}


/****
 * 
 * event listener for left pane users to courses
 * 
 */



 export function dragOverUsersToCourses(cssClass) {

    for (const dropCategories of document.querySelectorAll(`.${cssClass}`)){
        dropCategories.addEventListener("dragover", listener.dragOverHandler);
    }

    //change style and allow dropping
    for (const dropCategories of document.querySelectorAll(`.${cssClass}`)){
            dropCategories.addEventListener("dragleave", listener.dragLeaveHandler);
    }

    //dropy
     for (const dropCategories of document.querySelectorAll(`.${cssClass}`)){
            dropCategories.addEventListener("drop", listener.dropHandlerUserIntoCourseList);    
            } 
    User.uncheckAll();
}


/*****
 * 
 * export function to push all checked users to array
 * 
 */

export function dragMultiple() {
    var trueArray = [];
    for (const selectMultiple of document.querySelectorAll('.user-list-li')){
        let id = selectMultiple.id.replace('user-item-li-','');
        
        if(document.getElementById(`user-item-${id}`).checked){
            trueArray.push(id);
        }
    }
   
    if(trueArray.length){
        return trueArray;
    }else{
        return false;
    }
    
}


/****
 * 
 * render info of User
 * 
 */
export function  userInfo(id) {
    let user = new User(id);
    user.getUserInfo();
    
}



/****
 * 
 * close user-info-window
 * 
 */

export function closeInfoWindow() {
    let windowInfo = document.querySelector('.info-window');
    if(windowInfo){
        windowInfo.remove();
    }
    
}

/*****
 * 
 * effect to blink green and remove element
 * 
 */

export function removeEffect(element) {
    
    element.classList.add('confirm-blink');
    setTimeout(() => {  element.classList.remove('confirm-blink'); }, 950);
    setTimeout(() => {  element.remove(); }, 1);
    
    
}




/*****
 * 
 * dragover for category list
 * 
 * 
 */
   export function showBin(ev) {
       let draggedText = ev.target.getAttribute("userid");
       
       draggedText += `&`;
       //roleid
       draggedText += ev.target.parentElement.parentElement.id.replace('roles-carousel-','');
       draggedText += `&`;
       draggedText += ev.target.parentElement.parentElement.getAttribute("category");

        ev.dataTransfer.setData("text",draggedText);
        Category.dragOverCategoryNoParrents('carousel-btn');
        Category.renderBinToDrop();
   } 
 

   export function endBin() {
        Category.dragOverCategoryEnd('carousel-btn'); 
        document.querySelector('.drop-trash').remove();
   }



   export function getParrentDiv(target) {
       while (target.nodeName != 'DIV') {
           target = target.parentElement;  
       }
    
        return target;
    }


    /*
    re-render cateogry list
    */
    export function rerenderCategoryList() {
        
        const idUser = renderFullLoader('#results-users');
        const idCourse = renderFullLoader('#results-courses');

        const parentElement = document.querySelector('.category-main-categories');
        const parentid = parentElement.getAttribute("category");
        showTree(parentid,idUser,idCourse); 
    }

