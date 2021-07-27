<?php
namespace local_custom_reports\main;

class course_completion_class{

    public $table = "course_completions";
    public $tablefilters = '';
    public $columns;
    public $querry = "SELECT {course_completions}.id,{course_completions}.userid,{course_completions}.course AS courseid,{course_completions}.timeenrolled AS cas_zapisu,{course_completions}.timecompleted AS cas_dokonceni,{course}.startdate AS zacatek_kurzu,{course}.enddate as konec_kurzu,{course_categories}.name AS nazev_kategorie,{course}.category AS id_kategorie,{course}.fullname AS cely_nazev_kurzu,{course}.shortname AS short_code_kurzu,{course}.startdate AS zacatek_kurzu,{course}.enddate AS konec_kurzu,{user}.username,{user}.email,{user}.firstnamephonetic AS titul_pred,{user}.firstname AS jmeno,{user}.lastname AS prijmeni,{user}.lastnamephonetic AS titul_za,{user}.firstaccess AS prvni_pripojeni,{user}.lastaccess AS posledni_pripojeni FROM {course_completions} INNER JOIN {course} ON {course}.id = {course_completions}.course INNER JOIN {course_categories} ON {course_categories}.id = {course}.category INNER JOIN {user} ON {user}.id = {course_completions}.userid";
    public $recipient;
    public $initialdate;
    
