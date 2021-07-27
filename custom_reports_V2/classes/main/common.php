<?php
namespace local_custom_reports\main;

class common{

    public static function GetRecipients()
    {   global $DB;
        $recipients = $DB->get_fieldset_sql('SELECT value FROM {config_plugins} WHERE plugin = ? AND name like "%recipient_%"',array('local_custom_reports'));
        foreach ($recipients as $key => $value) {
            if(empty($value)){continue;}
            $return[] = $value;
        }
        if(empty($return)){
            return false;
        }else{
            return $return;
        }
    }


    public static function Process($sendObject)
    {   global $DB,$CFG;
    require $CFG->dirroot.'/local/custom_reports/lib.php';
        $recipients = \local_custom_reports\main\common::GetRecipients();
        if($recipients == false){
            exit('No recipients found.');  
        }
        $data = $DB->get_records_sql($sendObject->querry); 
        $numberOfOutcomes = sizeof($data);
         
         switch($sendObject->table){
            // $data = $DB->get_records_sql('SELECT * FROM {user} WHERE id < 100');
            
             case 'course':
                     foreach ($data as $line ){
                         $line =  courseAdvancedInfo($line,$line->courseid,$sendObject->columns); 
                         
                         $dataNew[] = courseSpecialInfo($line,$line->courseid,$sendObject->columns,$sendObject->querry);
                          
                         
                     } 
                 break;
             
             case 'user':    
 
                     foreach ($data as $line ){
                         
                         $line = userAdvancedInfo($line,$line->userid,$sendObject->columns);
                         $dataNew[] = userSpecialInfo($line,$line->userid,$sendObject->columns,$sendObject->querry);
                     } 
                 break;  
                 
             case 'simplecertificate_issues':
                     foreach ($data as $line ){
                         $line = courseAdvancedInfo($line,$line->courseid,$sendObject->columns);
                         $line =  courseSpecialInfo($line,$line->courseid,$sendObject->columns,$sendObject->querry);
                         $line = userAdvancedInfo($line,$line->userid,$sendObject->columns); 
                         $dataNew[] = userSpecialInfo($line,$line->userid,$sendObject->columns,$sendObject->querry);
                     } 
                 break;    
 
             case 'course_completions':
                 foreach ($data as $line ){
                     $line = courseAdvancedInfo($line,$line->courseid,$sendObject->columns);
                     $line =  courseSpecialInfo($line,$line->courseid,$sendObject->columns,$sendObject->querry);
                     $line = userAdvancedInfo($line,$line->userid,$sendObject->columns); 
                     $line = userSpecialInfo($line,$line->userid,$sendObject->columns,$sendObject->querry);
                     $dataNew[] = $line;
                 }
                 break;    
 
               }

        //naformatovani do CSV
               $data_string = '';    
               echo "nactenu jdu na csv";
               $firstkey = array_keys($data);
               $firstkey = $firstkey[0];
               foreach($data[$firstkey] as $value => $key){
   
                   $data_string .= $value.";";
               }
               $data_string .= "\r\n";
   
               foreach ($data as $line){
                   foreach($line as $row){
                       
                       // smrtelně důležité - kódování, jinak to je pro excel nechroupnutelné
                       if(preg_match('/[0-9]{10}/',$row)){
                           $data_string .= date('d.m.Y H:i',$row).";";
                              //iconv('UTF-8', 'CP1250', $row).";"; 
                       }else{
                           $data_string .=   iconv('UTF-8', 'CP1250', $row).";"; 
                       }
                   }
                   
                   $data_string .= "\r\n"; 
               
               }

        $attachment = $CFG->dirroot."/local/custom_reports/reports/".$sendObject->table."-export".date('d-m-Y-H-i',time()).".csv"; 
        $nameOfFile = $sendObject->table."-export".date('d-m-Y-H-i',time()).".csv"; 
        $url = $CFG->wwwroot.'/local/custom_reports/reports/'.$nameOfFile;
        $attachmentMessage = 'Zde naleznete odkaz pro stažení či sdílení: ';
        $attachmentMessage .= $url;
        $attachmentMessage .= '<br>Tento odkaz bude planý maximálně týden!<br><br>';
        $body = 'Váš report <br> 
                 <br>Jako hlavní klíč: ' .get_string($sendObject->table, 'local_custom_reports').'<br><br>';
                
        $body .= $attachmentMessage;
        $body .= '<br>Pěkný den<br>
                            Tým ESO<br>';
        $subject = 'Váš report ze systému';

        file_put_contents($attachment, $data_string, LOCK_EX);
              
   
        $finishTime = date('d.m.Y H:i',time());

        foreach ($recipients as $key => $recipient) {
            $toUser = $DB->get_record_sql('SELECT * FROM {user} WHERE email = ?',array($recipient));
            $fromUser = $DB->get_record_sql('SELECT * FROM {user} WHERE id = ?',array(2));
            
            email_to_user($toUser, $fromUser, $subject, $body, $body, '', '', true);
        }
        
        return get_string('all_ok','local_custom_reports');



    }

}