<?php

namespace local_my_organization;

namespace local_my_organization;
use local_my_organization\Category\Category;
use local_my_organization\User\User;
use local_my_organization\Course\Course;
use local_my_organization\Processor\Processor;

require_once '../../../config.php';
require_login();
global $DB,$USER;


if (isset($_POST) AND isset($_POST['categoryInfo'])) {
    
    echo json_encode(Processor::categoryInfo($_POST['categoryInfo']));
//ELSE

}elseif(isset($_POST) AND isset($_POST['createCategory'])) {

    echo json_encode(Processor::createCategory($_POST['createCategory']));

//ELSE    
}elseif(isset($_POST) AND isset($_POST['getMyCategories'])) {
        
    echo json_encode(Processor::getMyCategories($_POST['getMyCategories'])); 
    
    
//ELSE    get user rights
}elseif(isset($_POST) AND isset($_POST['getUserRights'])) {

    
    echo json_encode(Processor::getUserRights($_POST['getUserRights']));
        
       
//ELSE   get admin rights
}elseif(isset($_POST) AND isset($_POST['getAdminRights'])) {

    
    echo json_encode(Processor::getAdminRights($_POST['getAdminRights']));
        
       
//ELSE    delete Category
}elseif(isset($_POST) AND isset($_POST['deleteCategory'])) {
    

    echo json_encode(Processor::deleteCategory($_POST['deleteCategory']));
        
       
}elseif(isset($_POST) AND isset($_POST['getUsersList'])) {


    echo json_encode(Processor::getUsersList($_POST['getUsersList']));


//else
}elseif(isset($_POST) AND isset($_POST['getCourseList'])) {
    

    echo json_encode(Processor::getCourseList($_POST['getCourseList']));

        
//enrol user to course       
}elseif(isset($_POST) AND isset($_POST['enrolUserCourse'])) {
    

    echo json_encode(Processor::enrolUserCourse($_POST['enrolUserCourse']));

        
//assign user an category       
}elseif(isset($_POST) AND isset($_POST['assignUserCategory'])) {
    

    echo json_encode(Processor::assignUserCategory($_POST['assignUserCategory']));
        
 
// else user info window   
}elseif(isset($_POST) AND isset($_POST['getUserInfo'])) {


    echo json_encode(Processor::getUserInfo($_POST['getUserInfo']));

        
// else get info for course info window       
}elseif(isset($_POST) AND isset($_POST['getCourseInfo'])) {


   echo json_encode(Processor::getCourseInfo($_POST['getCourseInfo']));
    

// else get info for category info window         
}elseif(isset($_POST) AND isset($_POST['updateCategory'])) {


    echo json_encode(Processor::updateCategory($_POST['updateCategory']));
        
       
// else get info for category info window         
}elseif(isset($_POST) AND isset($_POST['removeRoleAssignment'])) {


    echo json_encode(Processor::removeRoleAssignment($_POST['removeRoleAssignment']));
        
       
// else unernolls user       
}elseif(isset($_POST) AND isset($_POST['unenrollUser'])) {


    echo json_encode(Processor::unenrollUser($_POST['unenrollUser']));
        

// else update user role
}elseif(isset($_POST) AND isset($_POST['updateUserRole'])) {


        echo json_encode(Processor::updateUserRole($_POST['updateUserRole']));
            
//else remove user role           
}elseif(isset($_POST) AND isset($_POST['unAsignUserRoleCategory'])) {


    echo json_encode(Processor::unAsignUserRoleCategory($_POST['unAsignUserRoleCategory']));
        
       
}elseif(isset($_POST) AND isset($_POST['searchAll'])) {


    echo json_encode(Processor::searchAll($_POST['searchAll']));
        
       
}




























