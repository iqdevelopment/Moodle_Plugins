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
    
    $bbb_instance = $DB->get_record_sql("SELECT id,course,name,meetingid FROM {bigbluebuttonbn} WHERE course = ".$id." AND id = ".$bbbid."");
        // sem p5idat jquerry
        echo alter_bbb_attendance($bbb_instance);
       
        
   
   
    echo $OUTPUT->footer();
