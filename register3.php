<?php
session_start();
include "templateHead.html";
?>


<FONT COLOR = "white"><BR><BR>

<?php

if($answer == '') {
  echo "<H1>You have not answered the question.  Go back and try again.";
  exit();
}

?>

<H3>Verification Question for <?php echo $Status; ?></H3><BR><BR>  
<FORM ACTION="register4.php" METHOD="post">

<?php

if($Status == 'Exhibition Recliner' || $Status == 'First Time Recliner') {
  if($answer == '1') {
    $STATUS = $Status;
    session_register("STATUS");
    echo "<INPUT TYPE='submit' VALUE='You have answered wisely.  Continue'>";
  }
  else {
    echo "<CENTER><H3>";
    echo "You have answered incorrectly, therefore you do not understand the basic principles of reclinathon.<BR>You are awarded no points, and may God have mercy on your soul.<BR><BR><BR>";
    include "blacklist_me.php";
    include "templateTail.html";
    exit();
  }
}

else if($Status == 'Novice Recliner') {
  if($answer == '2') {
    $STATUS = $Status;
    session_register("STATUS");
    echo "<INPUT TYPE='submit' VALUE='You have answered wisely.  Continue'>";
  }
  else {
    echo "Wrong.  You will now be verified to the preceding reclining status. <META http-equiv=\"refresh\" content=\"5;URL=register.php?limit=1\">";
    exit();
  }
}

else if($Status == 'Intermediate Recliner') {
  if($answer == '3') {
    $STATUS = $Status;
    session_register("STATUS");
    echo "<INPUT TYPE='submit' VALUE='You have answered wisely.  Continue'>";
  }
  else {
    echo "Wrong.  You will now be verified to the preceding reclining status. <META http-equiv=\"refresh\" content=\"5;URL=register.php?limit=2\">";
    exit();
  }
}

else if($Status == 'Advanced Recliner') {
  if($answer == '5') {
    $STATUS = $Status;
    session_register("STATUS");
    echo "<INPUT TYPE='submit' VALUE='You have answered wisely.  Continue'>";
  }
  else {
    echo "Wrong.  You will now be verified to the preceding reclining status. <META http-equiv=\"refresh\" content=\"5;URL=register.php?limit=3\">";
    exit();
  }
}

else if($Status == 'Reclining Machine') {
  if($answer == "I don\'t know....I just work here.") {
    $STATUS = $Status;
    session_register("STATUS");
    echo "<INPUT TYPE='submit' VALUE='All hail the reclining machine!  Continue'>";
  }
  else {
    echo "Wrong.  You will now be verified to the preceding reclining status. <META http-equiv=\"refresh\" content=\"5;URL=register.php?limit=4\">";
    exit();
  }
}

?>

<?php
include "templateTail.html";
?>