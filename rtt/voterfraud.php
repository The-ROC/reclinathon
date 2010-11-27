<?php
	$error = $_GET['error'];
?>

<HTML>

<HEAD><TITLE>Voter Fraud Protection</TITLE></HEAD>

<BODY>
<CENTER>

<FONT SIZE="9"><STRONG>Voter Fraud Protection</STRONG></FONT>
<?php
	if($error == 1)
	{
		echo("<br /><font color='red'>Incorrect answer. Please try again.</font>");
	}
?>
<BR /><BR />
Hello, Dude.  Please answer the following question to verify your identity, and help protect the Reclinathon from voter fraud.

<BR /><BR /><BR />

<FORM action="voterfraudproc.php" method="post">

<STRONG>Question</STRONG><BR />
What is the name of the Indian tribe who inhabits the famous Indian Caves in Springdale, Pennsylvania - A quiet hamlet northeast of Pittsburgh?
<BR /><BR />

<STRONG>Answer</STRONG><BR />
<INPUT type="text" name="answer">
<INPUT type="submit" value="Submit">

</FORM>
</CENTER>
</BODY>

</HTML>