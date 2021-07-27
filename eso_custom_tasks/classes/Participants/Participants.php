<?php 
// This file is part of Moodle - http://moodle.org/
        //
        // Moodle is free software: you can redistribute it and/or modify
        // it under the terms of the GNU General Public License as published by
        // the Free Software Foundation, either version 3 of the License, or
        // (at your option) any later version.
        //
        // Moodle is distributed in the hope that it will be useful,
        // but WITHOUT ANY WARRANTY; without even the implied warranty of
        // MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
        // GNU General Public License for more details.
        //
        // You should have received a copy of the GNU General Public License
        // along with Moodle.  If not, see <http://www.gnu.org/licenses/>.
        
        
/** 
 * @package     local_eso_custom_tasks 
 * @copyright     Simon Zajicek
 * @copyright     IQdevelopment
 * @copyright     2021

*/

namespace local_eso_custom_tasks\Participants; 



defined ( 'MOODLE_INTERNAL' ) || die(); 
 
class Participants{

        /**
         * does the task number of users in course limit
         */
        static function getDifferencies()
        {       global $DB;
                $enrols = $DB->get_records_sql('SELECT * FROM {enrol} WHERE enrol = "self" AND courseid in (SELECT id FROM {course} WHERE enddate > ? AND category NOT IN (3,8,124,127,128)) ORDER BY id DESC',array(time()));
                $records = $DB->get_records_sql('SELECT * FROM {course_max_students}');
                foreach ($enrols as $enrol) {
                       
                        echo '<br><br>';
                        $check = FALSE;
                        foreach ($records as $record) {
                                if($enrol->courseid == $record->courseid){
                                        $check = Participants::CheckIfCount($enrol,$record);
                                }
                        }

                        //did not found it, creating record
                        if(!$check){
                                echo 'did not found it - creating for course: '.$enrol->courseid.'<br>'.PHP_EOL;
                                $sql = 'INSERT INTO {course_max_students} (courseid,maxstudents) VALUES (?,?)';
                                $DB->execute($sql,array($enrol->courseid,$enrol->customint3));
                        }

                        
                }

                
                # code...
        }


        static function CheckIfCount($enrol,$record)
        {       
                $user_array = Participants::enrolledStudentCount($enrol->courseid);
                if(($enrol->customint3 == $record->maxstudents AND $user_array->self == $user_array->students)
                        OR ($enrol->customint3 == 0 AND $record->maxstudents == 0)){
                        //
                        echo 'all good with course: '.$enrol->courseid.'<br>'.PHP_EOL;
                        //all good values matches and there is no manual enrolment
                        return TRUE;
                }else{
                        $user_array = Participants::enrolledStudentCount($enrol->courseid);
                        echo 'PROBLEM with course: '.$enrol->courseid .'<br>'.PHP_EOL;
                        $student_manual_enrolled_count = $user_array->students - $user_array->self;
                        if($enrol->customint3 == ($record->maxstudents - $student_manual_enrolled_count)){
                                echo 'Problem solved, there are some manually enrolled users, in course: '.$enrol->courseid . 'count is all good<br>'.PHP_EOL;
                        }else{
                                echo 'Problem not solved, there are some manually enrolled users, in course: '.$enrol->courseid.'<br>'.PHP_EOL;
                                Participants::makeuserAdjustment($enrol,$record,$user_array);
                        }
                        echo 'max students -'. $record->maxstudents;

                        return TRUE;
                      //  if($record->maxstudents == );

                }
        }




           /**
         * returns object with properties lectors - count of lectors, students- count of students, total - total count
         */
        static function enrolledStudentCount($courseid)
	{   global $DB;
		$returnObject = new \stdClass();
		$course_context = $DB->get_record_sql('SELECT id,path FROM {context} WHERE contextlevel = 50 AND instanceid = ?',array($courseid));
		$course_context_array = explode('/',$course_context->path);
		array_shift($course_context_array);
		
		$users = $DB->get_fieldset_sql('SELECT userid FROM {role_assignments} WHERE contextid = ? GROUP BY userid',array($course_context->id));
		$returnObject->total = sizeof($users);
		$usercount = 0;
                
		foreach ($users as $key => $userid) {
			# code...
			$roles = $DB->get_records_sql('SELECT * FROM {role_assignments} WHERE userid = ?',array($userid));
			foreach ($roles as $key => $role) {
				if(in_array($role->contextid,$course_context_array) AND $role->roleid != 5){
					$usercount++;
					break 1;
				}
			}
		}
		$returnObject->lectors = $usercount;
		$returnObject->students = sizeof($users)-$usercount;
                $returnObject->self = $DB->get_field_sql(
                        'SELECT count(id) FROM {user_enrolments} WHERE enrolid = (SELECT id FROM {enrol} WHERE enrol = "self" AND courseid = ?)',
                        array($courseid)
                );
		return $returnObject;

	}


