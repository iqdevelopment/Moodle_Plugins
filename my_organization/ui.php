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
 * @package    local_my_organization
 * @copyright  2021 Simon Zajicek
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_my_organization;
use local_my_organization\Category\Category;
use local_my_organization\User\User;

require_once '../../config.php';
require_login();
global $USER,$DB,$CFG;

$PAGE->set_url('/local/my_organization/index.php');

$PAGE->navbar->add($PAGE->title, $PAGE->url);
$PAGE->requires->js('/local/my_organization/js/custom.js');
$PAGE->requires->css('/local/my_organization/css/css.css');



$strPageTitle = get_string('modulename','local_my_organization');

$PAGE->set_title($strPageTitle);

echo $OUTPUT->header();

/* $obj = new main\notification_template();
echo $obj->renderTemplates();
echo "<br><br>";
echo $obj->renderTemplateCreator(); */
$data = new \stdClass();
$data->name = 'zapis2';
$data->idnumber = 'zapis2';
$data->id = $DB->get_field_sql('SELECT id FROM {course_categories} WHERE idnumber = ?',array($data->idnumber));
$data->parent = 131;

//$category = category\category::updateCategoryParent($data);
//$category = \core_course_category::create($data);

//$output = Category::getCategoryChildren(3);

$user = new User(8706);
$user->getUserContextsAndRoles();
//$user->getUserChildUsers();
$user->getAllContexts();
//print_r($user);
$course = new \stdClass();
//$user->allowedCoursesObjects();
$user->getContextRoleCategory();


//$message =  $user->enrolUser($user->childUsers[5],$course);
//$message = $user->unenrolUser($user->childUsers[5],$course);
/*foreach ($user->childContexts as $key => $value) {
    $name = $DB->get_field_sql('SELECT name FROM {course_categories}
                                INNER JOIN {context} ON {context}.instanceid = {course_categories}.id
                                WHERE {context}.id = ?',array($value));
    //echo $value.'---'.$name.'<br><br><br><br><br>';
}*/

//$user->getUserEnrolments();
//$user->getChildUsersEnrolments();
//$message = $user->setUserRole(131,'teacher');
//$message = $user->setUserRoleForce(131,'specialista2');
echo $message;


echo $OUTPUT->footer();