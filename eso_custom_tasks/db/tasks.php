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




defined ( 'MOODLE_INTERNAL' ) || die(); 
 
$tasks = array( 

                
                        array(
                            'classname' => 'local_eso_custom_tasks\task\course_completion_email',
                            'blocking' => 0,
                            'minute' => '*',
                            'hour' => '*',
                            'day' => '*',
                            'dayofweek' => '*',
                            'month' => '*'
                        ), 
 

                
                        array(
                            'classname' => 'local_eso_custom_tasks\task\course_order',
                            'blocking' => 0,
                            'minute' => '*',
                            'hour' => '*',
                            'day' => '*',
                            'dayofweek' => '*',
                            'month' => '*'
                        ), 
 

                
                        array(
                            'classname' => 'local_eso_custom_tasks\task\fill_notification',
                            'blocking' => 0,
                            'minute' => '*',
                            'hour' => '*',
                            'day' => '*',
                            'dayofweek' => '*',
                            'month' => '*'
                        ), 
 

                
                        array(
                            'classname' => 'local_eso_custom_tasks\task\number_of_parcipitants',
                            'blocking' => 0,
                            'minute' => '*',
                            'hour' => '*',
                            'day' => '*',
                            'dayofweek' => '*',
                            'month' => '*'
                        ), 
 

                
                        array(
                            'classname' => 'local_eso_custom_tasks\task\password_for_course',
                            'blocking' => 0,
                            'minute' => '*',
                            'hour' => '*',
                            'day' => '*',
                            'dayofweek' => '*',
                            'month' => '*'
                        ), 
 

                
                        array(
                            'classname' => 'local_eso_custom_tasks\task\simplecertificate_auto_send',
                            'blocking' => 0,
                            'minute' => '*',
                            'hour' => '*',
                            'day' => '*',
                            'dayofweek' => '*',
                            'month' => '*'
                        ), 
 
); 
