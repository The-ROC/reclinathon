
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

foreach($seasons as $SEASON) {
//$SEASON = "Winter 2008";

    $query = "SELECT * FROM RECLINATHON_CONTEXT WHERE Season = '" . $SEASON . "' ORDER BY TimeStamp ASC";
    $result = $rcx->query($query);
    if (!$result)
    {
        echo "ERROR GETTING CONTEXT LIST!";
        exit();
    }
	

    //echo "Printing Rows:";
    	$rows = array();
	$endTimes = array();
    	while($row = mysql_fetch_assoc($result)) {
		$rows[] = $row;
		$endTimes[] = $row['TimeStamp'] + $row['EstimatedDuration'];
	}
	
	//array_multisort($endTimes, $rows);
	
	$count = 0;
	foreach($rows as $row) {

		$shouldPrint = false;
		if($row['StateID'] == 1) {
			//echo "MOVIE<br />";
			$shouldPrint = true;
			//$recliningTime += $row['EstimatedDuration'];
			$recliningTime += $rows[$count+1]['TimeStamp'] - $row['TimeStamp'];
		}
		else if($row['StateID'] == 2 || $row['StateID'] == 5) {
			//echo "DOWNTIME<br />";
			$shouldPrint = true;
			//$downTime += $row['EstimatedDuration'];	
			$downTime += $rows[$count+1]['TimeStamp'] - $row['TimeStamp'];
		}

		if($recliningTime > 0 && $shouldPrint) {
			$recliningRatio = $recliningTime / ($recliningTime + $downTime);
			//echo "ContextID: " . $row['ContextID'] . "<br />Reclining Ratio: ";
			echo $recliningRatio . "<br />";
		}
/*
		if($shouldPrint) {
			if($startTime == 0) {
				$startTime = $row['TimeStamp'];
				$currentSeason = $SEASON;
			} else if($SEASON != $currentSeason) {
				$startTime = $row['TimeStamp'] - $deltaTime;
				$currentSeason = $SEASON;
			}

			//$deltaTime = ($rows[$count+1]['TimeStamp'] - $startTime) / 60 / 60;
			$deltaTime += ($rows[$count+1]['TimeStamp'] - $row['TimeStamp']) / 60 / 60;
			echo $deltaTime . "<br />";
		}
*/

		$count++;
	}
}
