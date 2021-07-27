<?php
    require_once("../../config.php");
    require_once("../lib.php");
    require_once($CFG->dirroot.'/user/profile/lib.php');
    
    global $DB,$PAGE, $CFG,$USER;
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;


    require '../PHPMailer/src/Exception.php';
    require '../PHPMailer/src/PHPMailer.php';
    require '../PHPMailer/src/SMTP.php';



    if(isset($_POST["value"])){
        $sendObject = new stdClass();
        $sendObject->table = $_POST["value"]['stepOne'];
        $sendObject->filters = $_POST["value"]['stepTwo'];
        $sendObject->columns = $_POST["value"]['stepThree'];
        $sendObject->querry = $_POST["value"]['stepFour'];
        $sendObject->recipient = $_POST["value"]['stepFive'];
            $initialTime = date('d.m.Y H:i',time()); 
        //uzavírám psaní do session - vypnete se stav, kdy dokud se report neudělá není možné pro uživatele aby pracoval    
            session_write_close();

       /*******
        * 
        *
        *Tady začíná sranda s rovnáním reportu
        *
        */

       



         $logText = 'SQL: '.$sendObject->querry.' recipitent: '.$sendObject->recipient ;

        $log_querry = 'INSERT INTO {logstore_standard_log} (
            eventname,
            component,
            action,
            target,
            crud,
            edulevel,
            contextid,
            contextlevel,
            contextinstanceid,
            userid,
            timecreated,
            other
        ) VALUES (
            "custom_reports",
            "custom_reports",
            "report_started",
            "report",
            0,
            0,
            69,
            69,
            69,
            '.$USER->id.',
            '.time().',
            "'.$logText.'"
            
        )';

      //  $DB->execute($log_querry);


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
                        //print_r($line);                 
                        $dataNew[] = userSpecialInfo($line,$line->userid,$sendObject->columns,$sendObject->querry);
                        

                       // $dataNew[]  = userSpecialInfo($line,$line->userid,$sendObject->columns,$sendObject->querry);
                        
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


              //zkouska odstraneni prebytecnych dat
/*
              $firstkey = array_keys($data);
              $firstkey = $firstkey[0];
              foreach($data[$firstkey] as $value => $key){
  
                  $data_array[] = $value;
              }


             foreach( $sendObject->columns as $array){
                $usedArray[] = $array['name'];

             }
             $usedArray = array_unique($usedArray);

             $toDelete = array_diff($data_array,$usedArray);
             print_r($toDelete);

             unset($data);
            foreach ($dataNew as $line){
               
               foreach($toDelete as $var){
               unset($line->{$var});  
               }
               
               
                $data[] = $line;


            } 
*/
              //konec odstraneni prebytecnych dat



           /*******
            * 

            SEM JE POTŘEBA NARVAT VŠECHNY POLE A TO JAK SI VŮBEC POBEROU DALŠÍ POLE DO TISKU REPORTU

            *******/
          



           
            //$data = $dataNew;
            //print_r($data);
            
                $data_string = $sendObject->querry;    
            echo "nactenu jdu na csv";
            $firstkey = array_keys($data);
            $firstkey = $firstkey[0];
            foreach($data[$firstkey] as $value => $key){

                $data_string .= $value.";";
            }
            $data_string .= "\r\n";


            foreach ($data as $line){
                //profile_load_data($line); 
               // print_r($line);
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


            /****
             * 
             * 
             * odsud dál se to stará o odeslání emialu, to už jede
             * Potřebuji jsem dostat akorát naformátovaný string $datastring
             * 
             */




            
            $attachment = "../Reports/"."user-".$USER->id."-export".date('d-m-Y-H-i',time()).".csv";  
            file_put_contents($attachment, $data_string, LOCK_EX);
            

            $finishTime = date('d.m.Y H:i',time());


            $mail = new PHPMailer(true);

            try {
                //Server settings
                //$mail->SMTPDebug = SMTP::DEBUG_SERVER;                      // Enable verbose debug output
                $mail->isSMTP();                                            // Send using SMTP
                $mail->Host       =  $DB->get_field_sql('SELECT value FROM {config} WHERE name = "smtphosts"');                // Set the SMTP server to send through
                $mail->SMTPAuth   = true;                                   // Enable SMTP authentication
                $mail->Username   = $DB->get_field_sql('SELECT value FROM {config} WHERE name = "smtpuser"');                     // SMTP username
                $mail->Password   = $DB->get_field_sql('SELECT value FROM {config} WHERE name = "smtppass"');                              // SMTP password
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
                $mail->Port       = 587;                                    // TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above

                //Recipients

                $mail->setFrom($DB->get_field_sql('SELECT value FROM {config} WHERE name = "noreplyaddress"'),$DB->get_field_sql('SELECT firstname FROM {user} WHERE id = 2').' '.$DB->get_field_sql('SELECT lastname FROM {user} WHERE id = 2') );
                $mail->addAddress($sendObject->recipient, 'Report recipient');     // Add a recipient
            
                // Attachments
                
            // $mail->addAttachment($file2,'demo2.csv');         // Add attachments
            //  $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name

                if($numberOfOutcomes > 0){
                        //$mail->addAttachment($attachment);

                        $attachmentName = str_replace('../Reports/','',$attachment);
                        $attachmentUrl = str_replace('..','',$attachment);
                        $url = "/custom_reports".$attachmentUrl;
                        $attachmentMessage = 'Zde naleznete odkaz pro stažení či sdílení: ';
                        $attachmentMessage .= new moodle_url($url);
                        $attachmentMessage .= '<br>Tento odkaz bude planý maximálně týden!<br><br>';
                        $messageOfOutomes = 'Bylo nalezeno: '.$numberOfOutcomes.' výsledků<br>';
                }else{
                    $messageOfOutomes = '<b>Pro vaše zadané filtry nebyly nalezeny žádné výsledky!</b><br>';
                    $attachmentMessage = '';   
                }

                // Content
                $mail->isHTML(true);                                  // Set email format to HTML
                $mail->Subject = 'Váš report ze systému';
                $body = 'Váš report '.$attachmentName.'<br> Byl zahájen: '. $initialTime.' a dokončen: '.$finishTime.'
                 <br>Jako hlavní klíč: ' .$sendObject->table.'<br><br>';
                 $body .= $messageOfOutomes;
                
                 $body .= $attachmentMessage;
                $body .= '<br>Pěkný den<br>
                            Tým ESO<br>';
                $mail->Body = $body;
                $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';
                $mail->CharSet = 'UTF-8';
                $mail->send();
                echo 'Message has been sent';


                //logging
                $logText = 'SQL: '.$sendObject->querry.' recipitent: '.$sendObject->recipient ;

                $log_querry = 'INSERT INTO {logstore_standard_log} (
                    eventname,
                    component,
                    action,
                    target,
                    crud,
                    edulevel,
                    contextid,
                    contextlevel,
                    contextinstanceid,
                    userid,
                    timecreated,
                    other
                ) VALUES (
                    "custom_reports",
                    "custom_reports",
                    "report_sent",
                    "report",
                    0,
                    0,
                    69,
                    69,
                    69,
                    '.$USER->id.',
                    '.time().',
                    "'.$logText.'"
                    
                )';
        
              //  $DB->execute($log_querry);


            } catch (Exception $e) {
                echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
            }

}
?>