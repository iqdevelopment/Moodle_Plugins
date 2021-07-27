<?php
namespace local_custom_reports\task;

use local_custom_reports\main;
defined('MOODLE_INTERNAL') || die();
global $DB,$CFG;
//require $CFG->dirroot.'/local/custom_reports/lib.php';


 
/**
 * An example of a scheduled task.
 */
class course extends \core\task\scheduled_task {
 
    /**
     * Return the task's name as shown in admin screens.
     *
     * @return string
     */
    public function get_name() {
        return get_string('course_task', 'local_custom_reports');
    }
 
    /**
     * Execute the task.
     */
    public function execute() { 
        global $DB,$CFG;
        $obj = new main\course_class();
        $obj->GetColumns();
        $obj->GetFilters();
        return \local_custom_reports\main\common::Process($obj);
  
     
    }
}