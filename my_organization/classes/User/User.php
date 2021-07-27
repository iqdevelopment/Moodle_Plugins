<?php
namespace local_my_organization\User;


class User{
    public $userid;

    public function __construct($userid = null)
    {
        $this->userid = $userid;
    }
    
    /*
        gets user roles and contexts given on the course category level (40)

    */
    

    public function getUserContextsAndRoles()
    {    global $DB;
        $sql = 'SELECT {role_assignments}.id AS roleassignid,
                {role_assignments}.roleid,
                {role}.name AS rolename,
                {role_assignments}.contextid,
                {course_categories}.id as categoryid,
                {course_categories}.name,
                {course_categories}.idnumber,
                {course_categories}.path,
                {role}.name as rolename
            FROM {role_assignments}
            INNER JOIN {context} ON {context}.id = {role_assignments}.contextid
            INNER JOIN {course_categories} ON {course_categories}.id = {context}.instanceid
            INNER JOIN {role} ON {role}.id = {role_assignments}.roleid
                 WHERE userid = ? AND contextid IN (
                    SELECT id FROM {context} WHERE contextlevel = 40
                )';
            
        $pre_contextArray = $DB->get_records_sql($sql,array($this->userid));

         foreach ($pre_contextArray as $key => $value) {
            $sql = 'SELECT permission FROM {role_capabilities} WHERE roleid = ? AND capability = ?';
            $value->canManageCategory = $DB->get_field_sql($sql,array($value->roleid,'moodle/category:manage'));
            $value->canEnrol = $DB->get_field_sql($sql,array($value->roleid,'enrol/manual:enrol'));
            $value->canUnEnrol = $DB->get_field_sql($sql,array($value->roleid,'enrol/manual:unenrol'));
            $value->canManageCourse = $DB->get_field_sql($sql,array($value->roleid,'moodle/course:update'));
            $value->canViewUsers = $DB->get_field_sql($sql,array($value->roleid,'moodle/user:viewalldetails'));

           
        } 

        $this->contextArray = $pre_contextArray;
        $this->getChildContexts();
        $this->getInfoInSubcategories();
    }


    /*
     * 
     * function to get list of all users user can control -> therefore they are defined in any of my context and subcontexts
     * return array of object where are id,firstname,lastname and email of users
     * 
     */

    public function getUserChildUsers($categoryid = null)
    {   global $DB;
        $this->getUserContextsAndRoles();
        $this->getAllContexts();
        
        if($categoryid == null){
            //no filter
            foreach ($this->allContexts as $key => $value) {
                $sql_pre_array[] = $value->id;
            }
            
        }else{
            foreach ($this->allContexts as $key => $value) {
                if(strpos($value->path, $categoryid) !== false){
                $sql_pre_array[] = $value->id;
                }
            }
            
        }

        $sql_array = implode(',',$sql_pre_array);
        if($sql_array){
            $sql = 'SELECT id,firstname,lastname,email,username FROM {user} WHERE id IN 
                    (
                        SELECT userid FROM {role_assignments} WHERE contextid IN ('.$sql_array.')
                    )
                AND id != 1
                GROUP BY id ORDER BY lastname ASC
                
            ';
            $users = $DB->get_records_sql($sql,array($this->userid));

        }else{
            $users = false;
        }
        $this->childUsers = $users;
    }


     /*
     * 
     * function to get list of all users this is strictly for administators
     * 
     */

    public function getAdminsUsers($categoryid = null)
    {   global $DB;

            $sql = 'SELECT id,firstname,lastname,email,username FROM {user} WHERE id != 1
                GROUP BY id ORDER BY lastname ASC ';
            $users = $DB->get_records_sql($sql,array($this->userid));
        $this->childUsers = $users;
    }



    /****
     * 
     * get all users contexts
     * 
     * 
     */
    public function getAllContexts()
    {
        $this->allContexts = $this->childContexts;
        foreach ($this->contextArray as $key => $value) {
            $value->id = $value->contextid;
            array_push($this->allContexts,$value);
        }
    }





