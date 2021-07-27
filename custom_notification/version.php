<?php
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
 * Plugin capabilities
 *
 * @package    local_custom_notification
 * @copyright  2021 Simon Zajicek
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


//verze jen pro me
$plugin->version = 202001201;

// pozadovana verze moodle
$plugin->requires = 2014051200;

//nazev pluginu
$plugin->component = 'local_custom_notification';

$plugin->cron = 2*60;
// znovu verze pluginu
$plugin->release = '1.0';
//je potreba aby ho slo instalovat
$plugin->maturity = MATURITY_STABLE;
