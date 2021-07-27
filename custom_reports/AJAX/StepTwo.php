<?php

require_once("../../config.php");
require_once("../lib.php");
require ("../classes/field.php");
require ("../classes/filteredObject.php");
global $DB;




//print_r($fields);

if(isset($_POST["value"]))
  {

    $value = StepTwo($_POST["value"]);
  
    echo json_encode($value);
  }else{}




  
 function StepTwo($arraydata){
  global $DB;
  $object = array();
  foreach($arraydata as $data => $value){
     $object[$data] = $value;
    }
  

    $fieldArray = array();
    switch($object['stepOne']){
        case 'user':
          $fieldArray[] = new FilteredObject('lastaccess','date','Poslední připojení od','','{user}.lastaccess > #data#');

        //  $fieldArray[] = new FilteredObject('completed','number','Uživatel splnil více kurzů než','','course_completions');

          $fieldArray[] = new FilteredObject('Role_uzivatele','multipleselect','Pracovní role uživatele',explode("\n",$DB->get_field_sql("SELECT param1 FROM mdl_user_info_field WHERE id = 18")),
                '{user}.id IN (SELECT {user_info_data}.userid FROM {user_info_data} WHERE fieldid = (SELECT id FROM {user_info_field} WHERE shortname="Role_uzivatele") AND {user_info_data}.data IN #data#)');
          $fieldArray[] = new FilteredObject('role_system','multipleselect','Role uživatele v systému',$DB->get_fieldset_sql("SELECT name FROM mdl_role WHERE id IN (1,3,5,12,13,14,15)"),
                '{user}.id IN (SELECT userid FROM {role_assignments} WHERE contextid IN (SELECT id FROM {context} WHERE contextlevel = 50) AND roleid IN (SELECT id FROM {role} WHERE name IN #data#))');
          $fieldArray[] = new FilteredObject('kraj','multipleselect','Kraj působení',explode("\n",$DB->get_field_sql("SELECT param1 FROM mdl_user_info_field WHERE id = 26")),
                '{user}.id IN (SELECT {user_info_data}.userid FROM {user_info_data} WHERE fieldid = (SELECT id FROM {user_info_field} WHERE shortname="kraj") AND {user_info_data}.data IN #data#)');
          break;

        case 'course':
          $fieldArray[] = new FilteredObject('startdate','date','Začátek kurzu','','{course}.startdate > #data#');
          $fieldArray[] = new FilteredObject('enddate','date','Konec kurzu','','{course}.enddate < #data#');
          $fieldArray[] = new FilteredObject('name','multipleselect','Kraj/Kategorie',$DB->get_fieldset_sql("SELECT name FROM mdl_course_categories WHERE id NOT IN (114,3,8,125,126) ORDER BY name "),'{course_categories}.name IN #data#');
          break;   
          
        case 'simplecertificate_issues':
          $fieldArray[] = new FilteredObject('startdate','date','Konání kurzu od','','(({course}.startdate > #data# AND {course}.category NOT IN (127,124,128) ) OR ({course}.category IN (127,124,128) AND {course_completions}.timecompleted > #data#)) AND {simplecertificate_issues}.certificatename  !="dummy cert" AND {course_completions}.timecompleted IS NOT NULL ');
          $fieldArray[] = new FilteredObject('enddate','date','Konání kurzu do','','(({course}.enddate < #data# AND {course}.category NOT IN (127,124,128)) OR ({course}.category IN (127,124,128) AND {course_completions}.timecompleted < #data#))  AND {simplecertificate_issues}.certificatename  !="dummy cert" AND {course_completions}.timecompleted IS NOT NULL');
          $fieldArray[] = new FilteredObject('nadpis','multipleselect','Druh osvědčení/certifikátu',array('CERTIFIKÁT','OSVĚDČENÍ'),'{simplecertificate_issues}.nadpis IN #data# AND {simplecertificate_issues}.certificatename  !="dummy cert"');
          $fieldArray[] = new FilteredObject('name','multipleselect','Kraj/Kategorie',$DB->get_fieldset_sql("SELECT name FROM mdl_course_categories WHERE id NOT IN (114,3,8,125,126) ORDER BY name"),'{course_categories}.name IN #data# AND {simplecertificate_issues}.certificatename  !="dummy cert"');
          break;  
          
        case 'course_completions':
          $fieldArray[] = new FilteredObject('startdate','date','Konání kurzu od','','(({course}.startdate > #data# AND {course}.category NOT IN (127,124,128) ) OR ({course}.category IN (127,124,128) AND {course_completions}.timecompleted > #data#)) AND {course_completions}.timecompleted IS NOT NULL');
          $fieldArray[] = new FilteredObject('enddate','date','Konání kurzu do','','(({course}.enddate < #data# AND {course}.category NOT IN (127,124,128)) OR ({course}.category IN (127,124,128) AND {course_completions}.timecompleted < #data#)) AND {course_completions}.timecompleted IS NOT NULL');
         // $fieldArray[] = new FilteredObject('timecompleted','date','Splnění kurzu od','','course_completions');
         // $fieldArray[] = new FilteredObject('timecompleted','date','Splnění kurzu do','','course_completions');
          $fieldArray[] = new FilteredObject('name','multipleselect','Kraj/Kategorie',$DB->get_fieldset_sql("SELECT name FROM mdl_course_categories WHERE id NOT IN (114,3,8,125,126) ORDER BY name"),'{course_categories}.name IN #data# AND {course_completions}.timecompleted IS NOT NULL');
          break;  





    }


    $object['fields'] = $fieldArray;
    $output = $object;







  
  return $output;
 }





?>