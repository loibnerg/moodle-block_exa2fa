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
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle. If not, see <http://www.gnu.org/licenses/>.
/**
* Handle manual badge award.
*
* @package a2fa
* @copyright 2014 Sam Battat
* @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
*/
require_once('../../config.php');
require_once('../../message/output/email/message_output_email.php');

$userid = required_param('userid', PARAM_INT);
$url = required_param('url', PARAM_TEXT);

require_login();

if(is_siteadmin($USER)){
	$src = 'https://chart.googleapis.com/chart?chs=200x200&chld=M|0&cht=qr&chl='.$url.'';
	$exploded = explode('secret=', $src);
	$secret = $exploded[1];
	
	$img =  '<img src="'.$src.'"/>';

	$userreceiver = $DB->get_record('user', array('id'=>$userid));

	$message_output = new message_output_email();

	//send mail
	$eventdata = new stdClass();
	$eventdata->component         = 'auth_a2fa'; //your component name
	$eventdata->name              = 'qrcode'; //this is the message name from messages.php
	$eventdata->userfrom          = $USER;
	$eventdata->userto            = $userreceiver;
	$eventdata->subject           = "QR Code für Authentifizierung Test";
	$eventdata->fullmessage       = "";
	$eventdata->fullmessageformat = FORMAT_MARKDOWN;
	$eventdata->fullmessagehtml   = "Scannen Sie folgenden QR Code mit der Google Authenticator App um einen Token für die Moodle-Anmeldung 
		generieren zu können: </br>".$img."</br></br> Oder geben Sie alternativ folgenden Code in Ihre App ein: ".$secret;
	$eventdata->smallmessage      = '';
	$eventdata->notification      = 1; //this is only set to 0 for personal messages between users
	if($message_output->send_message($eventdata)){
		?><script>
			window.close();
		</script>
		<?php
	}
}else{
	echo 'Fehler aufgetreten';
}

?>

					