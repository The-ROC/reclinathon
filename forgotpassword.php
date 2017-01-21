<?php

include "include/connect.php";

$email = $_GET["email"];
$cstrong = false;
$bytes = openssl_random_pseudo_bytes(16, $cstrong);
$password = base64_encode($bytes);
$passwordHash = sha1($password);
$message = "";
$succeeded = true;

$query = "SELECT * FROM RECLINEE WHERE Email='$email'";
$result = mysql_query($query);
if (!$result || mysql_num_rows($result) == 0)
{
    $message = "The provided email address was not registered with reclinathon.  Please try again.  If you do not know the email address you used to register for reclinathon.com, please contact roc@reclinathon.com to restore your access.";
    $succeeded = false;
}

if ($succeeded)
{
    $row = mysql_fetch_assoc($result);
    $username = $row["UserName"];
    $reclineeID = $row["ReclineeID"];

    $query = "INSERT INTO TempPasswords (PasswordHash, ReclineeID) VALUES ('$passwordHash', '$reclineeID')";
    $result = mysql_query($query);
    if (!$result)
    {
        $message = "Your request failed.  Please follow up with roc@reclinathon.com to restore your access.";
        $succeeded = false;
    }
}

if ($succeeded)
{
    $headers = "From: roc@reclinathon.com";
    $subject = "Your reclinathon.com temporary password.";
    $body = "Thank you for your interest in Reclinathon.  If you did not request a password reset at reclinathon.com, then ignore this mail.  Otherwise, please go to \r\n \r\n http://reclinathon.com/accepttemppassword.php?password=$passwordHash \r\n \r\n and use the following information to log in to reclinathon.com: \r\n \r\n User: $username \r\n Password: $password \r\n \r\n Please change your password after logging in to your command center.";

    if (mail($email, $subject, $body, $headers))
    {
        $message = "An email with your temporary login information has been sent to $email.  Please check your junk mail folder, and follow up with roc@reclinathon.com if you still cannot log in.";
    }
    else
    {
        $message = "Your request failed.  Please follow up with roc@reclinathon.com to restore your access.";
    }
}

$URL = "http://" . $_SERVER['SERVER_NAME'] . "/login.php?message=" . $message;
header ("Location: $URL");

?>

