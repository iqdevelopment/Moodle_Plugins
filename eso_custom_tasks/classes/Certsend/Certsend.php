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
 * @copyright     IQdevelopment
 * @copyright     2021

*/

namespace local_eso_custom_tasks\Certsend;
use local_eso_custom_tasks\Simplecertificate\Simplecertificate;
//use \mod_simplecertificate\locallib\simplecertificate; 

global $CFG;

//require($CFG->dirroot.'/mod/simplecertificate/locallib.php');

defined ( 'MOODLE_INTERNAL' ) || die(); 
 
class Certsend { 
        

    static function sendCertificates()
    {   global $DB,$CFG,$USER;
        $url = $CFG->wwwroot.'/mod/simplecertificate/send_certificates.php/';
       

        //Initialize cURL.
        $ch = curl_init();

        //Set the URL that you want to GET by using the CURLOPT_URL option.
        curl_setopt($ch, CURLOPT_URL, $url);

        //Set CURLOPT_RETURNTRANSFER so that the content is returned as a variable.
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        //Set CURLOPT_FOLLOWLOCATION to true to follow redirects.
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

        //Execute the request.
        $data = curl_exec($ch);

        //Close the cURL handle.
        curl_close($ch);

        //Print the data out onto the page.
        echo $data;

        }
  


 
 
 } 