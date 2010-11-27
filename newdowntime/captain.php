<?php 

session_name("reclinathon2");
session_id("reclinathon2");
session_Start();

include "connect.php";

if(!session_is_registered("TIMER")) {
  $TIMER = "300";
  session_register("TIMER");
}

if(!session_is_registered("START")) {
  $START = time();
  session_register("START");
}

if($nextmovie != "")
{
  $NEXT_MOVIE = $nextmovie;
}

if($newtime != "") {
  $TIMER = $newtime - $START + time();
}

else if($addtime != "Set Time" && $addtime != "Set Movie" && $addtime > 0) {
  $TIMER += 60 * $addtime;
}

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




