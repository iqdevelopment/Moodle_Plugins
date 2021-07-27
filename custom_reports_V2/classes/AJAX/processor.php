<?php


namespace local_custom_reports\AJAX;

class processor{
    public $course;


    public function renderTemplates()
    {   global $DB;
        $alreadySet = $DB->get_records_sql('SELECT * FROM {config_plugins} WHERE plugin = ? AND name like "%category_id%"',array('local_custom_reports'));
        if($alreadySet){

        }else{
           $output =  get_string('no_templates','local_custom_reports').'<br>';
           $output = $DB->get_field_sql('SELECT value FROM {config_plugins} WHERE plugin = ? AND name = ?',array('local_custom_reports','default_text_periodical'));   
        }
        return $output;
        # code...
    }





    public function saveTemplate($POST)
    { global $DB;
    $categories = $POST['categories'];
    $text = $POST['text'];
    $sql = 'INSERT INTO {config_plugins} (plugin,name,value) values ("local_custom_reports",?,?)';
    $sqlOverwiteAll = 'UPDATE {custom_reports} SET text = ? WHERE courseid IN (SELECT id FROM {course} WHERE category = ?) AND type > 0 ';

        foreach ($categories as $key => $value) {
            $DB->execute($sql,array('category_id-'.$value,$text));
            if(isset($POST['reset-all'])){
                $DB->execute($sqlOverwiteAll,array($text,$value));
            }
    
        }
        return get_string('template_saved','local_custom_reports');
    }





    public function deleteTemplate($id)
    {   global $DB;
        $sql = 'DELETE FROM {config_plugins} WHERE plugin = ? AND name = ?';
        $DB->execute($sql,array('local_custom_reports','category_id-'.$id));
        return get_string('deleted','local_custom_reports');
    }





    public function updateTemplate($POST)
    {   global $DB;
       //print_r($POST);
      $existing = $DB->get_records_sql('SELECT * FROM {custom_reports} where courseid = ? AND type != 0',array($POST['courseid']));
      $array = array(1,3,7);

      if($existing){
        $sql = 'UPDATE {custom_reports} 
                    SET timecreated = ? ,
                        enabled = ? ,
                        text = ?
                    WHERE courseid = ? AND type = ?';
            
        foreach ($array as $key => $value) {
            if(isset($POST[$value.'-day'])){
                $enabled = $POST[$value.'-day'];
                if($enabled == 'on'){$enabled = 1;}else{$enabled = 0;}
            }else{
                $enabled = 0;
                }
           
                $DB->execute($sql,array(time(),$enabled,$POST['notifikace'],$POST['courseid'],$value));
            }
            
        }else{
            $sql = 'INSERT INTO {custom_reports} (courseid,type,sent,enabled,timecreated,text) values (?,?,?,?,?,?)';
            
            foreach ($array as $key => $value) {
                if(isset($POST[$value.'-day'])){
                    $enabled = $POST[$value.'-day'];
                    if($enabled == 'on'){$enabled = 1;}else{$enabled = 0;}
                }else{
                    $enabled = 0;
                    }
                    $DB->execute($sql,array($POST['courseid'],$value,0,$enabled,time(),$POST['notifikace']));
                }
  
      }
      
        return get_string('updated','local_custom_reports');
    }






