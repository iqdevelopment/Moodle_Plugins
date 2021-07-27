<?php
namespace local_custom_notification\main;

class course_notification{

    public $courseid;
    public $notificationText;
    public $type;
    public $enabled;
    public $sent;

    
    function __construct($courseid = null){
        $this->courseid = $courseid;

    }

    public function getCoursePeriodicalNotification()
    {   global $DB;
        $periodicNotification = $DB->get_record_sql('SELECT * FROM {custom_notification} WHERE courseid = ? AND type != 0',array($this->courseid));
        if($periodicNotification){
             return $periodicNotification->text;
            }
        else{
            $periodicNotification = $DB->get_record_sql('SELECT * FROM {custom_notification} WHERE courseid = 0 type != 0');
            
            if ($periodicNotification){
                return $periodicNotification->text;
            }else{
                return get_string('noPeriodicText','local_custom_notification');
            }
        }
        
    }


    public function getCourseOneTimeNotification()
    {   global $DB;
        $periodicNotification = $DB->get_record_sql('SELECT * FROM {custom_notification} WHERE courseid = ? AND type = 0',array($this->courseid));
        if($periodicNotification){
             return $periodicNotification->text;
            }
        else{
            $periodicNotification = $DB->get_record_sql('SELECT * FROM {custom_notification} WHERE courseid = 0 AND type = 0');
            
            if ($periodicNotification){
                return $periodicNotification->text;
            }else{
                return get_string('noOneTimeText','local_custom_notification');
            }
        }
        
    }


    public function fillNotification()
    {
        global $DB;
        $tofill = $DB->get_records_sql('SELECT {course}.id,{course}.category FROM {course}
                            WHERE {course}.category IN 
                            (
                                SELECT value FROM {config_plugins}  WHERE plugin = ? AND name = ?
                            )
                             AND visible = 1',
                             array('local_custom_notification','categories'));
        $defaultNotificationText = $DB->get_field_sql('SELECT VALUE from {config_plugins} WHERE plugin = ? AND name = ?',array('local_custom_notification','default_text_periodical'));
        foreach ($tofill as $key => $course) {
            $check = $DB->get_fieldset_sql('SELECT id FROM {custom_notification} WHERE courseid = ?',array($course->id));
            if($check){
                continue;
            }else{

               $customCategoryNotificationCheck =  $DB->get_record_sql('SELECT * FROM {config_plugins}  WHERE plugin = ? AND name = ?',array('local_custom_notification',$course->category));
                if($customCategoryNotificationCheck){
                    $notificationText = $customCategoryNotificationCheck->value;
                }else{
                    $notificationText = $defaultNotificationText; 
                }

                $sql = 'INSERT INTO {custom_notification} (courseid,type,sent,enabled,timecreated,text) values (?,?,?,?,?,?)';
                $types = $DB->get_records_sql('SELECT * FROM  {config_plugins}  WHERE plugin = ? AND name LIKE "%type%"',array('local_custom_notification'));
                foreach ($types as $type) {
                    $name = explode($type->name);
                    $name = $name[1];
                    $DB->execute($sql,array($course->id,$name,0,$type->value,time(),$notificationText));
                }
               
                
            }


        }
    }






        /*****
         * 
         *
         * functin to render customizable form to control periodic notification 
         * 
         */


