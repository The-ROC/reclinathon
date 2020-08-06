<?php
session_start();
$_SESSION = array();

/*
if (ini_get("session.use_cookies")) 
{
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
}

session_destroy();

session_start();
$_SESSION = array();
*/

$_SESSION["ReclineeID"] = "";
$_SESSION["ReclineeName"] = "";

include "include/connect.php";

$LoginSuccessful = FALSE;
$user = $_POST["username"];
$password = $_POST["password"];
$message = "";

if ($user == "" || $password == "")
{
    $message="Please enter all fields.<br />";
    $_SESSION["ReclineeID"] = "";
}
else
{
    $message = "Login failed.  Please try again.<br />";

    $query = $db->prepare(
        "SELECT * FROM RECLINEE WHERE UserName = ? and PasswordHash = ?"
    );
    $query->bind_param('ss', $user, sha1($password));
    $result = db_query($db, $query);
    if ($result && $result->num_rows > 0)
    {
        $LoginSuccessful = TRUE;
        $row = $result->fetch_assoc();
        $_SESSION["ReclineeID"] = $row["ReclineeID"];
        $_SESSION["ReclineeName"] = $row["DisplayName"];

        if ($_SESSION["ReclineeID"] == "")
        {
            $LoginSuccessful = FALSE;
            $message = "Something is weird.<br />";
        }

        //$ReclineeID = $row["ReclineeID"];
    }
    else
    {
        $_SESSION["ReclineeID"] = "";
        $_SESSION["ReclineeName"] = "";
    }
}

if($_POST["xml"])
{
    header("Cache-Control: no-cache, must-revalidate");
    header("Content-Type: text/xml");

    echo "<Login ";
    if($LoginSuccessful)
    {
        echo "result='success' username='" . $_SESSION["ReclineeID"] . "'";
    }
    else
    {
        echo "result='fail'";
    }
    echo " />";
}
else
{
    $currUrl = 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
    $baseUrl = substr($currUrl, 0, strrpos($currUrl, '/')) . '/';
    if (!$LoginSuccessful)
    {
        $URL = $baseUrl . "login.php?username=" . $user . "&message=" . $message;
        //header ("Location: $URL");
        //exit();
    }
    else
    { 
        $URL = $baseUrl . "rtt/ControlCenter.php";
    }
    
    //echo "URL: " . $URL;
    //echo "ReclineeID: " . $_SESSION["ReclineeID"];
    
    
    session_write_close();
    header ("Location: $URL");
    //echo session_id();
    //echo "<meta http-equiv='refresh' content=\"2;url=" . $URL . "\" />";
    //echo "<FORM action='$URL' method='post'><INPUT type='submit' value='continue' /><INPUT type='hidden' name='ReclineeID' value='$ReclineeID' /></FORM>";    
}