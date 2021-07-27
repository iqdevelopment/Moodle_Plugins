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
 * @package     local_custom_mail_log 
 * @copyright     Simon Zajicek
 * @copyright     2021
 * @copyright     IQdevelopment.cz

*/

namespace local_custom_mail_log\Mailloger; 



defined ( 'MOODLE_INTERNAL' ) || die(); 
 
class Mailloger { 
 
        public function fillDatabase()
        {       global $DB,$CFG;
                $this->path = $DB->get_field_sql('SELECT value FROM {config_plugins} WHERE name = ?',array('mail_log_path'));
                $size_of_table = $DB->get_field_sql('SELECT count(*) FROM {custom_maillog}');
                $size_of_table = $size_of_table * 4;
                $array = explode("\n", htmlspecialchars(file_get_contents($this->path,FALSE,NULL,$size_of_table)));
               // echo \sizeof($array);
               $number_of_cycles_dev = 100;
               $initual_cycle_count = 0;
               $max_timestamp = $this->getMaxTimestamp();
                foreach ($array as $key => $line) {
                        $line_data = explode('scap-c05-moodle01',$line);
                        $validated_line = $this->validateLine($line_data[1]);
                        if(!$validated_line){
                               //skip non usefull lines
                                continue;
                        }
                       $object = $this->objectCreator($line_data[0],$line_data[1]);

                       $this->saveRecord($object,$max_timestamp);
                      /*  print_r($object);
                       echo '<br><br>'; */
                        

                       

/* 


                        //breaks on counter, for dev purposes
                        if($initual_cycle_count > $number_of_cycles_dev){
                                break;
                        }
                        $initual_cycle_count++;
                       //end of the break, remove to delete/comment this */
                }
        }



        /**
         * returns valid timestamp from mail log date string, if timestring would be greater than current time, it assumes that it was last year
         */
        private function getDate($dateString)
        {
                $time = \strtotime($dateString);
                if($time > time() ){
                        $num = date('Y',time())-1;
                        $time = \strtotime($dateString.' '.$num);
                }
                return $time;
        }

        /**
         * validates if line contains something usefull
         */
        private function validateLine($data)
        {       
                if(strpos($data,'to=')){
                        return $data; }

        }

        /**
         * chunks the data do usefull object
         */
        private function objectCreator($timestamp,$data)
        {       $timestamp = $this->getDate($timestamp);
                $retrunObject = $this->cleanAndChunk($data);
                $retrunObject->timestamp = $timestamp;
                return $retrunObject;

        }

        /**
         * chunks and clears mail info data
         */
        private function cleanAndChunk($data)
        {      
                
                $chunks =  \explode(':',$data);
                $retrunObject = $this->restOfTheData($chunks);
                
                $retrunObject->messageid = str_replace(' ','',$chunks[1]);

                $details = \explode(',',$chunks[2]);
                $retrunObject->recipient = $this->cleaner($details[0]);
                $retrunObject->relay = $this->cleaner($details[1]);


                return $retrunObject;

        }


        /**
         * strips of specialCharacters AND spaces
         */
        private function cleaner($field)
        {       $field = str_replace(' ','',$field);
                $field = str_replace('to=','',$field);
                $field = str_replace('&gt;','',$field);
                $field = str_replace('&lt;','',$field);
                $field = str_replace('relay=','',$field);

               return $field;
        }

        /**
         * analyse the rest of object accepts whole chunk array
         */
        private function restOfTheData($chunks)
        {       $retrunObject = new \stdClass();
                array_shift($chunks);
                array_shift($chunks);
                array_shift($chunks);
                $chunk_string = \implode(' ',$chunks);
                $chunk_array = explode('status=',$chunk_string);
                if(\sizeof($chunk_array)<2){
                        $status = $chunk_array[0];
                }else{
                        $status = $chunk_array[1];
                }
              
                if(strpos($status,'sent')  !== false){
                        $retrunObject->status = 'OK'; 
                        $retrunObject->detail = null; 
                        return $retrunObject;    
                }else{
                        $retrunObject = $this->nonOKmail($status); 
                        return $retrunObject;       
                }
        }

