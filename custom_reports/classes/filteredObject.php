<?php


class FilteredObject{
    public $name;
    public $type;
    public $description;
    public $options;
    public $querry;


    function __construct($name,$type,$description,$options,$querry){
            $this->name = $name;
            $this->type = $type;
            $this->description = $description;
            $this->options = $options;
            $this->querry = $querry;

    }

    function getName() {
		return $this->name;
	}


}