<?php
session_start();
include "RECLINATHON_CONTEXT.php";
?>

<HTML>
<HEAD>
<TITLE>Reclinathon Special Election</TITLE>
<link rel="stylesheet" type="text/css" href="rtt.css" />

<script language="JavaScript">
<!-- Begin

function ToggleVoteButton()
{
    const requireThemeMovies = false;
    const minThemeSelected = requireThemeMovies ? 3 : 0;
    if (document.vote.MoviesSelected.value == "15" && parseInt(document.vote.ThemeMoviesSelected.value) >= minThemeSelected && document.vote.GoldenMovieID.value != "")
    {
        document.vote.VoteButton.disabled = false;
    }
    else
    {
        document.vote.VoteButton.disabled = true;
    }
}

function SetMoviesSelected()
{
    HideAll();
    document.vote.MoviesSelected.value = "0";
	document.vote.ThemeMoviesSelected.value = "0";
    document.vote.Info.value = "of 15 movies selected.";
	document.vote.Info2.value = "theme movies selected.";
}

function IsThemeMovie(box)
{
	if (box.className == "themeMovie")
	{
		return true;
	}
	
	return false;
}

function Down(box)
{
    document.vote.MoviesSelected.value--;
	
	if (IsThemeMovie(box))
	{
		document.vote.ThemeMoviesSelected.value--;
	}
}

function Up(box)
{
    document.vote.MoviesSelected.value++;
	
	if (IsThemeMovie(box))
	{
		document.vote.ThemeMoviesSelected.value++;
	}
}

function ToggleDisplay(box) 
{ 
    if (box.checked)
    {
        document.getElementById(box.value).style.display="";
        Up(box);
    }
    else
    {
        document.getElementById(box.value).style.display="none";
        Down(box);
        ResetGolden(box.value);
    }
    ToggleVoteButton();    
}

function ResetGolden(MovieString)
{
    if (MovieString == document.vote.GoldenMovieID.value)
    {
        document.vote.GoldenMovieID.value = "";
        document.vote.GoldenMovie.value = "";
    }
}

function SetGolden(MovieID, Title)
{
    var string = "vote"+MovieID;
    var box = document.getElementById(string);
    if (box.checked == 0)
    {
        box.checked = 1;
        ToggleDisplay(box);
    }
    document.vote.GoldenMovie.value = Title;
    if (MovieID != "")
    {
        document.vote.GoldenMovieID.value = "movie"+MovieID;
    }
    ToggleVoteButton();
}

function HideAll()
{
    var inputs = document.getElementsByTagName("INPUT");
    for (var i = 0; i < inputs.length; i++) 
    {
        if (inputs[i].type == 'checkbox')
        {
            inputs[i].checked = false;
            ToggleDisplay(inputs[i]);
        }
    }
    document.vote.MoviesSelected.value = "0";
	document.vote.ThemeMoviesSelected.value = "0";
}

function ShowAll()
{
	document.vote.MoviesSelected.value = 0;
	document.vote.ThemeMoviesSelected.value = 0;
    var inputs = document.getElementsByTagName("INPUT");
    for (var i = 0; i < inputs.length; i++) 
    {
        if (inputs[i].type == 'checkbox')
        {
            inputs[i].checked = true;
            ToggleDisplay(inputs[i]);
        }
    }
}

// End -->
</script>

</HEAD>

<BODY bgcolor='white' onload='SetMoviesSelected()'>

<FORM NAME='vote' ACTION="vote.php" METHOD="post">
<?php 

$reclineeID = $_SESSION["ReclineeID"];
if($_POST["ReclineeID"] != "")
    $reclineeID = $_POST["ReclineeID"];

if ($reclineeID == "")
{
    $reclinee = new RECLINEE();
    $reclinee->DisplayRocMemberList();
}
else
{   
	$reclinee = new RECLINEE();
    $reclinee->Load($reclineeID);
	echo "<h3>Welcome, " . $reclinee . "</h3>";
    echo "<INPUT TYPE='HIDDEN' NAME='ReclineeID' VALUE='" . $reclineeID . "'>";
}
?>
<INPUT TYPE="HIDDEN" NAME="GoldenMovieID" VALUE="">
<BR>
<INPUT TYPE="TEXT" NAME="MoviesSelected" VALUE="LOADING..."  style="border: 0px; text-align:right" readonly>
<INPUT TYPE="TEXT" NAME="Info" VALUE="PLEASE WAIT" style="border: 0px" readonly>
<BR>
<INPUT TYPE="TEXT" NAME="ThemeMoviesSelected" VALUE=""  style="border: 0px; text-align:right" readonly>
<INPUT TYPE="TEXT" NAME="Info2" VALUE="" style="border: 0px" readonly>
<BR>
GOLDEN MOVIE: <INPUT TYPE="TEXT" NAME="GoldenMovie" VALUE = "" SIZE="45" style="border: 0px;" readonly>
<BR>
<INPUT TYPE="SUBMIT" NAME="VoteButton" VALUE="Vote" disabled>

<BR><BR>
The 'Vote' button at the top of the page will be enabled when 15 movies and a golden movie are selected.<BR>At least 3 of the selected movies must be Reclinathon Theme Movies.
<BR><BR>There may also be Reclinathon Special Events in the ballot.  These are not traditional Reclinathon movies, but they are on the ballot to be considered for screening during this year's Reclinathon.  You are free to vote for these or not, as you see fit.  There is no requirement to choose any special events while voting.
<BR><BR>
<?php
$movie = new MOVIE();
$movie->DisplaySpecialElection();
?>

</FORM

</BODY>
</HTML>