        /**
         * if email was not sent sucessfully, accepts $status AND returns status object with status AND details
         */
        private function nonOKmail(String $string = null)
        {       $retrunObject = new \stdClass();
               // echo $string;// $retrunObject
                if(strpos($string,'deferred')  !== false){
                        $retrunObject->status =  get_string('deferred', 'local_custom_mail_log'); 
                        $retrunObject->detail = \str_replace('deferred','',$string);
                        return $retrunObject;
                       
                }elseif(strpos($string,'bounced')  !== false){
                        $retrunObject->status =  get_string('bounced', 'local_custom_mail_log'); 
                        $retrunObject->detail = \str_replace('bounced','',$string);
                        return $retrunObject;
                       
                }elseif(strpos($string,'expired')  !== false){
                        $retrunObject->status =  get_string('expired', 'local_custom_mail_log'); 
                        $retrunObject->detail = \str_replace('expired','',$string);
                        return $retrunObject;
                       
                }else{
                        $retrunObject->status =  get_string('else', 'local_custom_mail_log'); 
                        $retrunObject->detail = $string;
                        return $retrunObject;   
                }
                
                 
        }

        /**
         * does the check if value exists and if no than saves it
         * accepts lineobject and timestamp of most recent value in the database
         */

        private function saveRecord(Object $object, Int $max_timestamp = null)
        {       global $DB;
                if($object->timestamp < $max_timestamp){
                        //just skip this
                }elseif ($object->timestamp == $max_timestamp) {
                        $check = $DB->get_field_sql('SELECT id FROM {custom_maillog} WHERE timestamp = ? AND messageid = ?',array($object->timestamp,$object->messageid));
                        if(!$check){ $this->inDB($object); }
                }else {
                        $this->inDB($object);
                }
        }


        /**
         * gets the recordset object for saveRecord method
         */
        private function getMaxTimestamp()
        {       global $DB;
                $max_object = $DB->get_field_sql('SELECT max(timestamp) FROM {custom_maillog}');
                return $max_object;
        }


        /**
         * DB save record to the DB
         */
        private function inDB(Object $object)
        {       global $DB;
                $sql = 'INSERT INTO {custom_maillog} (timestamp,status,messageid,recipient,relay,detail) VALUES (?,?,?,?,?,?)';
                $DB->execute($sql,array(
                        $object->timestamp,
                        $object->status,
                        $object->messageid,
                        $object->recipient,
                        $object->relay,
                        $object->detail 
                ));
        }


        /**
         * returns 100 newest logs, or next 100 logs lower than given id
         */
        public function getFreshInfo($id)
        {       global $DB;
                if($id < 1){
                        
                        $objects = $DB->get_records_sql('SELECT * FROM {custom_maillog} WHERE timestamp ORDER BY id DESC LIMIT 80');
                        return $this->reSortAndDate($objects);
                }else{
                        $objects = $DB->get_records_sql('SELECT * FROM {custom_maillog} WHERE timestamp AND id < ? ORDER BY id DESC LIMIT 40',array($id));
                        return $this->reSortAndDate($objects);
                }
        }

        /**
         * sorts array of objects
         */

         private function reSortAndDate(array $objects)
         {
                
                usort($objects, fn($a, $b) => strcmp($b->timestamp, $a->timestamp));
                 foreach ($objects as $key => $value) {
                        $value->timestamp = date('d.m.Y G:i',$value->timestamp);
                } 
                return $objects;
         }


        /**
         * get info about this message
         */
        public function getinfo($id)
        {      global $DB;
               $object = $DB->get_record_sql('SELECT * FROM {custom_maillog} WHERE id = ?',array($id));
               return $object;
        }


        /**
         * controls custom querry
         */
        public function customQuerry($querry)
        {       global $DB;
                if (preg_match('/[\'^£$%&*()}{#~?><>,|=+¬]/', $querry))
                        {
                            return 'invalid string';
                        }
                //string is clear can proceed
                $querry_array = explode(' ',$querry);

                return $this->querrybuilder($querry_array);;
              
        }



        /**
         * controls custom querry append
         */
        public function customQuerryAppend($object)
        {       global $DB;
                $sql = \str_replace('ORDER BY timestamp DESC LIMIT 100','',$object["sql"]);
                $sql = $sql. ' AND id < '.$object["id"] . ' ORDER BY timestamp DESC LIMIT 40';
                $retrunObject = new \stdClass();
                $retrunObject->id = $object["sql"];
                $sql_return = $DB->get_records_sql($sql);
                $retrunObject->data = $this->reSortAndDate($sql_return);
                

                return $retrunObject;
              
        }

        /**
         * build the querry
         *  */        
        private function querrybuilder($querry_array)
        {       global $DB;
                $q_array = array();
                foreach ($querry_array as $key => $value) {
                        $q_array[] = $this->queryAnalyser($value);
                }

                $output = $this->setUpQuerry($q_array);
                //return $q_array;
                return $output;
        }

