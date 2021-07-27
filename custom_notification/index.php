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
global $USER,$DB,$CFG;

$PAGE->set_url('/local/custom_notification/index.php');
//$PAGE->set_heading($course->fullname);
//$PAGE->set_heading(format_string($course->fullname));
$PAGE->navbar->add($PAGE->title, $PAGE->url);
$PAGE->requires->js('/local/custom_notification/js/custom.js');
$PAGE->requires->css('/local/custom_notification/css/css.css');



$strPageTitle = get_string('modulename','local_custom_notification');
//$strPageTitle = 'pi4e';

$PAGE->set_title($strPageTitle);

echo $OUTPUT->header();

$obj = new main\notification_template();
echo $obj->renderTemplates();
echo "<br><br>";
echo $obj->renderTemplateCreator();



echo $OUTPUT->footer();