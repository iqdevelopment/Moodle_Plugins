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

namespace local_eso_custom_tasks\General; 



defined ( 'MOODLE_INTERNAL' ) || die(); 
 
class General { 
        






        static function courseCompletionEmail()
        {       global $DB;
                global $DB,$CFG; 

                $completionids = $DB->get_fieldset_sql("SELECT id FROM mdl_course_completions WHERE timecompleted > 0 AND mailsent IS NULL LIMIT 30");
                print_r($completionids);
                //$completionids = $DB->get_fieldset_sql("SELECT id FROM mdl_course_completions WHERE timecompleted > 0");
                foreach ($completionids as $completionid){
                $message = "";
                $record = $DB->get_record('course_completions',array('id'=>$completionid),'id,userid,course,timecompleted,mailsent');
                $coursename = $DB->get_field_sql("SELECT fullname FROM mdl_course WHERE id = ".$record->course."");
                $courseid = $record->course;  
                $iid = $record->id;
                $user = $DB->get_record('user',array('id'=>$record->userid),'*');
                $role = $DB->get_field_sql("SELECT data FROM mdl_user_info_data WHERE userid = ".$user->id." AND fieldid = 18");
                if ($role == "Úředník" OR $role = "Vedoucí Úředník"){
                        $badge = "Osvědčení";
                } 
                else {$badge = "Certifikát";}
                $course = $DB->get_record('user',array('id'=>$record->course),'*');
                $timecompleted = $record->timecompleted;
                $mailstate =  $record->mailsent;
                $certurl = $DB->get_field_sql("SELECT id FROM mdl_course_modules WHERE course = '".$course->id."' AND module = 23");
                $certurl = "$CFG->wwwroot/mod/simplecertificate/view.php?id=$certurl";
                $cert = "<a href='".$certurl."'>".$badge."</a>";
                $feedback = $DB->get_field_sql("SELECT id FROM mdl_course_modules WHERE course = '".$course->id."' AND module = 7");
                if ($feedback != ""){
                        $feedback = "$CFG->wwwroot/mod/feedback/view.php?id=$feedback";
                        $feedback = "Rádi bychom Vás tímto poprosili o vyplnění <a href='".$feedback."'> zpětné vazby </a> na stránkách kurzu.<br>";
                }else {$feedback ="";}

                $message .= "Vážená paní, vážený pane,<br>";
                $message .= "gratulujeme Vám ke splnění závěrečného testu a tím i ukončení kurzu: ";
                $message .= $coursename.".<br><br>";
                $message .= "Zde si můžete vyzvednout ".$cert." .<br>";
                $message .= $feedback;
                $message .= "<br><br>S pozdravem a přáním hezkého dne,<br<br>
                        Realizační tým projektu Efektivní správa obcí";

                // KONTROLNÍ ECHO       
                //echo "rid:".$iid." userid:".$user->id." courseid:".$course->id." time:".$timecompleted." sent:".$mailstate ;
                //echo "<br>";
                //echo $message;
                //echo "<br><br>";
                email_to_user($user,'Projekt Efektivní správa obcí','Notifikace o úspěšném dokončení kurzu',$message);
                $DB->execute("UPDATE IGNORE mdl_course_completions SET mailsent = 1 WHERE id = ".$completionid."");
                }
  
  
           
        }


        static function courseOrder()
        {
                global $DB,$CFG;
                $categories =  $DB->get_fieldset_sql("SELECT id FROM {course_categories} ORDER BY id");
                foreach($categories as $category){
                   //echo "KATEGORIE:".$category."- ";
                   $courselist = $DB->get_fieldset_sql("SELECT id FROM {course} WHERE category = ".$category." ORDER BY sortorder ASC");
                   $courselist_test = $DB->get_fieldset_sql("SELECT id FROM {course} WHERE category = ".$category." ORDER BY startdate DESC");
                       if($courselist == $courselist_test){
                       echo "KATEGORIE:".$category."- OK<br>";
                       }
                       else{
                       echo "KATEGORIE:".$category."- reorder<br>";
                       $order = 0;  
                       foreach($courselist_test as $course){
                       $order = sprintf('%04d', $order);
                           $DB->execute("UPDATE {course} SET sortorder = ".$category.$order." WHERE id = ".$course."");
                           echo "UPDATE {course} SET sortorder = ".$category.$order." WHERE id = ".$course."";
                           echo "<br>";
                           $order++;
                           }
                
                       }
                }
        }



