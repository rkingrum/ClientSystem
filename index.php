<?php 
	include "config/variables.php"; 
?>

<!DOCTYPE HTML>
<html>
	<head>
		<title>Holmes AutoPilot</title>
		<?php include $vars["files"]["head"]; ?>
	</head>
	<body>
		<?php
			if (session_status() == PHP_SESSION_NONE)
				if (isset($_COOKIE["PHPSESSID"]))
					session_start($_COOKIE["PHPSESSID"]);
			include $vars["files"]["dbConnect"];
			include $vars["files"]["functions"];
			if (login_check($mysqli) == true) {
				echo "<span>You are logged in as ".$_SESSION["username"].".</span> <br />";
				echo "<a href='".$vars["root"].$vars["files"]["logout"]."'>Logout</a>";
			}
			else { ?>
				<form id="loginForm" name="login" >
					<span>Login</span>
					<br />
					<span id="loginFeedback"></span>
					<br />
					<span>Username: </span>
					<input type="text" name="username" />
					<br />
					<span>Password: </span>
					<input type="password" name="password" />
					<br />
					<input type="button" value="login" />
				</form>
				<br />
				<br />
				<form id="createForm" name="createAccount" >
					<span>Create Account</span>
					<br />
					<span id="createFeedback"></span>
					<br />
					<span>Username: </span>
					<input type="text" name="username" />
					<br />
					<span>Email: </span>
					<input type="text" name="email" />
					<br />
					<span>Password: </span>
					<input type="password" name="password" />
					<br />
					<span>Confirm Password: </span>
					<input type="password" name="passwordConfirm" />
					<br />
					<span>Authorization Code: </span>
					<input type="text" name="authCode" />
					<br />
					<input type="button" value="Create Account" />
				</form>
			<?php } ?>
	</body>
</html>