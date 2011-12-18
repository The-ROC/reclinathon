<?php
set_include_path($_SERVER['DOCUMENT_ROOT']);
require_once "config.php";
include "include/connect.php";
?>

<html>
<head>
<title>Reclinathon Registration</title>
<link rel="stylesheet" type="text/css" href=<?php echo BASE_URL . "css/index_new.css"?> />
<link rel="stylesheet" type="text/css" href=<?php echo BASE_URL . "css/register.css"?> />
</head>

<body class="noborder">
<?php
$currentPage = "register";
require(BASE_PATH . "header.php");
?>

<div class="main">
<center>
<div class="container" align="left">
<img src=<?php echo BASE_URL . "images/sign_on_small.png"?> /><br />
<center>
<div class="content" align="left">
<br />
<h1 class="heading">Are you Ready to Recline?</h1>
<h3>Use this form to register with the Reclinathon Association of America.  <br /><br /> Registering will help you stay up to date with all of the latest 
    reclinathon news.  After registering, you can login to your command center to sign up for Reclinathon events and vote for your favorite movies.<br />
</h3>
<br />

<?php

$FormValid = FALSE;
$UserAvailable = TRUE;
$UserAdded = FALSE;
$FirstName =  $_POST["firstname"];
$LastName = $_POST["lastname"];
$Email = $_POST["email"];
$UserName = $_POST["username"];
$Password = $_POST["password"];
$VerifyPassword = $_POST["verifypassword"];

if ($FirstName == "" ||
    $LastName == "" ||
    $Email == "" ||
    $UserName == "" ||
    $Password == "" ||
    $VerifyPassword == "")
{
    echo "Please enter all form fields.";
}
else if ($Password != $VerifyPassword)
{
    echo "The passwords you entered did not match.  Please try again.";
}
else
{
    $FormValid = TRUE;
}

if ($FormValid)
{
    $query = "SELECT * FROM RECLINEE WHERE FirstName = '" . $FirstName . "' AND LastName = '" . $LastName . "'";
    $result = mysql_query($query);
    if (mysql_num_rows($result) > 0)
    {
        echo "You have already registered with the Reclinathon Association of America.  Please log in to your account.";
        $UserAvailable = FALSE;
    }

    if ($UserAvailable)
    {
        $query = "SELECT * FROM RECLINEE WHERE UserName = '" . $UserName . "'";
        $result = mysql_query($query);
        if (mysql_num_rows($result) > 0)
        {
            echo "The UserName you specified has already been registered.  Please choose a new UserName and try again.";
            $UserAvailable = FALSE;
        }
    }
    
    if ($UserAvailable)
    {
    
        $query = "INSERT INTO RECLINEE (FirstName, LastName, DisplayName, Bio, RocMember, Email, UserName, PasswordHash) VALUES ('" . $FirstName . "'";
        $query = $query . ", '" . $LastName . "'";
        $query = $query . ", '" . $FirstName . "'";
        $query = $query . ", ' '";
        $query = $query . ", '0'";
        $query = $query . ", '" . $Email . "'";
        $query = $query . ", '" . $UserName . "'";
        $query = $query . ", '" . sha1($Password) . "')";

        $result = mysql_query($query);

        if ($result)
        {
            $UserAdded = TRUE;
            echo "Registration succeeded.";
            exit();
        }
        else
        {
            echo "Registration failed.  Please try again later.";
        }
    }
}

?>

<center><form action="register.php" method="post">
<table width="60%" cellspacing='6'>
<tr><td><font color="white">First Name:</td><td><input type="text" name="firstname" value="<?php echo $_POST["firstname"]; ?>"></td></tr>
<TR><TD><FONT COLOR = "white">Last Name:</TD><TD><INPUT TYPE="text" NAME="lastname" VALUE="<?php echo $_POST["lastname"]; ?>"></TD></TR>
<TR><TD><FONT COLOR = "white">Email:</TD><TD><INPUT TYPE="text" NAME="email" VALUE="<?php echo $_POST["email"]; ?>"></TD></TR>
<tr><td><font color=#ffffff>Username:</td><td><input type="text" name="username" value="<?php echo $_POST["username"]; ?>"></td></tr>
<tr><td><font color=#ffffff>Password:</td><td><input type="password" name="password"</td></tr>
<tr><td><font color=#ffffff>Verify Password:</td><td><input type="password" name="verifypassword"</td></tr>
</table><br />
<INPUT TYPE="submit" VALUE="Continue">
</div>
</center>
</div>
</center>
</div>

</body>
</html>
