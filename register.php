<?php
// Test
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
    $query = $db->prepare("SELECT * FROM RECLINEE WHERE FirstName = ? AND LastName = ?");
    $query->bind_param('ss', $FirstName, $LastName);
    $query->execute();
    $result = $query->get_result();
    if ($query->num_rows() > 0)
    {
        echo "You have already registered with the Reclinathon Association of America.  Please log in to your account.";
        $UserAvailable = FALSE;
    }

    if ($UserAvailable)
    {
        $query = $db->prepare("SELECT * FROM RECLINEE WHERE UserName = ?");
        $query->bind_param('s', $UserName);
        $query->execute();
        $result = $query->get_result();
        if ($query->num_rows() > 0)
        {
            echo "The UserName you specified has already been registered.  Please choose a new UserName and try again.";
            $UserAvailable = FALSE;
        }
    }
    
    if ($UserAvailable)
    {
        $queryString = "INSERT INTO RECLINEE (FirstName, LastName, DisplayName, Bio, RocMember, Email, UserName, PasswordHash) ";
        $queryString .= "VALUES (?, ?, ?, ' ', '0', ?, ?, ?)";
        $query = $db->prepare($queryString);
        $query->bind_param('ssssss', $FirstName, $LastName, $FirstName, $Email, $UserName, sha1($Password));
        $result = $query->execute();

        if ($result)
        {
            $UserAdded = TRUE;
            $url = BASE_URL . "rtt/controlcenter.php";
            echo "Registration succeeded.<br /><a href='$url'>Go to your command center</a>.";
            exit();
        }
        else
        {
            echo "Registration failed. ";
            if ($query->error) {
                echo $query->error . ". ";
            }
            echo "Please try again later.";
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
