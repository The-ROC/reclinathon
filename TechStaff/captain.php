<?php 

session_name("reclinathon2");
session_id("reclinathon2");
session_Start();

include $_SERVER['DOCUMENT_ROOT']."\include\connect.php";

if(!session_is_registered("TIMER")) {
  $TIMER = 1200000;
  session_register("TIMER");
}

if(!session_is_registered("START")) {
  $START = round(1000 * (microtime(true) + time()));
  session_register("START");
}

if($newtime != "") {
  $START = round(1000 * (microtime(true) + time()));
  $TIMER = $newtime*1000;
}

else if($addtime != "Set Time" && $addtime != "Set Movie" && $addtime > 0) {
  $TIMER += 60000 * $addtime;
}

$captain = 1;
include "index.php";

?>

<FONT COLOR="white">
<BR><BR>ADD Time (minutes)<BR>
<FORM NAME="config" ACTION="captain.php" METHOD="post">
<INPUT TYPE="submit" NAME="addtime" VALUE=1>
<INPUT TYPE="submit" NAME="addtime" VALUE=5>
<INPUT TYPE="submit" NAME="addtime" VALUE=10>
<BR>Set Time (seconds)<BR>
<INPUT TYPE="text" NAME="newtime">
<INPUT TYPE="submit" NAME="addtime" VALUE="Set Time"><BR>
Next Movie<BR>
<INPUT TYPE="text" NAME = "nextmovie">
<INPUT TYPE="submit" NAME="addtime" VALUE="Set Movie">




