<?php

include "include/connect.php";

$passwordHash = $_GET["password"];
$message = "";
$succeeded = true;

$query = $db->prepare("SELECT * FROM TempPasswords WHERE PasswordHash=?");
$query->bind_param('s', $passwordHash);
$result = db_query($db, $query);
if (!$result || $result->num_rows == 0)
{
    $message = "The temporary password is no longer valid.  Please try the 'forgot your password' form again, or contact roc@reclinathon.com to restore your access to reclinathon.com/<br>";
    $succeeded = false;
}

if ($succeeded)
{
    $row = $result->fetch_assoc();
    $reclineeID = $row["ReclineeID"];

    $query = $db->prepare(
        "UPDATE RECLINEE SET PasswordHash = ? WHERE ReclineeID = ? LIMIT 1"
    );
    $query->bind_param('si', $passwordHash, $reclineeID);
    $result = db_query($db, $query);
    if (!$result)
    {
        $message = "Your request failed.  Please follow up with roc@reclinathon.com to restore your access.";
        $succeeded = false;
    }
}

if ($succeeded)
{
    $query = $db->prepare("DELETE FROM TempPasswords WHERE PasswordHash=?");
    $query->bind_param('s', $passwordHash);
    $result = db_query($db, $query);

    $message = "Your password has been reset.  Please log in to your command center using the information provided by email.";
}

$URL = "http://" . $_SERVER['SERVER_NAME'] . "/login.php?message=" . $message;
header ("Location: $URL");

?>

