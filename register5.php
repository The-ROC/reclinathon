<?php
session_start();
include "templateHead.html";

if(!session_is_registered("SCORE")) {
  $SCORE = 0;
  session_register("SCORE");
}

?>


<FONT COLOR = "white"><BR><BR>


<H1>Commitment Quiz</H1>

<H3> This section will determine your numerical commitment level to the relinathon.  The first section deals with general reclinathon procedures and situations. <BR><BR>

<BR><BR><CENTER>
<FORM ACTION="register6.php" METHOD="post">
<TABLE WIDTH='60%' ><TR><TD><FONT COLOR = "white">
Which of the following is an acceptable alternative to waiting in line for the bathroom?
</TD></TR></TABLE>
<BR><BR>
<TABLE WIDTH='60%' CELLSPACING='7'>
<TR><TD><FONT COLOR = "white">Going to the bathroom in the middle of the movie to avoid the rush.</TD><TD><INPUT TYPE="radio" NAME="answer1" VALUE="a"></TD></TR>
<TR><TD><FONT COLOR = "white">Pee pee canteen.</TD><TD><INPUT TYPE="radio" NAME="answer1" VALUE="b"></TD></TR>
<TR><TD><FONT COLOR = "white">Using the upstairs bathroom instead.</TD><TD><INPUT TYPE="radio" NAME="answer1" VALUE="c"></TD></TR>
<TR><TD><FONT COLOR = "white">BYOCathetar</TD><TD><INPUT TYPE="radio" NAME="answer1" VALUE="d"></TD></TR>
<TR><TD><FONT COLOR = "white">Becoming the new water feature in the reclinathon arena.</TD><TD><INPUT TYPE="radio" NAME="answer1" VALUE="e"></TD></TR>
<TR><TD><FONT COLOR = "white">Emergency Procedure:  Fall Asleep.</TD><TD><INPUT TYPE="radio" NAME="answer1" VALUE="f"></TD></TR>
</TABLE><BR>
<INPUT TYPE="submit" VALUE="Continue">
</FORM>

<?php
include "templateTail.html";
?>