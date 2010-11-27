<?php
session_start();
include "templateHead.html";
?>

<FONT COLOR = "white"><BR><BR>

<?php




if($answer4 == "a") {
  $SCORE += 10;
}

else if($answer4 == "b") {
  $SCORE -= 2;
}

else if($answer4 == "c") {
  $SCORE += 5;
}

else if($answer4 == "d") {
  $SCORE += 2;
}

else if($answer4 == "e") {
  $SCORE -= 1;
}

else if($answer4 == "f") {
  $SCORE += 0;
}

else {
  $SCORE += 0;
}





session_register("answer4");
?>

<H1>Commitment Quiz</H1>

<H3> This section will determine your numerical commitment level to the relinathon.  The first section deals with general reclinathon procedures and situations. <BR><BR>

<BR><BR><CENTER>
<FORM ACTION="register10.php" METHOD="post">
<TABLE WIDTH='60%' ><TR><TD><FONT COLOR = "white">
Chick Flick Mutiny:
Your decisive leadership skills have won the day.  The mutiny has been quelled.  What is a fitting punishment for the defeated scalawags?


</TD></TR></TABLE>
<BR><BR>
<TABLE WIDTH='60%' CELLSPACING='7'>
<TR><TD><FONT COLOR = "white">Create an extra section of seating waaaaaaay in the back of the arena, name it “The Mutineer Section”, and direct all of the mutineers to go and sit in it and solemnly think about what they’ve done.</TD><TD><INPUT TYPE="radio" NAME="answer5" VALUE="a"></TD></TR>
<TR><TD><FONT COLOR = "white">Make them all write, “I am very sorry for what I did to the captain” a hundred zillion times.</TD><TD><INPUT TYPE="radio" NAME="answer5" VALUE="b"></TD></TR>
<TR><TD><FONT COLOR = "white">Move them to an isolation chamber where they are forced to watch “The Cure for Insomnia” in its entirety, followed by “Mulholland Drive”, and subsequently followed by “Time Bandits” without falling asleep.</TD><TD><INPUT TYPE="radio" NAME="answer5" VALUE="c"></TD></TR>
<TR><TD><FONT COLOR = "white">I still don’t know what the hell this question is about.</TD><TD><INPUT TYPE="radio" NAME="answer5" VALUE="d"></TD></TR>
<TR><TD><FONT COLOR = "white">Put them all through wood chippers.</TD><TD><INPUT TYPE="radio" NAME="answer5" VALUE="e"></TD></TR>
<TR><TD><FONT COLOR = "white">Emergency Procedure:  Fall Asleep.</TD><TD><INPUT TYPE="radio" NAME="answer5" VALUE="f"></TD></TR>
</TABLE><BR>
<INPUT TYPE="submit" VALUE="Continue">
</FORM>

<?php
include "templateTail.html";
?>