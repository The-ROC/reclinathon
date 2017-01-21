<?php

// Point to where you downloaded the phar
include('./httpful.phar');
 
// And you're ready to go!
$uri = "https://login.live.com/accesstoken.srf";
$response = \Httpful\Request::post($uri)
    ->addHeader('Content-Type', 'application/x-www-form-urlencoded')
	->body('grant_type=client_credentials&client_id=ms-app%3a%2f%2fs-1-15-2-3737610237-3589016691-1707362716-3487004198-2547717087-2204276685-1978148968&client_secret=YdsjfhB0HW%2BdK%2BoeSEayydvi2QqmaLBz&scope=notify.windows.com')
    ->send();
 
echo $response->__toString();