    /**
     * 
     * 
     * this function returns array of contextid in subcategories of assigned categories
     * 
     */



    private function getChildContexts()
    {   global $DB;
        
        $result_array = array();
        foreach ($this->contextArray as $key => $value) {
            $path = $value->contextid;
            $sql = 'SELECT  {context}.id,{course_categories}.id AS categoryid,{course_categories}.name,{course_categories}.idnumber,{course_categories}.path
                        FROM {context}
                        INNER JOIN {course_categories} ON {course_categories}.id = {context}.instanceid
                        WHERE  {context}.contextlevel = 40 AND  {context}.path like "%/'.$path.'/%"';

            $child_context_array = $DB->get_records_sql($sql);
            $result_array = array_merge($result_array,$child_context_array);
        }
        $this->childContexts = $result_array;
    }


    /**
     * 
     * 
     * this function returns array of contextid in subcategories of assigned categories
     * 
     */



    public function getContextRoleCategory()
    {   global $DB;
        
        if(empty($this->contextArray)){
            $this->getUserContextsAndRoles();
        }

        $result_array = array();
        foreach ($this->contextArray as $key => $value) {
            $obj = new \stdClass();
            $obj->contextid = $value->contextid;
            $obj->categoryid = $value->categoryid;
            $obj->role = $value->roleid;
            $result_array[] = $obj;
            $path = $value->contextid;
            $sql = 'SELECT  {context}.id,{context}.instanceid
                        FROM {context}
                        INNER JOIN {course_categories} ON {course_categories}.id = {context}.instanceid
                        WHERE  {context}.contextlevel = 40 AND  {context}.path like "%/'.$value->contextid.'/%"';

            $child_context_array = $DB->get_records_sql($sql);
                foreach ($child_context_array as $child_context) {
                    $obj = new \stdClass();
                    $obj->contextid = $child_context->id;
                    $obj->categoryid = $child_context->instanceid;
                    $obj->role = $value->roleid;
                    $result_array[] = $obj;
                }
            
        }
        $this->contextRoleCategory = $result_array;
    }




    /**
     * 
     * function to get courses and status of individual user, within the USER camabality and total numbers
     * private function only
     * 
     * 
     */

     private function getUserEnrolments($user,$category_array_string)
     {  global $DB;
        
       
        $sql = 'SELECT
                    {course_completions}.id,
                    {enrol}.id AS enrolid,
                    {course}.id AS courseid,
                    {course}.category AS categoryid,
                    {course_completions}.timecompleted 
                FROM {user_enrolments}
                    INNER JOIN {enrol} on {enrol}.id = {user_enrolments}.enrolid
                    INNER JOIN {course} ON {course}.id = {enrol}.courseid
                    INNER JOIN {course_completions} on {course_completions}.course = {course}.id

                WHERE {course}.category IN '.$category_array_string.'
                    AND {user_enrolments}.userid = ?
                    AND {course_completions}.userid = ?
                    
                    ';
        $sql_total = 'SELECT
                    {course_completions}.id,
                    {enrol}.id AS enrolid,
                    {course}.id AS courseid,
                    {course}.category AS categoryid,
                    {course_completions}.timecompleted 
                FROM {user_enrolments}
                    INNER JOIN {enrol} on {enrol}.id = {user_enrolments}.enrolid
                    INNER JOIN {course} ON {course}.id = {enrol}.courseid
                    INNER JOIN {course_completions} on {course_completions}.course = {course}.id

                WHERE {user_enrolments}.userid = ?
                    AND {course_completions}.userid = ?
                    
                    ';
        

        $return =  $DB->get_records_sql($sql,array($user->id,$user->id));
        $return_total =  $DB->get_records_sql($sql_total,array($user->id,$user->id));  
        $this->childUsers[$user->id]->totalCoursesCount = sizeof($return_total);
        $this->childUsers[$user->id]->totalCourses = $return_total;
        $this->childUsers[$user->id]->coursesCount = sizeof($return);
        $this->childUsers[$user->id]->courses = $return;
        return $return;
        
     }




