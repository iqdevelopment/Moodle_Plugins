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
 * @package    local_my_organization
 * @copyright  2021 Simon Zajicek
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;
global $DB,$PAGE,$CFG;
$url = new moodle_url('/local/my_organization/assets/js/validateSettings.js');;
$PAGE->requires->js($url);

if ($hassiteconfig) {

	//$ADMIN->add('courses',new admin_externalpage('local_my_organization', get_string('plugin_advanced', 'local_my_organization'),new moodle_url('/local/my_organization/index.php')));
	
	$settings = new admin_settingpage('local_my_organization_settings',get_string('modulename','local_my_organization'));
	$ADMIN->add('localplugins',$settings );
    
	/*
    $options = array(1=>get_string('jumpto_coursepage', 'local_my_organization'),2=>get_string('jumpto_coursesettingspage', 'local_my_organization'));
	$settings->add(new admin_setting_configselect('local_my_organization/jump_to', get_string('jumpto', 'local_my_organization'),'', 1, $options));*/
	
	/***
	 * 
	 * přidá záznam do {config_plugins}
	 * 
	 */
	$catlist = $DB->get_records_sql("SELECT * FROM {course_categories} WHERE visible = 1");
    $categories = array();
    $categories[-1] = get_string('none', 'local_my_organization');
    foreach ($catlist as $category) {
        $categories[$category->id] = $category->name;
    }

    $settings->add(
        new admin_setting_configselect(
            'local_my_organization/default_category',
            get_string('default_category', 'local_my_organization'),
            '',
            1,
            $categories
        )
    );


    $role_list = $DB->get_records_sql("SELECT * FROM {role} WHERE id IN (SELECT roleid FROM {role_context_levels} WHERE contextlevel = 40)");
    $roles = array();
    $roles[-1] = get_string('none', 'local_my_organization');
    foreach ($role_list as $role) {
        $roles[$role->id] = $role->name;
    }

    $settings->add(
        new admin_setting_configselect(
            'local_my_organization/default_role',
            get_string('default_role', 'local_my_organization'),
            '',
            1,
            $roles
        )
    );
 

   /*  $settings->add(new admin_setting_configcheckbox('local_my_organization/type_1', get_string('one_day', 'local_my_organization'),'', 0));
    $settings->add(new admin_setting_configcheckbox('local_my_organization/type_3', get_string('three_day', 'local_my_organization'),'', 0));
    $settings->add(new admin_setting_configcheckbox('local_my_organization/type_7', get_string('seven_day', 'local_my_organization'),'', 0));
    $settings->add(new admin_setting_configcheckbox('local_my_organization/type_0', get_string('one_time', 'local_my_organization'),'', 0));
    $settings->add(new admin_setting_configtext('local_my_organization/default_subject', get_string('regular_subject', 'local_my_organization'),'', get_string('default_regular_subject', 'local_my_organization')));

	$settings->add(new admin_setting_configtextarea(
            'local_my_organization/default_text_periodical',
             get_string('default_text_periodical_title', 'local_my_organization'),
             '',
             get_string('default_text_periodical', 'local_my_organization')));

    $settings->add(new admin_setting_configtextarea(
            'local_my_organization/default_text_one_time',
                get_string('default_text_one_time_title', 'local_my_organization'),
                '',
                get_string('default_text_one_time', 'local_my_organization'))); */

	
	}


	
