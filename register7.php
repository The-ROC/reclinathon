<?php
session_start();
include "templateHead.html";
?>

<FONT COLOR = "white"><BR><BR>

<?php


if($answer2 == "a") {
  $SCORE += 5;
}

else if($answer2 == "b") {
  $SCORE -= 5;
}

else if($answer2 == "c") {
  $SCORE -= 2;
}

else if($answer2 == "d") {
  $SCORE += 1;
}

else if($answer2 == "e") {
  $SCORE += 4;
}

else if($answer2 == "f") {
  $SCORE += 0;
}

else {
  $SCORE += 0;
}


session_register("answer2");
?>

<H1>Commitment Quiz</H1>

<H3> This section will determine your numerical commitment level to the relinathon.  The first section deals with general reclinathon procedures and situations. <BR><BR>

<BR><BR><CENTER>
<FORM ACTION="register8.php" METHOD="post">
<TABLE WIDTH='60%' ><TR><TD><FONT COLOR = "white">
A movie you really want to see has not yet been selected as an official Reclinathon movie.  You are very disappointed.  What do you do?

</TD></TR></TABLE>
<BR><BR>
<TABLE WIDTH='60%' CELLSPACING='7'>
<TR><TD><FONT COLOR = "white">Whine and cry a lot, hoping that your pleas will move the selection committee to pick your choice next.</TD><TD><INPUT TYPE="radio" NAME="answer3" VALUE="a"></TD></TR>
<TR><TD><FONT COLOR = "white">Run up and harass the ROC…especially by inviting yourself to sit in on the next ROC selection meeting.</TD><TD><INPUT TYPE="radio" NAME="answer3" VALUE="b"></TD></TR>
<TR><TD><FONT COLOR = "white">Get up and leave…Reclinathon is stupid anyway.</TD><TD><INPUT TYPE="radio" NAME="answer3" VALUE="c"></TD></TR>
<TR><TD><FONT COLOR = "white">Be a dude…shut up and take it like a man.</TD><TD><INPUT TYPE="radio" NAME="answer3" VALUE="d"></TD></TR>
<TR><TD><FONT COLOR = "white">Sabotage the Reclinathon by replacing the next movie with “Pauly Shore is Dead”</TD><TD><INPUT TYPE="radio" NAME="answer3" VALUE="e"></TD></TR>
<TR><TD><FONT COLOR = "white">Emergency Procedure:  Fall Asleep.</TD><TD><INPUT TYPE="radio" NAME="answer3" VALUE="f"></TD></TR>
</TABLE><BR>
<INPUT TYPE="submit" VALUE="Continue">
</FORM>

<?php
include "templateTail.html";
?>