        static function fillNotification()
        {
                global $DB;

                $courselist_all = $DB->get_fieldset_sql("SELECT * FROM mdl_course WHERE category NOT IN (3,8,127,124,128,114) ");
                $courselist_notification_allready_in = $DB->get_fieldset_sql("SELECT courseid FROM mdl_notifikace GROUP BY courseid");
                $courselist = "";
                $courselist = array();

                foreach($courselist_all as $course_potential){
                if(in_array($course_potential,$courselist_notification_allready_in))
                        {
                
                        } else{
                        $courselist[] = $course_potential; 
                        }
                }

                $text = $DB->get_field_sql("SELECT text FROM mdl_notifikace WHERE courseid = 0");

                foreach ($courselist as $course){
                
                $DB->execute("INSERT INTO mdl_notifikace (courseid,type,sent,enabled,text,sendtime) VALUES (".$course.",1,0,1,'".$text."',NULL)");
                $DB->execute("INSERT INTO mdl_notifikace (courseid,type,sent,enabled,text,sendtime) VALUES (".$course.",3,0,1,'".$text."',NULL)");
                $DB->execute("INSERT INTO mdl_notifikace (courseid,type,sent,enabled,text,sendtime) VALUES (".$course.",7,0,1,'".$text."',NULL)");

                }
        }




        static function test($course_id){

                $context = \context_course::instance($course_id);
                $students = get_role_users(5,$context);
                $students_count = count($students);
                print_r($students_count);
                

        }



        static function multidateEnrollments(){
                global $DB;
                $list_sql = 'SELECT value FROM {customfield_data} WHERE value != "" AND fieldid = ? AND instanceid IN 
                        (
                                SELECT id FROM {course} WHERE visible = 1 AND startdate > ?
                        ) GROUP BY value';
                $list_of_multidate_codes = $DB->get_fieldset_sql($list_sql,array(17,time()-90000));

                foreach ($list_of_multidate_codes as $key => $value) {
                        $courses_sql = 'SELECT id,startdate,enddate FROM {course} WHERE id IN (
                                SELECT instanceid FROM {customfield_data} WHERE fieldid = ? AND value = ?
                        )
                        ORDER BY startdate ASC';
                        $courses = $DB->get_records_sql($courses_sql,array(17,$value));

                        foreach ($courses as $key => $course) {
                                $sql = 'SELECT * FROM {enrol} WHERE courseid = ? ';
                                $instance = $DB->get_record_sql($sql,array($course->id));
                                $enrol = enrol_get_plugin($instance->enrol);
                                print_r($enrol);
                                //$enrol->enrol_user($instance, $this->userid, $roleid, );
                                //$enrol->unenrol_user($instance, $this->userid);
                        }        
                                
                        
                }


                $sql = 'SELECT * FROM {enrol} WHERE courseid = ? AND enrol = "self"';
                                $instance = $DB->get_record_sql($sql,array(1551));
                                $enrol = enrol_get_plugin($instance->enrol);
                                $enrol->unenrol_user($instance, 5);
                             echo   $enrol->enrol_user($instance, 5, 5 );


        

        }


        /**
         * enrols user into course by enrol self
         */

        static function enrolUser($courseid,$userid)
        {       global $DB;
                $sql = 'SELECT * FROM {enrol} WHERE courseid = ? AND enrol = "self"';
                $instance = $DB->get_record_sql($sql,array($courseid));
                $enrol = enrol_get_plugin($instance->enrol);
                try{
                        $enrol->enrol_user($instance, $userid, 5 );
                } catch (Exception $e){
                        return $e;
                }
                return 'user: '.$userid.' enrolled in course: '.$courseid;
                
                
        }

         /**
         * unenrols user from course by enrol self
         */

        static function unenrolUser($courseid,$userid)
        {       global $DB;
                $sql = 'SELECT * FROM {enrol} WHERE courseid = ? AND enrol = "self"';
                $instance = $DB->get_record_sql($sql,array($courseid));
                $enrol = enrol_get_plugin($instance->enrol);
                try{
                        $enrol->unenrol_user($instance, $userid);
                } catch (Exception $e){
                        return $e;
                }
                return 'user: '.$userid.' unenrolled from course: '.$courseid;
                
                
        }
       

 
 
 } 