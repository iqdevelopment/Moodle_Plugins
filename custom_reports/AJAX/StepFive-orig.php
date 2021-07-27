<?php

require_once("../../config.php");
require_once("../lib.php");
require ("../classes/field.php");
require ("../classes/filteredObject.php");
global $DB;




//print_r($fields);

if(isset($_POST["value"]))
  {

  //  $value = StepFive($_POST["value"]);
  
    echo json_encode($_POST["value"]);
  }













?>