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




$ADMIN->add('courses',new admin_externalpage('local_bbb_meeting_overview', get_string('plugin_advanced', 'local_bbb_meeting_overview'),new moodle_url('/local/bbb_meeting_overview/index.php')));$settings = new admin_settingpage('local_bbb_meeting_overview_settings',get_string('modulename','local_bbb_meeting_overview'));$ADMIN->add('localplugins',$settings );
 
//example checkbox 
//$settings->add(new admin_setting_configcheckbox('local_bbb_meeting_overview/type_1', get_string('one_day', 'local_bbb_meeting_overview'),'', 0));
 
//example multiselect (multiselect of all visible categories) 

            /*$catlist = $DB->get_records_sql("SELECT * FROM {course_categories} WHERE visible = 1");
            $categories = array();
            foreach ($catlist as $category) {
                $categories[$category->id] = $category->name;
            }

            $settings->add(
                new admin_setting_configmultiselect(
                    'local_bbb_meeting_overview/categories',
                    get_string('categories_enabled', 'local_bbb_meeting_overview'),
                    '',
                    1,
                    $categories
                )
            );*/
 

           /*
            example textarea
            $settings->add(new admin_setting_configtextarea(
            'local_bbb_meeting_overview/default_text_periodical',
            get_string('default_text_periodical_title', 'local_bbb_meeting_overview'),
            '',
            get_string('default_text_periodical', 'local_bbb_meeting_overview')));
            */
 
