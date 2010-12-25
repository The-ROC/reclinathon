<?php 

session_name("reclinathon2");
session_id("reclinathon2");
session_Start();

include $_SERVER['DOCUMENT_ROOT']."\include\connect.php";

?>

<HTML><HEAD><TITLE>Reclinathon</TITLE>

<title>JavaScript Resources - A Stopwatch and Countdown Timer</title>

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
      duration = xmlHttp.responseText;
      var localdate = new Date();
      localstart = localdate.getTime();
      refreshtime = duration - (duration % refreshinterval);
      //refreshstart = localdate.getTime();
      document.sw.beg2.value = refreshtime;
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

	//var localdate = new Date();
	//localstart = localdate.getTime();
	//refreshstart = localdate.getTime();
	document.sw.beg2.value = 0;

	ajaxFunction();

	DownRepeat(); 
}

function DownRepeat() {
	
	var localdate = new Date();
	var localtime = localdate.getTime();

	var timeleft = duration - (localtime - localstart);
 	if(timeleft < 0)
	{
	  timeleft = 0;
	}

	if(timeleft < refreshtime)
	{
	  ajaxFunction();
     	}

	var cmin = 1*Minutes(timeleft);
	var csec = 0+Seconds(timeleft);

	document.sw.disp2.value=Display(cmin,csec);

	setTimeout("DownRepeat()", timerinterval); 	
}

// End -->
</script>

<body bgcolor="#000000" onload="Down()" onDblClick="ajaxFunction()"><CENTER>
<table width="1000" border="0">
  <tr>
    <td><div align="center"><font color="#FFFFFF" size="+5">La-Z-Dude Reclinathon: 
          Winter 2007</font></div></td>
  </tr>
  <tr>
    <td height="30" bgcolor="#000066">&nbsp;</td>
  </tr>
  <tr>
    <td><table width="100%" border="0">
          <FORM NAME="sw">
          <input type="text" name="beg2" value="1">
          <tr>
            <td width="200"><img src="rec_left.jpg" width="200" height="200"></td>
            <td width="800"><div align="center">
                <font color="#FFFFFF" size="+7"><strong><input type="text" name="disp2" size="5" border="0" STYLE="background: Black; font-size: 28pt; color: #FFFFFF"></strong></font><BR><BR>
                <font color="#FFFFFF" size="+1"><?php if($TIMER > 0) { echo "Remaining Until the Next Movie:"; } else { echo "Now Playing:"; } ?></font><BR><BR>
                <font color="#FFFFFF" size="+7"><?php echo $NEXT_MOVIE; ?></font> </div></td>
            <td width="200"><img src="rec_right.jpg" width="200" height="200"></td>
          </tr>
          </FORM>
        </table></td>
  </tr>
</table>
</CENTER>

</body>
</html>
