<?php
// This file is part of Exabis A2fa
//
// (c) 2016 GTN - Global Training Network GmbH <office@gtn-solutions.com>
//
// Exabis A2fa is free software: you can redistribute it and/or modify
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

require_once __DIR__.'/inc.php';

$PAGE->set_url($_SERVER['REQUEST_URI']);
$PAGE->set_context(context_system::instance());

$token = required_param('token', PARAM_ALPHANUMEXT);

function block_exa2fa_login_process_password_set($token)
{
    global $DB, $CFG, $OUTPUT, $PAGE, $SESSION;
    require_once($CFG->dirroot.'/user/lib.php');

    $pwresettime = isset($CFG->pwresettime) ? $CFG->pwresettime : 1800;
    $sql = "SELECT u.*, upr.token, upr.timerequested, upr.id as tokenid
              FROM {user} u
              JOIN {user_password_resets} upr ON upr.userid = u.id
             WHERE upr.token = ?";
    $user = $DB->get_record_sql($sql, array($token));

    $forgotpasswordurl = "{$CFG->httpswwwroot}/login/";
    if (empty($user) or ($user->timerequested < (time() - $pwresettime - DAYSECS))) {
        // There is no valid reset request record - not even a recently expired one.
        // (suspicious)
        // Direct the user to the forgot password page to request a password reset.
        echo $OUTPUT->header();
        notice(get_string('noresetrecord'), $forgotpasswordurl);
        die; // Never reached.
    }
    if ($user->timerequested < (time() - $pwresettime)) {
        // There is a reset record, but it's expired.
        // Direct the user to the forgot password page to request a password reset.
        $pwresetmins = floor($pwresettime / MINSECS);
        echo $OUTPUT->header();
        notice(get_string('resetrecordexpired', '', $pwresetmins), $forgotpasswordurl);
        die; // Never reached.
    }

    if ($user->auth === 'nologin' or !is_enabled_auth($user->auth)) {
        // Bad luck - user is not able to login, do not let them set password.
        echo $OUTPUT->header();
        print_error('forgotteninvalidurl');
        die; // Never reached.
    }

    // Check this isn't guest user.
    if (isguestuser($user)) {
        print_error('cannotresetguestpwd');
    }

    // Token is correct, and unexpired.

    // Delete this token so it can't be used again.
    $DB->delete_records('user_password_resets', array('id' => $user->tokenid));

    // disable a2fa
	if ($a2faSettings = \block_exa2fa\user_setting::get($user)) {
        $a2faSettings->deactivate();
    }

    unset($SESSION->wantsurl);
    redirect($forgotpasswordurl);

    /*
    $userauth = get_auth_plugin($user->auth);
    if (!$userauth->user_update_password($user, $data->password)) {
        print_error('errorpasswordupdate', 'auth');
    }
    user_add_password_history($user->id, $data->password);
    if (!empty($CFG->passwordchangelogout)) {
        \core\session\manager::kill_user_sessions($user->id, session_id());
    }
    // Reset login lockout (if present) before a new password is set.
    login_unlock_account($user);
    // Clear any requirement to change passwords.
    unset_user_preference('auth_forcepasswordchange', $user);
    unset_user_preference('create_password', $user);

    if (!empty($user->lang)) {
        // Unset previous session language - use user preference instead.
        unset($SESSION->lang);
    }
    complete_user_login($user); // Triggers the login event.

    \core\session\manager::apply_concurrent_login_limit($user->id, session_id());

    $urltogo = core_login_get_return_url();
    unset($SESSION->wantsurl);
    redirect($urltogo, get_string('passwordset'), 1);
    */
}

$token = block_exa2fa_login_process_password_set($token);
die('x');
if ($user = $DB->get_record('user', array('username'=>$username, 'mnethostid'=>$CFG->mnet_localhost_id))) {
	block_exa2fa_send_password_change_confirmation_email($user, 'asdfasdf');
}

exit;
echo $OUTPUT->header();

$msg = \block_exa2fa\trans('de:Die Anleitung zum Zurücksetzen des A2fa Codes wurde Ihnen per E-Mail gesendet.');
notice('<div style="text-align: center; padding: 30px;">'.$msg.'</div>', $CFG->wwwroot.'/index.php');

echo $OUTPUT->footer();
