<html>
<head>
<title>Register for Reclinathon</title>
<link rel="stylesheet" type="text/css" href="css/index_new.css" />
</head>

<body class="noborder">
<?php
session_start();
$currentPage = "register";
include "header.php";
?>

<div class="main">
<center>
<div class="content" align="left">
<br />
<h1 class="heding">Are you Ready to Recline?</h1>
<br /><br />
<H3>Use this form to register for the current reclinathon.  You will also be given a qualification exam to determine your reclining level.</H3><BR><BR>  

<CENTER><FORM ACTION="register2.php" METHOD="post">
<TABLE WIDTH="60%" CELLSPACING='6'>
<TR><TD><FONT COLOR = "white">First Name:</TD><TD><INPUT TYPE="text" NAME="FirstName" VALUE="<?php echo $FNAME;?>"></TD></TR>
<TR><TD><FONT COLOR = "white">Last Name:</TD><TD><INPUT TYPE="text" NAME="LastName" VALUE="<?php echo $LNAME;?>"></TD></TR>
<TR><TD><FONT COLOR = "white">Email:</TD><TD><INPUT TYPE="text" NAME="Email" VALUE="<?php echo $EMAIL;?>"></TD></TR>
<TR><TD><FONT COLOR = "white">Status:</TD><TD>
<SELECT NAME="Status">
	<?php if($limit == '') {$limit = 5; } ?>
	<OPTION VALUE="Exhibition Recliner">Exhibition Recliner</OPTION>
	<OPTION VALUE="First Time Recliner">First Time Recliner</OPTION>
	<?php if($limit > 1) echo "<OPTION VALUE='Novice Recliner'>Novice Recliner</OPTION>"; ?>
	<?php if($limit > 2) echo "<OPTION VALUE='Intermediate Recliner'>Intermediate Recliner</OPTION>"; ?>
	<?php if($limit > 3) echo "<OPTION VALUE='Advanced Recliner'>Advanced Recliner</OPTION>"; ?>
	<?php if($limit > 4) echo "<OPTION VALUE='Reclining Machine'>Reclining Machine</OPTION>"; ?>
</SELECT></TD></TR>
</TABLE><BR><BR>
<INPUT TYPE="submit" VALUE="Continue">
</div>
</center>
</div>

</body>
</html>
