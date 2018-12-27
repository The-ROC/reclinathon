<?php

include "RECLINATHON_CONTEXT.php";

if (isset($_POST["url"]) && isset($_POST["caption"]))
{
	$fullurl = $_POST["url"];
	$idpos = strpos($fullurl, "watch?v=");
	
	if ($idpos === false)
	{
		echo "Failed to find movie id";
		exit();
	}
	
	$movieid = substr($fullurl, $idpos+8);
	
	$url = "<iframe id=\"ytplayer\" type=\"text/html\" width=\"480\" height=\"270\" src=\"https://www.youtube.com/embed/$movieid?autoplay=0&origin=https://reclinathon.com&fs=1\" frameborder=\"0\" allowFullScreen></iframe>";
	
	$caption = $_POST["caption"];
	
	echo "$url<br>$caption<br><br>";
	
	$context = new RECLINATHON_CONTEXT();
	
	$query = "INSERT INTO VideoClips (URL, Refresh, Caption, Ordering, Played) VALUES('$url', '0', '$caption', '0', '0')";
	
    $result = $context->Query($query);
    
	if ($result)
    {
	    echo "success<br><br>";
    }
    else
    {
	    echo "error<br><br>";
    }
}

?>

<form action='InsertVideoClip.php' method='post'>
<table>
<tr>
<td>YouTube URL, ending in watch?v={id}: ></td>
<td><input type='text' name='url' id='url' /></td>
</tr>
<tr>
<td>Caption:</td>
<td><input type='text' name='caption' id='caption' /></td>
</tr>
</table>
<br>
<input type='submit' />
