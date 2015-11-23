$(function(){
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
	$form.attr('action', M.cfg.wwwroot+'/auth/a2fa/login/');
	
	function login() {
		// Send the data using post
		return $.post($form.attr('action'), {
			ajax: true,
			username: $form.find('input[name=username]').val(),
			password: $form.find('input[name=password]').val(),
			token: $form.find('input[name=token]').val(),
			rememberusername: $form.find('input[name=rememberusername]:checked').val(),
		});
	}
	
	$('form#login').submit(function(event){
		// Stop form from submitting normally
		event.preventDefault();

		// remove old errors
		$('.loginerrors').remove();
		
		login().always(function(content, ret, xhr){
			var $content = content[0] == '<' ? $(content) : $('');
			var $error = $content.find('.loginerrors');
			var matches;
			
			
			if ($error.length) {
				$error.insertBefore($form);

				$('#username-password').show();
				$('#a2fa-token-form').hide();

				$form.find('input[name=password]').val('');
				$form.find('input[name=token]').val('');
			} else if (matches = content.match(/a2fa-required(:\s*([^\s].*))?/)) {
				var errorText = matches[2];

				// a2fa error
				$('#username-password').hide();
				$('#a2fa-token-form').show();

				$form.find('input[name=token]').val('');
				
				$form.before('<div class="loginerrors"><span class="error">'+errorText+'</span></div>');
			} else {
				// success
				window.setTimeout(function(){
					document.location.href = M.cfg.wwwroot;
				}, 1);
			}
			
		});
	});
});
