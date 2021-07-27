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




$plugin->version = 20210607; //this is just for me
 
$plugin->requires = 2018120300; 
 //requred moodle version (this is for 3.6) more here: https://docs.moodle.org/dev/Releases
 
$plugin->component = 'local_eso_custom_tasks'; 
 
$plugin->cron = 60; //enables cron 
 
$plugin->release = '1.0'; 
 
$plugin->maturity = MATURITY_STABLE; 
 
