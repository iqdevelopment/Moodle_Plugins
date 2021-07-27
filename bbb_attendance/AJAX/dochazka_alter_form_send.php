<?php

require_once('../../../config.php');
require_once('../lib.php');
//require_once('custom_lib.php');
require_once($CFG->libdir.'/completionlib.php');

require_once('../classes/altered_attendance/altered_attendance.php');
require_once('../classes/original_attendance/original_attendance.php');
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

$courseid = $_POST['courseid'];
$bbbid = $_POST['bbbid'];
//print_r($_POST);
$alternation_array = $_POST;
array_shift($alternation_array); 
array_shift($alternation_array);
$BBB_header = $DB->get_field_sql('SELECT id FROM {bbb_attendance_headers} WHERE bigbluebuttonid = '.$bbbid.'
AND courseid = '.$courseid.'');
foreach ($alternation_array as $key => $value) {
    if(empty($value)){continue;}

    if($key == 'alteredstartdate' OR $key == 'alteredenddate'){
        $sql = 'SELECT '.$key.' FROM {bbb_attendance_headers} 
         WHERE bigbluebuttonid = '.$bbbid.'
         AND courseid = '.$courseid.'';
        //potřebuju funkci, která zjistí ten čas
        $real_time = $DB->get_field_sql($sql);

       
                $sql = 'UPDATE {bbb_attendance_headers} SET '.$key.' = '.transferTime($real_time,$value).'
                WHERE bigbluebuttonid = '.$bbbid.'
                AND courseid = '.$courseid.'';
           // echo    $sql."<br>";
                $DB->execute($sql);
          
        }else{
            $array = explode('-',$key);
            $userid = $array[0];
            $field = $array[1];
           // print_r($array);

            if($field == 'altered_note'){
                if($value == 'on'){
                    $value = '""';
                    }
                $sql = 'UPDATE {bbb_attendance_userdata} SET '.$field.' = '.$value.' WHERE userid = '.$userid.' AND bbb_header = '.$BBB_header.'';
                $DB->execute($sql);
                //echo $sql."<br>";

            }elseif($field == 'altered_percentage'){  
                //$real_time = $DB->get_field_sql('SELECT '.$field.' FROM {bbb_attendance_userdata} WHERE userid = '.$userid.' AND bbb_header = '.$BBB_header.'');

            $sql = 'UPDATE {bbb_attendance_userdata} SET '.$field.' = '.$value.' WHERE userid = '.$userid.' AND bbb_header = '.$BBB_header.'';
                $DB->execute($sql);
                // echo $sql."<br>";    

            }else{
                $real_time = $DB->get_field_sql('SELECT '.$field.' FROM {bbb_attendance_userdata} WHERE userid = '.$userid.' AND bbb_header = '.$BBB_header.'');

            $sql = 'UPDATE {bbb_attendance_userdata} SET '.$field.' = '.transferTime($real_time,$value).' WHERE userid = '.$userid.' AND bbb_header = '.$BBB_header.'';
                $DB->execute($sql);
               // echo $sql."<br>";
            }
            }

} 

echo $OUTPUT->header();
$url_dochazka= new moodle_url('/local/bbb_attendance/attendance.php', array('id'=>$courseid,'bbbid' => $bbbid));
$url_alter= new moodle_url('/local/bbb_attendance/attendance_setup.php', array('id'=>$courseid,'bbbid' => $bbbid));
echo "<h2>Změny byly provedeny.</h2><br><br>";
echo '<a href="'.$url_dochazka.'"><button>Zpět k docházce</button></a>';
echo '<a href="'.$url_alter.'"><button>Zpět k úpravám</button></a>';
echo $OUTPUT->footer();

