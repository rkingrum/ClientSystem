<?php 
	include "config/variables.php"; 
?>

<!DOCTYPE HTML>
<html>
	<head>
		<title>Holmes AutoPilot</title>
		<?php include $vars["files"]["head"]; ?>
		<?php echo $vars["scripts"]["jqPlot"]; ?>
	</head>
	<body>
		<div>
			<?php
				if (session_status() == PHP_SESSION_NONE)
					if (isset($_COOKIE["PHPSESSID"]))
						session_start($_COOKIE["PHPSESSID"]);
				include $vars["files"]["dbConnect"];
				include $vars["files"]["functions"];
				if (login_check($mysqli) == true) {
					echo "<span>You are logged in as ".$_SESSION["username"].".</span> <br />";
					echo "<a href='".$vars["root"].$vars["files"]["logout"]."'>Logout</a>";
					$stmt = $mysqli->prepare($vars["sql"]["getPoints"]);
					$stmt->execute();
					$result = $stmt->get_result();
					$stmt->close();
					echo "<select id='pointIDSelect'>";
					while ($row = $result->fetch_array(MYSQLI_NUM))
						echo "<option value='".$row[0]."'>".$row[0]."</option>";
					echo "</select>";
					echo "<input id='pointIDButton' type='button' value='Refresh Graph'></input><br />"; 
						?>
							<script>
								$(document).ready(function(){
									$('#pointIDButton').click(refreshRealtime);
									refreshRealtime();
								});
								function refreshRealtime() {
									$.ajax({
										type: "GET",
										url: "<?php echo $vars["root"].$vars["files"]["realtime"] ?>",
										data: { id: $('#pointIDSelect').val() }
									}).done( function(data) {
										$('#realtimeContainer').html(data);
									});
								}
							</script>
						<?php
					echo "<div id='realtimeContainer'></div>";
				}
				else {
					?>
						<span>You must be logged in to view this page.</span>
					<?php
				}
				
			?>
		</div>
	</body>
</html>