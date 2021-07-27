<?php
namespace local_my_organization\task;

use local_my_organization\Category\Category;
defined('MOODLE_INTERNAL') || die();


 
/**
 * An example of a scheduled task.
 */
class Create_cohorts_from_categories extends \core\task\scheduled_task {
 
    /**
     * Return the task's name as shown in admin screens.
     *
     * @return string
     */
    public function get_name() {
        return get_string('create_cohorts_from_categories', 'local_my_organization');
    }
 
    /**
     * Execute the task.
     */
    public function execute() { 
        Category::TaskcreateCohortFromCourseCategory();
  
     
    }
}