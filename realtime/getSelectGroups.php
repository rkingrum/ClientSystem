<?php
	include "../config/variables.php";
	include "../".$vars["files"]["dbConnect"];
	
	$system = (isset($_POST['system'])) ? $_POST['system'] : 1;
	
	$stmt = $mysqli->prepare($vars["sql"]["getGroups"]);
	$stmt->bind_param("i", $system);
	$stmt->execute();
	$result = $stmt->get_result();
	$stmt->close();
	
	$container = array();
	while ($row = $result->fetch_array(MYSQLI_NUM))
		$container[$row[0]] = $row[1];
		
	echo json_encode($container);
?>