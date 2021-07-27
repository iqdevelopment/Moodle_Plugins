<?php


namespace local_my_organization\Processor;
use local_my_organization\Category\Category;
use local_my_organization\User\User;
use local_my_organization\Course\Course;

class Processor{
    

    /****
     * gets data of existing category and full tree
     * 
     */

    static function categoryInfo($data)
    {
    
        $obj = new Category('','', $data);
        $obj->getCategoryFullTree();
        return $obj;
    
    }

    /*****
     * 
     * creates category
     * 
     */

    static function createCategory($data)
    {
    
        $string_array = \explode('&',$data);
        $check = TRUE;
        foreach ($string_array as $key => $value) {
            $output = Category::validateInput($value);
            if($output == FALSE){
                $check = FALSE;
            }
        }
    
        if (!$check) {
            $output = new \stdClass();
            $output->type = 'negative';
            $output->data = get_string('check_inputs','local_my_organization');
            
            return $output;
        }else{
            $obj = new Category($string_array[0],$string_array[1]);
            $response = $obj->createCategory($string_array[2]);
            if($response){
                return $response;   
            }else{
                return '';
            }
        }
    }


    /**
     * 
     * gets my categories
     * 
     */


    static function getMyCategories($data)
    {   global $USER;
    
        $obj = new User($USER->id);
        $obj->getUserContextsAndRoles();
            return $obj;
    }


    /***
     * 
     * gets user rights
     * 
     */

    static function getUserRights($data)
    {   global $USER,$DB;
    
        if($data == -1){
            $userid = $USER->id;
        }else{
            $userid =  $data;
        }
        $obj = new User($userid);
        $obj->getUserContextsAndRoles();

        $trimmed_context_array = array();
        foreach ($obj->contextArray as $key => $value) {
            if($value->canViewUsers == 1){
                $trimmed_context_array[] = $value;  
            }
        }

       // return $obj->contextArray;
        return $trimmed_context_array;
    }

     /***
     * 
     * gets all root categories and users for managing
     * 
     */

