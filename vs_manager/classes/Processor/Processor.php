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

namespace local_vs_manager\Processor; 



defined ( 'MOODLE_INTERNAL' ) || die(); 
 
class Processor { 
 
        public function searchText($text)
        {       global $DB;
                $sql = 'SELECT {user_vcode}.id,{user_vcode}.username,{user_vcode}.courseid,{user_vcode}.vcode FROM {user_vcode}
                INNER JOIN {course} ON {course}.id = {user_vcode}.courseid
                INNER JOIN {user} ON {user}.username = {user_vcode}.username
                INNER JOIN {user_info_data} ON {user_info_data}.userid = {user}.id
                WHERE {user_info_data}.fieldid = 8
                AND (
                        {user}.email like "%'.$text.'%"	OR
                    {user}.firstname like "%'.$text.'%" OR
                    {user}.lastname like "%'.$text.'%" OR
                    {user_info_data}.data like "%'.$text.'%" OR
                    {course}.fullname like "%'.$text.'%" OR
                    {course}.shortname like "%'.$text.'%" OR
                    {user_vcode}.vcode like "%'.$text.'%"
                        )
                                
                ';
                if($text == 1){
                        $sql = 'SELECT {user_vcode}.id,{user_vcode}.username,{user_vcode}.courseid,{user_vcode}.vcode FROM {user_vcode}';       
                }
                $results = $DB->get_records_sql($sql);
                return $results;
        }
 
 } 