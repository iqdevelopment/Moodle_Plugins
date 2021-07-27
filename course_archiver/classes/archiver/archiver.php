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
 * @package     local_course_archiver 
 * @copyright     Simon Zajicek
 * @copyright     IQdevelopment.cz
 * @copyright     2021

*/

namespace local_course_archiver\archiver; 



defined ( 'MOODLE_INTERNAL' ) || die(); 
 
class archiver { 
 
        static function task()
        {       global $DB;
                $enabled = $DB->get_field_sql('SELECT value FROM {config_plugins} WHERE plugin = ? AND name = ?',array('local_course_archiver','enable'));

                if(!$enabled){
                        echo 'not enabled'.PHP_EOL;
                }else{  
                        echo 'enabled'.PHP_EOL;
                        $where_to_backup = $DB->get_field_sql('SELECT value FROM {config_plugins} WHERE plugin = ? AND name = ?',array('local_course_archiver','category_to_backup_to'));  
                        $what_backup = $DB->get_field_sql('SELECT value FROM {config_plugins} WHERE plugin = ? AND name = ?',array('local_course_archiver','categories_to_backup'));
                        $what_backup_array = \explode(',',$what_backup);
                        $days = $DB->get_field_sql('SELECT value FROM {config_plugins} WHERE plugin = ? AND name = ?',array('local_course_archiver','days_after_end'));
                        $days_converted = intval($days)*60*60*24;
                        $date_to_convert = time() + $days_converted;


                        foreach ($what_backup_array as $category) {
                                echo 'doing category: '.$category.PHP_EOL.'<br>';
                               $courses = $DB->get_records_sql('SELECT id,category,startdate,enddate FROM {course} WHERE category = ?',array($category));
                               foreach ($courses as $course) {
                                       if($course->enddate > $date_to_convert){
                                               $sql = 'UPDATE {course} SET category = ? WHERE id = ?';
                                               echo 'course: '.$course->id . 'is beeing archived<br>'.PHP_EOL;
                                               $DB->execute($sql,array($where_to_backup,$course->id));
                                       }
                               }
                               echo PHP_EOL.PHP_EOL.'<br><br>';
                        }

                }
                # code...
        }
 
 } 