     /*
      *
        just optimalized function to add user courses to objects in $this->childUsers
      * 
      */


    public function getChildUsersEnrolments()
    {   $category_array = \local_my_organization\Category\Category::getCategoryChildrenIds($this->contextArray);
        $category_array_string = '('.implode(',',$category_array).')';
        foreach ($this->childUsers as $key => $user) {
            $this->getUserEnrolments($user,$category_array_string);
        }
    }



    /******
     * 
     * set role to user
     * 
     * 
     */


    public function setUserRole($categoryid,$role_id)
    {   global $DB,$USER;
        $output = new \stdClass();
        $message = $this->checkInputsRoleCRUD($role_id,$categoryid);
 
        $message_check = intval($message);
        if(!$message_check){
            $output->type = 'negative';
            $output->data = $message;  
            
            return $output;
        }
        $contextid = $message_check;
        //it seems the is no problem, co do the assignment
        $sql = 'INSERT INTO {role_assignments} (roleid,contextid,userid,timemodified,modifierid,component,itemid,sortorder) values (?,?,?,?,?,"",0,0)';
        $DB->execute($sql,array($role_id,$contextid,$this->userid,time(),$USER->id));
        $this->setUserCohort($contextid);
        $message = get_string('user_role_assigned','local_my_organization');
        $output->data = $message;  
        $output->type = 'positive';
        return $output;

    }



    /******
     * 
     * delete user role
     * 
     * 
     */

    public function unsetUserRole($categoryid,$role_id)
    {   global $DB,$USER;
        $output = new \stdClass();
        $contextid = $DB->get_field_sql('SELECT id FROM {context} WHERE instanceid = ? AND contextlevel = 40',array($categoryid));
        //$message = $this->checkInputsRoleCRUD($role_id,$categoryid);
        if(!$contextid){
            $output->type = 'negative';
            $output->data = 'context does not exist'; 
            return $output;
        }
        //it seems the is no problem, do the unassignment
        $sql = 'DELETE FROM {role_assignments} WHERE contextid = ? AND userid = ?';
        $DB->execute($sql,array($contextid,$this->userid));
        $this->unsetUserCohort($contextid);
        $message = get_string('user_role_unassigned','local_my_organization');
        $output->type = 'positive';
        $output->data = $message;  
        return $output;
    }
    


    /**
     * 
     * function to check if the role has more capabilities or not
     * 
     */

    private function checkRole($check,$roleid)
    {   global $DB;
       $current = $this->roleComparision($check->roleid);
       $new = $this->roleComparision($roleid);
       if($roleid == $check->roleid){
           return 'same';
       } 
       
       if($current < $new){
            return TRUE;
       }else {
           return FALSE;
       }
    }

    /******
     * 
     * 
     * 
     * 
     */

     private function checkInputsRoleCRUD($roleid,$categoryid)
     {  global $DB;
        //role exists?
        $roleid = $DB->get_field_sql('SELECT id FROM {role} WHERE id = ?',array($roleid));
        if(!$roleid){
            $message = get_string('invalid_role_id','local_my_organization');
            return $message; 
        }
        
        //context exists?
        $contextid = $DB->get_field_sql('SELECT id FROM {context} WHERE instanceid = ? AND contextlevel = 40',array($categoryid));
        if(!$contextid){
            $message = get_string('invalid_category_id','local_my_organization');
            return $message; 
        }
        // can be new role assigned in category context?
        $inial_check = $DB->get_record_sql('SELECT * FROM {role_context_levels} WHERE roleid = ? AND contextlevel = 40',array($roleid));
        if(!$inial_check){
            $message = get_string('role_canot_be_assigned','local_my_organization');
            return $message; 
        }
        // is there any other role assignemnts in this category?
        $check = $DB->get_record_sql('SELECT * FROM {role_assignments} WHERE contextid = ? AND userid = ?',array($contextid,$this->userid));
        if($check){
           $true_false = $this->checkRole($check,$roleid);
           // print_r($true_false);
           if($true_false === 'same'){
            $message = get_string('user_is_in_this_contex_same','local_my_organization');
           }elseif($true_false === FALSE) {
            $message = get_string('user_is_in_this_contex_allready_smaller','local_my_organization');
           }else{
            $message = get_string('user_is_in_this_contex_allready_bigger','local_my_organization');  
           }
            
            return $message;  
        }
        return $contextid;
     }