        /**
         * does the max users valuation, if counts does not match
         */
        static function makeuserAdjustment($enrol,$record,$user_array)
        {       global $DB;
                $student_manual_enrolled_count = $user_array->students - $user_array->self;
                //echo 'enrol:' . $enrol->customint3;
                if($enrol->customint3 == 0){
                        $sql2 = 'UPDATE {course_max_students} SET maxstudents = ? WHERE courseid = ?'; 
                        $DB->execute($sql2,array(0,$enrol->courseid)); 
                        return '';   
                }


                if($enrol->customint3 > $user_array->students AND $enrol->customint3 != ($record->maxstudents - $student_manual_enrolled_count )){
                        echo 'setting up new user capacity'.'<br>'.PHP_EOL;
                        $update_sql = 'UPDATE {course_max_students} SET  maxstudents = ? WHERE courseid = ?';
                        $DB->execute($update_sql,array($enrol->customint3,$enrol->courseid));
                        $record->maxstudents = $enrol->customint3;
                }
                

                        if($user_array->students > $record->maxstudents){
                                echo 'There are more enrolled users than course capacity'.$enrol->courseid.'<br>'.PHP_EOL;
                                $sql = 'UPDATE {enrol} SET customint3 = ? WHERE id = ?';
                                $sql2 = 'UPDATE {course_max_students} SET maxstudents = ? WHERE courseid = ?'; 
                                $DB->execute($sql,array($user_array->students,$enrol->id));
                                $DB->execute($sql2,array($user_array->students,$enrol->courseid));

                        }else{
                                
                                if($student_manual_enrolled_count < 0){
                                        echo 'lektor se sam zapsal';
                                        Participants::lectorToManual($enrol->courseid);
                                }else{
                                        echo '<br>maly spatny' .$student_manual_enrolled_count. '='.$user_array->students.'-'.$user_array->self.'<br>'.PHP_EOL;
                                        $sql = 'UPDATE {enrol} SET customint3 = ? WHERE id = ?';
                                        $difference = $record->maxstudents - $student_manual_enrolled_count;
                                        $DB->execute($sql,array($difference,$enrol->id));
                                        echo '<br>solved for course ' .$enrol->courseid. '<br>'.PHP_EOL;
                                }
                        
                        }
                
                
        }


        /**
         * lector manual enrol assign
         */
        static function lectorToManual($courseid)
        {       global $DB;
                $context = $DB->get_field_sql('SELECT id FROM {context} WHERE contextlevel = 50 AND instanceid = ?',array($courseid));
                $sql = 'SELECT * FROM {role_assignments} WHERE contextid = ? AND userid IN (
                                SELECT userid FROM {user_enrolments} WHERE enrolid = 
                                        (
                                                SELECT id FROM {enrol} WHERE courseid = ? AND enrol="self"
                                        )
                        ) AND roleid != 5';
                $wrong_enrols = $DB->get_records_sql($sql,array($context,$courseid));
                
