<?php

header("Cache-Control: no-cache, must-revalidate");
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Content-Type: text/xml");

include '../RECLINATHON_CONTEXT.php';

$remoteReclinathon = new REMOTE_RECLINATHON();
$currentReclinathonId = $remoteReclinathon->GetCurrentRemoteReclinathonId();

$currentContextId = 0;
$currentUrl = "";

if ($currentReclinathonId != "")
{
	$rcx = new RECLINATHON_CONTEXT();
    
	if ($rcx->LoadCurrentNonPending($currentReclinathonId))
	{
		$currentContextId = $rcx->GetContextId();
		$currentUrl = $rcx->GetUrl();
	}
}

echo "<Context id='$currentContextId' url='$currentUrl' />";
	
?>