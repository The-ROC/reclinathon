<?php
set_include_path($_SERVER["DOCUMENT_ROOT"]);
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
<br />
<h1 class="heading">Are you Ready to Recline?</h1>
<h3>Use this form to register for the current reclinathon.  You will also be given a qualification exam to determine your reclining level.</h3>
<br />
<center><form action="register_process.php" method="post">
<table width="60%" cellspacing='6'>
<tr><td><font color="white">First Name:</td><td><input type="text" name="firstname" value="<?php echo $FNAME;?>"></td></tr>
<TR><TD><FONT COLOR = "white">Last Name:</TD><TD><INPUT TYPE="text" NAME="lastname" VALUE="<?php echo $LNAME;?>"></TD></TR>
<TR><TD><FONT COLOR = "white">Email:</TD><TD><INPUT TYPE="text" NAME="email" VALUE="<?php echo $EMAIL;?>"></TD></TR>
<tr><td><font color=#ffffff>Username:</td><td><input type="text" name="username" value="<?php echo $USERNAME;?>"></td></tr>
<tr><td><font color=#ffffff>Password:</td><td><input type="password" name="password"</td></tr>
<TR><TD><FONT COLOR = "white">Status:</TD><TD>
<select name="status">
	<?php if($limit == '') {$limit = 5; } ?>
	<option value="Exhibition Recliner">Exhibition Recliner</option>
	<option value="First Time Recliner">First Time Recliner</option>
	<?php if($limit > 1) echo "<OPTION VALUE='Novice Recliner'>Novice Recliner</OPTION>"; ?>
	<?php if($limit > 2) echo "<OPTION VALUE='Intermediate Recliner'>Intermediate Recliner</OPTION>"; ?>
	<?php if($limit > 3) echo "<OPTION VALUE='Advanced Recliner'>Advanced Recliner</OPTION>"; ?>
	<?php if($limit > 4) echo "<OPTION VALUE='Reclining Machine'>Reclining Machine</OPTION>"; ?>
</select></td></tr>
<tr><td><input type="hidden" name="forward" value=<?php echo BASE_URL . "register/register2.php"; ?>></td></tr>
</table><br />
<INPUT TYPE="submit" VALUE="Continue">
</div>
</center>
</div>
</center>
</div>

</body>
</html>
