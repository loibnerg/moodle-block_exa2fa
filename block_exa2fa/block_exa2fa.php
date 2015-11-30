<?php

defined('MOODLE_INTERNAL') || die();

require_once __DIR__.'/lib/lib.php';

class block_exa2fa extends block_base {
	function init() {
		$this->title = \block_exa2fa\get_string('pluginname');
	}

	function applicable_formats() {
		return array('all' => true);
	}

	function get_content() {
		global $USER;
		
		if (!$a2faSettings = \block_exa2fa\user_setting::get($USER)) {
			return;
		}
		
		$this->content = new stdClass;
		$this->content->text  = $a2faSettings->getSettingOutput();
		
		return $this->content;
	}
}