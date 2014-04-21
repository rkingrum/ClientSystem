<?php
	$vars = array(
		"version"		=>	"0.1",
		"siteName"		=>	"HolmesAutoPilot",
		"root"			=>	"/clientSystem"
	);
	$vars["files"] = array(
		"index"			=>	"/index.php",
		"createAccount"	=>	"/scripts/php/createAccount.php",
		"functions"		=>	"/scripts/php/functions.php",
		"login"			=>	"/scripts/php/login.php",
		"logout"		=>	"/scripts/php/logout.php",
		"getCurrentData"=>	"/scripts/php/getCurrentData.php",
		"dbConnect"		=>	"/config/db_connect.php",
		"head"			=>	"/head.php",
		"jQuery"		=>	"/scripts/javascript/jquery-2.1.0.min.js",
		"formJs"		=>	"/scripts/javascript/forms.js",
		"realtime"		=>	"/realtime/realtime.php",
		"excanvas"		=>	"/jqPlot/excanvas.js",
		"jqPlot"		=>	"/jqPlot/jquery.jqplot.min.js",
		"jqDate"		=>	"/jqPlot/plugins/jqplot.dateAxisRenderer.min.js",
		"jqHigh"		=>	"/jqPlot/plugins/jqplot.highlighter.min.js",
		"jqCursor"		=>	"/jqPlot//plugins/jqplot.cursor.min.js",
		"jqPlotCSS"		=>	"/jqPlot/jquery.jqplot.css"
	);
	$vars["sql"] = array(
		"lastUpdate"		=>	"SELECT DateTime 
								FROM update_tracker 
								ORDER BY DateTime DESC 
								LIMIT 1",
		"updateUpdate"		=>	"INSERT INTO `update_tracker`(`DateTime`) 
								VALUES (UTC_TIMESTAMP())",
		"getRealtimeData"	=>	"SELECT A.PointID, 
									A.DateTime, 
									A.Value, 
									B.Name, 
									B.Desc, 
									B.RealTimeUnits 
								FROM raw_data A 
								LEFT JOIN points B 
									ON A.pointID = B.pointID 
								WHERE B.groupID = ?
									AND A.DateTime > ?
									AND A.DateTime < ?
								ORDER BY A.DateTime ASC",
		"getTempData"		=>	"SELECT A.PointID, 
									A.DateTime, 
									A.Value, 
									B.Name, 
									B.Desc, 
									B.RealTimeUnits 
								FROM tmp_data A 
								LEFT JOIN points B 
									ON A.pointID = B.pointID 
								WHERE B.groupID = ?
									AND A.DateTime > ?
									AND A.DateTime < ?
								ORDER BY A.DateTime ASC",
		"getPoints"			=> 	"SELECT DISTINCT PointID 
								FROM raw_data", //Deprecated
		"getLocation"		=>	"SELECT LocationID, 
									Name 
								FROM location 
								WHERE ClientID = ?",
		"getSystem"			=>	"SELECT SystemID, 
									Name 
								FROM system 
								WHERE LocationID = ?",
		"getGroups"			=>	"SELECT GroupID, 
									Name 
								FROM groups 
								WHERE SystemID = ?",
		"getConrollers"		=>	"SELECT ControllerID
								FROM controllers
								WHERE LocationID = ?",
		"insertTmpData"		=>	"INSERT INTO tmp_data
									(pointID, DateTime, Value)
								VALUES (?, ?, ?)"
	);
	$vars["scripts"] = array(
		"jqPlot"		=>	'<!--[if lt IE 9]><script language="javascript" type="text/javascript" src="'.$vars["root"].$vars["files"]["excanvas"].'"></script><![endif]-->
								<script type="text/javascript" src="'.$vars["root"].$vars["files"]["jqPlot"].'"></script>
								<script type="text/javascript" src="'.$vars["root"].$vars["files"]["jqDate"].'"></script>
								<script type="text/javascript" src="'.$vars["root"].$vars["files"]["jqHigh"].'"></script>
								<script type="text/javascript" src="'.$vars["root"].$vars["files"]["jqCursor"].'"></script>	
								<link rel="stylesheet" type="text/css" href="'.$vars["root"].$vars["files"]["jqPlotCSS"].'" />'
	);
?>