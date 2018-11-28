<?php

header("Cache-Control: no-cache, must-revalidate");
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Content-Type: text/xml");

include '../RECLINATHON_CONTEXT.php';

$sourceUrl = strtolower($_GET["sourceUrl"]);

$remoteReclinathon = new REMOTE_RECLINATHON();
$currentReclinathonId = $remoteReclinathon->GetCurrentRemoteReclinathonId();
$remoteReclinathonScheduled = $currentReclinathonId != "";
$rcx = new RECLINATHON_CONTEXT();
$contextFound = false;

if ($remoteReclinathonScheduled)
{
    $contextFound = $rcx->LoadCurrentNonPending($currentReclinathonId);
}

if (strpos($sourceUrl, "hugh") !== false && strpos($sourceUrl, "grant") !== false)
{
	echo "<next url='https://reclinathon.com' time='5000' sidebar='' />";
}
else if ($contextFound && strpos($sourceUrl, "/rtt/mockup/feed.php") !== false)
{
	if ($rcx->GetRecliningState() == "Downtime")
	{
	    echo "<next url='https://hangouts.google.com/call/styow2upujcp3hvhninl64l5uee' time='0' sidebar='' />";
	}
	else if ($rcx->GetRecliningState() == "Reclining")
	{
		$runTime = $rcx->GetMovie()->GetRunTime() * 60;
		$timeRemaining = $rcx->GetTimeRemaining();
		$timeCode = $runTime - $timeRemaining;
		$url = $rcx->GetMovie()->GetUrl() . "?t=$timeCode";
	    echo "<next url='$url' time='0' sidebar='' />";
	}
}
else if ($contextFound && strpos($sourceUrl, $rcx->GetMovie()->GetUrl()) !== false)
{
	$timeRemaining = $rcx->GetTimeRemaining() * 1000;
	echo "<next url='https://hangouts.google.com/call/styow2upujcp3hvhninl64l5uee' time='$timeRemaining' sidebar='' />";
}
else if ($contextFound && strpos($sourceUrl, "hangouts.google.com/call/styow2upujcp3hvhninl64l5uee") !== false)
{
	$timeRemaining = $rcx->GetTimeRemaining() * 1000;
	$url = $rcx->GetMovie()->GetUrl() . "?t=1";
	$sidebarUrl = ($rcx->IsDevMode() ? "http://localhost/rtt/mockup/feed.php" : "https://reclinathon.com/rtt/mockup/feed.php");
	echo "<next url='$url' time='$timeRemaining' sidebar='$sidebarUrl' />";
}
else
{
	echo "<next url='' time='0' sidebar='' />";
}

?>