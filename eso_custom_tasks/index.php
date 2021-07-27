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
 * @package     local_eso_custom_tasks 
 * @copyright     Simon Zajicek
 * @copyright     2021
 * @copyright     IQdevelopment.cz

*/

namespace local_eso_custom_tasks; 
use local_eso_custom_tasks\General\General; 
use local_eso_custom_tasks\Participants\Participants;



require_once '../../config.php';
require_login();
global $USER,$DB,$CFG;
 
$PAGE->set_url('/local/eso_custom_tasks/index.php');
$PAGE->set_title(get_string('modulename','local_eso_custom_tasks'));
$PAGE->navbar->add($PAGE->title, $PAGE->url);
echo $OUTPUT->header();
 //if needed JS $PAGE->requires->js('/local/eso_custom_tasks/assets/js/custom.js');
 //if needed JS $PAGE->requires->css('/local/eso_custom_tasks/assets/css/custom.css'); 



 Participants::getDifferencies();
 //Participants::doshit();
 //General::multidateEnrollments();

 
//your code
 
 
echo $OUTPUT->footer();
