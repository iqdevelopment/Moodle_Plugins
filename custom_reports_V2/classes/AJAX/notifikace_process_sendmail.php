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
      

    $values = $_POST;
    $text = $values['notifikace'];
    $text_nonformated = $values['notifikace'];
    $courseid = $values['courseid'];
    $text = nl2br($text);
    //$predmet = iconv( "Windows-1250", "UTF-8", ($values['predmet']));
    $predmet = $values['predmet'];
      
     
     
     

 

   echo "<a href='".$CFG->wwwroot."/course/view.php?id=".$courseid."'><button>".iconv( "Windows-1250", "UTF-8", ("Návrat do kurzu"))."</button></a> <a href='".$CFG->wwwroot."/course/notifikace.php?id=".$courseid."'><button>".iconv( "Windows-1250", "UTF-8", ("Návrat k notifikacím"))."</button></a><br><br>";
 
if (strstr($text_nonformated, '<') OR strstr($text_nonformated, '>') OR strstr($text_nonformated, '{') OR strstr($text_nonformated, '}'))
{ echo "<script>window.alert('Zakazane znaky!!');</script>";
redirect($CFG->wwwroot."/course/notifikace.php?id=".$courseid);

}else{}
   
   
   
   // nabrání objektu course a skupiny uživatelů
   $teachers = $students = $contacs = array(); 
   $course = $DB->get_record('course',array('id' => $courseid));
  
   $contextid = $DB->get_field_sql("SELECT id FROM mdl_context WHERE contextlevel = 50 AND instanceid = ".$course->id."");
   $context = context::instance_by_id($contextid, MUST_EXIST);
   $user_records = get_enrolled_users($context, '', 0, '*');
  
   //$notification = $notification_record->text;
   $notification = $text;
  
   $daystostart = round(($course->startdate - time())/(60*60*24));
 
   if($daystostart == 7){
    $daystostart_words = "7 dní";
   }elseif($daystostart == 3){
    $daystostart_words = "3 dny";
   }elseif($daystostart == 1){
    $daystostart_words = "1 den";
   }elseif($daystostart == 2){
    $daystostart_words = "2 dny";
   }elseif($daystostart == 4){
    $daystostart_words = "4 dny";
   }else{
   $daystostart_words = $daystostart." dní";
   } 
  

   
 
        //seznam všechn přesivoatelných hodnot
    $notification = str_replace('[nazev_kurzu]',$course->fullname,$notification);
    $notification = str_replace('[dny_do_zacatku]',iconv( 'Windows-1250', 'UTF-8', ($daystostart_words)),$notification);
    $notification = str_replace('[zacatek_kurzu]',date('d.m.Y H:i',$course->startdate),$notification);
    $notification = str_replace('[zacatek_kurzu_datum]',date('d.m.Y',$course->startdate),$notification);
    $notification = str_replace('[zacatek_kurzu_cas]',date('H:i',$course->startdate),$notification);
    $notification = str_replace('[konec_kurzu]',date('H:i',$course->enddate),$notification);
    $notification = str_replace('[konec_kurzu_datum]',date('d.m.Y H:i',$course->enddate),$notification);
    //$notification = str_replace('[url_kurzu]',$course->fullname,$notification);
    $notification = str_replace('[url_kurzu]','<a href="'.$CFG->wwwroot.'/course/view.php?id='.$course->id.'">'.$course->fullname.'</a>',$notification);
    $notification = str_replace('[lokace_kurzu]',get_course_city($course->id),$notification);
    $notification = str_replace('[lokace_kurzu_detail]',$DB->get_field_sql("SELECT value FROM mdl_customfield_data WHERE fieldid = 12  AND instanceid=".$course->id.""),$notification);
    //konec všech přepisovatelných hodnot
    
            //seznam všechn přesivoatelných hodnot
    $predmet = str_replace('[nazev_kurzu]',$course->fullname,$predmet);
    $predmet = str_replace('[dny_do_zacatku]',iconv( 'Windows-1250', 'UTF-8', ($daystostart_words)),$predmet);
    $predmet = str_replace('[zacatek_kurzu]',date('d.m.Y H:i',$course->startdate),$predmet);
    $predmet = str_replace('[konec_kurzu]',date('H:i',$course->enddate),$predmet);
    $predmet = str_replace('[konec_kurzu_datum]',date('d.m.Y H:i',$course->enddate),$predmet);
    //$predmet = str_replace('[url_kurzu]',$course->fullname,$predmet);
    $predmet = str_replace('[url_kurzu]','<a href="'.$CFG->wwwroot.'/course/view.php?id='.$course->id.'">'.$course->fullname.'</a>',$predmet);
    $predmet = str_replace('[lokace_kurzu]',get_course_city($course->id),$predmet);
    $predmet = str_replace('[lokace_kurzu_detail]',$DB->get_field_sql("SELECT value FROM mdl_customfield_data WHERE fieldid = 12  AND instanceid=".$course->id.""),$predmet);
    //konec všech přepisovatelných hodnot
   
  foreach ($user_records as $senduser){
 // $student = $studentid;
   
    email_to_user($senduser,iconv( "Windows-1250", "UTF-8", ('Neodpovídejte na tento e-mail přes eso')),$predmet,'The text of the message',$notification);
    echo iconv( "Windows-1250", "UTF-8", ('Úspěšně odesláno uživateli: ')).$senduser->firstname." ".$senduser->lastname." - ".$senduser->email."<br>";
    }
    $DB->execute("INSERT INTO mdl_notifikace (courseid,type,sent,enabled,text,sendtime) values 
    (".$course->id.",
    0,
    1,
    ".$USER->id.",
    '".$values['notifikace']."',
    ".time()."
    )");
  
 
   echo "<br><br>";
   echo iconv( 'Windows-1250', 'UTF-8', ('<b>Náhled:</b><br>'));
   echo iconv( 'Windows-1250', 'UTF-8', ('Předmět:')).$predmet."<br>";
   echo iconv( 'Windows-1250', 'UTF-8', ('Zpráva:')).$notification."<br><br>";
   
   echo iconv( 'Windows-1250', 'UTF-8', ('Událost byla zalogována pod Vaším ID:')).$USER->id." - ".$USER->username." - ".$USER->email;
   
   
   
    
    
    echo $OUTPUT->footer();
