<?php

session_start();
$_SESSION = array();

if (ini_get("session.use_cookies")) 
{
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
}

session_destroy();

$currUrl = 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
$baseUrl = substr($currUrl, 0, strrpos($currUrl, '/'));
$URL = $baseUrl . "/index.php";

header ("Location: $URL");

?>

