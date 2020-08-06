<?php

session_start();

$ReclineeID = $_SESSION["ReclineeID"];
//$ReclineeID = $_POST["ReclineeID"];

//$_SESSION = array();

//session_destroy();

// Find url 1 directory above
$currUrl = 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
$currRoot = substr($currUrl, 0, strrpos($currUrl, '/'));
$prevRoot = substr($currRoot, 0, strrpos($currRoot, '/')) . '/';
$currRoot .= '/';

if ($ReclineeID == "")
{
    $URL = $prevRoot . "login.php?message=You must log in to access the command center.";
    header ("Location: $URL");
    //echo "<meta http-equiv='refresh' content=\"0;url=" . $URL . "\" />";
    exit();
}
include "RECLINATHON_CONTEXT.php";

$reclinee = new RECLINEE();
$reclinee->Load($ReclineeID);

echo "<html>";
echo "<head>";
echo "<title>Reclinathon Command Center</title>";
echo "<link rel='stylesheet' type='text/css' href='../index_new.css' />";
echo "</head>";

echo "<body class='noborder'>";

$currentPage = "command center";
include "../header.php";

echo "<div class='main'>";
echo "<center>";
echo "<div class='content' align='left'>";
echo "<br />";
echo "<h1>Welcome, " . $reclinee . "</h1>";

echo "<br /><h3>What would you like to do today?</h3>";

$ReclineeIDInput = "<input type='hidden' name='ReclineeID' value='" . $ReclineeID . "' />";

echo "<table class='commandcenter' cellspacing='20'>";

echo "<tr><td>Edit your user information.</td><td><form action='EditReclinee.php' method='post'>" . $ReclineeIDInput . "<input type='submit' value='Go' /></form></tr>";

// Update this to true when an election is in progress.
$voteActive = true;

if ($voteActive)
{
    echo "<tr><td>Vote for the movies you want to see at this year's Reclinathon.</td><td>";
    if ($reclinee->HasVoted())
    {
        echo "Done! Thank you for voting.";
    }
    else
    {
        echo "<form action='SpecialElection.php' method='post'>" . $ReclineeIDInput . "<input type='submit' value='Go' /></form>";
    }
    echo "</td></tr>";
}

// Get the active quiz metadata.
$query = $reclinee->GetConnection()->prepare(
    "SELECT * FROM QUIZ_METADATA WHERE ACTIVE = '1'"
);
$result = $reclinee->Query($query);
$row = $result->fetch_assoc();

if ($row["QuizName"] != "")
{
    $introMessage = $row["IntroMessage"];
    $completionMessage = $row["CompletionMessage"];
    echo "<tr><td>$introMessage</td><td>";
    if ($reclinee->HasAnsweredQuiz($row["QuizName"]))
    {
        echo $completionMessage;
    }
    else
    {
        echo "<form action='quiz.php' method='post'>" . $ReclineeIDInput . "<input type='submit' value='Go' /></form>";
    }
    echo "</td></tr>";
}

echo "<tr><td>Log out</td><td>";
$URL = $prevRoot . "logout.php";
echo "<form action='$URL' method='post'><input type='submit' value='Go' /></form>";
echo "</td></tr>";

echo "</form></table><br />";

?>

</div>
</center>
</div>

</body>
</html>
