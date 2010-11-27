<?php

include "RECLINATHON_CONTEXT.php";

/*
if ($_GET["Tag"] == "")
{
    echo "Tag Not Specified";
    exit();
}

$query = "SELECT FormalName FROM RECLINEE r JOIN RFID rf on rf.ReclineeID = r.ReclineeID WHERE rf.Tag = '" . $_GET["Tag"] . "'";
$result = mysql_query($query);
if (!$result)
{
    echo "SQL Error";
    exit();
}
$row = mysql_fetch_row($result);
if (!$row)
{
    echo "Tag not found";
    exit();
}
echo $row[0];
*/

$rcx = new RECLINATHON_CONTEXT();
$rcx->ProcessAutomationCommand();

?>