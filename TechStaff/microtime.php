<HTML><HEAD><TITLE>Reclinathon</TITLE>

<script language="JavaScript">
<!-- Begin

function Down() {
	var localdate = new Date();
	var localstart = localdate.getTime();
	document.sw.time.value = localstart;
}

// End -->
</script>

</HEAD>

<BODY onload="Down()">
<?php

$time1 = microtime(true);
$time2 = round(1000 * (microtime(true) + time()));

echo $time1."<BR>";
echo $time2."<BR>";

?>

<FORM NAME="sw">
<input type="text" name="time"></input>
</FORM>