    static function getAdminRights($data)
    {   global $USER,$DB;

        $admins = $DB->get_field_sql('SELECT value FROM {config} WHERE name = ?',array('siteadmins'));
        $admins = \explode(',',$admins);
        //user is not site admin, return false
        if(!in_array($USER->id,$admins)){
            return false;
        }else{
            //user is site admin and can continue

             $obj = $DB->get_records_sql('SELECT id as categoryid,name FROM {course_categories} WHERE parent = 0');
             foreach ($obj as $value) {
                 $value->rolename = 'administrator';
                 $value->coursesAll = $DB->get_field_sql('SELECT count(*) FROM {course} WHERE category IN 
                    (
                        SELECT id FROM {course_categories} WHERE path like "%/'.$value->categoryid.'/%" OR path like "%/'.$value->categoryid.'"
                    )
                ');
                 # code...
             }
            /*  $test = new User(0);
             $test->contextArray = $obj;
             $test->getInfoInSubcategories(); */

             return $obj;
        }
        
       
    }



    


    /*****
     * 
     * deletes a category
     * 
     */

    static function deleteCategory($data)
    {   global $USER;
    
        $post_arraty = \explode('&',$data);

        $obj = new Category('','',$post_arraty[0]);
        if($post_arraty[1] == 'delete'){
            $return = $obj->deleteCategoryAll();
        }else {
           $return = $obj->deleteCategoryMove($post_arraty[2]);
        }
        //\print_r($return);
        return $return;
    }


    /***
     * 
     * gets list of users in category
     * 
     */

    static function getUsersList($data)
    {   global $USER,$DB;
        $category = $data;

        //user declates he is admin, so check it
        $is_admin = \explode('%',$category);
        if(\sizeof($is_admin) == 2){
                        
            $admins = $DB->get_field_sql('SELECT value FROM {config} WHERE name = ?',array('siteadmins'));
            $admins = \explode(',',$admins);
            if(!in_array($USER->id,$admins)){
                return false;
            }
            $obj = new User($USER->id);
            $obj->getAdminsUsers();
        }else{
         
            // user is not a admin and not trying to
            
            $post_array = \explode('&',$category);
            $is_admin = \explode('%',$category);
            $obj = new User($USER->id);
            $obj->getUserContextsAndRoles();
            $obj->getAllContexts();
            if(\sizeof($post_array) == 2){
                $obj->getUserChildUsers($post_array[0]);
                    
                    
                    
            }else {
                $obj->getUserChildUsers();
            }
        }
        $obj->childUsers = Processor::sortbyLastname($obj->childUsers);
        return $obj;
    }




    
    /**
     * 
     * gets course list
     * 
     */


    static function getCourseList($data)
    {   global $USER,$DB;
        //$post_arraty = \explode('&',$_POST['deleteCategory']);
        $category = $data;
        $post_array = \explode('&',$category);
        $admins = $DB->get_field_sql('SELECT value FROM {config} WHERE name = ?',array('siteadmins'));
        $admins = \explode(',',$admins);
        if(in_array($USER->id,$admins)){
        $is_admin = true;
        }
        
        if(\sizeof($post_array) == 2){
            $courses = Course::getCoursesListAll($post_array[0]);
        }elseif(\sizeof($post_array) == 3){
            if($is_admin == true){
                $courses = Course::getCoursesListAll(0);
            }else{
                return false;
            }
        }else {
            $courses = Course::getCoursesListInCategory($category);
        }
        $courses = Processor::sortbyFullname($courses);
        return $courses;
        
    }





     /**
     * 
     * gets course list
     * 
     */


    static function enrolUserCourse($data)
    { 
        //$post_arraty = \explode('&',$_POST['deleteCategory']);
        $post_array = \explode('&',$data);

        $user = new User($post_array[0]);
        $output = $user->enrolUser($post_array[1]);
        return $output;
    }


    
     /**
     * 
     * assign user category
     * 
     */


    static function assignUserCategory($data)
    {   global $DB;
        $output = new \stdClass();
        //$post_arraty = \explode('&',$_POST['deleteCategory']);
        $post_array = \explode('&',$data);
        $user = new User($post_array[0]);
        if(\sizeof($post_array) ==3){
            $roleid = $post_array[2];
        }else{
            $roleid = $DB->get_field_sql('SELECT value FROM {config_plugins} WHERE name = ? AND plugin = ?',array('default_role','local_my_organization'));
            //if not defaul role, assign role with archetype of student, that can be assigned to category
            if(!$roleid){
                $roleid = $DB->get_field_sql('SELECT id FROM {role} WHERE archetype = ? AND id IN (
                    SELECT id FROM {role_context_levels} where contextlevel = 40
                )'
                ,array('student'));  
            } 
            //if still no role - user 
            if(!$roleid){
                $roleid = $DB->get_field_sql('SELECT id FROM {role} WHERE archetype = ? AND id IN (
                    SELECT id FROM {role_context_levels} where contextlevel = 40
                )'
                ,array('user'));  
            } 
        }
        
