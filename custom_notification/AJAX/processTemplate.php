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

require_once '../../../config.php';
global $USER,$DB,$CFG;
if($USER->id == 1){
    redirect($CFG->wwwroot,get_string('not_allowed','local_custom_notification'), 0);  
}
$PAGE->set_url('/local/custom_notification/AJAX/processTemplate.php');
//$PAGE->set_heading($course->fullname);
//$PAGE->set_heading(format_string($course->fullname));


$strPageTitle = get_string('modulename','local_custom_notification');
//$strPageTitle = 'pi4e';

$PAGE->set_title($strPageTitle);
require_login();
if(isset($_POST) AND !$_POST['delete'] AND !$_POST['update']){
    $obj = new AJAX\processor();
   $message = $obj->saveTemplate($_POST);
   //$message ='all good';
    redirect($CFG->wwwroot.'/local/custom_notification/index.php',$message, 0);
}elseif($_POST['delete'] AND !$_POST['update']){
    $obj = new AJAX\processor();
   $message = $obj->deleteTemplate($_POST['delete']);
   redirect($CFG->wwwroot.'/local/custom_notification/index.php',$message, 0);

}elseif($_POST['update']){
    $obj = new AJAX\processor();
   $message = $obj->updateTemplate($_POST);
  // $message ='redirect funguje';
   redirect($CFG->wwwroot.'/local/custom_notification/edit.php?id='.$_POST['courseid'],$message, 0);
}

