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
 * @package     local_vs_manager 
 * @copyright     Simon Zajicek
 * @copyright     IQdevelopment
 * @copyright     2021

*/

namespace local_vs_manager; 
use local_vs_manager\AJAX\AJAX;
use local_vs_manager\VS\VS;
use local_vs_manager\Processor\Processor;


require_once '../../config.php';
require_login();
global $USER,$DB,$CFG;
 
$PAGE->set_url('/local/vs_manager/index.php');
$PAGE->set_title(get_string('modulename','local_vs_manager'));
$PAGE->navbar->add($PAGE->title, $PAGE->url);
$PAGE->requires->css('/local/vs_manager/assets/css/css.css');
echo $OUTPUT->header();
$PAGE->requires->js('/local/vs_manager/assets/js/js.js');

 

//$meeting = new VS();
 
$obj = new VS(1,'zajicek.simon@seznam.cz',685,26598);

echo '<span><input type="search" id="site-search"  name="q" aria-label="Search through site content" 
         placeholder="'.get_string('search_for','local_vs_manager').'" size="80%"><button onClick="search()">'.get_string('search_button','local_vs_manager').'</button></span><br><br><br>';
echo 
        ' <button onClick="search(1)">'.
        get_string('show_all','local_vs_manager').'</button>'.
        get_string('from','local_vs_manager').
        $DB->get_field_sql('SELECT count(*) FROM {user_vcode}').
        get_string('number_of_records','local_vs_manager').
        '<br>';



        $sql = 'SELECT count(*) FROM {user_vcode} WHERE username NOT IN (SELECT username FROM {user}) OR courseid NOT IN (SELECT id FROM {course})';
        $records_to_delete = $DB->get_field_sql($sql);
echo '<form method="POST" action="'.$CFG->wwwroot.'/local/vs_manager/AJAX/AJAX.php">
        <input type ="submit" value="'.get_string('delete_all_empty','local_vs_manager').'"></input>'.
        get_string('from','local_vs_manager').
        $records_to_delete.
        get_string('number_of_empty_records','local_vs_manager').
        
        '<input type="hidden" name="deleteEmpty" value="1">
        </form>';    
        echo '<button onClick="deleteMulti()">'.get_string('delete_marked','local_vs_manager').'</button>';      
echo '<div id="results" class="results"></div>';

/*$log->createLog($obj);
$log->trigger();
print_r(log);
echo 'hovno'; */
 
//date_interval_create_from_date_string

//$event = \local_vs_manager\event
 
echo $OUTPUT->footer();
