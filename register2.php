<?php
session_start();
include "templateHead.html";
?>


<FONT COLOR = "white"><BR><BR>

<?php
// Comment Comment
// comment Comment 2
if($FirstName == '' || $LastName == '' || $Email == '' || $Status == '') {
  echo "<H1>You have not entered all of the required information.  Go back and try again.";
  exit();
}

if(!session_is_registered("FNAME")) {
  $FNAME = $FirstName;
  session_register("FNAME");
}

if(!session_is_registered("LNAME")) {
  $LNAME = $LastName;
  session_register("LNAME");
}

if(!session_is_registered("EMAIL")) {
  $EMAIL = $Email;
  session_register("EMAIL");
}

?>

<H3>Verification Question for <?php echo $Status; ?></H3><BR><BR>  

<CENTER><FORM ACTION="register3.php" METHOD="post">
<TABLE WIDTH="40%" CELLSPACING='6'>
<TR><TD><FONT COLOR = "white">

<?php

if($Status == 'Reclining Machine') {
  echo "When did the Mongols rule China?";
  echo "<INPUT TYPE='text' NAME='answer'>";
}

else if($Status == 'Advanced Recliner') {
  echo "How much does 30 pounds of chocolate cost?<BR><BR>";
  echo "$<INPUT TYPE='text' NAME='answer' SIZE='3'>";
}

else if($Status == 'Intermediate Recliner') {
  echo "Which of the following actors has had a role in a movie shown at an official RAA Reclinathon.<BR><BR>";
  echo "<TABLE WIDTH='80%'><TR><TD><INPUT TYPE='radio' NAME='answer' VALUE='1'></TD><TD>Hugh Grant</TD></TR>";
  echo "<TR><TD><INPUT TYPE='radio' NAME='answer' VALUE='2'></TD><TD>Michael Caine</TD></TR>";
  echo "<TR><TD><INPUT TYPE='radio' NAME='answer' VALUE='3'></TD><TD>John Ratzenberger.</TD></TR>";
  echo "<TR><TD><INPUT TYPE='radio' NAME='answer' VALUE='4'></TD><TD>Jim Caviezel</TD></TR></TABLE>";
}

else if($Status == 'Novice Recliner') {
  echo "Which of the following is against RAA rules and regulations for a reclinathon?<BR><BR>";
  echo "<TABLE WIDTH='80%'><TR><TD><INPUT TYPE='radio' NAME='answer' VALUE='1'></TD><TD>Booing at the end of a movie.</TD></TR>";
  echo "<TR><TD><INPUT TYPE='radio' NAME='answer' VALUE='2'></TD><TD>Standing up to stretch your legs during a movie.</TD></TR>";
  echo "<TR><TD><INPUT TYPE='radio' NAME='answer' VALUE='3'></TD><TD>Asking your neighbor to get you a Mountain Dew during a movie.</TD></TR>";
  echo "<TR><TD><INPUT TYPE='radio' NAME='answer' VALUE='4'></TD><TD>Falling asleep during a movie.</TD></TR></TABLE>";
}

else if($Status == 'First Time Recliner' || $Status == 'Exhibition Recliner') {
  echo "What is better?<BR><BR>";
  echo "<TABLE WIDTH='80%'><TR><TD><INPUT TYPE='radio' NAME='answer' VALUE='1'></TD><TD>Sitting in a recliner for 26.2 hours.</TD></TR>";
  echo "<TR><TD><INPUT TYPE='radio' NAME='answer' VALUE='0'></TD><TD>Doing Jumping Jacks for 26.2 hours.</TD></TR></TABLE>";
}

else {
  echo "problem";
  exit();
}

?>

</TD></TR></TABLE><BR><BR>
<INPUT TYPE="hidden" NAME="Status" VALUE="<?php echo $Status; ?>">
<INPUT TYPE="submit" VALUE="Continue">




<?php
include "templateTail.html";
?>
