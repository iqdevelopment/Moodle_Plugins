<?php

//  Display the course home page.

    require_once('../../config.php');
    require_once('lib.php');
   /* require_once('custom_lib.php');*/
    require_once($CFG->libdir.'/completionlib.php');
    global $DB,$USER,$CFG;
    $strPageTitle = get_string('pluginname','local_bbb_attendance');
    $PAGE->set_title($strPageTitle);

    require_login();

        if(!has_capability('local/bbb_attendance:admin', context_system::instance()))
        {
        echo $OUTPUT->header();
        echo "<h3>".get_string('noacess','local_bbb_attendance')."</h3>";
        echo $OUTPUT->footer();
        exit;
        }
    
    
        $id= optional_param('id', 0, PARAM_INT);
        $bbbid= optional_param('bbbid', 0, PARAM_INT);

       //  $PAGE->set_heading($course->fullname);
    
    echo $OUTPUT->header();
    
    
        
   $bbb_check =  $DB->get_records_sql("SELECT id,course,name,meetingid FROM {bigbluebuttonbn} WHERE course = ".$id."");
   $bbb_check_auth = $DB->get_fieldset_sql("SELECT id FROM {bigbluebuttonbn} WHERE course = ".$id."");
   $course = $DB->get_record('course',array('id' => $id));
  // echo sizeof($bbb_check) ;
    
     if(!empty($bbb_check) AND ((!empty($bbbid) AND in_array($bbbid,$bbb_check_auth)) OR (empty($bbbid) AND sizeof($bbb_check)== 1))){
        echo '<b>Docházka pro kurz: </b>'."<a href='".$CFG->wwwroot."/course/view.php?id=".$course->id."'>".$course->fullname."</a><br><br>";
        if(sizeof($bbb_check) == 1){
        $bbb_instance = $bbb_instance = $DB->get_record_sql("SELECT id,course,name,meetingid FROM {bigbluebuttonbn} WHERE course = ".$id."");
        }else{
        $bbb_instance = $DB->get_record_sql("SELECT id,course,name,meetingid FROM {bigbluebuttonbn} WHERE course = ".$id." AND id = ".$bbbid."");
        } 

        echo '<b>nazev modulu: '.$bbb_instance->name.'</b><br>';   
        //echo "zobraz jen jednu dochazku";
        $method = "getMeetingInfo";
        //bbbIsRuning($instance);
        $data = array(
	   'meetingID' => $bbb_instance->meetingid.'-'.$bbb_instance->course.'-'.$bbb_instance->id);
        $checkIsRunning = fetchFromServer($method, $data);
        if($checkIsRunning->returncode == 'SUCCESS'){
        echo 'Konference právě probíhá, nejprve ji ukončete!<br>';
        
        
        }else{
        //kontrola, zda dochazka uz existuje, pokud ne, vytvori prazdne zaznamy -> jinak by nefungoval zapis
            
            $instance_id =  $DB->get_field_sql("SELECT id FROM mdl_grade_items WHERE courseid = ".$course->id." AND itemname = 'dochazka'");
            if(empty($instance_id)){
                echo 'Docházka neexistuje, prosím vytvořte ji a poté se vraťte!';
                exit;
            }

            $record_users = $DB->get_fieldset_sql("SELECT userid FROM mdl_grade_grades WHERE itemid = ".$instance_id."");
            $context = $DB->get_field_sql("SELECT id FROM mdl_context WHERE contextlevel = 50 AND instanceid = ".$course->id."");
            $users =  $DB->get_fieldset_sql("SELECT userid FROM mdl_role_assignments WHERE roleid = 5 AND contextid = ".$context."");
            foreach ($users as $user){
                  
              $user_check = $DB->get_field_sql("SELECT userid FROM mdl_grade_grades WHERE itemid = ".$instance_id." AND userid=".$user."");

          if(empty($user_check)){
              $DB->execute("INSERT INTO mdl_grade_grades 
              (itemid,userid,rawgrade,rawgrademax,rawgrademin,rawscaleid,usermodified,finalgrade,hidden,locked,locktime,exported,overridden,excluded,feedback,feedbackformat,information,informationformat,timecreated,timemodified,aggregationstatus,aggregationweight)
              VALUES (
              ".$instance_id.",
              ".$user.",
              NULL,
              1.00000,
              0.00000,
              NULL,
              ".$USER->id.",
              0.00000,
              0,
              0,
              0,
              0,
              0,
              0,
              NULL,
              0,
              NULL,
              0,
              NULL,
              ".time().",
              'used',
              1.00000       
              )
              ");
              echo "<script>
          location.reload();
          </script>";       
              
          }
              }
        
        
        
        // sem p5idat jquerry
        echo show_bbb_attendance($bbb_instance);
       
        }
   
    
   // v kurzu existuje mod BBB , je vice modulu a nebo neni platne zadane bid
    }elseif(!empty($bbb_check) AND (empty($bbbid) OR !in_array($bbbid,$bbb_check_auth))){
       echo '<b>Docházka pro kurz: </b>'."<a href='".$CFG->wwwroot."/course/view.php?id=".$course->id."'>".$course->fullname."</a><br><br>";
      // $bbb_check =  $DB->get_records_sql("SELECT id,course,name,meetingid FROM {bigbluebuttonbn} WHERE course = ".$id."");
       echo '<h2>Zvolte konferenci, ze které chcete docházku</h2>';
      foreach($bbb_check as $bbb_instance){
            $url = "<a href='" . new moodle_url('/course/dochazka.php',array('id' => $bbb_instance->course,'bbbid' => $bbb_instance->id)) . "'>".$bbb_instance->name."</a>";
            echo $url."<br>";
       }
   
   
   
   // v kurzu neni zadny BBB plugin -> pokracuji k normalni dochazce
   }else{

   $instance_id =  $DB->get_field_sql("SELECT id FROM mdl_grade_items WHERE courseid = ".$id." AND itemname = 'dochazka'");
   $records = $DB->get_records_sql("SELECT mdl_grade_grades.id,mdl_grade_grades.itemid,mdl_grade_grades.userid,mdl_grade_grades.usermodified,mdl_grade_grades.finalgrade
         FROM mdl_grade_grades,mdl_user WHERE itemid = ".$instance_id." AND mdl_grade_grades.userid = mdl_user.id ORDER BY mdl_user.lastname");
 
   //p�erovat recordy podle p��jmen� userid
  
   $context = $DB->get_field_sql("SELECT id FROM mdl_context WHERE contextlevel = 50 AND instanceid = ".$id."");
   
   $users =  $DB->get_fieldset_sql("SELECT userid FROM mdl_role_assignments WHERE roleid = 5 AND contextid = ".$context."");
   $record_users = $DB->get_fieldset_sql("SELECT userid FROM mdl_grade_grades WHERE itemid = ".$instance_id."");

   if($record_users !== $users){

     if(!empty($users)){   
        foreach ($users as $user){
        
            $user_check = $DB->get_field_sql("SELECT userid FROM mdl_grade_grades WHERE itemid = ".$instance_id." AND userid=".$user."");

        if(empty($user_check)){
            $DB->execute("INSERT INTO mdl_grade_grades 
            (itemid,userid,rawgrade,rawgrademax,rawgrademin,rawscaleid,usermodified,finalgrade,hidden,locked,locktime,exported,overridden,excluded,feedback,feedbackformat,information,informationformat,timecreated,timemodified,aggregationstatus,aggregationweight)
             VALUES (
             ".$instance_id.",
            ".$user.",
            NULL,
            1.00000,
            0.00000,
            NULL,
            ".$USER->id.",
            0.00000,
            0,
            0,
            0,
            0,
            0,
            0,
            NULL,
            0,
            NULL,
            0,
            NULL,
            ".time().",
            'used',
            1.00000       
             )
             ");
             echo "<script>
        location.reload();
        </script>";       
             
        }
            }
 
    }else{
    echo 'Zde nejsou žádní uživatelé na které by se vztahovala docházka!<br>' ;
    }   
        }
   echo '<b>Docházka pro kurz: </b>'."<a href='".$CFG->wwwroot."/course/view.php?id=".$course->id."'>".$course->fullname."</a>";
   echo "<form action='dochazka_process.php' method='post'>";
   echo "<table border = 1 style='text-align:center;'>";
  // echo "<script type='text/javascript' src='check.js'></script>";
           echo "<tr>
           <td width='40px'>
           <input type='checkbox' id='selectall' onClick='selectAll(this)' value='selected'></td><td>".'<b>Jméno a příjmení</b>'."</td></tr>";
   echo "
   <input type='text' name='courseid' value='".$id."' hidden>
   <input type='text' name='itemid' value='".$instance_id."' hidden>";       
   foreach  ($records as $record){
   $user = $DB->get_record('user',array('id' => $record->userid));
   if($record->finalgrade == 1){$checked = "checked";} else {$checked = "";}
   echo "<tr>
   <td><input type='checkbox' value=".$record->userid." name = 'selectedusers[]' ".$checked."></td><td style='text-align:left;padding: 5px; '> "." ".$user->firstnamephonetic." ".$user->lastname." ".$user->firstname." ".$user->lastnamephonetic."</td>
   <tr>";
   
   }
   echo "<tr><td colspan=2><input type='submit'></td></tr>
   </table>
</form>"; 


    }
    echo $OUTPUT->footer();
