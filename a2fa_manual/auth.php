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
 * a2fa_manual authentication login
 *
 * @package auth_a2fa_manual
 * @author Petr Skoda
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 */

defined('MOODLE_INTERNAL') || die();

require_once __DIR__.'/../manual/auth.php';

/**
 * Plugin for no authentication - disabled user.
 */
class auth_plugin_a2fa_manual extends auth_plugin_manual {


    /**
     * Constructor.
     */
    function auth_plugin_a2fa_manual() {
        $this->authtype = 'a2fa_manual';
        $this->pluginconfig = 'auth/'.$this->authtype;
    }

    /**
     * Do not allow any login.
     *
     */
    function user_login($username, $password) {
		if (!parent::user_login($username, $password)) {
			return false;
		}

		$a2fa = get_auth_plugin('a2fa');
		if ($a2fa->user_login($username, $password)) {
			// ok
			return true;
		} else {
			die('a2fa-required: Bitte gültigen code eingeben');
		}
	}
}


