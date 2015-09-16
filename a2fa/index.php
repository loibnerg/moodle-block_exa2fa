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
 * Main login page.
 *
 * @package    core
 * @subpackage auth
 * @copyright  1999 onwards Martin Dougiamas  http://dougiamas.com
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require('../../config.php');

$context = context_system::instance();
$PAGE->set_url("$CFG->httpswwwroot/login/index.php");
$PAGE->set_context($context);
$PAGE->set_pagelayout('login');

$PAGE->navbar->ignore_active();
$loginsite = get_string("loginsite");
$PAGE->navbar->add($loginsite);

echo $OUTPUT->header();

if (isloggedin() and !isguestuser()){
	redirect(new moodle_url('/my'));
}

$content = html_writer::tag('h4', 'W&auml;hlen Sie eine Authentifizierungsmethode:');
$content .= html_writer::empty_tag('br').html_writer::empty_tag('br');
$content .= html_writer::link(new moodle_url('/auth/a2fa/login.php'), '2-Faktoren Authentifizierung');
$content .= html_writer::empty_tag('br').html_writer::empty_tag('br');
$content .= html_writer::link(new moodle_url('/auth/a2fa/login.php?standard=1'), 'Standard-Login');

echo html_writer::div(html_writer::div(html_writer::div($content, '', array('style'=>'position: relative; float: left; left: -50%;')), '', array('style'=>'position: relative; float: left; left: 50%;')), '', array('style'=>'position: relative; overflow: hidden;'));
