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
				class Propagator {
					public $Sql, $Param, $Label, $Id;
					function Propagator($sql, $param, $label, $id) {
						$this->Sql = $sql;
						$this->Param = $param;
						$this->Label = $label;
						$this->Id = $id;
					}
				}
			
				if (session_status() == PHP_SESSION_NONE)
					if (isset($_COOKIE["PHPSESSID"]))
						session_start($_COOKIE["PHPSESSID"]);
				include $vars["files"]["dbConnect"];
				include $vars["files"]["functions"];
				if (login_check($mysqli) == true) {
					echo "<span>You are logged in as ".$_SESSION["username"].".  </span>";
					echo "<a href='".$vars["root"].$vars["files"]["logout"]."'>Logout</a> <br />";
					
					// Select Propagator
					$constraints = array();
					$constraints[] = new Propagator("getLocation", $_SESSION["user_id"], "Location: ", "locationIDSelect");
					$constraints[] = new Propagator("getSystem", null, "System: ", "systemIDSelect");
					$constraints[] = new Propagator("getGroups", null, "Groups: ", "groupsIDSelect");
					
					for ($i = 0; $i < count($constraints); $i++) {
						$stmt = $mysqli->prepare($vars["sql"][$constraints[$i]->Sql]);
						$stmt->bind_param("i", $constraints[$i]->Param);
						$stmt->execute();
						$result = $stmt->get_result();
						$stmt->close();
						echo "<span>".$constraints[$i]->Label."</span><select id='".$constraints[$i]->Id."'>";
						while ($row = $result->fetch_array(MYSQLI_NUM)) {
							if (isset($constraints[$i+1]))
								if ($constraints[$i+1]->Param == null)
									$constraints[$i+1]->Param = $row[0];
							echo "<option value='".$row[0]."'>".$row[1]."</option>";
						}
						echo "</select> <br />";
					}
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
										data: { group: $('#pointIDSelect').val() }
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