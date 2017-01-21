<?php 

$currentTime = round($_SERVER["REQUEST_TIME_FLOAT"] * 1000);
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Cache-Control: no-cache");
header("Pragma: no-cache");
header("X-CurrentTime: $currentTime");

echo $_SERVER["REQUEST_TIME_FLOAT"] . "<br />";
echo microtime(true) . "<br />";

?>