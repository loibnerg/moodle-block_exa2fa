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
		global $USER, $DB;
		
		if (false) {
			// Default formatting.
			$secret = 'sffdssdf';
			$site = $DB->get_record('course', array('id'=>1));
			$urlencoded = urlencode('otpauth://totp/'.str_replace(' ','-',$site->fullname).'-'.fullname($USER).'?secret='.$secret.'');
			$src = 'https://chart.googleapis.com/chart?chs=200x200&chld=M|0&cht=qr&chl='.$urlencoded.'';
	
			$img =  '<img src="'.$src.'"/>';
			
			$this->content = new stdClass;
			$this->content->text  = '<div style="text-align: center;">Dein a2fa Code:<br />';
			$this->content->text .= '<div style="padding: 5px;">';
			$this->content->text .= $img;
			$this->content->text .= '</div>';
			$this->content->text .= html_writer::empty_tag('input', ['type'=>'button', 'value'=>\block_exa2fa\trans('Code neu generieren')]);
			$this->content->text .= '&nbsp;&nbsp;';
			$this->content->text .= html_writer::empty_tag('input', ['type'=>'button', 'value'=>\block_exa2fa\trans('A2fa deaktivieren')]);
			$this->content->text .= '</div>';
		} else {
			$this->content = new stdClass;
			$this->content->text  = '<div style="text-align: center;">Hier kannst du A2fa aktivieren um Moodle noch sicherer zu machen.<br /><br />';
			$this->content->text .= html_writer::empty_tag('input', ['type'=>'button', 'value'=>\block_exa2fa\trans('A2fa aktivieren')]);
			$this->content->text .= '</div>';
		}
		
		return $this->content;
	}
}