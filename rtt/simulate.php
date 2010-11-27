<?php

include "RECLINATHON_CONTEXT.php";

$MovieList = new MOVIE_LIST();
$QuotaList;
$NumQuotas = 0;
$GenreNames;
$GenreValues;
$NumGenres = 0;
$TicketsPerVote = 33;
$VotesPerAutoApprove = 4;

if ($_GET["TicketsPerVote"] != "")
{
    $TicketsPerVote = $_GET["TicketsPerVote"];
}

if ($_GET["VotesPerAutoApprove"] != "")
{
    $VotesPerAutoApprove = $_GET["VotesPerAutoApprove"];
}

$query = "SELECT GenreID, Name FROM GENRE";
$result = $MovieList->Query($query);
while($row = mysql_fetch_assoc($result))
{
    $GenreNames[$NumGenres] = $row["Name"];
    $GenreValues[$NumGenres] = $row["GenreID"];
    $NumGenres++;
}

echo "<FORM ACTION='simulate.php' METHOD='get'><TABLE>";
echo "<TR><TD>Tickets Per Vote</TD><TD><INPUT TYPE='text' NAME='TicketsPerVote' VALUE='" . $TicketsPerVote . "'></TD></TR>";
echo "<TR><TD>Votes Per Auto-Approve</TD><TD><INPUT TYPE='text' NAME='VotesPerAutoApprove' VALUE='" . $VotesPerAutoApprove . "'></TD></TR>";
for($i = 0; $i < 10; $i++)
{
    if ($_GET["Genre" . $i] != "" && $_GET["RunTime" . $i] != "")
    {
        $Quota = new QUOTA($_GET["Genre" . $i], $_GET["RunTime" . $i]);
        $QuotaList[$NumQuotas] = $Quota;
        $NumQuotas++;
    }
    echo "<TR><TD><SELECT NAME='Genre" .$i . "'>";
    for($j=0; $j < $NumGenres; $j++)
    {
        echo "<OPTION VALUE='" . $GenreValues[$j] . "'";
        if ($_GET["Genre" . $i] == $GenreValues[$j])
        {
            echo " SELECTED";
        }
        echo ">" . $GenreNames[$j] . "</OPTION>";
    }
    echo "</SELECT></TD><TD><INPUT TYPE='text' NAME='RunTime" . $i . "' VALUE='" . $_GET["RunTime" . $i] . "'></TD></TR>";
}
echo "<TR><TD></TD><TD><INPUT TYPE='submit' NAME='action' VALUE='New List'></TD></TR>";
echo "<TR><TD COLSPAN='2'>Enter a name to load or save a movie list.  When saving, any existing list with the same name will be overwritten.</TD></TR>";
echo "<TR><TD><INPUT TYPE='text' NAME='ListName' VALUE='" . $_GET["ListName"] . "'></TD><TD><INPUT TYPE='submit' NAME='action' VALUE='Load List'></TD></TR>";
echo "<TR><TD></TD><TD><INPUT TYPE='submit' NAME='action' VALUE='Save List'></TD></TR>";
echo "<TR><TD></TD><TD><INPUT TYPE='submit' NAME='action' VALUE='Delete List'></TD></TR>";
echo "<TR><TD></TD><TD><INPUT TYPE='submit' NAME='action' VALUE='Re-Simulate List'></TD></TR></TABLE></FORM>";

if ($_GET["action"] == 'Load List')
{
    if (!$MovieList->Load($_GET["ListName"]))
    {
        echo "List not found.<BR>";
        exit();
    }
    $MovieList->Simulate(mktime(16, 0, 0, 12, 28, 2008));
    $MovieList->Insert("temp");
}

else if ($_GET["action"] == 'Re-Simulate List')
{
    if (!$MovieList->Load("temp"))
    {
        echo "List not found.<BR>";
        exit();
    }
    $MovieList->Simulate(mktime(16, 0, 0, 12, 28, 2008));
    $MovieList->Insert("temp");
}

else if ($_GET["action"] == 'New List')
{
    $MovieList->GenerateList($VotesPerAutoApprove, $TicketsPerVote, 15, $QuotaList, $NumQuotas);
    $MovieList->Simulate(mktime(16, 0, 0, 12, 28, 2008));
    $MovieList->Insert("temp");
}

else if ($_GET["action"] == 'Save List')
{
    $MovieList->Load("temp");
    if (!$MovieList->Insert($_GET["ListName"]))
    {
        echo "Error saving list.<BR>";
        exit();
    }
    echo "List " . $_GET["ListName"] . " saved.<BR>";
}

else if ($_GET["action"] == 'Delete List')
{
    if (!$MovieList->Delete($_GET["ListName"]))
    {
        echo "Error deleting list.<BR>";
        exit();
    }
    echo "List " . $_GET["ListName"] . " deleted.<BR>";
}

//$MovieList->Dump(0, true);
?>