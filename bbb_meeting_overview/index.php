<?php 
// This file is part of Moodle - http://moodle.org/
        //
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
 * @package     local_bbb_meeting_overview 
 * @copyright     Simon Zajicek
 * @copyright     2021
 * @copyright     IQdevelopment

*/

namespace local_bbb_meeting_overview; 
use local_bbb_meeting_overview\API\API;
use local_bbb_meeting_overview\Meeting\Meeting;



require_once '../../config.php';
require_login();
global $USER,$DB,$CFG;
 
$PAGE->set_url('/local/bbb_meeting_overview/index.php');
$PAGE->set_title(get_string('modulename','local_bbb_meeting_overview'));
$PAGE->navbar->add($PAGE->title, $PAGE->url);
$PAGE->requires->css('/local/bbb_meeting_overview/assets/css/css.css');
echo $OUTPUT->header();
 //if needed JS $PAGE->requires->js('/local/bbb_meeting_overview/assets/js/custom.js');
 
$meetings = Meeting::getMeetings();
echo '<table class="pretty-table">
        <tr>
                <th class="coursename">'.get_string('coursename','local_bbb_meeting_overview').'</th>
                <th>'.get_string('settings','local_bbb_meeting_overview').'</th>
                <th>'.get_string('users_capacity','local_bbb_meeting_overview').'</th>
                <th>'.get_string('startdate','local_bbb_meeting_overview').'</th>
                <th>'.get_string('enddate','local_bbb_meeting_overview').'</th>
                <th>'.get_string('state','local_bbb_meeting_overview').'</th>
        </tr>';
foreach ($meetings as $key => $value) {
        $test_meeting = new Meeting($value);
       echo $test_meeting->renderRow();

}
echo '</table>';
/* $test_meeting = new Meeting($meetings[69]);
$test_meeting->getInfo();
$test_meeting->isOnline();
//print_r($test_meeting);
$api = new API(); */
//echo $api->getMeetingAPIinfo($test_meeting);
 
 
//your code
 
 
echo $OUTPUT->footer();
