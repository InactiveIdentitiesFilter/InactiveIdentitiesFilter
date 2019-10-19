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
require_once($CFG->libdir . '/adminlib.php');
require_once($CFG->dirroot . '/' . $CFG->admin . '/tool/inactiveidentities/checkInactiveByPeriod_form.php');

function is_param_set($param) {
    if ($param > ""){
        return true;
    }
    else{
        return false;
    }
}
$action = optional_param('action', '', PARAM_ALPHANUMEXT);
$syscontext = context_system::instance();
require_login();
admin_externalpage_setup('toolinactiveidentities'); // checks permissions specified in settings.php
$PAGE->set_context(get_context_instance(CONTEXT_SYSTEM));
echo $OUTPUT->header();

$btEmail = is_param_set (optional_param('bt_email', "", PARAM_TEXT));
$btDelete = is_param_set (optional_param('bt_delete', "", PARAM_TEXT));
$btSelect = is_param_set (optional_param('bt_select', "", PARAM_TEXT));
$timelimit = is_param_set (optional_param('timelimit', "", PARAM_TEXT));
/*  ************ EMAIL ************ */
if ( $btEmail != false) {
    $_SESSION['ACTION'] = 0;  // 'email';
} 
else if ( $btDelete && $timelimit) {          
/*  ************ DELETE ************ */
        $_SESSION['ACTION'] = 1; // 'delete'; 
}
else if ( $btSelect && $timelimit) {
/*  ************ SELECT ************ */
        $_SESSION['ACTION'] = 2; // 'select';
        $variable = optional_param_array('timelimit', null, PARAM_INT);
        $_SESSION['NUM'] = $variable['number'];
        $_SESSION['TUNT'] = $variable['timeunit']; 
}
$checkinactiveform = new tool_checkinactive_by_period_form();
$checkinactiveform->display();
echo $OUTPUT->footer();
