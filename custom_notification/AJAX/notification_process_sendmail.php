<?php

//  Display the course home page.
  namespace local_custom_notification;
    require_once('../../../config.php');
    
    global $DB,$USER,$CFG,$PAGE;
    $obj = new AJAX\processor();
   // require_login();
    if(isset($_POST['data'])){
       // session_write_close();
      $output =  $obj->sendOneTimeMessage($_POST['data']);
       // return $_POST['data'];
        echo json_encode($output);
    }else{
      echo json_encode($output);
    }