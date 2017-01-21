<?php

header("Cache-Control: no-cache, must-revalidate");
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Content-Type: text/xml");

include "RECLINATHON_CONTEXT.php";

if ("" == $_GET["season"])
{
    $Season = "Winter2013";
}
else
{
    $Season = $_GET["season"];
}

$context = new RECLINATHON_CONTEXT();

$context->ShowRecliningRatio($Season);

?>