<?
require_once 'facebook.php';

$appapikey = 'YOURAPIKEY';
$appsecret = 'YOURSECRETKEY';
$facebook = new Facebook($appapikey, $appsecret);
$user = $facebook->require_login();

//[todo: change the following url to your callback url]
$appcallbackurl = 'CALLBACKURL';

//can't catch exception with php4, so just check if user has added the application
if (!$facebook->api_client->users_isAppAdded()) {
    $facebook->redirect($facebook->get_add_url());
}
?>