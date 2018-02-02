// This file is part of Exabis A2fa
//
// (c) 2016 GTN - Global Training Network GmbH <office@gtn-solutions.com>
//
// Exabis A2fa is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// This script is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You can find the GNU General Public License at <http://www.gnu.org/licenses/>.
//
// This copyright notice MUST APPEAR in all copies of the script!

$(function(){
	var $form = $('form#login');
	$form.attr('action', M.cfg.wwwroot+'/blocks/exa2fa/login/');
	
	// add a2fa to form
	$form.wrapInner('<div id="username-password"></div>');
	$form.append(
		'<div id="a2fa-token-form" style="display: none;">' +
		'	<div class="clearer"><!-- --></div>' +
		'	<div class="form-label"><label for="token">Authenticator Einmalpasswort</label></div>' +
		'	<div class="form-input">' +
		'		<input type="text" name="token" id="token" size="15" value="" />' +
		'	</div>' +
		'</div>'
	);

	$('.forgetpass').addClass('original');
	$(
		'<div class="forgetpass a2fa" style="display: none;">' +
		'   <a href="#">A2fa Code vergessen?</a>' +
		'</div>'
	).insertAfter('.forgetpass').click(function(){
		$form.attr('action', M.cfg.wwwroot+'/blocks/exa2fa/send_a2fa.php');
		// submit the form, without triggering the special login handler
		$form[0].submit();
	});


	function login() {
		// Send the data using post
		return $.ajax({
			dataType: "json",
			url: $form.attr('action'),
			dataType: 'json',
			method: 'post',
			data: {
				ajax: true,
				username: $form.find('input[name=username]').val(),
				password: $form.find('input[name=password]').val(),
				token: $form.find('input[name=token]').val(),
				rememberusername: $form.find('input[name=rememberusername]:checked').val(),
			}
		});
	}
	
	function error(errorText) {
		$('.loginerrors').remove();
		$form.before('<div class="loginerrors"><span class="error">'+errorText+'</span></div>');
	}
	
	$form.submit(function(event){
		// Stop form from submitting normally
		event.preventDefault();

		// remove old errors
		$('.loginerrors').remove();
		$('input').attr('disabled', 'disabled');
		login().done(function(data, ret, xhr){
			// for testing
			/*
			window.content = content;
			window.xhr = xhr;
			window.ret = ret;
			return;
			*/
			
			if (data['a2fa-error']) {
				error(data['a2fa-error']);

				// a2fa error
				$('#username-password').hide();
				$('#a2fa-token-form').show();
				$('input').attr('disabled', null);
				$('input[name=token]').focus();

				$('.forgetpass.original').hide();
				$('.forgetpass.a2fa').show();

				$form.find('input[name=token]').val('');
			} else if (data.url) {
				// we got an url -> redirect
				window.setTimeout(function(){
					document.location.href = data.url;
				}, 1);
			} else {
				// could check data.error here, but not needed, because moodle shows error after reload
				location.reload(true);
			}
		}).fail(function(){
			error('Unknown Error');
		});
	});
	
	// show error on page load, if non present
	if (!$('.loginerrors').length && document.location.href.match(/[&?]errorcode=4(&|$)/)) {
		error(M.util.get_string('sessionerroruser', 'error'));
	}
});
