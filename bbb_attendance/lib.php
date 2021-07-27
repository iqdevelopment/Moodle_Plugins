<?php
defined('MOODLE_INTERNAL') || die;
global $DB,$PAGE, $CFG;
require_once($CFG->dirroot.'/user/profile/lib.php');



function alter_bbb_attendance($instance){
  global $DB;
    //$bbb_instance = $DB->get_record_sql("SELECT id,course,name,meetingid FROM {bigbluebuttonbn} WHERE course = ".$id." AND id = ".$bbbid."");
    
    echo "<div class='results'></div>";
    $intervals = $DB->get_fieldset_sql("SELECT timestamp FROM {bbb_attendance} WHERE bigbluebuttonid = ".$instance->id." ORDER BY timestamp ASC");
   // print_r($intervals);
   echo "<link rel='stylesheet' href='assets/css/css.css'>"; 
   echo "<script src='./assets/js/dochazka_bbb.js'></script>";
   echo "<script>AttendanceAlterAjax(".$instance->course.",".$instance->id.");</script>";





  return $output;
}



function show_bbb_attendance($instance){
  global $DB;
    //$bbb_instance = $DB->get_record_sql("SELECT id,course,name,meetingid FROM {bigbluebuttonbn} WHERE course = ".$id." AND id = ".$bbbid."");
    
    echo "<div class='results'></div>";
    $intervals = $DB->get_fieldset_sql("SELECT timestamp FROM {bbb_attendance} WHERE bigbluebuttonid = ".$instance->id." ORDER BY timestamp ASC");
   // print_r($intervals);
   echo "<link rel='stylesheet' href='assets/css/css.css'>"; 
   echo "<script src='./assets/js/dochazka_bbb.js'></script>";
   
   echo "<script>startAttendanceGeneration();</script>";
   echo '<script src= "https://printjs-4de6.kxcdn.com/print.min.js"></script>';
   echo '<style src= "https://printjs-4de6.kxcdn.com/print.min.css"></style>';





  //return $output;
}

function get_enrolled_students($courseid){
global $DB;
$context = $DB->get_field_sql('SELECT id FROM {context} WHERE contextlevel = 50 AND instanceid = '.$courseid.'');
$users = $DB->get_fieldset_sql('SELECT userid FROM {role_assignments} WHERE roleid = 5 AND contextid = '.$context.' GROUP BY userid');
return $users;
}

function  get_enrolled_others($courseid){
   global $DB;
$context = $DB->get_field_sql('SELECT id FROM {context} WHERE contextlevel = 50 AND instanceid = '.$courseid.'');
$users = $DB->get_fieldset_sql('SELECT userid FROM {role_assignments} WHERE roleid != 5 AND contextid = '.$context.' GROUP BY userid');
return $users;
}   



/*****
 * 
 * render originalni vstup
 * 
 * 
 */




