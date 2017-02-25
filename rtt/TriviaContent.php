<?php
include "RECLINATHON_CONTEXT.php";
?>

<HTML>
<HEAD>

<script language="JavaScript">
<!-- Begin

function SubmitForm(sender)
{
	if (sender.name == "insert")
	{
		var actionInput = document.createElement("input");
		actionInput.type = "hidden";
		actionInput.name = "action";
		actionInput.value = "insert";
		document.triviaForm.appendChild(actionInput);
		
		document.triviaForm.appendChild(document.getElementById("newQuestion"));
		document.triviaForm.appendChild(document.getElementById("newAnswer"));
	}
	else
	{
		var action = (sender.name[0] == 'd') ? "delete" : "edit";
		var tid = sender.name.substr(sender.name.indexOf('_'));
		var questionElement = document.getElementById("question" + tid);
		var answerElement = document.getElementById("answer" + tid);
		
		var actionInput = document.createElement("input");
		actionInput.type = "hidden";
		actionInput.name = "action";
		actionInput.value = action;
		document.triviaForm.appendChild(actionInput);
		
		var tidInput = document.createElement("input");
		tidInput.type = "hidden";
		tidInput.name = "tid";
		tidInput.value = tid.substr(1);
		document.triviaForm.appendChild(tidInput);
		
		var questionInput = document.createElement("input");
		questionInput.type = "hidden";
		questionInput.name = "question";
		questionInput.value = questionElement.innerHTML;
		document.triviaForm.appendChild(questionInput);
		
		var answerInput = document.createElement("input");
		answerInput.type = "hidden";
		answerInput.name = "answer";
		answerInput.value = answerElement.value;
		document.triviaForm.appendChild(answerInput);
	}
	
	document.triviaForm.submit();
}

</script>

</head>


<?php

$MovieList = new MOVIE_LIST();

if ($_POST["action"] == "delete")
{
	$tid = $_POST["tid"];
	
	if ($tid == "")
	{
		echo "Could not delete record: TID not found<br>";
	}
	else
	{
	    $query = "DELETE FROM Trivia WHERE TID = '$tid' LIMIT 1";
		$result = $MovieList->Query($query);
		if ($result)
		{
			echo "Record successfully deleted<BR>";
		}
		else
		{
			echo "Failed to delete record<BR>";
		}
	}
}
else if ($_POST["action"] == "edit")
{
	$tid = $_POST["tid"];
	$question = $_POST["question"];
	$answer = $_POST["answer"];
	
	if ($tid == "")
	{
		echo "Could not edit record: TID not found<br>";
	}
	else if ($question == "")
	{
		echo "Could not edit record: Question not found<BR>";
	}
	else
	{
		$question = addslashes($question);
		$answer = addslashes($answer);
	    $query = "UPDATE Trivia SET Question = '$question', Answer = '$answer' WHERE TID = '$tid'";
		$result = $MovieList->Query($query);
		if ($result)
		{
			echo "Record successfully modified<BR>";
		}
		else
		{
			echo "$query<BR>Failed to edit record<BR>";
		}
	}
}
else if ($_POST["action"] == "insert")
{
	$question = addslashes($_POST["newQuestion"]);
	$answer = addslashes($_POST["newAnswer"]);
	
	if ($question == "")
	{
		echo "Could not add record: Question not found<BR>";
	}
	else
	{
		$query = "INSERT INTO Trivia (Question, Answer) VALUES ('$question', '$answer')";
		$result = $MovieList->Query($query);
		if ($result)
		{
			echo "Record added successfully<BR>";
		}
		else
		{
			echo "Failed to add record<BR>";
		}
	}
}
  
$query = "SELECT * FROM Trivia";
$result = $MovieList->Query($query);

echo "<body><table><tr><th></th><th></th><th>Question/Fact</th><th>Answer</th></tr>";
echo "<tr><td colspan='2'><button onclick='SubmitForm(this)' name='insert'>add</button></td>";
echo "<td><textarea id='newQuestion' name='newQuestion' rows='4' cols='100'></textarea></td>";
echo "<td><input id='newAnswer' name='newAnswer' type='text' size='75' /></td></tr>";

while ($row = mysql_fetch_assoc($result))

{

    $TID = $row["TID"];
    $Question = $row["Question"];

    $Answer = $row["Answer"];


    echo "<tr>";
    echo "<td><button onclick='SubmitForm(this)' name='delete_$TID'>x</button></td>";
    echo "<td><button onclick='SubmitForm(this)' name='edit_$TID'>&#10004;</button></td>";
    echo "<td><textarea id='question_$TID' rows='4' cols='100'>$Question</textarea></td>";
    echo "<td><input id='answer_$TID' type='text' size='75' value='$Answer' /></td>";
    echo "</tr>";

}


echo "</table><form name='triviaForm' action='TriviaContent.php' method='post'></form></body></html>";


?>