<?php
require __DIR__.'/../../../config.php';

// disable infinite loop forwarding of the login form
$CFG->alternateloginurl = null;

// require the script to modify the login form
$PAGE->requires->jquery();
$PAGE->requires->js('/auth/a2fa/javascript/login.js', true);

require __DIR__.'/../../../login/index.php';

