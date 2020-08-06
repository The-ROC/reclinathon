<?php

include('./connect.php');
include('./httpful.phar');
 
$currentTime = time();
 
$authHeader = "";

$message = $_GET["message"];

if ($message == "")
{
	$message = "The current time is $currentTime.";
}

$messageSize = strlen($message);
 
$uri = "https://login.live.com/accesstoken.srf";
$response = \Httpful\Request::post($uri)
    ->addHeader('Content-Type', 'application/x-www-form-urlencoded')
	->body('grant_type=client_credentials&client_id=ms-app%3a%2f%2fs-1-15-2-3737610237-3589016691-1707362716-3487004198-2547717087-2204276685-1978148968&client_secret=YdsjfhB0HW%2BdK%2BoeSEayydvi2QqmaLBz&scope=notify.windows.com')
    ->send();
 
if ($response->code == 200)
{
	$accessToken = $response->body->access_token;
	$tokenType = $response->body->token_type;
	$expiryTime = $response->body->expires_in;
	
	if ($accessToken == "" || $tokenType == "")
	{
		echo "auth token not found";
		exit();
	}
	
	$authHeader = "$tokenType $accessToken";
}
else
{
	echo $response->__toString();
	exit();
}


$query = $db->prepare("SELECT * FROM PushNotificationChannels WHERE ? < ExpirationTime");
$query->bind_param('i', $currentTime);
$result = db_query($db, $query);

while ($row = $result->fetch_assoc())
{
	$channelUri = $row["ChannelUri"];
	
	$response = \Httpful\Request::post($channelUri)
		->addHeader('Authorization', $authHeader)
		->addHeader('Content-Type', 'application/octet-stream')
		->addHeader('Content-Length', "$messageSize")
		->addHeader('X-WNS-Type', 'wns/raw')
		->body($message)
		->send();
	
	if ($response->code == 200)
	{
		echo "Push sent to $channelUri <br /><br />";
	}
	else
	{
		echo "Failed to send push to $channelUri : Code  $response->code<br /><br />";
	}
}
	
?>