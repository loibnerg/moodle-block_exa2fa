<?php

namespace block_exa2fa;

defined('MOODLE_INTERNAL') || die();

require_once __DIR__.'/../lib/lib.php';

class api {
	static function user_login($username, $password) {
		global $CFG, $DB;
		
		if (!$user = $DB->get_record('user', array('username'=>$username, 'mnethostid'=>$CFG->mnet_localhost_id))) {
			// no user yet -> no a2fa configured -> a2fa check not needed
			return true;
		}
		
		if (!$data = \block_exa2fa\user_is_a2fa_active($user->id)) {
			// no secret configured -> a2fa check not needed
			return true;
		}
		
		$token = optional_param('token', "", PARAM_TEXT);
		$ga = new \PHPGangsta_GoogleAuthenticator();
		if (!empty($token) && $ga->verifyCode($data->secret, $token, 2)) {
			// login ok
			return true;
		}
		
		$error = 'Bitte gültigen code eingeben';
		
		// for login form, set the login error message
		global $A2FA_ERROR;
		$A2FA_ERROR = $error;
		
		// for webservice, set the login error header
		header('X-A2fa-Required: '.htmlentities($error));
		
		return false;
	}
}