                if($wrong_enrols){
                        $sql = 'UPDATE {user_enrolments} SET enrolid = ? WHERE userid = ? AND enrolid = (SELECT id FROM {enrol} WHERE courseid = ? AND enrol = "self")';
                        foreach ($wrong_enrols as $enrol) {
                                $new_enrolid = $DB->get_field_sql('SELECT id FROM {enrol} WHERE courseid = ? AND enrol = "manual"',array($courseid));
                                $DB->execute($sql,array($new_enrolid,$enrol->userid,$courseid));
                                echo 'enrol was changed for user '.$enrol->userid. ' in course : '.$courseid .'<br>'.PHP_EOL; 
                        }
                }else{
                        $course_context = $DB->get_record_sql('SELECT id,path FROM {context} WHERE contextlevel = 50 AND instanceid = ?',array($courseid));
                        $course_context_array = explode('/',$course_context->path);
                        array_shift($course_context_array);

                        $sql = 'SELECT userid FROM {user_enrolments} WHERE enrolid = 
                                        (
                                                SELECT id FROM {enrol} WHERE courseid = ? AND enrol="self"
                                        )';
                        $users = $DB->get_fieldset_sql($sql,array($courseid));
                        $new_enrolid = $DB->get_field_sql('SELECT id FROM {enrol} WHERE courseid = ? AND enrol = "manual"',array($courseid));
                        foreach ($users as $key => $userid) {
                                # code...
                                $roles = $DB->get_records_sql('SELECT * FROM {role_assignments} WHERE userid = ?',array($userid));
                                foreach ($roles as $key => $role) {
                                        if(in_array($role->contextid,$course_context_array) AND $role->roleid != 5){
                                                $sql = 'UPDATE {user_enrolments} SET enrolid = ? WHERE userid = ? AND enrolid = (SELECT id FROM {enrol} WHERE courseid = ? AND enrol = "self")';
                                                $DB->execute($sql,array($new_enrolid,$userid,$courseid));
                                                echo 'enrol was changed for user '.$userid. ' in course : '.$courseid .'<br>'.PHP_EOL; 
                                        }
                                }
                        }

                }

                
        }



        /**
         * this leaves only one self enrol and users are moved in in for each course
         */

        static function doshit()
        {       global $DB;
                $waitlist = 'DELETE from {user_enrolments} WHERE enrolid IN (SELECT id from {enrol} WHERE enrol = "waitlist")';
                $delete_slq = 'DELETE from {enrol} WHERE enrol = "waitlist"';
                $DB->execute($waitlist);
                $DB->execute($delete_slq);
                $new_enrol_sql = 'UPDATE {enrol} SET name = NULL, customint3 = ?, status = 0 WHERE id = ?';
                $user_transfer_sql = 'UPDATE IGNORE {user_enrolments} SET enrolid = ? WHERE enrolid = ?';
                $enrol_remove_sql = 'DELETE FROM {enrol} WHERE id = ?';
                $final_clear_sql = 'DELETE from {user_enrolments} WHERE enrolid NOT IN (SELECT id FROM {enrol})';


               $courses = $DB->get_fieldset_sql('SELECT id FROM {course} WHERE category != 3');
               

               foreach ($courses as $courseid) {
                       echo '<br>course: '.$courseid.'<br>';
                       
                       $enrols = $DB->get_records_sql('SELECT * FROM {enrol} WHERE courseid = ? AND enrol = "self"',array($courseid));
                       $max_users = $DB->get_field_sql('SELECT value FROM {customfield_data} WHERE fieldid = 22 AND instanceid = ?',array($courseid));
                       $max_users = \intval($max_users);
                       //pokud je jen jeden self zapis
                       if(sizeof($enrols) == 1){
                               foreach ($enrols as $key => $enrol) {
                                echo 'there is only one enrol in course: '.$enrol->courseid.' going to do this<br>';
                               $DB->execute($new_enrol_sql,array($max_users,$enrol->id));
                               }
                               continue;
                              
                       } 

                       
                       //get and create the new central enroll
                       $enrol_to_stay = $DB->get_field_sql('SELECT min(id) FROM {enrol} WHERE enrol = "self" AND courseid = ?',array($courseid));
                        //$DB->excute($new_enrol_sql,array($max_users,$enrol_to_stay));
                        echo 'cental enrol is  '.$enrol_to_stay .'there are '.sizeof($enrols).' enrols users going to do this<br>';
                        foreach ($enrols as  $enrol) {
                                $enrol->users = $DB->get_field_sql('SELECT count(*) FROM {user_enrolments} WHERE enrolid = ?',array($enrol->id));
                                if($enrol->users != 0 AND $enrol->id != $enrol_to_stay){
                                        echo 'transferring '.$enrol->users.' enrolled users from '.$enrol->id .' to:'.$enrol_to_stay.'<br>';
                                        $DB->execute($user_transfer_sql,array($enrol_to_stay,$enrol->id));
                                        $DB->execute($enrol_remove_sql,array($enrol->id));
                                }elseif($enrol->id != $enrol_to_stay){
                                        echo 'nothing to transfer from '.$enrol->id .' to:'.$enrol_to_stay.' just deleting<br>';
                                        $DB->execute($enrol_remove_sql,array($enrol->id)); 
                                }
                               
                                
                        }
                        echo 'nothing to do<br>';

                      // print_r($enrols);

                       //echo '<br><br>';
               }

               $DB->execute($final_clear_sql);
        }

}