<?php

require_once("../../config.php");
require_once("../lib.php");
require ("../classes/field.php");
require ("../classes/filteredObject.php");
global $DB;




//print_r($fields);

if(isset($_POST["value"]))
  {

    $value = StepFour($_POST["value"]);
  
    echo json_encode($value);
  }




  
 function StepFour($arraydata){
  global $DB;
  /*$object = array();
  foreach($arraydata as $data => $value){
     $object[$data] = $value;
    }*/
    $return = new stdClass();
    $sql = SelectAndInnerJoinsSQL($arraydata);
    $sql .= whereSQL($arraydata);
    $return->sql = $sql;
    $return->size = sizeof($DB->get_records_sql($sql));

    
  

   /* $sql = str_replace("{", "mdl_", $sql);
      $sql = str_replace("}", "", $sql);*/


    
    //$output = $object;







  
  return $return;
 }



 /*pozn sbírat v krocích do array a pak je explodnout:
 a) posbírat fields do arraye
 b) posbírat inner joiny
 c) posbírat where podmínky
 */
 

/*function countResults($array){
    global $DB;
    //FROM
    $sql = SelectsSQL($array);
    //$sql = 'SELECT count(id) FROM '.$array['StepOne'].' ';
    // WHERE and INNER JOINS
    $sql .= innerJoinsSQL($array);
    $sql .= whereSQL($array);
    

    return $sql;
}*/












?>