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
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle. If not, see <http://www.gnu.org/licenses/>.
/**
* Handle manual badge award.
*
* @package a2fa
* @copyright 2014 Sam Battat
* @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/
require_once('../../config.php');
require_once __DIR__.'/lib/lib.php';

$action = required_param('action', PARAM_ALPHANUMEXT);
$returnurl = new moodle_url(required_param('returnurl', PARAM_LOCALURL));
$userid = optional_param('userid', 0, PARAM_INT);
$courseid = optional_param('courseid', 0, PARAM_INT);

require_login($courseid);

if (!$userid || $userid == $USER->id) {
	$userid = $USER->id;
} elseif (($action == 'deactivate') && \block_exa2fa\teacher_can_deactivate_student($COURSE->id, $userid)) {
	// teacher can only deactivate
} else {
	print_error('no permissions');
}

if ($action == 'activate') {
	\block_exa2fa\user_setting::get($userid)->activate();
} elseif ($action == 'deactivate') {
	\block_exa2fa\user_setting::get($userid)->deactivate();
} elseif ($action == 'generate') {
	\block_exa2fa\user_setting::get($userid)->generate_secret();
} else {
	print_error('unknown action');
}

redirect($returnurl);

