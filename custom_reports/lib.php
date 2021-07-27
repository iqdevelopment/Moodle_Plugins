<?php


function includesFiles(){
    $output = "
    <script src='js/wizard.js'></script> 
    <script src='js/jquery-3.5.1.min.js'></script>
    <script src='js/jquery-ui.min.js'></script>
    <link rel='stylesheet' href='./css/custom.css'>
      ";
    
    return $output;
    }

/********
 * 
 * Fukce která vytvoří seznam select do SQL
 * 
 *******/

    function SelectAndInnerJoinsSQL($array){
      $fieldArray = array();
      $outputNew = array();
      $innerJoinArray = array();
     //print_r($array); 

            
            switch($array['stepOne']){
                case "user":
                  $fieldArray= array('id AS userid','username','email','firstnamephonetic AS titul_pred','firstname AS jmeno','lastname AS prijmeni','lastnamephonetic AS titul_za','firstaccess AS prvni_pripojeni','lastaccess AS posledni_pripojeni');
                  $tableArray = '{user}';
                  $addionFields = array();
                 // $innerJoinArray[] = 'INNER JOIN {user} ON {user}.id = {course_completions}.userid';
                  break;

                case "course":
                  $fieldArray= array('id AS courseid','category AS id_kategorie','fullname AS cely_nazev_kurzu','shortname AS short_code_kurzu','startdate AS zacatek_kurzu','enddate AS konec_kurzu');
                  $tableArray = '{course}';
                  $addionFields = array('{course_categories}.name AS nazev_kategorie');
                  $innerJoinArray[] = 'INNER JOIN {course_categories} ON {course_categories}.id = {course}.category';
                  break;

                case "course_completions":
                  $fieldArray= array('id','userid','course AS courseid','timeenrolled AS cas_zapisu','timecompleted AS cas_dokonceni');
                  $tableArray = '{course_completions}';
                  $addionFields = array('{course}.startdate AS zacatek_kurzu','{course}.enddate as konec_kurzu',
                                  '{course_categories}.name AS nazev_kategorie',
                                  '{course}.category AS id_kategorie','{course}.fullname AS cely_nazev_kurzu','{course}.shortname AS short_code_kurzu','{course}.startdate AS zacatek_kurzu',
                                  '{course}.enddate AS konec_kurzu',
                                  '{user}.username','{user}.email','{user}.firstnamephonetic AS titul_pred','{user}.firstname AS jmeno','{user}.lastname AS prijmeni','{user}.lastnamephonetic AS titul_za',
                                  '{user}.firstaccess AS prvni_pripojeni','{user}.lastaccess AS posledni_pripojeni'
                                    );
                  $innerJoinArray[] = 'INNER JOIN {course} ON {course}.id = {course_completions}.course';
                  $innerJoinArray[] = 'INNER JOIN {course_categories} ON {course_categories}.id = {course}.category';
                  $innerJoinArray[] = 'INNER JOIN {user} ON {user}.id = {course_completions}.userid';
                  //$innerJoinArray[] = 'INNER JOIN {course_completions} ON {course_completions}.course = {course}.id';
                  break;

                case "simplecertificate_issues":
                  $fieldArray = array('id AS cert_id','certificateid','userid AS userid','coursecode','coursecertnumber','nadpis','hours AS hodiny','poradove_cislo_kod','datum AS datum_vydani','timecreated AS realne_vystaveni');
                  $tableArray = '{simplecertificate_issues}';
                  $addionFields = array('{simplecertificate}.course AS courseid',
                                    '{course_categories}.name AS nazev_kategorie',
                                    '{course}.category AS id_kategorie','{course}.fullname AS cely_nazev_kurzu','{course}.shortname AS short_code_kurzu','{course}.startdate AS zacatek_kurzu',
                                    '{course}.enddate AS konec_kurzu',
                                    '{user}.username','{user}.email','{user}.firstnamephonetic AS titul_pred','{user}.firstname AS jmeno','{user}.lastname AS prijmeni','{user}.lastnamephonetic AS titul_za',
                                    '{user}.firstaccess AS prvni_pripojeni','{user}.lastaccess AS posledni_pripojeni');
                  $innerJoinArray[] = 'INNER JOIN {simplecertificate} ON {simplecertificate}.id = {simplecertificate_issues}.certificateid';
                  $innerJoinArray[] = 'INNER JOIN {course} ON {course}.id = {simplecertificate}.course';
                  $innerJoinArray[] = 'INNER JOIN {course_categories} ON {course_categories}.id = {course}.category';
                  $innerJoinArray[] = 'INNER JOIN {user} ON {user}.id = {simplecertificate_issues}.userid';
                  $innerJoinArray[] = 'INNER JOIN {course_completions} ON {course_completions}.course = {course}.id AND {course_completions}.userid = {simplecertificate_issues}.userid';
                 // $innerJoinArray[] = 'INNER JOIN {course_completions} ON {course_completions}.course = {course}.id';
                  break;

 


      }

      foreach ($fieldArray as $line){
        $outputNew[] .= $tableArray.'.'.$line;
        
      }
      //$tableArray[] = $array['stepOne'];
      
      $output = 'SELECT ';
      $output .= implode(',',array_merge($outputNew,$addionFields));
     //$output .= implode(',',$outputNew);
     // $output .= $outputNew;
      //print_r($outputNew); 
      //$output .= count($fieldArray);
      $output .= ' FROM '.$tableArray.' ';
      $output .= implode(' ',$innerJoinArray);

      return $output;
    }





