<?php 
// This file is part of Moodle - http://moodle.org/
        //
        // Moodle is free software: you can redistribute it and/or modify
        // it under the terms of the GNU General Public License as published by
        // the Free Software Foundation, either version 3 of the License, or
        // (at your option) any later version.
        //
        // Moodle is distributed in the hope that it will be useful,
        // but WITHOUT ANY WARRANTY; without even the implied warranty of
        // MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
        // GNU General Public License for more details.
        //
        // You should have received a copy of the GNU General Public License
        // along with Moodle.  If not, see <http://www.gnu.org/licenses/>.
        
        
/** 
 * @package     local_bbb_meeting_overview 
 * @copyright     Simon Zajicek
 * @copyright     2021
 * @copyright     IQdevelopment

*/

namespace local_bbb_meeting_overview\API; 



defined ( 'MOODLE_INTERNAL' ) || die(); 
 
class API { 
 
     private function fetchFromServer($server_url, $salt, $method, $data)
        {     global $DB;  

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


        public function getMeetingAPIinfo($meetingobj)
        {       global $DB;  
                $server_url = $DB->get_field_sql('SELECT value FROM {config} WHERE name = ?',array('bigbluebuttonbn_server_url'));
                $salt = $DB->get_field_sql('SELECT value FROM {config} WHERE name = ?',array('bigbluebuttonbn_shared_secret'));
                $method = 'getMeetingInfo';
                $data = array('meetingID' => $meetingobj->meetingid.'-'.$meetingobj->courseid.'-'.$meetingobj->id); 
                $result = $this->fetchFromServer($server_url, $salt, $method, $data);
                //print_r($result);
                return $result;
                # code...
        }
 
 
 } 