<?php

header("Cache-Control: no-cache, must-revalidate");
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Content-Type: text/xml");

include "RECLINATHON_CONTEXT.php";

$context = new RECLINATHON_CONTEXT();

$vcid = $_GET["vcid"];
$contextId = $_GET["contextId"];

$query = "UPDATE VideoClips SET Played = '$contextId' WHERE VCID = '$vcid'";
$result = $context->Query($query);
if ($result)
{
	echo "success";
}
else
{
	echo "error";
}

?>