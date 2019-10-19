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

require_once($CFG->libdir.'/formslib.php');

class tool_inactiveidentities_index_form extends moodleform {
    public function definition () {
        $mform = $this->_form;
        $s = get_string('home_title', 'tool_inactiveidentities');
        $mform->addElement('header', 'header1', $s);
        /**** EMAIL form link *****************************************************/
        $scaption = get_string('email_form_caption', 'tool_inactiveidentities');
        $slink = get_string('email_form_link', 'tool_inactiveidentities');
        $linkcontent = '<a href="email.php">' . $slink . '</a>';
        $mform->addElement('static', 'menu_1', $scaption, $linkcontent);
        /**** Seelect by PERIOD form link *****************************************************/
        $scaption = get_string('select_form_by_period_caption', 'tool_inactiveidentities');
        $slink = get_string('select_form_by_period_link', 'tool_inactiveidentities');
        $linkcontent = '<a href="checkInactiveByPeriod.php">' . $slink . '</a>';
        $mform->addElement('static', 'menu_2', $scaption, $linkcontent);
        /**** Seelect by COURSE form link *****************************************************/
        $scaption = get_string('select_form_by_course_caption', 'tool_inactiveidentities');
        $slink = get_string('select_form_by_course_link', 'tool_inactiveidentities');
        $linkcontent = '<a href="checkByCourse.php">' . $slink . '</a>';
        $mform->addElement('static', 'menu_3', $scaption, $linkcontent);
    }
}