        if($roleid){
           // $user->output = $user->setUserRole($post_array[1],$roleid);   
            $output = $user->setUserRole($post_array[1],$roleid);
            return $output;
        }else{

            $output->data = get_string('no_valid_role','local_my_organization');
            $output->type = 'negative';
            return $output;

        }
    }


    /***
     * 
     * gets user info
     * 
     */
    static function getUserInfo($data)
    { 
        $user = new User($data);
        $user->getUserContextsAndRoles();
        $user->getEnrolAndMyRights();
        $user->permisionOnUserCategories();
        //$post_arraty = \explode('&',$_POST['deleteCategory']);
    /*  $post_array = \explode('&',$_POST['assignUserCategory']);
        
        $output = $user->setUserRole($post_array[1],19); */
        
        return $user;
    }


    /*****
     * 
     * get info for course info window
     * 
     */

    static function getCourseInfo($data)
    {
        $course = new Course($_POST['getCourseInfo']);
        $course->getCourseInfo();
        $course->startdate = date('d.m.y H:i',$course->startdate);
        $course->enddate = date('d.m.y H:i',$course->enddate);
        return $course;
    }


    /***
     * 
     * get info for category info window 
     * 
     */

    static function updateCategory($data)
    {
        $category = new Category('','',$data);
        $category->getCategoryInfo();
        $category->getCategoryUsers();
    
        foreach ($category->roles as $key => $value) {
            $value->users = Processor::sortbyLastname($value->users);
        }
        
        return $category;
    }


    /****
     * 
     * get info for category info window  
     * 
     */
    static function removeRoleAssignment($data)
    {   global $DB;
        $sql = 'SELECT {role_assignments}.roleid,{role_assignments}.userid,{context}.instanceid AS categoryid
            FROM {role_assignments}
            INNER JOIN {context} ON {context}.id = {role_assignments}.contextid
            WHERE {role_assignments}.id = ?';
        $result = $DB->get_record_sql($sql,array($data));

        $user = new User($result->userid);
        $output = $user->unsetUserRole($result->categoryid,$result->roleid);
    
        
        return $output;
    }


    /****
     * 
     * unenroll user
     * 
     */

    static function unenrollUser($data)
    {
        $post_array = \explode('&',$data);
        $user = new User($post_array[0]);
        $output = $user->unenrolUser($post_array[1]);
        
        return $output;
    }
   


    /*****
     * 
     * updates user role in givven category
     * 
     */

    static function updateUserRole($data)
    {   
        $post_array = \explode('&',$data);
        $user = new User($post_array[0]);
        $output = $user->updateUserRole($post_array[1],$post_array[2]);
        return $output;
    }


    /*****
     * 
     * unasign user category based on userid, roleid and categoryid
     * 
     */

    static function unAsignUserRoleCategory($data)
    {   
        $post_array = \explode('&',$data);
        $user = new User($post_array[0]);
        $output = $user->unsetUserRole($post_array[2],$post_array[1]);
        return $output;
    }


    static function searchAll($data)
    {   global $USER;
        $user = new User($USER->id);
        $user->getUserContextsAndRoles();
        $user->getUserChildUsers();
        $user->searchForUsers($data);
        $user->searchForCategories($data);
        $user->searchForCourses($data);
        return $user;
    }

    static function sortbyLastname($object)
    {   
       
        usort($object, function($a, $b)
        {
            static $czechCharsS = array('Á', 'Č', 'Ď', 'É', 'Ě' , 'Ch' , 'Í', 'Ň', 'Ó', 'Ř', 'Š', 'Ť', 'Ú', 'Ů' , 'Ý', 'Ž', 'á', 'č', 'ď', 'é', 'ě' , 'ch' , 'í', 'ň', 'ó', 'ř', 'š', 'ť', 'ú', 'ů' , 'ý', 'ž');
            static $czechCharsR = array('AZ','CZ','DZ','EZ','EZZ','HZZZ','IZ','NZ','OZ','RZ','SZ','TZ','UZ','UZZ','YZ','ZZ','az','cz','dz','ez','ezz','hzzz','iz','nz','oz','rz','sz','tz','uz','uzz','yz','zz');
    
            $A = str_replace($czechCharsS, $czechCharsR, $a->{'lastname'});
            $B = str_replace($czechCharsS, $czechCharsR, $b->{'lastname'});
            return strnatcasecmp($A, $B);
            return strcmp($a->{'lastname'}, $b->{'lastname'});
        });
        return $object;
    }


    static function sortbyFullname($object)
    {   
       
        usort($object, function($a, $b)
        {
            static $czechCharsS = array('Á', 'Č', 'Ď', 'É', 'Ě' , 'Ch' , 'Í', 'Ň', 'Ó', 'Ř', 'Š', 'Ť', 'Ú', 'Ů' , 'Ý', 'Ž', 'á', 'č', 'ď', 'é', 'ě' , 'ch' , 'í', 'ň', 'ó', 'ř', 'š', 'ť', 'ú', 'ů' , 'ý', 'ž');
            static $czechCharsR = array('AZ','CZ','DZ','EZ','EZZ','HZZZ','IZ','NZ','OZ','RZ','SZ','TZ','UZ','UZZ','YZ','ZZ','az','cz','dz','ez','ezz','hzzz','iz','nz','oz','rz','sz','tz','uz','uzz','yz','zz');
    
            $A = str_replace($czechCharsS, $czechCharsR, $a->{'fullname'});
            $B = str_replace($czechCharsS, $czechCharsR, $b->{'fullname'});
            return strnatcasecmp($A, $B);
            return strcmp($a->{'fullname'}, $b->{'fullname'});
        });
        return $object;
    }
    

}