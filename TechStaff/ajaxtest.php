<?php
//session_name("reclinathon2");
//session_id("reclinathon2");
//session_Start();

header("Cache-Control: no-cache, must-revalidate");
 // Date in the past
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");

$currenttime = round(1000 * (microtime(true) + time()));
//$duration = $START + $TIMER - $currenttime;  

//if($duration < 0) 
//{
//    $duration = 0;
//} 

//echo $duration;
echo $currenttime;
?>