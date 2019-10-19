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

defined('MOODLE_INTERNAL') || die();
require_once($CFG->libdir.'/formslib.php');

class tool_inactiveidentities_email_form extends moodleform {
    public function definition () {
        $mform = $this->_form;
        $s = get_string('email_title', 'tool_inactiveidentities');
        $mform->addElement('header', 'header1', $s);

        $scaption = get_string('home_form_caption', 'tool_inactiveidentities');
        $slink = get_string('home_form_link', 'tool_inactiveidentities');
        $linkcontent = '<a href="index.php">' . $slink . '</a>';
        $mform->addElement('static', 'menu_1', $scaption, $linkcontent);

        $mform->addElement('header', 'config_headeremail', get_string('email_settings_title', 'tool_inactiveidentities'));

        $mform->addElement('text', 'config_retryemail', get_string('email_retry', 'tool_inactiveidentities'));
        $mform->setType('config_retryemail', PARAM_TEXT);
        $mform->setDefault('config_retryemail', get_string('email_default_retry', 'tool_inactiveidentities'));

        $mform->addElement('text', 'config_subjectemail', get_string('email_subject', 'tool_inactiveidentities'));
        $mform->setType('config_subjectemail', PARAM_TEXT);
        $mform->setDefault('config_subjectemail', get_string('email_default_subject', 'tool_inactiveidentities'));

        $editoroptions = array('trusttext' => true, 'subdirs' => true, 'maxfiles' => 1,
        'maxbytes' => 1024);
        $mform->addElement('editor', 'config_bodyemail',
                get_string('email_body', 'tool_inactiveidentities'),
                $editoroptions)->setValue(
                        array('text' => get_string('email_default_body', 'tool_inactiveidentities')) );
        $mform->setType('config_bodyemail', PARAM_RAW);

        $this->add_action_buttons();
    }
}
