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

namespace local_vs_manager\VS; 


require_once($CFG->dirroot.'/user/profile/lib.php');
defined ( 'MOODLE_INTERNAL' ) || die(); 

 
class VS {
        public $id; 
        public $username;
        public $courseid;
        public $vcode;

        function __construct($id,$username = null,$courseid = null,$vcode = null)
        {
                $this->id = $id;
                $this->username = $username;
                $this->courseid = $courseid;
                $this->vcode = $vcode;
        }

        private function isValidUser()
        {       global $DB;
                $sql = 'SELECT id,firstname,lastname,email FROM {user} WHERE username = ?';
                $record = $DB->get_record_sql($sql,array($this->username));
                if($record){
                        $record->profile_field_titul_pred = $DB->get_field_sql('SELECT data FROM {user_info_data} WHERE fieldid = 8 AND userid = ?',array($record->id));
                        profile_load_data($record);
                        return $record;
                }else{
                        $record = new \stdClass();
                        $record->profile_field_titul_pred = get_string('not_exists','local_vs_manager') ;
                        $record->firstname = get_string('not_exists','local_vs_manager') ;
                        $record->lastname = get_string('not_exists','local_vs_manager') ;
                        $record->email = get_string('not_exists','local_vs_manager') ;
                        return $record;
                }
        }

        private function isValidCourse()
        {
                global $DB;
                $sql = 'SELECT * FROM {course} WHERE id = ?';
                $record = $DB->get_record_sql($sql,array($this->courseid));
                if($record){           
                        return $record;
                }else{  
                        $record = new \stdClass();
                        $record->fullname = get_string('not_exists','local_vs_manager') ;
                        $record->shortname = get_string('not_exists','local_vs_manager') ;
                        return $record;
                }
        }

        public function deleteRecord()
        {       global $DB;
                $sql = 'DELETE from {user_vcode} WHERE id = ?';
                $DB->execute($sql,array($this->id));
                //should be good to log this shit
        }

        public function updateRecord($new_vs)
        {       global $DB;
                $sql = 'UPDATE {user_vcode} SET vcode = ? WHERE id = ?';
                $DB->execute($sql,array($new_vs,$this->id));
        }

        public function renderRow()
        {       global $DB;
                $user = $this->isValidUser();
                $course = $this->isValidCourse();
                
                $output = 
                        '<tr  id="row-'.$this->id.'" class="clickable">
                                <td>
                                <input  type="checkbox" onclick="classCheckboxChange('.$this->id.')"  class="check-check" id = "check-'.$this->id.'" name = "'.$this->id.'">
                                </td>

                                <td class="coursename" id="coursename-'.$this->id.'" onclick="check('.$this->id.')" >
                                '.$course->fullname.'
                                </td>

                                <td onclick="check('.$this->id.')" >
                                '.$course->shortname.'
                                </td>

                                <td class="vcode" id="vcode-'.$this->id.'">
                                '.$this->vcode.'
                                </td>

                                <td onclick="check('.$this->id.')" >
                                '.$user->firstname.'
                                </td>

                                <td class="lastname" onclick="check('.$this->id.')" >
                                '.$user->lastname.'
                                </td>

                                <td class="email" id="email-'.$this->id.'" onclick="check('.$this->id.')" >
                                '.$user->email.'
                                </td>

                                <td onclick="check('.$this->id.')" >
                                '.$user->profile_field_nazev_uradu.'
                                </td>

                                <td>
                                <button onClick="editRecord('.$this->id.')" class="update-record">'.get_string('update','local_vs_manager').'</button>
                                </td>

                                <td>
                                <button onClick="deleteRecord('.$this->id.')" class="delete-record">'.get_string('delete','local_vs_manager').'</button>
                                </td>
                        </tr>';

                return $output;
        }
 
 
 } 