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
			else {
				?>
					<span>You must be logged in to view this page.</span>
				<?php
			}
		?>
	</body>
</html>