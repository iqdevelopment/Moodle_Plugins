<?php
namespace local_custom_reports\main;

class course_class{

    public $table = 'course';
    public $tablefilters = '';
    public $columns;
    public $querry = "SELECT {course}.id AS courseid,{course}.category AS id_kategorie,{course}.fullname AS cely_nazev_kurzu,{course}.shortname AS short_code_kurzu,{course}.startdate AS zacatek_kurzu,{course}.enddate AS konec_kurzu,{course_categories}.name AS nazev_kategorie FROM {course} INNER JOIN {course_categories} ON {course_categories}.id = {course}.category";
    public $recipient;
    public $initialdate;
    
    public function GetColumns()
    {       
        
        $this->columns[] = array('id'=> "courseBasicInfo", 'name'=> "id", 'value'=> "int");
        $this->columns[] = array('id'=> "courseBasicInfo", 'name'=> "categoryId", 'value'=> "int");
        $this->columns[] = array('id'=> "courseBasicInfo", 'name'=> "categoryName()", 'value'=> "func");
        $this->columns[] = array('id'=> "courseBasicInfo", 'name'=> "fullname", 'value'=> "text");
        $this->columns[] = array('id'=> "courseBasicInfo", 'name'=> "shortname", 'value'=> "text");
        $this->columns[] = array('id'=> "courseBasicInfo", 'name'=> "startdate", 'value'=> "date");
        $this->columns[] = array('id'=> "courseBasicInfo", 'name'=> "enddate", 'value'=> "date");
        $this->columns[] = array('id'=> "courseAdvancedInfo", 'name'=> "kod_kurzu", 'value'=> "menu");
        $this->columns[] = array('id'=> "courseAdvancedInfo", 'name'=> "kod_ved_urednik", 'value'=> "menu");
        $this->columns[] = array('id'=> "courseAdvancedInfo", 'name'=> "kod_urednik", 'value'=> "menu");
        $this->columns[] = array('id'=> "courseAdvancedInfo", 'name'=> "mesto", 'value'=> "menu");
        $this->columns[] = array('id'=> "courseAdvancedInfo", 'name'=> "misto_konani", 'value'=> "menu");
        $this->columns[] = array('id'=> "courseAdvancedInfo", 'name'=> "popis_kurzu", 'value'=> "datetime");
        $this->columns[] = array('id'=> "courseAdvancedInfo", 'name'=> "multi_date_course", 'value'=> "text");
        $this->columns[] = array('id'=> "courseAdvancedInfo", 'name'=> "cas_kurzu", 'value'=> "text");
        $this->columns[] = array('id'=> "courseAdvancedInfo", 'name'=> "druh_kurzu", 'value'=> "text");
        $this->columns[] = array('id'=> "courseAdvancedInfo", 'name'=> "zamekkod", 'value'=> "text");
        $this->columns[] = array('id'=> "courseAdvancedInfo", 'name'=> "max_students", 'value'=> "text");
        $this->columns[] = array('id'=> "courseSpecialInfo", 'name'=> "numberOfStudents", 'value'=> "Pocet_studentu");
        $this->columns[] = array('id'=> "courseSpecialInfo", 'name'=> "numberOfNonStudents", 'value'=> "Pocet_ostatnich");
        $this->columns[] = array('id'=> "courseSpecialInfo", 'name'=> "numberOfCompletions", 'value'=> "Pocet_uspesnych_dokonceni");
        $this->columns[] = array('id'=> "courseSpecialInfo", 'name'=> "numberOfCertificates", 'value'=> "Pocet_osvedceni_a_certifikatu");
        $this->columns[] = array('id'=> "courseSpecialInfo", 'name'=> "numberOfOsvedceni", 'value'=> "Pocet_osvedceni");
        $this->columns[] = array('id'=> "courseSpecialInfo", 'name'=> "numberOfCertifikatu", 'value'=> "Pocet_certifikatu");
        $this->columns[] = array('id'=> "courseSpecialInfo", 'name'=> "courseContacts", 'value'=> "Kontaktni_osoba");
        
    }

    public function GetFilters()
    {
        $this->$tablefilters[] = array( 'filter'=> "startdate", 'data'=> "", 'querry'=> "{course}.startdate > #data#", 'type' => 'date');
        $this->$tablefilters[] = array( 'filter'=> "enddate", 'data'=> "", 'querry'=> "{course}.enddate < #data#", 'type' => 'date');
    }





}