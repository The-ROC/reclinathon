<?php
include "templateHead.html";
?>


<FONT COLOR = "white"><H1>

<?php

if($_COOKIE["CaptainCookie"] != "true") {

echo "&nbsp;&nbsp;&nbsp;<BR>You Wanna See the Secret Page? <BR>";
echo "<BR><BR>";
echo "Only the captain can see the secret page.</H1>";
include "templateTail.html";
exit();
}



else {

echo "<BR>Welcome, Captain!<BR>";


}

include "templateTail.html";
