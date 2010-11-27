<?php
require_once('facebook.php');
require_once('../rtt/connect.php');

$appapikey = 'd4bd48e69c48f0e5db0910c5146643ec';
$appsecret = 'f02a3782e8ab4d696e2d985c9a24ab38';
$facebook = new Facebook($appapikey, $appsecret);
$user_id = $facebook->require_login();
?>