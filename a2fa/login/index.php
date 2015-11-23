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

// require 'index.php';
exit;

$PAGE->set_title("$site->fullname: $loginsite");
$PAGE->set_heading("$site->fullname");

$PAGE->requires->jquery();
echo $OUTPUT->header();

if (isloggedin() and !isguestuser()) {
    // prevent logging when already logged in, we do not want them to relogin by accident because sesskey would be changed
    echo $OUTPUT->box_start();
    $logout = new single_button(new moodle_url($CFG->httpswwwroot.'/login/logout.php', array('sesskey'=>sesskey(),'loginpage'=>1)), get_string('logout'), 'post');
    $continue = new single_button(new moodle_url($CFG->httpswwwroot.'/auth/a2fa/login.php?standard='.$standard, array('cancel'=>1)), get_string('cancel'), 'get');
    echo $OUTPUT->confirm(get_string('alreadyloggedin', 'error', fullname($USER)), $logout, $continue);
    echo $OUTPUT->box_end();
} else {
    
?>
<?php
if ($show_instructions) {
	$columns = 'twocolumns';
} else {
	$columns = 'onecolumn';
}

if (!empty($CFG->loginpasswordautocomplete)) {
	$autocomplete = 'autocomplete="off"';
} else {
	$autocomplete = '';
}
if (empty($CFG->authloginviaemail)) {
	$strusername = get_string('username');
} else {
	$strusername = get_string('usernameemail');
}

?>
<script>
$(function(){
	
	function login() {
		var $form = $('form#login');

		// Send the data using post
		return $.post($form.attr('action'), {
			ajax: true,
			username: $form.find('input[name=username]').val(),
			password: $form.find('input[name=password]').val(),
		});
	}
	
	var needsA2FA = null;
	$('form#login').submit(function(event){
		// Stop form from submitting normally
		event.preventDefault();

		login().always(function(){
			console.log('asdf');
			return;
			if (needsA2FA === null) {
				needsA2FA = true;
				$('#username-password').hide();
				$('#a2fa-token-form').show();
			} else {
                window.setTimeout(function(){
                    document.location.href = 'pw_test2.php?username=ok';
                }, 1);
			}
		});
	});
});
</script>

<div class="loginbox clearfix <?php echo $columns ?>">
  <div class="loginpanel">
<?php
  if (($CFG->registerauth == 'email') || !empty($CFG->registerauth)) { ?>
	  <div class="skiplinks"><a class="skip" href="signup.php"><?php print_string("tocreatenewaccount"); ?></a></div>
<?php
  } ?>
	<h2><?php print_string("login") ?></h2>
	  <div class="subcontent loginsub">
		<?php
		  if (!empty($errormsg)) {
			  echo html_writer::start_tag('div', array('class' => 'loginerrors'));
			  echo html_writer::link('#', $errormsg, array('id' => 'loginerrormessage', 'class' => 'accesshide'));
			  echo $OUTPUT->error_text($errormsg);
			  echo html_writer::end_tag('div');
		  }
		?>
		<form action="<?php echo $CFG->httpswwwroot; ?>/auth/a2fa/login.php" method="post" id="login" <?php echo $autocomplete; ?> >
		  <div class="loginform">
			<div id="username-password">
				<div class="form-label"><label for="username"><?php echo($strusername) ?></label></div>
				<div class="form-input">
				  <input type="text" name="username" id="username" size="15" value="<?php p($frm->username) ?>" />
				</div>
				<div class="clearer"><!-- --></div>
				<div class="form-label"><label for="password"><?php print_string("password") ?></label></div>
				<div class="form-input">
				  <input type="password" name="password" id="password" size="15" value="" <?php echo $autocomplete; ?> />
				</div>
			</div>
			<div id="a2fa-token-form" style="display: none;">
				<div class="clearer"><!-- --></div>
				<div class="form-label"><label for="token">Google Authenticator Code</label></div>
				<div class="form-input">
					<input type="text" name="token" id="token" size="15" value="" />
				</div>
			</div>
		 </div>
		  
			<div class="clearer"><!-- --></div>
			  <?php if (isset($CFG->rememberusername) and $CFG->rememberusername == 2) { ?>
			  <div class="rememberpass">
				  <input type="checkbox" name="rememberusername" id="rememberusername" value="1" <?php if ($frm->username) {echo 'checked="checked"';} ?> />
				  <label for="rememberusername"><?php print_string('rememberusername', 'admin') ?></label>
			  </div>
			  <?php } ?>
		  <div class="clearer"><!-- --></div>
		  <input type="submit" id="loginbtn" value="<?php print_string("login") ?>" />
		  <div class="forgetpass"><a href="../../login/forgot_password.php"><?php print_string("forgotten") ?></a></div>
		</form>
		<div class="desc">
			<?php
				echo get_string("cookiesenabled");
				echo $OUTPUT->help_icon('cookiesenabled');
			?>
		</div>
	  </div>

<?php if ($CFG->guestloginbutton and !isguestuser()) {  ?>
	  <div class="subcontent guestsub">
		<div class="desc">
		  <?php print_string("someallowguest") ?>
		</div>
		<form action="index.php?standard=<?php echo $standard;?>" method="post" id="guestlogin">
		  <div class="guestform">
			<input type="hidden" name="username" value="guest" />
			<input type="hidden" name="password" value="guest" />
			<input type="submit" value="<?php print_string("loginguest") ?>" />
		  </div>
		</form>
	  </div>
<?php } ?>
	 </div>
<?php if ($show_instructions) { ?>
	<div class="signuppanel">
	  <h2><?php print_string("firsttime") ?></h2>
	  <div class="subcontent">
<?php	 if (is_enabled_auth('none')) { // instructions override the rest for security reasons
			  print_string("loginstepsnone");
		  } else if ($CFG->registerauth == 'email') {
			  if (!empty($CFG->auth_instructions)) {
				  echo format_text($CFG->auth_instructions);
			  } else {
				  print_string("loginsteps", "", "signup.php");
			  } ?>
				 <div class="signupform">
				   <form action="signup.php" method="get" id="signup">
				   <div><input type="submit" value="<?php print_string("startsignup") ?>" /></div>
				   </form>
				 </div>
<?php	 } else if (!empty($CFG->registerauth)) {
			  echo format_text($CFG->auth_instructions); ?>
			  <div class="signupform">
				<form action="signup.php" method="get" id="signup">
				<div><input type="submit" value="<?php print_string("startsignup") ?>" /></div>
				</form>
			  </div>
<?php	 } else {
			  echo format_text($CFG->auth_instructions);
		  } ?>
	  </div>
	</div>
<?php } ?>
<?php if (!empty($potentialidps)) { ?>
	<div class="subcontent potentialidps">
		<h6><?php print_string('potentialidps', 'auth'); ?></h6>
		<div class="potentialidplist">
<?php foreach ($potentialidps as $idp) {
	echo  '<div class="potentialidp"><a href="' . $idp['url']->out() . '" title="' . $idp['name'] . '">' . $OUTPUT->render($idp['icon'], $idp['name']) . $idp['name'] . '</a></div>';
} ?>
		</div>
	</div>
<?php } ?>
</div>
<?php
    if ($errormsg) {
        $PAGE->requires->js_init_call('M.util.focus_login_error', null, true);
    } else if (!empty($CFG->loginpageautofocus)) {
        //focus username or password
        $PAGE->requires->js_init_call('M.util.focus_login_form', null, true);
    }
}

echo $OUTPUT->footer();

exit;
if (@$_REQUEST['username']) {
    echo "<pre>";
    print_r($_REQUEST);
    die('login ok');
}

echo 'random string: '.time();
?>
<script src="https://code.jquery.com/jquery-1.11.3.min.js"></script>
<script>
$(function(){
    var needsA2FA = null;
    $('form').submit(function(e){
        e.preventDefault();
        window.setTimeout(function(){
        
            if (needsA2FA === null) {
                needsA2FA = true;
                $('#normal').hide();
				$('#a2fa').show();
            } else {
                // $('form').remove();
                // history.replaceState({success:true}, 'title', "/asdf"); 
                // $('body').append('login erfolgreich');
                window.setTimeout(function(){
                    document.location.href = 'pw_test2.php?username=ok';
                }, 1);
            }
        }, 1000);
        return false;
    });
});
</script>
<form method="post" id="form">
<div id="normal">
user: <input type="text" name="username" /><br />
password: <input type="password" name="password" />
</div>
<div id="a2fa" style="display: none;">a2fa: <input type="text" name="a2fa" /></div>
<input type="submit" />
</form>
