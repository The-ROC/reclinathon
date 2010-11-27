<?php
session_start();
include "templateHead.html";
?>

<FONT COLOR = "white"><BR><BR>

<?php



if($answer3 == "a") {
  $SCORE -= 66;
}

else if($answer3 == "b") {
  $SCORE -= 88;
}

else if($answer3 == "c") {
  $SCORE -= 2;
}

else if($answer3 == "d") {
  $SCORE += 10;
}

else if($answer3 == "e") {
  $SCORE -= 4;
}

else if($answer3 == "f") {
  $SCORE += 8;
}

else {
  $SCORE += 0;
}



session_register("answer3");
?>

<H1>Commitment Quiz</H1>

<H3> This section will determine your numerical commitment level to the relinathon.  The first section deals with general reclinathon procedures and situations. <BR><BR>

<BR><BR><CENTER>
<FORM ACTION="register9.php" METHOD="post">
<TABLE WIDTH='60%' ><TR><TD><FONT COLOR = "white">
Chick Flick Mutiny:
By sitting in the LA-Z-DUDE CompuCliner, you have taken on the responsibilities of Reclinathon Captain during your tenure of ensconcement.  A certain Reclinathon participant has repeatedly vocalized his strong desire to go dancing, and this crates a fervor in the arena to watch the movie “Shall we Dance” starring Jennifer Lopez.  Riots ensue, and soon you have a mutiny on your hands.  As the captain, you are responsible for quelling the odious display of chick-flickerry.  What are your orders?


</TD></TR></TABLE>
<BR><BR>
<TABLE WIDTH='60%' CELLSPACING='7'>
<TR><TD><FONT COLOR = "white">Rig up My Pet Monster with a yo-yo-like attachment to quickly fend off multiple attackers.</TD><TD><INPUT TYPE="radio" NAME="answer4" VALUE="a"></TD></TR>
<TR><TD><FONT COLOR = "white">Don’t worry about it.  Do the gay letting the mutineers win routine.</TD><TD><INPUT TYPE="radio" NAME="answer4" VALUE="b"></TD></TR>
<TR><TD><FONT COLOR = "white">Exploit the known weakness of the chief mutineer by creating a Pizza Bianca diversion.</TD><TD><INPUT TYPE="radio" NAME="answer4" VALUE="c"></TD></TR>
<TR><TD><FONT COLOR = "white">I don’t know.  I didn’t understand any of the words in the question.</TD><TD><INPUT TYPE="radio" NAME="answer4" VALUE="d"></TD></TR>
<TR><TD><FONT COLOR = "white">RUN AWAY!</TD><TD><INPUT TYPE="radio" NAME="answer4" VALUE="e"></TD></TR>
<TR><TD><FONT COLOR = "white">Emergency Procedure:  Fall Asleep.</TD><TD><INPUT TYPE="radio" NAME="answer4" VALUE="f"></TD></TR>
</TABLE><BR>
<INPUT TYPE="submit" VALUE="Continue">
</FORM>

<?php
include "templateTail.html";
?>