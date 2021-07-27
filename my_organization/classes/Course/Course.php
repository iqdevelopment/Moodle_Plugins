<?php
namespace local_my_organization\Course;
use local_my_organization\Category\Category;
use local_my_organization\User\User;
use local_my_organization\Processor\Processor;

class Course{

    public $shortname;
    public $fullname;
    public $id;
    public $startdate;
    public $enddate;
    public $summary;
    public $category;
    public $users;

    public function __construct($courseid = null)
    {
        $this->courseid = $courseid;
    }

    /****
     * 
     * static funcion to just generate list of courses in givven categoryid
     * 
     */

    static public function getCoursesListInCategory($categoryid)
    {   global $DB;
        $courses = $DB->get_records_sql('SELECT id,shortname,fullname,category,startdate,enddate FROM {course} WHERE category = ? ORDER BY startdate',array($categoryid));
        foreach ($courses as $key => $course) {
            $course->startdate = date('d.m.Y H:i',$course->startdate);
            $course->enddate = date('d.m.Y H:i',$course->enddate);
        }
        return $courses;
    }

    /**
     * 
     * static funcion to generate list of courses in givven category id and all subcategories
     * 
     */

    static public function getCoursesListAll($categoryid)
    {
        global $DB;
        if($categoryid == 0){
            $sql ='SELECT id,shortname,fullname,category,startdate,enddate FROM {course}';
        }else{

            $sql ='SELECT id,shortname,fullname,category,startdate,enddate FROM {course} WHERE category IN 
                    (
                        SELECT id FROM {course_categories} WHERE path like "%/'.$categoryid.'/%" OR id = ?
                    )
                    ORDER BY startdate
            ';
        }
        $courses = $DB->get_records_sql($sql,array($categoryid));
        foreach ($courses as $key => $course) {
            $course->startdate = date('d.m.Y H:i',$course->startdate);
            $course->enddate = date('d.m.Y H:i',$course->enddate);
        }
        return $courses;
    }


    /**
     * 
     * get basic info of course
     * 
     */

    public function getCourseInfo()
    {   global $DB;
        
        $info = $DB->get_record_sql('SELECT id,shortname,fullname,startdate,enddate,summary,category FROM {course} WHERE id = ?',array($this->courseid));
        
       $this->shortname = $info->shortname;
       $this->fullname = $info->fullname;
       $this->id = $info->id;
       $this->startdate = $info->startdate;
       $this->enddate = $info->enddate;
       $this->summary = $info->summary;
       $this->category = $info->category;
       $this->users = $this->getCourseUsers();
       $this->permisionOnUserCourse();
    }


    /*****
     * 
     * 
     * get info about users in course
     */

     private function getCourseUsers()
     {  global $DB;
        $sql = 'SELECT {user}.id,{user}.firstname,{user}.lastname,{user}.email,{user}.username,{user_enrolments}.enrolid,{role}.name AS rolename
                FROM {user}
                    INNER JOIN {user_enrolments} ON {user_enrolments}.userid = {user}.id
                    INNER JOIN {enrol} ON {enrol}.id = {user_enrolments}.enrolid
                    INNER JOIN {role_assignments} ON {role_assignments}.userid = {user}.id
                    INNER JOIN {role} ON {role}.id = {role_assignments}.roleid
                WHERE {enrol}.courseid = ?
                AND {role_assignments}.contextid = 
                    (
                        SELECT id FROM {context} WHERE contextlevel = 50 AND instanceid = ?  
                    )
                    GROUP BY {user}.id
                    ORDER BY {user}.lastname';

        $users = $DB->get_records_sql($sql,array($this->id,$this->id));
        $users = Processor::sortbyLastname($users);
        $users = $this->superseedByHigherRights($users);
    /*     usort($users, function($a, $b)
            {
                return strcmp($a->lastname, $b->lastname);
            }); */
        return $users;
     }


     /*****
      * 
      check if there is a higher rights for this user
      */

      private function superseedByHigherRights($users)
      {     global $DB;
            $context = $DB->get_record_sql("SELECT * FROM mdl_context WHERE contextlevel=50 AND instanceid = ".$this->id."");
            $path = explode('/',$context->path);
            $path_new = $path;
            foreach ($path as $key => $value) {
                array_pop($path_new);
                $test_string = implode('/',$path_new);
                $check_context =  $DB->get_field_sql('SELECT id FROM {context} WHERE path = ?',array($test_string));
                $results =  $DB->get_fieldset_sql('SELECT userid FROM {role_assignments} WHERE contextid = ? AND roleid != ?',array($check_context,5));
                foreach ($users as $key => $user) {
                    if(\in_array($user->id,$results)){
                        $user->rolename = 'lektor'; 
                    }
                }
                
            }
            return $users;
      }


     /**
      * 
      * get rigts if i can even manage this enrolments
      *
      */

    private function permisionOnUserCourse()
    {   global $DB,$USER;
        // there should be only check of user rights if he can manage users
        $current_user = new User($USER->id);
        $current_user->getUserContextsAndRoles();
        
        foreach ($this->users as $user) {
            $user->canedit = TRUE;
        }
        
    }



    









}