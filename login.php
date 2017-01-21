<?php 
session_start();
?>

<html>
<head>
<title>Enter Your Command Center</title>
<link rel="stylesheet" type="text/css" href="css/index_new.css" />
</head>

<body class="noborder">

<?php
$currentPage = "command center";
 include "header.php";
?>

<div class="main">
<div class="content" align="left">

<?php
if ($_GET["message"] != "")
{
   echo $_GET["message"]; 
}
?>

<h1>
Enter your Command Center.
</h1>

<form action="dologin.php" method="post">
Username:
<input type="text" name="username" />
Password:
<input type="password" name="password" />
<input type="submit" value="Go" />
</form>

<br />
<br />
<form action="forgotpassword.php" method="get">
Forgot your login information?  Enter your email address to reset your password. <br />
<input type="text" name="email" />
<input type="submit" value="Reset Password" />

</div>
</div>
</html>


