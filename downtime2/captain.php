<?php 

session_name("reclinathon2");
session_id("reclinathon2");
session_Start();

include $_SERVER['DOCUMENT_ROOT']."\include\connect.php";

if(!session_is_registered("GO")) {
  $GO = 1;
  session_register("GO");
}

if(!session_is_registered("TIMER")) {
  $TIMER = "315";
  session_register("TIMER");
}

if(!session_is_registered("RANDMAX")) {
  $query = $db->prepare("SELECT * FROM Trivia");
  $result = db_query($db, $query);
  $RANDMAX = $result->num_rows;
  $row = $result->fetch_row();
  session_register("RANDMAX");
}

if(!session_is_registered("RANDMAX_CLIP")) {
  $query = $db->prepare("SELECT * FROM VideoClips");
  $result = db_query($db, $query);
  $RANDMAX_CLIP = $result->num_rows;
  $row = $result->fetch_row();
  session_register("RANDMAX_CLIP");
}

if(!session_is_registered("RAND")) {
  $RAND = rand(1, $RANDMAX);
  session_register("RAND");
}

if(!session_is_registered("RAND_CLIP")) {
  $RAND_CLIP = rand(1, $RANDMAX_CLIP);
  session_register("RAND_CLIP");
}

if(!session_is_registered("MODE")) {
  $MODE = 0;
  session_register("MODE");
}

if(!session_is_registered("NEXT_MOVIE")) {
  $query = $db->prepare("SELECT * FROM NextMovie");
  $result = db_query($db, $query);
  $row = $result->fetch_row();
  $NEXT_MOVIE = $row[0];
  session_register("NEXT_MOVIE");
}

if (!session_is_registered("MODE")) {
  $MODE = 1;
  session_register("MODE");
}

$GO = 1;

if($nextmovie != "")
{
  $NEXT_MOVIE = $nextmovie;
}

if($newtime != "") {
  $TIMER = $newtime + 16;
}

else if($addtime != "Set Time" && $addtime != "Set Movie" && $addtime > 0) {
  $TIMER += 60 * $addtime;
}

if($MODE = 1) {
  $query = $db->prepare("SELECT * FROM VideoClips WHERE VCID = ?");
  $query->bind_param('i', $RAND_CLIP);
  $result = db_query($db, $query);
  $row = $result->fetch_row();
  $refresh = $row[2];
}
else {
  $refresh = 15;
}



if($MODE == 10) {
  $MODE = 0;
}
else {
  $MODE = $MODE + 1;
}


//echo "<meta http-equiv=\"refresh\" content=\"15\">";
//echo $TIMER;
$RAND = rand(1, $RANDMAX);
$RAND_CLIP = rand(1, $RANDMAX_CLIP);
//echo "<BR><BR>".$RAND;
if($TIMER != 0) {
  $TIMER -= 15;
  if($TIMER < 0) {
    $TIMER = 0;
  }
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




