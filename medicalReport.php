<?php
session_start();
include "templateHead.html";
?>


<FONT COLOR = "white"><BR>

<H1>Medical Evaluation</H1>

<?php

if($bladder == '') {
  echo "<H1>You have not entered all the required fields.  Go back and try again.";
  exit();
}

session_register("heart");
session_register("blood");
session_register("seizure");
session_register("bowel");
session_register("pregnant");
session_register("sitting");
session_register("buffalo");
session_register("talking");
session_register("bladder");
session_register("chipper");
session_register("death");


$medicalScore = 0;

if($blood == "yes") {
  $medicalScore -= 5;
  echo "Low blood pressure can lead to serious injury, or death, during a reclinathon, due to excess relaxation.  Please make sure to consume many salts, and be sure to excercise between movies.";
  echo "<BR><BR>";
}

if($seizure == "yes") {
  $medicalScore -= 2;
  echo "To avoid becoming a mouth-foaming distraction, please don't watch pokemon or any other rapid display of flashing lights during reclinathon.";
  echo "<BR><BR>";
}

if($bowel == "yes") {
  $medicalScore -= 7;
  echo "We insist that your recliner during the reclinathon be the toilet.  Non-compliance to this rule will not be tolerated.";
  echo "<BR><BR>";
}

if($pregnant == "yes") {
  $medicalScore += 1;
  echo "Congratulations on being pregnant.  You probably will want to move even less, which is always good, and your reclinathon counts double!";
  echo "<BR><BR>";
}

if($sitting == "yes") {
  $medicalScore -= 1000;
  echo "Reclinathon is not a disabled accessible event.  If you can't sit, you can't come to reclinathon.  Sorry.";
  echo "<BR><BR>";
}

if($buffalo == "yes") {
  $medicalScore -= 2;
  echo "Please be cautious of the buffalo wings, and all people who have touched the buffalo wings, during reclinathon.  If you do get in contact with buffalo sauce, immediately wash the affected area with Mountain Dew.";
  echo "<BR><BR>";
}

if($talking == "yes") {
  $medicalScore -= 5;
  echo "If you talk excessively during Reclinathon, you are at risk for serious injury, inflicted by the ROC.  You also risk others being disqualified, because they must stand up to beat you.  Be forwarned.";
  echo "<BR><BR>";
}

$medicalScore -= (10 - $bladder);
if($bladder <= 6) {
  echo "Make sure to go to the bathroom between EVERY movie.  This will ensure comfort for everyone.";
  echo "<BR><BR>";
}

if($chipper == "yes") {
  $medicalScore += 1;
  echo "Congratulations on a job well done.  Please describe to the group exactly what it was like inside the wood chipper.  Also, watch out for Steve Buscemi during reclinathon.";
  echo "<BR><BR>";
}

if($death == "Sometimes") {
  $medicalScore += 2;
  echo "If you have experienced sudden death, you are dead, and thus would be very good at Reclinathon.  Please do us the favor of dousing yourself with formaldehyde to take away your stench.";
  echo "<BR><BR>";
}

else if($death == "yes") {
  $medicalScore += 1;
  echo "If you have experienced sudden death, you are dead, and thus would be very good at Reclinathon.  Please do us the favor of dousing yourself with formaldehyde to take away your stench.";
  echo "<BR><BR>";
}

if($medicalScore <= -5) {
  echo "We apologize, but you are currently medically unfit to attend reclinathon.  We at the RAA take the health and happiness of our reclinees as our highest priority, and we feel that reclinathon might be a danger to your health and the confort of others.";
  echo "We hope that you recover soon, so that you will eventually be able to attend future reclinathons.  For the time being, you are, regettably, blacklisted.  Best wishes.<BR><BR>--The ROC";
  echo "<BR><BR>";
  include "blacklist_me.php";
  include "templateTail.html";
  exit();
}

echo "Congratulations!  You are medically fit for reclinathon.  If you received any health warnings above, please pay careful attention to them.<BR><BR>";

?>

<CENTER>
<FORM ACTION="register5.php" METHOD="post">
<INPUT TYPE="submit" VALUE="Continue">
</FORM>

<?php
include "templateTail.html";
?>