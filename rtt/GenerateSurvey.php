<?php
include "RECLINATHON_CONTEXT.php";

$rcx = new RECLINATHON_CONTEXT();

$season = "Winter2016";
$movieNumber = 1;
foreach ($rcx->GetMovieListBySeason($season) as $title => $movie)
{	
	$questionairre = $season . "Survey";
	$question = addslashes("What was your reaction to the following movie: <BR><BR><B>$title<BR><BR></B>Please feel free to add comments to support your answer.");
	$query = $rcx->GetConnection()->prepare(
		"INSERT INTO QUIZ_QUESTION (Season, Ordering, Section, Question) VALUES (?, ?, 'Reclinathon Movie Quality', ?)"
	);
	$query->bind_param('sis', $questionairre, $movieNumber, $question);
	$result = $rcx->query($query);
	if (!$result)
	{
		echo "$query<br>";
		echo "Failed to insert question $movieNumber<br>";
		exit();
	}
	
	$questionNumber = $rcx->GetConnection()->insert_id;
	$choiceNumber = 1;
	
	$query = $rcx->GetConnection()->prepare(
		"INSERT INTO QUIZ_CHOICES (QuestionID, Ordering, Choice, Score, Type) VALUES (?, ?, '&#128079; (Clap)', '4', 'mc')"
	);
	$query->bind_param('ii', $questionNumber, $choiceNumber);
	$result = $rcx->query($query);
	if (!$result)
	{
		echo "Failed to insert question $questionNumber choice $choiceNumber<br>";
	}
	$choiceNumber++;
	
	$query = $rcx->GetConnection()->prepare(
		"INSERT INTO QUIZ_CHOICES (QuestionID, Ordering, Choice, Score, Type) VALUES (?, ?, '&#127917 (Snap)', '3', 'mc')"
	);
	$query->bind_param('ii', $questionNumber, $choiceNumber);
	$result = $rcx->query($query);
	if (!$result)
	{
		echo "Failed to insert question $questionNumber choice $choiceNumber<br>";
	}
	$choiceNumber++;
	
	$query = $rcx->GetConnection()->prepare(
		"INSERT INTO QUIZ_CHOICES (QuestionID, Ordering, Choice, Score, Type) VALUES (?, ?, '&#128078; (Boo)', '2', 'mc')"
	);
	$query->bind_param('ii', $questionNumber, $choiceNumber);
	$result = $rcx->query($query);
	if (!$result)
	{
		echo "Failed to insert question $questionNumber choice $choiceNumber<br>";
	}
	$choiceNumber++;
	
	$query = $rcx->GetConnection()->prepare(
		"INSERT INTO QUIZ_CHOICES (QuestionID, Ordering, Choice, Score, Type) VALUES (?, ?, '&#128164; (Sleeper)', '1', 'mc')"
	);
	$query->bind_param('ii', $questionNumber, $choiceNumber);
	$result = $rcx->query($query);
	if (!$result)
	{
		echo "Failed to insert question $questionNumber choice $choiceNumber<br>";
	}
	$choiceNumber++;
	
	$query = "INSERT INTO QUIZ_CHOICES (QuestionID, Ordering, Choice, Score, Type) VALUES (?, ?, 'N/A - I was not reclining during this movie', '0', 'mc')";
	$query->bind_param('ii', $questionNumber, $choiceNumber);
	$result = $rcx->query($query);
	if (!$result)
	{
		echo "Failed to insert question $questionNumber choice $choiceNumber<br>";
	}
	$choiceNumber++;
	
	$query = "INSERT INTO QUIZ_CHOICES (QuestionID, Ordering, Choice, Score, Type) VALUES (?, ?, '', '0', 'essay')";
	$query->bind_param('ii', $questionNumber, $choiceNumber);
	$result = $rcx->query($query);
	if (!$result)
	{
		echo "Failed to insert question $questionNumber choice $choiceNumber<br>";
	}
	$choiceNumber++;
	
	$movieNumber++;
}

echo "Finished Populating Survey<br>";

?>