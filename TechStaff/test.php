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

function Minutes(data) {

	return(Math.floor(data/60000)); 
}

function Seconds(data) {

	return( Math.round(data / 1000) % 60 ); 
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

	var cmin = 1*Minutes(timeleft);
	var csec = 0+Seconds(timeleft);

	document.sw.disp2.value=Display(cmin,csec);

	//setTimeout("DownRepeat()", timerinterval); 	
}

// End -->
</script>

</HEAD>
<BODY onload='Down()'>

<FORM NAME="TestForm" ACTION="test.php" METHOD="post">
ContextID = 
<INPUT TYPE="text" NAME="ContextID" VALUE="<?php echo $ContextID; ?>"> 
<INPUT TYPE="submit" VALUE="Show">
</FORM>

<?php

$rcx = new RECLINATHON_CONTEXT();

if ($ContextID != '')
{
    if (!$rcx->Load($ContextID))
    {
        echo "Context Record not found.<BR>";
    }
}

echo "<BR><CENTER>";
$rcx->DisplayModule();
$rcx->GetMovie()->DisplayModule();
echo "</CENTER>";

echo '<BR><BR><BR>';

$rcx->Dump(0, TRUE);

echo '<BR><BR><BR>';

var_dump($rcx);

?>
