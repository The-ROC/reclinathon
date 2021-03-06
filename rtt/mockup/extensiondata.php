<?php

header("Cache-Control: no-cache, must-revalidate");
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Content-Type: text/xml");

include '../RECLINATHON_CONTEXT.php';

$sourceUrl = strtolower($_GET["sourceUrl"]);
$baseUrl = $_GET["baseUrl"];

$remoteReclinathon = new REMOTE_RECLINATHON();
$currentReclinathonId = $remoteReclinathon->GetCurrentRemoteReclinathonId();
$remoteReclinathonScheduled = $currentReclinathonId != "";
$rcx = new RECLINATHON_CONTEXT();
$contextFound = false;
$feedSidebarUrl = $baseUrl . "/rtt/mockup/feed.php?joined=1";

if ($remoteReclinathonScheduled)
{
    $contextFound = $rcx->LoadCurrentNonPending($currentReclinathonId);
}

if (strpos($sourceUrl, "hugh") !== false && strpos($sourceUrl, "grant") !== false)
{
	echo "<next url='https://reclinathon.com' time='5000' sidebar='' />";
}
else if ($contextFound && strpos(strtolower($sourceUrl), strtolower($rcx->GetMovie()->GetUrl())) !== false)
{
	$timeRemaining = $rcx->GetTimeRemaining() * 1000;
	echo "<next url='https://hangouts.google.com/call/styow2upujcp3hvhninl64l5uee' time='$timeRemaining' sidebar='$feedSidebarUrl' />";
}
else if (strpos($sourceUrl, "hangouts.google.com/call/styow2upujcp3hvhninl64l5uee") !== false)
{
	$timeRemaining = 0;
	$url = "";
	
	if ($contextFound)
	{
	    $timeRemaining = $rcx->GetTimeRemaining() * 1000;
	    $url = $rcx->GetMovie()->GetUrl() . "?t=1";
	}
	
	echo "<next url='$url' time='$timeRemaining' sidebar='$feedSidebarUrl' />";
}
else
{
	echo "<next url='' time='0' sidebar='' />";
}

?>