<?php 

session_name("reclinathon");
session_id("reclinathon");
session_Start();

include $_SERVER['DOCUMENT_ROOT']."\include\connect.php";

if(!session_is_registered("GO")) {
  $GO = 0;
  session_register("GO");
}


while($GO == 0) {
}

$RAND = rand(1, $RANDMAX);

$query = "SELECT * FROM Trivia WHERE TID = ".$RAND;
$result = mysql_query($query);
$row = mysql_fetch_row($result);
echo $row[1]."<BR><BR>".$row[2];

?>

<HTML><HEAD><TITLE>Timer</TITLE>

<title>JavaScript Resources - A Stopwatch and Countdown Timer</title>
<script language="JavaScript">
<!-- Begin

var cmin,csec;

function Minutes(data) {

	return(Math.floor(data/60)); 
}

function Seconds(data) {

	return(data % 60); 
}

function Display(min,sec) {

	var disp;

	if(min<=9) disp=" 0";

	else disp=" ";

	disp+=min+":";

	if(sec<=9) disp+="0"+sec;

	else disp+=sec;

	return(disp); 
}

function Down() {

	cmin=1*Minutes(document.sw.beg2.value);

	csec=0+Seconds(document.sw.beg2.value);

	DownRepeat(); 
}

function DownRepeat() {

	csec--;

	if(csec==-1) { csec=59; cmin--; }

	document.sw.disp2.value=Display(cmin,csec);

	if((cmin==0)&&(csec==0)) alert("Countdown Stopped");

	else down=setTimeout("DownRepeat()",1000); 
}

// End -->
</script>


</HEAD>

<BODY onload="Down()">

<FORM NAME="sw">
  <input type="hidden" name="beg2" value="<?php echo $TIMER;?>">
  <input type="text" name="disp2" size="9">
</FORM>


