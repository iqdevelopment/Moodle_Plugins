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




$ADMIN->add('courses',new admin_externalpage('local_eso_custom_tasks', get_string('plugin_advanced', 'local_eso_custom_tasks'),new moodle_url('/local/eso_custom_tasks/index.php')));$settings = new admin_settingpage('local_eso_custom_tasks_settings',get_string('modulename','local_eso_custom_tasks'));$ADMIN->add('localplugins',$settings );
 
//example checkbox 
//$settings->add(new admin_setting_configcheckbox('local_eso_custom_tasks/type_1', get_string('one_day', 'local_eso_custom_tasks'),'', 0));
 
//example multiselect (multiselect of all visible categories) 

/*             $catlist = $DB->get_records_sql("SELECT * FROM {course_categories} ");
            $categories = array();
            foreach ($catlist as $category) {
                $categories[$category->id] = $category->name;
            }

            $settings->add(
                new admin_setting_configmultiselect(
                    'local_eso_custom_tasks/categories',
                    get_string('categories_enabled', 'local_eso_custom_tasks'),
                    '',
                    1,
                    $categories
                )
            );
 

            $settings->add(new admin_setting_configtextarea(
            'local_eso_custom_tasks/default_text_periodical',
            get_string('default_notification', 'local_eso_custom_tasks'),
            get_string('tags', 'local_eso_custom_tasks'),
            get_string('default_notification_text', 'local_eso_custom_tasks')));
             */
 
