<?php

include "RECLINATHON_CONTEXT.php";

$reclinee = new RECLINEE();

$result = $reclinee->ProcessForm();

$URL = "http://" . $_SERVER['SERVER_NAME'] . "/rtt/ControlCenter.php";

if ($result)
{
    echo "Success!";
    echo "<meta http-equiv='refresh' content=\"0;url=" . $URL . "\" />";
}
else
{
    echo "Failed to update your user information.  Please verify the form fields and try again.<br />";
}

echo "<a href='" . $URL . "'>Go back</a>";

?>