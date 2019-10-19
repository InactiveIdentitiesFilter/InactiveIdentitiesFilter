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
require_once($CFG->dirroot.'/'.$CFG->admin.'/tool/inactiveidentities/email_form.php');

$action = optional_param('action', '', PARAM_ALPHANUMEXT);

$syscontext = context_system::instance();

require_login();
admin_externalpage_setup('toolinactiveidentities'); // checks permissions specified in settings.php
$PAGE->set_context(get_context_instance(CONTEXT_SYSTEM));

echo $OUTPUT->header();


$emailform = new tool_inactiveidentities_email_form();
$emailfromdata = $emailform->get_data();
$configdata = get_config('tool_inactiveidentities');
if (!empty($configdata->daysbeforedeletion)) {
        $data = new stdClass();
        $data->config_daysbeforedeletion = $configdata->daysbeforedeletion;
        $data->config_daysofinactivity = $configdata->daysofinactivity;
        $data->config_subjectemail = $configdata->emailsubject;
        $data->config_bodyemail['text'] = $configdata->emailbody;
        $emailform->set_data($data);
}
$emailform->display();

if ($emailform->is_submitted()) {
    set_config('emailsubject', $emailfromdata->config_subjectemail, 'tool_inactiveidentities');
    set_config('emailbody', $emailfromdata->config_bodyemail['text'], 'tool_inactiveidentities');
}


?>


 
<?php
echo $OUTPUT->footer();

