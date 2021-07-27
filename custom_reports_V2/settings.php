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
 * @package    local_custom_reports
 * @copyright  2021 Simon Zajicek
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

global $DB;
if ($hassiteconfig) {

//	$ADMIN->add('courses',new admin_externalpage('local_custom_reports', get_string('plugin_advanced', 'local_custom_reports'),new moodle_url('/local/custom_reports/index.php')));
	
	$settings = new admin_settingpage('local_custom_reports_settings',get_string('modulename','local_custom_reports'));
	$ADMIN->add('localplugins',$settings );
    
    $settings->add(new admin_setting_configtext('local_custom_reports/recipient_1', get_string('recipient_1', 'local_custom_reports'),'', ''));
    $settings->add(new admin_setting_configtext('local_custom_reports/recipient_2', get_string('recipient_2', 'local_custom_reports'),'', ''));
    $settings->add(new admin_setting_configtext('local_custom_reports/recipient_3', get_string('recipient_3', 'local_custom_reports'),'', ''));



	
	}


	
