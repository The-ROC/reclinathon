<html>
<head>
<title>Reclinathon Login</title>
<link rel="stylesheet" type="text/css" href="index_new.css" />
</head>

<body class="noborder">
<?php
// Comment
$currentPage = "login";
include "header.php";
include "include/connect.php";
?>

<div class="main">
<center>
<div class="content" align="left">
<br />
<h1>Enter your Command Center.</h1>

<?php echo $_GET["message"]; ?>

<form action="dologin.php" method="post">
<table width="60%" cellspacing='6'>
<tr><td><font color=#ffffff>Username:</td><td><input type="text" name="username" value="<?php echo $_GET["username"]; ?>"></td></tr>
<tr><td><font color=#ffffff>Password:</td><td><input type="password" name="password"</td></tr>
</table><br />
<INPUT TYPE="submit" VALUE="Continue">
</form>

</div>
</center>
</div>

</body>
</html>
