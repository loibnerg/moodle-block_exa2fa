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
		
		$this->content = new stdClass;
		$this->content->text  = \block_exa2fa\user_setting::get($USER)->getSettingOutput();
		
		return $this->content;
	}
}