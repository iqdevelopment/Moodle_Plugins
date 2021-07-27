<?php
namespace local_custom_reports\main;

class user_class{

    public $table = 'user';
    public $tablefilters;
    public $columns;
    public $querry = "SELECT {user}.id AS userid,{user}.username,{user}.email,{user}.firstnamephonetic AS titul_pred,{user}.firstname AS jmeno,{user}.lastname AS prijmeni,{user}.lastnamephonetic AS titul_za,{user}.firstaccess AS prvni_pripojeni,{user}.lastaccess AS posledni_pripojeni FROM {user} ";
    public $recipient;
    public $initialdate;
    
    public function GetColumns()
    {
        
        $this->columns[] = array( 'id'=> "userBasicInfo", 'name'=> "id", 'value'=> "int");
        $this->columns[] = array( 'id'=> "userBasicInfo", 'name'=> "username", 'value'=> "text");
        $this->columns[] = array( 'id'=> "userBasicInfo", 'name'=> "email", 'value'=> "text");
        $this->columns[] = array( 'id'=> "userBasicInfo", 'name'=> "firstnamephonetic", 'value'=> "text");
        $this->columns[] = array( 'id'=> "userBasicInfo", 'name'=> "firstname", 'value'=> "text");
        $this->columns[] = array( 'id'=> "userBasicInfo", 'name'=> "lastname", 'value'=> "text");
        $this->columns[] = array( 'id'=> "userBasicInfo", 'name'=> "lastnamephonetic", 'value'=> "text");
        $this->columns[] = array( 'id'=> "userBasicInfo", 'name'=> "firstaccess", 'value'=> "date");
        $this->columns[] = array( 'id'=> "userBasicInfo", 'name'=> "lastaccess", 'value'=> "date");
        $this->columns[] = array( 'id'=> "userAdvancedInfo", 'name'=> "Pohlavi", 'value'=> "menu");
        $this->columns[] = array( 'id'=> "userAdvancedInfo", 'name'=> "Postaveni_na_trhu_prace", 'value'=> "menu");
        $this->columns[] = array( 'id'=> "userAdvancedInfo", 'name'=> "vzdelani", 'value'=> "menu");
        $this->columns[] = array( 'id'=> "userAdvancedInfo", 'name'=> "Podle_sektoru_ekonomiky_aktivni", 'value'=> "menu");
        $this->columns[] = array( 'id'=> "userAdvancedInfo", 'name'=> "Podle_typu_znevyhodneni", 'value'=> "menu");
        $this->columns[] = array( 'id'=> "userAdvancedInfo", 'name'=> "Datum_narozeni", 'value'=> "datetime");
        $this->columns[] = array( 'id'=> "userAdvancedInfo", 'name'=> "Obec", 'value'=> "text");
        $this->columns[] = array( 'id'=> "userAdvancedInfo", 'name'=> "Cast_obce", 'value'=> "text");
        $this->columns[] = array( 'id'=> "userAdvancedInfo", 'name'=> "Ulice", 'value'=> "text");
        $this->columns[] = array( 'id'=> "userAdvancedInfo", 'name'=> "Cislo_popisne", 'value'=> "text");
        $this->columns[] = array( 'id'=> "userAdvancedInfo", 'name'=> "Cislo_orientacni", 'value'=> "text");
        $this->columns[] = array( 'id'=> "userAdvancedInfo", 'name'=> "Znak_cisla_orientacniho", 'value'=> "text");
        $this->columns[] = array( 'id'=> "userAdvancedInfo", 'name'=> "PSC", 'value'=> "text");
        $this->columns[] = array( 'id'=> "userAdvancedInfo", 'name'=> "Telefon", 'value'=> "text");
        $this->columns[] = array( 'id'=> "userAdvancedInfo", 'name'=> "Datum_vstupu_do_projektu", 'value'=> "datetime");
        $this->columns[] = array( 'id'=> "userAdvancedInfo", 'name'=> "Role_uzivatele", 'value'=> "menu");
        $this->columns[] = array( 'id'=> "userAdvancedInfo", 'name'=> "misto_narozeni", 'value'=> "text");
        $this->columns[] = array( 'id'=> "userAdvancedInfo", 'name'=> "Pristup_k_bydleni", 'value'=> "menu");
        $this->columns[] = array( 'id'=> "userAdvancedInfo", 'name'=> "zamestnanec_verejneho_sektoru", 'value'=> "menu");
        $this->columns[] = array( 'id'=> "userAdvancedInfo", 'name'=> "kraj", 'value'=> "menu");
        $this->columns[] = array( 'id'=> "userAdvancedInfo", 'name'=> "mistozam", 'value'=> "text");
        $this->columns[] = array( 'id'=> "userAdvancedInfo", 'name'=> "mesto_obec_zam", 'value'=> "menu");
        $this->columns[] = array( 'id'=> "userAdvancedInfo", 'name'=> "misto_zam_typ", 'value'=> "menu");
        $this->columns[] = array( 'id'=> "userSpecialInfo", 'name'=> "countCoursesEnrolledAllTime", 'value'=> "Celkovy_pocet_zapsanych_kurzu");
        $this->columns[] = array( 'id'=> "userSpecialInfo", 'name'=> "countCompletedCoursesAlltime", 'value'=> "Celkovy_pocet_dokoncenych_kurzu");
        $this->columns[] = array( 'id'=> "userSpecialInfo", 'name'=> "countHoursInCompletedCoursesAllTime", 'value'=> "Celkovy_pocet_hodin_ve_splnenych_kurzech");
        $this->columns[] = array( 'id'=> "userSpecialInfo", 'name'=> "countHoursInPresentionCompletedCoursesAllTime", 'value'=> "Celkovy_pocet_hodin_ve_splnenych_prezencnich_kurzech");
        $this->columns[] = array( 'id'=> "userSpecialInfo", 'name'=> "countHoursInDistantionCompletedCoursesAllTime", 'value'=> "Celkovy_pocet_hodin_ve_splnenych_distancnich_kurzech");
        $this->columns[] = array( 'id'=> "userSpecialInfo", 'name'=> "countHoursInOnlineCompletedCoursesAllTime", 'value'=> "Celkovy_pocet_hodin_ve_splnenych_online_kurzech");
        $this->columns[] = array( 'id'=> "userSpecialInfo", 'name'=> "countCertificateOsvedceniAllTime", 'value'=> "Celkovy_pocet_Osvedceni");
        $this->columns[] = array( 'id'=> "userSpecialInfo", 'name'=> "countCertificateCertificateAllTime", 'value'=> "Celkovy_pocet_Certifikatu");
    }

    public function GetFilters()
    {
        $this->$tablefilters[] = array( 'filter'=> "lastaccess", 'data'=> "", 'querry'=> "{user}.lastaccess > #data#", 'type' => 'date');
    }






}