    public function sendOneTimeMessage($ApiString)
    {   global $DB,$USER;
        $user = $DB->get_record_sql('SELECT * FROM {user} WHERE id = ?',array(5));
        $AipArray = explode('&',$ApiString);
        $obj = new \stdClass();
        foreach ($AipArray as $value) {
            $array = explode('=',$value);
            $obj->{$array[0]} = $array[1];
        }
        $obj->users = $DB->get_records_sql('
                                SELECT * FROM {user} WHERE id IN
                                (
                                SELECT userid FROM {user_enrolments}
                                 INNER JOIN {enrol} ON {enrol}.id = {user_enrolments}.enrolid
                                 WHERE {enrol}.courseid = ?
                                 )
                                 ',array($obj->courseid));
        $this->course = $DB->get_record_sql('SELECT * FROM {course} WHERE id = ?',array($obj->courseid)); 
        $subject = $this->replaceBracketString($obj->subject);
        $notificationTextOrig = $this->replaceBracketString($obj->text);
        $notificationText = nl2br($notificationTextOrig);
        $from = $DB->get_record_sql('SELECT * FROM {user} where id = ?',array(2));
        $logQuerry = 'INSERT INTO {custom_reports_log} (notificationid,sent,userid,sender_userid,timecreated,text) values(?,?,?,?,?,?)';
        foreach ($obj->users as $user) {
            //keep for test and not to spamm other users    
            $user = $DB->get_record_sql('SELECT * FROM {user} WHERE id = 5');
                email_to_user($user,$from,$subject,'',$notificationText);
                $DB->execute($logQuerry,array('-'.$this->course->id,1,$user->id,$USER->id,time(),$notificationTextOrig));
            }
               
        return $notificationText;
        
    }






    public function replaceBracketString($text)
    {   global $DB,$CFG;
        $text = str_replace('[course_name]',$this->course->fullname,$text);
        $text = str_replace('[course_url]','<a href="'.$CFG->wwwroot.'/course/view.php?id='.$this->course->id.'">'.$this->course->fullname.'</a>',$text);
        $text = str_replace('[course_start_date_and_time]',date('d.m.Y H:i',$this->course->startdate),$text);
        $text = str_replace('[course_starttime]',date('H:i',$this->course->startdate),$text);
        $text = str_replace('[course_startdate]',date('d.m.Y',$this->course->startdate),$text);
        $text = str_replace('[course_endtime]',date('H:i',$this->course->enddate),$text);
        $text = str_replace('[course_end_date_and_time]',date('d.m.Y H:i',$this->course->enddate),$text);
        $text = str_replace('[days_to_start]',$this->daysToStart(),$text);
        $customFields = $DB->get_records_sql('SELECT id,shortname,name,type FROM {customfield_field}');
       // print_r($this->course);
        foreach ($customFields as $value) {
            $stringToReplace = '[course_field_'.$value->shortname.']';
            $text = str_replace($stringToReplace
            ,
            call_user_func(__NAMESPACE__ .'\processor::getCourseCustomField_'.$value->type,$this->course->id,$value->id)
            ,
            $text);

        }
        
     return $text;  
    }






    /******
     * 
     * functions to access all custom field data 
     * 
     */

    static public function getCourseCustomField_text($courseId,$fieldId){
        global $DB;
        $field = $DB->get_field_sql("SELECT value FROM mdl_customfield_data WHERE fieldid=".$fieldId." AND instanceid=".$courseId."");
        return $field;
    }

    static public function getCourseCustomField_textarea($courseId,$fieldId){
        //print_r($fieldId);echo '<br>';
        global $DB;
        $field = $DB->get_field_sql("SELECT value FROM mdl_customfield_data WHERE fieldid=".$fieldId." AND instanceid=".$courseId."");
        return $field;
    }



    static public function getCourseCustomField_select($courseId,$fieldId){
        //print_r($fieldId);echo '<br>';
        global $DB;
       //přepsání výchozí e-mail msg
            
           $fieldConfig = $DB->get_field_sql("SELECT configdata FROM mdl_customfield_field WHERE id=".$fieldId."");
         
            $data = str_replace('"required":"0","uniquevalues":"0","options":"','',$fieldConfig);
            $data = str_replace('","defaultvalue":"","locked":"0","visibility":"2"','',$data);
            $data = str_replace('","defaultvalue":"NE","locked":"0","visibility":"2"}','',$data);
                  
           $data = str_replace('\r','',$data);
           $data = str_replace('{','',$data);
           $data = str_replace('}','',$data);
           
            
       //začátek debilní části      
           $data = str_replace('\u00e1','á',$data);
           $data = str_replace('\u00e9','é',$data);
           $data = str_replace('\u011b','ě',$data);
           $data = str_replace('\u00ed','í',$data);
           $data = str_replace('\u00fd','ý',$data);
           $data = str_replace('\u00f3','ó',$data);
           $data = str_replace('\u00fa','ú',$data);
           $data = str_replace('\u00da','Ú',$data);
           $data = str_replace('\u016f','ů',$data);
      
           $data = str_replace('\u0148','ň',$data);   //háček
           $data = str_replace('\u0165','ť',$data);   //háček
           
           $data = str_replace('\u010c','Č',$data);  //háček
           $data = str_replace('\u010d','č',$data);  //háček
           $data = str_replace('\u0161','š',$data);  //háček
           $data = str_replace('\u0160','Š',$data);  //háček
           $data = str_replace('\u0159','ř',$data);  //háček
           $data = str_replace('\u0158','Ř',$data);  //háček
           $data = str_replace('\u010','Ď',$data);  //háček
           $data = str_replace('\u00e9','e',$data);
           $data = str_replace('\u00e1','á',$data);
           $data = str_replace('\u00e9','é',$data);
           $data = str_replace('\u00e1','á',$data);
           $data = str_replace('\u00e9','é',$data);
           $data = str_replace('\u017e','ž',$data); //háček
           $data = str_replace('\u017d','Ž',$data); //háček
           $data = str_replace('\u010f','ď',$data); //háček
          
           $data = explode("\\n",$data);
           
           $field = $DB->get_field_sql("SELECT intvalue FROM mdl_customfield_data WHERE fieldid=".$fieldId." AND instanceid=".$courseId."");
           if($field){
                return($data[$field-1]) ;
            }else{
                return '';
            }
            
      }


      /**********
       * 
       * days to start function
       * 
       **********/

       public function daysToStart()
       {    global $DB;
           $now = time();
           $event = $this->course->startdate;
           $daysToStart = date('d',$event-$now);
           $daysToStart = str_replace('0','',$daysToStart);
           

            if ($daysToStart == 1) {
                $returnstring = get_string('future','local_custom_reports').' '.$daysToStart.' '.get_string('one_day_text','local_custom_reports');

            
            } elseif ($daysToStart > 1 AND $daysToStart < 5) {
                $returnstring = get_string('future','local_custom_reports').' '.$daysToStart.' '.get_string('days_2-4','local_custom_reports');

            
            } elseif ($daysToStart > 4) {
                $returnstring = get_string('future','local_custom_reports').' '.$daysToStart.' '.get_string('days_5_plus','local_custom_reports');
            
            } elseif ($daysToStart == 0) {
                $returnstring = get_string('today','local_custom_reports');
            
            } elseif ($daysToStart < 0 AND $daysToStart < -2) {
                $returnstring = get_string('past','local_custom_reports').' '.$daysToStart.' '.get_string('one_day_past','local_custom_reports');
            
            } elseif ($daysToStart < -2) {
                $returnstring = get_string('past','local_custom_reports').' '.$daysToStart.' '.get_string('days_2-4','local_custom_reports');
            }
            

        return $returnstring;

       }






            /*******
             * 
             * 
             * functions for task to send regullar notification
             * 
             * 
             */




       public function sendRegullarNotification()
       {
           global $DB;
          $courses = $this->getCoursesAndTextsToSend();
          $MaxInOneTask = 30;

        //check kvůli tomu,že array může být prázdný hned na začítku
        if(empty($courses[1])) {unset($courses[1]); }
        if(empty($courses[3])) {unset($courses[3]); }
        if(empty($courses[7])) {unset($courses[7]); }
        
        echo '<br><br><br>';

          for ($i=0; $i < $MaxInOneTask; $i++) { 
//validation if sendOneEmail return true -> it means all the emails for this course are sent and its time to go to another course in $courses
// when every email fot tomorrow is sent, the $courses[1] is unset so the for loop will get to the 3-days from now and so one and so one...so we can send 30 emails every cron
                if(isset($courses[1])){
                    $this->type = 1;
                      $check = $this->sendOneEmail($courses[1]);
                      if($check == TRUE){ 
                            array_shift($courses[1]);
                            if(empty($courses[1])){
                                unset($courses[1]);        
                            }
                        }

                      //  echo '1-';
                }elseif (isset($courses[3])) {
                    $this->type = 3;
                    $check = $this->sendOneEmail($courses[3]);
                      if($check == TRUE){

                            array_shift($courses[3]);
                            if(empty($courses[3])){
                                unset($courses[3]);
                            }
                        }

                      //  echo '3-';

                }elseif (isset($courses[7])) {
                    $this->type = 7;
                    $check = $this->sendOneEmail($courses[7]);
                      if($check == TRUE){
                            array_shift($courses[7]);
                            if(empty($courses[7])){
                                     unset($courses[7]);
                            }
                        }

                      //  echo '4-';
                }else{
                    break;
                }
               
              
          }
          
          
       }







       public function getCoursesAndTextsToSend()
       {    global $DB;
            $processDays = array(1,3,7);
            $allowed = $DB->get_field_sql('SELECT value FROM {config_plugins} WHERE plugin = ? AND name = ?',array('local_custom_reports','categories'));

            $sql = 'SELECT {course}.id,{course}.category,{course}.startdate,{custom_reports}.text FROM {course}
                    INNER JOIN {custom_reports} ON {custom_reports}.courseid = {course}.id
                    WHERE
                            {course}.category IN ('.$allowed.') 
                        AND {custom_reports}.enabled = 1
                        AND {custom_reports}.sent = 0
                        AND {course}.startdate > ?
                        AND {course}.startdate < ?
                        AND {custom_reports}.type = ?
                        ';

            foreach ($processDays as $key => $value) {
                $from = time()+($value*3600*24)-(3600*24);
                $to = time()+($value*3600*24);

                $toProcess[$value] = $DB->get_records_sql($sql,array($from,$to,$value));
            }   

           return $toProcess;
       }



       public function sendOneEmail($array)
       {    global $DB;
        $firstKey = array_key_first($array);
        $courseid = $array[$firstKey]->id;
        $this->course = $DB->get_record_sql('SELECT * FROM {course} WHERE id = ?',array($courseid));
        $text = $this->replaceBracketString(nl2br($array[$firstKey]->text));
        $sql = 'SELECT * FROM {user} WHERE id IN 
                    (
                        SELECT userid FROM {user_enrolments} 
                            WHERE enrolid IN (
                                    SELECT id FROM {enrol} WHERE courseid = ?
                                )
                    )
                    AND id NOT IN (
                        SELECT userid FROM {custom_reports_log} 
                            WHERE sent = 1 AND notificationid = (
                                SELECT id FROM {custom_reports} 
                                    WHERE courseid = ? AND type = ?
                            )
                    )
                    LIMIT 1
                    ';
        $user = $DB->get_record_sql($sql,array($this->course->id,$this->course->id,$this->type));
        if(empty($user)){
            $updateQuerry = 'UPDATE {custom_reports} SET sent = 1 , timecreated = ? WHERE courseid = ? AND type = ?';
            $DB->execute($updateQuerry,array(time(),$this->course->id,$this->type));
            return TRUE;
        }
        $logQuerry = 'INSERT INTO {custom_reports_log} (notificationid,sent,userid,sender_userid,timecreated,text) values (?,?,?,?,?,?)';
        $notificationId = $DB->get_field_sql('SELECT id FROM {custom_reports} WHERE courseid = ? AND type = ?',array($this->course->id,$this->type));
        $DB->execute($logQuerry,array($notificationId,1,$user->id,0,time(),$notificationId));
        $subject = $this->replaceBracketString($DB->get_field_sql('SELECT value FROM {config_plugins} WHERE plugin = ? AND name = ?',array('local_custom_reports','default_subject')));                        
        $from = $DB->get_record_sql('SELECT * FROM {user} where id = ?',array(2));
        $user = $DB->get_record_sql('SELECT * FROM {user} where id = ?',array(5));
        email_to_user($user,$from,$subject,'',$text);
       // $DB->execute($logQuerry,array('-'.$this->course->id,1,$user->id,$USER->id,time(),$notificationTextOrig));
        
          
       }




}