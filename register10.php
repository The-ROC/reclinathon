<?php
session_start();
include "templateHead.html";
?>

<FONT COLOR = "white"><BR><BR>

<?php



if($answer5 == "a") {
  $SCORE += 10;
}

else if($answer5 == "b") {
  $SCORE += 7;
}

else if($answer5 == "c") {
  $SCORE += 8;
}

else if($answer5 == "d") {
  $SCORE += 2;
}

else if($answer5 == "e") {
  $SCORE += 9;
}

else if($answer5 == "f") {
  $SCORE += 0;
}

else {
  $SCORE += 0;
}






session_register("answer5");
?>

<H1>Commitment Quiz</H1>

<H3> The current section of the commitment quiz is the Astrophysics section.  How fun. </H3>

<CENTER>
<FORM ACTION="register11.php" METHOD="post">
<TABLE WIDTH='90%' ><TR><TD><FONT COLOR = "white">
You have been diagnosed with Gluteus Lazine of Overt Plumbum (G.L.O.O.P., or obviously lazy lead rump syndrome) which requires you to participate in a reclinathon every week.  Unfortunately, reclinathons are only held once a year…what to do?
<BR><BR>
Your friend, Hercules Q. Einstein, comes up with a brilliant idea: why don’t you get on a rocket, traveling at close to the speed of light, and let time dilation take care of the rest.  
<BR><BR>
Assuming you can neglect any acceleration effects (and pretending you are in a flat, but toroidal, space to neglect turn around time) how fast would your rocket have to be traveling in order to make one year seem like one week in your reference frame, and thus save yourself from the ravaging effects of G.L.O.O.P?  (i.e. solve for v to the nearest integer)
<BR><BR>
Useful Constants and Formulas:<BR><BR>
t = gamma*tau   (time dilation equation)<BR>
gamma = 1/sqrt((1 – ß^2))    (Lorentz gamma factor)<BR>
ß = v/c   (relativistic ratio)<BR>
c = 3 x 10^8 m/s (speed of light in vacuum)<BR>
t = earth time<BR>
tau = rocket time<BR>
</TD></TR></TABLE>
<BR>
v = <INPUT TYPE="text" NAME="answer6" SIZE='5'><BR>or<BR>
<INPUT TYPE="checkbox" NAME="answer6alt" VALUE="1"> Dave, you suck.<BR><BR>
<INPUT TYPE="submit" VALUE="Continue">
</FORM>

<?php
include "templateTail.html";
?>