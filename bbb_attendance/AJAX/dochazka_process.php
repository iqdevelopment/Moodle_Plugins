<?php

//  Display the course home page.

require_once('../../../config.php');
    require_once('../lib.php');
    require_once($CFG->libdir.'/completionlib.php');
    global $DB,$USER,$CFG;
    $strPageTitle = get_string('pluginname','local_bbb_attendance');
$PAGE->set_title($strPageTitle);
    
            $course_contacts = $DB->get_fieldset_sql("SELECT userid FROM mdl_role_assignments WHERE roleid IN (1,2,3,4,11,12) GROUP BY userid");
       // $course_contacts .= $DB->get_field_sql("SELECT value FROM mdl_config WHERE name='siteadmins'");
      $to_push = $DB->get_field_sql("SELECT value FROM mdl_config WHERE name='siteadmins'");
         $to_push = explode(",", $to_push);
       $course_contacts =  array_merge($course_contacts,$to_push);
       
          if (in_array($USER->id,$course_contacts)){}else{redirect(get_login_url());}; 
    
    $values = $_POST;

        echo $OUTPUT->header();
    
    $cid =  $values['courseid'];
    $itemid =  $values['itemid'];
    $passed = $values['selectedusers'];
    $passed_imploded = implode(',',$passed);
 
    echo "<a href='".$CFG->wwwroot."/course/view.php?id=".$cid."'><button>Návrat do kurzu</button></a> <a href='".$CFG->wwwroot."/local/bbb_attendance/attendance.php?id=".$cid."'><button>Návrat k docházce</button></a><br><br>";
    echo "<h3>Prosím zkontrolujte, že jsou provedené změny v pořádku a potvrďte tlačítkem 'SOUHLASÍ'!</h3>";
    foreach ($passed as $user){
        $user = $DB->get_record('user',array('id' => $user));
        $is_changed = $DB->get_field_sql("SELECT finalgrade FROM mdl_grade_grades WHERE itemid = ".$itemid." AND userid=".$user->id."");
            if($is_changed < 1){
                echo "Docházka bude označena za splněnou pro: <b>".$user->firstnamephonetic." ".$user->firstname." ".$user->lastname." ".$user->lastnamephonetic."</b><br>";
               // $DB->execute("UPDATE mdl_grade_grades SET finalgrade='1.00000' WHERE itemid = ".$itemid." AND userid=".$user->id."");
               $complete_item = $DB->get_field_sql("SELECT id FROM mdl_grade_items WHERE courseid = ".$cid." AND itemtype='course'");
                
                          }else{
                echo "Docházka již byla splněna: <b>".$user->firstnamephonetic." ".$user->firstname." ".$user->lastname." ".$user->lastnamephonetic."</b><br>";
                }
    }
    if(empty($passed_imploded)){
     $failed = $DB->get_fieldset_sql("SELECT userid FROM mdl_grade_grades WHERE itemid = ".$itemid."");
    }else{
    $failed = $DB->get_fieldset_sql("SELECT userid FROM mdl_grade_grades WHERE itemid = ".$itemid." AND userid NOT IN (".$passed_imploded.")");
    }
    
    foreach ($failed as $fail){
        $user = $DB->get_record('user',array('id' => $fail));
        $is_changed = $DB->get_field_sql("SELECT finalgrade FROM mdl_grade_grades WHERE itemid = ".$itemid." AND userid=".$user->id."");
            if($is_changed == '1.00000'){
                echo "Docházka bude označena za nesplněnou pro: <b>".$user->firstnamephonetic." ".$user->firstname." ".$user->lastname." ".$user->lastnamephonetic."</b><br>";
               // $DB->execute("UPDATE mdl_grade_grades SET finalgrade='0.00000' WHERE itemid = ".$itemid." AND userid=".$user->id."");
                $complete_item = $DB->get_field_sql("SELECT id FROM mdl_grade_items WHERE courseid = ".$cid." AND itemtype='course'");
              //  $DB->execute("DELETE FROM mdl_grade_grades WHERE itemid = ".$complete_item." AND userid = ".$user->id."");
                }else{
                 echo "Docházka stále nesplněna: <b>".$user->firstnamephonetic." ".$user->firstname." ".$user->lastname." ".$user->lastnamephonetic."</b><br>";
                }
        }

  

       
       
       
       
       
       
       
        $failed = $DB->get_fieldset_sql("SELECT userid FROM mdl_grade_grades WHERE itemid = ".$itemid."");
    
    echo "<form action='".$CFG->wwwroot."/grade/report/singleview/index.php?id=".$cid."&item=grade&group=&itemid=".$itemid."' method='post'>";
   // echo "<form action='".$CFG->wwwroot."/grade/report/singleview/index.php?id=".$cid."&item=grade&group=&itemid=".$itemid."' method='post'>";
    foreach ($failed as $user){
        if(in_array($user,$passed)){
        $finalgrade = 1;
        $oldgrade = 0;
        } else{
        $finalgrade = 0;
        $oldgrade = 1;
        }
    

    echo "<input type='text' name='".'finalgrade_'.$itemid._.$user."' value='".$finalgrade."' hidden>";
    echo "<input type='text' name='".'oldfinalgrade_'.$itemid._.$user."' value='".$oldgrade."' hidden>";
    echo "<input type='text' name='".'feedback_'.$itemid._.$user."' value='0' hidden>";
    echo "<input type='text' name='".'oldfeedback_'.$itemid._.$user."' value='0' hidden>";
    echo "<input type='text' name='".'oldexclude_'.$itemid._.$user."' value='0' hidden>";
    }
    echo "<input type='text' name='".'bulk_'.$itemid.'_type'."' value='blanks' hidden>";
    echo "<input type='text' name='".'bulk_'.$itemid.'_value'."' value='0' hidden>";
    echo "<input type='text' name='".'oldbulk_'.$itemid.'_value'."' value='0' hidden>";
    echo "<input type='text' name='sesskey' value='".$USER->sesskey."' hidden>";  
    
    
    echo "<br>";
    echo "<button type='submit' value='souhlasi'>SOUHLASÍ</button>";

    
    
    
   
    
    
    echo $OUTPUT->footer();
