<?php
	$location = (isset($_POST['location'])) ? $_POST['location'] : 1;

	include "../../config/variables.php";
	include "../../".$vars["files"]["dbConnect"];

	$stmt = $mysqli->prepare($vars["sql"]["getConrollers"]);
	$stmt->bind_param("i", $location);
	$stmt->execute();
	$result = $stmt->get_result();
	$stmt->close();
	$controllers = array();
	while ($row = $result->fetch_array(MYSQLI_NUM))
		$controllers[] = $row[0];
	$ezeurl = "https://ezecontrol.com/api/status.php";
	$ezepass = "daisy5958";
	
	$points = array();
	for ($i = 0, $length = count($controllers); $i < $length; $i++) {
		$cookiefile = tempnam ("/tmp", "CURLCOOKIE");
		$ch = curl_init($ezeurl);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_DIGEST);
		curl_setopt($ch, CURLOPT_USERPWD, $controllers[$i].":".$ezepass);
		curl_setopt($ch, CURLOPT_FRESH_CONNECT, 1);
		$result = curl_exec($ch); // send the request
		curl_close($ch);

		if(($ctldata = json_decode($result)) === NULL)
			echo($controllers[$i].": Bad json <br />");
		else {
			if($ctldata->status == 'OK') {
				$timestamp = date("Y-m-d H:i:s", strtotime($ctldata->time));
				foreach($ctldata->inputs as $inp)  
					if(preg_match('/#(\d\d\d\d\d\d)/', $inp->name, $id) == 1)
						$points[intval($id[1])] = floatval($inp->real);
			}
		}
		$result = null;
		$ctldata = null;
	}
	
	foreach ($points as $key => $value) {
		if ($value < 0)
			$value = 0;
		$stmt = $mysqli->prepare($vars["sql"]["insertTmpData"]);
		$stmt->bind_param("isi", $key, $timestamp, $value);
		$stmt->execute();
		$stmt->close();
	}
	
	echo json_encode($points);
?>