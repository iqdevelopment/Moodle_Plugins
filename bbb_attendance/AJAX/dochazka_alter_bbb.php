<?php

//  Display the course home page.

    require_once('../../../config.php');
    require_once('../lib.php');
    //require_once('custom_lib.php');
    require_once($CFG->libdir.'/completionlib.php');

    require_once('../classes/altered_attendance/altered_attendance.php');
    require_once('../classes/original_attendance/original_attendance.php');
    global $DB,$USER,$CFG;
    

if(isset($_POST))
{
$data = $_POST["data"];
$data = explode('-',$data);
$course = $DB->get_record_sql('select * from {course} WHERE ID = '.$data[0].'');
$instance_id =  $DB->get_field_sql("SELECT id FROM mdl_grade_items WHERE courseid = ".$course->id." AND itemname = 'dochazka'");
$courseid = $data[0];
if ($data[1] == 'null'){
        $bbbid = $DB->get_field_sql('SELECT id FROM {bigbluebuttonbn} WHERE course = '.$course->id.'');
    }else{
        $bbbid = $data[1];
}

  //check jestli jsou k dispozici upravena data

  $alteredHeader = $DB->get_field_sql('SELECT id FROM {bbb_attendance_headers} WHERE bigbluebuttonid = '.$bbbid.' AND courseid = '.$courseid.'');


      if(!empty($alteredHeader)){
        $attendance = new Altered_attendance($courseid,$bbbid);
        $attendance->getInitialData();
        $output = $attendance->renderBBBAttendanceToAlter();




      }else{
        unset($alteredHeader);
        $attendance = new Original_attendance($courseid,$bbbid);
        $attendance->getInitialData();

        
        /***
         * 
         * Check, jestli existuji spravna data k online dochazce
         */


       $attendance->createBBBAttendanceToAlter();
              

      $output = 'reload';
      }
  echo json_encode($output);
 }
 