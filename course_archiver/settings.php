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




$ADMIN->add('courses',new admin_externalpage('local_course_archiver', get_string('plugin_advanced', 'local_course_archiver'),new moodle_url('/local/course_archiver/index.php')));$settings = new admin_settingpage('local_course_archiver_settings',get_string('modulename','local_course_archiver'));$ADMIN->add('localplugins',$settings );
 
//example checkbox 
//$settings->add(new admin_setting_configcheckbox('local_course_archiver/type_1', get_string('one_day', 'local_course_archiver'),'', 0));
 
//example multiselect (multiselect of all visible categories) 

    $settings->add(new admin_setting_configcheckbox('local_course_archiver/enable',
            get_string('enable_desc', 'local_course_archiver'),
            '',
            0));

            $settings->add(new admin_setting_configtext(
                'local_course_archiver/days_after_end',
                get_string('days_after_end', 'local_course_archiver'),
                '',
                30));


            $catlist = $DB->get_records_sql("SELECT * FROM {course_categories}");
            $categories = array();
            foreach ($catlist as $category) {
                $categories[$category->id] = $category->name;
            }

            $settings->add(
                new admin_setting_configselect(
                    'local_course_archiver/category_to_backup_to',
                    get_string('category_to_backup_to', 'local_course_archiver'),
                    '',
                    1,
                    $categories
                )
            );

            $selected = $DB->get_field_sql('SELECT value FROM {config_plugins} WHERE plugin = ? AND name = ?',array('local_course_archiver','category_to_backup_to'));
            if(!$selected){$selected = 0;}
            $catlist = $DB->get_records_sql("SELECT * FROM {course_categories} WHERE id != ?",array($selected));
            $categories = array();
            foreach ($catlist as $category) {
                $categories[$category->id] = $category->name;
            }

            $settings->add(
                new admin_setting_configmultiselect(
                    'local_course_archiver/categories_to_backup',
                    get_string('categories_to_backup', 'local_course_archiver'),
                    '',
                    1,
                    $categories
                )
            );
 

           /*
            example textarea
            $settings->add(new admin_setting_configtextarea(
            'local_course_archiver/default_text_periodical',
            get_string('default_text_periodical_title', 'local_course_archiver'),
            '',
            get_string('default_text_periodical', 'local_course_archiver')));
            */
 
