<?php

session_start();

include "RECLINATHON_CONTEXT.php";

$ReclineeID = $_SESSION["ReclineeID"];

//$_SESSION = array();

//session_destroy();

if ($ReclineeID == "")
{
    $URL = "http://" . $_SERVER['SERVER_NAME'] . "/login.php";
    header ("Location: $URL");
}

$reclinee = new RECLINEE();
$reclinee->Load($ReclineeID);

echo "<html>";
echo "<head>";
echo "<title>Reclinathon Command Center</title>";
echo "<link rel='stylesheet' type='text/css' href='../index_new.css' />";
echo "</head>";

echo "<body class='noborder'>";

$currentPage = "login";
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

echo "<tr><td>Vote for the movies you want to see at this year's Reclinathon.</td><td>";
if ($reclinee->HasVoted())
{
    echo "Done! Thank you for voting.";
}
else
{
    echo "<form action='display.php' method='post'>" . $ReclineeIDInput . "<input type='submit' value='Go' /></form>";
}
echo "</td></tr>";

echo "</form></table><br />";

?>

</div>
</center>
</div>

</body>
</html>
