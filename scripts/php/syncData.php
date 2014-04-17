<?php
	header("Cache-Control: no-cache, must-revalidate");
	include "../../config/variables.php";
	$vars["rootPath"] = "../../";
	
	/* Define Database variables. */
	include $vars["rootPath"].$vars["files"]["dbConnect"];
	
	/* Test connection to database. */
	if($mysqli->connect_errno) {
		printf ("Connect failed: %s\n", $mysqli->connect_error);
		exit();
	}
	
	/* Define eZe variables. */
	$EZEURL = "https://ezecontrol.com/api/log.php"; // API URL
	$CTLPASS = "daisy5958";	// Read passcode
	$calcBase = array();
	$CTLSERIAL = array(
		0 => "AAC258",	//Pepsi
		1 => "AAC255",	//Axcelis
		2 => "AAC257",
		3 => "AAC270",
		4 => "AAC351",
		5 => "AAC355",
		6 => "AAC373",
		7 => "AAC389",
		8 => "AAC400",
		9 => "AAC494",	//Acumen
		10 => "AAB421"	//Demo
	);
	
	if ($result = $mysqli->query($vars["sql"]["lastUpdate"]))
		$lastUpdate = $result->fetch_row()[0];
	else {
		printf ("Query failed: %s\n", $mysqli->error);
		exit();
	}

	$lastUpdate = strtotime($lastUpdate." UTC");
	$lastUpdate = DateTime::createFromFormat('U', $lastUpdate);
	$currentTime = DateTime::createFromFormat('U', time(), new DateTimeZone('UTC'));
	if ($lastUpdate->add(new DateInterval('PT5S')) < $currentTime) {
		printf("TRUE");
		if (!$result = $mysqli->query($vars["sql"]["updateUpdate"])) {
			printf ("Query failed: %s\n", $mysqli->error);
			exit();
		}
	}
	else
		exit();
	
	$container = array();
	for ($i = 0, $length = count($CTLSERIAL); $i < $length; $i++) {
		for ($j = 1; $j <= 40; $j++) {
			set_time_limit(20);
			$step = clone $lastUpdate;
			$step->add(new DateInterval('P1D'));
			while ($step < $currentTime) {
				$container[] = getData($lastUpdate->format('Y-m-d\TH:i:s'), $step->format('Y-m-d\TH:i:s'), $j, $CTLSERIAL[$i], $CTLPASS, $EZEURL);
				$lastUpdate = $step;
				$step = $lastUpdate->add(new DateInterval('P1D'));
			}
			$container[] = getData($lastUpdate->format('Y-m-d\TH:i:s'), $currentTime->format('Y-m-d\TH:i:s'), $j, $CTLSERIAL[$i], $CTLPASS, $EZEURL);
		}
	}
	
	foreach ($container as $value) {
		if (!is_string($value))
			foreach ($value as $key => $input)
				foreach ($input as $date) {
					set_time_limit(20);
					$temp = str_replace("#", '', $key);
					printf("<span>".$key."</span>");
					$stmt = $mysqli->prepare("INSERT INTO raw_data (PointID, DateTime, Value) VALUES (?, ?, ?)");
					$stmt->bind_param("isi", $temp, $date["time"], $date["value"]);
					if($stmt->execute())
						printf("<span>Success</span><br />");
					else
						printf("<span>Failed</span><br />");
					$stmt->close();
				}
	}
		
	function getData($start, $end, $input, $serial, $pass, $url) {
		printf("<span>Getting data for ".$serial.":".$input.".</span><br />");
		$control = array();
		$curl = curl_init($url."?input=".$input."&from=".$start."&to=".$end);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_DIGEST);
		curl_setopt($curl, CURLOPT_USERPWD, $serial.":".$pass);
		curl_setopt($curl, CURLOPT_FRESH_CONNECT, 1);
		$result = curl_exec($curl);
		if (curl_errno($curl)) {
			printf("Curl error: " . curl_error($curl));
			exit();
		}	
		curl_close($curl);
		$result = json_decode($result);
		if ($result->status == "OK") {
			$id = $result->source->inputname;
			for ($i = 0; $i < count($result->data); $i++) {
				$control[$id][$i] = array();
				$control[$id][$i]["time"] = $result->data[$i]->time;
				$control[$id][$i]["value"] = $result->data[$i]->value;
			}
			return $control;
		}
		else
			return "Controller Inactive";
	}
	
	/*
		// Decode the json into an object, and make sure it's ok.
		if(($ctldata = json_decode($result)) === NULL)
			echo($CTLSERIAL[$i].": Bad json <br />");
		else {
			// Make sure the data was returned ok.
			if($ctldata->status != 'OK')
				echo($CTLSERIAL[$i].": ".$ctldata->status."<br />");
			else {
				$timestamp = date("Y-m-d H:i:s", strtotime($ctldata->time));
				
				// Go through all the inputs
				foreach($ctldata->inputs as $inp)  
					// Look for a PointID tag in the name (#nnn)
					if(preg_match('/#(\d\d\d\d\d\d)/', $inp->name, $id) == 1)
						$calcBase[intval($id[1])] = floatval($inp->real);
				echo($CTLSERIAL[$i].": Success <br />");
			}
		}
		$result = null;
		$ctldata = null;
	} */
?>