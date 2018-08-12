<?php

header("Cache-Control: no-cache, must-revalidate");
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Content-Type: text/xml");

$sourceUrl = strtolower($_GET["sourceUrl"]);

if (strpos($sourceUrl, "hugh") !== false && strpos($sourceUrl, "grant") !== false)
{
	echo "<next url='https://reclinathon.com' time='3000' sidebar='' />";
}
else if (strpos($sourceUrl, "netflix.com/watch/60031236") !== false)
{
	echo "<next url='https://hangouts.google.com/call/styow2upujcp3hvhninl64l5uee' time='60000' sidebar='https://www.reclinathon.com/rtt/feed_mockup.php?activity=Reclining1' />";
}
else if (strpos($sourceUrl, "hangouts.google.com/call/styow2upujcp3hvhninl64l5uee") !== false)
{
	echo "<next url='https://www.netflix.com/watch/70083111?t=52' time='15000' sidebar='https://www.reclinathon.com/rtt/feed_mockup.php?activity=Downtime' />";
}
else if (strpos($sourceUrl, "netflix.com/watch/70083111") !== false)
{
	echo "<next url='https://www.reclinathon.com/rtt/feed_mockup.php?activity=EndReclinathon' time='60000' sidebar='https://www.reclinathon.com/rtt/feed_mockup.php?activity=Reclining2' />";
}
else
{
	echo "<next url='' time='0' sidebar='' />";
}

?>