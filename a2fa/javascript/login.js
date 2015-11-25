$(function(){
	// add a2fa to form
	$('.loginform').wrapInner('<div id="username-password"></div>');
	$('.loginform').append(
		'<div id="a2fa-token-form" style="display: none;">' +
		'	<div class="clearer"><!-- --></div>' +
		'	<div class="form-label"><label for="token">Google Authenticator Code</label></div>' +
		'	<div class="form-input">' +
		'		<input type="text" name="token" id="token" size="15" value="" />' +
		'	</div>' +
		'</div>'
	);

	var $form = $('form#login');
	$form.attr('action', M.cfg.wwwroot+'/blocks/exa2fa/login/');
	
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
});
