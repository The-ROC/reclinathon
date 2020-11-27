<?php

include "RECLINATHON_CONTEXT.php";

$MovieList = new MOVIE_LIST();

$Season = $MovieList->GetCurrentSeason();

$ResultType = $_GET["ResultType"];

if ($ResultType == "SummaryRoc")
{
    $query = $MovieList->GetConnection()->prepare(
        "SELECT m.Title, COUNT(v.VoteID) AS TotalVotes, SUM(v.Golden) AS TotalGoldenVotes FROM VOTE v JOIN MOVIE m ON v.MovieID = m.MovieID JOIN RECLINEE r ON r.ReclineeID = v.ReclineeID WHERE v.Season = ? AND r.RocMember = '1' GROUP BY v.MovieID ORDER BY TotalGoldenVotes DESC, TotalVotes DESC"
    );
    $query->bind_param('s', $Season);
    $result = $MovieList->Query($query);

    echo "<h3>Reclinathon ROC Election Results for $Season</h3><table><tr><th>Movie</th><th>Total Votes</th><th>Total Golden Votes</th></tr>";

    while ($row = $result->fetch_assoc())
    {
        $Movie = $row["Title"];
        $TotalVotes = $row["TotalVotes"];
        $TotalGoldenVotes = $row["TotalGoldenVotes"];

        echo "<tr><td>$Movie</td><td>$TotalVotes</td><td>$TotalGoldenVotes</td></tr>";
    }
    echo "</table>";
}
else if ($ResultType == "SummaryAll")
{
    $query = $MovieList->GetConnection()->prepare(
        "SELECT m.Title, COUNT(v.VoteID) AS TotalVotes, SUM(v.Golden) AS TotalGoldenVotes FROM VOTE v JOIN MOVIE m ON v.MovieID = m.MovieID JOIN RECLINEE r ON r.ReclineeID = v.ReclineeID WHERE v.Season = ? GROUP BY v.MovieID ORDER BY TotalGoldenVotes DESC, TotalVotes DESC"
    );
    $query->bind_param('s', $Season);
    $result = $MovieList->Query($query);

    echo "<h3>Reclinathon General Election Results for $Season</h3><table><tr><th>Movie</th><th>Total Votes</th><th>Total Golden Votes</th></tr>";

    while ($row = $result->fetch_assoc())
    {
        $Movie = $row["Title"];
        $TotalVotes = $row["TotalVotes"];
        $TotalGoldenVotes = $row["TotalGoldenVotes"];

        echo "<tr><td>$Movie</td><td>$TotalVotes</td><td>$TotalGoldenVotes</td></tr>";
    }
    echo "</table>";
}
else if ($ResultType == "full")
{
    $query = $MovieList->GetConnection()->prepare(
        "select r.FirstName, r.LastName, r.RocMember, m.Title, v.Golden from VOTE v join MOVIE m on v.MovieID = m.MovieID join RECLINEE r on r.ReclineeID = v.ReclineeID where v.Season = ?"
    );
    $query->bind_param('s', $Season);
    $result = $MovieList->Query($query);

    echo "<h3>Reclinathon Election Results for $Season</h3><table><tr><th>Reclinee</th><th>ROC Member</th><th>Movie</th><th>Golden</th></tr>";

    while ($row = $result->fetch_assoc())
    {
        $Reclinee = $row["FirstName"] . " " . $row["LastName"];
        $RocMember = $row["RocMember"];
        $Movie = $row["Title"];
        $Golden = $row["Golden"];

        echo "<tr><td>$Reclinee</td><td>$RocMember</td><td>$Movie</td><td>$Golden</td></tr>";
    }
    echo "</table>";
}
else
{
    echo "<h3>Reclinathon Election Results for $Season</h3>";
    echo "<a href = 'ElectionResults.php?ResultType=SummaryRoc'>ROC Vote Summary</a><br>";
    echo "<a href = 'ElectionResults.php?ResultType=SummaryAll'>General Vote Summary</a><br>";
    echo "<a href = 'ElectionResults.php?ResultType=full'>Individual Votes</a><br>";
}

?>