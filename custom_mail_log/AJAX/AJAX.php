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

namespace local_custom_mail_log\AJAX; 
use local_custom_mail_log\Mailloger\Mailloger;
use local_custom_mail_log\Processor\Processor;



require_once '../../../config.php';
require_login();
if(is_siteadmin()){
        if (isset($_POST) AND isset($_POST['getlist'])) {
        
        echo json_encode(Processor::getlist($_POST['getlist']));
        
        //ELSE
        }elseif(isset($_POST) AND isset($_POST['getinfo'])) {
        
                echo json_encode(Processor::getinfo($_POST['getinfo']));
        
        //ELSE
        }elseif(isset($_POST) AND isset($_POST['getbyquerry'])) {
        
                echo json_encode(Processor::getbyquerry($_POST['getbyquerry']));
        
        //ELSE
        }elseif(isset($_POST) AND isset($_POST['getbyquerryappend'])) {
                //echo json_encode($_POST['getbyquerryappend']);
                echo json_encode(Processor::getbyquerryappend($_POST['getbyquerryappend']));
        
        //ELSE
        }
}else{
        echo 'nup';
}


 