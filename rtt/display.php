<?php

include "RECLINATHON_CONTEXT.php";

?>

<HTML>
<HEAD>
<link rel="stylesheet" type="text/css" href="rtt.css" />

<script language="JavaScript">
<!-- Begin

function ToggleVoteButton()
{
    if (document.vote.MoviesSelected.value == "15" && document.vote.GoldenMovieID.value != "")
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
    document.vote.Info.value = "of 15 movies selected.";
    document.vote.Info2.value = "The 'Vote' button at the top of the page will be enabled when 15 movies and a golden movie are selected.";
}

function Down()
{
    document.vote.MoviesSelected.value--;
}

function Up()
{
    document.vote.MoviesSelected.value++;
}

function ToggleDisplay(box) 
{ 
    if (box.checked)
    {
        document.getElementById(box.value).style.display="";
        Up();
    }
    else
    {
        document.getElementById(box.value).style.display="none";
        Down();
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
}

function ShowAll()
{
    var TotalMovies = 0;
    var inputs = document.getElementsByTagName("INPUT");
    for (var i = 0; i < inputs.length; i++) 
    {
        if (inputs[i].type == 'checkbox')
        {
            TotalMovies++;
            inputs[i].checked = true;
            ToggleDisplay(inputs[i]);
        }
    }
    document.vote.MoviesSelected.value = TotalMovies;
}

// End -->
</script>

</HEAD>

<BODY onload='SetMoviesSelected()'>

<FORM NAME='vote' ACTION="vote.php" METHOD="post">
<?php 
$reclinee = new RECLINEE();
$reclinee->DisplayRocMemberList();
?>
<INPUT TYPE="HIDDEN" NAME="GoldenMovieID" VALUE="">
<INPUT TYPE="TEXT" NAME="MoviesSelected" VALUE="LOADING..."  style="border: 0px; text-align:right" readonly>
<INPUT TYPE="TEXT" NAME="Info" VALUE="PLEASE WAIT" style="border: 0px" readonly>
<INPUT TYPE="SUBMIT" NAME="VoteButton" VALUE="Vote" disabled>
<BR>
GOLDEN MOVIE: <INPUT TYPE="TEXT" NAME="GoldenMovie" VALUE = "" SIZE="45" style="border: 0px; text-align:right" readonly>
<BR>
<INPUT TYPE="TEXT" NAME="Info2" VALUE="" SIZE="100" style="border: 0px" readonly>
<BR><BR>
<?php
$movie = new MOVIE();
$movie->DisplayAll();
?>

</FORM>