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
 * @package     local_custom_mail_log 
 * @copyright     Simon Zajicek
 * @copyright     2021
 * @copyright     IQdevelopment.cz

*/

namespace local_custom_mail_log; 
use local_custom_mail_log\Mailloger\Mailloger;
use local_custom_mail_log\Processor\Processor;



require_once '../../config.php';
require_login();
global $USER,$DB,$CFG;
 
$PAGE->set_url('/local/custom_mail_log/index.php');
$PAGE->set_title(get_string('modulename','local_custom_mail_log'));
$PAGE->navbar->add($PAGE->title, $PAGE->url);
if(is_siteadmin()){
  $PAGE->requires->css('/local/custom_mail_log/dist/main.css');

  $PAGE->requires->js('/local/custom_mail_log/dist/main.js');
}

//end of production

//dev
//echo '<script type="module" src="'.$CFG->wwwroot.'/local/custom_mail_log/dist/main.js"></script>';

//end of dev


echo $OUTPUT->header();
if(is_siteadmin()){
  echo '<div id="head-utils">
  '.get_string('wait_minutes','local_custom_mail_log').'<br>
 <input id="search-field" type="search" size="70" placeholder="'.get_string('placeholder','local_custom_mail_log').'"</input>
 <button id="search-button">'.get_string('search','local_custom_mail_log').'</button>';
 echo '<div id="results"></div>';
}else{
  echo get_string('no_permision','local_custom_mail_log');
}

/*  echo '<div id="head-utils">
 <input id="search-field" type="search" size="70" placeholder="'.get_string('placeholder','local_custom_mail_log').'"</input>
 <button id="search-button">'.get_string('search','local_custom_mail_log').'</button>';
 echo '<div id="results"></div>'; */
/* $test = new Processor();
$test2 = new Mailloger();
$test2->fillDatabase();
  */
 
//your code
 
 
echo $OUTPUT->footer();
