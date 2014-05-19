<?php 
	include "config/variables.php"; 
	
	class Propagator {
		public $Sql, $Param, $Label, $Id;
		function Propagator($sql, $param, $label, $id) {
			$this->Sql = $sql;
			$this->Param = $param;
			$this->Label = $label;
			$this->Id = $id;
		}
	}

	if (!isset($_SESSION) || $_SESSION['is_open'] == FALSE)
		if (isset($_COOKIE["PHPSESSID"]))
			session_start($_COOKIE["PHPSESSID"]);
	include $vars["files"]["dbConnect"];
	include $vars["files"]["functions"];
	
	if (login_check($mysqli) == true)
		$login = true;
	else
		$login = false;
		
	$constraints = array();
	$constraints[] = new Propagator("getLocation", $_SESSION["user_id"], "Location: ", "locationIDSelect");
	$constraints[] = new Propagator("getSystem", null, "System: ", "systemIDSelect");
	$constraints[] = new Propagator("getGroups", null, "Groups: ", "groupsIDSelect");
	
	$realtimeString = '<table id="dataTable"><tr>';	
	for ($i = 0; $i < count($constraints); $i++) {
		$stmt = $mysqli->prepare($vars["sql"][$constraints[$i]->Sql]);
		$stmt->bind_param("i", $constraints[$i]->Param);
		$stmt->execute();
		$stmt->bind_result($id, $name);
		$result = array();
		while ($stmt->fetch())
			$result[] = array($id, $name);
		$stmt->close();
		$realtimeString .= "<span>".$constraints[$i]->Label."</span><select id='".$constraints[$i]->Id."'>";
		foreach ($result as $row) {
			if (isset($constraints[$i+1]))
				$constraints[$i+1]->Param = $row[0];
			$realtimeString .= "<option value='".$row[0]."' selected='selected'>".$row[1]."</option>";
		}
		$realtimeString .= "</select></td>";
	}
	$date = date('Y-m-d\TH:i:s');
	$realtimeString .= "<td>";
	$realtimeString .= "<span>Starting Date: </span><input id='dateStart' type='datetime-local' value='".date('Y-m-d\TH:i:s', strtotime('-1 day', strtotime($date)))."' /> <br />";
	$realtimeString .= "<span>Ending Date: </span><input id='dateEnd' type='datetime-local' value='".$date."' /> <br />";
	$realtimeString .= "</td><td>";
	$realtimeString .= "<input type='button' id='refreshRealtimeButton' value='Refresh'></input><br />";
	$realtimeString .= '<input id="realtime-reset" type="button" value="Reset Zoom"></input>';
	$realtimeString .= "</td></tr></table>";
?>

<!DOCTYPE HTML>
<html>
	<head>
		<title>Holmes AutoPilot</title>
		<?php include $vars["files"]["head"]; ?>
		<?php echo $vars["scripts"]["jqPlot"]; ?>
		<script>
			function refreshRealtime() {
				$.ajax({
					type: "GET",
					url: "<?php echo $vars["root"].$vars["files"]["realtime"] ?>",
					data: {
						group: $('#groupsIDSelect').val(),
						start: $('#dateStart').val(),
						end: $('#dateEnd').val()
					}
				}).done( function(data) {
					console.log(data);
					$('#realtimeContainer').html(data);
				});
			}
			function getInstant() {
				
				$.ajax({
					type: "POST",
					url: "<?php echo $vars["root"].$vars["files"]["getCurrentData"] ?>",
					data: { group: $('#pointIDSelect').val() },
					dataType: 'json',
					cache: false,
					success: function(result) {
						refreshRealtime();
					}
				})
			}
			function refreshData(data) {
				$('#dataContainer').html(data);
			}

			$(document).ready( function() {
				$('#refreshRealtimeButton').click(refreshRealtime);
				refreshRealtime();
				
				window.setInterval(function(){
					getInstant();
				}, 10000);

				$("#systemIDSelect").on('change', function() {
					$.ajax({
						type: "POST",
						url: "<?php echo $vars["root"].$vars["files"]["getSelectGroup"] ?>",
						data: { system: $('#systemIDSelect').val() },
						dataType: 'json',
						cache: false,
						success: function(result) {
							$("#groupsIDSelect").empty();
							$.each(result, function(key, value) {
								$("#groupsIDSelect").append($("<option selected='selected'></option>")
									.attr("value", key).text(value));
							});
						}
					});				
				});
			});
		</script>
	</head>
	<body>
		<div id="header">
			<div>
				<a href="http//:www.holmesautopilot.com"><img src="<?php echo  $vars["root"].$vars["files"]["logo"]; ?>" /></a>
				<div>
					<?php
						if ($login == true)
							echo "<span>You are logged in as ".$_SESSION["username"].".  <a href='".$vars["root"].$vars["files"]["logout"]."'>Logout</a></span>";
						else
							echo "<span>You must be logged in to view this page.</span>";
					?>
				</div>
			</div>
		</div>
		<div id="body">
			<div id='realtimeContainer'></div>
			<div id='realtimeOptions'>
				<?php echo $realtimeString; ?>
			</div>
			<div id='dataContainer'></div>
		</div>
		<div id="footer">
			<span>
				<a href="http://www.holmesautopilot.com">HOME</a> | Copyright 2014 &copy; Holmes AutoPilot LLC | <a href="http://www.holmesautopilot.com/meet-the-creator">MEET THE CREATOR</a>
			</span>
		</div>
	</body>
</html>