<?php

namespace block_exa2fa;

defined('MOODLE_INTERNAL') || die();

require_once __DIR__.'/common.php';
require_once __DIR__.'/GoogleAuthenticator.php';

use block_exa2fa\globals as g;

class user_setting {
	
	var $user;
	var $exa2fauser;
	
	/**
	 * @return \block_exa2fa\user_setting
	 */
	static function get($userid) {
		// always load current user from database
		// because $USER caches the user settings
		$user = g::$DB->get_record('user', ['id' => is_object($userid) ? $userid->id : $userid]);
		
		if (!$user) {
			return null;
		} else {
			$class = __CLASS__;
			return new $class($user);
		}
	}
	
	function __construct($user) {
		$this->user = $user;
	
		$this->exa2fauser = g::$DB->get_record('block_exa2fauser', ['userid' => $this->user->id]);
		}
	
	function getSettingOutput() {
		if (!$this->can_a2fa()) {
			return null;
		}
		
		$url = new \moodle_url('/blocks/exa2fa/configure.php', ['action'=>null, 'returnurl' => (new \moodle_url(g::$PAGE->url))->out_as_local_url(false)]);
		
		if ($data = $this->is_a2fa_active()) {
			$output  = '<div style="text-align: center;">A2fa ist aktiv.<br />';
			$output .= \html_writer::empty_tag('input', ['type'=>'button',
							'value'=>\block_exa2fa\trans('Neuen Code generieren'),
							'onclick'=>'document.location.href='.json_encode($url->out(false, ['action' => 'generate']))]);
			$output .= '&nbsp;&nbsp;';
			$output .= \html_writer::empty_tag('input', ['type'=>'button',
							'value'=>\block_exa2fa\trans('A2fa deaktivieren'),
							'onclick'=>'document.location.href='.json_encode($url->out(false, ['action' => 'deactivate']))]);
			$output .= '</div>';
		} else {
			$output  = '<div style="text-align: center;">Hier kannst du A2fa aktivieren um Moodle noch sicherer zu machen.<br /><br />';
			$output .= \html_writer::empty_tag('input', ['type'=>'button',
							'value'=>\block_exa2fa\trans('A2fa aktivieren'),
							'onclick'=>'document.location.href='.json_encode($url->out(false, ['action' => 'activate']))]);
			$output .= '</div>';
		}

		return $output;

	}
	
	function getTeacherOutput($courseid) {
		if (!$this->can_a2fa()) {
			return null;
		}
		
		if ($this->is_a2fa_active()) {
			$url = new \moodle_url('/blocks/exa2fa/configure.php', ['action'=>null, 'returnurl' => (new \moodle_url(g::$PAGE->url))->out_as_local_url(false), 'userid' => $this->user->id, 'courseid'=>$courseid]);
			
			$output  = '<div style="text-align: center;">';
			$output .= \html_writer::empty_tag('input', ['type'=>'button',
							'value'=>\block_exa2fa\trans('A2fa deaktivieren'),
							'onclick'=>'document.location.href='.json_encode($url->out(false, ['action' => 'deactivate']))]);
			$output .= '</div>';
			return $output;
		}
	}
	
	function can_a2fa() {
		return $this->user->id && !empty($this->user->auth) && /* guest user has no auth set */
		(in_array($this->user->auth, get_enabled_plugins_with_a2fa_available()) /* is standard login */
				|| preg_match('!^a2fa_!', $this->user->auth)); /* is a2fa login */
	}

	function is_a2fa_active() {
		if (preg_match('!^a2fa_!', $this->user->auth) && $this->exa2fauser && $this->exa2fauser->a2faactive && $this->exa2fauser->secret) {
			return $this->exa2fauser;
		} else {
			return null;
		}
	}

	function activate($secret, $token) {
		if (!$this->can_a2fa()) {
			print_error('a2fa not allowed');
		}

		$ga = new \PHPGangsta_GoogleAuthenticator();
		if (!$ga->verifyCode($secret, $token, 2)) {
			return false;
		}

		$data = [
			'a2faactive' => 1,
			'secret' => $secret
		];

		\block_exa2fa\globals::$DB->insert_or_update_record('block_exa2fauser', $data, ['userid' => $this->user->id]);
		
		g::$DB->update_record('user', [
			'id' => $this->user->id,
			'auth' => 'a2fa_'.preg_replace('!^a2fa_!', '', $this->user->auth)
		]);

		return true;
	}
	
	function deactivate() {
		\block_exa2fa\globals::$DB->update_record('block_exa2fauser', [
			'a2faactive' => 0
		], ['userid' => $this->user->id]);
	
		g::$DB->update_record('user', [
			'id' => $this->user->id,
			'auth' => preg_replace('!^a2fa_!', '', $this->user->auth)
		]);
	}
}

function generate_secret() {
	$ga = new \PHPGangsta_GoogleAuthenticator();
	do{
		$secret = $ga->createSecret();
		$secretCheck = g::$DB->get_field_select('block_exa2fauser', 'secret', g::$DB->sql_compare_text('secret')." = ?", [$secret]);
	} while($secretCheck);

	return $secret;
}

function get_enabled_plugins_with_a2fa_available() {
	$enabledPlugins = \core_plugin_manager::instance()->get_enabled_plugins('auth');
	$a2faPlugins = [];
	
	foreach (['manual', 'ldap', 'email'] as $plugin) {
		if (in_array($plugin, $enabledPlugins) && in_array('a2fa_'.$plugin, $enabledPlugins)) {
			// normal and a2fa plugin available
			$a2faPlugins[$plugin] = $plugin;
		}
	}
	
	return $a2faPlugins;
}

function teacher_can_deactivate_student($courseid, $studentid) {
	global $USER;
	$teacher = $USER;
	
	$context = \context_course::instance($courseid);
	
	return has_capability('enrol/manual:enrol', $context, $teacher) // is teacher
		&& is_enrolled($context, $studentid); // student is enrolled
}
