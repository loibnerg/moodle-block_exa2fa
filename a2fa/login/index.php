<?php
require __DIR__.'/../../../config.php';

$CFG->alternateloginurl = null;

$PAGE->requires->jquery();
$PAGE->requires->js('/auth/a2fa/javascript/login.js', true);

/*
ob_start(function($output){
	$output = preg_replace('![^"\']/login/index.php+!', 'asdf');
	return $output;
});
*/

require __DIR__.'/../../../login/index.php';