    /******
     * 
     * 
     * just count permission to declare what role has bigger capabalities
     * 
     * 
     */
    private function roleComparision($roleid)
    {   global $DB;
        $summ = 0;
        $sql = 'SELECT permission FROM {role_capabilities} WHERE roleid = ?';
        $array = $DB->get_fieldset_sql($sql,array($roleid));
        foreach ($array as $key => $value) {
            if($value < -3){continue;}
            $summ +=  \intval($value)+1;
        }
        return $summ;
    }

    /*****
     * 
     * 
     * after returnin statement of different role capabalities, this allows to force this desizion
     * 
     */

    public function updateUserRole($categoryid,$roleid)
    {   global $DB,$USER;
        $output = new \stdClass();
        //$roleid = $DB->get_field_sql('SELECT id FROM {role} WHERE shortname = ?',array($role_short_name));
        $contextid = $DB->get_field_sql('SELECT id FROM {context} WHERE instanceid = ? AND contextlevel = 40',array($categoryid));
        $sql = 'UPDATE {role_assignments}
                        SET roleid = ?,
                            timemodified = ?,
                            modifierid = ? 
                            WHERE contextid = ? AND userid = ?';
        $DB->execute($sql,array($roleid,time(),$USER->id,$contextid,$this->userid));
        $output->type = 'positive';
        $message = get_string('user_role_assigned','local_my_organization');
        $output->data = $message; 
        return $output;
    }



    /***
     * 
     * function to enrol user by given only user object, courseid and optionally role (default is student with ID 5)
     * 
     */


    public function enrolUser($course,$roleid = 5)
    {   global $CFG, $DB;
        $output = new \stdClass();
        $this->userProfile();
        if($this->isEnrolled($course)){
            $output->data = get_string('user','local_my_organization').$this->firstname.' '.$this->lastname .get_string('user_enroled_already','local_my_organization');  
            $output->type = 'neutral';
            return $output;
        }
       
        $instance = $DB->get_record_sql('SELECT * FROM {enrol} WHERE enrol = "manual" AND courseid = ? AND status = 0',array($course));
        $enrol = enrol_get_plugin('manual');
                try {
            $enrol->enrol_user($instance, $this->userid, $roleid, );

        } catch (Exception $e) {
           // throw new mnet_server_exception(5019, 'couldnotenrol', 'enrol_mnet', $e->getMessage());
            $output->data = mnet_server_exception(5019, 'couldnotenrol', 'enrol_mnet', $e->getMessage());
            $output->type = 'negative';
            return $output;
        }
        
        $output->data = get_string('user','local_my_organization').$this->firstname.' '.$this->lastname .get_string('user_enroled','local_my_organization'); 
        $output->type = 'positive';
        return $output;
    }


    
    /***
     * 
     * function to unenrol user by given only user object, courseid 
     * it will query the DB to see how is user enrolled and unenroll it
     *       
     */


