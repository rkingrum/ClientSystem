<?php
	$group = (isset($_GET['group'])) ? $_GET['group'] : 1;
	$start = (isset($_GET['start'])) ? $_GET['start'] : 1;
	$end = (isset($_GET['end'])) ? $_GET['end'] : 1;
	
	include "../config/variables.php";
	include "../".$vars["files"]["dbConnect"];
	$container = array();
	$dataSwitch = [ 0 => $vars["sql"]["getRealtimeData"], 1 => $vars["sql"]["getTempData"] ];
	foreach ($dataSwitch as $conn) {
		$stmt = $mysqli->prepare($conn);
		$stmt->bind_param("iss", $group, $start, $end);
		$stmt->execute();
		$result = $stmt->get_result();
		$stmt->close();
		while ($row = $result->fetch_array(MYSQLI_NUM))
			$container[$row[0]][] = [
				"time"		=>	$row[1],
				"value"		=>	$row[2],
				"name"		=>	$row[3],
				"desc"		=>	$row[4],
				"units"		=>	$row[5]
			];
	}
	$chartData = "[";
	foreach ($container as $id => $point) {
		$chartData .= "[";
		foreach ($point as $data) {
			$chartData .= "['".date("Y-n-d g:iA", strtotime($data["time"]))."',".$data["value"]."],";
		}
		$chartData = substr($chartData, 0, -1);
		$chartData .= "],";
	}
	$chartData = substr($chartData, 0, -1);
	$chartData .= "]";
?>

<div id="realtimeChart" style="height:500px; width:1000px;" >
</div>
<input id="realtime-reset" type="button" value="Reset Zoom"></input>
<script>
	$(document).ready(function() {
		var realtime = $.jqplot('realtimeChart', <?php echo $chartData; ?>, {
			title: 'Realtime Graph',
			series:[
				<?php
					$i = 1;
					foreach ($container as $point) {
						if ($i != 1)
							echo "{ label: '".$point[0]["name"]."', showMarker:false, yaxis: 'y".$i."axis' },\n";
						else
							echo "{ label: '".$point[0]["name"]."', showMarker:false, yaxis: 'yaxis' },\n";
						$i++;
					}
				?>
			],
			axes:{ 
				xaxis:{ 
					renderer:$.jqplot.DateAxisRenderer,
					min:'<?php echo date("Y-n-d g:iA", strtotime($start)); ?>',
					max:'<?php echo date("Y-n-d g:iA", strtotime($end)); ?>'
				},
				<?php
					$i = 1;
					foreach ($container as $point) {
						if ($i != 1)
							echo "y".$i."axis:";
						else
							echo "yaxis:";
						echo "{ autoscale:true, useSeriesColor: true, rendererOptions: { alignTicks: true } },";
						$i++;
					}
				?>
			},
			highlighter:{
				show: true,
				sizeAdjust: 7.5
			},
			cursor:{
				show: true,
				zoom: true,
				tooltipLocation: 'sw'
			},
			legend:{
				show: true,
				placement: 'outsideGrid'
			},
			seriesDefaults: {
				rendererOptions: {
					smooth: false,
					animation: {
						show: true,
						speed: 500
					}
				}
			}
		});
		
		$('#realtime-reset').click(function() {realtime.resetZoom()});
		<?php
			$chartData = "<table><tr><td>Name</td><td>Value</td><td>Units</td></tr>";
			foreach ($container as $point) {
				$i = count($point) - 1;
				$chartData .= "<tr><td>".$point[$i]["name"]."</td><td>".$point[$i]["value"]."</td><td>".$point[$i]["units"]."</td></tr>";
			}
			$chartData .= "</table>"; 
			echo "refreshData('".$chartData."');";
		?>
	});
</script>