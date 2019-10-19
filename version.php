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


$plugin->version   = 2018040900;
$plugin->requires  = 2015111000;
$plugin->component = 'tool_inactiveidentities';
$plugin->maturity = MATURITY_BETA;
$plugin->release = 'v3.4.2-r0.93'; // version 0.93 works with moodle version 3.4.2.
