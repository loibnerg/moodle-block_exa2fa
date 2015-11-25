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
		global $USER, $DB, $CFG, $PAGE;
		
		if (!\block_exa2fa\user_can_a2fa()) {
			return null;
		}
		
		if ($data = \block_exa2fa\user_is_a2fa_active()) {
			// Default formatting.
			$site = $DB->get_record('course', array('id'=>1));
			$urlencoded = urlencode('otpauth://totp/'.str_replace(' ','-',$site->fullname.'-'.fullname($USER)).'?secret='.$data->secret.'');
			$src = 'https://chart.googleapis.com/chart?chs=200x200&chld=M|0&cht=qr&chl='.$urlencoded.'';
	
			$img =  '<img src="'.$src.'"/>';
			
			$this->content = new stdClass;
			$this->content->text  = '<div style="text-align: center;">Dein a2fa Code lautet: '.$data->secret.'<br />';
			$this->content->text .= '<div style="padding: 5px;">';
			$this->content->text .= $img;
			$this->content->text .= '</div>';
			$this->content->text .= html_writer::empty_tag('input', ['type'=>'button',
									'value'=>\block_exa2fa\trans('Code neu generieren'),
									'onclick'=>'document.location.href='.json_encode($CFG->wwwroot.'/blocks/exa2fa/configure.php?action=generate&returnurl='.urlencode($PAGE->url))]);
			$this->content->text .= '&nbsp;&nbsp;';
			$this->content->text .= html_writer::empty_tag('input', ['type'=>'button',
									'value'=>\block_exa2fa\trans('A2fa deaktivieren'),
									'onclick'=>'document.location.href='.json_encode($CFG->wwwroot.'/blocks/exa2fa/configure.php?action=deactivate&returnurl='.urlencode($PAGE->url))]);
			$this->content->text .= '</div>';
		} else {
			$this->content = new stdClass;
			$this->content->text  = '<div style="text-align: center;">Hier kannst du A2fa aktivieren um Moodle noch sicherer zu machen.<br /><br />';
			$this->content->text .= html_writer::empty_tag('input', ['type'=>'button',
									'value'=>\block_exa2fa\trans('A2fa aktivieren'),
									'onclick'=>'document.location.href='.json_encode($CFG->wwwroot.'/blocks/exa2fa/configure.php?action=activate&returnurl='.urlencode($PAGE->url))]);
			$this->content->text .= '</div>';
		}
		
		return $this->content;
	}
}