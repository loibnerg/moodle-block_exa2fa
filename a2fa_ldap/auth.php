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
 * a2fa_ldap authentication login
 *
 * @package auth_a2fa_ldap
 * @author Petr Skoda
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 */

defined('MOODLE_INTERNAL') || die();

require_once $CFG->dirroot.'/auth/ldap/auth.php';
require_once $CFG->dirroot.'/auth/a2fa/auth.php';

/**
 * Plugin for no authentication - disabled user.
 */
class auth_plugin_a2fa_ldap extends auth_plugin_ldap {


    /**
     * Constructor.
     */
    function auth_plugin_a2fa_ldap() {
        $this->init_plugin('ldap');
        $this->authtype = 'a2fa_ldap';
    }

    /**
     * Do not allow any login.
     *
     */
    function user_login($username, $password) {
    	global $CFG, $DB;
    	
		if (!parent::user_login($username, $password)) {
			return false;
		}

		if (!$user = $DB->get_record('user', array('username'=>$username, 'mnethostid'=>$CFG->mnet_localhost_id))) {
			return false;
		}
		
		$field = $DB->get_record('user_info_field', array('shortname'=>'a2fasecret'));
		$secret = $DB->get_record('user_info_data', array('fieldid'=>$field->id, 'userid'=>$user->id));
		if (empty($secret) || empty($secret->data)) {
			// no secret configured
			return true;
		}
		
		$token = optional_param('token', "", PARAM_TEXT);
		$ga = new PHPGangsta_GoogleAuthenticator();
		if (!empty($token) && $ga->verifyCode($secret->data, $token, 2)) {
			return true;
		}
		
		// for login form, set the login error message
		global $SESSION;
		$SESSION->loginerrormsg = 'A2fa-Required: Bitte gültigen code eingeben';
		
		// for webservice, set the login error header
		header('X-A2fa-Required: Bitte gültigen code eingeben');
		
		return false;
	}
	
    function config_form($config, $err, $user_fields) {
    }

	function validate_form($form, &$err) {
    }

    function process_config($config) {
        return true;
    }
}

