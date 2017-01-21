<?php

include "include/connect.php";

$passwordHash = $_GET["password"];
$message = "";
$succeeded = true;

$query = "SELECT * FROM TempPasswords WHERE PasswordHash='$passwordHash'";
$result = mysql_query($query);
if (!$result || mysql_num_rows($result) == 0)
{
    $message = "The temporary password is no longer valid.  Please try the 'forgot your password' form again, or contact roc@reclinathon.com to restore your access to reclinathon.com/<br>";
    $succeeded = false;
}

if ($succeeded)
{
    $row = mysql_fetch_assoc($result);
    $reclineeID = $row["ReclineeID"];

    $query = "UPDATE RECLINEE SET PasswordHash = '$passwordHash' WHERE ReclineeID = '$reclineeID' LIMIT 1";
    $result = mysql_query($query);
    if (!$result)
    {
        $message = "Your request failed.  Please follow up with roc@reclinathon.com to restore your access.";
        $succeeded = false;
    }
}

if ($succeeded)
{
    $query = "DELETE FROM TempPasswords WHERE PasswordHash='$passwordHash'";
    $result = mysql_query($query);

    $message = "Your password has been reset.  Please log in to your command center using the information provided by email.";
}

$URL = "http://" . $_SERVER['SERVER_NAME'] . "/login.php?message=" . $message;
header ("Location: $URL");

?>

