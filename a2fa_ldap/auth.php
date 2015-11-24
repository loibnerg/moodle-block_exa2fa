<?php

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
		if (!parent::user_login($username, $password)) {
			return false;
		}
		
		return auth_plugin_a2fa::subplugin_user_login($username, $password);
	}
	
	function config_form($config, $err, $user_fields) {
	}

	function validate_form($form, &$err) {
	}

	function process_config($config) {
		return true;
	}
}

