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
require_once($CFG->libdir . '/formslib.php');

class tool_checkinactive_by_period_form extends moodleform {

    public $qry = '';

    public function definition() {
        session_start();
        global $DB;
        $sendingStatus = '';
        $sendingMsg = '';
        /* Query for email and select actions is same */
        /* Oracle 11g R2, MySQL 5.6, MSSQL Server 2017, PostgreSQL 9.6, PostgreSQL 9.3, SQLite, Firebird 2.5 */
        $nowtime = time();
        $timeunit = $_SESSION['TUNT'];
        $num = $_SESSION['NUM'];
        $difference = $timeunit * $num;
        $diff = " $nowtime - CASE "
                . "           WHEN u.lastlogin >= u.currentlogin THEN u.lastlogin "
                . "           WHEN u.currentlogin > u.lastlogin THEN u.currentlogin "
                . "           ELSE u.lastlogin "
                . "           END "
                . "           > $difference AND (ID > 1) AND (deleted = 0) ";

        $qry = 'select u.id, u.firstname, u.lastname, '
                . 'u.email, u.phone1, u.lastlogin, u.currentlogin '
                . 'from  {user} u '
                . 'where ' . $diff;
        /*  ************ EMAIL ************ */
        if ($_SESSION['ACTION'] === 0){
            $rs = $DB->get_recordset_sql($qry);
            foreach ($rs as $row) {
                    $to = $row->email;
                    $subject = get_string('email_default_subject', 'tool_inactiveidentities');
                    $message = get_string('email_body', 'tool_inactiveidentities');
                    $headers = "From: " . get_string('email_default_retry', 'tool_inactiveidentities');
                    $mail_sent = @mail($to, $subject, $message, $headers);
                    if ($mail_sent == false){
                        $sendingStatus = 'Sending status:';
                        $sendingMsg = 'Sending error!';
                    }else{
                        $sendingStatus = 'Sending status:';  
                        $sendingMsg = 'All messages sent!';
                    }   
            }
            $rs->close();
            $_SESSION['ACTION'] = 2;
        }          
        /*  ************ DELETE ************ */
        else if ($_SESSION['ACTION'] === 1) {  
            /* Oracle 11g R2, MySQL 5.6, MSSQL Server 2017, PostgreSQL 9.6, PostgreSQL 9.3, SQLite, Firebird 2.5 */
            $nowtime = time();
            $timeunit = $_SESSION['TUNT'];
            $num = $_SESSION['NUM'];
            $difference = $timeunit * $num;
            $diff = " $nowtime - CASE "
                    . "           WHEN lastlogin >= currentlogin THEN lastlogin "
                    . "           WHEN currentlogin > lastlogin THEN currentlogin "
                    . "           ELSE lastlogin "
                    . "           END "
                    . "           > $difference AND (ID > 1) AND (deleted = 0) ";
            $diff = 'update {user} '
                    . ' set deleted = 1 '
                    . ' where ' . $diff;
            $query = $diff;
            $DB->execute($query);
            $_SESSION['ACTION'] = 2;
        }
        /*  ************ SELECT ************ */
        if ($_SESSION['ACTION'] === 2){  
            $mform = & $this->_form;
            $s = get_string('select_form_by_period_caption', 'tool_inactiveidentities');
            $mform->addElement('header', 'header1', $s);
            /*         * ** Back *** */
            $scaption = get_string('home_form_caption', 'tool_inactiveidentities');
            $slink = get_string('home_form_link', 'tool_inactiveidentities');
            $linkcontent = '<a href="index.php">' . $slink . '</a>';
            $mform->addElement('static', 'menu_1', $scaption, $linkcontent);

            $inact_period = get_string('inactivityperiod', 'tool_inactiveidentities');
            $mform->addElement('duration', 'timelimit', $inact_period);
            $sel_period = get_string('select_form_by_period_btRunQuery', 'tool_inactiveidentities');
            $mform->addElement('submit', 'bt_select', $sel_period);
            // *** *** *** *** *** *** *** *** ***
            $query = $qry;
            $user_names_array = array();
            $rs_set_width = $DB->get_recordset_sql($query);
            $sizes_array = array("id" => 0, "firstname" => 0, "lastname" => 0, "email" => 0, "phone1" => 0, "lastlogin" => 0, "currentlogin" => 0);
            foreach ($rs_set_width as $r) {
                $columns_array = array("id" => $r->id, "firstname" => $r->firstname, "lastname" => $r->lastname, "email" => $r->email, "phone1" => $r->phone1, "lastlogin" => $r->lastlogin, "currentlogin" => $r->currentlogin);
                foreach ($columns_array as $key => $colValue) {
                    $length = strlen($colValue);
                    if ($length > $sizes_array[$key]){
                        $sizes_array[$key] = $length;
                    }
                }
            }
            $rs_set_width->close();
            $rs2 = $DB->get_recordset_sql($query);
            foreach ($rs2 as $row) {
                $userdata = "";
                foreach ($row as $key => $value) {
                    if (strlen($value) == 0) {
                        $value = ",";
                    }
                    $userdata = $userdata . str_pad($value, $sizes_array[$key] + 1, ",", STR_PAD_RIGHT) . "|,";
                }
                $userdata = str_replace(",", "&nbsp;", $userdata);
                array_push($user_names_array, $userdata);
            }
            $rs2->close();
            $attributes = "";
            $select = $mform->addElement('select', 'users', get_string('users'), $user_names_array, $attributes);
            $select->setMultiple(true);
            $select->setSelected('0');

            $sbt_del = get_string('select_form_by_period_btDelete', 'tool_inactiveidentities');
            $mform->addElement('submit', 'bt_delete', $sbt_del);
            $sbt_email = get_string('select_form_by_period_btEmail', 'tool_inactiveidentities');
            $mform->addElement('submit', 'bt_email', $sbt_email);
            $mform->addElement('static', 'description', $sending_status, $sending_msg);
        }
          
    }

}
