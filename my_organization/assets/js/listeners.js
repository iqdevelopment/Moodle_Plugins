import Category from './category.js'
import User from './users.js'
import Course from './courses.js'
import * as listener from './listeners.js'
import * as general from './output.js'

/***
 * 
 * adds class dragover on dragover
 * 
 */

export var dragOverHandler = function(e) {
    e.preventDefault();
    e.target.parentElement.classList.add('dragover');
}

 // dragOver without user of parents


export var dragOverHandlerNP = function(e) {
    e.preventDefault();
    e.target.classList.add('dragover');
}



/****
 * 
 * add class when drag leave
 * 
 */


export var dragLeaveHandler = function (e) {
    e.target.parentElement.classList.remove('dragover');
}

// drag leave without parents

export var dragLeaveHandlerNP = function (e) {
    e.target.classList.remove('dragover');
}





/****
 * 
 * defines drop actions
 * 
 */

//from left panel to category role carousel
export var dragDropHandler = function (e) {
    let check = general.dragMultiple();
    var data = e.dataTransfer.getData("text");
    var target = e.target.id;
    
    ////console.log(e.target.nodeName);
     if(e.target.nodeName == 'LI'){
        var div = e.target.parentElement.parentElement;
    }else if(e.target.nodeName == 'UL'){
        var div = e.target.parentElement;
    }else{
        var div = e.target;
    } 
    //var div = e.target;
    //console.log(div);
    //console.log(data);

    //multiple targets
    if(check){
        
        for (let index = 0; index < check.length; index++) {
            const element = check[index];
            let user = new User(element);
            user.assignUserCategory(div.getAttribute("category"),div.id.replace('roles-carousel-',''));
        }       
        
    //only one user
    }else{
        let user = new User(data);
        user.assignUserCategory(div.getAttribute("category"),div.id.replace('roles-carousel-',''));

    }
   

    e.target.parentElement.classList.remove('dragover');
  User.uncheckAll();
}



/**
 * 
 *  
 */









export var DropHandlerUserPanelToCategoryButton = function (e) {
    let check = general.dragMultiple();
    //console.log(check);
    //if more users are passed in the if statement this value is superseed by array from check
    var userid = e.dataTransfer.getData("text");
    var target = e.target.id;
    var div = e.target;
    let categoryid = div.getAttribute("category");
    //console.log(categoryid);
    //console.log(userid);


    //multiple targets
    if(check){
        
        for (let index = 0; index < check.length; index++) {
            const element = check[index];
            let user = new User(element);
            user.assignUserCategory(categoryid);
        }       
        
    //only one user
    }else{
        let user = new User(userid);
        user.assignUserCategory(categoryid);

    }
   

    e.target.parentElement.classList.remove('dragover');
  User.uncheckAll();
}


/***
 * 
 * special for change user role in category info
 * 
 */

export var dragDropHandlerNP = function (e) {
    let check = general.dragMultiple();
    var data = e.dataTransfer.getData("text");
    let transferArray = data.split('&');
    let userid = transferArray[0];
    let actualRoleId = transferArray[1];
    var roleid = e.target.id.replace('carousel-btn-','');
    var categoryid = e.target.parentElement.getAttribute("category");
    //console.log(e);
    
    //console.log(e.dataTransfer);

    //multiple targets
    if(check){
        
        for (let index = 0; index < check.length; index++) {
            const element = check[index];
            let user = new User(element);
            user.updateUserRole(categoryid,roleid,actualRoleId);
        }       
        
    //only one user
    }else{
        let user = new User(userid);
        user.updateUserRole(categoryid,roleid,actualRoleId);

    }

  User.uncheckAll();
}


/***
 * 
 * special for unasing users form category
 * 
 */

 export var dragDropHandlerCategory = function (e) {
    let check = general.dragMultiple();
    var data = e.dataTransfer.getData("text");

    let transferArray = data.split('&');
    let userid = transferArray[0];
    let roleid = transferArray[1];
    var categoryid = transferArray[2];;
    //console.log(e);
    //multiple targets
    if(check){
        
        for (let index = 0; index < check.length; index++) {
            const element = check[index];
            let user = new User(element);
           User.unAsignUserRoleCategory(data);
        }       
        
    //only one user
    }else{

       User.unAsignUserRoleCategory(data);

    }

  User.uncheckAll();
}

/***
 * 
 * handler for enrolling and unenrolling users when course details is active
 * 
 */


export var dropHandlerUserIntoCourseEnrollAndUnenroll = function (e) {
    let check = general.dragMultiple();
    var data = e.dataTransfer.getData("text");

    let transferArray = data.split('&');
    let userid = transferArray[0];
    var courseid = transferArray[1];
    //console.log(data);
    ////console.log(e.target);
    let div = general.getParrentDiv(e.target);
    //console.log(div);
    var categoryid = div.getAttribute("category");
    var courseid = div.getAttribute("course");
    var trash = div.getAttribute("trash");
    if(courseid){
        //console.log('yup course');
        let userArray = data.split(',');
        userArray.forEach(element => {
            //console.log(element);
            let user = new User(element);
            let output = user.enrolUserCourse(courseid);
            //console.log(output);
        });

    }else if(trash){
        //console.log('yup trash');
        let userArray = data.split(',');
        //console.log(data);
        userArray.forEach(element => {
            let unenrollArray = element.split('&');
            let userid = unenrollArray[0];
            let courseid = unenrollArray[1];
            let user = new User(userid);
            let output = user.unEnrollDrag(courseid);
            //console.log(courseid);
        });


    }


  User.uncheckAll();
}


export var dropHandlerUserIntoCourseList = function (e) {
    let check = general.dragMultiple();
    var data = e.dataTransfer.getData("text");
    var target = e.target.id;
    
    ////console.log(e.target.nodeName);
     if(e.target.nodeName == 'LI'){
        var div = e.target.parentElement.parentElement;
    }else if(e.target.nodeName == 'UL'){
        var div = e.target.parentElement;
    }else{
        var div = e.target;
    } 
    //var div = e.target;
    //console.log(div);
    //console.log(data);

    //multiple targets
    if(check){
        
        for (let index = 0; index < check.length; index++) {
            const element = check[index];
            let user = new User(element);
            let courseid = div.getAttribute("courseid");
            //console.log(div.getAttribute("courseid"));
            user.enrolUserCourse(courseid,true);
        }       
        
    //only one user
    }else{
        let user = new User(data);
        //console.log(div.getAttribute("courseid"));
        let courseid = div.getAttribute("courseid");
        user.enrolUserCourse(courseid,true);
    }
   

    e.target.parentElement.classList.remove('dragover');
  User.uncheckAll();
}

