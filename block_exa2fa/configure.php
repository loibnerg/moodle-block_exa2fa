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
$returnurl = required_param('returnurl', PARAM_LOCALURL);

if ($action == 'activate') {
	\block_exa2fa\user_activate();
	
	redirect($returnurl);
}
if ($action == 'deactivate') {
	\block_exa2fa\user_deactivate();

	redirect($returnurl);
}
if ($action == 'generate') {
	\block_exa2fa\user_generate_secret();
	
	redirect($returnurl);
}

die('error');
