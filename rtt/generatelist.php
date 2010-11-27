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

echo "<FORM ACTION='generatelist.php' METHOD='get'><TABLE>";
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
echo "<TR><TD></TD><TD><INPUT TYPE='submit' VALUE='Generate List'></TD></TR></TABLE></FORM>";

$MovieList->GenerateList($VotesPerAutoApprove, $TicketsPerVote, 15, $QuotaList, $NumQuotas);

/*
$query = "SELECT Title, COUNT(Title) AS picks FROM MovieListExperiment GROUP BY Title ORDER BY picks DESC";
$result = $movie->query($query);

$TotalPicks = 0;
echo "<TABLE>";
while ($row = mysql_fetch_assoc($result))
{
    $TotalPicks += $row["picks"];
    echo "<TD>" . $row["Title"] . "</TD><TD>" . $row["picks"] . "</TD></TR>";
}
echo "</TABLE>";
echo "<BR><BR>" . $TotalPicks / 15 . " lists generated";
*/

?>