/********
 * 
 * Fukce která vytvoří seznam WHERE PODMÍNEK
 * 
 *******/

    function whereSQL($array){


        foreach($array['stepTwo'] as $condition){
          $object = new stdClass();
          $object->filter = $condition['filter'];
          $object->data = $condition['data'];
          $object->querry = $condition['querry'];
          $object->type = $condition['type'];
          $output = '';
          //zeptá se, jestli jsou data vyplněná
          if($object->data !== ''){
            if($object->type == 'date'){
              // upravy vstupu
              $object->data = array(strtotime($object->data));
            }
            /*  if(sizeof($object->data) == 1){
                $hodnoty = "('".$object->data."')";

              }else{
                $hodnoty = "('".$object->data."')";
              }*/
              $hodnoty = "('".implode("','",$object->data)."')";

             $condiditonArray[] = str_replace("#data#", $hodnoty, $object->querry);

              if(count($condiditonArray) > 0){$output = ' WHERE '.implode(' AND ',$condiditonArray);
              }else{$output ='';}
                  


          }
      
        }

    return $output;
  }
    
  /***
   * 
   * Vezme objekt, kde je id id kurzu a přidá do objektu další klíče a to hodnoty a klíče z customfields
   */


  function courseAdvancedInfo($object,$courseId,$fieldArray = null) {
    global $DB;
      foreach($fieldArray as $line){
        $fieldArrayNew[] = $line['name'];  
        }     
      
      $fieldData = $DB->get_records_sql('SELECT {customfield_field}.id AS fieldid,{customfield_field}.shortname,{customfield_field}.type,{customfield_data}.value FROM {customfield_field}
        INNER JOIN {customfield_data} ON {customfield_data}.fieldid = {customfield_field}.id
        WHERE {customfield_data}.instanceid = '.$courseId.'');
      
      foreach ($fieldData as $data) {
          if (!in_array($data->shortname,$fieldArrayNew) ) {
              continue;
          }
         
              if($data->type == 'select'){
                  $object->{$data->shortname} = getCourseCustomFieldSelect($courseId,$data->fieldid) ;
                  
              }else{
              $datafilter = $data->value;
              $datafilter = str_replace("\r", "", $datafilter);
              $datafilter = str_replace("\n", "", $datafilter);

              $object->{$data->shortname} =  $datafilter;
              
            }
            
        }
  return $object;
}




function userAdvancedInfo($object,$userid,$fieldArray = null) {
  global $DB, $CFG;
  //print_r($fieldArray);
  foreach($fieldArray as $line){
    $fieldArrayNew[] = $line['name'];  
    } 
  
 $fields = $DB->get_records_sql('SELECT {user_info_field}.id,{user_info_field}.shortname FROM {user_info_field} WHERE {user_info_field}.shortname IN ("'.implode('","',$fieldArrayNew).'")');
 //$object->sql = 'SELECT id,shortname FROM {user_info_field} WHERE shortname IN ("'.implode('","',$fieldArrayNew).'")';
 //print_r($fields);
 foreach($fields as $field){

          if($field->shortname == 'Datum_narozeni'){  
          $object->{$field->shortname} = date('d.m.Y',$DB->get_field_sql('SELECT {user_info_data}.data FROM {user_info_data} WHERE {user_info_data}.userid = '.$userid.' AND {user_info_data}.fieldid = '.$field->id.'')); 
        }
        else{
          $object->{$field->shortname} = $DB->get_field_sql('SELECT {user_info_data}.data FROM {user_info_data} WHERE {user_info_data}.userid = '.$userid.' AND {user_info_data}.fieldid = '.$field->id.''); 
        }
 }
  return $object;
}


/****
 * 
 * 
 * 
 ******/

function getCourseCustomDataAll($courseId){
  global $DB;
  $course = $DB->get_record_sql('SELECT * FROM {course} WHERE id = '.$courseId.'');
  $customIds = $DB->get_records_sql('SELECT id,shortname FROM {customfield_field}');
  foreach($customIds as $customField){
    $course->{$customField->shortname} = getCourseCustomFieldSelect($course->id,$customField->id);

  }
  return $course;
}

/***
 * 
 * moodle custom fields - select-get data
 * 
 * 
 */
function getCourseCustomFieldSelect($courseId,$fieldId){
  //print_r($fieldId);echo '<br>';
  global $DB;
 //přepsání výchozí e-mail msg
      
     $cities_sql = $DB->get_field_sql("SELECT configdata FROM mdl_customfield_field WHERE id=".$fieldId."");
   
      $city_list = str_replace('"required":"0","uniquevalues":"0","options":"','',$cities_sql);
      $city_list = str_replace('","defaultvalue":"","locked":"0","visibility":"2"','',$city_list);
      $city_list = str_replace('","defaultvalue":"NE","locked":"0","visibility":"2"}','',$city_list);
            
     $city_list = str_replace('\r','',$city_list);
     $city_list = str_replace('{','',$city_list);
     $city_list = str_replace('}','',$city_list);
     
      
 //začátek debilní části      
     $city_list = str_replace('\u00e1','á',$city_list);
     $city_list = str_replace('\u00e9','é',$city_list);
     $city_list = str_replace('\u011b','ě',$city_list);
     $city_list = str_replace('\u00ed','í',$city_list);
     $city_list = str_replace('\u00fd','ý',$city_list);
     $city_list = str_replace('\u00f3','ó',$city_list);
     $city_list = str_replace('\u00fa','ú',$city_list);
     $city_list = str_replace('\u00da','Ú',$city_list);
     $city_list = str_replace('\u016f','ů',$city_list);

     $city_list = str_replace('\u0148','ň',$city_list);   //háček
     $city_list = str_replace('\u0165','ť',$city_list);   //háček
     
     $city_list = str_replace('\u010c','Č',$city_list);  //háček
     $city_list = str_replace('\u010d','č',$city_list);  //háček
     $city_list = str_replace('\u0161','š',$city_list);  //háček
     $city_list = str_replace('\u0160','Š',$city_list);  //háček
     $city_list = str_replace('\u0159','ř',$city_list);  //háček
     $city_list = str_replace('\u0158','Ř',$city_list);  //háček
     $city_list = str_replace('\u010','Ď',$city_list);  //háček
     $city_list = str_replace('\u00e9','e',$city_list);
     $city_list = str_replace('\u00e1','á',$city_list);
     $city_list = str_replace('\u00e9','é',$city_list);
     $city_list = str_replace('\u00e1','á',$city_list);
     $city_list = str_replace('\u00e9','é',$city_list);
     $city_list = str_replace('\u017e','ž',$city_list); //háček
     $city_list = str_replace('\u017d','Ž',$city_list); //háček
     $city_list = str_replace('\u010f','ď',$city_list); //háček
    
     $cities = explode("\\n",$city_list);
     
     $city_used = $DB->get_field_sql("SELECT intvalue FROM mdl_customfield_data WHERE fieldid=".$fieldId." AND instanceid=".$courseId."");
   return($cities[$city_used-1]) ;
      
}


/*******
 * 
 * 
 * specialni fukce pro volani dalsich funkci
 * 
 */



function userSpecialInfo($object,$userid,$fieldArray,$SQL){
 
  foreach($fieldArray as $field){
      if($field['id'] != 'userSpecialInfo'){
        continue;
      }
     // print_r($field);
        $object->{$field['value']} = call_user_func( $field['name'],$userid,$SQL);
        
      // return $object->{$field['type']} = 'hovnoo';  
      
    }
  return $object;

}


function courseSpecialInfo($object,$courseId,$fieldArray,$SQL){
  foreach($fieldArray as $field){
      if($field['id'] != 'courseSpecialInfo'){
        continue;
      }

        $object->{$field['value']} = call_user_func( $field['name'],$courseId,$SQL);
      
  }
  return $object;
}








/**********
 * 
 * 
 * funkce, pro speciální posle v reportech - tz jednoduché s jedním vstupem
 * 
 * 
 *********/

 function countCoursesEnrolledAllTime($userId,$sql = null){
   global $DB;
  return $DB->get_field_sql('SELECT count(id) FROM {user_enrolments} WHERE userid = '.$userId.'');

 }

 function countCompletedCoursesAlltime($userId,$sql = null){
  global $DB;
 return $DB->get_field_sql('SELECT count(id) FROM {course_completions} WHERE userid = '.$userId.' AND timecompleted IS NOT NULL');

}

function countHoursInCompletedCoursesAllTime($userId,$sql = null){
  global $DB;
  return  $DB->get_field_sql('SELECT sum( {customfield_data}.intvalue) FROM {course_completions} INNER JOIN {customfield_data} ON {customfield_data}.instanceid = {course_completions}.course WHERE {course_completions}.userid = '.$userId.' AND {course_completions}.timecompleted IS NOT NULL AND {customfield_data}.fieldid = 18');

//return 'SELECT sum( {customfield_data}.intvalue) FROM {course_completions} INNER JOIN {customfield_data} ON {customfield_data}.instanceid = {course_completions}.course WHERE {course_completions}.userid = '.$userId.' AND {course_completions}.timecompleted IS NOT NULL AND {customfield_data}.fieldid = 18';
}

function countHoursInPresentionCompletedCoursesAllTime($userId,$sql = null){
  global $DB;
  return  $DB->get_field_sql('SELECT sum({customfield_data}.intvalue) FROM {course_completions} 
 INNER JOIN {customfield_data} ON {customfield_data}.instanceid = {course_completions}.course
 INNER JOIN {course} ON {course}.id = {course_completions}.course
 WHERE 
  {course_completions}.userid = '.$userId.' 
  AND {course_completions}.timecompleted IS NOT NULL 
  AND {customfield_data}.fieldid = 18
  AND {course}.category NOT IN (127,124,128,129)');

}

function countHoursInDistantionCompletedCoursesAllTime($userId,$sql = null){
  global $DB;
  return  $DB->get_field_sql('SELECT sum({customfield_data}.intvalue) FROM {course_completions} 
  INNER JOIN {customfield_data} ON {customfield_data}.instanceid = {course_completions}.course
  INNER JOIN {course} ON {course}.id = {course_completions}.course
  WHERE 
   {course_completions}.userid = '.$userId.' 
   AND {course_completions}.timecompleted IS NOT NULL 
   AND {customfield_data}.fieldid = 18
   AND {course}.category IN (127,124,128)');

}

function countHoursInOnlineCompletedCoursesAllTime($userId,$sql = null){
  global $DB;
  return  $DB->get_field_sql('SELECT sum({customfield_data}.intvalue) FROM {course_completions} 
  INNER JOIN {customfield_data} ON {customfield_data}.instanceid = {course_completions}.course
  INNER JOIN {course} ON {course}.id = {course_completions}.course
  WHERE 
   {course_completions}.userid = '.$userId.' 
   AND {course_completions}.timecompleted IS NOT NULL 
   AND {customfield_data}.fieldid = 18
   AND {course}.category IN (129)');

}

function countCertificateOsvedceniAllTime($userId,$sql = null){
  global $DB;
 return $DB->get_field_sql('SELECT count(id) FROM {simplecertificate_issues} WHERE userid = '.$userId.' AND nadpis = "OSVĚDČENÍ"');
}

function countCertificateCertificateAllTime($userId,$sql = null){
  global $DB;
  return $DB->get_field_sql('SELECT count(id) FROM {simplecertificate_issues} WHERE userid = '.$userId.' AND nadpis = "CERTIFIKÁT"');

}

function numberOfStudents($courseId,$sql = null){
  global $DB;
  $context = $DB->get_field_sql('SELECT id FROM {context} WHERE instanceid = '.$courseId.' AND contextlevel = 50');
  return $DB->get_field_sql('SELECT count(id) FROM {role_assignments} WHERE contextid = '.$context.' AND roleid = 5');

}

function numberOfNonStudents($courseId,$sql = null){
  global $DB;
  $context = $DB->get_field_sql('SELECT id FROM {context} WHERE instanceid = '.$courseId.' AND contextlevel = 50');
  return $DB->get_field_sql('SELECT count(id) FROM {role_assignments} WHERE contextid = '.$context.' AND roleid != 5');

}

function numberOfCompletions($courseId,$sql = null){
  global $DB;
  return $DB->get_field_sql('SELECT count(id) FROM {course_completions} WHERE course = '.$courseId.' AND timecompleted IS NOT NULL AND timeenrolled > 0');

}

function numberOfCertificates($courseId,$sql = null){
  global $DB;
  return $DB->get_field_sql('SELECT count(id) FROM {simplecertificate_issues} WHERE certificateid = (SELECT id FROM {simplecertificate} WHERE course = '.$courseId.')');

}

function numberOfOsvedceni($courseId,$sql = null){
  global $DB;
  return $DB->get_field_sql('SELECT count(id) FROM {simplecertificate_issues} WHERE certificateid = (SELECT id FROM {simplecertificate} WHERE course = '.$courseId.') AND nadpis = "OSVĚDČENÍ"');

}

function numberOfCertifikatu($courseId,$sql = null){
  global $DB;
  return $DB->get_field_sql('SELECT count(id) FROM {simplecertificate_issues} WHERE certificateid = (SELECT id FROM {simplecertificate} WHERE course = '.$courseId.') AND nadpis = "CERTIFIKÁT"');

}

function courseContacts($courseId,$sql = null){
  global $DB;
  $usersarr = array();
  $context = $DB->get_field_sql('SELECT id FROM {context} WHERE instanceid = '.$courseId.' AND contextlevel = 50');
  $users = $DB->get_records_sql('SELECT firstnamephonetic,firstname,lastname,lastnamephonetic FROM {user} WHERE id IN (SELECT userid FROM {role_assignments} WHERE roleid = 1 AND contextid = '.$context.')');
  foreach($users as $user){
    $usersarr[] = $user->firstnamephonetic.' '.$user->firstname.' '.$user->lastname.' '.$user->lastnamephonetic;

  }
  if (!empty($usersarr)) {
    $output = implode(', ',$usersarr);
  }else{
  $output = ' / ';
}

   return $output;

}

/*************
 * 
 * A TED TA SRANDA
 * 
 **********/


//funkce ktera vytahne z SQL arraye podminky na cas
function getTimestampFromSQL($string){
  $string = explode('WHERE',$string);
  $array = array();
  //print_r($string);
  foreach($string as $substring){
      foreach(explode('AND',$substring) as $subSubString){
  
          if(preg_match('/[0-9]{10}/',$subSubString)){
              $array[] = $subSubString;
  
          } 
  
      }
  }
  return $array;
}




function countCoursesEnrolledInFilteredTime($userId,$sql = null){
  global $DB;
 if(!empty(getTimestampFromSQL($sql))){
      $where = implode(' AND ',getTimestampFromSQL($sql));
      
        $where = ' AND '.$where; 
      
    }else{ $where = '';}
 $innerJoin = '
 INNER JOIN {enrol} ON {enrol}.id = {user_enrolments}.enrolid
 INNER JOIN {course} ON {course}.id = {enrol}.courseid
 INNER JOIN {course_completions} ON {course_completions}.course = {course}.id';

 return $DB->get_field_sql('SELECT count({user_enrolments}.id) FROM {user_enrolments} '.$innerJoin.' WHERE {user_enrolments}.userid = '.$userId.' '.$where.'');

}

function countCompletedCoursesFilteredtime($userId,$sql = null){
 global $DB;
 if(!empty(getTimestampFromSQL($sql))){
     $where = implode(' AND ',getTimestampFromSQL($sql));
      
        $where = ' AND '.$where; 
      
    }else{ $where = '';}
 /*$innerJoin = '
 INNER JOIN {course} ON {course}.id = {course_completions}.course';*/
return $DB->get_field_sql('SELECT count({course_completions}.id) FROM {course_completions} INNER JOIN {course} ON {course}.id = {course_completions}.course WHERE userid = '.$userId.' AND timecompleted IS NOT NULL' .$where.'');

}

function countHoursInCompletedCoursesFilteredTime($userId,$sql = null){
 global $DB;
 if(!empty(getTimestampFromSQL($sql))){
     $where = implode(' AND ',getTimestampFromSQL($sql));
      
        $where = ' AND '.$where; 
      
    }else{ $where = '';}
 $innerJoin = '
 INNER JOIN {course} ON {course}.id = {course_completions}.course';
 return  $DB->get_field_sql('SELECT sum({customfield_data}.intvalue) FROM {course_completions} 
INNER JOIN {customfield_data} ON {customfield_data}.instanceid = {course_completions}.course '.$innerJoin .'
WHERE {course_completions}.userid = '.$userId.' AND {course_completions}.timecompleted IS NOT NULL AND {customfield_data}.fieldid = 18 '.$where. '');

}

// odsud dál dodělat
function countHoursInPresentionCompletedCoursesFilteredTime($userId,$sql = null){
 global $DB;
 if(!empty(getTimestampFromSQL($sql))){
     $where = implode(' AND ',getTimestampFromSQL($sql));
      
        $where = ' AND '.$where; 
      
    }else{ $where = '';}
 

 return  $DB->get_field_sql('SELECT sum({customfield_data}.intvalue) FROM {course_completions} 
INNER JOIN {customfield_data} ON {customfield_data}.instanceid = {course_completions}.course
INNER JOIN {course} ON {course}.id = {course_completions}.course
WHERE 
 {course_completions}.userid = '.$userId.' 
 AND {course_completions}.timecompleted IS NOT NULL 
 AND {customfield_data}.fieldid = 18
 AND {course}.category NOT IN (127,124,128,129) '.$where.'');

}

function countHoursInDistantionCompletedCoursesFilteredTime($userId,$sql = null){
 global $DB;
if(!empty(getTimestampFromSQL($sql))){
     $where = implode(' AND ',getTimestampFromSQL($sql));
 
        $where = ' AND '.$where; 
 
}else{ $where = '';}

 return  $DB->get_field_sql('SELECT sum({customfield_data}.intvalue) FROM {course_completions} 
 INNER JOIN {customfield_data} ON {customfield_data}.instanceid = {course_completions}.course
 INNER JOIN {course} ON {course}.id = {course_completions}.course
 WHERE 
  {course_completions}.userid = '.$userId.' 
  AND {course_completions}.timecompleted IS NOT NULL 
  AND {customfield_data}.fieldid = 18
  AND {course}.category IN (127,124,128) '.$where.'');

}


function countHoursInOnlineCompletedCoursesFilteredTime($userId,$sql = null){
  global $DB;
 if(!empty(getTimestampFromSQL($sql))){
      $where = implode(' AND ',getTimestampFromSQL($sql));
  
         $where = ' AND '.$where; 
  
 }else{ $where = '';}
 
  return  $DB->get_field_sql('SELECT sum({customfield_data}.intvalue) FROM {course_completions} 
  INNER JOIN {customfield_data} ON {customfield_data}.instanceid = {course_completions}.course
  INNER JOIN {course} ON {course}.id = {course_completions}.course
  WHERE 
   {course_completions}.userid = '.$userId.' 
   AND {course_completions}.timecompleted IS NOT NULL 
   AND {customfield_data}.fieldid = 18
   AND {course}.category IN (129) '.$where.'');
 
 }

function countCertificateOsvedceniFilteredTime($userId,$sql = null){
 global $DB;
 if(!empty(getTimestampFromSQL($sql))){
     $where = implode(' AND ',getTimestampFromSQL($sql));
      
        $where = ' AND '.$where; 
      
    }else{ $where = '';}
 $innerJoin = '
 INNER JOIN {simplecertificate} ON {simplecertificate}.id = {simplecertificate_issues}.certificateid
 INNER JOIN {course} ON {course}.id = {simplecertificate}.course
 INNER JOIN {course_completions} ON {course_completions}.course = {course}.id AND {course_completions}.userid = {simplecertificate_issues}.userid';
return $DB->get_field_sql('SELECT count({simplecertificate_issues}.id) FROM {simplecertificate_issues}  '.$innerJoin.'  WHERE {simplecertificate_issues}.userid = '.$userId.' AND nadpis = "OSVĚDČENÍ" '.$where.'');
}

function countCertificateCertificateFilteredTime($userId,$sql = null){
 global $DB;
 if(!empty(getTimestampFromSQL($sql))){
     $where = implode(' AND ',getTimestampFromSQL($sql));
      
        $where = ' AND '.$where; 
      
    }else{ $where = '';}

 return $DB->get_field_sql('SELECT count({simplecertificate_issues}.id) FROM {simplecertificate_issues} INNER JOIN {simplecertificate} ON {simplecertificate}.id = {simplecertificate_issues}.certificateid
 INNER JOIN {course} ON {course}.id = {simplecertificate}.course
 INNER JOIN {course_completions} ON {course_completions}.course = {course}.id AND {course_completions}.userid = {simplecertificate_issues}.userid
  WHERE {simplecertificate_issues}.userid = '.$userId.' AND nadpis = "CERTIFIKÁT" '.$where.' ');

}


