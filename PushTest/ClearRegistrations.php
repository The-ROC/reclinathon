<?php

include('./connect.php');

$query = $db->prepare("TRUNCATE TABLE PushNotificationChannels");
$result = db_query($db, $query);
if (!$result)
{
	echo "Failed to clear existing registrations.";
}

echo "Cleared existing registrations.";

?>