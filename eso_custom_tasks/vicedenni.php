<?php

require_once('./../../config.php');

global $DB,$PAGE,$USER,$CFG;

        // seznam všech, kteří jsou na nějaký kurz zapsaní jinak, než uživatel - kvůli tomu, aby nemohli všichni vyhledávat podle tvůrce 
        $course_contacts = $DB->get_fieldset_sql("SELECT userid FROM mdl_role_assignments WHERE roleid IN (1,2,3,4,11,12) GROUP BY userid");
       // $course_contacts .= $DB->get_field_sql("SELECT value FROM mdl_config WHERE name='siteadmins'");
      $to_push = $DB->get_field_sql("SELECT value FROM mdl_config WHERE name='siteadmins'");
         $to_push = explode(",", $to_push);
       $course_contacts =  array_merge($course_contacts,$to_push);
       
          if (in_array($USER->id,$course_contacts)){}else{redirect(get_login_url());}; 

 $context = context_system::instance();
$PAGE->set_context($context);
$PAGE->set_pagelayout('standard');
//$PAGE->set_title(get_string("pluginname", 'block_search_course'));
$PAGE->set_heading('Kontrola duplicit');
$pageurl = '/vicedenni.php';
$PAGE->set_url($pageurl);
//$PAGE->navbar->ignore_active();
//$PAGE->navbar->add(get_string("pluginname", 'block_search_course'));
$PAGE->set_cacheable(false);

//$PAGE->set_pagetype('site-index');
//$PAGE->set_docs_path('');
//$editing = $PAGE->user_is_editing();
$PAGE->set_title($SITE->fullname);
$PAGE->set_heading($SITE->fullname);

//require_course_login($course);
echo $OUTPUT->header();

function rand_string( $length ) {

$chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
return substr(str_shuffle($chars),0,$length);

}

echo '<b>Návrh kódu: </b>'.rand_string(5).date('dmyy',time());
echo '<br><form action="vicedenni_process.php" method="POST">
    <label>Manuálně synchronizovat uživatele v kurzu s kódem:</label>
    <input type="text" name="code" placeholder="kod vicedennich kurzu"></input>
    <input type="hidden" name="session" value="'.$USER->sesskey.'"></input>
    <input type="submit" value="synchronizovat"></input>
    </form>';

$vicedennikody = $DB->get_fieldset_sql("SELECT value FROM mdl_customfield_data WHERE fieldid=17 AND value != '' GROUP BY value");
echo '<br><br><h3>Přehled použitých kódů:</h3>';
echo "<table border='1' >";
echo "<tr><td colspan='4'>".'Kód'."</td><td>ID</td><td>Shortcode a odkaz</td><td>Datum kurzu</td></tr>";
foreach ($vicedennikody as $kod){
    $courses = $DB->get_fieldset_sql("SELECT instanceid FROM mdl_customfield_data WHERE fieldid=17 AND value = '".$kod."' ");
    $n = count($courses);
     $n++;
    echo "<tr><td rowspan=".$n." colspan='4'><b>".$kod."</b></td></tr>" ;
    //
        foreach ($courses as $course) {
        $courseinfo = $DB->get_record('course',['id'=>$course]);
         echo "<tr><td>".$courseinfo->id."</td><td><a href='".$CFG->wwwroot."/course/view.php?id=".$course."'>".$courseinfo->shortname."</a></td><td>".date('d.m.Y',$courseinfo->startdate)."</td></tr>";
        }   
 


    }
    echo "</table>";

  
 
 



echo $OUTPUT->footer();  
       
       
       
        ?>
        
        