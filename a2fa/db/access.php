<?php

defined('MOODLE_INTERNAL') || die();

$capabilities = array(
	'block/exa2fa:addinstance' => array(
			'captype' => 'write',
			'contextlevel' => CONTEXT_BLOCK,
			'archetypes' => array(
					'editingteacher' => CAP_ALLOW,
					'manager' => CAP_ALLOW
			),
			'clonepermissionsfrom' => 'moodle/site:manageblocks'
	),
	'block/exa2fa:myaddinstance' => array(
			'captype' => 'write',
			'contextlevel' => CONTEXT_SYSTEM,
			'archetypes' => array(
					'user' => CAP_PREVENT
			),
			'clonepermissionsfrom' => 'moodle/my:manageblocks'
	),
);
