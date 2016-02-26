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

require_once('../../config.php');
require_once __DIR__.'/lib/lib.php';

$action = required_param('action', PARAM_ALPHANUMEXT);
$returnurl = new moodle_url(required_param('returnurl', PARAM_LOCALURL));
$userid = optional_param('userid', 0, PARAM_INT);
$courseid = optional_param('courseid', 0, PARAM_INT);

require_login($courseid);

if (!$userid || $userid == $USER->id) {
	$userid = $USER->id;
} elseif (($action == 'deactivate') && \block_exa2fa\teacher_can_deactivate_student($COURSE->id, $userid)) {
	// teacher can only deactivate
} else {
	print_error('no permissions');
}

if ($action == 'deactivate') {
	\block_exa2fa\user_setting::get($USER->id)->deactivate();
	redirect($returnurl);
} elseif ($action == 'activate' || $action == 'generate') {

	$secret = optional_param('secret', '', PARAM_ALPHANUM);
	$token = optional_param('token', '', PARAM_ALPHANUM);

	$error = '';
	if ($secret && $token) {
		if (\block_exa2fa\user_setting::get($USER->id)->activate($secret, $token)) {
			// ok
			redirect($returnurl);
		}

		$error = 'Der eingegebene Code ist falsch';
	}

	if (!$secret) {
		$secret = \block_exa2fa\generate_secret();
	}

	// Default formatting.
	$ga = new \PHPGangsta_GoogleAuthenticator();
	// don't allow any special characters in code name
	$src = $ga->getQRCodeGoogleUrl(preg_replace('![^a-zA-Z0-9]+!', '-', $SITE->fullname.'-'.fullname($USER)), $secret);

	$img = '<img src="'.$src.'" />';

	$PAGE->set_url('/blocks/exa2fa/configure.php');
	$PAGE->set_context(context_system::instance());

	echo $OUTPUT->header();

	echo '<div style="text-align: center;">Dein neuer a2fa Code lautet: '.$secret.'<br />';

	echo '<h2>1. Bitte scannen Sie den QR Code mit einer Auth App (z.B. FreeOTP) ein.</h2>';

	echo $img;

	echo '<h2>2. Geben Sie zur Kontrolle den in der Auth App generierten 6-stelligen Code ein.</h2>';

	if ($error) {
		echo '<div class="alert alert-error">'.$error.'</div>';
	}

	?>
		<form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
			<input type="hidden" name="secret" value="<?php echo $secret; ?>" />
			<input type="text" name="token" size="15" value="" />
			<input type="submit" value="Bestätigen" />
		</form>
	<?php

	echo '<br /><br />';
	echo \html_writer::empty_tag('input', ['type'=>'button',
			'value'=>\block_exa2fa\trans('zurück'),
			'onclick'=>'document.location.href='.json_encode($returnurl->out(false))]);

	echo $OUTPUT->footer();
} else {
	print_error('unknown action');
}
