<?php

include "RECLINATHON_CONTEXT.php";

//CHANGE THIS VARIABLE TO CHANGE WHICH QUIZ IS DISPLAYED
$SEASON = "Winter 2009";

//
//Fetch the Reclinee taking the quiz
//
if ($_GET["ReclineeID"] == "")
{
    echo "No Reclinee Selected.";
    exit();
}
$r = new RECLINATHON_CONTEXT();

//
//Insert the most recent answers
//
if ($_GET["QuestionAnswered"] == 1)
{
    
}

//
//Get the ordering of the last question answered
//
$LatestQuestionOrder = 0;
$LastQuestionOrder = 0;

$query = "SELECT MAX(q.Ordering) AS LatestQuestionOrder FROM QUIZ_ANSWERS a JOIN QUIZ_QUESTION q ON q.QuestionID = a.QuestionID WHERE q.Season = '" . $SEASON . "' AND a.ReclineeID = '" . $_GET["ReclineeID"] . "'";
$result = $r->query($query);
if (!$result)
{
    echo "Error fetching last answered question.";
    exit();
}
$row = mysql_fetch_assoc($result);
if ($row["LatestQuestionOrder"] != "")
{
    $LatestQuestionOrder = $row["LatestQuestionOrder"]; 
}
echo $LatestQuestionOrder . "<BR>";

$query = "SELECT MAX(Ordering) AS LastQuestionOrder FROM QUIZ_QUESTION WHERE Season = '" . $SEASON . "'";
$result = $r->query($query);
if (!$result)
{
    echo "Error fetching last question.";
    exit();
}
$row = mysql_fetch_assoc($result);
if (!$row || $row["LastQuestionOrder"] == "")
{
    echo "No questions found for this season.";
    exit();
}
$LastQuestionOrder = $row["LastQuestionOrder"];
echo $LastQuestionOrder . "<BR>";

//
//Check if we are done with the quiz
//
if ($LatestQuestionOrder == $LastQuestionOrder)
{
    echo "Quiz is done.";
    exit();
}


//
//Fetch the next question;
//
$query = "SELECT * FROM QUIZ_QUESTION WHERE Season = '" . $SEASON . "' AND Ordering > '" . $LatestQuestionOrder . "' ORDER BY Ordering LIMIT 1";
$result = $r->query($query);
if (!$result)
{
    echo "Error fetching next question.";
    exit();
}
$row = mysql_fetch_assoc($result);
if (!$row)
{
    echo "Next question not found.";
    exit();
}

echo $row["Section"] . "<BR><BR>" . $row["Question"] . "<BR><BR>";

//
//Fetch the choices for this question
//
$query = "SELECT * FROM QUIZ_CHOICES WHERE QuestionID = '" . $row["QuestionID"] . "' ORDER BY Ordering";
$result = $r->query($query);
if (!$result)
{
    echo "Error fetching choices.";
    exit();
}
if (0 == mysql_num_rows($result))
{
    echo "No choices found.";
    exit();
}

$FillBox = false;
$EssayBox = false;
while ($row = mysql_fetch_assoc($result))
{
    switch($row["Type"])
    {
        case "mc":
            echo "<INPUT TYPE='radio' NAME='mc' VALUE='" . $row["ChoiceID"] . "'>" . $row["Choice"] . "</INPUT><BR>";
            break;
        case "sel":
            echo "<INPUT TYPE='checkbox' NAME='sel" . $row["ChoiceID"] . "'>" . $row["Choice"] . "</INPUT><BR>";
            break;
        case "fill":
            if (!$FillBox) { echo "<INPUT TYPE='text' NAME='fill'></INPUT><BR>"; $FillBox = true; }
            break;
        case "essay":
            if (!$EssayBox) { echo "<TEXTAREA NAME='essay' ROWS='7' COLS='50'></TEXTAREA><BR>"; $EssayBox = true; }
            break;
        default:
            echo "ERROR:  Unknown Choice Type<BR>";
            exit();
    }
}

echo "<BR><BR><INPUT TYPE='submit' VALUE='Submit'><BR>";


?>



    
