<?php

include('./connect.php');

$query = "TRUNCATE TABLE PushNotificationChannels";
$result = mysql_query($query);
if (!$result)
{
	echo "Failed to clear existing registrations.";
}

echo "Cleared existing registrations.";

?>