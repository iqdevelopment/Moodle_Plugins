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

require_once '../../config.php';
global $USER,$DB,$CFG;
//nastaveni stranky, kde bude plugin
$PAGE->set_url('/local/pluginname/index.php');
$PAGE->set_context(context_system::instance());

require_login();

$strPageTitle = get_string('pluginname','bbb_attendance');

$PAGE->set_title($strPageTitle);

echo $OUTPUT->header();

require_login();

if(!has_capability('local/bbb_attendance:admin', context_system::instance()))
{
echo $OUTPUT->header();
echo "<h3>".get_string('noacess','local_bbb_attendance')."</h3>";
echo $OUTPUT->footer();
exit;
}



echo $OUTPUT->footer();