<?php

include "RECLINATHON_CONTEXT.php";

include "../header.php";

//CHANGE THIS VARIABLE TO CHANGE WHICH QUIZ IS DISPLAYED
$SEASON = "Winter2015Survey";

//
//Fetch the Reclinee taking the quiz
//
if ($_POST["ReclineeID"] == "")
{
    echo "No Reclinee Selected.";
    exit();
}
$ReclineeID = $_POST["ReclineeID"];

$r = new RECLINATHON_CONTEXT();

//
//Insert the most recent answers
//
if ($_POST["QuestionID"] != "")
{
	$questionId = $_POST["QuestionID"];
	
	foreach ($_POST as $name => $value)
	{
		$query = "";
		
		if ($name == "mc" && $value != "")
		{
			$query = "INSERT INTO QUIZ_ANSWERS (ReclineeID, QuestionID, ChoiceID) VALUES ('$ReclineeID', '$questionId', '$value')";
		}
		else if ($name == "essay" && $value != "")
		{
			$answer = addslashes($value);
			$query = "INSERT INTO QUIZ_ANSWERS (ReclineeID, QuestionID, Answer) VALUES ('$ReclineeID', '$questionId', '$answer')";
		}
		else if ($name == "fill" && $value != "")
		{
			$answer = addslashes($value);
			$query = "INSERT INTO QUIZ_ANSWERS (ReclineeID, QuestionID, Answer) VALUES ('$ReclineeID', '$questionId', '$answer')";
		}
		else if (strpos($name, "sel") !== false && strpos($name, "sel") == 0)
		{
			$choiceId = substr($name, 3);
			$query = "INSERT INTO QUIZ_ANSWERS (ReclineeID, QuestionID, ChoiceID) VALUES ('$ReclineeID', '$questionId', '$choiceId')";
		}
		
		if ($query != "")
		{
			$result = $r->query($query);
			if (!$result)
			{
				echo "Error submitting answers<BR>$query<BR>.";
				exit();
			}
		}		
	}
}

//
//Find the ordering of the last question answered
//
$LatestQuestionOrder = 0;
$LastQuestionOrder = 0;

$query = "SELECT MAX(q.Ordering) AS LatestQuestionOrder FROM QUIZ_ANSWERS a JOIN QUIZ_QUESTION q ON q.QuestionID = a.QuestionID WHERE q.Season = '" . $SEASON . "' AND a.ReclineeID = '" . $ReclineeID . "'";
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
$CurrentQuestionOrder = $LatestQuestionOrder + 1;

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

//
//Check if we are done with the quiz
//
if ($LatestQuestionOrder == $LastQuestionOrder)
{
    echo "Thank you for your feedback!  Your answers have been recorded";
    exit();
}

echo "Question $CurrentQuestionOrder of $LastQuestionOrder.<BR><BR>";

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

$questionId = $row["QuestionID"];
if ($questionId == "")
{
    echo "Question ID not found.";
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

echo "<FORM action='quiz.php' method='post'><INPUT TYPE='hidden' NAME='ReclineeID' VALUE='$ReclineeID' /><INPUT TYPE='hidden' NAME='QuestionID' VALUE='$questionId' />";
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

echo "<BR><BR><INPUT TYPE='submit' VALUE='Submit'><BR></FORM>";


?>



    
