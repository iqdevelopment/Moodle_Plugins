<?php
namespace local_my_organization\task;

use local_my_organization\User\User;

defined('MOODLE_INTERNAL') || die();


 
/**
 * An example of a scheduled task.
 */
class Manage_users_and_roles extends \core\task\scheduled_task {
 
    /**
     * Return the task's name as shown in admin screens.
     *
     * @return string
     */
    public function get_name() {
        return get_string('manage_users_and_roles', 'local_my_organization');
    }
 
    /**
     * Execute the task.
     */
    public function execute() { 
        User::taskCohortsToAssignments();
  
     
    }
}