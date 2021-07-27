<?php


class Field {
    //properities
    public $nameForUsers;
    public $name;


    //methods
    public function setDisplayName($name){
        $this->nameForUsers = $name;
    }

    public function setSystemInfo($name){

        $this->name = $name;


    }


}




?>