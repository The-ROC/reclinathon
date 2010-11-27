<?php
// Copyright 2007 Facebook Corp.  All Rights Reserved. 
// 
// Application: Reclinathon
// File: 'index.php' 
//   This is a sample skeleton for your application. 
// 

require_once('config.php');
require_once('Compatibility.php');
	
/*******************************************
 * Logged-in User Information
 *******************************************/
$compatibility = Compatibility::getInstance();

// Greet the currently logged-in user!
$compatScores = $compatibility->getCompatibility($user_id);
echo "<p>Hello, <fb:name uid=\"$user_id\" useyou=\"false\" />! ";
echo "<strong>Your Reclinathon Compatibility Score is " . $compatScores[$user_id] . ".</strong></p>";

/*******************************************
 * Friends using Reclinathon
 *******************************************/
$appFriends = $facebook->api_client->friends_getAppUsers();

echo "<table><tr>";
newSection("Friends Using Reclinathon:");

$appUids = $appFriends['friends_getAppUsers_response']['uid'];
$compatScores = $compatibility->getCompatibility($appUids);

foreach(array_keys($compatScores) as $appUid) {
	showReclinee($appUid, $compatScores);
}

echo "</tr><tr>";

/*******************************************
 * Friends who might like Reclinathon
 *******************************************/
newSection("Friends who might like Reclinathon:");
$friends = $facebook->api_client->friends_get();
$compatScores = $compatibility->getCompatibility($friends);

foreach(array_keys($compatScores) as $uid) {
	echo "<fb:if-is-app-user uid=\"$uid\">";
  	echo "<fb:else>";
	if($compatScores[$uid] > 0) {
		showReclinee($uid, $compatScores);
	}
	echo "</fb:else>";
	echo "</fb:if-is-app-user>";
}
echo "</tr><tr>";

/*******************************************
 * Friends who probably like dancing instead
 *******************************************/
newSection("Friends who probably like dancing instead:");

foreach (array_keys($compatScores) as $uid) {
 	echo "<fb:if-is-app-user uid=\"$uid\">";
  	echo "<fb:else>";
  
  	if($compatScores[$uid] <= 0) {
		showReclinee($uid, $compatScores);
	}
	
	echo "</fb:else>";
	echo "</fb:if-is-app-user>";
}
echo "</tr></table>";

/*******************************************
 * Display
 *******************************************/
function display($reset=false) {
	static $count = 0;
	static $row_max = 6;

	if($reset) {
		$count = 0;
	}
	else {
		if($count == $row_max) {
			$count = 0;
			echo "</tr><tr>";
		}
		
		$count++;
	}
	// Return static variables
	return array("count"=>$count, "row_max"=>$row_max);
}
 
function showReclinee($uid, $compatScores) {
	$displayVars = display();
	
	echo "<td width=100><center><fb:profile-pic uid=\"$uid\" size=\"square\" /><br><fb:name uid=\"$uid\" useyou=\"false\" /><br>" . $compatScores[$uid] . "</center></td>";
}

function newSection($title) {
	$displayVars = display(true);
	
	echo "<td colspan=" . $displayVars['row_max'] . ">" . $title . "</td></tr><tr>";
}
?>