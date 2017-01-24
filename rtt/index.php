<?php

include "RECLINATHON_CONTEXT.php";

//$currenttime = round(1000 * (microtime(true) + time()));
//$duration = $START + $TIMER - $currenttime;

$rcx = new RECLINATHON_CONTEXT();
$SEASON = $rcx->GetCurrentSeason();

if ($_POST["ContextID"] != '')
{
    $ContextID = $_POST["ContextID"];
}
else if ($_GET["ContextID"] != '')
{
    $ContextID = $_GET["ContextID"];
}
else
{
    $query = "SELECT ContextID FROM RECLINATHON_CONTEXT WHERE Season = '" . $SEASON . "' AND TimeStamp <= '" . date("U") . "' ORDER BY TimeStamp DESC";
    $result = $rcx->query($query);
    if (!$result)
    {
        echo "ERROR GETTING CONTEXT LIST!";
        exit();
    }
    $row = mysql_fetch_assoc($result);
    $ContextID = $row["ContextID"];
}

if ($ContextID == ''       ||
    !$rcx->Load($ContextID) )
{
    echo "Context Record not found.<BR>";
    exit();
}

$StartTime = round(microtime(true) * 1000);
$TimeRemaining = $rcx->GetTimeRemaining();

?>

<HTML>
<HEAD>
<title>Reclinathon Tracking Technology</title>
<link rel="stylesheet" type="text/css" href="rtt.css" />

<script language="JavaScript">
<!-- Begin

var  localstart = 0;
var  refreshtime = 0;
var  duration = 0;
var  timerinterval = 200;
var  answerToNewQuestionInterval = 5000;
var  questionToAnswerInterval = 5000;
var  triviaAnswer = "";

function createXMLHttpRequest() 
{
	try { return new XMLHttpRequest(); } catch(e) {}
	try { return new ActiveXObject("Msxml2.XMLHTTP"); } catch (e) {}
	alert("XMLHttpRequest not supported");
	return null;
}
	
function GetTriviaContent()
{
	var xhReq = createXMLHttpRequest();
	xhReq.open("GET", "GetTriviaContent.php", true);
	xhReq.onreadystatechange = function() {
		if (xhReq.readyState != 4) { return; }
		var xml = xhReq.responseXML;
		var newQuestion = "";
		var newAnswer = "";
		
		if (xml != null)
		{
			var result = xml.getElementsByTagName("trivia");
			if (result.length > 0)
			{
				newQuestion = result[0].getAttribute("question");
				newAnswer = result[0].getAttribute("answer");
			}
			
			document.getElementById('TriviaQuestion').innerHTML = newQuestion;
			triviaAnswer = newAnswer;
			document.getElementById('TriviaAnswer').innerHTML = "";
			setTimeout("ShowAnswer()", questionToAnswerInterval);
		}
		else
		{
			setTimeout("GetTriviaContent()", 500);
		}
	};
	xhReq.send(null);
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

       if(days>0) disp +=days+" Day";
       if(days>1) disp +="s";
       if(days>0) disp += " ";

       if(hours<=9) disp += "0";

       disp +=hours+":";
 
	if(min<=9) disp += "0";

	disp+=min+":";

	if(sec<=9) disp+="0"+sec;

	else disp+=sec;

	return(disp); 
}

function ShowAnswer() {
	document.getElementById('TriviaAnswer').innerHTML = triviaAnswer;
	setTimeout("GetTriviaContent()", answerToNewQuestionInterval);
}

function Down() {

	var localdate = new Date();
	localstart = localdate.getTime();
	refreshstart = localdate.getTime();
       
       duration = <?php echo $TimeRemaining; ?>;
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
	  timeleft = 0;
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
<BODY bgcolor='white' onload='Down();GetTriviaContent();' CLASS='noborder'>

<?php
//----------------
// NEW TITLE DIV
// ADDED BY CA
//----------------
$rcx->DisplayTitleLogo();


echo "<CENTER><A HREF='http://www.reclinathon.com/watch.php'>Watch Live</A><BR><BR><TABLE CLASS='RttFrame'>";

//----------------
// OLD TITLE TR
// REMOVED BY CA
//----------------
//echo "<TR>";
//  echo "<TD COLSPAN='2' CLASS='RttFrame'>";
//    echo "<TABLE CLASS='title'><TR><TD CLASS='title'><H1>LA-Z-DUDE Reclinathon:  Winter 2008</H1></TD></TR></TABLE><BR>";
//  echo "</TD>";
//echo "</TR>";


echo "<TR>";
  echo "<TD CLASS='RttFrame'>";
    $rcx->DisplayModule();
  echo "</TD>";
  echo "<TD CLASS='RttFrameRight'>";
    $rcx->DisplayDowntimeModule(false);
  echo "</TD>";
echo "</TR>";
echo "<TR>";
  echo "<TD CLASS='RttFrame'>";
    echo "<BR>";
  echo "</TD>";
  echo "<TD CLASS='RttFrameRight'>";
    echo "<BR>";
  echo "</TD>";
echo "</TR>";
echo "<TR>";
  echo "<TD CLASS='RttFrame'>";
    if ($rcx->HasMovie())
    {
		$rcx->GetMovie()->HideVotingInfo();
        $rcx->GetMovie()->DisplayModule();
    }
  echo "</TD>";
  echo "<TD CLASS='RttFrameRight'>";
    $rcx->DisplayHistoryModule();
  echo "</TD>";
echo "</TR>";
echo "</TABLE></CENTER>";

?>

</body>
</html>
