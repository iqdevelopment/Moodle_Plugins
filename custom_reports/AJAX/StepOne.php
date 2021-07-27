<?php

require_once("../../config.php");
require_once("../lib.php");
require ("../classes/field.php");
global $DB;

// setup all fields i want to display
$fields = array();

$field =  new Field();
$field->setDisplayName('Uživatel');
$field->setSystemInfo('user');
$fields[] = $field;

$field =  new Field();
$field->setDisplayName('Kurz');
$field->setSystemInfo('course');
$fields[] = $field;

$field =  new Field();
$field->setDisplayName('Certifikát');
$field->setSystemInfo('simplecertificate_issues');
$fields[] = $field;

$field =  new Field();
$field->setDisplayName('Dokončení kurzu');
$field->setSystemInfo('course_completions');
$fields[] = $field;



//print_r($fields);

if(isset($_POST["getFields"]))
{
   
  echo json_encode($fields);
 }



?>