    public function renderRegularForm()
    {   global $DB,$CFG;
        $course = $DB->get_record('course',array('id' => $this->courseid));
       // print_r($this->id);
        $contextid = $DB->get_field_sql("SELECT id FROM mdl_context WHERE contextlevel = 50 AND instanceid = ".$course->id."");
        $user_records = $DB->get_records_sql('SELECT {role_assignments}.id,{role_assignments}.roleid,{user}.firstname,{user}.lastname,{user}.firstnamephonetic,{user}.lastnamephonetic
                                                 FROM {role_assignments}
                                                 INNER JOIN {user} ON {user}.id = {role_assignments}.userid
                                                  WHERE {role_assignments}.contextid = ?',array($contextid));
        foreach($user_records as $user){
           
            if($user->roleid == 1){
                $contacs[] = $user;
                }
            elseif($user->roleid == 3){
            $lectors[] = $user;
                }
            else{
            $students[] = $user;
                }  
        }

        echo get_string('notification_for_course', 'local_custom_notification')." <a href='".$CFG->wwwroot."/course/view.php?id=".$course->id."'>".$course->fullname."</a><br>";
        $contacs_size = count($contacs);
        $contact_from_array = "";
        //contact person
        if($contacs_size > 1){
            foreach ($contacs as $contac){
            $fullname = array($contac->firstnamephonetic,$contac->firstname,$contac->lastname,$contac->lastnamephonetic);
            $contact_from_array = $contact_from_array.implode(' ',$fullname);
            $contact_from_array .= ", ";
            }
        
        echo "<b>".get_string('contact_person', 'local_custom_notification')."</b>".$contact_from_array."<br>";
        }
        elseif($contacs_size == 1){
                    foreach ($contacs as $contac){
                        $fullname = array($contac->firstnamephonetic,$contac->firstname,$contac->lastname,$contac->lastnamephonetic);
                        $contact_from_array = $contact_from_array.implode(' ',$fullname);
                        }
        
        echo "<b>".get_string('contact_persons', 'local_custom_notification')."</b>".$contact_from_array."<br>";
        }else{
        echo "<b>".get_string('no_contact_person', 'local_custom_notification')."</b>"."<br>";
        }
        //lectors
        $lectors_size = count($lectors);
        $lectors_from_array = "";
        
             if($lectors_size > 1){
                foreach ($lectors as $lector){
                  $fullname = array($lector->firstnamephonetic,$lector->firstname,$lector->lastname,$lector->lastnamephonetic);
                 $lectors_from_array = $lectors_from_array.implode(' ',$fullname);
                 $lectors_from_array .= ", ";
                 }
             
             echo "<b>".get_string('lectors', 'local_custom_notification')."</b>".$lectors_from_array."<br>";
             }
             elseif($lectors_size == 1){
                        foreach ($lectors as $lector){
                             $fullname = array($lector->firstnamephonetic,$lector->firstname,$lector->lastname,$lector->lastnamephonetic);
                             $lectors_from_array = $lectors_from_array.implode(' ',$fullname);
                             }
             
              echo "<b>".get_string('lector', 'local_custom_notification')."</b>".$lectors_from_array."<br>";
             }else{
             echo "<b>".get_string('no_lectors', 'local_custom_notification')."</b>"."<br>";
             }

             $students_size = count($students);
        //student count
        if($students_size > 0){
        echo get_string('students', 'local_custom_notification').$students_size."<br>";
        }else{
        echo get_string('no_students', 'local_custom_notification')."<br>";
        }


        //checkboxes
        $checked = $DB->get_fieldset_sql("SELECT type FROM {custom_notification} WHERE courseid = ".$course->id." AND enabled='1'"); 

        if (in_array('1',$checked)){
        $checked_1 = 'checked';
        }else{
            $checked_1 = '';  
        }

        if (in_array('3',$checked)){
        $checked_3 = 'checked';
        }else{
            $checked_3 = '';
        }
        if (in_array('7',$checked)){
        $checked_7 = 'checked';
        }else{
            $checked_7 = '';
        }

        $form = '<br>';
        "<br><h3>".get_string('regular_notification', 'local_custom_notification')."</h3>"; 
        $form .= "<button id='help'>".get_string('hint', 'local_custom_notification')."</button>";
        $form .= "
        <table id='helpdiv' style='display:none;>
        <tr>
        <td colspan = '2'><b>
        ".get_string('text_restriction', 'local_custom_notification')."</b></td>
        </tr>
        <tr>
        <th >".get_string('tag', 'local_custom_notification')."</th>
        <th>".get_string('meaning', 'local_custom_notification')."</th>
        </tr>
        <tr>
        <td>[course_name]</td>
        <td>".get_string('course_name', 'local_custom_notification')."</td>
        </tr>
        <tr>
        <td>[days_to_start]</td>
        <td>".get_string('days_to_start', 'local_custom_notification')."</td>
        </tr>
        <tr>
        <td>[course_start_date_and_time]</td>
        <td>".get_string('course_start_date_and_time', 'local_custom_notification')."</td>
        </tr>
        <tr>
        <td>[course_starttime]</td>
        <td>".get_string('course_starttime', 'local_custom_notification')."</td>
        </tr>
        <tr>
        <td>[course_startdate]</td>
        <td>".get_string('course_startdate', 'local_custom_notification')."</td>
        </tr>
        <tr>
        <td>[course_endtime]</td>
        <td>".get_string('course_endtime', 'local_custom_notification')."</td>
        </tr>
        <tr>
        <td>[course_end_date_and_time]</td>
        <td>".get_string('course_end_date_and_time', 'local_custom_notification')."</td>
        </tr>
        <tr>
        <td>[course_url]</td>
        <td>".get_string('course_url', 'local_custom_notification')."</td>
        </tr>";

        $custom_course_fields = $DB->get_records_sql('SELECT shortname,name FROM {customfield_field}');
        foreach ($custom_course_fields as $key => $value) {
            $form .= "
                <tr>
                    <td >[course_field_".$value->shortname."]</td>
                    <td>".$value->name."</td>
                </tr>";
        }
            
        $form .=  "</table>";
        $notification_record = $DB->get_record_sql('SELECT * FROM {custom_notification} WHERE courseid = ? LIMIT 1',array('courseid'=>$course->id));
        if($notification_record){
            $notification_text = $notification_record->text;
        }else{
            
            $notification_record = $DB->get_record_sql('SELECT * FROM {config_plugins} WHERE plugin = ? AND name = ?',array('local_custom_notification','category_id-'.$course->category));
            if($notification_record){
                $notification_text = $notification_record->value;   
            }else {
                $notification_text = $DB->get_field_sql('SELECT value FROM {config_plugins} WHERE plugin = ? AND name = ?',array('local_custom_notification','default_text_periodical'));
            }
        }

        $form .= "<form action='".$CFG->wwwroot."/local/custom_notification/AJAX/processTemplate.php' method='post' id='notifikace-form'>"; 
        $form .="<input type='text' id='form2' name='update' value='".$course->id."' hidden>";
        $form .="<input type='text' id='form1' name='courseid' value='".$course->id."' hidden>";  
        $form .= "<textarea style='width:100%;white-space: pre-wrap;' form='notifikace-form' name='notifikace' rows='8' cols='20'>".
        $notification_text 

        ."</textarea><br>";
        $form .= "<input type='checkbox' name='1-day' ".$checked_1."> ".get_string('one_day', 'local_custom_notification')."</input><br>";
        $form .= "<input type='checkbox' name='3-day' ".$checked_3."> ".get_string('three_day', 'local_custom_notification')."</input><br>";
        $form .= "<input type='checkbox' name='7-day' ".$checked_7."> ".get_string('seven_day', 'local_custom_notification')."</input><br><br>";
        $form .= "<a href='".$CFG->wwwroot."/local/custom_notification'>".get_string('plugin_advanced', 'local_custom_notification')."</a><br>";
        $form .= "<input type='submit' value='".get_string('submit', 'local_custom_notification')."'>";
        $form .= "</form>"; 


        return $form;

    }





    /****
     * 
     * 
     * Form to immediately send custom message 
     * 
     */




    public function renderOneTimeForm()
    {   global $DB;
        $course = $DB->get_record('course',array('id' => $this->courseid));
        $form = "<br><h3>".get_string('one_time_heading', 'local_custom_notification')."</h3>"; 
       // $notification_record = $DB->get_record('notifikace',array('courseid'=>$course->id));
        $DefaultnotificationIneTimeText = $DB->get_field_sql('SELECT value FROM {config_plugins} WHERE plugin = ? AND name = ?',array('local_custom_notification','default_text_one_time'));
        $subject = get_string('one_time_subject_text', 'local_custom_notification');
        $form .= "<form action='notifikace_process_sendmail.php' method='post' id='notifikace-form-send-mail'>"; 
        $form .="<input type='text' name='courseid' value='". $this->courseid."' hidden>";
        $form .= get_string('one_time_subject', 'local_custom_notification')."<input type='text' id='subject' name='subject' value='".$subject."'></input>";    
        $form .= "<textarea id='text' style='width:100%;white-space: pre-wrap;' form='notifikace-form-send-mail' name='notifikace' rows='8' cols='20'>".
        $DefaultnotificationIneTimeText 
        ."</textarea><br>";
        $form .= "<input type='button' id='submit-bnt' onClick='sentOneTimeNotification(".$this->courseid.")' value='".get_string('send', 'local_custom_notification')."'>";
        $form .= "</form>"; 
        return $form;
    
    }
     













}


//defined('MOODLE_INTERNAL') || die();

