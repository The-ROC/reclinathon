<?php
session_start();
include "templateHead.html";
?>

<FONT COLOR = "white"><BR><BR>

<?php

if($answer1 == "a") {
  $SCORE -= 50;
}

else if($answer1 == "b") {
  $SCORE += 3;
}

else if($answer1 == "c") {
  $SCORE += 0;
}

else if($answer1 == "d") {
  $SCORE += 2;
}

else if($answer1 == "e") {
  $SCORE -= 3;
}

else if($answer1 == "f") {
  $SCORE += 5;
}

else {
  $SCORE += 0;
}


session_register("answer1");
?>

<H1>Commitment Quiz</H1>

<H3> This section will determine your numerical commitment level to the relinathon.  The first section deals with general reclinathon procedures and situations. <BR><BR>

<BR><BR><CENTER>
<FORM ACTION="register7.php" METHOD="post">
<TABLE WIDTH='60%' ><TR><TD><FONT COLOR = "white">
Pizza Disaster:
The pizza has just been ordered and you are the food liaison (aka sitting in the seat next to the door).  In the middle of a movie, you receive a distress call on your cell phone, which is silenced of course, from the delivery man who explains that he has fallen in the rear parking lot of Reclinathon and has broken his leg.  What do you do?

</TD></TR></TABLE>
<BR><BR>
<TABLE WIDTH='60%' CELLSPACING='7'>
<TR><TD><FONT COLOR = "white">Call the pizza place and calmly explain to them that if they want to be invited to sponsor Reclinathon in the future that they should make arrangements to get the pizza to your office (aka the recliner next to the door) ASAP.</TD><TD><INPUT TYPE="radio" NAME="answer2" VALUE="a"></TD></TR>
<TR><TD><FONT COLOR = "white">Immediately get up, go outside, and help the poor pizza man.</TD><TD><INPUT TYPE="radio" NAME="answer2" VALUE="b"></TD></TR>
<TR><TD><FONT COLOR = "white">Immediately get up, go outside, and get the pizza.</TD><TD><INPUT TYPE="radio" NAME="answer2" VALUE="c"></TD></TR>
<TR><TD><FONT COLOR = "white">Call 911</TD><TD><INPUT TYPE="radio" NAME="answer2" VALUE="d"></TD></TR>
<TR><TD><FONT COLOR = "white">Fashion a crude rescue device out of blankets and pee pee canteens</TD><TD><INPUT TYPE="radio" NAME="answer2" VALUE="e"></TD></TR>
<TR><TD><FONT COLOR = "white">Emergency Procedure:  Fall Asleep.</TD><TD><INPUT TYPE="radio" NAME="answer2" VALUE="f"></TD></TR>
</TABLE><BR>
<INPUT TYPE="submit" VALUE="Continue">
</FORM>

<?php
include "templateTail.html";
?>