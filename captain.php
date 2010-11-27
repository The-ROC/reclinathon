<?php 

session_name("reclinathon");
session_id("reclinathon");
session_Start();

include "connect.php";

if(!session_is_registered("GO")) {
  $GO = 0;
  session_register("GO");
}

if(!session_is_registered("TIMER")) {
  $TIMER = "300";
  session_register("TIMER");
}

if(!session_is_registered("RANDMAX")) {
  $query = "SELECT * FROM Trivia";
  $result = mysql_query($query);
  $RANDMAX = mysql_num_rows($result);
  $row = mysql_fetch_row($result);
  session_register("RANDMAX");
}

if(!session_is_registered("RAND")) {
  $RAND = rand(1, $RANDMAX);
  echo $RAND;
  session_register("RAND");
}

$GO = 1;
