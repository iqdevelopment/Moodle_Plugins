<?php
require_once('./../../config.php');

global $DB,$PAGE,$USER,$CFG;


if(isset($_POST['code']) AND isset($_POST['session']) AND $_POST['session'] == $USER->sesskey){

    $check_code = $DB->get_fieldset_sql('SELECT instanceid FROM {customfield_data} WHERE fieldid = 17 AND value = ?',array($_POST['code']));
    if($check_code AND $_POST['code'] != ""){
        syncEnrols($check_code);
        
        




    }else{
        $urltogo = $CFG->wwwroot.'/local/eso_custom_tasks/vicedenni.php';
        redirect($urltogo,'Tento kod neexistuje, použijte prosím kód z tabulky níže.',0);
    }
    



}else{
    $urltogo = $CFG->wwwroot.'/local/eso_custom_tasks/vicedenni.php';
    redirect($urltogo,'Neplatný požadavek.',0); 
}




function syncEnrols($course_array)
{   
    global $DB,$CFG;
    $count = 0;
    require($CFG->dirroot.'/enrol/self/lib.php');
    $sql_enrol = 'INSERT INTO {user_enrolments} (status, enrolid, userid, timestart, timeend, modifierid, timecreated, timemodified) VALUES (?,?,?,?,?,?,?,?)';
    $sql_users_another_self = 'SELECT userid FROM {user_enrolments} WHERE enrolid IN (
        SELECT id FROM {enrol} WHERE courseid IN ('.implode(',',$course_array).') AND courseid != ? AND enrol="self"
    GROUP BY userid) ' ;

    $sql_users_this_self = 'SELECT userid FROM {user_enrolments} WHERE enrolid IN (
        SELECT id FROM {enrol} WHERE  courseid = ? AND enrol="self"
    ) ' ;

    foreach ($course_array as $key => $course) {
        
       $users_from_another_courses = $DB->get_fieldset_sql($sql_users_another_self,array($course));
       
       $users_from_this = $DB->get_fieldset_sql($sql_users_this_self,array($course));
       
       $enrol_id = $DB->get_field_sql('SELECT id FROM {enrol} WHERE  courseid = ? AND enrol="self"',array($course));
       $sql = 'SELECT * FROM {enrol} WHERE courseid = ? AND enrol = "self"';
       $instance = $DB->get_record_sql($sql,array($course));
  

       foreach ($users_from_another_courses as $user) {

           if(!in_array($user,$users_from_this)){
            
               //echo 'zapis u:' .$user . ' eid:'.$enrol_id;
               $enrol = enrol_get_plugin($instance->enrol);
              
                $enrol->enrol_user($instance, $user, 5 );
               $count++;
               // $DB->execute($sql_enrol,array(0,$enrol_id,$user,time(),0,0,time(),time()));
           }
       }

      

    }


    $urltogo = $CFG->wwwroot.'/local/eso_custom_tasks/vicedenni.php';
    $message = 'Úspěšně bylo synchronizovano '.$count.' zápisů';
    //echo $count;
    redirect($urltogo,$message,0);


    
}
