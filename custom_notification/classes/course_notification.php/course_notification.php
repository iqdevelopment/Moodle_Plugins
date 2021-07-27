<?php
namespace local_custom_notification\course_notification;

class course_notification{

    public $courseid;
    public $notificationText;
    public $type;
    public $enabled;
    public $sent;

    
    function __construct($courseid){
        $this->courseid = $courseid;

    }

    public function getCoursePeriodicalNotification()
    {   global $DB;
        $periodicNotification = $DB->get_record_sql('SELECT * FROM {custom_notification} WHERE courseid = ? AND type != 0',array($this->courseid));
        if($periodicNotification){
             return $periodicNotification->text;
            }
        else{
            $periodicNotification = $DB->get_record_sql('SELECT * FROM {custom_notification} WHERE courseid = 0 type != 0');
            
            if ($periodicNotification){
                return $periodicNotification->text;
            }else{
                return get_string('noPeriodicText','local_custom_notification');
            }
        }
        
    }


    public function getCourseOneTimeNotification()
    {   global $DB;
        $periodicNotification = $DB->get_record_sql('SELECT * FROM {custom_notification} WHERE courseid = ? AND type = 0',array($this->courseid));
        if($periodicNotification){
             return $periodicNotification->text;
            }
        else{
            $periodicNotification = $DB->get_record_sql('SELECT * FROM {custom_notification} WHERE courseid = 0 AND type = 0');
            
            if ($periodicNotification){
                return $periodicNotification->text;
            }else{
                return get_string('noOneTimeText','local_custom_notification');
            }
        }
        
    }


   /* public function fillNotification()
    {
        global $DB;
        $tofill = $DB->get_records_sql('SELECT {course}.id,{course}.category FROM {course}
                            WHERE {course}.category IN 
                            (
                                SELECT value FROM {config_plugins}  WHERE plugin = ? AND name = ?
                            )
                             AND visible = 1',
                             array('local_custom_notification','categories'));
        //$defaultNotificationText = $DB->get_field_sql
        foreach ($tofill as $key => $course) {
            $check = $DB->get_fieldset_sql('SELECT id FROM {custom_notification} WHERE courseid = ?',array($course->id));
            if($check){
                continue;
            }else{
                $sql = 'INSERT INTO {custom_notification} (courseid,type,sent,enabled,timecreated,text) values (?,?,?,?,?,?)';
                $types = $DB->get_records_sql('SELECT * FROM  {config_plugins}  WHERE plugin = ? AND name LIKE "%type%"',array('local_custom_notification'));
                foreach ($types as $type) {
                    $name = explode($type->name);
                    $name = $name[1];
                    $DB->execute($sql,array($course->id,$name,0,$type->value,time(),));
                }
               
                
            }


        }
    }*/














}


//defined('MOODLE_INTERNAL') || die();

