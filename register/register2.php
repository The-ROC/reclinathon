<?php
set_include_path("/kunden/homepages/6/d95429370/htdocs/reclinathon");
require_once "config.php";
?>

<html>
<head>
<title>Reclinathon Registration</title>
<link rel="stylesheet" type="text/css" href=<?php echo BASE_URL . "css/index_new.css"?> />
<link rel="stylesheet" type="text/css" href=<?php echo BASE_URL . "css/register.css"?> />
</head>

<body class="noborder">
<?php
session_start();
$currentPage = "register";
require(BASE_PATH . "header.php");
?>

<div class="main">
<center>
<div class="container" align="left">
<img src=<?php echo BASE_URL . "images/sign_on_small"?> /><br />
<center>
<div class="content" align="left">


<?php
// Comment Comment
// comment Comment 2
if($firstname == '' || $lastname == '' || $email == '' || $status == '') {
  echo "<H1>You have not entered all of the required information.  Go back and try again.";
  exit();
}

if(!session_is_registered("FNAME")) {
  $FNAME = $firstname;
  session_register("FNAME");
}

if(!session_is_registered("LNAME")) {
  $LNAME = $lastname;
  session_register("LNAME");
}

if(!session_is_registered("EMAIL")) {
  $EMAIL = $email;
  session_register("EMAIL");
}

?>


<br />
<h1 class="heading">Verification Question for:<br /> <?php echo $status; ?></h1> 
<br />

<CENTER><FORM ACTION="../register3.php" METHOD="post">

<?php

if($status == 'Reclining Machine') {
  echo "<h3>When did the Mongols rule China?</h3>";
  echo "<INPUT TYPE='text' NAME='answer'>";
}

else if($status == 'Advanced Recliner') {
  echo "<h3>How much does 30 pounds of chocolate cost?</h3>";
  echo "$<INPUT TYPE='text' NAME='answer' SIZE='3'>";
}

else if($status == 'Intermediate Recliner') {
  echo "<h3>Which of the following actors has had a role in a movie shown at an official RAA Reclinathon.</h3>";
  echo "<TABLE><TR><TD><INPUT TYPE='radio' NAME='answer' VALUE='1'></TD><TD>Hugh Grant</TD></TR>";
  echo "<TR><TD><INPUT TYPE='radio' NAME='answer' VALUE='2'></TD><TD>Michael Caine</TD></TR>";
  echo "<TR><TD><INPUT TYPE='radio' NAME='answer' VALUE='3'></TD><TD>John Ratzenberger.</TD></TR>";
  echo "<TR><TD><INPUT TYPE='radio' NAME='answer' VALUE='4'></TD><TD>Jim Caviezel</TD></TR></TABLE>";
  echo "<br />";
}

else if($status == 'Novice Recliner') {
  echo "<h3>Which of the following is against RAA rules and regulations for a reclinathon?</h3>";
  echo "<TABLE><TR><TD><INPUT TYPE='radio' NAME='answer' VALUE='1'></TD><TD>Booing at the end of a movie.</TD></TR>";
  echo "<TR><TD><INPUT TYPE='radio' NAME='answer' VALUE='2'></TD><TD>Standing up to stretch your legs during a movie.</TD></TR>";
  echo "<TR><TD><INPUT TYPE='radio' NAME='answer' VALUE='3'></TD><TD>Asking your neighbor to get you a Mountain Dew during a movie.</TD></TR>";
  echo "<TR><TD><INPUT TYPE='radio' NAME='answer' VALUE='4'></TD><TD>Falling asleep during a movie.</TD></TR></TABLE>";
  echo "<br />";
}

else if($status == 'First Time Recliner' || $status == 'Exhibition Recliner') {
  echo "<h3>What is better?</h3>";
  echo "<TABLE><TR><TD><INPUT TYPE='radio' NAME='answer' VALUE='1'></TD><TD>Sitting in a recliner for 26.2 hours.</TD></TR>";
  echo "<TR><TD><INPUT TYPE='radio' NAME='answer' VALUE='0'></TD><TD>Doing Jumping Jacks for 26.2 hours.</TD></TR></TABLE>";
  echo "<br />";
}

else {
  echo "problem";
  exit();
}

?>

<INPUT TYPE="hidden" NAME="status" VALUE="<?php echo $status; ?>">
<INPUT TYPE="submit" VALUE="Continue">




<?php
include "templateTail.html";
?>