    public function unenrolUser($courseid)
    {   global $CFG, $DB;
        $output = new \stdClass();
        $this->userProfile();
        $sql = 'SELECT * FROM {enrol} WHERE id IN (
                SELECT enrolid FROM {user_enrolments} WHERE userid = ?
            ) AND courseid = ?
        ';
        $instance = $DB->get_record_sql($sql,array($this->userid,$courseid));
        if(empty($instance)){
            $output->data =get_string('user','local_my_organization').$this->email.get_string('not_enrolled','local_my_organization');
            $output->type = 'neutral';
            return $output;

        }
        $enrol = enrol_get_plugin($instance->enrol);
                try {
            $enrol->unenrol_user($instance, $this->userid);

        } catch (Exception $e) {
            $output->data = mnet_server_exception(5019, 'couldnotenrol', 'enrol_mnet', $e->getMessage());
            $output->type = 'negative';
            return $output;
           // throw new mnet_server_exception(5019, 'couldnotenrol', 'enrol_mnet', $e->getMessage());
            
        }
        $output->data = get_string('user','local_my_organization').$this->email.get_string('user_unenroled','local_my_organization');
        $output->type = 'positive';
        return $output;
       // return get_string('user','local_my_organization').$this->email.get_string('user_unenroled','local_my_organization');
    }

    /*****
     * 
     * function to check give array of object with courses that user can be assigned to
     * 
     */

     public function allowedCoursesObjects()
     {  global $DB;
        //$sql_array = implode(',',$this->contextToCategories());
        $sql_array = '('.implode(',',$this->contextToCategories()).')';
        $sql = 'SELECT * FROM {course} WHERE category IN '.$sql_array.'';
        $this->allowedCourses = $DB->get_records_sql($sql);
     }



     /**
      * 
      *
      *transforms contexts into categories
      * returns array of categories
      * 
     */
    private function contextToCategories()
    {
        global $DB;
        if(empty($this->allContexts)){
            $this->getAllContexts();
        }
        $sql_array = '('.implode(',',$this->allContexts).')';
        $sql = 'SELECT instanceid FROM {context} WHERE id IN '.$sql_array.'';
        $category_array = $DB->get_fieldset_sql($sql);
        return $category_array;
    }


    /*****
     * 
     * 
     * function to tell if user can access this course
     * 
     */

    public function canUserAcees($courseid)
    {   global $DB;
        if(empty($this->allowedCourses)){
            $this->allowedCoursesObjects();
        }
        foreach ($this->allowedCourses as $key => $value) {
            if($value->id == $courseid){
                return 1;
            }
        }
        return 0;


    }


    public static function taskCohortsToAssignments()
    {   global $DB;
         
                //this crazy ass query filters all users FROM role_assigments, who are not enrolled in cohorts with the same context
        $sql_role_assignments = 'SELECT id,roleid,contextid,userid FROM {role_assignments}
                                    WHERE (userid,contextid) NOT IN 
                                        (
                                            SELECT {cohort_members}.userid,{context}.id as contextid
                                            FROM {cohort_members}
                                            INNER JOIN {cohort} ON {cohort}.id = {cohort_members}.cohortid
                                            INNER JOIN {course_categories} ON {course_categories}.idnumber = {cohort}.idnumber
                                            INNER JOIN {context} ON {context}.instanceid = {course_categories}.id 
                                                WHERE  {context}.contextlevel = 40
                                        )

                                        AND contextid IN (SELECT id FROM {context} WHERE contextlevel = 40)';
                        
         //this crazy ass query filters all users FROM cohort_members, who are not  in role_assigments with the same context
         $sql_cohort_members = 'SELECT {cohort}_members.userid,{cohort}.contextid,{course_categories}.id AS categoryid 
                                FROM {cohort}
                                INNER JOIN {cohort_members} ON {cohort_members}.cohortid = {cohort}.id
                                INNER JOIN {course_categories} ON {course_categories}.idnumber = {cohort}.idnumber
                                WHERE {cohort}.component = ?
                                AND (userid,contextid) NOT IN 
                                    (
                                        SELECT userid,contextid FROM {role_assignments} WHERE contextid IN (SELECT id FROM {context} WHERE contextlevel = 40)
                                    )';

        $sql_users_without_role = 'SELECT id FROM {user} WHERE id NOT IN (SELECT userid FROM {role_assignments} GROUP BY userid)';


        //execution of SQL givven up
        $users_in_role_assigments_but_not_in_cohort_members = $DB->get_records_sql($sql_role_assignments,array('local_my_organization'));
        $users_in_cohort_members_but_not_in_role_assigments = $DB->get_records_sql($sql_cohort_members,array('local_my_organization'));
        $users_totally_without_role = $DB->get_fieldset_sql($sql_users_without_role,array('local_my_organization'));



        //users from role assigments to cohort members is something allways needs to be done
        foreach ($users_in_role_assigments_but_not_in_cohort_members as $user) {
            $user_obj = new User($user->userid);
            $user_obj->setUserCohort($user->contextid);
            echo 'cohort assig: user:'. $user_obj->userid . ' set : context '.$user->contextid. PHP_EOL;
            print_r($user);
            echo  PHP_EOL . PHP_EOL;

        }
        
        
        
        //users from cohort to role assigments is something to do only if default role is set (otherwise script wont khow the role)
        //
        $test_cohorts_to_role_assigments = $DB->get_field_sql('SELECT value FROM {config_plugins} WHERE name = ? AND plugin = ?',array('default_role','local_my_organization'));
               if($test_cohorts_to_role_assigments > -1){
                $users_in_cohort_members_but_not_in_role_assigments;

                        foreach ($users_in_cohort_members_but_not_in_role_assigments as $user) {
                            $user_obj = new User($user->userid);
                            $role_id = $DB->get_field_sql('SELECT value FROM {config_plugins} WHERE name = ? AND plugin = ?',array('default_role','local_my_organization'));
                            $user_obj->setUserRole($user->categoryid,$role_id);
                            echo ' role assig: user'. $user_obj->id . ' set : category '.$user->categoryid;
                        }
               }

        // users without role assigned is something to do, only if default role and defualt category is set
        $test_users_without_any_role_assignt_all = $DB->get_field_sql('SELECT value FROM {config_plugins} WHERE name = ? AND plugin = ?',array('default_category','local_my_organization'));
        $test = $DB->get_field_sql('SELECT id FROM {course_categories} WHERE id = ?',array($test_users_without_any_role_assignt_all));
        if($test){
            if($test_cohorts_to_role_assigments > -1 AND $test_users_without_any_role_assignt_all > -1){
                echo 'role-less users -> cohorts & roles'. PHP_EOL;
              //  print_r($users_totally_without_role);
              $category_id = $DB->get_field_sql('SELECT value FROM {config_plugins} WHERE name = ? AND plugin = ?',array('default_category','local_my_organization'));
              $role_id = $DB->get_field_sql('SELECT value FROM {config_plugins} WHERE name = ? AND plugin = ?',array('default_role','local_my_organization'));
                foreach ($users_totally_without_role as $user) {
                    $user_obj = new User($user);
                    $return = $user_obj->setUserRole($category_id,$role_id);
                    echo ' role and cohort assig: user'. $user . ' set : category '.$category_id . PHP_EOL;
                    
                }
            }
        }else{
            echo 'the default category does not exist, make this right in the local_my_organzation setting'. PHP_EOL;
        }


       

    }

    

    private function setUserCohort($contextid)
    {   global $DB;
        $cohort_id_sql = 'SELECT id FROM {cohort} WHERE idnumber = (SELECT idnumber FROM {course_categories} WHERE id = (SELECT instanceid FROM {context} WHERE id = ?))';
        //$cohort_id = $DB->get_field_sql('SELECT id FROM {cohort} WHERE contextid = ?',array($contextid));
        $cohort_id = $DB->get_field_sql($cohort_id_sql,array($contextid));
        $sql = 'INSERT INTO {cohort_members} (cohortid,userid,timeadded) values (?,?,?)';
        $check = $DB->get_record_sql('SELECT * FROM {cohort_members} WHERE cohortid = ? AND userid = ?',array( $cohort_id,$this->userid ) );
        if($check){
            return 0;
        }
        $DB->execute( $sql,array ( $cohort_id,$this->userid,time() ) );
        return 1;
        
    }

    private function unsetUserCohort($contextid)
    {   global $DB;
        $cohort_id = $DB->get_field_sql('SELECT id FROM {cohort} WHERE contextid = ? AND component = ?',array($contextid,'local_my_organization'));
        $sql = 'DELETE FROM {cohort_members} WHERE cohortid = ? and userid = ?';
        $DB->execute($sql,array($cohort_id,$this->userid,));
        return 1;
        
    }

    /****
     * 
     * gets addional info about categories and subcategories assigned to user
     * 
     */

    private function getInfoInSubcategories(){
        foreach ($this->contextArray as $context){
           $count = 0;
           $count = $count + $this->numberOfCourses($context->categoryid);
           $context->coursesInCategory = $count;
           $count = $count + $this->subcategoryToCategory($context);

            $context->coursesAll = $count;
        }
    }

    /****
     * 
     * small function that uses $this->numberOfCourses on category children
     * 
     */

    private function subcategoryToCategory($context)
    {   global $DB;
        $count = 0;
        foreach($this->childContexts as $child){
            if(strpos($child->path, $context->categoryid) !== false){
                $count = $count + $this->numberOfCourses($child->categoryid);
            }else{
                continue;
            }
        }
        return $count;
    }

    /**
     * 
     * function to get number of courses in given categoryid
     * 
     */

    private function numberOfCourses($categoryid)
    {   global $DB;
        return $DB->get_field_sql('SELECT count(*) FROM {course} WHERE category = ?',array($categoryid));
    }


    /******
     * 
     * get profile info about user
     * 
     * 
     */
    private function userProfile()
    {   global $DB;
        $obj = $DB->get_record_sql('SELECT firstname,lastname,username,email FROM {user} WHERE id = ?',array($this->userid));
        $this->firstname = $obj->firstname;
        $this->lastname = $obj->lastname;
        $this->email = $obj->email;
        $this->username = $obj->username;
    }


    private function isEnrolled($courseid)
    {   global $DB;
        $sql = 'SELECT * FROM {user_enrolments} WHERE userid = ? AND enrolid IN (SELECT id FROM {enrol} WHERE courseid = ?)';
        $result = $DB->get_record_sql($sql,array($this->userid,$courseid));
        if($result){
            return TRUE;
        }else{
            return FALSE;
        }
    }

    /**
     * 
     * method to get list of user enrolled courses
     * 
     */

     public function getEnrolAndMyRights()
     {  global $DB,$USER;
        $this->userProfile();
        $sql = 'SELECT {user_enrolments}.enrolid,{course}.id,{course}.fullname,{course}.startdate,{course}.enddate,{role}.name AS rolename,{course}.category
                FROM {course} 
                INNER JOIN {enrol} ON  {enrol}.courseid = {course}.id
                INNER JOIN {user_enrolments} ON {user_enrolments}.enrolid = {enrol}.id
                INNER JOIN {context} ON {context}.instanceid = {course}.id
                INNER JOIN {role_assignments} ON {role_assignments}.contextid = {context}.id
                INNER JOIN {role} ON {role}.id = {role_assignments}.roleid
                WHERE {user_enrolments}.userid = ? AND {context}.contextlevel = 50
                GROUP BY {user_enrolments}.enrolid';
        $this->courses = $DB->get_records_sql($sql,array($this->userid));
        $this->permisionOnUserCourses();
     }

     /*
    **
      *
      confirm, if user can edit enrolment of this particular course of this particular user (course can be enrolled in different category and User editing can have no power over this)
      also i will add to this function role confirmation 
      */

     private function permisionOnUserCourses()
     {  global $USER,$DB;
        $current_user = new User($USER->id);
        $current_user->getUserContextsAndRoles();
        foreach ($current_user->contextArray as $value) {
            $categories_of_current_user[] = $value->categoryid;
        }
        foreach ($current_user->childContexts as $value) {
            $categories_of_current_user[] = $value->categoryid;
        }
       
        foreach ($this->courses as $course) {
            if(in_array($course->category,$categories_of_current_user) OR User::isAdmin() ){
                $course->canedit = TRUE;
            }else{
                $course->canedit = FALSE;
            }
        }
     }

     /**
      * 
      *same but for the categories
      * 
      */


     public function permisionOnUserCategories()
     {  global $USER,$DB;
        $current_user = new User($USER->id);
        $current_user->getUserContextsAndRoles();

        foreach ($current_user->contextArray as $value) {
            $categories_of_current_user[] = $value->categoryid;
        }

        foreach ($current_user->childContexts as $value) {
            $categories_of_current_user[] = $value->categoryid;
        }

       
        foreach ($this->contextArray as $context) {
            if(in_array($context->categoryid,$categories_of_current_user) OR User::isAdmin()){
                $context->canedit = TRUE;
            }else{
                $context->canedit = FALSE;
            }
        }
         
     }


     /*
      *
      function to search for givven string in users which are in users rights 
      *
      */

      public function searchForUsers($searchString)
      { global $DB;
        $user_id_array = array();
        foreach ($this->childUsers as $key => $value) {
            $user_id_array[] = $value->id;
        }
        $user_id_array = \implode(',',$user_id_array);
            $sql = 'SELECT id,firstname,lastname,email,username FROM {user} 
                WHERE 
                (
                    firstname like "%'.$searchString.'%"
                    OR lastname like "%'.$searchString.'%"
                    OR email like "%'.$searchString.'%"
                    OR username like "%'.$searchString.'%"
                )
                AND id IN ('.$user_id_array.')                
                ';
            $this->foundUsers = $DB->get_records_sql($sql);
      }

      /*
      *
      function to search for givven string in courses which are in users rights 
      *
      */

      public function searchForCourses($searchString)
      { global $DB;
        $parent_id_array = array();
        foreach ($this->allContexts as $key => $value) {
            $parent_id_array[] = $value->categoryid;
        }
        $parent_id_array = \implode(',',$parent_id_array);
            $sql = 'SELECT id,fullname,shortname,startdate,enddate FROM {course} 
                WHERE 
                (
                    fullname like "%'.$searchString.'%"
                   
                )
                AND category IN ('.$parent_id_array.')                
                ';
            $this->foundCourses = $DB->get_records_sql($sql);
          //  $this->foundCourses = $parent_id_array;
      }

      /*
      *
      function to search for givven string in categories which are in users rights 
      *
      */

      public function searchForCategories($searchString)
      { global $DB,$USER;
        $user_id_array = array();
        foreach ($this->allContexts as $key => $value) {
            $category_id_array[] = $value->categoryid;
        }
        $category_id_array = \implode(',',$category_id_array);
        $sql = 'SELECT {role_assignments}.id AS roleassignid,
        {role_assignments}.roleid,
        {role}.name AS rolename,
        {role_assignments}.contextid,
        {course_categories}.id as categoryid,
        {course_categories}.name,
        {course_categories}.idnumber,
        {course_categories}.path,
        {role}.name as rolename
    FROM {role_assignments}
    INNER JOIN {context} ON {context}.id = {role_assignments}.contextid
    INNER JOIN {course_categories} ON {course_categories}.id = {context}.instanceid
    INNER JOIN {role} ON {role}.id = {role_assignments}.roleid
         WHERE userid = ? AND contextid IN (
            SELECT id FROM {context} WHERE contextlevel = 40
        )
        AND ({course_categories} .name like "%'.$searchString.'%"
            OR {course_categories}.idnumber like "%'.$searchString.'%" )
        AND {course_categories}.id IN ('.$category_id_array.') ';
            $this->contextArray = $DB->get_records_sql($sql,array($USER->id));
            $this->getChildContexts();
            $this->getInfoInSubcategories();
      }



      static function isAdmin()
      {     global $DB,$USER;
          $admins = $DB->get_field_sql('SELECT value FROM {config} WHERE name = ?',array('siteadmins'));
            $admins = \explode(',',$admins);
            if(in_array($USER->id,$admins)){
                return TRUE;
            }else{
                return FALSE;
            }
                
    }


    static function isCapable()
    {   global $DB,$USER;
        if(User::isAdmin()){
            return TRUE;
        }

        $user = new User($USER->id);
        $user->getUserContextsAndRoles();
      //  \print_r($user);
        if(empty($user->contextArray)){
            return FALSE;
        }else{
            foreach ($user->contextArray as $key => $value) {
                if($value->roleid != 5){
                    return TRUE;
                }
            }
        }
        return FALSE;
    }





}