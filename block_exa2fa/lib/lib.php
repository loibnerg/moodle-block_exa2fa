<?php

namespace block_exa2fa;

defined('MOODLE_INTERNAL') || die();

require_once __DIR__.'/common.php';
require_once __DIR__.'/GoogleAuthenticator.php';

function user_is_a2fa_active($userid = 0) {
	global $USER, $DB;
	
	if (!$userid) {
		$userid = $USER->id;
	}
	
	$row = $DB->get_record('block_exa2fauser', ['userid' => $userid]);
	if ($row && $row->a2faactive && $row->secret) {
		return $row;
	} else {
		return null;
	}
}

function user_can_a2fa() {
	global $USER;

	return $USER->id && !empty($USER->auth) && /* guest user has no auth set */
		(in_array($USER->auth, ['manual', 'ldap', 'email']) /* is standard login */
		|| preg_replace('!^a2fa_!', '', $USER->auth)); /* is a2fa login */
}

function user_activate() {
	global $USER, $DB;
	
	if (!user_can_a2fa()) {
		print_error('a2fa not allowed');
	}
	
	$row = $DB->get_record('block_exa2fauser', ['userid' => $USER->id]);
	
	\block_exa2fa\db::insert_or_update_record('block_exa2fauser', [
		'a2faactive' => 1
	], ['userid' => $USER->id]);
	
	if ($row && $row->secret) {
		$secret = $row->secret;
	} else {
		// generate
		$ga = new \PHPGangsta_GoogleAuthenticator();
		do{
			$secret = $ga->createSecret();
			$secretCheck = $DB->get_field_select('block_exa2fauser', 'secret', $DB->sql_compare_text('secret')." = ?", [$secret]);
		} while($secretCheck);
	}
	
	if (!$row || !$row->secret) {
		user_generate_secret();
	}
	
	$DB->update_record('user', [
		'id' => $USER->id,
		'auth' => 'a2fa_'.preg_replace('!^a2fa_!', '', $USER->auth)
	]);
}

function user_deactivate() {
	global $USER, $DB;
	
	\block_exa2fa\db::update_record('block_exa2fauser', [
		'a2faactive' => 0
	], ['userid' => $USER->id]);

	$DB->update_record('user', [
		'id' => $USER->id,
		'auth' => preg_replace('!^a2fa_!', '', $USER->auth)
	]);
}

function user_generate_secret() {
	global $USER, $DB;
	
	$ga = new \PHPGangsta_GoogleAuthenticator();
	do{
		$secret = $ga->createSecret();
		$secretCheck = $DB->get_field_select('block_exa2fauser', 'secret', $DB->sql_compare_text('secret')." = ?", [$secret]);
	} while($secretCheck);
	
	\block_exa2fa\db::update_record('block_exa2fauser', [
		'secret' => $secret
	], ['userid' => $USER->id]);
}