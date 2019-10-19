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

class tool_check_by_course_form extends moodleform {

    public function definition() {
        session_start();
        global $DB;
        $sending_status = '';  
        $sending_msg = '';
        /* vars used in query */
        $selectedC = optional_param('courses', "", PARAM_TEXT);
        if ( !($selectedC>'') ){
            $courses = get_courses("all", "fullname ASC", 'c.id,c.fullname,c.shortname');
            $n = count($courses);
            if ($n > 0){
                $selectedC = '0';
            }
        }
        $strArr = $_SESSION['ALLCOURSES'];
        $diff = '';
        foreach ($selectedC as $numid) {
            $strrow = $strArr[$numid];
            $value = substr($strrow, 0, strpos($strrow, '|'));
            $value = str_replace("&nbsp;", '', $value);
            if ($diff != "") {
                $diff = $diff . ",";
            }
            $diff = $diff . $value;
        }
        /* Query for email and select actions is same */
        $qry ='select u.id, u.firstname, u.lastname, '
                . 'u.email, u.phone1, u.lastlogin, u.currentlogin '
                . 'FROM {user} u JOIN {user_enrolments} ue  '
                . 'ON ue.userid = u.id JOIN {enrol} e '
                . 'ON (e.id = ue.enrolid AND e.courseid in ( ' . $diff . ' ) ) '
                . 'WHERE u.deleted = 0' ;     
        /*  ************ EMAIL ************ */
        if ($_SESSION['ACTION'] === 0){    
            $rs = $DB->get_recordset_sql($qry);
            foreach ($rs as $row) {
                $to = $row->email;
                $subject = get_string('email_default_subject', 'tool_inactiveidentities');
                $message = get_string('email_body', 'tool_inactiveidentities');
                $headers = "From: " . get_string('email_default_retry', 'tool_inactiveidentities');
                //$mail_sent = @mail($to, $subject, $message, $headers);
                if ($mail_sent == false){
                    $sending_status = 'Sending status:';
                    $sending_msg = 'Sending error!';
                }else{
                    $sending_status = 'Sending status:';  
                    $sending_msg = 'All messages sent!';
                }
            }
            $rs->close();
            $_SESSION['ACTION'] = 2;
        }  
        /*  ************ DELETE ************ */
        else if ($_SESSION['ACTION'] === 1) {   
            $diff = ' id in (' . $_SESSION['ALLIDS'] . ' )';           
            $query = 'UPDATE {user} u SET deleted=1 '
                    . 'where ' . $diff;
            $DB->execute($query);
            $_SESSION['ACTION'] = 2;
        }
        /*  ************ SELECT ************ */
        if ($_SESSION['ACTION'] === 2){   
            $studcourses = $selectedC;   //$_SESSION['studcourses'];
            $mform = & $this->_form;
            $s = get_string('select_form_by_course_caption', 'tool_inactiveidentities');
            $mform->addElement('header', 'header1', $s);
            /*         * ** Back *** */
            $scaption = get_string('home_form_caption', 'tool_inactiveidentities');
            $slink = get_string('home_form_link', 'tool_inactiveidentities');
            $linkcontent = '<a href="index.php">' . $slink . '</a>';
            $mform->addElement('static', 'menu_1', $scaption, $linkcontent);
            // *** *** *** *** *** *** *** *** ***
            /* Retrieve and Format Courses */
            $course_names_array = array();
            $courses = get_courses("all", "fullname ASC", 'c.id,c.fullname,c.shortname');
            $sizes_array = array("id" => 0, "shortname" => 0, "fullname" => 0);
            $iddata = "";
            foreach ($courses as $r) {
                $columns_array = array("id" => $r->id, "shortname" => $r->shortname, "fullname" => $r->fullname);
                foreach ($columns_array as $key => $colValue) {
                    $length = strlen($colValue);
                    if ($length > $sizes_array[$key]){
                        $sizes_array[$key] = $length;
                    }
                }
            }
            foreach ($courses as $row) {
                $coursedata = "";
                foreach ($row as $key => $value) {
                    $coursedata = $coursedata . str_pad($value, $sizes_array[$key] + 1, ",", STR_PAD_RIGHT) . "|,";
                }
                $coursedata = str_replace(",", "&nbsp;", $coursedata);
                array_push($course_names_array, $coursedata);
            }
            $_SESSION['ALLCOURSES'] = $course_names_array;
            $user_names_array = array();
            // ******************************************************
            /* Retrieve and Format USERS */
            if (!empty($studcourses)) {
                if (!empty($qry)) {
                    $selectedC = $studcourses;
                    $strArr = $_SESSION['ALLCOURSES'];
                    $user_names_array = array();
                    $rs_users_format = $DB->get_recordset_sql($qry);
                    $sizes_array = array("id" => 0, "firstname" => 0, "lastname" => 0, "email" => 0, "phone1" => 0, "lastlogin" => 0, "currentlogin"=>0);
                    foreach ($rs_users_format as $r) {
                        $users_array = array("id" => $r->id, "firstname" => $r->firstname, "lastname" => $r->lastname, "email" => $r->email, "phone1" => $r->phone1, "lastlogin" => $r->lastlogin, "currentlogin" => $r->currentlogin);
                        foreach ($users_array as $key => $colValue) {
                            $length = strlen($colValue);
                            if ($length > $sizes_array[$key]){
                                $sizes_array[$key] = $length;
                            }
                        }
                    }
                    $rs_users_format->close();
                    //************************************ fill select list box
                    $rs_users = $DB->get_recordset_sql($qry);
                    foreach ($rs_users as $row) {
                        $userdata = "";
                        foreach ($row as $key => $value) {
                            if (strlen($value) == 0) {
                                $value = ",";
                            }
                            $userdata = $userdata . str_pad($value, $sizes_array[$key] + 1, ",", STR_PAD_RIGHT) . "|,";
                            if ($key == "id") {
                                if ($iddata != "") {
                                    $iddata = $iddata . ",";
                                }
                                $iddata = $iddata . $value;
                            }
                        }
                        $userdata = str_replace(",", "&nbsp;", $userdata);
                        array_push($user_names_array, $userdata);
                    }
                    $rs_users->close();
                }
                $_SESSION['ALLIDS'] = $iddata;
            }
            // ****************************************************** end users
            /* *** form elements *** */
            /* SELECT list box courses */
            $s = get_string('select_form_by_course_select_courses', 'tool_inactiveidentities');
            $select = $mform->addElement('select', 'courses', $s, $course_names_array, $attributes);
            $select->setMultiple(true);
            $select->setSelected('0');
            /* BUTTON run a query */
            $s = get_string('select_form_by_course_btRunQuery', 'tool_inactiveidentities');
            $mform->addElement('submit', 'bt_select', $s); //'bt_select_course', $s);
            /* SELECT list box users */
            $s = get_string('select_form_by_course_select_users', 'tool_inactiveidentities');
            $select = $mform->addElement('select', 'users', $s, $user_names_array, $attributes);
            $select->setMultiple(true);
            $select->setSelected('0');
            /* BUTTONS DELETE and EMAIL */
            $s = get_string('select_form_by_period_btDelete', 'tool_inactiveidentities');
            $mform->addElement('submit', 'bt_delete', $s);
            $s = get_string('select_form_by_period_btEmail', 'tool_inactiveidentities');
            $mform->addElement('submit', 'bt_email', $s);
            $mform->addElement('static', 'description', $sending_status, $sending_msg);

        }
    }
}
