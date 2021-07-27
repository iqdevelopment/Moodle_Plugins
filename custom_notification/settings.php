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
 * @package    local_custom_notification
 * @copyright  2021 Simon Zajicek
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

global $DB;
if ($hassiteconfig) {

	$ADMIN->add('courses',new admin_externalpage('local_custom_notification', get_string('plugin_advanced', 'local_custom_notification'),new moodle_url('/local/custom_notification/index.php')));
	
	$settings = new admin_settingpage('local_custom_notification_settings',get_string('modulename','local_custom_notification'));
	$ADMIN->add('localplugins',$settings );
    
	/*
    $options = array(1=>get_string('jumpto_coursepage', 'local_custom_notification'),2=>get_string('jumpto_coursesettingspage', 'local_custom_notification'));
	$settings->add(new admin_setting_configselect('local_custom_notification/jump_to', get_string('jumpto', 'local_custom_notification'),'', 1, $options));*/
	
	/***
	 * 
	 * přidá záznam do {config_plugins}
	 * 
	 */
	$catlist = $DB->get_records_sql("SELECT * FROM {course_categories} WHERE visible = 1");
    $categories = array();
    foreach ($catlist as $category) {
        $categories[$category->id] = $category->name;
    }
    $settings->add(new admin_setting_heading('local_custom_notification/aa', get_string('periodical_setting', 'local_custom_notification'), ''));
    $settings->add(
        new admin_setting_configmultiselect(
            'local_custom_notification/categories',
            get_string('categories_enabled', 'local_custom_notification'),
            '',
            1,
            $categories
        )
    );

    $settings->add(new admin_setting_configcheckbox('local_custom_notification/type_1', get_string('one_day', 'local_custom_notification'),'', 0));
    $settings->add(new admin_setting_configcheckbox('local_custom_notification/type_3', get_string('three_day', 'local_custom_notification'),'', 0));
    $settings->add(new admin_setting_configcheckbox('local_custom_notification/type_7', get_string('seven_day', 'local_custom_notification'),'', 0));
    $settings->add(new admin_setting_configcheckbox('local_custom_notification/type_0', get_string('one_time', 'local_custom_notification'),'', 0));
    $settings->add(new admin_setting_configtext('local_custom_notification/default_subject', get_string('regular_subject', 'local_custom_notification'),'', get_string('default_regular_subject', 'local_custom_notification')));

	$settings->add(new admin_setting_configtextarea(
            'local_custom_notification/default_text_periodical',
             get_string('default_text_periodical_title', 'local_custom_notification'),
             '',
             get_string('default_text_periodical', 'local_custom_notification')));

    $settings->add(new admin_setting_configtextarea(
            'local_custom_notification/default_text_one_time',
                get_string('default_text_one_time_title', 'local_custom_notification'),
                '',
                get_string('default_text_one_time', 'local_custom_notification')));


/**
 * 
 * enrols setting
 */


//special

    $settings->add(new admin_setting_heading('local_custom_notification/bb', get_string('enrol_setting_special', 'local_custom_notification'), ''));

    $catlist = $DB->get_records_sql("SELECT * FROM {course_categories} ORDER BY name"); 
        foreach ($catlist as $category) {
            $categories[$category->id] = $category->name;
        }
    
    $settings->add(
            new admin_setting_configmultiselect(
                'local_custom_notification/special_enrol_notification_category',
                get_string('special_enrol_notification_category', 'local_custom_notification'),
                '',
                1,
                $categories
            )
        );
    


        //self enrol
    $settings->add(new admin_setting_configtext('local_custom_notification/enrol_self_special_subject',
                     get_string('enrol_self_subject', 'local_custom_notification'),
                     '', 
                     get_string('enrol_self_subject_text', 'local_custom_notification')));

    $settings->add(new admin_setting_configtextarea(
            'local_custom_notification/enrol_self_special_text',
                get_string('enrol_self_text_title', 'local_custom_notification'),
                '',
                get_string('tags', 'local_custom_notification')));

        //self unenroll

    $settings->add(new admin_setting_configtext('local_custom_notification/unenrol_self_special_subject',
        get_string('unenrol_self_subject', 'local_custom_notification'),
        '', 
        get_string('unenrol_self_subject_text', 'local_custom_notification')));

    $settings->add(new admin_setting_configtextarea(
            'local_custom_notification/unenrol_self_special_text',
            get_string('unenrol_self_text_title', 'local_custom_notification'),
            '',
            get_string('tags', 'local_custom_notification')));
 
            
    //manual special
        //manual enrol
    $settings->add(new admin_setting_configtext('local_custom_notification/enrol_manual_special_subject',
            get_string('enrol_manual_subject', 'local_custom_notification'),
            '', 
            get_string('enrol_manual_subject_text', 'local_custom_notification')));
    
        $settings->add(new admin_setting_configtextarea(
            'local_custom_notification/enrol_manual_special_text',
            get_string('enrol_manual_text_title', 'local_custom_notification'),
            '',
            get_string('tags', 'local_custom_notification')));
    
    
            //manual unenrol
    $settings->add(new admin_setting_configtext('local_custom_notification/unenrol_manual_special_subject',
            get_string('unenrol_manual_subject', 'local_custom_notification'),
            '', 
            get_string('enrol_manual_subject_text', 'local_custom_notification')));
    
    $settings->add(new admin_setting_configtextarea(
            'local_custom_notification/unenrol_manual_special_text',
            get_string('unenrol_manual_text_title', 'local_custom_notification'),
            '',
            get_string('tags', 'local_custom_notification')));



            //default


    $settings->add(new admin_setting_heading('local_custom_notification/cc', get_string('enrol_setting_default', 'local_custom_notification'), ''));

            //default
                  //self enrol
    $settings->add(new admin_setting_configtext('local_custom_notification/enrol_self_subject',
        get_string('enrol_self_subject', 'local_custom_notification'),
        '', 
        get_string('enrol_self_subject_text', 'local_custom_notification')));

    $settings->add(new admin_setting_configtextarea(
        'local_custom_notification/enrol_self_text',
        get_string('enrol_self_text_title', 'local_custom_notification'),
        '',
        get_string('tags', 'local_custom_notification')));

    //self unenroll

    $settings->add(new admin_setting_configtext('local_custom_notification/unenrol_self_subject',
        get_string('unenrol_self_subject', 'local_custom_notification'),
        '', 
        get_string('enrol_self_subject_text', 'local_custom_notification')));

    $settings->add(new admin_setting_configtextarea(
        'local_custom_notification/unenrol_self_text',
        get_string('unenrol_self_text_title', 'local_custom_notification'),
        '',
        get_string('tags', 'local_custom_notification')));



    $settings->add(new admin_setting_configtext('local_custom_notification/enrol_manual_subject',
        get_string('enrol_manual_subject', 'local_custom_notification'),
        '', 
        get_string('enrol_manual_subject_text', 'local_custom_notification')));

    $settings->add(new admin_setting_configtextarea(
        'local_custom_notification/enrol_manual_text',
        get_string('enrol_manual_text_title', 'local_custom_notification'),
        '',
        get_string('tags', 'local_custom_notification')));



    $settings->add(new admin_setting_configtext('local_custom_notification/unenrol_manual_subject',
        get_string('unenrol_manual_subject', 'local_custom_notification'),
        '', 
        get_string('enrol_manual_subject_text', 'local_custom_notification')));

    $settings->add(new admin_setting_configtextarea(
        'local_custom_notification/unenrol_manual_text',
        get_string('unenrol_manual_text_title', 'local_custom_notification'),
        '',
        get_string('tags', 'local_custom_notification')));



     

	
	}


	
