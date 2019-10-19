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
 * Inactive identities(users) cleanup
 *
 * @package    tool
 * @subpackage inactiveidentities
 * @copyright  2017 anonymous1, anonymous2
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(__FILE__) . '/../../../config.php');
require_once($CFG->libdir.'/adminlib.php');
require_once($CFG->dirroot.'/'.$CFG->admin.'/tool/inactiveidentities/index_form.php');

$action = optional_param('action', '', PARAM_ALPHANUMEXT);

$syscontext = context_system::instance();

require_login();
admin_externalpage_setup('toolinactiveidentities'); // checks permissions specified in settings.php
$PAGE->set_context(get_context_instance(CONTEXT_SYSTEM));


echo $OUTPUT->header();
session_start();
$_SESSION['ACTION'] = 2; // 'select';
/*
$userStr = $CFG->prefix . "user";
$_SESSION['$QRYVAR'] = 'select u.id, u.firstname, u.lastname, '
                . 'u.email, u.phone1, u.lastlogin '
                . 'from ' . $userStr . ' u where (ID > 1) AND (deleted = 0) ';
$_SESSION['$DELVAR'] = 'select u.id, u.firstname, u.lastname, '
                    . ' u.email, u.phone1, u.lastlogin '
                    . ' from ' . $userStr . ' u where (ID > 1) AND (deleted = 0) ';
*/
$indexform = new tool_inactiveidentities_index_form();
$indexform->display();

?>




<?php
echo $OUTPUT->footer();

