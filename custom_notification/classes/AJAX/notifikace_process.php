<?php

//  Display the course home page.

    require_once('../config.php');
    require_once('lib.php');
    require_once($CFG->libdir.'/completionlib.php');
    require_once($CFG->libdir.'/custom_lib.php');
      require_once("$CFG->libdir/formslib.php");
    global $DB,$USER,$CFG;
    
    /*****************
     *
     * úvod který zabrání studentům a nepřihlášeným aby se dostali na tuto stránku
     * 
     * ***********************/
    
     $course_contacts = $DB->get_fieldset_sql("SELECT userid FROM mdl_role_assignments WHERE roleid IN (1,2,3,4,11,12) GROUP BY userid");
       // $course_contacts .= $DB->get_field_sql("SELECT value FROM mdl_config WHERE name='siteadmins'");
      $to_push = $DB->get_field_sql("SELECT value FROM mdl_config WHERE name='siteadmins'");
         $to_push = explode(",", $to_push);
       $course_contacts =  array_merge($course_contacts,$to_push);
       
          if (in_array($USER->id,$course_contacts)){}else{redirect(get_login_url());}; 
          
        /*****************
     *
     * konec - úvod který zabrání studentům a nepřihlášeným aby se dostali na tuto stránku
     * 
     * ***********************/

    
   
          $PAGE->set_heading($course->fullname);
    echo $OUTPUT->header();
      
   
    $default_text = $DB->get_field_sql("SELECT text FROM mdl_notifikace WHERE courseid = 0");
    $default_text_non_formated = $DB->get_field_sql("SELECT text FROM mdl_notifikace WHERE courseid = 0");
    $default_text = nl2br($default_text);
    $one_day_default = $DB->get_field_sql("SELECT enabled FROM mdl_notifikace WHERE courseid = 0");
    $three_day_default = $DB->get_field_sql("SELECT enabled FROM mdl_notifikace WHERE courseid = 0");
    $seven_day_default = $DB->get_field_sql("SELECT enabled FROM mdl_notifikace WHERE courseid = 0");
    $one_day_default =  $three_day = $seven_day = false;
    $values = $_POST;
    $one_day =  $three_day = $seven_day = "";
    $courseid = $values['courseid'];
    $one_day_default = $DB->get_field_sql("SELECT enabled FROM mdl_notifikace WHERE courseid = ".$courseid." AND type=1");
    $three_day_default = $DB->get_field_sql("SELECT enabled FROM mdl_notifikace WHERE courseid = ".$courseid." AND type=3");
    $seven_day_default = $DB->get_field_sql("SELECT enabled FROM mdl_notifikace WHERE courseid = ".$courseid." AND type=7");
    $one_day = $values['1-day'];
    $three_day = $values['3-day'];
    $seven_day = $values['7-day'];
    $text = $values['notifikace'];
    $text_nonformated = $values['notifikace'];
    $set_as_default = $values['set-default'];
 
 if (strstr($text_nonformated, '<') OR strstr($text_nonformated, '>') OR strstr($text_nonformated, '{') OR strstr($text_nonformated, '}'))
{ echo "<script>window.alert('Zakazane znaky!!');</script>";
redirect($CFG->wwwroot."/course/notifikace.php?id=".$courseid);

}else{}
 
 
    ($one_day) ? $one_day = 1 : $one_day = 0;
    ($three_day) ? $three_day = 1 : $three_day = 0;
    ($seven_day) ? $seven_day = 1 : $seven_day = 0;
    
    
    
    
    
    if($default_text != $text){
    $DB->execute("UPDATE mdl_notifikace SET text='".$text."' WHERE courseid=".$courseid."");
    
    }else{}
    
    if($one_day != $one_day_default OR $three_day != $three_day_default OR $seven_day!= $seven_day_default){
    $DB->execute("UPDATE mdl_notifikace SET enabled='".$one_day."' WHERE courseid=".$courseid." AND type=1");
    $DB->execute("UPDATE mdl_notifikace SET enabled='".$three_day."' WHERE courseid=".$courseid." AND type=3");
    $DB->execute("UPDATE mdl_notifikace SET enabled='".$seven_day."' WHERE courseid=".$courseid." AND type=7");
    
    }
    
    if($set_as_default == true){
    $notification_with_default_text = $DB->get_fieldset_sql("SELECT courseid FROM mdl_notifikace WHERE text='".$default_text_non_formated."' GROUP BY courseid");
    $notification_with_default_text = implode(",", $notification_with_default_text);
    $DB->execute("UPDATE mdl_notifikace SET text='".$text."' WHERE courseid IN(".$notification_with_default_text.")");
    }else{
    
    }
      
     
     
     

 

   echo "<a href='".$CFG->wwwroot."/course/view.php?id=".$courseid."'><button>".iconv( "Windows-1250", "UTF-8", ("Návrat do kurzu"))."</button></a> <a href='".$CFG->wwwroot."/course/notifikace.php?id=".$courseid."'><button>".iconv( "Windows-1250", "UTF-8", ("Návrat k notifikacím"))."</button></a><br><br>";
   echo iconv( "Windows-1250", "UTF-8", ('<b>Notifikace byla úspěšně nastavena !</b>'));
   
   
    
  // $DB->execute("UPDATE ");
  
  
   
   //takhle email to user funguje 
   // email_to_user($USER,iconv( "Windows-1250", "UTF-8", ('Neodpovídejte na tento e-mail přes eso')),iconv( "Windows-1250", "UTF-8", ('Notifikace o odhlášení z kurzu')),'The text of the message',$desc);

   
    
    
    echo $OUTPUT->footer();
