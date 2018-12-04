<?php

require_once('../FEED_EVENTS.php');

header("Cache-Control: no-cache, must-revalidate");
header("Content-Type: text/xml");

if(isset($_GET["lastEventID"]))
{
    $feedEvents = new FEED_EVENTS();
    
    if(isset($_POST["feedPost"]) && isset($_POST["reclineeID"]))
    {
        $feedEvents->PostUserMessage($_POST["reclineeID"], $_POST["feedPost"], time());
    }

    $feedEvents->LoadEventsAfterID($_GET["lastEventID"]);
    echo $feedEvents->FormatAJAXResponse();
}
?>