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
use local_my_organization\Course\Course;

require_once '../../config.php';
defined('MOODLE_INTERNAL') || die;
global $USER,$DB,$CFG;
///

$PAGE->set_url('/local/my_organization/index.php');

$strPageTitle = get_string('modulename','local_my_organization');
$PAGE->set_title($strPageTitle);
echo '<script>const USER = '.$USER->id.';
        const HOMEURL = "'.$CFG->wwwroot.'"</script>';
$PAGE->navbar->add($PAGE->title, $PAGE->url);

if(!User::isCapable()){
  echo $OUTPUT->header();
  echo '<h1>'.get_string('no_capabality','local_my_organization').'</h1>';
  redirect($CFG->wwwroot, get_string('redirect','local_my_organization'), 5);

  echo $OUTPUT->footer();
}else{
    //$PAGE->requires->js('/local/my_organization/dist/main.js');
    //$PAGE->requires->js('/local/my_organization/assets/js/src/main.js');
    /* $PAGE->requires->js('/local/my_organization/assets/js/output.js');
    $PAGE->requires->js('/local/my_organization/assets/js/categories.js');
    $PAGE->requires->js('/local/my_organization/assets/js/users.js');
    $PAGE->requires->js('/local/my_organization/assets/js/courses.js');
    $PAGE->requires->js('/local/my_organization/assets/js/listeners.js'); */
    $PAGE->requires->css('/local/my_organization/dist/main.css');
    //to production//

    $PAGE->requires->js('/local/my_organization/dist/main.js');
    //end of production

    //dev
    //echo '<script type="module" src="'.$CFG->wwwroot.'/local/my_organization/dist/main.js"></script>';

    //end of dev



    echo $OUTPUT->header();
    /* $test = Course::getCoursesListAll(3);
    \print_r($test); */
    echo '<div class="alerts-div"></div>';
    echo '<div id="head-utils">
          <input id="search-field" type="search" size="40" placeholder="'.get_string('placeholder','local_my_organization').'"</input>
          <button id="search-button">'.get_string('search','local_my_organization').'</button>
          <button class="show-my-categories" >'.get_string('my_categories','local_my_organization').'</button>';

    /* $admins = $DB->get_field_sql('SELECT value FROM {config} WHERE name = ?',array('siteadmins'));
    $admins = \explode(',',$admins); */
    if(User::isAdmin()){
      echo ' <button class="admin-categories">Jsem administrátor</button>';
    }
    echo  '</div>';

    echo '<div id="results">
            <div id="results-users"></div>
            <div id="results-categories"></div>
            <div id="results-courses"></div>
            
          </div>';
    




    echo $OUTPUT->footer();
}
