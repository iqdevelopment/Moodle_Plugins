<?php
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Plugin capabilities
 *
 * @package    local_custom_notification
 * @copyright  2021 Simon Zajicek
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


namespace local_custom_notification;
require_once '../../config.php';
require_once("$CFG->libdir/formslib.php");
require_once($CFG->dirroot.'/course/lib.php');
global $USER,$DB,$CFG;
//use classes\custom_notification\custom_notification;

$id = required_param('id', PARAM_INT);
$course = $DB->get_record('course', ['id' => $id]);

/*if (!$course) {
    print_error('invalidcourseid');
}*/
//$context = context_module::instance ($course->id);
//$PAGE->set_context($context);
//$course = get_course($id);
require_login($course);
//require_login($course, true);
//require_login();
//nastaveni stranky, kde bude plugin
$PAGE->set_url('/local/custom_notification/index.php');
//$PAGE->set_context(context_system::instance());
$PAGE->set_heading($course->fullname);
$PAGE->set_heading(format_string($course->fullname));
$PAGE->navbar->add($PAGE->title, $PAGE->url);
$PAGE->requires->js('/local/custom_notification/js/custom.js');
$PAGE->requires->css('/local/custom_notification/css/css.css');




$strPageTitle = get_string('modulename','local_custom_notification');
//$strPageTitle = 'pi4e';

$PAGE->set_title($course->fullname);

echo $OUTPUT->header();


        
$enabled = $DB->get_field_sql('SELECT value FROM {config_plugins} WHERE plugin = ? AND name = ?',array('local_custom_notification','categories'));
$enabled = explode(",",$enabled);
$oneTimeEnabled = $DB->get_field_sql('SELECT value FROM {config_plugins} WHERE plugin = ? AND name = ?',array('local_custom_notification','type_0'));
if( in_array($course->category,$enabled) AND $oneTimeEnabled){
    $obj = new main\course_notification($course->id);
    echo $obj->renderRegularForm();
    echo $obj->renderOneTimeForm();
}elseif(in_array($course->category,$enabled) AND $oneTimeEnabled){
    $obj = new main\course_notification($course->id);
    echo $obj->renderRegularForm();
}elseif (!in_array($course->category,$enabled) AND $oneTimeEnabled) {
    $obj = new main\course_notification($course->id);
    echo $obj->renderOneTimeForm();
}else{
   echo '<b>'.get_string('notification_not_allowed', 'local_custom_notification').'</b>';
}
//print_r($obj);
//echo get_string('noPeriodicText','local_custom_notification');
$kuku = new main\notification_template();
$kuku->fillNotificationTask();

echo $OUTPUT->footer();