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
require_once '../../../config.php';
use local_vs_manager\Processor\Processor;
use local_vs_manager\VS\VS;
require_login();
/* $proces = new Processor();
    $return = $proces->searchText('Zajicek');
    echo \json_encode($return); */

if (isset($_POST['search'])) {
    $proces = new Processor();
    $return = $proces->searchText($_POST['search']);
    if($return){
        $ajax_return = '<table class="search-table">';
        $ajax_return .= '<tr>
                        <th><input type="checkbox" id="maincheck" onchange="checkAll()"></input></th>
                        <th>'.get_string('course_fullname','local_vs_manager').'</th>
                        <th>'.get_string('course_shortname','local_vs_manager').'</th>
                        <th>'.get_string('vs','local_vs_manager').'</th>
                        <th>'.get_string('firstname','local_vs_manager').'</th>
                        <th>'.get_string('lastname','local_vs_manager').'</th>
                        <th>'.get_string('email','local_vs_manager').'</th>
                        <th>'.get_string('office','local_vs_manager').'</th>
                        <th colspan="2">'.get_string('actions','local_vs_manager').'</th>
                        </tr>';
        foreach ($return as $key => $value) {
            $object = new VS($value->id,$value->username,$value->courseid,$value->vcode);
            $ajax_return .= $object->renderRow();
        }
        $ajax_return .= '</table>';
        echo json_encode($ajax_return);
    }else{
        echo json_encode(get_string('no_results','local_vs_manager'));
    }

}elseif (isset($_POST['remove'])) {
    $object = new VS($_POST['remove']);
    $object->deleteRecord();

}elseif (isset($_POST['deleteEmpty']) and $_POST['deleteEmpty']==1) {
    echo "yup i will be deleting";
    $sql = 'SELECT * FROM {user_vcode} WHERE username NOT IN (SELECT username FROM {user}) OR courseid NOT IN (SELECT id FROM {course})';
    $records_to_delete = $DB->get_records_sql($sql);
    echo sizeof($records_to_delete);
    foreach ($records_to_delete as $value) {
        $object = new VS($value->id);
        $object->deleteRecord(); 
    }
    $url = $CFG->wwwroot.'/local/vs_manager'; 
    redirect($url, get_string('was_deleted','local_vs_manager').sizeof($records_to_delete), 0);

}elseif (isset($_POST['update'])) {
    $value_array = \explode('-',$_POST['update']);
    $object = new VS($value_array[0]);
    $object->updateRecord($value_array[1]);

}