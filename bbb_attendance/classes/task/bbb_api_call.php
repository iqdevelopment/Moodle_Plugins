<?php
namespace local_bbb_attendance\task;

use local_bbb_attendance\tasklib;
defined('MOODLE_INTERNAL') || die();


 
/**
 * An example of a scheduled task.
 */
class bbb_api_call extends \core\task\scheduled_task {
 
    /**
     * Return the task's name as shown in admin screens.
     *
     * @return string
     */
    public function get_name() {
        return get_string('taskname', 'local_bbb_attendance');
    }
 
    /**
     * Execute the task.
     */
    public function execute() { 
        global $DB,$CFG;
       function bbbWriteMeetings($meeting){

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




function fetchFromServer($server_url, $salt, $method, $data){

    ksort($data);
    $params = "";
    foreach ($data as $key => $value) {
        $params .= $key . '=' . urlencode($value) . "&";
    }
    $params = rtrim($params, "&");
    $params = ltrim($params, "=");

    $sign = sha1($method . $params . $salt);

    $url = $server_url . "api/". $method . "?" . $params .'&checksum=' . $sign;

    echo $url . "<br>";

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

    $server_url = $DB->get_field_sql('SELECT value FROM {config} WHERE name = "bigbluebuttonbn_server_url"');
    $server_url = str_replace(' ','',$server_url);
    $salt = $DB->get_field_sql('SELECT value FROM {config} WHERE name = "bigbluebuttonbn_shared_secret"');
    $salt = str_replace(' ','',$salt);
    $method = "getMeetings";
    $data = array(
	//'meetingID' => 'e43f167698f70501df7c81654ac12aa3f076415c-18-3',
    );





$result = fetchFromServer($server_url, $salt, $method, $data);
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
}