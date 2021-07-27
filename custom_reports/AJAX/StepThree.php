<?php

require_once("../../config.php");
require_once("../lib.php");
require ("../classes/field.php");
require ("../classes/filteredObject.php");
global $DB;




//print_r($fields);

if(isset($_POST["value"]))
  {

    $value = StepThree($_POST["value"]);
  
    echo json_encode($value);
  }else{}




  
 function StepThree($data){
  global $DB;

  $fieldObject = new Stdclass();

    switch($data){
        case 'user':
                        //basic info o uživateli
                    $fieldObject->userBasicInfo->categoryName = 'Základní info o uživateli';
                    $fieldObject->userBasicInfo->fields = ['id','username','email','firstnamephonetic','firstname','lastname','lastnamephonetic','firstaccess','lastaccess'];
                    $fieldObject->userBasicInfo->translations = ['id','uživatelské jméno','email','titul před jménem','jméno','příjmení','titul za jménem','první připojení','poslední připojení'];
                    $fieldObject->userBasicInfo->type = ['int','text','text','text','text','text','text','date','date']; 

                        // advanced info o uzivateli
                    $fieldObject->userAdvancedInfo->categoryName = 'Další info o uživateli';
                    $fieldObject->userAdvancedInfo->fields = $DB->get_fieldset_sql("SELECT shortname FROM mdl_user_info_field  WHERE id != 19");
                    $fieldObject->userAdvancedInfo->translations = $DB->get_fieldset_sql("SELECT name FROM mdl_user_info_field  WHERE id != 19");
                    $fieldObject->userAdvancedInfo->type = $DB->get_fieldset_sql("SELECT datatype FROM mdl_user_info_field  WHERE id != 19"); 

                    //special functions
                    $fieldObject->userSpecialInfo->categoryName = 'Informace o postupu kurzy';
                    $fieldObject->userSpecialInfo->fields = ['countCoursesEnrolledAllTime',
                                                            'countCompletedCoursesAlltime',
                                                            'countHoursInCompletedCoursesAllTime',
                                                            'countHoursInPresentionCompletedCoursesAllTime',
                                                            'countHoursInDistantionCompletedCoursesAllTime',
                                                            'countHoursInOnlineCompletedCoursesAllTime',
                                                            'countCertificateOsvedceniAllTime',
                                                            'countCertificateCertificateAllTime'];

                    $fieldObject->userSpecialInfo->translations = ['Celkový počet zapsaných kurzů',
                                                            'Celkový počet dokončených kurzů',
                                                            'Celkový počet hodin ve splněných kurzech',
                                                            'Celkový počet hodin ve splněných prezenčních kurzech',
                                                            'Celkový počet hodin ve splněných distančních kurzech',
                                                            'Celkový počet hodin ve splněných online kurzech',
                                                            'Celkový počet Osvědčení',
                                                            'Celkový počet Certifikátů'];

                    $fieldObject->userSpecialInfo->type = ['Celkovy_pocet_zapsanych_kurzu',
                    'Celkovy_pocet_dokoncenych_kurzu',
                    'Celkovy_pocet_hodin_ve_splnenych_kurzech',
                    'Celkovy_pocet_hodin_ve_splnenych_prezencnich_kurzech',
                    'Celkovy_pocet_hodin_ve_splnenych_distancnich_kurzech',
                    'Celkovy_pocet_hodin_ve_splnenych_online_kurzech',
                    'Celkovy_pocet_Osvedceni',
                    'Celkovy_pocet_Certifikatu'];

                    



                  /*  $fieldObject->userSpecialInfo->type = ['func',
                                                            'func',
                                                            'func',
                                                            'func',
                                                            'func',
                                                            'func',
                                                            'func']; */




          break;

        case 'course':
                        //basic info o kurzu
            $fieldObject->courseBasicInfo->categoryName = 'Základní info o kurzu';
            $fieldObject->courseBasicInfo->fields = ['id','categoryId','categoryName()','fullname','shortname','startdate','enddate'];
            $fieldObject->courseBasicInfo->translations = ['id','ID kategorie','Kraj/Kategorie','Celý název','shortcode','začátek kurzu','konec kurzu'];
            $fieldObject->courseBasicInfo->type = ['int','int','func','text','text','date','date']; 

            // advanced info o kurzu
            $fieldObject->courseAdvancedInfo->categoryName = 'Další info o kurzu';
            $fieldObject->courseAdvancedInfo->fields = $DB->get_fieldset_sql("SELECT shortname FROM mdl_customfield_field");
            $fieldObject->courseAdvancedInfo->translations = $DB->get_fieldset_sql("SELECT name FROM mdl_customfield_field");
            $fieldObject->courseAdvancedInfo->type = $DB->get_fieldset_sql("SELECT datatype FROM mdl_user_info_field"); 

             //special functions   
            $fieldObject->courseSpecialInfo->categoryName = 'Speciální informace o kurzu';
            $fieldObject->courseSpecialInfo->fields = ['numberOfStudents','numberOfNonStudents',
                                                    'numberOfCompletions','numberOfCertificates',
                                                    'numberOfOsvedceni','numberOfCertifikatu',
                                                    'courseContacts']; 

            $fieldObject->courseSpecialInfo->translations = ['Počet studentů','Počet ostatních',
                                                    'Počet úspěšných dokončení','Počet Osvědčení + Certifikátů',
                                                    'Počet Osvědčení','Počet Certifikátů',
                                                    'Kontaktní osoba(y)']; 
            $fieldObject->courseSpecialInfo->type = ['Pocet_studentu','Pocet_ostatnich',
            'Pocet_uspesnych_dokonceni','Pocet_osvedceni_a_certifikatu',
            'Pocet_osvedceni','Pocet_certifikatu',
            'Kontaktni_osoba'];
            

            /*$fieldObject->courseSpecialInfo->type = ['func','func',
                                                    'func','func',
                                                    'func','func',
                                                    'func']; */

          break;   
          
        case 'simplecertificate_issues':
            //basic info o certifikátu
            $fieldObject->certificateBasicInfo->categoryName = 'Základní info o certifikátu';
            $fieldObject->certificateBasicInfo->fields = ['id','userid','coursecode','coursecertnumber','nadpis','hours','poradove_cislo_kod','datum','time_created'];
            $fieldObject->certificateBasicInfo->translations = ['id','id uživatele','Zkratka kurzu','Pořadové číslo certifikátu','Certifikát/Osvědčení','Počet hodin','Pořadový kód','Datum na certifikátu','Reálné datum vystavení certifikátu'];
            $fieldObject->certificateBasicInfo->type = ['int','int','text','int','text','int','text','text','date']; 

            //basic info o kurzu
            $fieldObject->courseBasicInfo->categoryName = 'Základní info o kurzu';
            $fieldObject->courseBasicInfo->fields = ['course','categoryId','categoryName','fullname','shortname','startdate','enddate'];
            $fieldObject->courseBasicInfo->translations = ['id','ID kategorie','Kraj/Kategorie','Celý název','shortcode','začátek kurzu','konec kurzu'];
            $fieldObject->courseBasicInfo->type = ['int','int','text','text','text','date','date']; 


            // advanced info o kurzu
            $fieldObject->courseAdvancedInfo->categoryName = 'Další info o kurzu';
            $fieldObject->courseAdvancedInfo->fields = $DB->get_fieldset_sql("SELECT shortname FROM mdl_customfield_field");
            $fieldObject->courseAdvancedInfo->translations = $DB->get_fieldset_sql("SELECT name FROM mdl_customfield_field");
            $fieldObject->courseAdvancedInfo->type = $DB->get_fieldset_sql("SELECT datatype FROM mdl_user_info_field"); 

            // special funcions course
            $fieldObject->courseSpecialInfo->categoryName = 'Speciální informace o kurzu';
            $fieldObject->courseSpecialInfo->fields = ['numberOfStudents','numberOfNonStudents',
                                                    'numberOfCompletions','numberOfCertificates',
                                                    'numberOfOsvedceni','numberOfCertifikatu',
                                                    'courseContacts']; 
            $fieldObject->courseSpecialInfo->translations = ['Počet studentů','Počet ostatních',
                                                    'Počet úspěšných dokončení','Počet Osvědčení + Certifikátů',
                                                    'Počet Osvědčení','Počet Certifikátů',
                                                    'Kontaktní osoba(y)']; 
          $fieldObject->courseSpecialInfo->type = ['Pocet_studentu','Pocet_ostatnich',
          'Pocet_uspesnych_dokonceni','Pocet_osvedceni_a_certifikatu',
          'Pocet_osvedceni','Pocet_certifikatu',
          'Kontaktni_osoba'];
          /*  $fieldObject->courseSpecialInfo->type = ['func','func',
                                                    'func','func',
                                                    'func','func',
                                                    'func']; */

                        //basic info o uživateli
            $fieldObject->userBasicInfo->categoryName = 'Základní info o uživateli';
            $fieldObject->userBasicInfo->fields = ['userid','username','email','firstnamephonetic','firstname','lastname','lastnamephonetic','firstaccess','lastaccess'];
            $fieldObject->userBasicInfo->translations = ['id','uživatelské jméno','email','titul před jménem','jméno','příjmení','titul za jménem','první připojení','poslední připojení'];
            $fieldObject->userBasicInfo->type = ['int','text','text','text','text','text','text','date','date']; 

                // advanced info o uzivateli
            $fieldObject->userAdvancedInfo->categoryName = 'Další info o uživateli';
            $fieldObject->userAdvancedInfo->fields = $DB->get_fieldset_sql("SELECT shortname FROM mdl_user_info_field  WHERE id != 19");
            $fieldObject->userAdvancedInfo->translations = $DB->get_fieldset_sql("SELECT name FROM mdl_user_info_field  WHERE id != 19");
            $fieldObject->userAdvancedInfo->type = $DB->get_fieldset_sql("SELECT datatype FROM mdl_user_info_field  WHERE id != 19"); 

              // special funkce uzivatele
            $fieldObject->userSpecialInfo->categoryName = 'Informace o postupu kurzy';
            $fieldObject->userSpecialInfo->fields = ['countCoursesEnrolledAllTime','countCoursesEnrolledInFilteredTime',
            'countCompletedCoursesAlltime','countCompletedCoursesFilteredTime',
            'countHoursInCompletedCoursesAllTime','countHoursInCompletedCoursesFilteredTime',
            'countHoursInPresentionCompletedCoursesAllTime','countHoursInPresentionCompletedCoursesFilteredTime',
            'countHoursInDistantionCompletedCoursesAllTime','countHoursInDistantionCompletedCoursesFilteredTime',
            'countHoursInOnlineCompletedCoursesAllTime','countHoursInOnlineCompletedCoursesFilteredTime',
            'countCertificateOsvedceniAllTime','countCertificateOsvedceniFilteredTime',
            'countCertificateCertificateAllTime','countCertificateCertificateFilteredTime'];

              $fieldObject->userSpecialInfo->translations = ['Celkový počet zapsaných kurzů','Počet zapsaných kurzů v daném období',
                          'Celkový počet dokončených kurzů','Počet dokončených kurzů v daném období',
                          'Celkový počet hodin ve splněných kurzech','Počet hodin v kurzech v daném období',
                          'Celkový počet hodin ve splněných prezenčních kurzech','Počet hodin v prezenčních kurzech v daném období',
                          'Celkový počet hodin ve splněných distančních kurzech','Počet hodin v distančních kurzech v daném období',
                          'Celkový počet hodin ve splněných online kurzech','Počet hodin v online kurzech v daném období',
                          'Celkový počet Osvědčení','Počet počet Osvědčení v daném období',
                          'Celkový počet Certifikátů','Počet počet Certifikátů v daném období'];


              $fieldObject->userSpecialInfo->type = ['Celkovy_pocet_zapsanych_kurzu','Pocet_zapsanych_kurzu_v_danem_obdobi',
              'Celkovy_pocet_dokoncenych_kurzu','Pocet_dokoncenych_kurzu_v_danem_obdobi',
              'Celkovy_pocet_hodin_ve_splnenych_kurzech','Pocet_hodin_v_kurzech_v_danem_obdobi',
              'Celkovy_pocet_hodin_ve_splnenych_prezencnich_kurzech','Pocet_hodin_v_prezencnich_kurzech_v_danem_obdobi',
              'Celkovy_pocet_hodin_ve_splnenych_distancnich_kurzech','Pocet_hodin_v_distancnich_kurzech_v_danem_obdobi',
              'Celkovy_pocet_hodin_ve_splnenych_online_kurzech','Pocet_hodin_v_online_kurzech_v_danem_obdobi',
              'Celkovy_pocet_Osvedceni','Pocet_pocet_Osvedceni_v_danem_obdobi',
              'Celkovy_pocet_Certifikatu','Pocet_pocet_Certifikatu_v_danem_obdobi'];            

              /*$fieldObject->userSpecialInfo->type = ['func','func',
                          'func','func',
                          'func','func',
                          'func','func',
                          'func','func',
                          'func','func',
                          'func','func']; */
          break;  
          
        case 'course_completions':
            //basic course completions data
            $fieldObject->completionBasicInfo->categoryName = 'Základní info o dokončení kurzu';
            $fieldObject->completionBasicInfo->fields = ['id','userid','course','timeenrolled','timecompleted'];
            $fieldObject->completionBasicInfo->translations = ['ID dokončení','ID uživatele','ID kurzu','Datum zapsání uživatele do kurzu','Reálný čas dokončení kurzu'];
            $fieldObject->completionBasicInfo->type = ['int','int','int','date','date']; 
            
            
            //basic info o kurzu
            $fieldObject->courseBasicInfo->categoryName = 'Základní info o kurzu';
            $fieldObject->courseBasicInfo->fields = ['course','category','categoryName','fullname','shortname','startdate','enddate'];
            $fieldObject->courseBasicInfo->translations = ['id','ID kategorie','Kraj/Kategorie','Celý název','shortcode','začátek kurzu','konec kurzu'];
            $fieldObject->courseBasicInfo->type = ['int','int','text','text','text','date','date']; 


            // advanced info o kurzu
            $fieldObject->courseAdvancedInfo->categoryName = 'Další info o kurzu';
            $fieldObject->courseAdvancedInfo->fields = $DB->get_fieldset_sql("SELECT shortname FROM mdl_customfield_field");
            $fieldObject->courseAdvancedInfo->translations = $DB->get_fieldset_sql("SELECT name FROM mdl_customfield_field");
            $fieldObject->courseAdvancedInfo->type = $DB->get_fieldset_sql("SELECT datatype FROM mdl_user_info_field"); 

            
            // special funcions course
            $fieldObject->courseSpecialInfo->categoryName = 'Speciální informace o kurzu';
            $fieldObject->courseSpecialInfo->fields = ['numberOfStudents','numberOfNonStudents',
                                                    'numberOfCompletions','numberOfCertificates',
                                                    'numberOfOsvedceni','numberOfCertifikatu',
                                                    'courseContacts']; 
            $fieldObject->courseSpecialInfo->translations = ['Počet studentů','Počet ostatních',
                                                    'Počet úspěšných dokončení','Počet Osvědčení + Certifikátů',
                                                    'Počet Osvědčení','Počet Certifikátů',
                                                    'Kontaktní osoba(y)']; 

            $fieldObject->courseSpecialInfo->type = ['Pocet_studentu','Pocet_ostatnich',
                                                    'Pocet_uspesnych_dokonceni','Pocet_osvedceni_a_certifikatu',
                                                    'Pocet_osvedceni','Pocet_certifikatu',
                                                    'Kontaktni_osoba'];
           /* $fieldObject->courseSpecialInfo->type = ['func','func',
                                                    'func','func',
                                                    'func','func',
                                                    'func']; */

                        //basic info o uživateli
            $fieldObject->userBasicInfo->categoryName = 'Základní info o uživateli';
            $fieldObject->userBasicInfo->fields = ['userid','username','email','firstnamephonetic','firstname','lastname','lastnamephonetic','firstaccess','lastaccess'];
            $fieldObject->userBasicInfo->translations = ['id','uživatelské jméno','email','titul před jménem','jméno','příjmení','titul za jménem','první připojení','poslední připojení'];
            $fieldObject->userBasicInfo->type = ['int','text','text','text','text','text','text','date','date']; 

                // advanced info o uzivateli
            $fieldObject->userAdvancedInfo->categoryName = 'Další info o uživateli';
            $fieldObject->userAdvancedInfo->fields = $DB->get_fieldset_sql("SELECT shortname FROM mdl_user_info_field  WHERE id != 19");
            $fieldObject->userAdvancedInfo->translations = $DB->get_fieldset_sql("SELECT name FROM mdl_user_info_field  WHERE id != 19");
            $fieldObject->userAdvancedInfo->type = $DB->get_fieldset_sql("SELECT datatype FROM mdl_user_info_field  WHERE id != 19"); 

            //special funkce uživatele
            $fieldObject->userSpecialInfo->categoryName = 'Informace o postupu kurzy';

            $fieldObject->userSpecialInfo->fields = ['countCoursesEnrolledAllTime','countCoursesEnrolledInFilteredTime',
            'countCompletedCoursesAlltime','countCompletedCoursesFilteredTime',
            'countHoursInCompletedCoursesAllTime','countHoursInCompletedCoursesFilteredTime',
            'countHoursInPresentionCompletedCoursesAllTime','countHoursInPresentionCompletedCoursesFilteredTime',
            'countHoursInDistantionCompletedCoursesAllTime','countHoursInDistantionCompletedCoursesFilteredTime',
            'countHoursInOnlineCompletedCoursesAllTime','countHoursInOnlineCompletedCoursesFilteredTime',
            'countCertificateOsvedceniAllTime','countCertificateOsvedceniFilteredTime',
            'countCertificateCertificateAllTime','countCertificateCertificateFilteredTime'];

              $fieldObject->userSpecialInfo->translations = ['Celkový počet zapsaných kurzů','Počet zapsaných kurzů v daném období',
                          'Celkový počet dokončených kurzů','Počet dokončených kurzů v daném období',
                          'Celkový počet hodin ve splněných kurzech','Počet hodin v kurzech v daném období',
                          'Celkový počet hodin ve splněných prezenčních kurzech','Počet hodin v prezenčních kurzech v daném období',
                          'Celkový počet hodin ve splněných distančních kurzech','Počet hodin v distančních kurzech v daném období',
                          'Celkový počet hodin ve splněných online kurzech','Počet hodin v online kurzech v daném období',
                          'Celkový počet Osvědčení','Počet počet Osvědčení v daném období',
                          'Celkový počet Certifikátů','Počet počet Certifikátů v daném období'];

              $fieldObject->userSpecialInfo->type = ['Celkovy_pocet_zapsanych_kurzu','Pocet_zapsanych_kurzu_v_danem_obdobi',
                          'Celkovy_pocet_dokoncenych_kurzu','Pocet_dokoncenych_kurzu_v_danem_obdobi',
                          'Celkovy_pocet_hodin_ve_splnenych_kurzech','Pocet_hodin_v_kurzech_v_danem_obdobi',
                          'Celkovy_pocet_hodin_ve_splnenych_prezencnich_kurzech','Pocet_hodin_v_prezencnich_kurzech_v_danem_obdobi',
                          'Celkovy_pocet_hodin_ve_splnenych_distancnich_kurzech','Pocet_hodin_v_distancnich_kurzech_v_danem_obdobi',
                          'Celkovy_pocet_hodin_ve_splnenych_online_kurzech','Pocet_hodin_v_online_kurzech_v_danem_obdobi',
                          'Celkovy_pocet_Osvedceni','Pocet_pocet_Osvedceni_v_danem_obdobi',
                          'Celkovy_pocet_Certifikatu','Pocet_pocet_Certifikatu_v_danem_obdobi'];


             /* $fieldObject->userSpecialInfo->type = ['func','func',
                          'func','func',
                          'func','func',
                          'func','func',
                          'func','func',
                          'func','func',
                          'func','func']; */



          break;  





    }









  
  return $fieldObject;
 }


?>