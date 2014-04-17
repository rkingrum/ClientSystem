<?php
	if (isset($_GET['id']))
		$id = $_GET['id'];
	else
		$id = 6601;
		
	include "../config/variables.php";
	include "../".$vars["files"]["dbConnect"];
	$stmt = $mysqli->prepare($vars["sql"]["getRealtimeData"]);
	$stmt->bind_param("i", $id);
	$stmt->execute();
	$result = $stmt->get_result();
	$stmt->close();
	$chartData = "[[";
	while ($row = $result->fetch_array(MYSQLI_NUM))
		$chartData .= "['".date("Y-n-d g:iA", strtotime($row[1]))."',".$row[2]."],";
	$chartData = substr($chartData, 0, -1);
	$chartData .= "]]";
?>

<div id="realtimeChart" style="height:500px; width:700px;" >
</div>
<input id="realtime-reset" type="button" value="Reset Zoom"></input>
<script>
	$(document).ready(function() {
		var realtime = $.jqplot('realtimeChart', <?php echo $chartData; ?>, {
			title: 'Realtime Graph',
			series:[{showMarker:false}],
			axes:{ xaxis:{ renderer:$.jqplot.DateAxisRenderer } },
			highlighter:{
				show: true,
				sizeAdjust: 7.5
			},
			cursor:{
				show: true,
				zoom: true,
				tooltipLocation: 'sw'
			}
		});
		
		$('#realtime-reset').click(function() {realtime.resetZoom()});
	});
</script>