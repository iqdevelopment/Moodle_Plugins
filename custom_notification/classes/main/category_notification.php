<?php
namespace local_custom_notification\main;

class custom_notification{
    public $categoryarray;
    public $notificationText;


    public function renderTemplates()
    {   global $DB;
        $alreadySet = $DB->get_records_sql('SELECT * FROM {config_plugins} WHERE plugin = ? AND name like "%category_id%"',array('local_custom_notification'));
        if($alreadySet){

        }else{
           $output =  get_string('no_templates','local_custom_notification').'<br>';
           $output = $DB->get_field_sql('SELECT value FROM {config_plugins} WHERE plugin = ? AND name = ?',array('local_custom_notification','default_text_periodical'));   
        }
        # code...
    }



}