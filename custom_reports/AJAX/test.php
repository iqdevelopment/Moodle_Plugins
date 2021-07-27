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
            $sendObject->querry = "SELECT {simplecertificate_issues}.id,{simplecertificate_issues}.certificateid,{simplecertificate_issues}.userid,{simplecertificate_issues}.coursecode,{simplecertificate_issues}.coursecertnumber,{simplecertificate_issues}.nadpis,{simplecertificate_issues}.hours,{simplecertificate_issues}.poradove_cislo_kod,{simplecertificate_issues}.datum,{simplecertificate_issues}.timecreated,{simplecertificate}.course,{course_categories}.name FROM {simplecertificate_issues} INNER JOIN {simplecertificate} ON {simplecertificate}.id = {simplecertificate_issues}.certificateid INNER JOIN {course} ON {course}.id = {simplecertificate}.course INNER JOIN {course_categories} ON {course_categories}.id = {course}.category";
           // $data = $DB->get_records_sql('SELECT * FROM {user} WHERE id < 100');
           $data = $DB->get_records_sql($sendObject->querry);
            //print_r($data);
            

           
            $data_string = '';



           /* foreach ($data as $line ){
                profile_load_data($line); 
                
                } */
            // tahle část vytvoří hlavičky - bere se jako první řádek   
            $firstkey = array_keys($data);
            $firstkey = $firstkey[0];
            foreach($data[$firstkey] as $value => $key){

                $data_string .= $value.";";
            }
            $data_string .= "\r\n";


            foreach ($data as $line){
                //profile_load_data($line); 

                foreach($line as $row){
                    // smrtelně důležité - kódování, jinak to je pro excel nechroupnutelné
                    $data_string .=   iconv('UTF-8', 'CP1250', $row).";"; 
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





            $attachment = "../Reports/"."export".date('d-m-Y-H-i',time()).".csv";  
            file_put_contents($attachment, $data_string, LOCK_EX);

            $finishTime = date('d.m.Y H:i',time());


            $mail = new PHPMailer(true);

            try {
                //Server settings
                //$mail->SMTPDebug = SMTP::DEBUG_SERVER;                      // Enable verbose debug output
                $mail->isSMTP();                                            // Send using SMTP
                $mail->Host       =  $DB->get_field_sql('SELECT value FROM {config} WHERE name = "smtphosts"'); //'smtp.seznam.cz';                    // Set the SMTP server to send through
                $mail->SMTPAuth   = true;                                   // Enable SMTP authentication
                $mail->Username   = $DB->get_field_sql('SELECT value FROM {config} WHERE name = "smtpuser"');//'zajicek.simon@seznam.cz';                     // SMTP username
                $mail->Password   = $DB->get_field_sql('SELECT value FROM {config} WHERE name = "smtppass"');//'hliixadad';                               // SMTP password
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         // Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
                $mail->Port       = 587;                                    // TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above

                //Recipients

                $mail->setFrom($DB->get_field_sql('SELECT value FROM {config} WHERE name = "noreplyaddress"'),$DB->get_field_sql('SELECT firstname FROM {user} WHERE id = 2').' '.$DB->get_field_sql('SELECT lastname FROM {user} WHERE id = 2') );
                $mail->addAddress($sendObject->recipient, 'Report recipient');     // Add a recipient
            
                // Attachments
                $mail->addAttachment($attachment);
            // $mail->addAttachment($file2,'demo2.csv');         // Add attachments
            //  $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name


            $attachmentName = str_replace('../Reports/','',$attachment);
            $attachmentUrl = str_replace('..','',$attachment);
                // Content
                $mail->isHTML(true);                                  // Set email format to HTML
                $mail->Subject = 'Váš report ze systému';
                $body = 'Váš report '.$attachmentName.'<br> Byl zahájen: '. $initialTime.' a dokončen: '.$finishTime.'
                 <br>Jako hlavní klíč: ' .$sendObject->table.'<br><br>';
                 $url = "/custom_reports".$attachmentUrl;
                $body .= 'Zde naleznete odkaz pro stažení či sdílení: ';
                $body .= new moodle_url($url);
                $body .= '<br>Tento odkaz bude planý maximálně týden!<br><br>';
                $body .= '<br>Pěkný den<br>
                            Tým ESO<br>';
                $mail->Body = $body;
                $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';
                $mail->CharSet = 'UTF-8';
               // $mail->send();
                echo 'Message has been sent';
            } catch (Exception $e) {
                echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
            }


?>