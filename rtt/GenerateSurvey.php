<?php
include "RECLINATHON_CONTEXT.php";

$rcx = new RECLINATHON_CONTEXT();

$season = "Winter2015";
$movieNumber = 1;
foreach ($rcx->GetMovieListBySeason("Winter2015") as $title => $movie)
{	
	$questionairre = $season . "Survey";
    $question = addslashes("On a scale from 1 to 10, with 1 being a film that Dave calls \"interesting\" and 10 being a film that Schmidt actually really likes and Greg doesn't sleep through, how would you rate the following movie: <BR><BR><B>$title<BR><BR></B>Please feel free to add comments to support your answer.");
	$query = "INSERT INTO QUIZ_QUESTION (Season, Ordering, Section, Question) VALUES ('$questionairre', '$movieNumber', 'Reclinathon Movie Quality', '$question')";
	$result = $rcx->query($query);
	if (!$result)
	{
		echo "$query<br>";
		echo "Failed to insert question $movieNumber<br>";
		exit();
	}
	
	$choiceNumber = 1;
	
	$query = "INSERT INTO QUIZ_CHOICES (QuestionID, Ordering, Choice, Score, Type) VALUES ('$movieNumber', '$choiceNumber', '', '0', 'fill')";
	$result = $rcx->query($query);
	if (!$result)
	{
		echo "Failed to insert question $movieNumber choice $choiceNumber<br>";
	}
	$choiceNumber++;
	
	$query = "INSERT INTO QUIZ_CHOICES (QuestionID, Ordering, Choice, Score, Type) VALUES ('$movieNumber', '$choiceNumber', 'N/A - I was not reclining during this movie', '0', 'sel')";
	$result = $rcx->query($query);
	if (!$result)
	{
		echo "Failed to insert question $movieNumber choice $choiceNumber<br>";
	}
	$choiceNumber++;
	
	$query = "INSERT INTO QUIZ_CHOICES (QuestionID, Ordering, Choice, Score, Type) VALUES ('$movieNumber', '$choiceNumber', 'N/A - I was sleeping during this movie', '0', 'sel')";
	$result = $rcx->query($query);
	if (!$result)
	{
		echo "Failed to insert question $movieNumber choice $choiceNumber<br>";
	}
	$choiceNumber++;
	
	$query = "INSERT INTO QUIZ_CHOICES (QuestionID, Ordering, Choice, Score, Type) VALUES ('$movieNumber', '$choiceNumber', '', '0', 'essay')";
	$result = $rcx->query($query);
	if (!$result)
	{
		echo "Failed to insert question $movieNumber choice $choiceNumber<br>";
	}
	$choiceNumber++;
	
	$movieNumber++;
}

echo "Finished Populating Survey<br>";

?>