    public function GetColumns()
    {       

        $this->columns[] = array('id'=> "courseBasicInfo", 'name'=> "id", 'value'=> "int");
        
            $this->columns[] = array('id' => "completionBasicInfo", 'name' => "id", 'value' => "int");
            $this->columns[] = array('id' => "completionBasicInfo", 'name' => "userid", 'value' => "int");
            $this->columns[] = array('id' => "completionBasicInfo", 'name' => "course", 'value' => "int");
            $this->columns[] = array('id' => "completionBasicInfo", 'name' => "timeenrolled", 'value' => "date");
            $this->columns[] = array('id' => "completionBasicInfo", 'name' => "timecompleted", 'value' => "date");
            $this->columns[] = array('id' => "courseBasicInfo", 'name' => "course", 'value' => "int");
            $this->columns[] = array('id' => "courseBasicInfo", 'name' => "category", 'value' => "int");
            $this->columns[] = array('id' => "courseBasicInfo", 'name' => "categoryName", 'value' => "text");
            $this->columns[] = array('id' => "courseBasicInfo", 'name' => "fullname", 'value' => "text");
            $this->columns[] = array('id' => "courseBasicInfo", 'name' => "shortname", 'value' => "text");
            $this->columns[] = array('id' => "courseBasicInfo", 'name' => "startdate", 'value' => "date");
            $this->columns[] = array('id' => "courseBasicInfo", 'name' => "enddate", 'value' => "date");
            $this->columns[] = array('id' => "courseAdvancedInfo", 'name' => "kod_kurzu", 'value' => "menu");
            $this->columns[] = array('id' => "courseAdvancedInfo", 'name' => "kod_ved_urednik", 'value' => "menu");
            $this->columns[] = array('id' => "courseAdvancedInfo", 'name' => "kod_urednik", 'value' => "menu");
            $this->columns[] = array('id' => "courseAdvancedInfo", 'name' => "mesto", 'value' => "menu");
            $this->columns[] = array('id' => "courseAdvancedInfo", 'name' => "misto_konani", 'value' => "menu");
            $this->columns[] = array('id' => "courseAdvancedInfo", 'name' => "popis_kurzu", 'value' => "datetime");
            $this->columns[] = array('id' => "courseAdvancedInfo", 'name' => "multi_date_course", 'value' => "text");
            $this->columns[] = array('id' => "courseAdvancedInfo", 'name' => "cas_kurzu", 'value' => "text");
            $this->columns[] = array('id' => "courseAdvancedInfo", 'name' => "druh_kurzu", 'value' => "text");
            $this->columns[] = array('id' => "courseAdvancedInfo", 'name' => "zamekkod", 'value' => "text");
            $this->columns[] = array('id' => "courseAdvancedInfo", 'name' => "max_students", 'value' => "text");
            $this->columns[] = array('id' => "courseSpecialInfo", 'name' => "numberOfStudents", 'value' => "Pocet_studentu");
            $this->columns[] = array('id' => "courseSpecialInfo", 'name' => "numberOfNonStudents", 'value' => "Pocet_ostatnich");
            $this->columns[] = array('id' => "courseSpecialInfo", 'name' => "numberOfCompletions", 'value' => "Pocet_uspesnych_dokonceni");
            $this->columns[] = array('id' => "courseSpecialInfo", 'name' => "numberOfCertificates", 'value' => "Pocet_osvedceni_a_certifikatu");
            $this->columns[] = array('id' => "courseSpecialInfo", 'name' => "numberOfOsvedceni", 'value' => "Pocet_osvedceni");
            $this->columns[] = array('id' => "courseSpecialInfo", 'name' => "numberOfCertifikatu", 'value' => "Pocet_certifikatu");
            $this->columns[] = array('id' => "courseSpecialInfo", 'name' => "courseContacts", 'value' => "Kontaktni_osoba");
            $this->columns[] = array('id' => "userBasicInfo", 'name' => "userid", 'value' => "int");
            $this->columns[] = array('id' => "userBasicInfo", 'name' => "username", 'value' => "text");
            $this->columns[] = array('id' => "userBasicInfo", 'name' => "email", 'value' => "text");
            $this->columns[] = array('id' => "userBasicInfo", 'name' => "firstnamephonetic", 'value' => "text");
            $this->columns[] = array('id' => "userBasicInfo", 'name' => "firstname", 'value' => "text");
            $this->columns[] = array('id' => "userBasicInfo", 'name' => "lastname", 'value' => "text");
            $this->columns[] = array('id' => "userBasicInfo", 'name' => "lastnamephonetic", 'value' => "text");
            $this->columns[] = array('id' => "userBasicInfo", 'name' => "firstaccess", 'value' => "date");
            $this->columns[] = array('id' => "userBasicInfo", 'name' => "lastaccess", 'value' => "date");
            $this->columns[] = array('id' => "userAdvancedInfo", 'name' => "Pohlavi", 'value' => "menu");
            $this->columns[] = array('id' => "userAdvancedInfo", 'name' => "Postaveni_na_trhu_prace", 'value' => "menu");
            $this->columns[] = array('id' => "userAdvancedInfo", 'name' => "vzdelani", 'value' => "menu");
            $this->columns[] = array('id' => "userAdvancedInfo", 'name' => "Podle_sektoru_ekonomiky_aktivni", 'value' => "menu");
            $this->columns[] = array('id' => "userAdvancedInfo", 'name' => "Podle_typu_znevyhodneni", 'value' => "menu");
            $this->columns[] = array('id' => "userAdvancedInfo", 'name' => "Datum_narozeni", 'value' => "datetime");
            $this->columns[] = array('id' => "userAdvancedInfo", 'name' => "Obec", 'value' => "text");
            $this->columns[] = array('id' => "userAdvancedInfo", 'name' => "Cast_obce", 'value' => "text");
            $this->columns[] = array('id' => "userAdvancedInfo", 'name' => "Ulice", 'value' => "text");
            $this->columns[] = array('id' => "userAdvancedInfo", 'name' => "Cislo_popisne", 'value' => "text");
            $this->columns[] = array('id' => "userAdvancedInfo", 'name' => "Cislo_orientacni", 'value' => "text");
            $this->columns[] = array('id' => "userAdvancedInfo", 'name' => "Znak_cisla_orientacniho", 'value' => "text");
            $this->columns[] = array('id' => "userAdvancedInfo", 'name' => "PSC", 'value' => "text");
            $this->columns[] = array('id' => "userAdvancedInfo", 'name' => "Telefon", 'value' => "text");
            $this->columns[] = array('id' => "userAdvancedInfo", 'name' => "Datum_vstupu_do_projektu", 'value' => "datetime");
            $this->columns[] = array('id' => "userAdvancedInfo", 'name' => "Role_uzivatele", 'value' => "menu");
            $this->columns[] = array('id' => "userAdvancedInfo", 'name' => "misto_narozeni", 'value' => "text");
            $this->columns[] = array('id' => "userAdvancedInfo", 'name' => "Pristup_k_bydleni", 'value' => "menu");
            $this->columns[] = array('id' => "userAdvancedInfo", 'name' => "zamestnanec_verejneho_sektoru", 'value' => "menu");
            $this->columns[] = array('id' => "userAdvancedInfo", 'name' => "kraj", 'value' => "menu");
            $this->columns[] = array('id' => "userAdvancedInfo", 'name' => "mistozam", 'value' => "text");
            $this->columns[] = array('id' => "userAdvancedInfo", 'name' => "mesto_obec_zam", 'value' => "menu");
            $this->columns[] = array('id' => "userAdvancedInfo", 'name' => "misto_zam_typ", 'value' => "menu");
            $this->columns[] = array('id' => "userSpecialInfo", 'name' => "countCoursesEnrolledAllTime", 'value' => "Celkovy_pocet_zapsanych_kurzu");
            $this->columns[] = array('id' => "userSpecialInfo", 'name' => "countCoursesEnrolledInFilteredTime", 'value' => "Pocet_zapsanych_kurzu_v_danem_obdobi");
            $this->columns[] = array('id' => "userSpecialInfo", 'name' => "countCompletedCoursesAlltime", 'value' => "Celkovy_pocet_dokoncenych_kurzu");
            $this->columns[] = array('id' => "userSpecialInfo", 'name' => "countCompletedCoursesFilteredTime", 'value' => "Pocet_dokoncenych_kurzu_v_danem_obdobi");
            $this->columns[] = array('id' => "userSpecialInfo", 'name' => "countHoursInCompletedCoursesAllTime", 'value' => "Celkovy_pocet_hodin_ve_splnenych_kurzech");
            $this->columns[] = array('id' => "userSpecialInfo", 'name' => "countHoursInCompletedCoursesFilteredTime", 'value' => "Pocet_hodin_v_kurzech_v_danem_obdobi");
            $this->columns[] = array('id' => "userSpecialInfo", 'name' => "countHoursInPresentionCompletedCoursesAllTime", 'value' => "Celkovy_pocet_hodin_ve_splnenych_prezencnich_kurzech");
            $this->columns[] = array('id' => "userSpecialInfo", 'name' => "countHoursInPresentionCompletedCoursesFilteredTime", 'value' => "Pocet_hodin_v_prezencnich_kurzech_v_danem_obdobi");
            $this->columns[] = array('id' => "userSpecialInfo", 'name' => "countHoursInDistantionCompletedCoursesAllTime", 'value' => "Celkovy_pocet_hodin_ve_splnenych_distancnich_kurzech");
            $this->columns[] = array('id' => "userSpecialInfo", 'name' => "countHoursInDistantionCompletedCoursesFilteredTime", 'value' => "Pocet_hodin_v_distancnich_kurzech_v_danem_obdobi");
            $this->columns[] = array('id' => "userSpecialInfo", 'name' => "countHoursInOnlineCompletedCoursesAllTime", 'value' => "Celkovy_pocet_hodin_ve_splnenych_online_kurzech");
            $this->columns[] = array('id' => "userSpecialInfo", 'name' => "countHoursInOnlineCompletedCoursesFilteredTime", 'value' => "Pocet_hodin_v_online_kurzech_v_danem_obdobi");
            $this->columns[] = array('id' => "userSpecialInfo", 'name' => "countCertificateOsvedceniAllTime", 'value' => "Celkovy_pocet_Osvedceni");
            $this->columns[] = array('id' => "userSpecialInfo", 'name' => "countCertificateOsvedceniFilteredTime", 'value' => "Pocet_pocet_Osvedceni_v_danem_obdobi");
            $this->columns[] = array('id' => "userSpecialInfo", 'name' => "countCertificateCertificateAllTime", 'value' => "Celkovy_pocet_Certifikatu");
            $this->columns[] = array('id' => "userSpecialInfo", 'name' => "countCertificateCertificateFilteredTime", 'value' => "Pocet_pocet_Certifikatu_v_danem_obdobi");

    }

    public function GetFilters()
    {
        $this->$tablefilters[] = array( 'filter'=> "startdate", 'data'=> "", 'querry'=> "(({course}.startdate > #data# AND {course}.category NOT IN (127,124,128) ) OR ({course}.category IN (127,124,128) AND {course_completions}.timecompleted > #data#)) AND {course_completions}.timecompleted IS NOT NULL", 'type' => 'date');
        $this->$tablefilters[] = array( 'filter'=> "enddate", 'data'=> "", 'querry'=> "(({course}.enddate < #data# AND {course}.category NOT IN (127,124,128)) OR ({course}.category IN (127,124,128) AND {course_completions}.timecompleted < #data#)) AND {course_completions}.timecompleted IS NOT NULL", 'type' => 'date');
    }





}