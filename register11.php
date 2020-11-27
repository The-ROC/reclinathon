<?php
session_start();
include $_SERVER['DOCUMENT_ROOT']."\include\connect.php";
include "templateHead.html";
?>

<FONT COLOR = "white"><BR><BR>

<?php


if($answer6 == 299944898 | $answer6 == 299944819) {
  $SCORE += 10;
}

else if($answer6 >= 250000000) {
  $SCORE += 6;
}

else if($answer6 == "42") {
  $SCORE += 4;
}

else {
  if($answer6alt == 1) {
    $SCORE -= 2;
  }

  else {
    $SCORE += 0;
  }

}


session_register("answer6");
session_register("answer6alt");

$commitment = 1;

if($SCORE >= 44) {
  $commitment = 10;
}

else if( $SCORE < 44 && $SCORE >= 40 ) {
  $commitment = 9;
}

else if( $SCORE < 40 && $SCORE >= 35 ) {
  $commitment = 8;
}

else if( $SCORE < 35 && $SCORE >= 30 ) {
  $commitment = 7;
}

else if( $SCORE < 30 && $SCORE >= 25 ) {
  $commitment = 6;
}

else if( $SCORE < 25 && $SCORE >= 20 ) {
  $commitment = 5;
}

else if( $SCORE < 20 && $SCORE >= 13 ) {
  $commitment = 4;
}

else if( $SCORE < 13 && $SCORE >= 7 ) {
  $commitment = 3;
}

else if( $SCORE < 7 && $SCORE >= 4 ) {
  $commitment = 2;
}

else if( $SCORE < 4 && $SCORE >= 0 ) {
  $commitment = 1;
}

else {
  echo "You are not a dude.<BR><BR>";
  include "blacklist_me.php";
  include "templateTail.html";
  exit();
}


?>



<?php
/*
echo $FNAME."<BR>";
echo $LNAME."<BR>";
echo $EMAIL."<BR>";
echo $STATUS."<BR>";
echo $heart."<BR>";
echo $blood."<BR>";
echo $seizure."<BR>";
echo $bowel."<BR>";
echo $pregnant."<BR>";
echo $sitting."<BR>";
echo $buffalo."<BR>";
echo $talking."<BR>";
echo $bladder."<BR>";
echo $chipper."<BR>";
echo $death."<BR>";
echo $answer1."<BR>";
echo $answer2."<BR>";
echo $answer3."<BR>";
echo $answer4."<BR>";
echo $answer5."<BR>";
echo $answer6."<BR>";
echo $answer6alt."<BR><BR><BR><BR>";
echo $SCORE;
*/
?>

<BR><BR><BR>

<CENTER>
<FONT SIZE="+2"><B>Your Commitment Level is:  <?php echo $commitment; ?> <BR><BR>Welcome to Reclinathon!<BR><BR></FONT>

<?php

 $query = $db->prepare(
   "insert into Reclinees (FirstName, LastName, Email, Status, Commitment) values (?, ?, ?, ?, ?)"
 );
 $query->bind_param('sssss', $FNAME, $LNAME, $EMAIL, $STATUS, $commitment);
 
  $result = db_query($db, $query);

  if ($result != TRUE){ 
      echo "We have a problem.";
  } 

  session_destroy();


include "templateTail.html";
?>