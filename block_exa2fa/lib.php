<?php

defined('MOODLE_INTERNAL') || die();

require_once __DIR__.'/lib/lib.php';

/**
 * @param \core_user\output\myprofile\tree $tree
 * @param unknown $user
 * @param unknown $iscurrentuser
 * @param unknown $course
 */
function block_exa2fa_myprofile_navigation($tree, $user, $iscurrentuser, $course) {
	if (!$a2faSettings = \block_exa2fa\user_setting::get($user)) {
		return;
	}
	
	$content = '';
	if ($iscurrentuser) {
		// current user can turn on/off
		$content = $a2faSettings->getSettingOutput();
	} elseif ($course && \block_exa2fa\teacher_can_deactivate_student($course->id, $user->id)) {
		$content = $a2faSettings->getTeacherOutput($course->id);
	}
	
	if (!$content) {
		return;
	}
	
	$node = new core_user\output\myprofile\node('contact', 'a2fa_settings', \block_exa2fa\trans('de:A2fa Einstellungen'), null, null, $content);
	$tree->add_node($node);
}