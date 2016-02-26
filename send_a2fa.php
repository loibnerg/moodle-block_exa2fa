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
require_once $CFG->dirroot.'/login/lib.php';

$PAGE->set_url($_SERVER['REQUEST_URI']);
$PAGE->set_context(context_system::instance());

$username = required_param('username', PARAM_USERNAME);
// $password = required_param('password', PARAM_RAW);

if ($user = $DB->get_record('user', array('username'=>$username, 'mnethostid'=>$CFG->mnet_localhost_id))) {
	$resetrecord = core_login_generate_password_reset($user);
	block_exa2fa_send_password_change_confirmation_email($user, $resetrecord);
}

/**
 * copied from login/lib.php
 * @param $user
 * @param $resetrecord
 * @return bool
 * @throws coding_exception
 * @throws moodle_exception
 */
function block_exa2fa_send_password_change_confirmation_email($user, $resetrecord) {
    global $CFG;

    $site = get_site();
    $supportuser = core_user::get_support_user();
    $pwresetmins = isset($CFG->pwresettime) ? floor($CFG->pwresettime / MINSECS) : 30;

    $data = new stdClass();
    $data->firstname = $user->firstname;
    $data->lastname  = $user->lastname;
    $data->username  = $user->username;
    $data->sitename  = format_string($site->fullname);
    $data->link      = $CFG->httpswwwroot .'/blocks/exa2fa/reset_a2fa.php?token='. $resetrecord->token;
    $data->admin     = generate_email_signoff();
    $data->resetminutes = $pwresetmins;

    $message = \block_exa2fa\trans('de:Guten Tag {$a->firstname},

jemand (wahrscheinlich Sie) hat bei \'{$a->sitename}\' das Zurücksetzen des A2fa Codes für das Nutzerkonto \'{$a->username}\' angefordert.

Um diese Anforderung zu bestätigen und den A2fa Code zu deaktivieren, gehen Sie bitte auf folgende Webseite:

{$a->link}

Hinweis:
Dieser Link wird {$a->resetminutes} Minuten nach der Anforderung ungültig. Meistens erscheint die Webadresse als blauer Link, auf den Sie einfach klicken können. Falls dies nicht funktioniert, kopieren Sie die Webadresse vollständig in die Adresszeile Ihres Browsers. Falls Sie das Zurücksetzen nicht selber ausgelöst haben, hat vermutlich jemand anders Ihren Anmeldenamen oder Ihrer E-Mail-Adresse eingegeben. Dies ist kein Grund zur Beunruhigung. Ignorieren Sie die Nachricht dann bitte.

Bei Problemen wenden Sie sich bitte an die Administrator/innen der Website.

Viel Erfolg!

{$a->admin}', $data);
    $subject = \block_exa2fa\trans('de:{$a}: A2fa Code zurücksetzen', format_string($site->fullname));

    // Directly email rather than using the messaging system to ensure its not routed to a popup or jabber.
    return email_to_user($user, $supportuser, $subject, $message);

}

// always show ok message

echo $OUTPUT->header();

$msg = \block_exa2fa\trans('de:Die Anleitung zum Zurücksetzen des A2fa Codes wurde Ihnen per E-Mail gesendet.');
notice('<div style="text-align: center; padding: 30px;">'.$msg.'</div>', $CFG->wwwroot.'/index.php');

echo $OUTPUT->footer();
