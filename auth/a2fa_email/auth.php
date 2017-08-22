<?php
// This file is part of Moodle - http://moodle.org/
//
// (c) 2016 GTN - Global Training Network GmbH <office@gtn-solutions.com>
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// This script is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You can find the GNU General Public License at <http://www.gnu.org/licenses/>.
//
// This copyright notice MUST APPEAR in all copies of the script!

defined('MOODLE_INTERNAL') || die();

require_once $CFG->dirroot.'/auth/email/auth.php';

/**
 * Plugin for no authentication - disabled user.
 */
class auth_plugin_a2fa_email extends auth_plugin_email {

	/**
	 * Constructor.
	 */
	function __construct() {
		parent::__construct();
		$this->authtype = 'a2fa_email';
        $this->errorlogtag = '[AUTH A2FA_EMAIL] ';
	}

	/**
	 * Do not allow any login.
	 *
	 */
	function user_login($username, $password) {
		if (!parent::user_login($username, $password)) {
			return false;
		}
		
		return \block_exa2fa\api::user_login($username, $password);
	}
	
	function config_form($config, $err, $user_fields) {
	}

	function validate_form($form, &$err) {
	}

	function process_config($config) {
		return true;
	}

	function user_update_password($user, $newpassword) {
		if (!parent::user_update_password($user, $newpassword)) {
			return false;
		}

		return \block_exa2fa\api::user_update_password($user, $newpassword);
	}
}
