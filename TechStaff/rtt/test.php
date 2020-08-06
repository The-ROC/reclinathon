<?php

include "RECLINATHON_CONTEXT.php";
$ContextID = $_POST['ContextID'];

$currenttime = round(1000 * (microtime(true) + time()));
$duration = $START + $TIMER - $currenttime;

?>

<HTML>
<HEAD>
<link rel="stylesheet" type="text/css" href="rtt.css" />

<script language="JavaScript">
<!-- Begin

var  localstart = 0;
var  refreshtime = 0;
var  duration = 0;
var  timerinterval = 200;
var  refreshinterval = 10000;

function ajaxFunction()
{
var xmlHttp;
try
  {
  // Firefox, Opera 8.0+, Safari
  xmlHttp=new XMLHttpRequest();
  }
catch (e)
  {
  // Internet Explorer
  try
    {
    xmlHttp=new ActiveXObject("Msxml2.XMLHTTP");
    }
  catch (e)
    {
    try
      {
      xmlHttp=new ActiveXObject("Microsoft.XMLHTTP");
      }
    catch (e)
      {
      alert("Your browser does not support AJAX!");
      return false;
      }
    }
  }
  xmlHttp.onreadystatechange=function()
    {
    if(xmlHttp.readyState==4)
      {
      //duration = ;
      var localdate = new Date();
      localstart = localdate.getTime();
      refreshtime = duration - (duration % refreshinterval);
      //refreshstart = localdate.getTime();
      //document.sw.beg2.value = refreshtime;
      }
    }
  xmlHttp.open("GET","ajaxtest.php",true);
  xmlHttp.send(null);
  }

function Days(data) {

	return ( Math.floor(data/(1000*60*60*24)) );
}

function Hours(data) {

	return ( Math.floor(data/(1000*60*60)) % 24 );
}

function Minutes(data) {

	return ( Math.floor(data/(1000*60)) % 60 );
}

function Seconds(data) {

	return ( Math.floor(data/1000) % 60);
}

function Display(days,hours,min,sec) {

	var disp = "";

                if(days>0) disp +=days+" Days ";

                disp +=hours+":";
 
	if(min<=9) disp += " 0";

	disp+=min+":";

	if(sec<=9) disp+="0"+sec;

	else disp+=sec;

	return(disp); 
}

function Down() {

	var localdate = new Date();
	localstart = localdate.getTime();
	refreshstart = localdate.getTime(); 
                duration = document.sw.disp2.value;
                duration *= 1000;

	//ajaxFunction();

	DownRepeat();
}

function DownRepeat() {
	
	var localdate = new Date();
	var localtime = localdate.getTime();

	var timeleft = duration - (localtime - localstart);
 	if(timeleft < 0)
	{
	  //timeleft = 0;
	}

	if(timeleft < refreshtime)
	{
	  //ajaxFunction();
     	}

	var cdays = 1*Days(timeleft);
                var chours = 1*Hours(timeleft);
	var cmin = 1*Minutes(timeleft);
	var csec = 0+Seconds(timeleft);

	document.sw.disp2.value=Display(cdays,chours,cmin,csec);

	setTimeout("DownRepeat()", timerinterval); 	
}

// End -->
</script>

</HEAD>
<BODY bgcolor='white' onload='Down()' style='margin:0; padding:0; border:0px;'>

<div style='width:100%; height:171px; background-image:url(images/gradient.png);'>
<CENTER>
<!--<TABLE CLASS='title'><TR><TD CLASS='title'>-->

<IMG SRC='images/ReclinathonLogo.png'>
<!--<H1>LA-Z-DUDE Reclinathon:  Winter 2008</H1>-->

<!--</TD></TR></TABLE>-->
</CENTER>
</div>

<?php

$query = $this->GetConnection()->prepare(
  "SELECT ContextID FROM RECLINATHON_CONTEXT WHERE TimeStamp <= ? ORDER BY TimeStamp DESC"
);
$query->bind_param('i', date('U'));

$rcx = new RECLINATHON_CONTEXT();

$result = $rcx->query($query);
if (!$result)
{
    echo "ERROR GETTING CONTEXT LIST!";
    exit();
}

$row = $result->fetch_assoc();
$ContextID = $row["ContextID"];

if ($ContextID != '')
{
    if (!$rcx->Load($ContextID))
    {
        echo "Context Record not found.<BR>";
    }
}

echo "<BR><CENTER>";
$rcx->DisplayModule();
if ($rcx->HasMovie())
{
    $rcx->GetMovie()->DisplayModule();
}
echo "</CENTER>";

?>
