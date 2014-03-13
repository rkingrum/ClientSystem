$(document).ready( function() {
	$('#loginForm input[type="button"]').click(function() {
		$.ajax({
			type: "GET",
			url: "scripts/php/login.php",
			data: { 
				username: $('#loginForm > input[name="username"]').val(), 
				password: $('#loginForm > input[name="password"]').val()}
		}).done( function(data) {
			$('#loginFeedback').html(data);
			
			if (data == "Login Successful.")
				window.location.replace("view.php");
		});
		return false;
	});

	$('#createForm input[type="button"]').click(function() {
		$.ajax({
			type: "GET",
			url: "scripts/php/createAccount.php",
			data: {
				username: $('#createForm > input[name="username"]').val(),
				email: $('#createForm > input[name="email"]').val(),
				password: $('#createForm > input[name="password"]').val(),
				passwordConfirm: $('#createForm > input[name="passwordConfirm"]').val(),
				authCode: $('#createForm > input[name="authCode"]').val()}
		}).done( function(data) {
			$('#createFeedback').html(data);
		});
		return false;
	});
});