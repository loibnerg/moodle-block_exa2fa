$(function(){
	var $field = $('#id_profile_field_a2fasecret');
	var $newBtn = $('#id_newsecret');
	var baseUrl = $('input[name="a2fa_baseurl"]').val();
	var url = $('input[name="a2fa_url"]').val();
	var userinfo = $('input[name="a2fa_userinfo"]').val();

	$newBtn.on('click', function(e){
		$.getJSON(baseUrl + '/auth/a2fa/generate.php', function(res){
			if(res.status = "success"){
				$field.val(res.secret);
				urlparam = 'otpauth://totp/'+userinfo+'?secret='+res.secret;
				
				var oReq = new XMLHttpRequest();
				oReq.open("get", url+"&url="+urlparam, true);
				oReq.send();
				//TODO form submit
				document.getElementById('mform1').submit();
				console.log(document.getElementById('mform1'))
			}
			else{
				alert('There was an error generating a new secret');
			}
		});
	});
});
