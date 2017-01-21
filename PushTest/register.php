<?php

include('./connect.php');
include('./httpful.phar');

$channelUri = $_GET["channelUri"];

if ($channelUri == "")
{
	$channelUri = $_POST["channelUri"];
}

if ($channelUri == "")
{
	$requestBody = @file_get_contents('php://input');
	
	$channelUriStartPos = strpos($requestBody, "\"ChannelUri\":\"");
	
	if ($channelUriStartPos !== false) 
	{
		$channelUriStartPos += 14;
		$channelUriEndPos = strpos($requestBody, "\"", $channelUriStartPos);
		
		if ($channelUriEndPos !== false) 
		{
			$channelUri = urldecode(substr($requestBody, $channelUriStartPos, $channelUriEndPos - $channelUriStartPos));
		}
	}
}

if ($channelUri == "")
{	
	http_response_code(400); 
	$requestBody = @file_get_contents('php://input');
	echo "channelUri not supplied<br />$requestBody";
	exit();
}
 
$accessToken = "";
$tokenType = "";
 
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
		http_response_code(400);
		echo "auth token not found";
		exit();
	}
}
else
{
	http_response_code(400);
	echo $response->__toString();
	exit();
}

if ($expiryTime == "")
{
	$expiryTime = 3600;
}

$query = "INSERT INTO PushNotificationChannels (ChannelUri, ExpirationTime) VALUES ('$channelUri', (UNIX_TIMESTAMP() + $expiryTime))";
$result = mysql_query($query);
if (!$result)
{
	http_response_code(400);
	echo "failed to register channel uri";
	exit();
}

http_response_code(200);
echo "Registered channel $channelUri";
