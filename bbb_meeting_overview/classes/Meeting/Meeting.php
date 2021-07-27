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

namespace local_bbb_meeting_overview\Meeting; 
use local_bbb_meeting_overview\API\API;



defined ( 'MOODLE_INTERNAL' ) || die(); 
 
class Meeting { 

        public $id;
        public $courseid;
        public $name;

        public static function getMeetings()
        {       global $DB;
                $sql = 'SELECT {bigbluebuttonbn}.id,{bigbluebuttonbn}.course,{bigbluebuttonbn}.name,{bigbluebuttonbn}.wait,{bigbluebuttonbn}.userlimit,{bigbluebuttonbn}.meetingid,
                        {course}.id as courseid,{course}.category,{course}.fullname,{course}.shortname,{course}.startdate,{course}.enddate,{course}.visible,{course}.featured
                        FROM {bigbluebuttonbn}
                        INNER JOIN {course} ON {course}.id = {bigbluebuttonbn}.course
                        ORDER BY {course}.startdate DESC';
                $meetings = $DB->get_records_sql($sql);
                return $meetings;
        }

        public function __construct($object)
        {
                $this->id = $object->id;
                $this->courseid = $object->course;
                $this->name = $object->name;
                $this->userlimit = $object->userlimit;
                $this->meetingid = $object->meetingid;
                $this->startdate = $object->startdate;
                $this->enddate = $object->enddate;
                $this->fullname = $object->fullname;
                $this->wait = $object->wait;
        }
/* 
        public function getInfo()
        {       global $DB;
                $this->course = $DB->get_record_sql('SELECT {course}.id,{course}.category,{course}.fullname,{course}.shortname,{course}.startdate,{course}.enddate,{course}.visible,{course}.featured FROM {course} WHERE id = ?',array($this->courseid));
        } */

        public function renderRow()
        {       global $DB;

                $class_done = '';
                if(time() > $this->enddate){
                        $class_done = 'class="in-past"';   
                }
                $output = '<tr '.$class_done.'>
                                <td class="course-url">'.$this->courseUrl().'</td>
                                <td class="settings-url">'.$this->meetingSettingsUrl().'</td>
                                <td>'.$this->getParticipantsCount().'/'.$this->userlimit.'</td>
                                <td>'.date('d.m.Y H:i',$this->startdate).'</td>
                                <td>'.date('d.m.Y H:i',$this->enddate).'</td>
                                <td>'.$this->isOnline().'</td>
                        </tr>';

                return $output;
        }

        private function courseUrl()
        {       global $CFG;
                $url = new \moodle_url("/course/view.php",array('id' => $this->courseid));
                return '<a href="'.$url.'">'.$this->fullname.'</a>';
        }

        private function meetingSettingsUrl()
        {       global $CFG,$DB;
                $module = $DB->get_field_sql('SELECT id FROM {modules} WHERE name = "bigbluebuttonbn"');
                $module_id = $DB->get_field_sql('SELECT id FROM {course_modules} WHERE course = ? AND module = ?',array($this->courseid,$module));
                $url = new \moodle_url("/course/modedit.php",array('update' => $module_id));
                return '<a href="'.$url.'">'.get_string('settings','local_bbb_meeting_overview').'</a>';
        }

        private function getParticipantsCount()
        {       global $CFG,$DB;
                $enrols = $DB->get_fieldset_sql('SELECT id FROM {enrol} WHERE courseid = ?',array($this->courseid));
                $enrols_string = '('.\implode(',',$enrols).')';
                $enrol_count = $DB->get_field_sql('SELECT count(*) FROM {user_enrolments} WHERE enrolid IN '.$enrols_string.' ');
                return $enrol_count;
        }

        private function isOnline()
        {
                $api = new API();
               $result = $api->getMeetingAPIinfo($this);
               $result->returncode = str_replace(' ','',$result->returncode);
                if($result->returncode != 'FAILED'){
                        return '<span class="is-online">' . $result->participantCount . '/'. $result->maxUsers."</span>"; 
                }else{
                        return '<span class="is-offline"><i class="fa fa-power-off"></i></span>';
                }
        
        }
 
 
 
 } 