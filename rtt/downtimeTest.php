<?php

include "RECLINATHON_CONTEXT.php";

//$currenttime = round(1000 * (microtime(true) + time()));
//$duration = $START + $TIMER - $currenttime;

?>
<HTML>
<HEAD>
<title>Reclining Ratio Test</title>
<link rel="stylesheet" type="text/css" href="rtt.css" />

<?php
$rcx = new RECLINATHON_CONTEXT();
$seasons = array("Winter 2008", "Winter 2009", "Winter 2010", "Winter2011");


	$recliningRatio = 0;
	$recliningTime = 0;
	$downTime = 0;
	$startTime = 0;
	$currentSeason = null;
	$deltaTime = 0;

$SEASON = "Winter2011";

    $query = $rcx->GetConnection()->prepare(
		"SELECT * FROM RECLINATHON_CONTEXT WHERE Season = ? ORDER BY TimeStamp ASC"
	);
	$query->bind_param('s', $SEASON);
    $result = $rcx->query($query);
    if (!$result)
    {
        echo "ERROR GETTING CONTEXT LIST!";
        exit();
    }
	

    	$rows = array();
    	while($row = $result->fetch_assoc()) {
		$rows[] = $row;
	}
	
	$count = 0;
	foreach($rows as $row) {
		$currentDowntime = 0;
		if($row['StateID'] == 2) {
			$currentDowntime = $rows[$count+1]['TimeStamp'] - $row['TimeStamp'];
			echo ($currentDowntime / 60) . "<br />";
		}

		$count++;
	}

	echo "<br />";

	$count = 0;
	foreach($rows as $row) {
		if($row['StateID'] == 1 && $startTime == 0) {
			$startTime = $row['TimeStamp'];
		}

		if($row['StateID'] == 2) {
			$elapsedTime = $row['TimeStamp'] - $startTime;
			echo ($elapsedTime / 60 / 60) . "<br />";
		}

		$count++;
	}
