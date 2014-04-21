<?php
	$location = (isset($_GET['location'])) ? $_GET['location'] : 1;

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

	for ($i = 0, $length = count($controllers); $i < $length; $i++) {
		$cookiefile = tempnam ("/tmp", "CURLCOOKIE");
		$ch = curl_init($ezeurl);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_DIGEST);
		curl_setopt($ch, CURLOPT_USERPWD, $controllers[$i].":".$ezepass);
		curl_setopt($ch, CURLOPT_FRESH_CONNECT, 1);
		$result = curl_exec($ch); // send the request
		curl_close($ch);

		// Decode the json into an object, and make sure it's ok.
		if(($ctldata = json_decode($result)) === NULL)
			echo($controllers[$i].": Bad json <br />");
		else {
			// Make sure the data was returned ok.
			if($ctldata->status != 'OK')
				echo($controllers[$i].": ".$ctldata->status."<br />");
			else {
				$timestamp = date("Y-m-d H:i:s", strtotime($ctldata->time));
				
				// Go through all the inputs
				foreach($ctldata->inputs as $inp)  
					// Look for a PointID tag in the name (#nnn)
					if(preg_match('/#(\d\d\d\d\d\d)/', $inp->name, $id) == 1)
						$calcBase[intval($id[1])] = floatval($inp->real);
				echo($controllers[$i].": Success <br />");
			}
		}
		$result = null;
		$ctldata = null;
	}
	
	foreach ($calcBase as $key => $value) {
		if ($value < 0)
			$value = 0;
		$stmt = $mysqli->prepare($vars["sql"]["insertTmpData"]);
		$stmt->bind_param("isi", $key, $timestamp, $value);
		$stmt->execute();
		$stmt->close();
		echo ($key." Complete. <br />");
	}
?>