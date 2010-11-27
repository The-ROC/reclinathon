<?php
	$answer = $_POST['answer'];
	
	//print_r($_POST);
	
	$correct = FALSE;
	$answerList = array("Abenaki", "Abanaki", "Abinaki", "Abonaki", 
						"Abbenaki", "Abbanaki", "Abbinaki", "Abbonaki",
						"Abenacki", "Abanacki", "Abinacki", "Abonacki",
						"Abbenacki", "Abbanacki", "Abbinacki", "Abbonacki",
						
						"Abenaky", "Abanaky", "Abinaky", "Abonaky",
						"Abbenaky", "Abbanaky", "Abbinaky", "Abbonaky",
						"Abenacky", "Abanacky", "Abinacky", "Abonacky",
						"Abbenacky", "Abbanacky", "Abbinacky", "Abbonacky",
						
						"Abenakee", "Abanakee", "Abinakee", "Abonakee",
						"Abbenakee", "Abbanakee", "Abbinakee", "Abbonakee",
						"Abenackee", "Abanackee", "Abinackee", "Abonackee",
						"Abbenackee", "Abbanackee", "Abbinackee", "Abbonackee",
						
						
						"Abenakis", "Abanakis", "Abinakis", "Abonakis", 
						"Abbenakis", "Abbanakis", "Abbinakis", "Abbonakis",
						"Abenackis", "Abanackis", "Abinackis", "Abonackis",
						"Abbenackis", "Abbanackis", "Abbinackis", "Abbonackis",
						
						"Abenakies", "Abanakies", "Abinakies", "Abonakies",
						"Abbenakies", "Abbanakies", "Abbinakies", "Abbonakies",
						"Abenackies", "Abanackies", "Abinackies", "Abonackies",
						"Abbenackies", "Abbanackies", "Abbinackies", "Abbonackies",
						
						"Abenakees", "Abanakees", "Abinakees", "Abonakees",
						"Abbenakees", "Abbanakees", "Abbinakees", "Abbonakees",
						"Abenackees", "Abanackees", "Abinackees", "Abonackees",
						"Abbenackees", "Abbanackees", "Abbinackees", "Abbonackees",
						);
						
	foreach($answerList as $correctAnswer)
	{
		//echo("Mine: " . $answer . "Correct: " . $correctAnswer . "<br>");
		if(strcasecmp($answer, $correctAnswer) == 0)
		{
			$correct = TRUE;
			break;
		}
	}
	
	$url = "voterfraud.php?error=1";
	if($correct)
	{
		$url = "display.php";
	}

	header("Location: " . $url);
	
	//echo("Url: " . $url);
?>