        /**
         * analyses the query string and returns array 
         */
        private function queryAnalyser($string)
        {       global $DB;
                $retrunObject = new \stdClass();
                if(strpos($string,'.') AND strpos($string,'@')){
                        $retrunObject->type = 'email';
                        $retrunObject->data = $string;
                }elseif(strpos(' '.$string,'-')){
                        $retrunObject->type = 'range';
                        $retrunObject->data = null;
                }elseif(strpos($string,'.')){
                        $retrunObject->type = 'date';
                        $retrunObject->data = $this->checkDate($string);
                }else{
                        $retrunObject->type = 'user';
                        $retrunObject->data = $string;
                }
                return $retrunObject;
        }

        /**
         * to ensure the date is valid
         */
        private function checkDate($datestring)
        {       
                $check = explode('.',$datestring);

                if(sizeof($check) < 3 OR $check[2] == ""){
                        return 'invalid date format';
                }elseif(intval($check[0]) > 31 OR intval($check[0]) < 0 ){
                        return 'invalid day';
                }elseif(intval($check[1]) > 12 OR intval($check[1]) < 0 ){
                        return 'invalid month';
                }elseif(intval($check[2]) > 2050 OR intval($check[2]) < 2000 ){
                        return 'invalid year';
                }
                return strtotime($datestring);
        }


        /**
         * does the query itself together
         */
        private function setUpQuerry($array)
        {       global $DB;
                $complete_sql = 'SELECT * FROM {custom_maillog} WHERE ';
                $user_arr = array();
                $date_arr = array();
                $email_arr = array();

                foreach ($array as $key => $record) {
                        if($record->type == 'user'){
                                $user_arr[] = $record->data;     
                        }elseif($record->type == 'email'){
                                $email_arr[] = $record->data;     
                        }else{
                                $date_arr[] = $record;     
                        }
                }

                //process the date
                if($date_arr){
                        $complete_sql = $this->dateCheck($date_arr,$complete_sql);
                }


                //process the users

                if($user_arr OR $email_arr ){
                       $complete_sql = $this->userAndEailCheck($user_arr,$email_arr,$complete_sql);

                }

                $complete_sql .= ' ORDER BY timestamp DESC LIMIT 100';
                $retrunObject = new \stdClass();

                $retrunObject->sql = $complete_sql;
                $retrunObject->data = $this->reSortAndDate($DB->get_records_sql($complete_sql));
                return $retrunObject;
                
        }


        /**
         * accepts array with objects where type == date / range
         * if the array is OK it returns part of SQL querry, if not, it returns error message
         */
        private function dateCheck($date_arr,$complete_sql)
        {
                $range_check = FALSE;
                $dates = array();
                foreach ($date_arr as $key => $value) {
                        if($value->type == 'range'){
                                $range_check = TRUE;
                        }else{
                                $dates[] = $value->data;
                        }
                }
                //cheking correct inputs
                if(
                        (\sizeof($dates) == 2 AND $range_check == TRUE)
                        OR
                        (\sizeof($dates) == 1 AND $range_check == FALSE)
                )
                {      
                       if( (\sizeof($dates) == 1 AND $range_check == FALSE)){
                        $from = $dates[0];
                        $to    = $dates[0] + 86400;
                       }else{
                        $from = $dates[0];
                        $to = $dates[1] + 86400;
                       }

                       return $complete_sql . '( timestamp > '.$from .' AND timestamp < '.$to.' )';
                        
                }else{
                        return get_string('dates_err','local_custom_mail_log');
                }
        }


        /**
         * user and email check, returns completed SQL array
         */
        private function userAndEailCheck($user_arr,$email_arr,$complete_sql)
        {       global $DB;
                $user_emails = array();
                if(\sizeof($user_arr) == 0 AND \sizeof($email_arr) == 0){
                        return $complete_sql;
                }

                if(\sizeof($user_arr) > 0){
                        $user_arr_sql = array();
                        foreach ($user_arr as $value) {
                                if($value != ''){
                                        $user_arr_sql[] = ' (firstname like "%'.$value.'%" OR lastname like "%'.$value.'%")';    
                                }
                        }
                        $userSQL = 'SELECT email FROM {user} WHERE '.\implode(' OR ',$user_arr_sql). ' AND deleted = 0';
                        $user_emails = $DB->get_fieldset_sql($userSQL);
                }
                $comp_array = array_merge($user_emails,$email_arr);
                if($complete_sql == 'SELECT * FROM {custom_maillog} WHERE '){
                        $return_sql = $complete_sql . ' recipient IN ("'. implode('","',$comp_array). '")';
                }else{
                        $return_sql = $complete_sql . ' AND recipient IN ("'. implode('","',$comp_array). '")';  
                }
                return $return_sql;
                //

        }



 
 
 } 