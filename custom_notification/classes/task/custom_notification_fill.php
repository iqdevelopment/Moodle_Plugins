<?php
namespace local_custom_notification\task;

use local_custom_notification\main;
defined('MOODLE_INTERNAL') || die();


 
/**
 * An example of a scheduled task.
 */
class custom_notification_fill extends \core\task\scheduled_task {
 
    /**
     * Return the task's name as shown in admin screens.
     *
     * @return string
     */
    public function get_name() {
        return get_string('taskfill', 'local_custom_notification');
    }
 
    /**
     * Execute the task.
     */
    public function execute() { 
        global $DB,$CFG;
        $obj = new main\notification_template();
        $obj->fillNotificationTask();
  
     
    }
}