function render_bbb_attendance_for_user($userid,$course,$role,$start,$end,$realtime,$checked){
  global $DB;
  $user = $DB->get_record('user',array('id' => $userid));
  profile_load_data($user);
/*   $time_in_course = $DB->get_fieldset_sql('
  SELECT timestamp 
FROM {bbb_attendance} 
INNER JOIN {bbb_attendance_users} 
  ON {bbb_attendance_users}.bbb_attendance_id = {bbb_attendance}.id 
WHERE {bbb_attendance_users}.userid = '.$user->id.' 
AND {bbb_attendance}.timestamp >= '.$start.'
AND {bbb_attendance}.timestamp <= '.$end.'
AND {bbb_attendance}.courseid = '.$course->id.'
  GROUP BY timestamp
  ORDER BY timestamp ASC
  ');*/

     $time_in_course = $DB->get_field_sql('
  SELECT count(*) 
FROM {bbb_attendance} 
INNER JOIN {bbb_attendance_users} 
  ON {bbb_attendance_users}.bbb_attendance_id = {bbb_attendance}.id 
WHERE {bbb_attendance_users}.userid = '.$user->id.' 
AND {bbb_attendance}.timestamp >= '.$start.'
AND {bbb_attendance}.timestamp <= '.$end.'
AND {bbb_attendance}.courseid = '.$course->id.'
  ');

  $time_in_course_max = $DB->get_field_sql('
  SELECT max(timestamp) 
FROM {bbb_attendance} 
INNER JOIN {bbb_attendance_users} 
  ON {bbb_attendance_users}.bbb_attendance_id = {bbb_attendance}.id 
WHERE {bbb_attendance_users}.userid = '.$user->id.' 
AND {bbb_attendance}.timestamp >= '.$start.'
AND {bbb_attendance}.timestamp <= '.$end.'
AND {bbb_attendance}.courseid = '.$course->id.'

  ');

  $time_in_course_min = $DB->get_field_sql('
  SELECT min(timestamp) 
FROM {bbb_attendance} 
INNER JOIN {bbb_attendance_users} 
  ON {bbb_attendance_users}.bbb_attendance_id = {bbb_attendance}.id 
WHERE {bbb_attendance_users}.userid = '.$user->id.' 
AND {bbb_attendance}.timestamp >= '.$start.'
AND {bbb_attendance}.timestamp <= '.$end.'
AND {bbb_attendance}.courseid = '.$course->id.'

  ');

/*   $time_in_course_check = $DB->get_fieldset_sql('
  SELECT timestamp 
FROM {bbb_attendance} 
INNER JOIN {bbb_attendance_users} 
  ON {bbb_attendance_users}.bbb_attendance_id = {bbb_attendance}.id 
WHERE {bbb_attendance_users}.userid = '.$user->id.' 
AND {bbb_attendance}.timestamp >= '.$start.'
AND {bbb_attendance}.timestamp <= '.$end.'
  ORDER BY timestamp ASC
  ');  */

  $time_in_course_check = $DB->get_field_sql('SELECT count(*) 
  FROM {bbb_attendance} 
  INNER JOIN {bbb_attendance_users} 
    ON {bbb_attendance_users}.bbb_attendance_id = {bbb_attendance}.id 
  WHERE {bbb_attendance_users}.userid = '.$user->id.' 
  AND {bbb_attendance}.timestamp >= '.$start.'
  AND {bbb_attendance}.timestamp <= '.$end.'

    ');



//echo sizeof($time_in_course).';';

//$size = sizeof($time_in_course) -1;
$size = $time_in_course -1;
$time = $size*60;
if ($size == -1){
  $log_in = '/';
  $log_out = '/';
 // $render_time_comp = '0:00/'.date('G:i',$realtime);

}else{
/*   $log_in = date('G:i',$time_in_course[0]);
  $log_out = date('G:i',$time_in_course[$size]); */

  $log_in = date('G:i',$time_in_course_min);
  $log_out = date('G:i',$time_in_course_max);
    /*
  if($time < 3600){
    $time = $time+60;
    $render_time_comp = '0:'.date('i',$time).'/'.date('G:i',$realtime);
  }else{
  $render_time_comp = date('G:i',$time).'/'.date('G:i',$realtime);
   }
 // $render_time_comp = date('i',$time).'/'.date('G:i',$realtime);
*/
 
}
//$render_time_comp = round((sizeof($time_in_course)/$realtime)*100);
$render_time_comp = round(($time_in_course/$realtime)*100);
//$render_time_comp = sizeof($time_in_course)."/".$realtime;

//$render_time_comp_check = round((sizeof($time_in_course_check)/$realtime)*100);
$render_time_comp_check = round(($time_in_course_check/$realtime)*100);


if ($render_time_comp_check > 101){
          $results = $DB->get_fieldset_sql('
          SELECT courseid 
        FROM {bbb_attendance} 
        INNER JOIN {bbb_attendance_users} 
          ON {bbb_attendance_users}.bbb_attendance_id = {bbb_attendance}.id 
        WHERE {bbb_attendance_users}.userid = '.$user->id.' 
        AND {bbb_attendance}.timestamp >= '.$start.'
        AND {bbb_attendance}.timestamp <= '.$end.'
        AND {bbb_attendance}.courseid != '.$course->id.'
          GROUP BY courseid
          ');
  //print_r($result);
  foreach($results as $res){
    $result = 'uživatel byl v tuto dobu účastníkem i kurzu s id: '.$res;
  }

 // $result = implode(',',$result);
  $render_time_comp_check = '<td class="to-hide">'.$result.'</td>';
}else{
  $render_time_comp_check = ''; 
}


//$render_time_comp = $size.' / '.$realtime;
// $render_time_comp = sizeof($time_in_course);
if($role == 'moderátor'){
  $checkbox = '>'; 
  $tr_class = 'moderator';
 
}else{

$checkbox = 'class="check"><input type="checkbox" value='.$user->id.' name = "selectedusers[]" '.$checked.' class="checkbox">';
$tr_class = 'student';
}


  
$output = '<tr class="'.$tr_class.'">
<td '.$checkbox.'</td>
<td>'.$role.'</td>
<td>'.$user->firstnamephonetic.'</td>
<td>'.$user->firstname.'</td>
<td>'.$user->lastname.'</td>
<td>'.$user->lastnamephonetic.'</td>
<td>'.date('d.m.Y',$user->profile_field_Datum_narozeni).'</td>
<td class="to-hide">'.$user->profile_field_Obec.'</td>
<td class="to-hide">'.$user->profile_field_mistozam.'</td>
<td>'.$user->email.'</td>
<td class="to-hide">'.$course->fullname.'</td>
<td class="to-hide">'.date('d.m.Y',$course->startdate).'</td>
<td>'.$log_in.'</td>
<td>'.$log_out.'</td>
<td class="to-hide" id="realtime">'.$render_time_comp.'%</td>
'.$render_time_comp_check.'
</tr> ';

return $output;
}

 

/*****
 * 
 * render upraveny vstup
 * 
 * 
 */

function get_initial_sql_attendance_for_user($userid,$course,$role,$start,$end,$realtime,$headerId){
  global $DB;
  $user = $DB->get_record('user',array('id' => $userid));

  $time_in_course = $DB->get_fieldset_sql('
      SELECT timestamp 
      FROM {bbb_attendance} 
      INNER JOIN {bbb_attendance_users} 
      ON {bbb_attendance_users}.bbb_attendance_id = {bbb_attendance}.id 
      WHERE {bbb_attendance_users}.userid = '.$user->id.' 
      AND {bbb_attendance}.timestamp >= '.$start.'
      AND {bbb_attendance}.timestamp <= '.$end.'
      AND {bbb_attendance}.courseid = '.$course->id.'
      GROUP BY timestamp
      ORDER BY timestamp ASC
  ');

  $time_in_course_check = $DB->get_fieldset_sql('
      SELECT timestamp 
      FROM {bbb_attendance} 
      INNER JOIN {bbb_attendance_users} 
      ON {bbb_attendance_users}.bbb_attendance_id = {bbb_attendance}.id 
      WHERE {bbb_attendance_users}.userid = '.$user->id.' 
      AND {bbb_attendance}.timestamp >= '.$start.'
      AND {bbb_attendance}.timestamp <= '.$end.'
      ORDER BY timestamp ASC
  ');


$size = sizeof($time_in_course) -1;
$time = $size*60;
if ($size == -1){
  $log_in = -1;
  $log_out = -1;


}else{
  $log_in = $time_in_course[0];
  $log_out = $time_in_course[$size];
 
}
$render_time_comp = round((sizeof($time_in_course)/$realtime)*100);

$render_time_comp_check = round((sizeof($time_in_course_check)/$realtime)*100);


if ($render_time_comp_check > 101){
          $results = $DB->get_fieldset_sql('
          SELECT courseid 
          FROM {bbb_attendance} 
          INNER JOIN {bbb_attendance_users} 
          ON {bbb_attendance_users}.bbb_attendance_id = {bbb_attendance}.id 
          WHERE {bbb_attendance_users}.userid = '.$user->id.' 
          AND {bbb_attendance}.timestamp >= '.$start.'
          AND {bbb_attendance}.timestamp <= '.$end.'
          AND {bbb_attendance}.courseid != '.$course->id.'
          GROUP BY courseid
          ');
      }

  if(!empty($results)){
    $note = '';
    foreach($results as $res){
      $note .= 'uživatel byl v tuto dobu účastníkem i kurzu s id: '.$res.', ';
    }
  }

$userSQL = 'INSERT INTO {bbb_attendance_userdata} 
(bbb_header,userid,user_role,real_connection_startdate,
real_connection_enddate,altered_connection_startdate,altered_connection_enddate,real_percentage,
altered_percentage,real_note,altered_note) 
VALUES 
('.$headerId.','.$user->id.','.$role.','.$log_in.',
'.$log_out.','.$log_in.','.$log_out.','.$render_time_comp.',
'.$render_time_comp.',"'.$note.'","'.$note.'") ';

return $userSQL;


}




function render_altered_bbb_attendance_for_user($userid,$course,$role,$start,$end,$alteredHeader,$checked){
  global $DB;
  $user = $DB->get_record('user',array('id' => $userid));
  profile_load_data($user);
  $altered = $DB->get_record_sql('SELECT * FROM {bbb_attendance_userdata} WHERE userid = '.$user->id.' AND bbb_header = '.$alteredHeader.'');
  
//test jestli byl uzivatel pripojen
  if ($altered->altered_connection_startdate < 1){
    $log_in = '/';
    $log_out = '/';

  }else{
    $log_in = date('G:i',$altered->altered_connection_startdate);
    $log_out = date('G:i',$altered->altered_connection_enddate);
  }


//$render_time_comp = $size.' / '.$realtime;
// $render_time_comp = sizeof($time_in_course);
if($role == 'moderátor'){
  $checkbox = '>'; 
  $tr_class = 'moderator';
 
}else{

$checkbox = 'class="check"><input type="checkbox" value='.$user->id.' name = "selectedusers[]" '.$checked.' class="checkbox">';
$tr_class = 'student';
}


  
$output = '<tr class="'.$tr_class.'">
<td '.$checkbox.'</td>
<td>'.$role.'</td>
<td>'.$user->firstnamephonetic.'</td>
<td>'.$user->firstname.'</td>
<td>'.$user->lastname.'</td>
<td>'.$user->lastnamephonetic.'</td>
<td>'.date('d.m.Y',$user->profile_field_Datum_narozeni).'</td>
<td class="to-hide">'.$user->profile_field_Obec.'</td>
<td class="to-hide">'.$user->profile_field_mistozam.'</td>
<td>'.$user->email.'</td>
<td class="to-hide">'.$course->fullname.'</td>
<td class="to-hide">'.date('d.m.Y',$course->startdate).'</td>
<td>'.$log_in.'</td>
<td>'.$log_out.'</td>
<td id="realtime" class="to-hide">'.$altered->altered_percentage.'%</td>';

if(!empty($altered->altered_note)){$output .= '<td>'.$altered->altered_note.'</td>';}
$output .='</tr> ';

return $output;
}



/*****
 * 
 * 
 * 
 * 
 */


function render_altered_to_setup_bbb_attendance_for_user($userid,$course,$role,$start,$end,$alteredHeader,$checked){
  global $DB;
  $user = $DB->get_record('user',array('id' => $userid));
  profile_load_data($user);
  $altered = $DB->get_record_sql('SELECT * FROM {bbb_attendance_userdata} WHERE userid = '.$user->id.' AND bbb_header = '.$alteredHeader.'');
  
//test jestli byl uzivatel pripojen
  if ($altered->altered_connection_startdate < 1){
    $log_in = '';
    $real_log_in = '';
    $log_out = '';
    $real_log_out = '';

  }else{
    $log_in = date('H:i',$altered->altered_connection_startdate);
    $real_log_in = date('H:i',$altered->real_connection_startdate);
    $log_out = date('H:i',$altered->altered_connection_enddate);
    $real_log_out = date('H:i',$altered->real_connection_enddate);
  }


//$render_time_comp = $size.' / '.$realtime;
// $render_time_comp = sizeof($time_in_course);
if($role == 'moderátor'){
  $checkbox = '>'; 
  $tr_class = 'moderator';
 
}else{

$checkbox = 'class="check"><input type="checkbox" value='.$user->id.' disabled '.$checked. ' class="checkbox">';
$tr_class = 'student';
}

if($altered->real_note != $altered->altered_note){
  $checked_delete = 'checked';
}
  
$output = '<tr id="'.$user->id.'" class="'.$tr_class.'">
<td '.$checkbox.'</td>
<td>'.$role.'</td>
<td>'.$user->firstnamephonetic.'</td>
<td>'.$user->firstname.'</td>
<td>'.$user->lastname.'</td>
<td>'.$user->lastnamephonetic.'</td>
<td>'.$real_log_in.'</td>
<td><input  name="'.$user->id.'-altered_connection_startdate" class="startTimeChange"  type="time" value ="'.$log_in.'"></td>
<td>'.$real_log_out.'</td>
<td><input name="'.$user->id.'-altered_connection_enddate" class="endTimeChange" type="time" value = "'.$log_out.'"></td>
<td>'.$altered->real_percentage.'%</td>
<td><input name="'.$user->id.'-altered_percentage" type="number" min="0" max="100" style="width:5em"  placeholder = "'.$altered->altered_percentage.'%"></td>
<td>'.$altered->real_note.'</td>
<td><input type="checkbox" name= "'.$user->id.'-altered_note" '.$checked_delete.'></input></td>
</tr> ';

return $output;
}



/***
 * 
 * fce pro zaznam dochazky
 * 
 */

function bbbWriteMeetings($meeting){
echo "started bbbWriteMeetings() ";
  global $DB;
  $future_id = $DB->get_field_sql("SELECT max(id) FROM {bbb_attendance}")+1;
  $bbb_id =  explode('-',$meeting->meetingID)[2];
  $bbb_course =  explode('-',$meeting->meetingID)[1];
  
    if(empty($future_id)){
        $future_id = 0;
        }


    if(empty($bbb_id)){
        $bbb_id = 0;
        }
        
    if(empty($bbb_course)){
    $bbb_course = 0;
    }
    
      $sql = "INSERT INTO {bbb_attendance} 
                  (id,courseid,bigbluebuttonid,timestamp)
              values (".$future_id.",".$bbb_course.",".$bbb_id.",".time().")";
              
  $DB->execute($sql);
  echo sizeof($meeting->attendees->attendee);
  
  if(sizeof($meeting->attendees->attendee) > 1){
  $atendees = $meeting->attendees->attendee;
  //print_r($atendees);
  foreach ($atendees as $atendee){
  //print_r($atendee);
         if(empty($atendee->userID)){
  $atendee->userID = 0;
  
  }
     // echo "<br>zapisuju".$atendee->userID;
      $sql = "INSERT INTO {bbb_attendance_users} 
              (bbb_attendance_id,userid)
          values (".$future_id.",".$atendee->userID.")";
   $DB->execute($sql);  
  }
     }else{
      $atendees = $meeting->attendees;
       foreach ($atendees as $atendee){
                if(empty($atendee->userID)){
  $atendee->userID = 0;
  
  }
  //print_r($atendee);
    //  echo "<br>zapisuju".$atendee->userID;
      $sql = "INSERT INTO {bbb_attendance_users} 
              (bbb_attendance_id,userid)
          values (".$future_id.",".$atendee->userID.")";
   $DB->execute($sql); 
     
     }
     }
  $state = 'wroten '.time().' - courseid:'.$bbb_course. '';
  return ($state);

}



// funkce ktera se pripoji na API BBB, dle configu a vyhodi data ohledne probihajicich konferenci

function fetchFromServer($method, $data){
  global $DB;
  $server_url = $DB->get_field_sql('SELECT value FROM {config} WHERE name = "bigbluebuttonbn_server_url"');
   $salt = $DB->get_field_sql('SELECT value FROM {config} WHERE name = "bigbluebuttonbn_shared_secret"');
  // $server_url = "https://api.mynaparrot.com/bigbluebutton/iqdevelopment/";
  // $salt = "gTdddGZCzDgPhFXpiqON";

  ksort($data);
  $params = "";
  foreach ($data as $key => $value) {
      $params .= $key . '=' . urlencode($value) . "&";
  }
  $params = rtrim($params, "&");
  $params = ltrim($params, "=");

  $sign = sha1($method . $params . $salt);

  $url = $server_url . "api/". $method . "?" . $params .'&checksum=' . $sign;

  //echo $url . "<br>";
   $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);
    curl_setopt($ch, CURLOPT_TIMEOUT, 90); //timeout in seconds
    $response = curl_exec($ch);
    curl_close ($ch);

  
    
  if(!$response){

      return curl_error($ch);
  }
    
  try {
      $xml = simplexml_load_string($response);
      return json_decode(json_encode($xml));
  } catch (Exception $e) {
      return $e->getMessage();
  }
}




// funkce obsaravajici logiku cronu 
function bbbAtendanceLog(){
echo "started bbbAttendanceLog() ";
           $method = 'getMeetings';
          $result = fetchFromServer($method, $data);
          if(!empty($result->meetings->meeting)){
                $meetings = $result->meetings->meeting;
              $check = is_array($meetings);
                if ($check){
                foreach ($meetings as $meeting){
                echo "mnoho meetingu";
                    bbbWriteMeetings($meeting);
                }
            }else{
                $meeting = $meetings;
                echo "jeden meeting";
                bbbWriteMeetings($meeting);

            }
          }
}


/***
 * 
 * konec fce pro zaznam dochazky
 * 
 */

/*****
 * 
 * 
 * převod časů pro zapsání do DB
 * 
 */

function transferTime($real_time,$altered_time){
       
  $altered_time= explode(':',$altered_time);
  $new_minutes =$altered_time[1];
  $new_hours = $altered_time[0];
  $hours = date('H',$real_time);
  $minutes = date('i',$real_time);
  $hours_difference = $new_hours-$hours;
  $minutes_difference = $new_minutes-$minutes;
  $total_difference = ($hours_difference * 3600)+($minutes_difference * 60);
  return $real_time+$total_difference;
}



function render_bbb_attendance_for_user_new($userid,$course,$role,$start,$end,$timepoints,$checked,$object,$scamers=null){
  global $DB;
  $user = $DB->get_record('user',array('id' => $userid));
  profile_load_data($user);

  $user_data = array();

  foreach ($object as $value) {
      if( $value->userid == $userid){
        $user_data[] = $value;
      }

      
  }


  if(sizeof($user_data) == 0){
      $log_in = '/';
      $log_out = '/';
      $render_time_comp = 0;
      $render_time_comp_check = '';
    
  }else{
      $render_time_comp = sizeof($user_data);
      $log_in = date('G:i',$user_data[0]->timestamp);
      $log_out = date('G:i',$user_data[sizeof($user_data)-1]->timestamp);
     // $percentage = round((($end-$start)/120)/sizeof($user_data)*100);
      $percentage = round(sizeof($user_data)/(($timepoints))*100);
      $render_time_comp = $percentage;
      $render_time_comp_check = '';
  }
  


if ($percentage > 10 AND $scamers != null){
  $results = array();
        foreach ($scamers as $key => $value) {
            if($value->userid == $userid){
              $results[] = $value->courseid;
            }
        }


          
  //print_r($result);
  foreach($results as $res){
    $result = 'uživatel byl v tuto dobu účastníkem i kurzu s id: '.$res;
  }

 // $result = implode(',',$result);
  $render_time_comp_check = '<td class="to-hide">'.$result.'</td>';
 
}else{
  $render_time_comp_check = ''; 
}

if( $render_time_comp > 100){
  $render_time_comp = 100;
}


//$render_time_comp = $size.' / '.$realtime;
// $render_time_comp = sizeof($time_in_course);

if($role == 'moderátor'){
  $checkbox = '>'; 
  $tr_class = 'moderator';
 
}else{

$checkbox = 'class="check"><input type="checkbox" value='.$user->id.' name = "selectedusers[]" '.$checked.' class="checkbox">';
$tr_class = 'student';
}


 

  
$output = '<tr class="'.$tr_class.'">
<td '.$checkbox.'</td>
<td>'.$role.'</td>
<td>'.$user->firstnamephonetic.'</td>
<td>'.$user->firstname.'</td>
<td>'.$user->lastname.'</td>
<td>'.$user->lastnamephonetic.'</td>
<td>'.date('d.m.Y',$user->profile_field_Datum_narozeni).'</td>
<td class="to-hide">'.$user->profile_field_Obec.'</td>
<td class="to-hide">'.$user->profile_field_mistozam.'</td>
<td>'.$user->email.'</td>
<td class="to-hide">'.$course->fullname.'</td>
<td class="to-hide">'.date('d.m.Y',$course->startdate).'</td>
<td>'.$log_in.'</td>
<td>'.$log_out.'</td>
<td class="to-hide" id="realtime">'.$render_time_comp.'%</td>
'.$render_time_comp_check.'
</tr> ';

return $output;
}